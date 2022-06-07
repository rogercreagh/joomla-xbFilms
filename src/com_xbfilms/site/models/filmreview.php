<?php
/*******
 * @package xbFilms
 * @filesource site/models/film.php
 * @version 0.9.7 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class XbfilmsModelFilmreview extends JModelItem {
		
	protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('filmreview.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) ) { //|| !is_null($id)
			$id    = is_null($id) ? $this->getState('filmreview.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.title AS title, 
				a.film_id AS film_id, b.poster_img AS poster_img, b.title AS film_title,
				a.rev_date AS rev_date, a.where_seen AS where_seen, a.rating AS rating,
				a.summary AS summary, a.review AS review, a.reviewer AS reviewer, a.subtitled AS subtitled,
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbfilmreviews AS a');
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
			$query->join('LEFT', '#__xbfilms AS b ON b.id = a.film_id');
			$query->where('a.id = '.$id);
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {
				
				$item = &$this->item;
				// Load the JSON string
				$params = new Registry;
				$params->loadString($item->params, 'JSON');
				$item->params = $params;
				
				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($item->params);
				$item->params = $params;				
				
				//get people and counts
				$item->people = XbfilmsGeneral::getFilmRoleArray($item->film_id);
				$cnts = array_count_values(array_column($item->people, 'role'));
				$item->authcnt = (key_exists('director',$cnts))? $cnts['director'] : 0;
				$item->editcnt = (key_exists('producer',$cnts))? $cnts['producer'] : 0;
				
				//makedirectoreditor list
				$item->edauths = '<i>';
				if ($item->editcnt == 0){
					if ($item->authcnt == 0){
						$item->edauths .= JText::_( 'XBFILMS_NODIRECTOR' ).'</i>';
					} else {
						$item->edauths .= ($item->authcnt>1)?JText::_('XBCULTURE_CAPDIRECTORS'):JText::_('XBCULTURE_CAPDIRECTOR');
						$item->edauths .= '</i>: '.XbfilmsGeneral::makeLinkedNameList($item->people,'director',',',false);
					}
				} else {
					$item->edauths .= JText::_('XBCULTURE_CAPPRODUCER').'</i>: '.
							XbfilmsGeneral::makeLinkedNameList($item->people,'producer',',',false);
				}
				
				//get other reviews
				$item->reviews = XbfilmsGeneral::getFilmReviews($item->film_id);				
			} //end if loadobject			
            return $this->item;			
		} //end if item not set already and we have an id				
	} //end getitem()
	
}