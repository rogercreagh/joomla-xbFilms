<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/camera.php
 * @version 0.9.9.7 8th September 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('combo');

class JFormFieldCamera extends JFormFieldCombo {
	
	protected $type = 'Camera';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT cam_format AS text, cam_format AS value')
			->from('#__xbfilms')
			->where("cam_format<>''")
			//->where("cam_format NOT IN ('','B&W','Colour')")
			->order('cam_format');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
