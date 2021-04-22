<?php
/*******
 * @package xbFilms
 * @filesource admin/models/fields/characters.php
 * @version 0.2.3b 1st January 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

JFormHelper::loadFieldClass('list');

class JFormFieldCharacters extends JFormFieldList {
    
    protected $type = 'Characters';
    
    public function getOptions() {
        
    	$params = ComponentHelper::getParams('com_xbfilms');
    	$options = array();
        
        $db = Factory::getDbo();
        $query  = $db->getQuery(true);
        
        $query->select('id As value, name AS text')
	        ->from('#__xbcharacters')
	        ->where('state IN (0,1)')  //exclude trashed and archived
	        ->order('state DESC, text ASC'); //pub published first and unpublished at end
        $db->setQuery($query);
        $alpha = $db->loadObjectList();
        
        $query->clear();
        $query->select('id As value, name AS text')
	        ->from('#__xbcharacters')
	        ->order('created DESC')
	        ->setLimit('3');
        $recent = $db->loadObjectList();
        //add a separator between recent and alpha
        $blank = new stdClass();
        $blank->value = 0;
        $blank->text = '------------';
        $recent[] = $blank;
        
        // Merge any additional options in the XML definition with recent (top 3) and alphabetical list.
        $options = array_merge(parent::getOptions(), $recent, $alpha);
        return $options;
    }
}
