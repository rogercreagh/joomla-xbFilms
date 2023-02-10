<?php
/*******
 * @package xbFilms
 * @filesource site/models/film.php
 * @version 1.0.3.8 9th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class XbfilmsModelFilm extends JModelItem {
		
	protected function populateState() {
		$app = Factory::getApplication('site');
		
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
			$query->select('a.id AS id, a.title AS title, a.subtitle AS subtitle, 
				a.summary AS summary, a.synopsis AS synopsis, a.setting AS setting, a.poster_img AS poster_img, a.rel_year AS rel_year,
                a.orig_lang AS orig_lang, a.studio AS studio, a.country AS country, a.runtime AS runtime, 
                a.filmcolour, a.aspect_ratio, a.cam_format, a.filmsound, a.tech_notes,
				a.ext_links AS ext_links, a.first_seen AS first_seen, a.last_seen AS last_seen,
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata, a.created AS created ');
			$query->from('#__xbfilms AS a');
			$query->select('(SELECT COUNT(DISTINCT(fp.person_id)) FROM #__xbfilmperson AS fp WHERE fp.film_id = a.id) AS pcnt');
			$query->select('(SELECT COUNT(DISTINCT(fc.char_id)) FROM #__xbfilmcharacter AS fc WHERE fc.film_id = a.id) AS ccnt');
			$query->select('(SELECT COUNT(DISTINCT(fg.group_id)) FROM #__xbfilmgroup AS fg WHERE fg.film_id = a.id) AS gcnt');
			$query->select('(SELECT AVG(fr.rating) FROM #__xbfilmreviews AS fr WHERE fr.film_id=a.id) AS averat');
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
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
				$target = ($params->get('extlink_target')==1) ? 'target="_blank"' : '';
				// Convert the JSON-encoded links info into an array
				$extlinks = new Registry;
				$extlinks->loadString($item->ext_links, 'JSON');
				$item->ext_links = $extlinks;
				$item->ext_links_cnt = 0;
//				$item->ext_links = json_decode($item->ext_links);
				$item->ext_links_list ='';
				if(is_object($item->ext_links)) {
					$item->ext_links_cnt = 0;
					$item->ext_links_list = '<ul>';
					foreach($item->ext_links as $lnk) {
						$item->ext_links_list .= '<li><a href="'.$lnk->link_url.'" '.$target.'>'.$lnk->link_text.
							'</a> - '.$lnk->link_desc.'</li>';
						$item->ext_links_cnt += 1;
					}
					$item->ext_links_list .= '</ul>';
				}
				
				if ($item->pcnt) {
				    $people = XbfilmsGeneral::getFilmPeople($item->id);
				 
				    //get counts for director,producers,cast,crew,appearances
				    $roles = array_column($people,'role');
				    $item->dircnt = count(array_keys($roles, 'director'));
				    $item->prodcnt = count(array_keys($roles, 'producer'));
				    $item->crewcnt = count(array_keys($roles, 'crew'));
				    $item->subjcnt = count(array_keys($roles, 'appearsin'));
				    $item->castcnt = count(array_keys($roles, 'actor'));
				
				    //make director/producer/char lists
				    $item->dirlist = XbcultureHelper::makeItemLists($people,'director','tn',3,'ppvmodal');
				    $item->prodlist = XbcultureHelper::makeItemLists($people,'producer','tn',3,'ppvmodal');
				    $item->crewlist = XbcultureHelper::makeItemLists($people,'crew','ul','tn',3,'ppvmodal');
				    $item->subjlist = XbcultureHelper::makeItemLists($people,'appearsin','tn',3,'ppvmodal');
				    $item->castlist = XbcultureHelper::makeItemLists($people,'actor','tn',3,'ppvmodal');
				} else {
				    $item->dircnt = 0;
				    $item->prodcnt = 0;
				    $item->crewcnt = 0;
				    $item->subjcnt = 0;
				    $item->castcnt = 0;				    
				}
				
				if ($item->ccnt) {
				    $chars = XbfilmsGeneral::getFilmChars($item->id);
				    $item->charslist = XbcultureHelper::makeItemLists($chars,'char','tn',3,'cpvmodal');
				}
				
				if ($item->gcnt) {
				    $groups = XbfilmsGeneral::getFilmGroups($item->id);
				    $item->groupslist = XbcultureHelper::makeItemLists($groups,'','trn',3,'cpvmodal');
				}
				
				//order by review rating or date?
				$item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
				$item->revcnt = count($item->reviews);
			} //end if loadobject			
            return $this->item;			
		} //end if item not ok				
	} //end getitem()
	
}
