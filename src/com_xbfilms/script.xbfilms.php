<?php
/*******
 * @package xbFilms
 * @filesource script.xbfilms.php
 * @version 1.0.3.11 13th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html 
 ******/
// No direct access to this file
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;

class com_xbfilmsInstallerScript 
{	
    protected $jminver = '3.10';
    protected $jmaxver = '4.0';
    protected $extension = 'com_xbfilms';
    protected $ver = 'v0';
    protected $date = '';
    protected $pminver = '1.0.3.8';
    
    function preflight($type, $parent) {
        $jversion = new Version();
        $jverthis = $jversion->getShortVersion();
        if ((version_compare($jverthis, $this->jminver,'lt')) || (version_compare($jverthis, $this->jmaxver, 'ge'))) {
            throw new RuntimeException('xbFilms requires Joomla version greater than '.$this->jminver. ' and less than '.$this->jmaxver.'. You have '.$jverthis);
        }
        $message='';
        $xbp = $this->checkXbPeople($this->pminver);
        
        if ($xbp === false ) {
            $message = 'Component xbPeople appears not to be installed. Please install and enable it before installing xbFilms.';
            Factory::getApplication()->enqueueMessage($message);
            throw new RuntimeException('xbPeople not found: install aborted');
        }
        if ($xbp === 0 ) {
            Factory::getApplication()->enqueueMessage('Component xbPeople appears to be disabled. Please enable it and save options before running xbFilms.','alert');
        } elseif (is_array($xbp)) {
             $message = 'xbPeople version '.$xbp['version'].' is out of date. Please update xbPeople to '.$this->pminver.' or higher before installing xbFilms.';
             Factory::getApplication()->enqueueMessage($message);
             throw new RuntimeException('xbPeople version out of date: install aborted' );
        } elseif ($xbp !==1) {
            Factory::getApplication()->enqueueMessage('unknow value checking com_xbpeople');
            throw new RuntimeException();
        }
        if ($type=='update') {
        	$componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbfilms/xbfilms.xml'));
        	$this->ver = $componentXML['version'];
        	$this->date = $componentXML['creationDate'];
        	$message = 'Updating xbFilms component from '.$componentXML['version'].' '.$componentXML['creationDate'];
        	$message .= ' to '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate;
        }
        if ($message!='') { Factory::getApplication()->enqueueMessage($message,'');}
    }
    
    function install($parent) {        
    }
    
    function uninstall($parent) {
        $app = Factory::getApplication();
        //clear the packageuninstall flag if it is set
        $oldval = Factory::getSession()->clear('xbpkg');
        
        $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbfilms/xbfilms.xml'));
    	$message = 'Uninstalling xbFilms component v.'.$componentXML['version'].' '.$componentXML['creationDate'];
    	//are we also clearing data?
    	$savedata = ComponentHelper::getParams('com_xbfilms')->get('savedata',0);
    	if ($savedata == 0) {
    	    if ($this->uninstalldata()) {
    	        $message .= ' ... xbFilms data tables deleted';
    	    }
    	    $dest='/images/xbfilms';
    	    if (JFolder::exists(JPATH_ROOT.$dest)) {
    	        if (JFolder::delete(JPATH_ROOT.$dest)){
    	            $message .= ' ... images/xbfilms folder deleted';
    	        } else {
    	            $err = 'Problem deleting xbFilms images folder "/images/xbfilms" - please check in Media manager';
    	            $app->enqueueMessage($err,'Error');
    	        }
    	    }
    	} else {
    	    $message .= ' xbFilms data tables and images folder have <b>NOT</b> been deleted. CATEGORIES may be recovered on re-install, but TAG links will be lost although tags have not been deleted.';

    	    // allow categories to be recovered with same id
    	    $db = Factory::getDbo();
    	    $db->setQuery(
    	        $db->getQuery(true)
    	        ->update('#__categories')
    	        ->set('extension='.$db->q('!com_xbfilms!'))
    	        ->where('extension='.$db->q('com_xbfilms'))
    	        )
    	        ->execute();
    	        $cnt = $db->getAffectedRows();
    	        
    	        if ($cnt>0) {
    	            $message .= '<br />'.$cnt.' xbFilms category extensions renamed as "<b>!</b>com_xbfilms<b>!</b>". They will be recovered on reinstall with original ids.';
    	        }
    	}    	
    	$app->enqueueMessage($message,'Info');    	
    }
    
