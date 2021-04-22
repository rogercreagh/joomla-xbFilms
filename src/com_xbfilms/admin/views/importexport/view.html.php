<?php
/*******
 * @package xbFilms
 * @filesource admin/views/importexport/view.html.php
 * @version 0.3.2 15th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

//jimport( 'joomla.application.component.view' );
JHTML::addIncludePath(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers');

class XbfilmsViewImportexport extends JViewLegacy {
//	protected $state;
	protected $form;
	
	function display($tpl = null) {
		$params = ComponentHelper::getParams('com_xbfilms');
		$this->show_sample = $params->get('show_sample');
		
		$this->form = $this->get('Form');
		XbfilmsHelper::addSubmenu('Importexport');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
		    throw new Exception(implode("\n", $errors), 500);
		}

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	protected function addToolbar() {
	    $canDo = XbfilmsHelper::getActions();
	    
	    ToolBarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'COM_XBFILMS_DATAMANAGER' ), 'file-check importexport' );

	    $samplesexist = XbfilmsHelper::getIdFromAlias('#__categories', 'sample-films')
	    + XbfilmsHelper::getIdFromAlias('#__categories', 'sample-filmpeople');
	    if ($this->show_sample==1) {
	    	if ($samplesexist > 0) {
	    		ToolbarHelper::custom('cpanel.unsample', 'file-minus', '', 'COM_XBFILMS_REMOVE_SAMPLE', false) ;
	    	} else {
	    		ToolbarHelper::custom('cpanel.sample', 'file-plus', '', 'COM_XBFILMS_INSTALL_SAMPLE', false) ;
	    	}
	    }
	    
	    if ($canDo->get('core.admin')) {
	        ToolbarHelper::preferences('com_xbfilms');
	    }	    
	    ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#impexp' );
	}
}
