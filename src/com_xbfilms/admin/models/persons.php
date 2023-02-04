<?php
/*******
 * @package xbFilms
 * @filesource admin/models/persons.php
 * @version 1.0.3.2 4th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbfilmsModelPersons extends JModelList {

	public function __construct($config = array()) {
        
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a,id',
            	'firstname', 'lastname', 
                'nationality', 'a.nationality',
            	'published', 'a.state',
            	'ordering', 'a.ordering',
                'category_title', 'c.title',
                'catid', 'a.catid', 'category_id', 'tagfilt', 'taglogic',
                'sortdate', 'fcnt', 'bcnt' );
        }
        parent::__construct($config);
    }
    
    protected function getListQuery() {
        $sess = Factory::getSession();
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
        $query->select('(SELECT COUNT(DISTINCT(fr.role)) FROM #__xbfilmperson AS fr WHERE fr.person_id = a.id) AS frcnt');
        if ($sess->get('xbbooks_ok',false)==1) $query->select('(SELECT COUNT(DISTINCT(bp.book_id)) FROM #__xbbookperson AS bp WHERE bp.person_id = a.id) AS bcnt');
        if ($sess->get('xbevents_ok',false)==1) $query->select('(SELECT COUNT(DISTINCT(ep.event_id)) FROM #__xbeventperson AS ep WHERE ep.person_id = a.id) AS ecnt');
        
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
        }
        
        //filter by nationality
        $natfilt = $this->getState('filter.nationality');
        if (!empty($natfilt)) {
            $query->where('a.nationality = '.$db->quote($natfilt));
        }
        
        //Filter by role
        $rolefilt = $this->getState('filter.rolefilt');
        if (empty($rolefilt)) { $rolefilt = 'film'; }
        if ($rolefilt!='all') {
        	if ($rolefilt == 'film') {
        		$query->where('b.id IS NOT NULL');
        	} elseif ($rolefilt == 'notfilm') {
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
        if (is_numeric($categoryId)) {
        	$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
        } elseif (is_array($categoryId)) {
            $categoryId = implode(',', $categoryId);
            $query->where($db->quoteName('a.catid') . ' IN ('.$categoryId.')');
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
        
        if (empty($tagfilt)) {
            $subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias LIKE '.$db->quote('com_xb%.person').')';
            if ($taglogic === '1') {
                $query->where('a.id NOT IN '.$subQuery);
            } elseif ($taglogic === '2') {
                $query->where('a.id IN '.$subQuery);
            }
        } else {
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
                    if (count($tagfilt)==1) {
                        $query->where($tagfilt[0].' IN '.$subquery);
                    } else {
                        $tagIds = implode(',', $tagfilt);
                        if ($tagIds) {
                            $subQueryAny = '(SELECT DISTINCT content_item_id FROM #__contentitem_tag_map
                                WHERE tag_id IN ('.$tagIds.') AND type_alias = '.$db->quote('com_xbpeople.person').')';
                            $query->innerJoin('(' . (string) $subQueryAny . ') AS tagmap ON tagmap.content_item_id = a.id');
                        }
                    }
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
        $sess = Factory::getSession();
        $items  = parent::getItems();
        // we are going to add the list of films (with roles) for each person
        $tagsHelper = new TagsHelper;
        
        foreach ($items as $i=>$item) {            
            if ($sess->get('xbnooks_ok',false)!=1) $item->bcnt = 0;
            if ($sess->get('xbevents_ok',false)!=1) $item->ecnt = 0;
            
            $item->films = XbcultureHelper::getPersonFilms($item->id);
            
            $roles = array_column($item->films,'role');
            $item->dircnt = count(array_keys($roles, 'director'));
            $item->prodcnt = count(array_keys($roles, 'producer'));
            $item->crewcnt = count(array_keys($roles, 'crew'));
            $item->appcnt = count(array_keys($roles, 'appearsin'));
            $item->castcnt = count(array_keys($roles, 'actor'));
            
            $item->dirlist = $item->dircnt==0 ? '' : XbcultureHelper::makeItemLists($item->films,'director','t',3,'fpvmodal');
            $item->prodlist = $item->prodcnt==0 ? '' : XbcultureHelper::makeItemLists($item->films,'producer','t',3,'fpvmodal');
            $item->crewlist = $item->crewcnt==0 ? '' : XbcultureHelper::makeItemLists($item->films,'crew','tn',3,'fpvmodal');
            $item->castlist = $item->castcnt==0 ? '' : XbcultureHelper::makeItemLists($item->films,'actor','tn',3,'fpvmodal');
            $item->applist = $item->appcnt==0 ? '' : XbcultureHelper::makeItemLists($item->films,'appearsin','tn',3,'fpvmodal');
            
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
	        $item->tags = $tagsHelper->getItemTags('com_xbpeople.person' , $item->id);
        } //end foreach item
	        return $items;
    }

}