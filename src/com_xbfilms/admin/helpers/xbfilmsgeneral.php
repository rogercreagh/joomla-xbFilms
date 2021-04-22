<?php
/*******
 * @package xbBooks
 * @filesource admin/helpers/xbfilmsgeneral.php
 * @version 0.4.9 6th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;


//class for methods used by both site and admin

class XbfilmsGeneral {
    
    /***
     * checkComponent()
     * test whether a component is installed and enabled. Sets a session variable to save a subsequent db call
     * @param  $name - component name as stored in the extensions table (eg com_xbfilms)
     * @param $usedb - if true will ignore session variable an force db check
     * @return boolean|number - true= installed and enabled, 0= installed not enabled, false = not installed
     */
    public static function checkComponent($name) {     	
    	$sname=substr($name,4).'_ok';
    	$sess= Factory::getSession();
    	$db = Factory::getDBO();
    	$db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote($name));
   		$res = $db->loadResult();
   		$sess->set($sname,$res);
  		return $res;
    }
    
    /**
     * @name makeSummaryText
     * @desc returns a plain text version of the source trunctated at the first or last sentence within the specified length
     * @param string $source the string to make a summary from
     * @param int $len the maximum length of the summary
     * @param bool $first if true truncate at end of first sentence, else at the last sentence within the max length
     * @return string
     */
    public static function makeSummaryText(string $source, int $len=250, bool $first = true) {
    	if ($len == 0 ) {$len = 100; $first = true; }
    	//first strip any html and truncate to max length
    	$summary = HTMLHelper::_('string.truncate', $source, $len, true, false);
    	//strip off ellipsis if present (we'll put it back at end)
    	$hadellip = false;
    	if (substr($summary,strlen($summary)-3) == '...') {
    		$summary = substr($summary,0,strlen($summary)-3);
    		$hadellip = true;
    	}
    	// get a version with '? ' and '! ' replaced by '. '
    	$dotsonly = str_replace(array('! ','? '),'. ',$summary.' ');
    	if ($first) {
    		// look for first ". " as end of sentence
    		$dot = strpos($dotsonly,'. ');
    	} else {
    		// look for last ". " as end of sentence
    		$dot = strrpos($dotsonly,'. ');
    	}
    	// are we going to cut some more off?)
    	if (($dot!==false) && ($dot < strlen($summary)-3)) {
    		$hadellip = true;
    	}
    	if ($dot>3) {
    		$summary = substr($summary,0, $dot+1);
    	}
    	if ($hadellip) {
    		// put back ellipsis with a space
    		$summary .= ' ...';
    	}
    	return $summary;
    }
    
    /**
     * @name getFilmReviews()
     * @desc Gets an object list of reviews given a film id
     * @param int $film required
     * @return object list
     */
    public static function getFilmReviews(int $film) {
    	$ord = 'rev_date'; $dir='ASC'; //oldest first
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);   	
    	$query->select('r.*,c.title AS category_title')
    	->from('#__xbfilmreviews AS r')
    	->join('LEFT','#__categories AS c ON c.id = r.catid')
    	->where('r.film_id = "'.$film.'"' )
    	->where('r.state = '.$db->quote('1'));
    	$query->order($db->escape($ord.' '.$dir));
    	$db->setQuery($query);
    	$revs = $db->loadObjectList();
    	$tagsHelper = new TagsHelper;
    	foreach ($revs as $r) {
    		$r->tags = $tagsHelper->getItemTags('com_xbfilms.review' , $r->id);
    		$r->tagcnt = count($r->tags);
    	}
    	return $revs;
    }
    
    /**
     * @name makeLinkedNameList
     * @param array $arr required - array of details to turn into list
     * @param string $role default'' - filter by role type
     * @param string $sep default ',' - separtor between list elements (eg <br />)
     * @param boolean $linked default true - if true use linked names to detail view (set false for use in tooltips)
     * @param boolean $amp default true - if true and list is only two people used ampersand as separator
     * @param boolean $note default 0 - - 1 = prepend role_note as itallics in span minwidth 60px, 2 = append the role_note to the name in brackets
     * @return string
     */
    public static function makeLinkedNameList($arr, $role='', $sep=',', $linked=true, $amp = true, $note = 0) {
        $wynik = '';
        $cnt = 0;
        foreach ($arr as $item) {
            if (($role=='') || ($role == $item->role)) {
            	if($note==1) {
            		$wynik .= '<span class="xbnit xbsp60">'.$item->role_note.':</span> ';
            	}
            	$wynik .= ($linked) ? $item->link : $item->display;
            	if (($note==2) && ($item->role_note !='')) {
            		$wynik .= ' ('.$item->role_note.')';
            	}
            	$wynik .= $sep;
            	$cnt++;
            }
        }
        //strip off final separator which could be a string so can't use trim
        if (substr($wynik,-strlen($sep))===$sep) $wynik = substr($wynik, 0, strlen($wynik)-strlen($sep));
        //if it is a comma list with only two items then we might use & rather than ,
        if (($cnt==2) && (trim($sep)==',') && $amp) {
            $wynik = str_replace($sep,' &amp; ',$wynik);
        }
        return trim($wynik);
    }
    
    /**
     * @name getFilmRoleArray
     * @desc given a film id returns an array of objects representing people with their roles in the film.
     * @param int $filmid
     * @param string $role if set return only those with a specific role, or '' for all roles
     * @param boolean $edit set true for the link to be to the edit view, default false for the front-end view
     * @return Array of objects (empty if no match)
     */
    public static function getFilmRoleArray($filmid, $role='', $edit=false) {
    	$link = 'index.php?option=com_xbfilms';
    	$link .= $edit ? '&task=person.edit&id=' : '&view=person&id=';
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
    	//TODO use global name order param
    	$query->select('a.role, a.role_note, p.firstname, p.lastname, p.id, p.state AS pstate')
    	->from('#__xbfilmperson AS a')
    	->join('LEFT','#__xbpersons AS p ON p.id=a.person_id')
    	->join('LEFT','#__xbfilms AS f ON f.id=a.film_id')
    	->where('a.film_id = "'.$filmid.'"' );
    	$query->order(array('a.role','a.listorder ASC','p.lastname'));
//    	if ($role!='') {
//    	} else {
//    	    $query->order('p.lastname ASC');
//    	}
    	if (!empty($role)) {
    		$query->where('a.role = "'.$role.'"');
    	}
    	
    	$db->setQuery($query);
    	$list = $db->loadObjectList();
    	foreach ($list as $i=>$item){
    		$ilink = Route::_($link . $item->id);
    		$name = ($item->firstname!='') ? $item->firstname.' ' : '';
    		$name .= $item->lastname;
    		//if not published highlight in yellow if editable or grey if view not linked
    		if ($item->pstate != 1) {
    			$flag = $edit ? 'xbhlt' : 'xbdim';
    			$item->display = '<span class="'.$flag.'">'.$name.'</span>';
    		} else {
    			$item->display = $name;
    		}
    		//if item not published only link if to edit page
    		if (($edit) || ($item->pstate == 1)) {
    			$item->link = '<a href="'.$ilink.'">'.$item->display.'</a>';
    		} else {
    			$item->link = $item->display;
    		}
    	}
    	return $list;
    }
    
    public static function getFilmCharsArray($filmid) {
    	$admin = Factory::getApplication()->isClient('administrator');
    	$link = 'index.php?option=com_xbfilms'. ($admin) ? '&task=character.edit&id=' : '&view=character&id=';
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
    	
    	$query->select('c.name, c.id, c.state AS chstate, a.char_note, a.actor_id')
    	->from('#__xbfilmcharacter AS a')
    	->join('LEFT','#__xbcharacters AS c ON c.id=a.char_id')
    	->where('a.film_id = "'.$filmid.'"' );
    	if (!$admin) {
    		$query->where('c.state = 1');
    	}
    	$query->order('a.film_id, a.listorder', 'ASC');
    	try {
    		$db->setQuery($query);
    		$list = $db->loadObjectList();
    	} catch (Exception $e) {
    		return '';
    	}
    	foreach ($list as $i=>$item){
    		$ilink = Route::_($link . $item->id);
    		if ($item->chstate != 1) {
    			$item->display = '<span style="background:yellow;">'.$item->name.'</span>';
    		} else {
    			$item->display = $item->name;
    		}
    		$item->link = '<a href="'.$ilink.'">'.$item->display.'</a>';
    	}
    	return $list;
    }
    
    /**
     * @name getPersonROleArray
     * @desc given a person id returns an array of objects representing films and their role in the film  
     * @param int $personid
     * @param string $role - if set return only those with matching roles, or '' for all roles
     * @param boolean $edit - true for the linked version of the title to point to the edit page for the film, false for the normal view
     * @return Array of objects
     */
    public static function getPersonRoleArray($personid, $role='', $edit=false) {
        $link = 'index.php?option=com_xbfilms';
        $link .= $edit ? '&task=film.edit&id=' : '&view=film&id=';
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        
        $query->select('a.role, a.role_note, f.title, f.subtitle, f.rel_year, f.id, f.state AS fstate')
        ->from('#__xbfilmperson AS a')
        ->join('LEFT','#__xbfilms AS f ON f.id=a.film_id')
        ->where('a.person_id = "'.$personid.'"' )
        ->order('f.rel_year DESC, f.title', 'ASC');
        if (!empty($role)) {
            $query->where('a.role = "'.$role.'"');
        }
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach ($list as $i=>$item){
            $tlink = Route::_($link . $item->id);
            //if not published highlight in yellow if editable or grey if view
            if ($item->fstate != 1) {
            	$flag = $edit ? 'xbhlt' : 'xbdim';
            	$item->display = '<span class="'.$flag.'">'.$item->title.'</span>';
            } else {
            	$item->display = $item->title;
            }
            //if item not published only link if to edit page
            if (($edit) || ($item->fstate == 1)) {
            	$item->link = '<a href="'.$tlink.'">'.$item->display.'</a>';
            } else {
            	$item->link = $item->display;
            }
        }
        return $list;
    }   

    public static function credit() {
        if (Factory::getSession()->get('xbpeople_ok')) { //(self::checkComponent('com_xbpeople') ) {
            require_once JPATH_ADMINISTRATOR.'/components/com_xbpeople/helpers/xbpeople.php';
            if (XbpeopleHelper::penPont()) {
                return '';
            }
        }
        $credit='<div class="xbcredit">';
        if (Factory::getApplication()->isClient('administrator')==true) {
            $xmldata = Installer::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_xbfilms/xbfilms.xml');
            $credit .= '<a href="http://crosborne.uk/xbfilms" target="_blank">
                xbFilms Component '.$xmldata['version'].' '.$xmldata['creationDate'].'</a>';
            $credit .= '<br />'.Text::_('COM_XBCULTURE_BEER_TAG');
            $credit .= Text::_('COM_XBCULTURE_BEER_FORM');
        } else {
        	$credit .= 'xbFilms by <a href="http://crosborne.uk/xbfilms" target="_blank">CrOsborne</a>';           	
        }
        $credit .= '</div>';
        return $credit;
    }
    
}