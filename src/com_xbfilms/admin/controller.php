<?php
/*******
 * @package xbFilms
 * @filesource admin/controller.php
 * @version 0.5.4 20th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbfilmsController extends JControllerLegacy {
	protected $default_view = 'cpanel';
	
	public function display ($cachable = false, $urlparms = false){
	    //require_once JPATH_COMPONENT.'/helpers/xbfilms.php';
	    //require_once JPATH_COMPONENT.'/helpers/xbfilmsgeneral.php';
	    
		return parent::display();
	}
}

