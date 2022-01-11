<?php 
/*******
 * @package xbFilms
 * @filesource site/xbfilms.php
 * @version 0.9.6.f 10th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_xbpeople/css/xbculture.css', array('version'=>'auto'));
$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
$document->addStyleSheet($cssFile);

Factory::getLanguage()->load('com_xbculture', JPATH_ADMINISTRATOR);

// Require helper files
JLoader::register('XbfilmsHelper', JPATH_COMPONENT . '/helpers/xbfilms.php');
JLoader::register('XbfilmsGeneral', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/xbfilmsgeneral.php');
JLoader::register('XbcultureHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php');

Factory::getSession()->set('xbfilms_ok',true);
//detect related components and set session flag
XbcultureHelper::checkComponent('com_xbpeople');
XbcultureHelper::checkComponent('com_xbbooks');
	
// Get an instance of the controller
$controller = JControllerLegacy::getInstance('Xbfilms');

// Perform the Request task
$input = Factory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
