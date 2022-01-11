<?php
/*******
 * @package xbFilms
 * @filesource admin/views/films/view.html.php
 * @version 0.9.6.f 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

class XbfilmsViewFilms extends JViewLegacy {
    
	public function display($tpl = null) {
        
		$this->items = $this->get('Items');
		
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        $this->searchTitle = $this->state->get('filter.search');
        $this->catid = $this->state->get('catid');
        
        $params = ComponentHelper::getParams('com_xbfilms');
        $this->zero_rating = $params->get('zero_rating');
        $this->zero_class = $params->get('zero_class');
        $this->star_class = $params->get('star_class');
        $this->halfstar_class = $params->get('halfstar_class');
        
        $this->xbpeople_ok = Factory::getSession()->get('xbpeople_ok');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }
		
		if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
            XbfilmsHelper::addSubmenu('films');
            $this->sidebar = JHtmlSidebar::render();
        }

        parent::display($tpl);
        
        $this->setDocument();
        
    }

	protected function addToolbar() {
	    $canDo = XbfilmsHelper::getActions();
	    
	    ToolbarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'XBCULTURE_TITLE_FILMMANAGER' ), 'screen' );

		if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('film.add');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
            ToolbarHelper::editList('film.edit');
        }
		if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('film.publish','JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('film.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolbarHelper::archiveList('film.archive');
        }
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
            ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'film.delete','JTOOLBAR_EMPTY_TRASH');
        } else if ($canDo->get('core.edit.state')) {
            ToolbarHelper::trash('film.trash');
        }

        // Add a batch button
        if ($canDo->get('core.create') && $canDo->get('core.edit')
        		&& $canDo->get('core.edit.state'))
        {
        	// we use a standard Joomla layout to get the html for the batch button
        	$bar = Toolbar::getInstance('toolbar');
        	$layout = new FileLayout('joomla.toolbar.batch');
        	$batchButtonHtml = $layout->render(array('title' => Text::_('JTOOLBAR_BATCH')));
        	$bar->appendButton('Custom', $batchButtonHtml, 'batch');
        }
        
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbfilms');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#admin-films' );
	}
    
    protected function setDocument() {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('COM_XBFILMS_ADMIN_FILMS'));
    }
    
    
}
