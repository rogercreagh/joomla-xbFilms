<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/filmsound.php
 * @version 0.9.6.f 10th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('combo');

class JFormFieldFilmsound extends JFormFieldCombo {
	
	protected $type = 'Filmsound';
	
	public function getOptions() {
		
		$options = array();
		
		$db = Factory::getDbo();
		$query  = $db->getQuery(true);
		
		$query->select('DISTINCT filmsound AS text, filmsound AS value')
		->from('#__xbfilms')
		//		->where("filmsound<>''")
		->where("filmsound NOT IN ('','Silent','Talkie')")
		->order('filmsound');
		
		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
