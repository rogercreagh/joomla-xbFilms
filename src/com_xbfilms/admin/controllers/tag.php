<?php
/*******
 * @package xbFilms
 * @filesource admin/controlers/tag.php
 * @version 0.1.0 22nd November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbfilmsControllerTag extends JControllerAdmin {
    
    public function getModel($name = 'Tag', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);        
        return $model;
    }
    
    function tags() {
    	$this->setRedirect('index.php?option=com_xbfilms&view=tags');
    }

    function tagedit() {
    	$id =  Factory::getApplication()->input->get('tid');
    	$this->setRedirect('index.php?option=com_tags&task=tag.edit&id='.$id);
    }
    
}