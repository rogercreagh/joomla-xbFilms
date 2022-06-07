<?php 
/*******
 * @package xbFilms
 * @filesource admin/views/tag/view.html.php
 * @version 0.3.2 15th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class XbfilmsViewTag extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->addToolBar();
		XbfilmsHelper::addSubmenu('tags');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
		// Set the document
		$this->setDocument();
	}
	
	protected function addToolBar() {
		$canDo = XbfilmsHelper::getActions();
		
		ToolBarHelper::title(Text::_( 'COM_XBFILMS' ).': '.Text::_( 'XBCULTURE_TITLE_TAGMANAGER' ), 'tag' );
		
		ToolbarHelper::custom('tag.tags', 'tags', '', 'XBFILMS_TAG_LIST', false) ;
		ToolbarHelper::custom('tag.tagedit', 'edit', '', 'XBCULTURE_EDIT_TAG', false) ;
		
		if ($canDo->get('core.admin')) {
			ToolBarHelper::preferences('com_xbfilms');
		}
	}
	
	protected function setDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('XBFILMS_ADMIN_TAGITEMS'));
	}
	
}
