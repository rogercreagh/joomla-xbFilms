<?php
/*******
 * @package xbFilms
 * @filesource admin/models/tag.php
 * @version 1.0.1.3 5th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Component\ComponentHelper;

class XbfilmsModelTag extends JModelItem {
	
	protected function populateState() {
		$app = Factory::getApplication();
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('tag.id', $id);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) || !is_null($id)) {
			$params = ComponentHelper::getParams('com_xbpeople');
			$people_sort = $params->get('people_sort');
			
			$id    = is_null($id) ? $this->getState('tag.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('t.id AS id, t.path AS path, t.title AS title, t.note AS note, t.description AS description, 
				t.alias, t.published AS published');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mb WHERE mb.type_alias='.$db->quote('com_xbfilms.film').' AND mb.tag_id = t.id) AS bcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mp WHERE mp.type_alias='.$db->quote('com_xbpeople.person').' AND mp.tag_id = t.id) AS pcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mr WHERE mr.type_alias='.$db->quote('com_xbfilms.review').' AND mr.tag_id = t.id) AS rcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS mr WHERE mr.type_alias='.$db->quote('com_xbpeople.character').' AND mr.tag_id = t.id) AS chcnt');
			$query->select('(SELECT COUNT(*) FROM #__contentitem_tag_map AS ma WHERE ma.tag_id = t.id) AS allcnt ');
			$query->from('#__tags AS t');
			$query->where('t.id = '.$id);
			$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
			
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {				
				$item = &$this->item;
				//calculate how many non xbfilms items the tag applies to to save doing it later
				$item->othercnt = $item->allcnt - ($item->bcnt + $item->pcnt + $item->rcnt);
				//get titles and ids of films, people and reviews with this tag
				$db    = Factory::getDbo();
				if ($item->bcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('b.id AS bid, b.title AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbfilms AS b ON b.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbfilms.film'");
					$query->order('b.title');
					$db->setQuery($query);
					$item->bks = $db->loadObjectList();
				} else {
					$item->bks = '';
				}
				if ($item->pcnt > 0) {
					$query = $db->getQuery(true);
					if ($people_sort == '0') {
						$query->select('p.id AS pid, CONCAT(p.firstname,'.$db->quote(' '). ',p.lastname) AS title');
					} else {
						$query->select('p.id AS pid, CONCAT(p.lastname,'.$db->quote(', '). ',p.firstname) AS title');
					}
					$query->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbpersons AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbpeople.person'");
					$query->order($people_sort==1 ? 'p.lastname' : 'p.firstname');
					$db->setQuery($query);
					$item->people = $db->loadObjectList();
				} else {
					$item->people='';
				}
				if ($item->chcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('p.id AS pid, p.name AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbcharacters AS p ON p.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbpeople.character'");
					$query->order('p.lastname');
					$db->setQuery($query);
					$item->chars = $db->loadObjectList();
				} else {
					$item->chars='';
				}
				if ($item->rcnt > 0) {
					$query = $db->getQuery(true);
					$query->select('r.id AS rid, r.title AS title')->from('#__tags AS t');
					$query->join('LEFT','#__contentitem_tag_map AS m ON m.tag_id = t.id');
					$query->join('LEFT','#__xbfilmreviews AS r ON r.id = m.content_item_id');
					$query->where("t.id='".$item->id."' AND m.type_alias='com_xbfilms.review'");
					$query->order('r.title');
					$db->setQuery($query);
					$item->revs = $db->loadObjectList();
				} else {
					$item->revs = '';
				}	
				if ($item->othercnt > 0) {
					$query = $db->getQuery(true);
					$query->select('m.type_alias AS type_alias, m.core_content_id, c.core_title AS core_title'); 
					$query->from('#__contentitem_tag_map AS m');
					$query->join('LEFT','#__ucm_content AS c ON m.core_content_id = c.core_content_id');
					$query->where('m.tag_id = '.$item->id);
					$query->where('m.type_alias NOT IN ('.$db->quote('com_xbfilms.film').','.$db->quote('com_xbpeople.person').','.$db->quote('com_xbpeople.character').','.$db->quote('com_xbfilms.review').')');
					$query->order('m.type_alias, c.core_title');
					$db->setQuery($query);
					$item->others = $db->loadObjectList();
				} else {
					$item->others = '';
				}
			}
			return $this->item;
		} //endif item set			
	} //end getItem()
}
