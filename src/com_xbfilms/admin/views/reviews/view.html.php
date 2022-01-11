<?php
/*******
 * @package xbFilms
 * @filesource admin/views/reviews/view.html.php
 * @version 0.9.6.f 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

class XbfilmsViewReviews extends JViewLegacy {
    
    function display($tpl = null) {
 
        // Get data from the model
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state			= $this->get('State');
        $this->filterForm    	= $this->get('FilterForm');
        $this->activeFilters 	= $this->get('ActiveFilters');
        $this->searchTitle = $this->state->get('filter.search');
        
        $params = ComponentHelper::getParams('com_xbfilms');
        $this->zero_rating = $params->get('zero_rating');
        $this->zero_class = $params->get('zero_class');
        $this->stars_class = $params->get('stars_class');
        
 //       $this->xbpeople_ok = Factory::getSession()->get('xbpeople_ok');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	return false;
        }
        
        if ($this->getLayout() !== 'modal') {
            XbfilmsHelper::addSubmenu('reviews');
            $this->sidebar = JHtmlSidebar::render();
            $this->addToolbar();
        }

        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
        $canDo = XbfilmsHelper::getActions();
                
        ToolbarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'XBCULTURE_TITLE_REVIEWSMANAGER' ), 'users' );
        
        if ($canDo->get('core.create') > 0) {
            ToolbarHelper::addNew('review.add');
        }
        if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) {
            ToolbarHelper::editList('review.edit');
        }
        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('review.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('review.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::archiveList('review.archive');
        }
        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
            ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'review.delete','JTOOLBAR_EMPTY_TRASH');
        } else if ($canDo->get('core.edit.state')) {
            ToolbarHelper::trash('review.trash');
        }
        
        // Add a batch button
        if ($canDo->get('core.create') && $canDo->get('core.edit')
        		&& $canDo->get('core.edit.state'))
        {
        	$bar = Toolbar::getInstance('toolbar');
        	// we use a standard Joomla layout to get the html for the batch button
        	$layout = new FileLayout('joomla.toolbar.batch');
        	$batchButtonHtml = $layout->render(array('title' => Text::_('JTOOLBAR_BATCH')));
        	$bar->appendButton('Custom', $batchButtonHtml, 'batch');
        }
        
        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_xbfilms');
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#admin-reviews' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle(Text::_('COM_XBFILMS_ADMIN_REVIEWS'));
    }
    
}