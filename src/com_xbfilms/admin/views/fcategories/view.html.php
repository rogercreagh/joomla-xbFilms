<?php
/*******
 * @package xbFilms
 * @filesource admin/views/fcategories/view.html.php
 * @version 0.9.4 17th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class XbfilmsViewFcategories extends JViewLegacy {
    
    function display($tpl = null) {
        // Get data from the model
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        $this->searchTitle = $this->state->get('filter.search');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	
            return false;
        }
        
        $this->xbpeople_ok = Factory::getSession()->get('xbpeople_ok');
        
        XbfilmsHelper::addSubmenu('fcategories');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
        $canDo = XbfilmsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'XBCULTURE_TITLE_CATSMANAGER' ), 'folder' );
        
        //index.php?option=com_categories&view=category&layout=edit&extension=com_xbfilms
        if ($canDo->get('core.create') > 0) {
        	ToolbarHelper::custom('fcategories.categorynew','new','','COM_XBFILMS_NEW_FCAT',false);
        	if (Factory::getSession()->get('xbpeople_ok')!=0) {
        		ToolbarHelper::custom('fcategories.categorynewpeep','new','','COM_XBFILMS_NEW_PCAT',false);
        	}
        }
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::editList('fcategories.categoryedit', 'COM_XBFILMS_EDIT_CAT');       	
         }
         
         ToolbarHelper::custom(); //spacer
         if ($this->xbpeople_ok) {
         	ToolbarHelper::custom('fcategories.people', 'folder', '', 'xbPeople', false) ;
         }
         
         if ($canDo->get('core.admin')) {
        	ToolbarHelper::preferences('com_xbfilms');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#admin-cats' );
    }

    protected function setDocument() {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('COM_XBFILMS_ADMIN_CATS'));
    }
}
