<?php
/*******
 * @package xbFilms
 * @filesource admin/xbfilms.php
 * @version 0.9.0 7th April 2021
 * @since 0.1.0 22nd November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html	
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

if (!Factory::getUser()->authorise('core.manage', 'com_xbfilms')) {
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
	return false;
}

//add the component, xbculture and fontawesome css
$document = Factory::getDocument();
$cssFile = Uri::root(true)."/media/com_xbpeople/css/xbculture.css";
$document->addStyleSheet($cssFile);
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
        if ($app->input->get('view')!='cpanel') {
            $app->redirect('index.php?option=com_xbfilms&view=cpanel');
            $app->close();
        }
    }
}

// Get an instance of the controller prefixed
$controller = JControllerLegacy::getInstance('Xbfilms');
// Perform the Request task and Execute request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
