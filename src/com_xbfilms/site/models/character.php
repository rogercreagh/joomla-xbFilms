<?php
/*******
 * @package xbFilms
 * @filesource site/models/character.php
 * @version 0.2.3b 1st January 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbfilmsModelCharacter extends JModelItem {
	
	protected function populateState() {
		$app = JFactory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('film.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
		
		if (!isset($this->item) || !is_null($id)) {
			$id    = is_null($id) ? $this->getState('film.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.name AS name, a.image AS image, 
				a.summary AS summary, a.description AS description,  
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbcharacters AS a');
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
			$query->where('a.id = '.$id);
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {
				$item = &$this->item;
				// Load the JSON string
				$params = new JRegistry;
				$params->loadString($item->params, 'JSON');
				$item->params = $params;
				
				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($item->params);
				$item->params = $params;
				$target = ($params->get('extlink_target')==1) ? 'target="_blank"' : '';
				
				$item->films = XbfilmsHelper::getCharacterFilmsArray($item->id);
				$item->bcnt = count($item->films);
				
				//makedirectoreditor/char lists
				if ($item->bcnt == 0){
					$item->clist = '';
				} else {
					$item->clist = XbfilmsGeneral::makeLinkedNameList($item->films,'',', ',true);
				}
								
			}
		}
		return $this->item;
	}
}
	