    function update($parent) {
    	$message = '<br />Visit the <a href="index.php?option=com_xbfilms&view=dashboard" class="btn btn-small btn-info">';
    	$message .= 'xbFilms Dashboard</a> page for overview of status.</p>';
    	$message .= '<br />For ChangeLog see <a href="http://crosborne.co.uk/xbfilms/changelog" target="_blank">
            www.crosborne.co.uk/xbfilms/changelog</a></p>';
    	Factory::getApplication()->enqueueMessage($message,'Message');
    	$delfiles = '';
    	//since v1.0.1
    	$delfiles .= '/models/fields/allpeople.php,/models/fields/filmpeople.php,/models/fields/catsubtree.php,/models/fields/characters.php';
        $delfiles .= ',/models/fields/nationality.php,/models/fields/natlist.php,/models/fields/people.php';
        $delfiles .= ',/models/fields/filmyear.php,/models/fields/revyear.php,/models/forms/booklist.xml,/models/forms/peoplelist.xml';
        $delfiles .= ',/controllers/character.php,/controllers/person.php,/models/character.php,/models/person.php';
        $delfiles .= ',/models/forms/character.xml,/models/forms/person.xml,/tables/character.php,/tables/person.php';
        $delfiles .= ',/views/character,/views/person,/models/forms/filmlist.xml,/views/films/tmpl/modal.php,/views/review/tmpl/view.php';
        $delfiles .= ',views/persons/tmpl/default_batch_body.php,views/persons/tmpl/default_batch_footer.php';
        $delfiles .= ',views/characters/tmpl/default_batch_body.php,views/characters/tmpl/default_batch_footer.php';
        $delfiles .= ',views/reviews/tmpl/default_batch_body.php,views/reviews/tmpl/default_batch_footer.php';
        $delfiles .= ',/views/filmlist/tmpl/onecol.php,/views/filmlist/tmpl/onecol.xml';
        //reset above after v1.2.0
        $delfiles = explode(',',$delfiles);
        $cnt = 0; $dcnt=0;
    	$ecnt = 0;
    	$message = 'Deleting Redundant Files in '.JPATH_ROOT.'/[administrator/]components/com_xbfilms/ <br />';
    	foreach ($delfiles as $f) {
    	    if (substr($f,0,1)=='/') {
    	        $name = JPATH_ROOT.'/components/com_xbfilms'.$f;
    	    } else {
    	        $name = JPATH_ADMINISTRATOR.'/components/com_xbfilms/'.$f;
    	    }
    	    if (file_exists($name)) {
    	        if (is_dir($name)) {
    	            if ($this->rrmdir($name)) {
    	                $dcnt ++;
        	               $message .= 'RMDIR '.$f.'<br />';
    	            }
    	        } else {
    	            if (unlink($name)) {
        	            $message .= 'DEL '.$f.'<br />';
    	                $cnt ++;
    	            } else {
    	                $message .= 'DELETE FAILED: '.$f.'<br />';
    	                $ecnt ++;
    	            }
    	        }
    	    } else {
        	  //  $message .= 'FILE NOT FOUND: '.$f.'<br />';
    	    }
    	}
    	if (($cnt+$ecnt+$dcnt)>0) {
    	    $message .= $cnt.' files, '.$dcnt.' folders cleared';
    	    $mtype = ($ecnt>0) ? 'Warning' : 'Message';
    	    Factory::getApplication()->enqueueMessage($message, $mtype);
    	}
    }
    
