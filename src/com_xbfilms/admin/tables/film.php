<?php
/*******
 * @package xbFilms
 * @filesource admin/tables/film.php
 * @version 0.9.7 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

//use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Registry\Registry;
use Joomla\CMS\Table\Table;

class XbfilmsTableFilm extends Table
{
	public function __construct(&$db) {
		$this->setColumnAlias('published', 'state');
        parent::__construct('#__xbfilms', 'id', $db);
        JTableObserverTags::createObserver($this, array('typeAlias' => 'com_xbfilms.film'));
	}

	public function check() {
	    $params = ComponentHelper::getParams('com_xbfilms');
	    
	    $title = trim($this->title);
	    //check title 
	    if ($title == '') {
	        $this->setError(JText::_('XBCULTURE_PROVIDE_VALID_TITLE'));
	        return false;
	    }
	    
	    if (($this->id == 0) && (XbfilmsHelper::checkTitleExists($title,'#__xbfilms'))) {
	    	$this->setError(JText::_('Film "'.$title.'" already exists; if this is a different film with the same title please append something to the title to distinguish them'));
	        return false;
	    }
	    
	    $this->title = $title;
	    
	    if (trim($this->alias) == '') {
	        $this->alias = $title;
	    }
	    $this->alias = OutputFilter::stringURLSafe($this->alias);
	    
	    //set category
	    if (!$this->catid>0) {
	        $defcat=0;
	        if ($params->get('def_new_filmcat')>0) {
	            $defcat=$params->get('def_new_filmcat');
	        } else {
	            $defcat = XbfilmsHelper::getIdFromAlias('#__categories', 'uncategorised');
	        }
	        if ($defcat>0) {
	            $this->catid = $defcat;
	            Factory::getApplication()->enqueueMessage(JText::_('XBCULTURE_CATEGORY_DEFAULT_SET').' ('.XbfilmsHelper::getCat($this->catid)->title.')');
	        } else {
	        	// this shouldn't happen unless uncategorised has been deleted
	            $this->setError(JText::_('XBCULTURE_CATEGORY_MISSING'));
	            return false;
	        }
	    }
	    
	    //warn if summary missing
	    if ((trim($this->summary)=='')) {
	        if (trim($this->synopsis)=='' ) {
	            Factory::getApplication()->enqueueMessage(JText::_('XBCULTURE_MISSING_SUMMARY'));
	        }
	    }
	    
	    //json encode ext_links if set
	    if (is_array($this->ext_links)) {
	        $this->ext_links = json_encode($this->ext_links);
	    }
	    
	    //set metadata to defaults
	    $metadata = json_decode($this->metadata,true);
	    // meta.author will be created_by_alias (see above)
	    if ($metadata['author'] == '') {
	        if ($this->created_by_alias =='') {
	            $metadata['author'] = $params->get('def_author');
	        } else {
	            $metadata['author'] = $this->created_by_alias;
	        }
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
            // Convert the params field to a string.
            $parameter = new Registry;
            $parameter->loadArray($array['params']);
            $array['params'] = (string)$parameter;
        }
 	    
        // 		if (isset($array['rules']) && is_array($array['rules'])) {
        //             $rules = new JAccessRules($array['rules']);
        //             $this->setRules($rules);
        //         }
        
        if (isset($array['metadata']) && is_array($array['metadata'])) {
            $registry = new Registry;
            $registry->loadArray($array['metadata']);
            $array['metadata'] = (string)$registry;
        }
        return parent::bind($array, $ignore);
        
    }

	protected function _getAssetName() {
                $k = $this->_tbl_key;
                return 'com_xbfilms.film.'.(int) $this->$k;
        }

	protected function _getAssetTitle() {
                return $this->title;
        }

	protected function _getAssetParentId(Table $table = null, $id = null)
        {
            // We will retrieve the parent-asset from the Asset-table
            $assetParent = Table::getInstance('Asset');
            // Default: if no asset-parent can be found we take the global asset
            $assetParentId = $assetParent->getRootId();
            
            // Find the parent-asset
            if (($this->catid)&& !empty($this->catid))
            {
                // The item has a category as asset-parent
                $assetParent->loadByName('com_xbfilms.category.' . (int) $this->catid);
            }
            else
            {
                // The item has the component as asset-parent
                $assetParent->loadByName('com_xbfilms');
            }
            
            // Return the found asset-parent-id
            if ($assetParent->id)
            {
                $assetParentId=$assetParent->id;
            }
            return $assetParentId;
	}

	public function delete($pk=null) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete()->from('#__xbfilmcharacter')->where('film_id = '. $pk);
		$this->_db->setQuery($query);
		$this->_db->execute();
		$query = $db->getQuery(true);
		$query->delete()->from('#__xbfilmperson')->where('film_id = '. $pk);
		$this->_db->setQuery($query);
		$this->_db->execute();
		return parent::delete($pk);
    }

}
