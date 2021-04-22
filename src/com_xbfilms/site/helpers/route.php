<?php
/*******
 * @package xbFilms
 * @filesource site/helpers/xbfilms.php
 * @version 0.5.0 5th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class XbfilmsHelperRoute
{
	public static function &getItems() {
		static $items;
		
		// Get the menu items for this component.
		if (!isset($items)) {
			$component = ComponentHelper::getComponent('com_xbfilms');
			$items     = Factory::getApplication()->getMenu()->getItems('component_id', $component->id);			
			// If no items found, set to empty array.
			if (!$items) {
				$items = array();
			}
		}		
		return $items;
	}

	/**
	 * @name getFilmsRoute
	 * @desc Get menu itemid filmlist view in default layout
	 * @param boolean $retstr if false return integer id, if true return return string with "&Itemid="
	 * @return string|int|NULL
	 */
	public static function getFilmsRoute($retstr=false) {
		$items  = self::getItems();
		foreach ($items as $item) {
			if ((isset($item->query['view']) && $item->query['view'] === 'filmlist')
					&& ((empty($item->query['layout']) || $item->query['layout'] === 'default')) ) {
						return ($retstr)? '&Itemid='.$item->id : $item->id;
					}
		}
		return null;
	}
	
	/**
	 * @name getFilmsLink
	 * @desc Get link to films view
	 * @return string
	 */
	public static function getFilmsLink() {
		$flink = 'index.php?option=com_xbfilms';
		$items  = self::getItems();
		foreach ($items as $item) {
		    if ((isset($item->query['view']) && $item->query['view'] === 'filmlist') 
		        && ((empty($item->query['layout']) || $item->query['layout'] === 'default')) ) {
			        return $flink.'&Itemid='.$item->id;              
			}
		}
		return $flink.'&view=filmlist';
	}

/**
	 * @name getFilmsCompactRoute
	 * @desc Get menu itemid filmlist view in default layout
	 * @param boolean $retstr if false return integer id, if true return return string with "&Itemid="
	 * @return string|int|NULL
	 */
	public static function getFilmsCompactRoute($retstr=false) {
	    $items  = self::getItems();
	    foreach ($items as $item) {
	        if ((isset($item->query['view']) && $item->query['view'] === 'filmlist') && ($item->query['layout'] === 'compact')) {
	                return ($retstr)? '&Itemid='.$item->id : $item->id;
	        }
	    }
	    return null;
	}
	
	/**
	 * @name getBooksCompactLink
	 * @desc Get link to compact books view
	 * @return string
	 */
	public static function getBooksCompactLink() {
		$blink = 'index.php?option=com_xbbooks';
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'booklist'
					&& $item->query['layout'] === 'compact' ) {
						return $blink.'&Itemid='.$item->id;
					}
		}
		return $blink.'&view=filmlist&layout=compact';
	}
	
	/**
	 * @name getFilmRoute
	 * @desc returns the itemid for a menu item for film view with id  $fd, if not found returns menu id for a filmlist, if not found null
	 * @param int $fid
	 * @return int|string|NULL
	 */
	public static function getFilmRoute($fid) {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'film' && isset($item->query['id']) && $item->query['id'] == $fid ) {
				return $item->id;
			}
		}
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'filmlist' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id.'&view=film&id='.$fid;
			}
		}
		return null;
	}

	/**
	 * @name getFilmLink
	 * @desc gets a comlete link for a film menu item either dedicated, or filmlist menu or generic
	 * @param int $fid
	 * @return string
	 */
	public static function getFilmLink($fid) {
	    $flink = 'index.php?option=com_xbfilms';
	    $items  = self::getItems();
	    foreach ($items as $item) {
	        if (isset($item->query['view']) && $item->query['view'] === 'film' && isset($item->query['id']) && $item->query['id'] == $fid ) {
	            return $flink.'&Itemid='.$item->id;
	        }
	    }
	    foreach ($items as $item) {
	        if (isset($item->query['view']) && $item->query['view'] === 'filmlist' &&
	            (empty($item->query['layout']) || $item->query['layout'] === 'default')) {
	                return $flink.'&Itemid='.$item->id.'&view=film&id='.$fid;
	            }
	    }
	    return $flink.'&view=film&id='.$fid;
	}
	
	public static function getBlogRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'blog' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
	public static function getReviewsRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'films' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
	public static function getPeopleRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'people' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
	public static function getCharsRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'characters' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
	public static function getCategoriesRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'categories' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
	public static function getTagsRoute() {
		$items  = self::getItems();
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'tags' &&
				(empty($item->query['layout']) || $item->query['layout'] === 'default')) {
					return $item->id;
			}
		}
		return null;
	}
	
}
