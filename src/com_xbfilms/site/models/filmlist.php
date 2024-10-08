<?php
/*******
 * @package xbFilms
 * @filesource site/models/filmlist.php
 * @version 1.0.3.14 16th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbfilmsModelFilmlist extends JModelList {
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array ('title', 'a.title',
					'rel_year','a.rel_year', 'first_seen', 'a.first_seen',
					'averat', 'last_seen', 'a.last_seen',					
					'catid', 'a.catid', 'category_id',
					'category_title','tagfilt' );
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {
		$app = Factory::getApplication('site');
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
		$categoryId = $app->getUserStateFromRequest('catid', 'catid','');
		$app->setUserState('catid', '');
		$this->setState('categoryId',$categoryId);
		
		$tagId = $app->getUserStateFromRequest('tagid', 'tagid','');
		$app->setUserState('tagid', '');
		$this->setState('tagId',$tagId);
		
		parent::populateState($ordering, $direction);
		
		//pagination limit
		$limit = $this->getUserStateFromRequest($this->context.'.limit', 'limit', 25 );
		$this->setState('limit', $limit);
		$this->setState('list.limit', $limit);
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', $app->get('start'));
		$this->setState('list.start', $limitstart);
		
	}
	
	protected function getListQuery() {
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.id AS id, a.title AS title, a.subtitle AS subtitle, a.alias AS alias,
            a.summary AS summary, a.rel_year AS rel_year, a.catid AS catid, 
            a.poster_img AS poster_img, a.synopsis AS synopsis, a.state AS published,
            a.created AS created,  a.first_seen AS first_seen, a.last_seen AS last_seen,
            a.created_by_alias AS created_by_alias,
            a.ordering AS ordering, a.params AS params'); 
            $query->from('#__xbfilms AS a');
            
            $query->select('(SELECT COUNT(DISTINCT(fp.person_id)) FROM #__xbfilmperson AS fp WHERE fp.film_id = a.id) AS pcnt');
            $query->select('(SELECT COUNT(DISTINCT(fc.char_id)) FROM #__xbfilmcharacter AS fc WHERE fc.film_id = a.id) AS ccnt');
            $query->select('(SELECT COUNT(DISTINCT(fg.group_id)) FROM #__xbfilmgroup AS fg WHERE fg.film_id = a.id) AS gcnt');
            
            //             	->join('LEFT OUTER',$db->quoteName('#__xbfilmperson', 'p') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('p.film_id'))
//             	->join('LEFT OUTER',$db->quoteName('#__xbfilmcharacter', 'ch') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('ch.film_id'));

            $query->select('c.title AS category_title');
            $query->join('LEFT', '#__categories AS c ON c.id = a.catid');
            
            $query->select('(SELECT AVG(br.rating) FROM #__xbfilmreviews AS br WHERE br.film_id=a.id) AS averat');
//            $query->select('GREATEST(a.acq_date, COALESCE(a.last_seen, 0)) AS sort_date');
            
            // Filter by published state, we only show published items in front end. Both item and its category must be published.
            $query->where('a.state = 1');
            $query->where('c.published = 1');
            
            // Search in title/id/synop
            $search = $this->getState('filter.search');            
            if (!empty($search)) {
                if ((stripos($search,'d:')===0) || (stripos($search,'s:')===0)) {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
            		$query->where('(a.synopsis LIKE ' . $search.' OR a.summary LIKE '.$search.')');
            	} else {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            		$query->where('(a.title LIKE ' . $search . ' OR a.subtitle LIKE ' . $search . ')');
            	}
            }
            
             $searchbar = (int)$this->getState('params')['search_bar'];
            //if a menu filter is set this takes priority and serch filter field is hidden
 
           // Filter by category
             $categoryId = $this->getState('categoryId');
             $this->setState('categoryId','');
             if (empty($categoryId)) {
	            $categoryId = $this->getState('params')['menu_category_id'];
             }
            if (($searchbar==1) && ($categoryId==0)){
            	$categoryId = $this->getState('filter.category_id');
            }
            if ((is_numeric($categoryId)) && ($categoryId > 0) ){
                $query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
            } elseif (is_array($categoryId)) {
                $catlist = implode(',', $categoryId);
                $query->where($db->quoteName('a.catid') . ' IN ('.$catlist.')');
            }
            
            //             $catlist = '';
//             if ($dosubcats) {               
//                 if (is_array($categoryId)) {
//                     foreach ($categoryId as $cat) {
//                         $catlist .= implode(',',XbcultureHelper::getChildCats($categoryId,'com_xbfilms',true));
//                     }
//                 } elseif ((is_numeric($categoryId)) && ($categoryId > 0) ) {
//                     $catlist .= implode(',',XbcultureHelper::getChildCats($categoryId,'com_xbfilms',true));
//                 }
//                 $query->where($db->quoteName('a.catid') . ' IN ('.$catlist.')');
//             } else {                
//                 if ((is_numeric($categoryId)) && ($categoryId > 0) ){
//                     $query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
//                 } elseif (is_array($categoryId)) {
//                     $catlist = implode(',', $categoryId);
//                     $query->where($db->quoteName('a.catid') . ' IN ('.$catlist.')');
//                 }
//             }
            
            
            //filter by seen/unseen
            $seenfilt = $this->getState('filter.seenfilt');
            if ((int)$seenfilt==1) {
                $query->where('a.first_seen > 0');
            } elseif ($seenfilt==2) {
                $query->where('COALESCE(a.first_seen,0) = 0');
            }
            
            //filter by person 
            $pfilt = $this->getState('params')['menu_perfilt'];
            $prole = $this->getState('params')['menu_prole'];
            if (($searchbar==1) && ($pfilt==0)) { 	//look for filter setting
            	$pfilt = $this->getState('filter.perfilt');
            	$prole = $this->getState('filter.prole');
            }
            if ((int)$pfilt>0) {
            	$query->where('p.person_id = '.$pfilt);
            	if ($prole == 1 ) { $query->where('p.role = '.$db->quote('director'));}
            	if ($prole == 2 ) { $query->where('p.role = '.$db->quote('producer'));}
            }
            
            //filter by character
            $chfilt = $this->getState('params')['menu_charfilt'];
            if (($searchbar==1) && ($chfilt==0)) { 	//look for filter setting
            	$chfilt = $this->getState('filter.charfilt');
            }
            if ((int)$chfilt>0) {
            	$query->where('ch.char_id = '.$chfilt);
            }
            
            //filter by tag
            $tagfilt = $this->getState('tagId');
            // $this->setState('tagId','');
            $taglogic = 0;
            if (empty($tagfilt)) {
                $tagfilt = $this->getState('params')['menu_tag'];
                $taglogic = $this->getState('params')['menu_taglogic']; //1=AND otherwise OR
            }
            if ((!is_array($tagfilt)) && (!empty($tagfilt))) {
                $tagfilt = array($tagfilt);
            }
            
            if (($searchbar==1) && (empty($tagfilt))) { 	//look for menu options
                //look for filter options and ignore menu options
                $tagfilt = $this->getState('filter.tagfilt');
                $taglogic = $this->getState('filter.taglogic'); //1=AND otherwise OR
            }
            
            if (empty($tagfilt)) {
                $subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias LIKE '.$db->quote('com_xbfilms.film').')';
                if ($taglogic === '1') {
                    $query->where('a.id NOT IN '.$subQuery);
                } elseif ($taglogic === '2') {
                    $query->where('a.id IN '.$subQuery);
                }
            } else {
                $tagfilt = ArrayHelper::toInteger($tagfilt);
                $subquery = '(SELECT tmap.tag_id AS tlist FROM #__contentitem_tag_map AS tmap
                WHERE tmap.type_alias = '.$db->quote('com_xbfilms.film').'
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
                                WHERE tag_id IN ('.$tagIds.') AND type_alias = '.$db->quote('com_xbfilms.film').')';
                                $query->innerJoin('(' . (string) $subQueryAny . ') AS tagmap ON tagmap.content_item_id = a.id');
                            }
                        }
                        break;
                }
            } //end if $tagfilt
            
            
            // Add the list ordering clause.
            $orderCol       = $this->state->get('list.ordering', 'last_seen');
            $orderDirn      = $this->state->get('list.direction', 'DESC');
            switch($orderCol) {
                case 'last_seen' :
                case 'first_seen' :
                    $query->order('CASE WHEN '.$orderCol.' IS NULL THEN 1 ELSE 0 END, '.$orderCol.' '.$orderDirn.', title');
                    break;
            	case 'a.ordering' :
            	case 'a.catid' :
            		//needs a menu option to set orderCol to ordering. Also menu option to alllow user to reorder on table
            		$query->order('category_title '.$orderDirn.', a.ordering');
            		break;
            	case 'category_title':
            		$query->order('category_title '.$orderDirn.', title');
            		break;
            	default:
            		$query->order($db->escape($orderCol.' '.$orderDirn));
            		break;
            }
            
            $query->group('a.id');                  
            return $query;
	}
	
	public function getItems() {
		$items  = parent::getItems();
		$tagsHelper = new TagsHelper;
		
		$app = Factory::getApplication();
		$bks = array();
		for ($i = 0; $i < count($items); $i++) {
		    $bks[$i] = $items[$i]->id;
		}
		$app->setUserState('films.sortorder', $bks);
		
		
		foreach ($items as $i=>$item) {
		    if ($item->pcnt>0) {
		        $item->people = XbfilmsGeneral::getFilmPeople($item->id);
    			$roles = array_column($item->people,'role');
    			$item->dircnt = count(array_keys($roles, 'director'));
    			$item->dirlist = $item->dircnt==0 ? '' : XbcultureHelper::makeItemLists($item->people,'director','t',3,'person');
    			$item->prodcnt = count(array_keys($roles, 'producer'));
    			$item->prodlist = $item->prodcnt==0 ? '' : XbcultureHelper::makeItemLists($item->people,'producer','t',3,'person');
    			$item->crewcnt = count(array_keys($roles, 'crew'));
    			$item->crewlist = $item->crewcnt==0 ? '' : XbcultureHelper::makeItemLists($item->people,'crew','t',3,'person');
    			$item->subjcnt = count(array_keys($roles, 'appearsin'));
    			$item->subjlist = $item->subjcnt==0 ? '' : XbcultureHelper::makeItemLists($item->people,'appearsin','t',3,'person');
    			$item->castcnt = count(array_keys($roles, 'actor'));
    			$item->castlist = $item->castcnt==0 ? '' : XbcultureHelper::makeItemLists($item->people,'actor','t',3,'person');
		    } else {
		        $item->dircnt = 0; $item->prodcnt = 0; $item->crewcnt = 0; $item->subjcnt = 0; $item->castcnt = 0;
		    }
		    
		    if ($item->ccnt>0) {
		        $item->chars = XbfilmsGeneral::getFilmChars($item->id);
		        $item->charlist = XbcultureHelper::makeItemLists($item->chars,'','t',3,'char');
		    }
		    if ($item->gcnt>0) {
		        $item->groups = XbfilmsGeneral::getFilmGroups($item->id);
		        $item->grouplist = XbcultureHelper::makeItemLists($item->groups,'','t',3,'group');
		    }
			
			$item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
			$item->revcnt = count($item->reviews);
			
			$item->tags = $tagsHelper->getItemTags('com_xbfilms.film' , $item->id);			
			
		} //foreach item
		return $items;
	}	
		
}
