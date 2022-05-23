<?php
/*******
 * @package xbFilms
 * @filesource admin/models/films.php
 * @version 0.9.5 8th Mayl 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;


class XbfilmsModelFilms extends JModelList
{

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array( 
            		'id', 'a.id',
            		'title', 'a.title',
            		'ordering','a.ordering',
            		'category_title', 'c.title',
            		'catid', 'a.catid', 'category_id',
            		'acq_date', 'a.acq_date', 
            		'published','a.state',            		  
            		'rel_year','a.rel_year');
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = 'acq_date', $direction = 'desc') {
        $app = Factory::getApplication();
        
        // Adjust the context to support modal layouts.
            	if ($layout = $app->input->get('layout')) {
            		$this->context .= '.' . $layout;
            	}
        
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);
        $categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
        $this->setState('filter.categoryId', $categoryId);
        $tagfilt = $this->getUserStateFromRequest($this->context . '.filter.tagfilt', 'filter_tagfilt', '');
        $this->setState('filter.tagfilt', $tagfilt);
        $filt = $this->getUserStateFromRequest($this->context . '.filter.taglogic', 'filter_taglogic');
        $this->setState('filter.taglogic', $filt);
//         $filt = $this->getUserStateFromRequest($this->context . '.filter.', 'filter_');
//         $this->setState('filter.', $filt);
                
        $formSubmited = $app->input->post->get('form_submited');
        
        if ($formSubmited)
        {
            $categoryId = $app->input->post->get('category_id');
            $this->setState('filter.category_id', $categoryId);
            
            $tagfilt = $app->input->post->get('tagfilt');
            $this->setState('filter.tagfilt', $tagfilt);
        }
        
        // List state information.
        parent::populateState($ordering, $direction);
        
    }
        
    protected function getListQuery() {
	
//		$user   = JFactory::getUser();
		$db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.subtitle AS subtitle, a.alias AS alias, 
            a.summary AS summary, a.rel_year AS rel_year, a.catid AS catid, 
            a.poster_img AS poster_img, a.synopsis AS synopsis, a.state AS published, 
            a.created AS created, a.created_by AS created_by, a.acq_date AS acq_date,
            a.created_by_alias AS created_by_alias, a.ext_links AS ext_links,
            a.checked_out AS checked_out, a.checked_out_time AS checked_out_time, 
            a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
		$query->from('#__xbfilms AS a')
        //join to persons and characters to allow filtering on them
		->join('LEFT OUTER',$db->quoteName('#__xbfilmperson', 'p') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('p.film_id'))
		->join('LEFT OUTER',$db->quoteName('#__xbfilmcharacter', 'ch') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('ch.film_id'));
		
		$query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');
        
        $query->select('(SELECT AVG(br.rating) FROM #__xbfilmreviews AS br WHERE br.film_id=a.id) AS averat');
        $query->select('(SELECT MAX(fr.rev_date) FROM #__xbfilmreviews AS fr WHERE fr.film_id=a.id) AS lastseen');
        
		// Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
                $query->where('state = ' . (int) $published);
        } else if ($published === '') {
                $query->where('(state IN (0, 1))');
        }

        // Filter by category.
        //TODO handle multiple cats
        $app = Factory::getApplication();
        //do we have a catid request, if so we need to over-ride any filter, but save the filter to re-instate?
        $categoryId = $app->getUserStateFromRequest('catid', 'catid','');
        $app->setUserState('catid', '');
//        $subcats=0;
        if ($categoryId=='') {
        	$categoryId = $this->getState('filter.category_id');
//        $subcats = $this->getState('filter.subcats');
        }
        if (is_numeric($categoryId)) {
//            if ($subcats) {
//                $query->where('a.catid IN ('.(int)$categoryId.','.self::getSubCategoriesList($categoryId).')');
//            } else {
                $query->where($db->quoteName('a.catid') . ' = ' . (int) $categoryId);
//            }
        }

        // Filter by search in title/id/synop
        $search = $this->getState('filter.search');
        
        if (!empty($search)) {
            if (stripos($search, 'i:') === 0) {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 2));
            } elseif (stripos($search,'s:')===0) {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim(substr($search,2)), true) . '%'));
                $query->where('(a.synopsis LIKE ' . $search.' OR a.summary LIKE '.$search.')');
            } elseif (stripos($search,':')!= 1) {           	
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(a.title LIKE ' . $search . ' OR a.subtitle LIKE ' . $search . ')');
            }
        }
        
        //filter by person (optional specify role)
        $pfilt = $this->getState('filter.perfilt');
        if (is_numeric($pfilt)) {
            $query->where('p.person_id = '.$db->quote($pfilt));
            $ptype = $this->getState('filter.pertype');
            if ($ptype != '') {
                $query->where('p.role = '.$db->quote($ptype));
            }
        }
        
        //filter by character 
        $chfilt = $this->getState('filter.charfilt');
        if (is_numeric($chfilt)) {
            $query->where('ch.char_id = '.$db->quote($chfilt));
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
 					WHERE type_alias = '.$db->quote('com_xbfilms.film').')';
            $query->where('a.id NOT IN '.$subQuery);
        }
        
        
        if (!empty($tagfilt)) { 
        	$tagfilt = ArrayHelper::toInteger($tagfilt);
	        
	        if ($taglogic==2) { //exclude anything with a listed tag
	        	// subquery to get a virtual table of item ids to exclude
	        	$subQuery = '(SELECT content_item_id FROM #__contentitem_tag_map 
					WHERE type_alias = '.$db->quote('com_xbfilms.film'). 
	        		' AND tag_id IN ('.implode(',',$tagfilt).'))';
	        	$query->where('a.id NOT IN '.$subQuery);
	        } else {
	        	if (count($tagfilt)==1)	{ //simple version for only one tag
	        		$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', 'tagmap')
	        				. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id') )
	        			->where(array( $db->quoteName('tagmap.tag_id') . ' = ' . $tagfilt[0],
	        						$db->quoteName('tagmap.type_alias') . ' = ' . $db->quote('com_xbfilms.film') )
	        			);
	        	} else { //more than one tag
	        		if ($taglogic == 1) { // match ALL listed tags
	        			// iterate through the list adding a match condition for each
	        			for ($i = 0; $i < count($tagfilt); $i++) {
	        				$mapname = 'tagmap'.$i;
	        				$query->join( 'INNER', $db->quoteName('#__contentitem_tag_map', $mapname).
	        						' ON ' . $db->quoteName($mapname.'.content_item_id') . ' = ' . $db->quoteName('a.id'));
	        				$query->where( array(
	        						$db->quoteName($mapname.'.tag_id') . ' = ' . $tagfilt[$i],
	        						$db->quoteName($mapname.'.type_alias') . ' = ' . $db->quote('com_xbfilms.film'))
	        						);
	        			}
	        		} else { // match ANY listed tag
	        			// make a subquery to get a virtual table to join on
			        	$subQuery = $db->getQuery(true)
			        	->select('DISTINCT ' . $db->quoteName('content_item_id'))
			        	->from($db->quoteName('#__contentitem_tag_map'))
			        	->where( array(
			        				$db->quoteName('tag_id') . ' IN (' . implode(',', $tagfilt) . ')',
			        				$db->quoteName('type_alias') . ' = ' . $db->quote('com_xbfilms.film'))
			        		);		        	
			        	$query->join(
			        			'INNER',
			        			'(' . $subQuery . ') AS ' . $db->quoteName('tagmap')
			        			. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
			        			);
	        			
	        		} //endif all/any
	        	} //endif one/many tag
	        }
        } //if not empty tagfilt
               
        // Add the list ordering clause.
        $orderCol       = $this->state->get('list.ordering', 'acq_date');
        $orderDirn      = $this->state->get('list.direction', 'DESC');
        if ($orderCol == 'a.ordering' || $orderCol == 'a.catid') {
                $orderCol = 'category_title '.$orderDirn.', a.ordering';  
        }

        $query->order($db->escape($orderCol.' '.$orderDirn));

		$query->group('a.id');
        return $query;
    }

	public function getItems() {
        $items  = parent::getItems();
		$tagsHelper = new TagsHelper;
		
        foreach ($items as $i=>$item) {  
            $item->people = XbfilmsGeneral::getFilmRoleArray($item->id,'', true);
            $cnts = array_count_values(array_column($item->people, 'role'));
            $item->dircnt = (key_exists('director',$cnts))? $cnts['director'] : 0;
            $item->prdcnt = (key_exists('producer',$cnts))? $cnts['producer'] : 0;
            $item->crewcnt = (key_exists('crew',$cnts))? $cnts['crew'] : 0;
            $item->actcnt = (key_exists('actor',$cnts))? $cnts['actor'] : 0;
            $item->appcnt = (key_exists('appearsin',$cnts))? $cnts['appearsin'] : 0;
            
            $item->chars = XbfilmsGeneral::getFilmCharsArray($item->id);
            $item->charcnt = count($item->chars);
            
            $item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
            $item->revcnt = count($item->reviews);
            if ($item->revcnt>0) {
            	$item->acq_date = $item->reviews[0]->rev_date;
            }
        	
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
            $item->tags = $tagsHelper->getItemTags('com_xbfilms.film' , $item->id);
            
        } //foreach item
        return $items;
    }

}

 
