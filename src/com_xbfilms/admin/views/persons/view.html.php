<?php
/*******
 * @package xbFilms
 * @filesource admin/views/persons/view.html.php
 * @version 1.0.3.2 4th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

class XbfilmsViewPersons extends JViewLegacy {

    function display($tpl = null) {
        
        $this->items		= $this->get('Items');

        $this->pagination	= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');

        $this->searchTitle = $this->state->get('filter.search');
        $this->catid 		= $this->state->get('catid');
        if ($this->catid>0) {
            $this->cat 		= XbcultureHelper::getCat($this->catid);
        }
               
        $this->xbpeople_ok = Factory::getSession()->get('xbpeople_ok');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	
            return false;
        }
        
        if ($this->getLayout() !== 'modal') {
        // Set the toolbar & sidebar
            XbfilmsHelper::addSubmenu('persons');
            $this->addToolbar();
            $this->sidebar = JHtmlSidebar::render();
        }
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
        $canDo = XbfilmsHelper::getActions();
        
        	$bar = Toolbar::getInstance('toolbar');        	

        	ToolbarHelper::title(Text::_('XBFILMS_ADMIN_PEOPLE'), 'users' );
        
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('person.add');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
            ToolbarHelper::editList('person.edit');
        }
        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('person.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('person.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        
        ToolbarHelper::custom(); //spacer
        if ($this->xbpeople_ok) {
        	ToolbarHelper::custom('persons.people', 'users', '', 'All People', false) ;
        }
        
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbfilms');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#admin-people' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle(JText::_('XBFILMS_ADMIN_PEOPLE'));
    }
    
}