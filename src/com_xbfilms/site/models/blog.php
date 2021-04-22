<?php
/*******
 * @package xbFilms
 * @filesource site/models/blog.php
 * @version 0.5.0 28th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

//jimport('joomla.application.component.modellist');
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;

class XbfilmsModelBlog extends JModelList {
		
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array ('title','category_title','rating', 
			'film_title','rev_date' );
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
		$limit = $this->getUserStateFromRequest($this->context.'.limit', 'limit', 25 );
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
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__xbfilms AS f ON f.id = a.film_id');
		$query->select('f.id AS fid, f.title AS film_title, f.subtitle AS subtitle, f.poster_img AS poster_img,f.summary AS film_summary, 
			f.synopsis AS synopsis, f.rel_year AS rel_year, f.country AS country, f.runtime AS runtime, f.catid AS fcatid');
		$query->join('LEFT', '#__categories AS fc ON fc.id = f.catid');
		$query->select('fc.title AS fcat_title');
		
		//ignore ones without review
		$query->where("a.review !=''");
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
 
		// Filter by category.
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
				$subcatlist = XbfilmsHelper::getChildCats($categoryId);
				if ($subcatlist) { $catlist .= ','.implode(',',$subcatlist);}
				$query->where('a.catid IN ('.$catlist.')');
			} else {
				$query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
			}
		}		
		
		//filter by tag
		$tagfilt = $this->getState('tagId');
		$this->setState('tagId','');
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
		// Run simplified query when filtering by one tag.
		if (is_array($tagfilt) && count($tagfilt) === 1) {
			$tagfilt = $tagfilt[0];
		}
		if ($tagfilt && is_array($tagfilt)) {
			$tagfilt = ArrayHelper::toInteger($tagfilt);
			if ($taglogic) { //AND logic
				for ($i = 0; $i < count($tagfilt); $i++) {
					$mapname = 'tagmap'.$i;
					$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', $mapname).
							' ON ' . $db->quoteName($mapname.'.content_item_id') . ' = ' . $db->quoteName('a.id'));
					$query->where( array(
							$db->quoteName($mapname.'.tag_id') . ' = ' . $tagfilt[$i],
							$db->quoteName($mapname.'.type_alias') . ' = ' . $db->quote('com_xbfilms.review'))
							);
				}
			} else { //OR logic
				$subQuery = $db->getQuery(true)
				->select('DISTINCT ' . $db->quoteName('content_item_id'))
				->from($db->quoteName('#__contentitem_tag_map'))
				->where(
						array(
								$db->quoteName('tag_id') . ' IN (' . implode(',', $tagfilt) . ')',
								$db->quoteName('type_alias') . ' = ' . $db->quote('com_xbfilms.review'),
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
									$db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_xbfilms.review')
							)
							);
		} //endif tagfilt
		
		//filter by rating
		$ratfilt = $this->getState('filter.ratfilt');
		if (!empty($ratfilt)) {
			$query->where('a.rating = '.$db->quote($ratfilt));
		}
		
		//filter by reviewer
		$reviewer = $this->getState('filter.reviewer');
		if (!empty($reviewer)) {
			$query->where('a.reviewer = '.$db->quote($reviewer));
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
			$dirs = XbfilmsGeneral::getFilmRoleArray($item->id,'director');
			$item->dircnt = count($dirs);
			if ($item->dircnt==0){
				$item->dlist = ''; 
			} else {
				$item->dlist = XbfilmsGeneral::makeLinkedNameList($dirs,'director',', ',true, false);
			}
			$item->tags = $tagsHelper->getItemTags('com_xbfilms.review' , $item->id);
			$item->ftags = $tagsHelper->getItemTags('com_xbfilms.film' , $item->fid);			
			//get film director(s) stars 
		} //foreach item
		
		return $items;		
	}
}
