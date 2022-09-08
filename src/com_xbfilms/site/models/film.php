<?php
/*******
 * @package xbFilms
 * @filesource site/models/film.php
 * @version 0.9.8.3 24th May 2022
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
                a.filmcolour, a.aspect_ratio, a.cam_format, a.filmsound,
				a.ext_links AS ext_links, a.acq_date AS acq_date, a.last_seen AS last_seen,
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
				
				$item->dirlist = '';
				$item->prodlist = '';
				$item->crewlist = '';
				$item->castlist = '';
				$item->subjlist = '';
				$item->dircnt = 0;
				$item->prodcnt = 0;
				$item->crewcnt =  0;
				$item->subjcnt =  0;
				$item->castcnt =  0;

				$item->people = XbfilmsGeneral::getFilmPeople($item->id);
				 
				//get counts for director,producers,cast,crew,appearances
				$cnts = array_count_values(array_column($item->people, 'role'));
				$item->dircnt = (key_exists('director',$cnts))? $cnts['director'] : 0;
				$item->prodcnt = (key_exists('producer',$cnts))? $cnts['producer'] : 0;
				$item->crewcnt = (key_exists('crew',$cnts))? $cnts['crew'] : 0;
				$item->subjcnt = (key_exists('appearsin',$cnts))? $cnts['appearsin'] : 0;
				$item->castcnt = (key_exists('actor',$cnts))? $cnts['actor'] : 0;
				
				
				//make director/producer/char lists
				if ($item->dircnt > 0){
					$item->dirlist = XbcultureHelper::makeLinkedNameList($item->people,'director','comma');
				}
				if ($item->prodcnt > 0){
				    $item->prodlist = XbcultureHelper::makeLinkedNameList($item->people,'producer','comma');
				}
				if ($item->crewcnt > 0){
				    $item->crewlist = XbcultureHelper::makeLinkedNameList($item->people,'crew','ul',true,1);
				}
				if ($item->subjcnt > 0){
				    $item->subjlist = XbcultureHelper::makeLinkedNameList($item->people,'appearsin','ul',true,1);
				}
				if ($item->castcnt > 0){
				    $item->castlist = XbcultureHelper::makeLinkedNameList($item->people,'actor','ul',true,1);
				}
			
				$item->charcnt=0;
				$item->chars = XbfilmsGeneral::getFilmCharsArray($item->id);
				if (!empty($item->chars)) {
				    $item->charcnt = count($item->chars);
				}
				$item->charlist = '';
				if ($item->charcnt > 0){
				    $item->charlist = XbcultureHelper::makeLinkedNameList($item->chars,'','ul',true,1);
				}
				
				//order by review rating or date?
				$item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
				$item->revcnt = count($item->reviews);
			} //end if loadobject			
            return $this->item;			
		} //end if item not ok				
	} //end getitem()
	
}
