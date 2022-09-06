<?php
/*******
 * @package xbFilms
 * @filesource admin/models/persons.php
 * @version 0.9.9.7 4th September 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbfilmsModelPersons extends JModelList {

	protected $xbbooksStatus;
	
	public function __construct($config = array()) {
        
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a,id',
            	'firstname', 'lastname', 
            	'published', 'a.state',
            	'ordering', 'a.ordering',
                'category_title', 'c.title',
                'catid', 'a.catid', 'category_id', 'tagfilt', 'taglogic',
                'sortdate', 'fcnt', 'bcnt' );
        }
        //$this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
        $this->xbbooksStatus = Factory::getSession()->get('xbbooks_ok',false);
        parent::__construct($config);
    }
    
    protected function getListQuery() {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.firstname AS firstname, a.lastname AS lastname, a.alias AS alias, 
			a.summary AS summary, a.portrait AS portrait, a.biography AS biography, a.ext_links AS ext_links,
			a.nationality AS nationality, a.year_born AS year_born, a.year_died AS year_died,
			a.catid AS catid, a.state AS published, a.created AS created, a.created_by AS created_by, 
			a.created_by_alias AS created_by_alias, a.checked_out AS checked_out, a.checked_out_time AS checked_out_time, 
            a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
        $query->select('IF((year_born>-9999),year_born,year_died) AS sortdate');
            
        $query->from($db->quoteName('#__xbpersons','a'));
        
        $query->select('(SELECT COUNT(DISTINCT(fp.film_id)) FROM #__xbfilmperson AS fp WHERE fp.person_id = a.id) AS fcnt');
        if ($this->xbbooksStatus) $query->select('(SELECT COUNT(DISTINCT(bp.book_id)) FROM #__xbbookperson AS bp WHERE bp.person_id = a.id) AS bcnt');

        $query->join('LEFT',$db->quoteName('#__xbfilmperson', 'b') . ' ON ' . $db->quoteName('b.person_id') . ' = ' .$db->quoteName('a.id'));
        
        $query->select('c.title AS category_title')
            ->join('LEFT', '#__categories AS c ON c.id = a.catid');
            
            // Filter: like / search
        $search = $this->getState('filter.search');
        
        if (!empty($search)) {
            if (stripos($search, 'i:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 2));
            } elseif (stripos($search, 'b:') === 0) {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
                $query->where('(summary LIKE ' . $search.' OR biography LIKE '.$search.')');
            } else {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(lastname LIKE ' . $search.' OR firstname LIKE '.$search.')');               
            }
        }
        
        // Filter by published state
        $published = $this->getState('filter.published');
        
        if (is_numeric($published)) {
            $query->where('state = ' . (int) $published);
//        } elseif ($published === '') {
            //$query->where('(state IN (0, 1))');
        }
        
        //Filter by role
        $rolefilt = $this->getState('filter.rolefilt');
        if (empty($rolefilt)) { $rolefilt = 'film'; }
        if ($rolefilt!='all') {
        	if ($rolefilt == 'film') {
        		$query->where('b.id IS NOT NULL');
        	} elseif ($rolefilt == 'notfilm') {
        	    $query->where('b.id IS NULL');
        	} elseif ($rolefilt == 'orphans') {
        		if ($this->xbbooksStatus) {
        			$query->join('LEFT OUTER',$db->quoteName('#__xbbookperson', 'f') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('f.person_id'));
        			$query->where('f.id IS NULL');
        		}
        		$query->where('b.id IS NULL');
        	} else {
        		$query->where('b.role = '.$db->quote($rolefilt));       		
        	}
        }
        
        // Filter by category.
        $app = Factory::getApplication();
        $categoryId = $app->getUserStateFromRequest('catid', 'catid','');
        $app->setUserState('catid', '');
        if ($categoryId=='') {
        	$categoryId = $this->getState('filter.category_id');
        }
//        $subcats=0;
        if (is_numeric($categoryId))
        {
        	$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
        }
        
        //filter by tags
        $tagId = $app->getUserStateFromRequest('tagid', 'tagid','');
        $app->setUserState('tagid', '');
        if (!empty($tagId)) {
        	$tagfilt = array(abs($tagId));
        	$taglogic = $tagId>0 ? 0 : 2;
        } else {
        	$tagfilt = $this->getState('filter.tagfilt');
        	$taglogic = $this->getState('filter.taglogic');  //0=ANY 1=ALL 2= None
        }
        
        if (($taglogic === '2') && (empty($tagfilt))) {
        	//if if we select tagged=excl and no tags specified then only show untagged items
        	$subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias LIKE '.$db->quote('com_xb%.person').')';
        	$query->where('a.id NOT IN '.$subQuery);
        }
        if ($tagfilt && is_array($tagfilt)) {
            $tagfilt = ArrayHelper::toInteger($tagfilt);
            $subquery = '(SELECT tmap.tag_id AS tlist FROM #__contentitem_tag_map AS tmap
                WHERE tmap.type_alias = '.$db->quote('com_xbpeople.person').'
                AND tmap.content_item_id = a.id)';
            switch ($taglogic) {
                case 1: //all
                    for ($i = 0; $i < count($tagfilt); $i++) {
                        $query->where($tagfilt[$i].' IN '.$subquery);
                    }
                    break;
                case 2: //none
                    for ($i = 0; $i < count($tagfilt); $i++) {
                        $query->where($tagfilt[$i].' NOT IN '.$subquery);
                    }
                    break;
                default: //any
                    $conds = array();
                    for ($i = 0; $i < count($tagfilt); $i++) {
                        $conds[] = $tagfilt[$i].' IN '.$subquery;
                    }
                    $query->extendWhere('AND', $conds, 'OR');
                    break;
            }
        } //end if $tagfilt
        
        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'lastname');
        $orderDirn 	= $this->state->get('list.direction', 'asc');
        if ($orderCol == 'a.ordering' || $orderCol == 'a.catid') {
        	$orderCol = 'category_title '.$orderDirn.', a.ordering';
        }
        
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
        if ($orderCol != 'lastname') {
        	$query->order('lastname ASC');
        }
        
        $query->group('a.id');
        
        return $query;
    }
    
    public function getItems() {
        $items  = parent::getItems();
        // we are going to add the list of people (with roles) for teach film
        //and apply any film title filter
        $tagsHelper = new TagsHelper;
        
        foreach ($items as $i=>$item) {            
            
            $item->films = XbcultureHelper::getPersonFilmRoles($item->id);
            $cnts = array_count_values(array_column($item->films, 'role'));
            $item->dircnt = (key_exists('director',$cnts))?$cnts['director'] : 0;
            $item->prdcnt = (key_exists('producer',$cnts))?$cnts['producer'] : 0;
            $item->crewcnt = (key_exists('crew',$cnts))?$cnts['crew'] : 0;
            $item->castcnt = (key_exists('actor',$cnts))?$cnts['actor'] : 0;
            $item->appcnt = (key_exists('appearsin',$cnts))?$cnts['appearsin'] : 0;

            $item->dirlist = '';
            $item->prdlist ='';
            $item->crewlist='';
            $item->castlist='';
            $item->applist='';
            foreach ($item->films as $film) {
                switch ($film->role) {
                    case 'director' :
                        $item->dirlist .= $film->listitem;
                        break;
                    case 'producer' :
                        $item->prdlist .= $film->listitem;
                        break;
                    case 'crew' :
                        $item->crewlist .= $film->listitem;
                        break;
                    case 'actor' :
                        $item->castlist .= $film->listitem;
                        break;
                    case 'appearsin' :
                        $item->applist .= $film->listitem;
                        break;
                    default:
                        break;
                }
                
            }
/*             
            $item->bookcnt = 0;
            if ($this->xbbooksStatus) {
            	$db    = Factory::getDbo();
            	$query = $db->getQuery(true);
            	$query->select('COUNT(*)')->from('#__xbbookperson');
            	$query->where('person_id = '.$db->quote($item->id));
            	$db->setQuery($query);
            	$item->bookcnt = $db->loadResult();
            }
            $item->dirlist='';
            if ($item->dircnt>0) {
            	$item->dirlist = htmlentities(XbfilmsGeneral::makeLinkedNameList($item->films,'director',', ',false,true));
            }
            $item->prdlist='';
            if ($item->prdcnt>0) {
            	$item->prdlist = htmlentities(XbfilmsGeneral::makeLinkedNameList($item->films,'producer',', ',false,true));
            }
            $item->crewlist='';
            if ($item->crewcnt>0) {
            	$item->crewlist = htmlentities(XbfilmsGeneral::makeLinkedNameList($item->films,'crew',', ',false,true));
            }
            $item->actlist='';
            if ($item->actcnt>0) {
            	$item->actlist = htmlentities(XbfilmsGeneral::makeLinkedNameList($item->films,'actor',', ',false,true));
            }
            $item->applist='';
            if ($item->appcnt>0) {
            	$item->applist = htmlentities(XbfilmsGeneral::makeLinkedNameList($item->films,'appearsin',', ',false,true));
            }
 */            
            $item->ext_links = json_decode($item->ext_links);
            $item->ext_links_list ='';
            $item->ext_links_cnt = 0; 
            if(is_object($item->ext_links)) {
            	$item->ext_links_cnt = count((array)$item->ext_links);
            	foreach($item->ext_links as $lnk) {
					$item->ext_links_list .= '<a href="'.$lnk->link_url.'" target="_blank">'.$lnk->link_text.'</a>, ';
				}
				$item->ext_links_list = trim($item->ext_links_list,', ');
	        } //end if is_object
	        $item->persontags = $tagsHelper->getItemTags('com_xbpeople.person' , $item->id);
	        $item->filmtags = $tagsHelper->getItemTags('com_xbfilms.person' , $item->id);
	        $item->booktags = $tagsHelper->getItemTags('com_xbbooks.person' , $item->id);
        } //end foreach item
	        return $items;
    }

}