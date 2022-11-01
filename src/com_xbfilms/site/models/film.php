<?php
/*******
 * @package xbFilms
 * @filesource site/models/film.php
 * @version 0.9.9.8 10th October 2022
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
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata ');
			$query->from('#__xbfilms AS a');
			$query->select('(SELECT COUNT(DISTINCT(fp.person_id)) FROM #__xbfilmperson AS fp WHERE fp.film_id = a.id) AS pcnt');
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
				
				$item->people = XbfilmsGeneral::getFilmPeople($item->id);
				 
				//get counts for director,producers,cast,crew,appearances
				$roles = array_column($item->people,'role');
				$item->dircnt = count(array_keys($roles, 'director'));
				$item->prodcnt = count(array_keys($roles, 'producer'));
				$item->crewcnt = count(array_keys($roles, 'crew'));
				$item->subjcnt = count(array_keys($roles, 'appearsin'));
				$item->castcnt = count(array_keys($roles, 'actor'));
				
				//make director/producer/char lists
			    $item->dirlist = $item->dircnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'director','comma');
			    $item->prodlist = $item->prodcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'producer','comma');
			    $item->crewlist = $item->crewcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'crew','ul',true,1);
			    $item->subjlist =$item->subjcnt==0 ? '' :  XbcultureHelper::makeLinkedNameList($item->people,'appearsin','ul',true,1);
			    $item->castlist = $item->castcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'actor','ul',true,1);
			
				$item->chars = XbfilmsGeneral::getFilmChars($item->id);
				$item->charcnt = empty($item->chars) ? 0 : count($item->chars);
				$item->charlist = $item->charcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->chars,'char','ul',true,1);
				
				//order by review rating or date?
				$item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
				$item->revcnt = count($item->reviews);
			} //end if loadobject			
            return $this->item;			
		} //end if item not ok				
	} //end getitem()
	
}
