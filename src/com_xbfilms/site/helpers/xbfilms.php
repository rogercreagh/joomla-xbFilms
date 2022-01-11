<?php
/*******
 * @package xbFilms
 * @filesource site/helpers/xbfilms.php
 * @version 0.9.5 9th May 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class XbfilmsHelper extends JHelperContent {
	
	public static function getCharacterFilmsArray($personid) {
		$link = 'index.php?option=com_xbfilms&view=film&id=';
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('b.title, b.subtitle, b.rel_year, b.id')
		->from('#__xbfilmcharacter AS a')
		->join('LEFT','#__xbfilms AS b ON b.id=a.film_id')
		->where('a.char_id = "'.$personid.'"' )
		->order('b.rel_year, b.title', 'ASC');
		$db->setQuery($query);
		$list = $db->loadObjectList();
		foreach ($list as $i=>$item){
			$tlink = Route::_($link . $item->id);
			$item->link = '<a href="'.$tlink.'">'.$item->title.'</a>';
			$item->display = $item->title;
		}
		return $list;
	}
	
	public static function getChildCats($pid, $ext, $incroot = true) {
// 		$childarr = array();
// 		$db    = Factory::getDbo();
// 		$query = $db->getQuery(true);
// 		$query->select('id')->from('#__categories')->where('parent_id = '.$db->quote($pid));
// 		$db->setQuery($query);
// 		$children = $db->loadColumn();
// 		if ($children) {
// 			$childarr = array_merge($childarr,$children);
// 			foreach ($children as $child){
// 				$gch = self::getChildCats($child);
// 				if ($gch) { $childarr = array_merge($childarr, $gch);}
// 			}
// 			return $childarr;
// 		}
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__categories')->where('id='.$pid);
		$db->setQuery($query);
		$pcat=$db->loadObject();
		$start = $incroot ? '>=' : '>';
		$query->clear();
		$query->select('id')->from('#__categories')->where('extension = '.$db->quote($ext));
		$query->where(' lft'.$start.$pcat->lft.' AND rgt <='.$pcat->rgt);
		$db->setQuery($query);
		return $db->loadColumn();
	}
	
	
	
	public static function sitePageHeader($displayData) {
		$header ='';
		if (!empty($displayData)) {
			$header = '	<div class="row-fluid"><div class="span12 xbpagehead">';
			if ($displayData['showheading']) {
				$header .= '<div class="page-header"><h1>'.$displayData['heading'].'</h1></div>';
			}
			if ($displayData['title'] != '') {
				$header .= '<h3>'.$displayData['title'].'</h3>';
				if ($displayData['subtitle']!='') {
					$header .= '<h4>'.$displayData['subtitle'].'</h4>';
				}
				if ($displayData['text'] != '') {
					$header .= '<p>'.$displayData['text'].'</p>';
				}
			}
		}
		return $header;
	}

}