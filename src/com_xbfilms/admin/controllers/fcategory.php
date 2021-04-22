<?php
/*******
 * @package xbFilms
 * @filesource admin/controlers/fcategory.php 
 * @version 0.9.1 9th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbfilmsControllerFcategory extends JControllerAdmin {
    
    public function getModel($name = 'Category', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function fcategories() {
    	$this->setRedirect('index.php?option=com_xbfilms&view=fcategories');
    }
    
}