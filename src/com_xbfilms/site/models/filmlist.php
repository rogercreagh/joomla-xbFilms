<?php
/*******
 * @package xbFilms
 * @filesource site/models/filmlist.php
 * @version 0.9.9.3 13th July 2022
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
					'rel_year','a.rel_year',
					'averat', 'sort_date', 'last_seen', 'a.last_seen',					
					'catid', 'a.catid', 'category_id',
					'category_title' );
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
            a.created AS created,  a.acq_date AS acq_date, a.last_seen AS last_seen,
            a.created_by_alias AS created_by_alias,
            a.ordering AS ordering, a.params AS params'); 
//            ->select('(GROUP_CONCAT(p.person_id SEPARATOR '.$db->quote(',') .')) AS personlist');
            $query->from('#__xbfilms AS a')
            	->join('LEFT OUTER',$db->quoteName('#__xbfilmperson', 'p') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('p.film_id'))
            	->join('LEFT OUTER',$db->quoteName('#__xbfilmcharacter', 'ch') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('ch.film_id'));
            	//->join('LEFT', $db->quoteName('#__xbfilmreviews', 'r').' ON r.film_id = a.id');
            $query->select('c.title AS category_title');
            $query->join('LEFT', '#__categories AS c ON c.id = a.catid');
            
//            $query->select('(SELECT COUNT(*) FROM #__xbfilmreviews AS br WHERE br.film_id=a.id AND br.state=1) AS revcnt');
            $query->select('(SELECT AVG(br.rating) FROM #__xbfilmreviews AS br WHERE br.film_id=a.id) AS averat');
//            $query->select('(SELECT MAX(fr.rev_date) FROM #__xbfilmreviews AS fr WHERE fr.film_id=a.id) AS lastseen');
            $query->select('GREATEST(a.acq_date, COALESCE(a.last_seen, 0)) AS sort_date');
            
            // Filter by published state, we only show published items in front end. Both item and its category must be published.
            $query->where('a.state = 1');
            $query->where('c.published = 1');
            
            // Search in title/id/synop
            $search = $this->getState('filter.search');            
            if (!empty($search)) {
            	if (stripos($search,'s:')===0) {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
            		$query->where('(a.synopsis LIKE ' . $search.' OR a.summary LIKE '.$search.')');
            	} else {
            		$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
            		$query->where('(a.title LIKE ' . $search . ' OR a.subtitle LIKE ' . $search . ')');
            	}
            }
            
             $searchbar = (int)$this->getState('params',0)['search_bar'];
            //if a menu filter is set this takes priority and serch filter field is hidden
 
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
            if ($categoryId > 0) {
            	if ($dosubcats) {
            		//TODO improve this by getting details for categoryId and using lft and rgt
            		$catlist = $categoryId;
            		$subcatlist = XbcultureHelper::getChildCats($categoryId,'com_xbfilms');
            		if ($subcatlist) { $catlist .= ','.implode(',',$subcatlist);}
            		$query->where('a.catid IN ('.$catlist.')');
            	} else {
            		$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
            	}
            }
            
            //filter by seen/unseen
            $seenfilt = $this->getState('filter.seenfilt');
            if ((int)$seenfilt==1) {
                $query->where('a.last_seen > 0');
            } elseif ($seenfilt==2) {
                $query->where('COALESCE(a.last_seen,0) = 0');
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
            $this->setState('tagId','');
            $taglogic = 0;
            if (empty($tagfilt)) {
                $tagfilt = $this->getState('params')['menu_tag'];
                $taglogic = $this->getState('params')['taglogic']; //1=AND otherwise OR           	
            }
                       
            if (($searchbar==1) && (empty($tagfilt))) { 
            //look for filter options and ignore menu options 
	            $tagfilt = $this->getState('filter.tagfilt');
	            $taglogic = $this->getState('filter.taglogic'); //1=AND otherwise OR
            }
            // Run simplified query when filtering by one tag.
            if (is_array($tagfilt) && count($tagfilt) === 1) {
            	$tagfilt = $tagfilt[0];
            }           
            if ($tagfilt && is_array($tagfilt)) {
            	$tagfilt = ArrayHelper::toInteger($tagfilt);            	
            	if ($taglogic) { //AND logic
            		/***
            		 SELECT id FROM j3tst_xbfilms AS a
            		 INNER JOIN j3tst_contentitem_tag_map AS tagmap
            		 ON tagmap.content_item_id  = a.id
            		 INNER JOIN j3tst_contentitem_tag_map AS tagmap2
            		 ON tagmap2.content_item_id  = a.id
            		 WHERE tagmap.tag_id = 5 AND tagmap.type_alias = 'com_xbfilms.film'
            		 AND tagmap2.tag_id = 2 AND tagmap2.type_alias = 'com_xbfilms.film'
            		 ***/
            		for ($i = 0; $i < count($tagfilt); $i++) {
            			$mapname = 'tagmap'.$i;
            			$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', $mapname).
            					' ON ' . $db->quoteName($mapname.'.content_item_id') . ' = ' . $db->quoteName('a.id'));
            			$query->where( array(
            							$db->quoteName($mapname.'.tag_id') . ' = ' . $tagfilt[$i],
            							$db->quoteName($mapname.'.type_alias') . ' = ' . $db->quote('com_xbfilms.film'))
            					);
            		}
            	} else { //OR logic           		
            		$subQuery = $db->getQuery(true)
            		->select('DISTINCT ' . $db->quoteName('content_item_id'))
            		->from($db->quoteName('#__contentitem_tag_map'))
            		->where(
            				array(
            						$db->quoteName('tag_id') . ' IN (' . implode(',', $tagfilt) . ')',
            						$db->quoteName('type_alias') . ' = ' . $db->quote('com_xbfilms.film'),
            				)
            				);
            		
            		$query->join(
            				'INNER',
            				'(' . $subQuery . ') AS ' . $db->quoteName('tagmap')
            				. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
            				);
            		
            	}
            	
            } elseif ($tag = (int) $tagfilt) {
            	$query->join(
            			'INNER',
            			$db->quoteName('#__contentitem_tag_map', 'tagmap')
            			. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
            			)
            			->where(
            					array(
            							$db->quoteName('tagmap.tag_id') . ' = ' . $tagfilt,
            							$db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_xbfilms.film')
            					)
            					);
            } //endif tagfilt
            
            
            // Add the list ordering clause.
            $orderCol       = $this->state->get('list.ordering', 'sort_date');
            $orderDirn      = $this->state->get('list.direction', 'DESC');
            switch($orderCol) {
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
			$item->people = XbfilmsGeneral::getFilmRoleArray($item->id);
			$cnts = array_count_values(array_column($item->people, 'role'));
			$item->dircnt = (key_exists('director',$cnts))? $cnts['director'] : 0;
			$item->editcnt = (key_exists('producer',$cnts))? $cnts['producer'] : 0;
			
			$item->chars = XbfilmsHelper::getCharacterFilmsArray($item->id);
			$item->charcnt = count($item->chars);
			
			$item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
			$item->revcnt = count($item->reviews);
			
			//make director editor lists
			$item->dlist = $item->dircnt==0 ? '' : XbfilmsGeneral::makeLinkedNameList($item->people,'director',',', (($item->editcnt)==0)? true:false) ;
			$item->elist = $item->editcnt==0 ? '' : XbfilmsGeneral::makeLinkedNameList($item->people,'producer',',');
			
			if (($item->charcnt)==0){
				$item->clist = '';
			} elseif ($item->charcnt == 1) {
				$item->clist = XbfilmsGeneral::makeLinkedNameList($item->chars,'',', ',true);
			} else {
				$item->clist = XbfilmsGeneral::makeLinkedNameList($item->chars,'',', ',false);
			}
			
			$item->tags = $tagsHelper->getItemTags('com_xbfilms.film' , $item->id);			
			
		} //foreach item
		return $items;
	}	
		
}
