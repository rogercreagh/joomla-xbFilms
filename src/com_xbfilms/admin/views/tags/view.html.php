<?php
/*******
 * @package xbFilms
 * @filesource admin/views/tags/view.html.php
 * @version 0.5.4 22nd March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class XbfilmsViewTags extends JViewLegacy {
    
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
        
        XbfilmsHelper::addSubmenu('tags');
        $this->sidebar = JHtmlSidebar::render();
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
    }
    
    protected function addToolBar() {
        $canDo = XbfilmsHelper::getActions();
        
        ToolbarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'XBCULTURE_TITLE_TAGSMANAGER' ), 'tags' );
        
        if ($canDo->get('core.create') > 0) {
        	ToolbarHelper::addNew('tags.tagnew');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
        	ToolbarHelper::editList('tags.tagedit');
        }
        
        ToolbarHelper::custom(); //spacer
        if ($this->xbpeople_ok) {
        	ToolbarHelper::custom('tags.people', 'tags', '', 'xbPeople', false) ;
        }
        
        if ($canDo->get('core.admin')) {
        	ToolbarHelper::preferences('com_xbfilms');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#admin-tags' );
    }
    
    protected function setDocument()
    {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('COM_XBFILMS_ADMIN_TAGS'));
    }
    
}