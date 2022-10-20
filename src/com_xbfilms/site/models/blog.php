<?php
/*******
 * @package xbFilms
 * @filesource site/models/blog.php
 * @version 0.9.9.8 20th October 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

//jimport('joomla.application.component.modellist');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbfilmsModelBlog extends JModelList {
		
	public function __construct($config = array()) {
	    $showrevs = ComponentHelper::getParams('com_xbfilms')->get('show_revs',1);
	    if (!$showrevs) {
	        header('Location: index.php?option=com_xbfilms&view=filmlist');
	        exit();
	    }
	    
	    if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'a.id',
					'title', 'a.title',
					'ordering','a.ordering',
					'category_title', 'c.title',
					'catid', 'a.catid', 'category_id',
					'fcatid', 'f.catid', 'fcategory_id',
					'first_seen', 'a.first_seen', 'last_seen', 'a.last_seen',
					'published','a.state',
					'rel_year','a.rel_year','tagfilt');
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null) {
		// Load the parameters.
		$app = Factory::getApplication();
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
		$limit = $this->getUserStateFromRequest($this->context.'.limit', 'limit', 5 );
		$this->setState('limit', $limit);
		$this->setState('list.limit', $limit);
		$limitstart = $app->getUserStateFromRequest('limitstart', 'limitstart', $app->get('start'));
		$this->setState('list.start', $limitstart);
		
	}
	
	protected function getListQuery() {
		
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.id AS id, a.title AS title, a.alias AS alias,
            a.summary AS summary, a.review AS review, a.catid AS catid,
            a.rating AS rating, a.film_id AS film_id, a.state AS published,
            a.rev_date AS rev_date, a.where_seen AS where_seen, a.reviewer AS reviewer,
            a.ordering AS ordering, a.params AS params');
		$query->from('#__xbfilmreviews AS a');
		$query->select('(SELECT AVG(br.rating) FROM #__xbfilmreviews AS br WHERE br.film_id=a.film_id) AS averat');
		$query->select('(SELECT COUNT(fr.rating) FROM #__xbfilmreviews AS fr WHERE fr.film_id=a.film_id) AS ratcnt');
		
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__xbfilms AS f ON f.id = a.film_id');
		$query->select('f.id AS fid, f.title AS film_title, f.subtitle AS subtitle, f.poster_img AS poster_img,f.summary AS film_summary, 
			f.synopsis AS synopsis, f.rel_year AS rel_year, f.country AS country, f.runtime AS runtime, f.catid AS fcatid');
		$query->join('LEFT', '#__categories AS fc ON fc.id = f.catid');
		$query->select('fc.title AS fcat_title');
		
		
		// Filter by published state 
		// 	`category and film must both be published state=1 as well 
		$query->where('a.state = 1');
		$query->where('c.published = 1');
		$query->where('f.state = 1');
		
		// Filter by search in title/sum/rev
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search,'s:')===0) {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
				$query->where('(a.review LIKE ' . $search.' OR a.summary LIKE '.$search.')');
			} else {
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where('a.title LIKE ' . $search );
			}
		}
		
		$searchbar = (int)$this->getState('params',0)['search_bar'];
		//if a menu filter is set this takes priority and serch filter field is hidden
 
		// Filter by review category.
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
				$catlist = $categoryId;
				$subcatlist = XbcultureHelper::getChildCats($categoryId,'com_xbfilms');
				if ($subcatlist) { $catlist .= ','.implode(',',$subcatlist);}
				$query->where('a.catid IN ('.$catlist.')');
			} else {
				$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
			}
		}
		
		// Filter by film category.
		$fcategoryId = $this->getState('fcategoryId');
		$this->setState('fcategoryId','');
		$dosubcats = 0;
		if (empty($fcategoryId)) {
			$fcategoryId = $this->getState('params',0,'int')['menu_fcategory_id'];
			$dosubcats=$this->getState('params',0)['menu_subcats'];
		}
		if (($searchbar==1) && ($fcategoryId==0)){
			$fcategoryId = $this->getState('filter.fcategory_id');
			$dosubcats=$this->getState('filter.subcats');
		}
		if ($fcategoryId > 0) {
			if ($dosubcats) {
				$catlist = $fcategoryId;
				$subcatlist = XbcultureHelper::getChildCats($fcategoryId,'com_xbfilms');
				if ($subcatlist) { $catlist .= ','.implode(',',$subcatlist);}
				$query->where('f.catid IN ('.$catlist.')');
			} else {
				$query->where($db->quoteName('f.catid') . ' = ' . (int) $fcategoryId);
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
                 WHERE type_alias IN ('.$db->quote('com_xbfilms.film').','.$db->quote('com_xbfilms.review').')';
   		    $query->where('a.id NOT IN '.$subQuery);
		}
		if ($tagfilt && is_array($tagfilt)) {
		    $tagfilt = ArrayHelper::toInteger($tagfilt);
		    $subquery = '(SELECT tmap.tag_id AS tlist FROM #__contentitem_tag_map AS tmap
                WHERE tmap.type_alias IN ('.$db->quote('com_xbfilms.film').','.$db->quote('com_xbfilms.review').') 
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
		
		//filter by rating
		$ratfilt = $this->getState('filter.ratfilt');
		if (!empty($ratfilt)) {
			$query->where('a.rating = '.$db->quote($ratfilt));
		}
		
		//filter by review date
		$yearfilt = $this->getState('filter.rev_year');
		if ($yearfilt != '') {
			$query->where('YEAR(rev_date) = '.$db->quote($yearfilt));
			$monthfilt = $this->getState('filter.rev_month');
			if ($monthfilt != '') {
				$query->where('MONTH(rev_date) = '.$db->quote($monthfilt));
			}			
		}
		
		// Add the list ordering clause.
		$orderCol       = $this->state->get('list.ordering', 'rev_date');
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
		$app->setUserState('filmreviews.sortorder', $bks);

		foreach ($items as $i=>$item) {			
			$dirs = XbfilmsGeneral::getFilmPeople($item->film_id,'director');
			//TODO what about other roles - cast etc
			$item->dircnt = count($dirs);
			$item->dirlist = $item->dircnt==0 ? '' : XbcultureHelper::makeLinkedNameList($dirs,'director','comma',true, 1);
			
			$item->tags = $tagsHelper->getItemTags('com_xbfilms.review' , $item->id);
			$item->ftags = $tagsHelper->getItemTags('com_xbfilms.film' , $item->fid);			
			//get film director(s) stars 
		} //foreach item
		
		return $items;		
	}
	
}
