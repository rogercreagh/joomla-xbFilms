<?php
/**
 * @package xbFilms-Package
 * @filesource pkg_xbfilms_script.php
 * @version 0.9.8.8 19th October 2022
 * @desc install, upgrade and uninstall actions
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;

class pkg_xbfilmsInstallerScript
{
    protected $jminver = '3.10';
    protected $jmaxver = '4.0';
    
    function preflight($type, $parent)
    {
        $jversion = new Version();
        $jverthis = $jversion->getShortVersion();
        if ((version_compare($jverthis, $this->jminver,'lt')) || (version_compare($jverthis, $this->jmaxver, 'ge'))) {
            throw new RuntimeException('xbFilms requires Joomla version greater than or equal to '.$this->jminver. ' and less than '.$this->jmaxver.'. You have '.$jverthis);
        }
    }
    
    function install($parent)
    {
    }
    
    function uninstall($parent) {
        $oldval = Factory::getSession()->set('xbpkg', 'films');
        $db = Factory::getDBO();
        $db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbbooks'));
        $res = $db->loadResult();
        if ($res) {
            $message = 'xbBooks is still installed. If you wish to uninstall xbFilms then just uninstall the component for now as xbBooks requires xbPeople';
            //films component could have been uninstalled manually so we'll redirect to xbbooks dashboard as we know that exists
            $targ = Uri::base().'index.php?option=com_xbbooks&view=cpanel&err='.urlencode($message);
            header("Location: ".$targ);
            exit();
        }
        echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
    	echo '<h4>Uninstalling xbFilms Package</h4>';
    	$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbfilms'));
    	if ($db->loadResult()) {
    	    echo '<p>xbFilms component uninstalled.</p>';
    	} else {
    	    echo '<p>xbFilms component appears to have already been uninstalled.</p>';
    	}
    	$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbpeople'));
    	if ($db->loadResult()) {
    	    echo '<p>xbPeople component uninstalled.</p>';
    	} else {
    	    echo '<p>xbPeople component appears to have already been uninstalled.</p>';
    	}
    	echo '</div>';
    }
    
    function update($parent)
    {
    	echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
    	echo '<h3>xbFilms Package updated to version ' . $parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate . ' with components</h3>';
    	echo '<ul><li>xbFilms v.' . $parent->get('manifest')->xbfilms_version . '</li>';
    	echo '<li>xbPeople v.' . $parent->get('manifest')->xbpeople_version . '</li>';
    	echo '</ul>';
    	echo '<p>For details see <a href="https://crosborne.co.uk/xbfilms/changelog" target="_blank">
            www.crosborne.co.uk/xbfilms/changelog</a></p>';
    	echo '<p class="xbhlt">NB: Please refresh the xbFilms package cache on the Extensions|Manage page or the previous manifest may be used and cause uninstall errors</p>';
    	echo '</div>';
    }
    
    function postflight($type, $parent)
    {
    	if ($type=='install') {
	    	echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
		border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
	    	echo '<h3>xbFilms Package installed</h3>';
	    	echo '<p>Package version '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'<br />';
	    	echo 'Extensions included: </p>';
	    	echo '<ul><li><b>xbFilms '.$parent->get('manifest')->xbfilms_version.'</b>: manage/display films details and reviews</li>';
	    	echo '<li><b>xbPeople '.$parent->get('manifest')->xbpeople_version.'</b>: manage/display people and characters</li>';
	    	echo '</ul>';
	    	echo '<p>For help and information see <a href="https://crosborne.co.uk/xbflims/doc" target="_blank">
	            www.crosborne.co.uk/xbfilms/doc</a></p>';
	    	echo '<p><b>Important</b> Before starting review &amp; set the component options&nbsp;&nbsp;';
	    	echo '<a href="index.php?option=com_config&view=component&component=com_xbfilms" class="btn btn-small btn-info">xbFilms Options</a>';
	    	echo '<br /><i>After saving the options you will exit to the Dashboard for an overview</i>';
	    	echo '</p>';
	    	echo '<p><b>Dashboard</b> <i>The Dashboard view provides an overview of the component status</i>&nbsp;&nbsp;: ';
	    	echo '<a href="index.php?option=com_xbfilms&view=cpanel">xbFilms Dashboard</a> (<i>but save the options first!</i>)';
	    	echo '</p>';
	    	echo '<p><b>Sample Data</b> <i>You can install some sample data</i>&nbsp;&nbsp;: ';
	    	echo 'first check the option to show sample data button on the <a href="index.php?option=com_config&view=component&component=com_xbfilms#admin">Options Admin</a> tab, ';
	    	echo 'then an [Install/Remove Sample Data] button will appear in the xbFilms Dashboard toolbar.';
	    	echo '</p>';
	    	echo '<p><b>Import Data</b> <i>you can import data from CSV or SQL file</i>&nbsp;&nbsp;: ';
	    	echo 'visit the <a href="index.php?option=com_xbfilms&view=importexport#imp">Data Management Import</a> tab.';
	    	echo 'Be sure to read the <a href="https://crosborne.uk/xbfilms/doc#impcsv">documentation</a> first if importing from CSV';
	    	echo '</p>';
	    	echo '</div>';
	    	
	    	$message = $parent->get('manifest')->name .' v.'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.' has been installed';
	    	
	    	Factory::getApplication()->enqueueMessage($message, 'message');
    	}
    }
    
}
