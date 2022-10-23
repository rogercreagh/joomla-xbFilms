<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/filmpeople.php
 * @version 0.9.6.f 10th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

class JFormFieldFilmpeople extends JFormFieldList {
    
    protected $type = 'Filmpeople';
    
    /**
     * @desc gets a list of people published and in a film, 
     * sorted by name (first/last as per params)
     * {@inheritDoc}
     * @see JFormFieldList::getOptions()
     */
    public function getOptions() {
        
    	$params = ComponentHelper::getParams('com_xbfilms');
    	$people_sort = $params->get('people_sort');
    	$select = ($people_sort == 0) ? 'CONCAT(firstname, " ", lastname) AS text' : 'CONCAT(lastname, ", ", firstname ) AS text';
    	//poss param to show state " (", state, ")"
    	$options = array();
    	
    	$db = Factory::getDbo();
    	$query  = $db->getQuery(true);
    	
    	$query->select('DISTINCT p.id As value')
    	->select($select)
    	->from('#__xbpersons AS p')
    	->join('LEFT', '#__xbfilmperson AS fp ON fp.person_id = p.id')
    	->where('fp.id IS NOT NULL')
    	->where('state = 1')
    	->order('text');
    	// Get the options.
    	$db->setQuery($query);
    	$options = $db->loadObjectList();
    	// Merge any additional options in the XML definition.
    	$options = array_merge(parent::getOptions(), $options);
    	return $options;
    	
    	
    	
//     	$names = ($people_sort == 0) ? 'firstname, " ", lastname' : 'lastname, ", ", firstname';
        
//         $db = Factory::getDbo();
//         $query  = $db->getQuery(true);       
//         $query->select('DISTINCT a.id As value')
//         	->select('CONCAT('.$names.') AS text')
// 	        ->from('#__xbfilmperson AS fp')
// 	        ->join('LEFT','#__xbpersons AS a ON a.id = fp.person_id')
// 	        ->where('a.state = 1')
// 	        ->order('text ASC');
//         $db->setQuery($query);
//         $options = $db->loadObjectList();
//         // Merge any additional options in the XML definition.
//         $options = array_merge(parent::getOptions(), $options);
//         return $options;
    }
}
