<?php
/*******
 * @package xbFilms
 * @filesource admin/views/films/view.html.php
 * @version 1.0.1.3 5th January 2023
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
        
        $show_cats = $params->get('show_cats','1','int');
        $this->showcat = ($show_cats) ? $params->get('show_bcat','1','int') : 0;
        
        $show_tags = $params->get('show_tags','1','int');
        $this->showtags = ($show_tags) ? $params->get('show_btags','1','int') : 0;
        
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
	    
	    ToolbarHelper::title(Text::_( 'XBFILMS_ADMIN_FILMS' ), 'screen' );

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
        $document->setTitle(Text::_('XBFILMS_ADMIN_FILMS'));
    }
    
    
}
