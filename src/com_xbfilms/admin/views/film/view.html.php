<?php
/*******
 * @package xbFilms
 * @filesource admin/views/film/view.html.php
 * @version 0.9.3 12th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
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
            $tagsHelper = new TagsHelper;
            
            $this->taggroup1_parent = $this->params->get('taggroups1_parent',0);
            $taggroup_ids = array();
            $taggroup_ids[1] = $this->taggroup1_parent;
            //($this->taggroup1_parent) ? $tagsHelper->getTagNames($this->taggroup1_parent) : '';
            $this->taggroup2_parent = $this->params->get('taggroups2_parent',0);
            $taggroup_ids[2] = $this->taggroup2_parent;
            $this->taggroup3_parent = $this->params->get('taggroups3_parent',0);
            $taggroup_ids[3] = $this->taggroup3_parent;
            $this->taggroup4_parent = $this->params->get('taggroups4_parent',0);
            $taggroup_ids[4] = $this->taggroup4_parent;
            $this->taggroup_names = $tagsHelper->getTagNames($taggroup_ids);           
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
        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#filmedit' );
    }
    
    protected function setDocument()
    {
        $document = Factory::getDocument();
        $document->setTitle($this->isNew ? Text::_('XBFILMS_NEW_FILM') : Text::_('XBFILMS_EDIT_FILM'));
    }
    
    
}