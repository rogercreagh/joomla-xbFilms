<?php
/*******
 * @package xbFilms
 * @filesource site/models/person.php
 * @version 0.9.7 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;

class XbfilmsModelPerson extends JModelItem {
	
	protected $xbbooksStatus;
	
	public function __construct($config = array()) {
		//$this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
		$this->xbbooksStatus = Factory::getSession()->get('com_xbbooks',false);
		parent::__construct($config);
	}
	
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
			$query->select('a.id AS id, a.firstname AS firstname, a.lastname AS lastname, a.portrait AS portrait, 
				a.summary AS summary, a.biography AS biography, a.year_born AS year_born, a.year_died AS year_died,
				a.nationality AS nationality, a.ext_links AS ext_links, 
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbpersons AS a');
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
//				$extlinks = new Registry;
//				$extlinks->loadString($item->ext_links, 'JSON');
//				$item->ext_links = $extlinks;
				$item->ext_links = json_decode($item->ext_links);
				$item->ext_links_list ='';
				$item->ext_links_cnt = 0;
				if(is_object($item->ext_links)) {
					$item->ext_links_cnt = count((array)$item->ext_links);
					$item->ext_links_list = '<ul>';
					foreach($item->ext_links as $lnk) {
						$item->ext_links_list .= '<li><a href="'.$lnk->link_url.'" '.$target.'>'.$lnk->link_text.'</a></li>';
					}
					$item->ext_links_list .= '</ul>';
				}
				$item->films = XbfilmsGeneral::getPersonRoleArray($item->id);
				$item->bcnt = count($item->films);
				$cnts = array_count_values(array_column($item->films, 'role'));
				$item->dircnt = (key_exists('director',$cnts))?$cnts['director'] : 0;
				$item->prdcnt = (key_exists('producer',$cnts))?$cnts['producer'] : 0;
				$item->crewcnt = (key_exists('crew',$cnts))?$cnts['crew'] : 0;
				$item->actcnt = (key_exists('actor',$cnts))?$cnts['actor'] : 0;
				$item->appcnt = (key_exists('appearsin',$cnts))?$cnts['appearsin'] : 0;
				
				//makedirectoreditor/char lists
				if ($item->dircnt == 0){
					$item->dirlist = '';
				} else {
					$item->dirlist = XbfilmsGeneral::makeLinkedNameList($item->films,'director','<br />', true, false, 2);
				}
				if ($item->prdcnt == 0){
					$item->prdlist = '';
				} else {
					$item->prdlist = XbfilmsGeneral::makeLinkedNameList($item->films,'producer','<br />',true, false, 2);
				}
				if ($item->crewcnt == 0){
					$item->crewlist = '';
				} else {
					$item->crewlist = XbfilmsGeneral::makeLinkedNameList($item->films,'crew','<br />',true, false, 1);
				}
				if ($item->actcnt == 0){
					$item->actlist = '';
				} else {
					$item->actlist = XbfilmsGeneral::makeLinkedNameList($item->films,'actor','<br />',true, false, 2);
				}
				if ($item->appcnt == 0){
					$item->applist = '';
				} else {
					$item->applist = XbfilmsGeneral::makeLinkedNameList($item->films,'appearsin','<br />',true, false, 2);
				}
				
				$item->bookcnt = 0;
				if ($this->xbbooksStatus===true) {
					$db    = Factory::getDbo();
					$query = $db->getQuery(true);
					$query->select('COUNT(*)')->from('#__xbbookperson');
					$query->where('person_id = '.$db->quote($item->id));
					$db->setQuery($query);
					$item->bookcnt = $db->loadResult();
				}
				
			}
		}
		return $this->item;
	}
}
	
