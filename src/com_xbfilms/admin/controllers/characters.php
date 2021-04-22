<?php
/*******
 * @package xbFilms
 * @filesource admin/controlers/characters.php
 * @version 0.5.5 22nd March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

class XbfilmsControllerCharacters extends JControllerAdmin {
    
    public function getModel($name = 'Character', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function people() {
    	$this->setRedirect('index.php?option=com_xbpeople&view=characters');
    }
    
}