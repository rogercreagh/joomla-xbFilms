<?php
/**
 * @package xbFilms-Package
 * @filesource pkg_xbfilms_script.php
 * @version 0.9.3 12th April 2021
 * @desc install, upgrade and uninstall actions
 * @author Roger C-O
 * @copyright (C) Roger Creagh-Osborne, 2019
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

class pkg_xbfilmsInstallerScript
{
    protected $jminver = '3.9';
    protected $jmaxver = '4.0';
    
    function preflight($type, $parent)
    {
        $jversion = new JVersion();
        $jverthis = $jversion->getShortVersion();
        if ((version_compare($jverthis, $this->jminver,'lt')) || (version_compare($jverthis, $this->jmaxver, 'ge'))) {
            throw new RuntimeException('xbFilms requires Joomla version greater than or equal to '.$this->jminver. ' and less than '.$this->jmaxver.'. You have '.$jverthis);
        }
    }
    
    function install($parent)
    {
    }
    
    function uninstall($parent)
    {
    	$message = 'Uninstalling xbFilms Package';
//     	$db = Factory::getDBO();
//     	$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbbooks'));
//     	$res = $db->loadResult();
//     	if ($res) {
//     		$message = '<b>xbFilms package uninstall says:</b> xbBooks is still installed but xbPeople has been removed with this package. No data has been deleted, but if you wish to continue using xbBooks it is recommended you reinstall xbPeople.';
//     		$message .= '<br />To install it now copy this url <b> https://www.crosborne.uk/downloads?download=11 </b>, and paste the link into the box on the ';
//     		$message .= '<a href="index.php?option=com_installer&view=install#url">Install from URL page</a>, ';
//     		$message .= 'or <a href="https://www.crosborne.uk/downloads?download=11">download here</a> and drag and drop onto the install box on this page.';
//      		Factory::getApplication()->enqueueMessage($message,'Error');
     		
//     	}
    	echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
	border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
    	echo '<h4>Uninstalling xbFilms Package</h4>';
        echo '<p>This is removing the xbFilms and xbPeople components, but will leave the xbPeople data tables and images.';
        echo '<br />You can can delete the images using Media manager (under Admin menu|Content).';
        echo '<br />A separate tool to clear residual xbPeople data will be <a href="https://crosborne.uk/xbdelpeople">available dreckly</a> from CrOsborne...</p>';
        echo '<i>"<b>dreckly</b>" is Cornish dialect word meaning the same as "whenever" but without the terrible sense of urgency</i>';
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
	    	echo '<ul><li>xbFilms '.$parent->get('manifest')->xbfilms_version.' manage/display films details and reviews</li>';
	    	echo '<li>xbPeople '.$parent->get('manifest')->xbpeople_version.' manage/display people and characters</li>';
	    	echo '</ul>';
	    	echo '<p>For help and information see <a href="https://crosborne.co.uk/xbflims/doc" target="_blank">
	            www.crosborne.co.uk/xbfilms/doc</a></p>';
	    	echo '<h4>Next steps</h4>';
	    	echo '<p><i>Review &amp; set the options</i>&nbsp;&nbsp;';
	    	echo '<a href="index.php?option=com_config&view=component&component=com_xbfilms" class="btn btn-small btn-info">xbFilms Options</a></p>';
	        echo '<p><i>Check the control panel for an overview</i>&nbsp;&nbsp;';
	        echo '<a href="index.php?option=com_xbfilms&view=cpanel" class="btn btn-small btn-success">xbFilms cPanel</a></p>';
	        echo '<p><i>Sample data can be installed from xbFilms Control Panel after saving the option found on the admin tab to display the toolbar button</i>';
	        echo '</p>';
	        echo '</div>';
	    	
	    	$message = $parent->get('manifest')->name .' v.'.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.' has been installed';
	    	
	    	Factory::getApplication()->enqueueMessage($message, 'message');
    	}
    }
    
}
