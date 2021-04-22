<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/studio.php
 * @version 0.1.0 22nd November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('combo');

class JFormFieldStudio extends JFormFieldCombo {
	
	protected $type = 'Studio';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT studio AS text, studio AS value')
		->from('#__xbfilms')
		->where("studio<>''")
		->order('studio');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
