<?php
/*******
 * @package xbFilms
 * @filesource admin/helpers/xbfilms.php
 * @version 1.0.3.1 3rd Februaryy 2023
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
				'index.php?option=com_xbpeople&view=person&layout=edit',
				$vName == 'person'
				);
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_GROUPS'),
			    'index.php?option=com_xbfilms&view=groups',
			    $vName == 'groups'
			    );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_NEWGROUP'),
			    'index.php?option=com_xbpeople&view=group&layout=edit',
			    $vName == 'group'
			    );
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_CHARS'),
				'index.php?option=com_xbfilms&view=characters',
				$vName == 'characters'
				);
			JHtmlSidebar::addEntry(
				Text::_('XBCULTURE_ICONMENU_NEWCHAR'),
				'index.php?option=com_xbpeople&view=character&layout=edit',
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
	            Text::_('XBCULTURE_ICONMENU_DATAMAN'),
	            'index.php?option=com_xbfilms&view=importexport',
	            $vName == 'importexport'
	        );
			JHtmlSidebar::addEntry(
			    Text::_('XBCULTURE_ICONMENU_OPTIONS'),
			    'index.php?option=com_config&view=component&component=com_xbfilms',
			    $vName == 'options'
			    );
			
		} else {
			JHtmlSidebar::addEntry(
					Text::_('XBFILMS_XBFILMS_DASHBOARD'),
					'index.php?option=com_xbfilms&view=dashboard',
					$vName == 'dashboard'
					);
			
			JHtmlSidebar::addEntry(
					Text::_('XBFILMS_XBFILMS'),
					'index.php?option=com_xbfilms&view=films',
					$vName == 'films'
					);
			JHtmlSidebar::addEntry(
					Text::_('XBFILMS_XBFILM_REVIEWS'),
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
    
 }
    
