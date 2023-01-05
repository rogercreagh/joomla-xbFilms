<?php
/*******
 * @package xbFilms
 * @filesource admin/models/films.php
 * @version 1.0.1.3 5th January 2023
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
        		'id', 'a.id', 'title', 'a.title',
        		'ordering','a.ordering', 'category_title', 'c.title',
        		'catid', 'a.catid', 'category_id', 'tagfilt', 'taglogic',
        		'first_seen', 'a.first_seen', 'last_seen', 'a.last_seen',
                'created', 'a.created',
                'published','a.state', 'rel_year','a.rel_year');
        }
        parent::__construct($config);
    }

    protected function populateState($ordering = 'last_seen', $direction = 'desc') {
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
	
        $app = Factory::getApplication();
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select('a.id AS id, a.title AS title, a.subtitle AS subtitle, a.alias AS alias, 
            a.summary AS summary, a.rel_year AS rel_year, a.catid AS catid, 
            a.poster_img AS poster_img, a.synopsis AS synopsis, a.state AS published, 
            a.created AS created, a.created_by AS created_by, a.first_seen AS first_seen, a.last_seen AS last_seen,
            a.created_by_alias AS created_by_alias, a.ext_links AS ext_links,
            a.checked_out AS checked_out, a.checked_out_time AS checked_out_time, 
            a.metadata AS metadata, a.ordering AS ordering, a.params AS params, a.note AS note');
		$query->from('#__xbfilms AS a')
        //join to persons and characters to allow filtering on them
		->join('LEFT OUTER',$db->quoteName('#__xbfilmperson', 'p') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('p.film_id'))
		->join('LEFT OUTER',$db->quoteName('#__xbfilmcharacter', 'ch') . ' ON ' .$db->quoteName('a.id') . ' = ' . $db->quoteName('ch.film_id'));
		
		$query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');
        
        $query->select('(SELECT COUNT(*) FROM #__xbfilmreviews AS fr WHERE fr.film_id=a.id) AS revcnt');
        $query->select('(SELECT AVG(fr.rating) FROM #__xbfilmreviews AS fr WHERE fr.film_id=a.id) AS averat');
        
		// Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
                $query->where('state = ' . (int) $published);
        }

        // Filter by category.
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
        if (($orderCol == 'last_seen') || ($orderCol == 'first_seen')) {
            $query->order('CASE WHEN '.$orderCol.' IS NULL THEN 1 ELSE 0 END, '.$orderCol.' '.$orderDirn);
        } else {
            if ($orderCol == 'a.ordering' || $orderCol == 'a.catid') {
                $orderCol = 'category_title '.$orderDirn.', a.ordering';  
            }
            $query->order($db->escape($orderCol.' '.$orderDirn.', title'));
        }
		$query->group('a.id');
        return $query;
    }

	public function getItems() {
        $items  = parent::getItems();
		$tagsHelper = new TagsHelper;
		
        foreach ($items as $item) {  
            $item->people = XbfilmsGeneral::getFilmPeople($item->id,'');
                        
            $roles = array_column($item->people,'role');
            $item->dircnt = count(array_keys($roles, 'director'));
            $item->prodcnt = count(array_keys($roles, 'producer'));
            $item->crewcnt = count(array_keys($roles, 'crew'));
            $item->subjcnt = count(array_keys($roles, 'appearsin'));
            $item->castcnt = count(array_keys($roles, 'actor'));
            
            $item->dirlist = $item->dircnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'director','comma',true,5);            
            $item->prodlist = $item->prodcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'producer','comma',true,5);
            $item->castlist = $item->castcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'actor','comma',true,4);
            $item->crewlist = $item->crewcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'crew','comma',true,4);
            $item->subjlist = $item->subjcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->people,'appearsin','comma',true,4);
            
            $item->chars = XbfilmsGeneral::getFilmChars($item->id);
            $item->charcnt = (empty($item->chars)) ? 0 : count($item->chars);
            $item->charlist = $item->charcnt==0 ? '' : XbcultureHelper::makeLinkedNameList($item->chars,'char','comma',true,5);
            
            $item->reviews = XbfilmsGeneral::getFilmReviews($item->id);
        	
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

 
