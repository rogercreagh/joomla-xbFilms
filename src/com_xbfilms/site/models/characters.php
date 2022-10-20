<?php
/*******
 * @package xbFilms
 * @filesource site/models/characters.php
 * @version 0.9.9.6 25th August 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbfilmsModelCharacters extends JModelList {
	
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array ('name', 'category_title','c.title',
					'catid', 'a.catid', 'category_id', 'tagfilt', 'fcnt'
			);
		}
		$this->xbbooksStatus = Factory::getSession()->get('com_xbbooks',false);
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
		
		$query->select('a.id AS id, a.name AS name, 
            a.summary AS summary, a.catid AS catid,
            a.image AS image, a.description AS description, a.state AS published,
            a.created AS created, a.created_by_alias AS created_by_alias,
            a.ordering AS ordering, a.params AS params, a.note AS note');
            $query->from('#__xbcharacters AS a')
            	->join('LEFT OUTER',$db->quoteName('#__xbfilmcharacter', 'p') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('p.char_id'))
            	->where('p.film_id IS NOT NULL');
            $query->select('COUNT(DISTINCT p.film_id) AS fcnt');
            $query->join('LEFT', '#__categories AS c ON c.id = a.catid');
            $query->select('c.title AS category_title');
            
            // Filter by published state, we only show published items in the front-end. Both item and its category must be published.
            $query->where('a.state = 1');
            $query->where('c.published = 1');
            
             // Filter by search in title/id/synop
            $search = $this->getState('filter.search');
            
            if (!empty($search)) {
            	if (stripos($search,'s:')===0) {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
            		$query->where('(a.description LIKE ' . $search.' OR a.summary LIKE '.$search.')');
            	} else {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            		$query->where('(a.name LIKE ' . $search . ' )');
            	}
            }
            
            $searchbar = (int)$this->getState('params',0)['search_bar'];
            //if menu option set it will take precedence and hide the corresponding filter option
            
           // Filter by category and subcats
            $categoryId = $this->getState('categoryId');
            $this->setState('categoryId','');
            $dosubcats = 0;
            if (empty($categoryId)) {
                $categoryId = $this->getState('params',0,'int')['menu_category_id'];
                $dosubcats=$this->getState('params',0)['menu_subcats'];
            }
            if (($searchbar==1) && ($categoryId==0)){
            	$categoryId = $this->getState('filter.category_id');
            	$dosubcats=$this->getState('filter.subcats');
            }
 //           if ($this->getState('catid')>0) { $categoryId = $this->getState('catid'); }
            if ($categoryId > 0) {
            	if ($dosubcats) {
            		$catlist = $categoryId;
            		$subcatlist = XbcultureHelper::getChildCats($categoryId,'com_xbpeople');
            		if ($subcatlist) { $catlist .= ','.implode(',',$subcatlist);}
            		$query->where('a.catid IN ('.$catlist.')');
            	} else {
            		$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
            	}
            }
            
            //filter by tag
            $tagfilt = $this->getState('tagId');
            // $this->setState('tagId','');
            $taglogic = 0;
            if (empty($tagfilt)) {
                $tagfilt = $this->getState('params')['menu_tag'];
                $taglogic = $this->getState('params')['menu_taglogic']; //1=AND otherwise OR
            }
            
            if (($searchbar==1) && (empty($tagfilt))) { 	//look for menu options
                //look for filter options and ignore menu options
                $tagfilt = $this->getState('filter.tagfilt');
                $taglogic = $this->getState('filter.taglogic'); //1=AND otherwise OR
            }
            
            if (($taglogic === '2') && (empty($tagfilt))) {
                //if if we select tagged=excl and no tags specified then only show untagged items
                $subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map
 					WHERE type_alias = '.$db->quote('com_xbpeople.character').')';
                $query->where('a.id NOT IN '.$subQuery);
            }
            if ($tagfilt && is_array($tagfilt)) {
                $tagfilt = ArrayHelper::toInteger($tagfilt);
                $subquery = '(SELECT tmap.tag_id AS tlist FROM #__contentitem_tag_map AS tmap
                WHERE tmap.type_alias = '.$db->quote('com_xbpeople.character').'
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
                        if (count($tagfilt)==1) {
                            $query->where($tagfilt[0].' IN '.$subquery);
                        } else {
                            $query->where('1=1'); //fudge to ensure there is a where clause to extend
                            $query->extendWhere('AND', $conds, 'OR');
                        }
                        break;
                }
            } //endif tagfilt
            
            // Add the list ordering clause.
            $orderCol       = $this->state->get('list.ordering', 'name');
            $orderDirn      = $this->state->get('list.direction', 'ASC');
            switch($orderCol) {
                case 'a.ordering' :
                case 'a.catid' :
                    //needs a menu option to set orderCol to ordering. Also menu option to alllow user to reorder on table
                    $query->order('category_title '.$orderDirn.', a.ordering');
                    break;
                case 'category_title':
                    $query->order('category_title '.$orderDirn.', name');
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
		$peep = array();
		for ($i = 0; $i < count($items); $i++) {
			$peep[$i] = $items[$i]->id;
		}
		$app->setUserState('characters.sortorder', $peep);
		
		foreach ($items as $i=>$item) {
			//get films by role if they are being displayed...
		    $item->films = ($item->fcnt>0) ? XbcultureHelper::getCharFilms($item->id) : ''; 
		    
		    $item->bcnt = 0;
		    if ($this->xbbooksStatus) {
		        $db    = Factory::getDbo();
		        $query = $db->getQuery(true);
		        $query->select('COUNT(DISTINCT book_id) AS bcnt')->from('#__xbbookcharacter');
		        $query->where('person_id = '.$db->quote($item->id));
		        $db->setQuery($query);
		        $item->bcnt = $db->loadResult();
		    }
		    
		    //  XbfilmsHelper::getCharacterFilmsArray($item->id);
//			$item->ccnt = count($item->films);
			
// 			//makedirectoreditor/char lists
// 			if ($item->ccnt == 0){
// 				$item->clist = '';
// 			} else {
// 				$item->clist = XbfilmsGeneral::makeLinkedNameList($item->films,'',', ',true);
// 			}
			
			$item->tags = $tagsHelper->getItemTags('com_xbpeople.character' , $item->id);
			
		} //end foreach item
		return $items;
	}
		
}
            
            
