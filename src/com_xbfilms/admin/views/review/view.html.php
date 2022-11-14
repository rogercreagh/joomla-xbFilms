<?php
/*******
 * @package xbFilms
 * @filesource admin/views/review/view.html.php
 * @version 0.9.10.1 12th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbfilmsViewReview extends JViewLegacy {
    
    protected $form = null;

    public function display($tpl = null) {
        // Get the Data
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->canDo = XbfilmsHelper::getActions('com_xbfilms', 'review', $this->item->id);
        
        $this->params      = $this->get('State')->get('params');
        $this->revtaggroup_parent = $this->params->get('revtaggroup_parent',0);
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, title, description')->from($db->quoteName('#__tags'))
        ->where('id = '.$this->revtaggroup_parent);
        $db->setQuery($query);
        $this->taggroupinfo = $db->loadAssocList('id');
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	return false;
        }
        
        // Set the toolbar
        $this->addToolBar();
        
        // Display the template
        parent::display($tpl);
        
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() {
        $input = Factory::getApplication()->input;
        
        // Hide Joomla Administrator Main menu
        $input->set('hidemainmenu', true);
        
        $isNew = ($this->item->id == 0);
        
        $title = Text::_( 'COM_XBFILMS' ).': ';
        if ($isNew) {
            $title .= Text::_('XBCULTURE_TITLE_NEWREVIEW');
        } else {
            $title .= Text::_('XBCULTURE_TITLE_EDITREVIEW');
        }
        
        ToolbarHelper::title($title, 'user');
        
        ToolbarHelper::apply('review.apply');
        ToolbarHelper::save('review.save');
        ToolbarHelper::save2new('review.save2new');
        ToolbarHelper::custom('review.saveback', 'reply', '', 'Save->Film', false) ;
        if ($isNew) {
            ToolbarHelper::cancel('review.cancel','JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('review.cancel','JTOOLBAR_CLOSE');
        }
    }
    
    protected function setDocument() {
        $isNew = ($this->item->id < 1);
        $document = Factory::getDocument();
        $document->setTitle($isNew ? Text::_('XBFILMS_REVIEW_CREATING') :
            Text::_('XBFILMS_REVIEW_EDITING'));
    }
}