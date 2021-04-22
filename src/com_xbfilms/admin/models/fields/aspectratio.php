<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/aspectratio.php
 * @version 0.3.0 7th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('combo');

class JFormFieldAspectratio extends JFormFieldCombo {
	
	protected $type = 'Aspectratio';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT aspect_ratio AS text, aspect_ratio AS value')
		->from('#__xbfilms')
		->where("aspect_ratio NOT IN ('','Standard','Widescreen')")
		->order('aspect_ratio');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
