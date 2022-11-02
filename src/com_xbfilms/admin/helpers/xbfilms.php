<?php
/*******
 * @package xbFilms
 * @filesource admin/helpers/xbfilms.php
 * @version 0.9.9.9 2nd November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Filter\OutputFilter;

class XbfilmsHelper extends ContentHelper
{

	public static function getActions($component = 'com_xbfilms', $section = 'component', $categoryid = 0) {
	    
	    $user 	=Factory::getUser();
	    $result = new JObject;
	    if (empty($categoryid)) {
	        $assetName = $component;
	        $level = $section;
	    } else {
	        $assetName = $component.'.category.'.(int) $categoryid;
	        $level = 'category';
	    }
	    $actions = Access::getActions('com_xbfilms', $level);
	    foreach ($actions as $action) {
	        $result->set($action->name, $user->authorise($action->name, $assetName));
	    }
	    return $result;
	}
	
	public static function addSubmenu($vName = 'dashboard') {
		if ($vName != 'categories') {
			JHtmlSidebar::addEntry(
	            Text::_('XBCULTURE_ICONMENU_DASHBOARD'),
	            'index.php?option=com_xbfilms&view=dashboard',
	            $vName == 'dashboard'
		        );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_FILMS'),
			    'index.php?option=com_xbfilms&view=films',
			    $vName == 'films'
			    );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_NEWFILM'),
			    'index.php?option=com_xbfilms&view=film&layout=edit',
			    $vName == 'film'
			    );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_REVIEWS'),
			    'index.php?option=com_xbfilms&view=reviews',
			    $vName == 'reviews'
			    );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_NEWREVIEW'),
			    'index.php?option=com_xbfilms&view=review&layout=edit',
			    $vName == 'review'
			    );
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_PEOPLE'),
				'index.php?option=com_xbfilms&view=persons',
				$vName == 'persons'
				);
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_NEWPERSON'),
				'index.php?option=com_xbfilms&view=person&layout=edit',
				$vName == 'person'
				);
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_CHARS'),
				'index.php?option=com_xbfilms&view=characters',
				$vName == 'characters'
				);
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_NEWCHAR'),
				'index.php?option=com_xbfilms&view=character&layout=edit',
				$vName == 'character'
				);
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_FILMCATS'),
				'index.php?option=com_xbfilms&view=fcategories',
				$vName == 'fcategories'
				);
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_NEWCAT'),
			    'index.php?option=com_categories&view=category&task=category.edit&extension=com_xbfilms',
			    $vName == 'category'
			    );
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_EDITCATS'),
				'index.php?option=com_categories&view=categories&extension=com_xbfilms',
				$vName == 'categories'
				);
			if (Factory::getSession()->get('xbpeople_ok')==true) {			
				JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_ICONMENU_SUBPEOPLECATS'),
					'index.php?option=com_xbpeople&view=pcategories',
					$vName == 'pcategories'
					);
			}
			JHtmlSidebar::addEntry(
	            Text::_('XBCULTURE_ICONMENU_TAGS'),
	            'index.php?option=com_xbfilms&view=tags',
	            $vName == 'tags'
	        	);
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_NEWTAG'),
				'index.php?option=com_tags&view=tag&layout=edit',
				$vName == 'tag'
				);
			JHtmlSidebar::addEntry(
	            Text::_('XBCULTURE_ICONMENU_IMPORTEXPORT'),
	            'index.php?option=com_xbfilms&view=importexport',
	            $vName == 'importexport'
	        );
		} else {
			JHtmlSidebar::addEntry(
					Text::_('XBFILMS_XBFILMS_DASHBOARD'),
					'index.php?option=com_xbfilms&view=dashboard',
					$vName == 'dashboard'
					);
			
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_FILMS'),
					'index.php?option=com_xbfilms&view=films',
					$vName == 'films'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBCULTURE_REVIEWS_U'),
					'index.php?option=com_xbfilms&view=reviews',
					$vName == 'reviews'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBFILMS_FILM_CAT_CNTS'),
					'index.php?option=com_xbfilms&view=fcategories',
					$vName == 'fcategories'
					);
		}
	}
    
    public static function getFilmTitleById($id) {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);      
        $query->select('title')
            ->from('#__xbfilms')
            ->where('id = '. (int) $id);
        $db->setQuery($query);
        return $db->loadResult();       
    }
    
    public static function getIdFromAlias($table,$alias, $ext = 'com_xbfilms') {
        $alias = trim($alias,"' ");
        $table = trim($table,"' ");
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select('id')->from($db->quoteName($table))->where($db->quoteName('alias')." = ".$db->quote($alias));
        if ($table === '#__categories') {
        	$query->where($db->quoteName('extension')." = ".$db->quote($ext));
        }
        $db->setQuery($query);
        $res =0;
        $res = $db->loadResult();
        return $res;
    }
    
    public function getColCounts($srcarr,$col) {
    	return array_count_values(array_column($srcarr, $col));
    }
    
    /**
     * @name getItemCnt
     * @desc returns the number of items in a table
     * @param string $table
     * @return integer
     */
    public static function getItemCnt($table) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')->from($db->quoteName($table));
        $db->setQuery($query);
        $cnt=-1;
        try {
            $cnt = $db->loadResult();
        } catch (Exception $e) {
            $dberr = $e->getMessage();
            Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');
        }
        return $cnt;
    }
        
    /**
     * @name createCategory()
     * @desc creates a new category if it doesn't exist, returns id of category
     * NB passing a name and no alias will check for alias based on name.
     * @param (string) $name for category
     * @param string $alias - usually lowercase name with hyphens for spaces, must be unique, will be created from name if not supplied
     * @param string $ext - the extension owning the category
     * @param string $desc - optional description
     * @param number $parentid - id of parent category (defaults to root
     * @return integer - id of new or existing category, or false if error. Error message is enqueued 
     */
    public static function createCategory($name, $alias='', $ext='com_xbfilms', $desc='', $parentid = 0) {
    	if ($alias=='') {
    		//create alias from name
    		$alias = OutputFilter::stringURLSafe(strtolower($name));
    	}
    	//check category doesn't already exist
    	$db = Factory::getDbo();
    	$query = $db->getQuery(true);
    	$query->select('id')->from($db->quoteName('#__categories'))->where($db->quoteName('alias')." = ".$db->quote($alias));
    	$query->where($db->quoteName('extension')." = ".$db->quote($ext));
    	$db->setQuery($query);
    	$id =0;
    	$res = $db->loadResult();
    	if ($res>0) {
    		return $res;
    	}
    	//get category model
    	$basePath = JPATH_ADMINISTRATOR.'/components/com_categories';
    	require_once $basePath.'/models/category.php';
    	$config  = array('table_path' => $basePath.'/tables');
    	//setup data for new category
    	$category_model = new CategoriesModelCategory($config);
    	$category_data['id'] = 0;
    	$category_data['parent_id'] = $parentid;
    	$category_data['published'] = 1;
    	$category_data['language'] = '*';
    	$category_data['params'] = array('category_layout' => '','image' => '');
    	$category_data['metadata'] = array('author' => '','robots' => '');
    	$category_data['extension'] = $ext;
    	$category_data['title'] = $name;
    	$category_data['alias'] = $alias;
    	$category_data['description'] = $desc;
    	if(!$category_model->save($category_data)){
    		Factory::getApplication()->enqueueMessage('Error creating category: '.$category_model->getError(), 'error');
    		return false;
    	}
    	$id = $category_model->getItem()->id;
    	return $id;
    }

    public static function checkPersonExists($firstname, $lastname) {
    	$db = Factory::getDbo();
    	$query = $db->getQuery(true);
    	$query->select('id')->from('#__xbpersons')
    	->where('LOWER('.$db->quoteName('firstname').')='.$db->quote(strtolower($firstname)).' AND LOWER('.$db->quoteName('lastname').')='.$db->quote(strtolower($lastname)));
    	$db->setQuery($query);
    	$res = $db->loadResult();
    	if ($res > 0) {
    		return true;
    	}
    	return false;
    }
    
    public static function checkTitleExists($title, $table) {
    	$col = ($table == '#__xbcharacters') ? 'name' : 'title';
    	$db = Factory::getDbo();
    	$query = $db->getQuery(true);
    	$query->select('id')->from($db->quoteName($table))
    	->where('LOWER('.$db->quoteName($col).')='.$db->quote(strtolower($title)));
    	$db->setQuery($query);
    	$res = $db->loadResult();
    	if ($res > 0) {
    		return true;
    	}
    	return false;
    }
    
}
    
