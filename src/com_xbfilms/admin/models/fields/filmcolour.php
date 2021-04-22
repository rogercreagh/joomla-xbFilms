<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/filmcolour.php
 * @version 0.2.1a 30th November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('combo');

class JFormFieldFilmcolour extends JFormFieldCombo {
	
	protected $type = 'Filmcolour';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT filmcolour AS text, filmcolour AS value')
		->from('#__xbfilms')
//		->where("filmcolour<>''")
		->where("filmcolour NOT IN ('','B&W','Colour')")
		->order('filmcolour');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
