<?php
/*******
 * @package xbFilms
 * @filesource admin/views/characters/view.html.php
 * @version 0.5.4 22nd March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbfilmsViewCharacters extends JViewLegacy {

    function display($tpl = null) {

        $this->items		= $this->get('Items');
        
        $this->pagination	= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');

        $this->searchTitle = $this->state->get('filter.search');
        $this->catid 		= $this->state->get('catid');
        
        $this->xbpeople_ok = Factory::getSession()->get('xbpeople_ok');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
        
        if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
            XbfilmsHelper::addSubmenu('characters');
            $this->sidebar = JHtmlSidebar::render();
        }
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
        $canDo = XbfilmsHelper::getActions();
                
        ToolbarHelper::title(Text::_('COM_XBFILMS').': '.Text::_('XBCULTURE_TITLE_CHARMANAGER'), 'users' );
        
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('character.add');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
            ToolbarHelper::editList('character.edit');
        }
        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('character.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('character.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::archiveList('character.archive');
        }
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
           ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'character.delete','JTOOLBAR_EMPTY_TRASH');
        } else if ($canDo->get('core.edit.state')) {
           ToolbarHelper::trash('character.trash');
        }
        
        // Add a batch button
        $bar = Toolbar::getInstance('toolbar');
        if ($canDo->get('core.create') && $canDo->get('core.edit')
        		&& $canDo->get('core.edit.state'))
        {
        	// we use a standard Joomla layout to get the html for the batch button
        	$layout = new JLayoutFile('joomla.toolbar.batch');
        	$batchButtonHtml = $layout->render(array('title' => Text::_('JTOOLBAR_BATCH')));
        	$bar->appendButton('Custom', $batchButtonHtml, 'batch');
        }
        
        ToolbarHelper::custom(); //spacer
        if ($this->xbpeople_ok) {
        	ToolbarHelper::custom('characters.people', 'users', '', 'xbPeople', false) ;
        }
        
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbfilms');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#admin-chars' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('COM_XBFILMS_ADMIN_CHARS'));
    }
    
}