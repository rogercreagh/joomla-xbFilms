<?php
/*******
 * @package xbFilms
 * @filesource admin/views/film/view.html.php
 * @version 1.0.3.12 14th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbfilmsViewFilm extends JViewLegacy {
    
    protected $form = null; 
    protected $params = '';
    
    public function display($tpl = null) {

        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->canDo = XbfilmsHelper::getActions('com_xbfilms', 'film', $this->item->id);
        
        $this->params      = $this->get('State')->get('params');
        $this->zero_class = $this->params->get('zero_class','fas fa-thumbs-down');
        $this->star_class = $this->params->get('star_class','fa fa-star xbred');
        $this->halfstar_class = $this->params->get('halfstar_class');
        
        $this->taggroups = $this->params->get('enable_taggroups',0);
        if ($this->taggroups) {
            $taggroup_ids = array();
            $this->taggroup1_parent = $this->params->get('taggroup1_parent',0);
            if ($this->taggroup1_parent) $taggroup_ids[] = $this->taggroup1_parent;
            $this->taggroup2_parent = $this->params->get('taggroup2_parent',0);
            if ($this->taggroup2_parent) $taggroup_ids[] = $this->taggroup2_parent;
            $this->taggroup3_parent = $this->params->get('taggroup3_parent',0);
            if ($this->taggroup3_parent) $taggroup_ids[] = $this->taggroup3_parent;
            $this->taggroup4_parent = $this->params->get('taggroup4_parent',0);
            if ($this->taggroup4_parent) $taggroup_ids[] = $this->taggroup4_parent;
            
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('id, title, description')->from($db->quoteName('#__tags'))
                ->where('id IN ('.implode(',',$taggroup_ids).')');
            $db->setQuery($query);
            $this->taggroupinfo = $db->loadAssocList('id');    
        }
        
        if (count($errors = $this->get('Errors'))) {
        	Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
        	return false;
        }

        $this->addToolBar();

        parent::display($tpl);
        // Set the document
        $this->setDocument();
    }
    
    protected function addToolBar() 
    {
        $input = Factory::getApplication()->input;
        $input->set('hidemainmenu', true);
        $user = Factory::getUser();
        $userId = $user->get('id');
        $checkedOut     = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        
        $canDo = $this->canDo;
        
        $this->isNew = ($this->item->id == 0);
        $icon = $this->isNew ? 'filmadd' : 'filmedit';

        $title = Text::_( 'COM_XBFILMS' ).': ';
        
        if ($this->isNew) {
            $title .= Text::_('XBCULTURE_TITLE_NEWFILM');
        } elseif ($checkedOut) {
            $title .= Text::_('XBCULTURE_TITLE_VIEWFILM');
        } else {
            $title .= Text::_('XBCULTURE_TITLE_EDITFILM');
        }
        ToolBarHelper::title($title, 'video');
        
        ToolbarHelper::apply('film.apply');
        ToolbarHelper::save('film.save');
        ToolbarHelper::save2new('film.save2new');
        ToolbarHelper::save2copy('film.save2copy');
        ToolbarHelper::custom('film.save2NewPer', 'user', '', 'XBCULTURE_BTN_SAVE2PER', false) ;
        ToolbarHelper::custom('film.save2NewChar', 'user', '', 'XBCULTURE_BTN_SAVE2CHAR', false) ;
        ToolbarHelper::custom('film.save2NewRev', 'comment', '', 'XBCULTURE_BTN_SAVE2REV', false) ;
        if ($this->isNew) {
            ToolbarHelper::cancel('film.cancel','JTOOLBAR_CANCEL');
        } else {
            ToolbarHelper::cancel('film.cancel','JTOOLBAR_CLOSE');
        }
        ToolbarHelper::custom(); //spacer
        
        $bar = Toolbar::getInstance( 'toolbar' );
        if ($this->item->id > 0) {
            $dhtml = '<a href="index.php?option=com_xbfilms&view=film&layout=modalpv&tmpl=component&id='.$this->item->id.'"
            	data-toggle="modal" data-target="#ajax-pvmodal" data-backdrop="static"
            	class="btn btn-small btn-primary"><i class="icon-eye"></i> '.Text::_('XBCULTURE_PREVIEW').'</a>';
            $bar->appendButton('Custom', $dhtml);
        }
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#filmedit' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle($this->isNew ? Text::_('XBFILMS_NEW_FILM') : Text::_('XBFILMS_EDIT_FILM'));
    }
    
    
}