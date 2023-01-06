<?php
/*******
 * @package xbFilms
 * @filesource admin/controlers/persons.php
 * @version 1.0.1.4 6th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbfilmsControllerPersons extends JControllerAdmin {
    
    public function getModel($name = 'Persons', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }

    function people() {
    	$this->setRedirect('index.php?option=com_xbpeople&view=persons');
    }
    
}