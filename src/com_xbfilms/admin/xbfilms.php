<?php
/*******
 * @package xbFilms
 * @filesource admin/xbfilms.php
 * @version 1.0.1.3 5th January 2023
 * @since 0.1.0 22nd November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html	
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Controller\BaseController;

$app = Factory::getApplication();
if (!Factory::getUser()->authorise('core.manage', 'com_xbfilms')) {
    Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'),'warning');
    return false;
}

$document = Factory::getDocument();
Factory::getLanguage()->load('com_xbculture');

//add the component, xbculture and fontawesome css
$params = ComponentHelper::getParams('com_xbfilms');
if ($params->get('savedata','notset')=='notset') {
    Factory::getApplication()->enqueueMessage(Text::_('XBCULTURE_OPTIONS_UNSAVED'),'Error');
}
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

$cssFile = "https://use.fontawesome.com/releases/v5.8.1/css/all.css\" integrity=\"sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf\" crossorigin=\"anonymous";
$document->addStyleSheet($cssFile);

// Require helper files
JLoader::register('XbfilmsHelper', JPATH_ADMINISTRATOR . '/components/com_xbfilms/helpers/xbfilms.php');
JLoader::register('XbfilmsGeneral', JPATH_ADMINISTRATOR . '/components/com_xbfilms/helpers/xbfilmsgeneral.php');
JLoader::register('XbcultureHelper', JPATH_ADMINISTRATOR . '/components/com_xbpeople/helpers/xbculture.php');

$sess= Factory::getSession();
$sess->set('xbfilms_ok',true);

//detect related components and set session flags
if ($sess->get('xbpeople_ok',false) != 1) {
    if (XbfilmsGeneral::checkComPeople() != 1) {
        if ($app->input->get('view')!='dashboard') {
            $app->redirect('index.php?option=com_xbbooks&view=dashboard');
            $app->close();
        }
    }
}
//if there is no session variable for films/events check them.
if (!$sess->has('xbbooks_ok')) {
    XbcultureHelper::checkComponent('com_xbbooks');
}
if (!$sess->has('xbevents_ok')) {
    XbcultureHelper::checkComponent('com_xbevents');
}
// Get an instance of the controller prefixed
$controller = BaseController::getInstance('xbfilms');
// Perform the Request task and Execute request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
