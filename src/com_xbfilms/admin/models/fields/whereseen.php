<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/whereseen.php
 * @version 0.9.10.1 11th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('combo');

class JFormFieldWhereseen extends JFormFieldCombo {
	
	protected $type = 'Whereseen';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT where_seen AS text, where_seen AS value')
		->from('#__xbfilmreviews')
		->where("where_seen<>''")
		->order('where_seen');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
