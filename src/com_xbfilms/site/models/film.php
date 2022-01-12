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
				a.ext_links AS ext_links, a.cat_date AS cat_date, 
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata ');
			$query->from('#__xbfilms AS a');
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
				
				
				//getdirector,producers,characters
				$item->people = XbfilmsGeneral::getFilmRoleArray($item->id);
				$cnts = array_count_values(array_column($item->people, 'role'));
				$item->dircnt = (key_exists('director',$cnts))? $cnts['director'] : 0;
				$item->prodcnt = (key_exists('producer',$cnts))? $cnts['producer'] : 0;
				$item->crewcnt = (key_exists('crew',$cnts))? $cnts['crew'] : 0;
				$item->subjcnt = (key_exists('appearsin',$cnts))? $cnts['appearsin'] : 0;
				$item->castcnt = (key_exists('actor',$cnts))? $cnts['actor'] : 0;
				
				$item->chars = XbfilmsGeneral::getFilmCharsArray($item->id);
				$item->charcnt = count($item->chars);
				
				
				//make director/producer/char lists
				if ($item->dircnt==0){
					$item->dlist = ''; //'<i>'.JText::_( 'COM_XBFILMS_NODIRECTOR' ).'</i>';
				} else {
					$item->dlist = XbfilmsGeneral::makeLinkedNameList($item->people,'director',',');
				}
				if (($item->prodcnt)==0){
					$item->plist = '';
				} else {
					$item->plist = ': '.XbfilmsGeneral::makeLinkedNameList($item->people,'producer',',');
				}
				if (($item->crewcnt)==0){
					$item->crlist = '';
				} else {
					$item->crlist = ': '.XbfilmsGeneral::makeLinkedNameList($item->people,'crew','<br />',true,false,true);
				}
				if (($item->subjcnt)==0){
					$item->slist = '';
				} else {
					$item->slist = ': '.XbfilmsGeneral::makeLinkedNameList($item->people,'appearsin','<br />',true,false,true);
				}
				if (($item->castcnt)==0){
					$item->alist = '';
				} else {
					$item->alist = ': '.XbfilmsGeneral::makeLinkedNameList($item->people,'actor','<br />',true,false,true);
				}
				
				
				if (($item->charcnt)==0){
					$item->chlist = '';
				} elseif ($item->charcnt < 4) {
					$item->chlist = XbfilmsGeneral::makeLinkedNameList($item->chars,'',', ',true);
				} else {
					$item->chlist = XbfilmsGeneral::makeLinkedNameList($item->chars,'','<br />',false);
				}
				
				//order by review rating or date?
				$item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
				$item->revcnt = count($item->reviews);
				$item->lastseen = $item->cat_date;
				if ($item->revcnt>0) {
				    $item->lastseen = max(array_column($item->reviews,'rev_date'));
				}
			} //end if loadobject			
            return $this->item;			
		} //end if item not ok				
	} //end getitem()
	
/*
 	function max_attribute_in_array($array, $prop) {
		return max(array_column($array, $prop));
		// php<v7 version below
		return max(array_map(function($o) use($prop) {
			return $o->$prop;
		},
		$array));
	}
 */
}
