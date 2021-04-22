<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/people.php
 * @version 0.2.3f 10th January 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

JFormHelper::loadFieldClass('list');

class JFormFieldActors extends JFormFieldList {
    
    protected $type = 'Actors';
    
    public function getOptions() {
        //this will get a list of people who have the role 'actor'
    	$params = ComponentHelper::getParams('com_xbfilms');
    	$people_sort = $params->get('people_sort');
    	$names = ($people_sort == 0) ? 'CONCAT(firstname, " ", lastname) AS text' : 'CONCAT(lastname, ", ", firstname ) AS text';
    	//poss param to show state " (", state, ")"
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
 
        $query->select('p.id As value')
	        ->select($names)
	        ->from('#__xbpersons AS p')
	        ->join('LEFT','#__xbfilmperson AS f ON f.person_id = p.id')
	        ->where('p.state = 1')
	        ->where('f.role = '.$db->quote('actor'))
			->group('p.id')
        	->order('text');
        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();
        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
