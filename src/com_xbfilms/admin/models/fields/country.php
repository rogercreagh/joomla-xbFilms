<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/country.php
 * @version 0.2.3b 1st January 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

JFormHelper::loadFieldClass('combo');

class JFormFieldCountry extends JFormFieldCombo {
	
	protected $type = 'Country';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT country AS text, country AS value')
		->from('#__xbfilms')
		->where("country<>''")
		->order('country');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
