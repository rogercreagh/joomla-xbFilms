<?php
/*******
 * @package xbFilms
 * @filesource admin/xbfilms.php
 * @version 0.9.8.7 5th June 2022
 * @since 0.1.0 22nd November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html	
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Controller\BaseController;

if (!Factory::getUser()->authorise('core.manage', 'com_xbfilms')) {
    Factory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'),'warning');
    return false;
}

$document = Factory::getDocument();
//add the component, xbculture and fontawesome css
$params = ComponentHelper::getParams('com_xbfilm');
$usexbcss = $params->get('use_xbcss',1);
if ($usexbcss<2) {
    $cssFile = Uri::root(true)."/media/com_xbpeople/css/xbculture.css";
    $altcss = $params->get('css_file','');
    if ($usexbcss==0) {
        if ($altcss && file_exists(JPATH_ROOT.$altcss)) {
            $cssFile = $altcss;
        }
    }
    $document->addStyleSheet($cssFile);
}
$exticon = $params->get('ext_icon',0);
if ($exticon) {
    $style = 'a[target="_blank"]:after {font-style: normal; font-weight:bold; content: "\2197";}' ;
    $document->addStyleDeclaration($style);
}
$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
$document->addStyleSheet($cssFile);

Factory::getLanguage()->load('com_xbculture');

// Require helper files
JLoader::register('XbfilmsHelper', JPATH_ADMINISTRATOR . '/components/com_xbfilms/helpers/xbfilms.php');
JLoader::register('XbfilmsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbfilms/helpers/xbfilmsgeneral.php');
JLoader::register('XbcultureHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php');

Factory::getSession()->set('xbfilms_ok',true);

//detect related components and set session flag
if (!Factory::getSession()->get('xbpeople_ok',false)) {
    if (file_exists(JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php')) {
        XbcultureHelper::checkComponent('com_xbpeople');
    } else {
        $app = Factory::getApplication();
        if ($app->input->get('view')!='dashboard') {
            $app->redirect('index.php?option=com_xbfilms&view=dashboard');
            $app->close();
        }
    }
}

// Get an instance of the controller prefixed
$controller = BaseController::getInstance('xbfilms');
// Perform the Request task and Execute request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
