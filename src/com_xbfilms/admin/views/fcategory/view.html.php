<?php 
/*******
 * @package xbFilms
 * @filesource admin/views/fcategory/view.html.php
 * @version 0.9.1 9th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbfilmsViewFcategory extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->addToolBar();
		XbfilmsHelper::addSubmenu('fcategories');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
	
	protected function addToolBar() {
		$canDo = XbfilmsHelper::getActions();
		
		ToolBarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'XBCULTURE_TITLE_CATMANGER' ), 'tag' );
		
		ToolbarHelper::custom('fcategory.fcategories', 'folder', '', 'COM_XBFILMS_CAT_LIST', false) ;
		
		if ($canDo->get('core.admin')) {
			ToolBarHelper::preferences('com_xbfilms');
		}
	}
	
	protected function setDocument() {
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_XBFILMS_ADMIN_CATITEMS'));
	}
	
}
