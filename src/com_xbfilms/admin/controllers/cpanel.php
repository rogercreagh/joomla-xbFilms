<?php
/*******
 * @package xbFilms
 * @filesource admin/controllers/cpanel.php
 * @version 0.9.0 7th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class XbfilmsControllerCpanel extends JControllerAdmin {

    public function getModel($name = 'Cpanel', $prefix = 'XbfilmsModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config );
        return $model;
    }
    
    function books() {
    	$xbbooks_ok = XbcultureHelper::checkComponent('com_xbbooks');
    	//Factory::getSession()->get('xbbooks_ok',false);
        if ($xbbooks_ok == true) {
            $this->setRedirect('index.php?option=com_xbbooks&view=cpanel');
        } elseif ($xbbooks_ok === 0) {
            Factory::getApplication()->enqueueMessage('xbBooks '.JText::_('COM_XBFILMS_COMP_DISABLED'), 'warning');
            $this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbbooks');
        } else {
            Factory::getApplication()->enqueueMessage('xbBooks '.JText::_('COM_XBFILMS_COMP_MISSING'), 'info');
            $this->setRedirect('index.php?option=com_xbfilms&view=cpanel');
        }
    }
    
    function gigs() {
        $xbgigs_ok = Factory::getSession()->get('xbgigs_ok',false);
        if ($xbgigs_ok == true) {
            $this->setRedirect('index.php?option=com_xbgigs');
        } elseif ($xbgigs_ok === 0) {
            Factory::getApplication()->enqueueMessage('xbGigs '.JText::_('COM_XBFILMS_COMP_DISABLED'), 'warning');
            $this->setRedirect('index.php?option=com_installer&view=manage&filter[search]=xbgigs');
        } else {
            Factory::getApplication()->enqueueMessage('xbGigs '.JText::_('COM_XBFILMS_COMP_MISSING'), 'info');
            $this->setRedirect('index.php?option=com_xbfilms&view=cpanel');
        }
    }
    
    function people() {
    	$this->setRedirect('index.php?option=com_xbpeople&view=cpanel');
    }
        
    function sample() {

        $filename = 'xbfilms-sample-data.sql';
        $src = JPATH_ROOT.'/media/com_xbfilms/samples/'.$filename;
        $dest = JPATH_COMPONENT_ADMINISTRATOR ."/uploads/". $filename;
        JFile::copy($src, $dest);  
        $dummypost = array('setpub'=>1, 
        	'impcat'=>XbfilmsHelper::createCategory('sample-films','','com_xbfilms','Sample film data - anything in this category will be deleted when Sample Data is removed'),
            'imppcat'=>XbfilmsHelper::createCategory('sample-filmpeople','','com_xbpeople','Sample film people data - anything in this category will be deleted when Sample Data is removed'),
        	'poster_path'=>'/images/xbfilms/samples/films/',
        	'portrait_path'=>'/images/xbfilms/samples/people/', 
        	'reviewer'=>'');
        $impmodel = $this->getmodel('importexport');
        //TODO move this to model as new function
        $wynik = $impmodel->mergeSql($filename,$dummypost);
        if ($wynik['errs'] == '') {
        	if ($wynik['donecnt'] > 0 ) {
        		$mess='Sample data installed. ';
        		if ($wynik['#__xbfilms']>0) { $mess .= $wynik['#__xbfilms'].' films, ';}
        		if ($wynik['#__xbfilmreviews']>0) { $mess .= $wynik['#__xbfilmreviews'].' reviews assigned to samples-books category.<br />';}
        		if ($wynik['#__xbpersons']>0) { $mess .= $wynik['#__xbpersons'].' people assigned to samples-filmpeople category.';}
        		if ($wynik['#__xbfilmperson']>0) { $mess .= $wynik['#__xbfilmperson'].' people-film links created, ';}
        		$msgtype = 'success';
        	} else {
        		$mess = 'Nothing to import, possibly items already exist in other categories. ';
        		$msgtype = 'info';
        	}
        	$mess .= $wynik['mess'];
        	//copy sample images folder to images
        	$src = '/media/com_xbfilms/samples/images/';
        	$dest = '/images/xbfilms/samples';
        	if (JFolder::exists(JPATH_ROOT.$dest))
        	{
        		$mess .= '<br />'.JText::sprintf('Sample images not copied, folder %s already exists', $dest) ;
        		$msgtype = 'info';
        	} else {
        		if (JFolder::copy(JPATH_ROOT.$src,JPATH_ROOT.$dest)){
        			$mess .= '<br /> Sample images copied to '.$dest;
        		} else {
        			$mess .= '<br />Warning, problem copying sample images to'.$dest;
        			$msgtype = 'warning';
        		}
        	}
        } else {
        	$mess = $wynik['errs'];
        	$msgtype = 'error';
        }
        Factory::getApplication()->enqueueMessage($mess,$msgtype);
        $this->setRedirect('index.php?option=com_xbfilms&view=cpanel');
    }
    
    function unsample() {
    	$impmodel = $this->getmodel('importexport');
    	$wynik = $impmodel->uninstallSample();
    	$this->setRedirect('index.php?option=com_xbfilms&view=cpanel');
    }
    
}