    function postflight($type, $parent) {
    	if ($type=='install') {
    	    $app = Factory::getApplication();
    	    $componentXML = Installer::parseXMLInstallFile(Path::clean(JPATH_ADMINISTRATOR . '/components/com_xbfilms/xbfilms.xml'));
    		$message = '<b>xbFilms '.$componentXML['version'].' '.$componentXML['creationDate'].'</b><br />';
    		
    		//create xbfilms image folder
        	if (!file_exists(JPATH_ROOT.'/images/xbfilms')) {
         		mkdir(JPATH_ROOT.'/images/xbfilms',0775);
         		$message .= 'Film images folder created (/images/xbfilms/).<br />';
        	} else{
         		$message .= '"/images/xbfilms/" already exists.<br />';
         	}
         	
         	// Recover categories if they exist assigned to extension !com_xbfilms!
         	$db = Factory::getDbo();
         	$qry = $db->getQuery(true);
         	$qry->update('#__categories')
         	->set('extension='.$db->q('com_xbfilms'))
         	->where('extension='.$db->q('!com_xbfilms!'));
         	$db->setQuery($qry);
         	try {
         	    $db->execute();
         	    $cnt = $db->getAffectedRows();
         	} catch (Exception $e) {
         	    $app->enqueueMessage($e->getMessage(),'Error');
         	}
         	$message .= $cnt.' existing xbFilm categories restored. ';
         	// create default categories using category table
         	$cats = array(
         		array("title"=>"Uncategorised","desc"=>"default fallback category for all xbFilms items"),
                array("title"=>"Imported","desc"=>"default category for xbFilms imported data"),
         	    array("title"=>"Films","desc"=>"default parent category for Films"),
                array("title"=>"Film Reviews","desc"=>"default parent category for Film Reviews"));
         	$message .= $this->createCategory($cats);
         	
         	$app->enqueueMessage($message,'Info');  

	        // we assume people default categories are already installed by xbpeople
	        // we assume that indicies for xbpersons and xbcharacter tables have been handled by xbpeople install
	        //set up indicies for books and bookreviews tables - can't be done in install.sql as they may already exists
	        //mysql doesn't support create index if not exists.
	        $message = 'Checking indicies... ';
	        
	        $prefix = $app->get('dbprefix');
	        $querystr = 'ALTER TABLE '.$prefix.'xbfilms ADD INDEX filmaliasindex (alias)';
	        $err=false;
	        try {
	            $db->setQuery($querystr);
	            $db->execute();
	        } catch (Exception $e) {
	            if($e->getCode() == 1061) {
	                $message .= '- film alias index already exists. ';
	            } else {
	                $message .= '[ERROR creating filmaliasindex: '.$e->getCode().' '.$e->getMessage().']';
	                $app->enqueueMessage($message, 'Error');
	                $message = 'Checking indicies... ';
	                $err = true;
	            }
	        }
	        if (!$err) {
	            $message .= '- film alias index created. ';
	        }
	        $querystr = 'ALTER TABLE '.$prefix.'xbfilmreviews ADD INDEX reviewaliasindex (alias)';
	        $err=false;
	        try {
	            $db->setQuery($querystr);
	            $db->execute();
	        } catch (Exception $e) {
	            if($e->getCode() == 1061) {
	                $message .= '- filmreviews alias index already exists';
	            } else {
	                $message .= '<br />[ERROR creating reviewaliasindex: '.$e->getCode().' '.$e->getMessage().']<br />';
	                $app->enqueueMessage($message, 'Error');
	                $message = '';
	                $err = true;
	            }
	        }
	        if (!$err) {
	            $message .= '- filmreviews alias index created.';
	        }
	        
	        $app->enqueueMessage($message,'Info');
	        
	        
         	//check if people available
    		$xbpeople = true;
    		$db = Factory::getDbo();
    		$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbpeople'));
    		if (!$db->loadObject()) {
    			$xbpeople = false;
    		}

	        $oldval = Factory::getSession()->set('xbpeople_ok', $xbpeople);
	              
	        echo '<div style="padding: 7px; margin: 0 0 8px; list-style: none; -webkit-border-radius: 4px; -moz-border-radius: 4px;
		border-radius: 4px; background-image: linear-gradient(#ffffff,#efefef); border: solid 1px #ccc;">';
	        echo '<h3>xbFilms Component installed</h3>';
	        echo '<p>version '.$parent->get('manifest')->version.' '.$parent->get('manifest')->creationDate.'<br />';
	        echo '<p>For help and information see <a href="https://crosborne.co.uk/xbfilms/doc" target="_blank">
	            www.crosborne.co.uk/xbfilms/doc</a> or use Help button in xbFilms Dashboard</p>';
	        echo '<h4>Next steps</h4>';
	        if (!$xbpeople) {
	        	echo '<h4 style="color:red;margin-left:30px;">You must (re-)install xbPeople component before you can use xbFilms or any other xbCulture component';
	        	echo '</h4>';
	        } else {
		        echo '<p><b>Important</b> Before starting review &amp; set the component options&nbsp;&nbsp;';
		        echo '<a href="index.php?option=com_config&view=component&component=com_xbfilms" class="btn btn-small btn-info">xbFilms Options</a>';
		        echo '<br /><i>After saving the options you will exit to the Dashboard for an overview</i>';
		        echo '</p>';
		        echo '<p><b>Dashboard</b> <i>The Dashboard view provides an overview of the component status</i>&nbsp;&nbsp;:';
		        echo '<a href="index.php?option=com_xbfilms&view=dashboard">xbFilms Dashboard</a> (<i>but save the options first!</i>)';
		        echo '</p>';
		        echo '<p><b>Sample Data</b> <i>You can install some sample data</i>&nbsp;&nbsp;: ';
		        echo 'first check the option to show sample data button on the <a href="index.php?option=com_config&view=component&component=com_xbfilms#admin">Options Admin</a> tab, ';
		        echo 'then an [Install/Remove Sample Data] button will appear in the xbFilms Dashboard toolbar.';
		        echo '</p>';
		        echo '<p><b>Import Data</b> <i>you can import data from CSV or SQL file</i>&nbsp;&nbsp;: ';
		        echo 'visit the <a href="index.php?option=com_xbfilms&view=importexport#imp">Data Management Import</a> tab.';
		        echo 'Be sure to read the <a href="https://crosborne.uk/xbfilms/doc#impcsv">documentation</a> first if importing from CSV';
		        echo '</p>';
	        }
	        echo '</div>';
            $oldval = Factory::getSession()->set('xbfilms_ok', true);       
    	}
	}
     
	public function createCategory($cats) {
		$message = 'Creating '.$this->extension.' categories. ';
		foreach ($cats as $cat) {
			$db = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('id')->from($db->quoteName('#__categories'))
			->where($db->quoteName('title')." = ".$db->quote($cat['title']))
			->where($db->quoteName('extension')." = ".$db->quote('com_xbfilms'));
			$db->setQuery($query);
			if ($db->loadResult()>0) {
				$message .= '"'.$cat['title'].' already exists<br /> ';
			} else {
				$category = Table::getInstance('Category');
				$category->extension = $this->extension;
				$category->title = $cat['title'];
				if (array_key_exists('alias', $cat)) { $category->alias = $cat['alias']; }
				$category->description = $cat['desc'];
				$category->published = 1;
				$category->access = 1;
				$category->params = '{"category_layout":"","image":"","image_alt":""}';
				$category->metadata = '{"page_title":"","author":"","robots":""}';
				$category->language = '*';
				// Set the location in the tree
				$category->setLocation(1, 'last-child');
				// Check to make sure our data is valid
				if ($category->check()) {
					if ($category->store(true)) {
						// Build the path for our category
						$category->rebuildPath($category->id);
						$message .= $cat['title'].' id:'.$category->id.' created ok. ';
					} else {
						throw new Exception(500, $category->getError());
						//return '';
					}
				} else {
					throw new Exception(500, $category->getError());
					//return '';
				}
			}
		}
		return $message;
	}
	
	protected function uninstalldata() {
	    $message = '';
	    $db = Factory::getDBO();
	    $db->setQuery('DROP TABLE IF EXISTS `#__xbfilms`, `#__xbfilmreviews`, `#__xbfilmperson`, `#__xbfilmcharacter`');
	    $res = $db->execute();
	    if ($res === false) {
	        $message = 'Error deleting xbFilms tables, please check manually';
	        Factory::getApplication()->enqueueMessage($message,'Error');
	        return false;
	    }
	    return true;
	}
	
	protected function rrmdir($dir) {
	    if (is_dir($dir)) {
	        $objects = scandir($dir);
	        foreach ($objects as $object) {
	            if ($object != "." && $object != "..") {
	                if (filetype($dir."/".$object) == "dir") {
	                    $this->rrmdir($dir."/".$object);
	                } else {
	                    unlink($dir."/".$object);
	                }
	            }
	        }
	        reset($objects);
	        rmdir($dir);
	        return true;
	    }
	    return false;
	}

	protected static function checkXbPeople(string $minver = '') {
	    $db = Factory::getDBO();
	    $qry = $db->getQuery(true);
	    $qry->select('enabled, manifest_cache')
	    ->from($db->quoteName('#__extensions'))
	    ->where($db->quoteName('element').' = '.$db->quote('com_xbpeople'));
	    $db->setQuery($qry);
	    $res = $db->loadAssoc();
	    if (is_null($res)) {
	        return false;
	    } elseif($res['enabled']==0) {
	        return 0;
	    } else {
	        if ($minver != '') {
	            $manifest = json_decode($res['manifest_cache'],true);
	            if (version_compare($minver, $manifest['version']) == 1) {
	                return $manifest;
	            }
	        }
	    }	    
	    return 1;
	}
	
	
}

