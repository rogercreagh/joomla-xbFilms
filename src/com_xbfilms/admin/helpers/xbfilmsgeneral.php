<?php
/*******
 * @package xbFilms
 * @filesource admin/helpers/xbfilmsgeneral.php
 * @version 1.0.1.3 5th January 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Router\Route;

//class for methods used by both site and admin only in xbFilms

class XbfilmsGeneral {
    
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
     * @name getFilmPeople
     * @desc given a film id returns array of people objects, can be filtered by role
     * @param int $filmid
     * @param string $role if set return only those with a specific role, or '' for all roles
     * @return Array of objects (empty if no match) with properties name, link, role, note (plus others not used outside this function : firstname, lastname, id, pstate
     */
    public static function getFilmPeople($filmid, $role='') {
        $isadmin = Factory::getApplication()->isClient('administrator');
        $plink = 'index.php?option=com_xbpeople&view=person';
        if ($isadmin) {
            $plink .= '&layout=edit';
        }
        $plink .= '&id=';
        //$plink .= $edit ? '&task=person.edit&id=' : '&view=person&id=';
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select('a.role, a.role_note AS note, p.firstname, p.lastname, p.id, p.state AS pstate')
            ->from('#__xbfilmperson AS a')
            ->join('LEFT','#__xbpersons AS p ON p.id=a.person_id')
            ->join('LEFT','#__xbfilms AS f ON f.id=a.film_id')
            ->where('a.film_id = "'.$filmid.'"' );
        if (!$isadmin) {
            $query->where('p.state = 1');
        }
        if (!empty($role)) {
            $query->where('a.role = "'.$role.'"');
        } else { //order by role and listorder first
            //TODO make role names and order into param or table
            $query->order('(case a.role when '.$db->quote('director').' then 0
            when '.$db->quote('producer').' then 1
            when '.$db->quote('crew').' then 2
            when '.$db->quote('actor').' then 3
            when '.$db->quote('appearsin').' then 4
            end)');
            $query->order('a.listorder ASC');
        }
        //TODO use global name order param
        $query->order(array('p.lastname','p.firstname'));
        
        $db->setQuery($query);
        $people = $db->loadObjectList();
 
        foreach ($people as $i=>$p){
            $p->link = Route::_($plink . $p->id);
            $p->name = ($p->firstname!='') ? $p->firstname.' ' : '';
            $p->name .= $p->lastname;
            //if not published highlight in yellow if editable
            if ($p->pstate != 1) {
                $p->name = '<span class="xbhlt">'.$p->name.'</span>';
            }
        }
        return $people;
}

/**
 * @name getFilmChars
 * @desc given a film id returns array of chaaracter objects, can be filtered by role
 * @param int $filmid
 * @return Array of objects (empty if no match) with properties name, link, role, note including actor name if available
 */
public static function getFilmChars($filmid) {
    	$admin = Factory::getApplication()->isClient('administrator');
    	$link = 'index.php?option=com_xbfilms'. ($admin) ? '&task=character.edit&id=' : '&view=character&id=';
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
    	
    	$query->select('c.name, c.id, c.state AS chstate, a.char_note, a.actor_id AS aid, p.firstname AS firstname,p.lastname AS lastname')
    	->from('#__xbfilmcharacter AS a')
    	->join('LEFT','#__xbcharacters AS c ON c.id=a.char_id')
    	->join('LEFT','#__xbpersons AS p ON p.id=a.actor_id')
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
    	if (!empty($list)) {
        	foreach ($list as $i=>$item){
        	    $item->link = Route::_($link . $item->id);
        		if ($item->chstate != 1) {
        			$item->name = '<span style="background:yellow;">'.$item->name.'</span>';
        		}
        		$item->role='char';
        		if (!empty($item->aid)) {
        		    $item->note = 'played by '.$item->firstname.' '.$item->lastname.' - '.$item->char_note;
        		} else {
        		    $item->note = $item->char_note;
        		}
        	    
        	}
    	}
    	return $list;
    }
    
    /**
     * @name getFilmGroups()
     * @desc returns list of groups linked to given film
     * @param int $filmid
     * @return array of objects
     */
    public static function getFilmGroups($filmid) {
        $admin = Factory::getApplication()->isClient('administrator');
        $link = 'index.php?option=com_xbpeople'. ($admin) ? '&task=group.edit&id=' : '&view=group&id=';
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        
        $query->select('g.title, g.id, g.state AS gstate, a.role, a.role_note')
        ->from('#__xbfilmgroup AS a')
        ->join('LEFT','#__xbgroups AS g ON g.id=a.group_id')
        ->where('a.film_id = "'.$filmid.'"' );
        if (!$admin) {
            $query->where('g.state = 1');
        }
        $query->order('a.listorder', 'ASC');
        try {
            $db->setQuery($query);
            $list = $db->loadObjectList();
        } catch (Exception $e) {
            return '';
        }
        if (!empty($list)) {
            foreach ($list as $i=>$item){
                $item->link = Route::_($link . $item->id);
                if ($item->gstate != 1) {
                    $item->title = '<span class="xbhlt">'.$item->title.'</span>';
                }
                $item->note = $item->role_note;
            }
        }
        return $list;
    }
    
    /**
     * @name checkComPeople()
     * @desc check whether com_xbpeople is installed and sets/clears session variable
     * needed here as can't depend on XbcultureHelper::checkComponent() being available if xbPeople not installed
     * @return null if not installed, 0 if not enabled, 1 if ok
     */
    public static function checkComPeople() {
        $sess= Factory::getSession();
        $db = Factory::getDBO();
        $db->setQuery('SELECT enabled FROM #__extensions WHERE element = '.$db->quote('com_xbpeople'));
        $res = $db->loadResult();
        if (is_null($res)) {
            $sess->clear('xbpeople_ok');
        } else {
            $sess->set('xbpeople_ok',$res);
        }
        return $res;
    }
    
}