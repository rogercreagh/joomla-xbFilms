<?php 
/*******
 * @package xbFilms
 * @filesource site/controller.php
 * @version 0.9.0 7th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;


class XbfilmsController extends BaseController
{
	public function display ($cachable = false, $urlparms = false){
//	    require_once JPATH_COMPONENT.'/helpers/xbfilms.php';
	    require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/xbfilmsgeneral.php';
	    require_once JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php';
	    
		return parent::display();
	}
	
}
