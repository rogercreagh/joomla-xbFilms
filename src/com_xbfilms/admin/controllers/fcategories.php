<?php
/*******
 * @package xbFilms
 * @filesource admin/controlers/fcategories.php
 * @version 0.9.4 14th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbfilmsControllerFcategories extends JControllerAdmin {
 
	protected $edcatlink = 'index.php?option=com_categories&task=category.edit&extension=';
	
    public function getModel($name = 'Categories', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    function categoryedit() {
    	$ids =  Factory::getApplication()->input->get('cid');
    	$id=$ids[0];
    	//check if this is a film or people category
    	$db = Factory::getDBO();
    	$db->setQuery('SELECT extension FROM #__categories WHERE id = '.$db->quote($id));
    	$ext = $db->loadResult();
    	if ($ext == 'com_xbfilms') {
    		$this->setRedirect($this->edcatlink.'com_xbfilms&id='.$id);    		
    	} elseif ($ext = 'com_xbpeople') {
    		//if (Factory::getSession()->get('xbpeople_ok')==1){
    		if (XbcultureHelper::checkComponent('com_xbpeople')==1){
    			$this->setRedirect($this->edcatlink.'com_xbpeople&id='.$id);
	    	} else {
	    		Factory::getApplication()->enqueueMessage('COM_XBFILMS_NO_COMPEOPLE','error');
	    	}
    	}
    }
    
    function categorynew() {
    	$this->setRedirect($this->edcatlink.'com_xbfilms&id=0');
    }
    
    function categorynewpeep() {
    	if (XbcultureHelper::checkComponent('com_xbpeople')==1){
    		$this->setRedirect($this->edcatlink.'com_xbpeople&id=0');
    	} else {
    		Factory::getApplication()->enqueueMessage('COM_XBFILMS_NO_COMPEOPLE','error');
    	}
    }

    function people() {
    	$this->setRedirect('index.php?option=com_xbpeople&view=pcategories');
    }
    
}