<?php
/*******
 * @package xbFilms
 * @filesource admin/tables/review.php
 * @version 0.9.11.0 15th November 2022November 
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

//use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Table\Observer\Tags;
use Joomla\Registry\Registry;
use Joomla\CMS\Table\Table;

class XbfilmsTableReview extends Table
{
    function __construct(&$db) {
        $this->setColumnAlias('published', 'state');
        parent::__construct('#__xbfilmreviews', 'id', $db);
        $this->_supportNullValue = true;  //write empty checkedouttime as null
        Tags::createObserver($this, array('typeAlias' => 'com_xbfilms.review'));
    }
    
    public function check() {
    	$params = ComponentHelper::getParams('com_xbfilms');

    	//get count of existing reviews +1 for this review to use in default alias	
    	$db = $this->getDbo();
    	$query= $db->getQuery(true);
    	$query->select('COUNT(r.id) as revcnt')->from('#__xbfilmreviews AS r')
    	->where('r.film_id = '.$this->film_id);
    	$db->setQuery($query);
    	$revno = $db->loadResult()+1;
    	//get film title for default revie/rating title
    	$ftitle = '"'.XbfilmsHelper::getFilmTitleById($this->film_id).'"';
    	
    	$title = trim($this->title);
	   	//check title and create default if none supplied
    	if (($title == '') && (trim($this->summary)=='') && (trim($this->synopsis==''))) {
    		//do quick rating
    		$title = 'Rating '.$ftitle;
    		if (trim($this->alias) == '') {
    			$this->alias = 'rating-'.$revno.'-'.$ftitle;
    		}
    	} else { 
	   		if ($title == '') {
	    		$title = 'Review of '.$ftitle;
	    		Factory::getApplication()->enqueueMessage('No review title supplied; default created - please check and change as necessary','Warning');
	    	}
	    	if (($this->id == 0) && (XbcultureHelper::checkTitleExists($title,'#__xbfilmreviews'))) {
	    		$this->setError(JText::_('Review "'.$title.'" already exists; if this is a different review with the same title please append something to the title to distinguish them'));
	    	    return false;
	    	}
	    	if (trim($this->alias) == '') {
	    	    $this->alias = 'review-'.$revno.'-'.$title;
	    	}
	    		
    	}
    	    	
    	$this->title = $title;
    	
    	$this->alias = OutputFilter::stringURLSafe($this->alias);
    	
    	//set reviewer if not set (default to current user)
    	if (trim($this->reviewer) == '') {
    		$user = Factory::getUser($this->item->created_by);
    		$this->reviewer = '';
    		switch ($params->get('rev_auth')) {
    		    case 1:
    		        $this->reviewer = $user->name;
    		        break;
    		    case 2:
    		        $this->reviewer = $user->username;
    		        break;
    		}
    	}
    	//set date reviewed
    	if ($this->rev_date == '') {
    		$this->rev_date = Factory::getDate()->toSql();
    	}
    	
    	//set category
        if (!$this->catid>0) {
        	$defcat=0;
        	if ($params->get('def_new_revcat')>0) {
        		$defcat = $params->get('def_new_revcat');
        	} else {
        	    $defcat = XbcultureHelper::getIdFromAlias('#__categories', 'uncategorised','com_xbfilms');
        	}
        	if ($defcat>0) {
        		$this->catid = $defcat;
        		Factory::getApplication()->enqueueMessage(JText::_('XBCULTURE_CATEGORY_DEFAULT_SET').' ('.XbcultureHelper::getCat($this->catid)->title.')');
        	} else {
        		// this shouldn't happen unless uncategorised has been deleted
        		$this->setError(JText::_('XBCULTURE_CATEGORY_MISSING'));
        		return false;
        	}
        }
        
        //check summary
        if ((trim($this->summary)=='')) {
        	if (trim($this->review)=='' ) {
        		Factory::getApplication()->enqueueMessage(JText::_('XBCULTURE_MISSING_SUMMARY'));
        	}
        }
        
        //set metadata to defaults
        $metadata = json_decode($this->metadata,true);
        //meta.author will be set to reviewer if blank. Will only be created on page display (view.html.php)
        if ($metadata['author'] == '') {
        	$metadata['author'] = $this->reviewer;
        }
        //meta.description can be set to first 150 chars of summary if not otherwise set and option is set
        $summary_metadesc = $params->get('summary_metadesc');
        if (($summary_metadesc) && (trim($metadata['metadesc']) == '')) {
        	$metadata['metadesc'] = HTMLHelper::_('string.truncate', $this->summary,150,true,false);
        }
        //meta.rights will be set to default if not otherwise set
        $def_rights = $params->get('def_rights');
        if (($def_rights != '') && (trim($metadata['rights']) == '')) {
        	$metadata['rights'] = $def_rights;
        }
        //meta.keywords will be set to a list of tags unless otherwise set if the option is set
        //TODO update this when tags are added
        // convert existing keyword list to array, get tag names as array, merge arrays and implode to a list
        $tags_keywords = $params->get('tags_keywords');
        if (($tags_keywords) && (trim($metadata['metakey']) == '')) {
        	$tagsHelper = new TagsHelper;
        	$tags = implode(',',$tagsHelper->getTagNames(explode(',',$tagsHelper->getTagIds($this->id,'com_xbfilms.film'))));
        	$metadata['metakey'] = $tags;
        }
        $this->metadata = json_encode($metadata);
        
        return true;
    }
    
    public function bind($array, $ignore = '') {
    	if (isset($array['params']) && is_array($array['params'])) {
    		$registry = new Registry($array['params']);
    		$array['params'] = (string) $registry;
    	}
    	
    	if (isset($array['metadata']) && is_array($array['metadata'])) {
    		$registry = new Registry($array['metadata']);
    		$array['metadata'] = (string) $registry;
    	}
    	return parent::bind($array, $ignore);
    	
//     	if (isset($array['rules']) && is_array($array['rules'])) {
//    	$rules = new Rules($array['rules']);
//    	$this->setRules($rules);
//     	}
    	
    }
    
}
