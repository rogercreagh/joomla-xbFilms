<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/revyear.php
 * @version 0.9.5.1 10th May 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

class JFormFieldFilmyear extends JFormFieldList {
	
	protected $type = 'Filmyear';

	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT rel_year AS text, rel_year AS value')
		->from('#__xbfilms')
		->order('rel_year DESC');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}

	
