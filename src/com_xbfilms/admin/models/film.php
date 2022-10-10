<?php
/*******
 * @package xbFilms
 * @filesource admin/models/film.php
 * @version 0.9.9.8 10th October 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;

class XbfilmsModelFilm extends JModelAdmin {
    
	public $typeAlias = 'com_xbfilms.film';
	
	public function getItem($pk = null) {
		
		$item = parent::getItem($pk);
		
		if (!empty($item->id)) {
			// Convert the metadata field to an array.
			$registry = new Registry($item->metadata);
			$item->metadata = $registry->toArray();
			
			$tagsHelper = new TagsHelper;
			$item->tags = $tagsHelper->getTagIds($item->id, 'com_xbfilms.film');
			
			//get last rating
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('fr.rating AS rate, fr.rev_date AS seen')->from('#__xbfilmreviews AS fr')
			 ->where('fr.film_id='.$db->quote($item->id))->order('seen DESC');
			$db->setQuery($query);
			$item->lastrat = $db->loadAssoc();
			if ((!empty($item->lastrat)) && (empty($item->last_seen))) {
			    $item->last_seen = $item->lastrat['seen'];
			}
		}	
		return $item;
	}
		
	public function getTable($type = 'Film', $prefix = 'XbfilmsTable', $config = array()) {

        return Table::getInstance($type, $prefix, $config);
    }
    
    public function getForm($data = array(), $loadData = true) {

        $form = $this->loadForm( 'com_xbfilms.film', 'film',
            array(
                'control' => 'jform',
                'load_data' => $loadData
            )
        );
        
        if (empty($form)) {
            return false;
        }
        
        $params = ComponentHelper::getParams('com_xbfilms');
        $poster_path = $params->get('poster_path','');
        if ($poster_path != '') { 
        	$form->setFieldAttribute('poster_img','directory',$poster_path);
        }
        return $form;
    }
    
    protected function loadFormData() {
        $data = Factory::getApplication()->getUserState('com_xbfilms.edit.film.data', array() );
        
        if (empty($data)) {
            $data = $this->getItem();          
	        $data->directorlist=$this->getFilmPeoplelist('director');
	        $data->producerlist=$this->getFilmPeoplelist('producer');
	        $data->crewlist=$this->getFilmPeoplelist('crew');
	        $data->subjectlist=$this->getFilmPeoplelist('appearsin');
	        $castlist=$this->getFilmPeoplelist('actor');
	        //rolenote may contain id of character check against charperson table
	        $data->castlist=$castlist;
	        $data->charlist=$this->getFilmCharlist();
        }
             
        return $data;
    }
    
    protected function prepareTable($table) {
        $date = Factory::getDate();
        $user = Factory::getUser();
        $db = $this->getDbo();
        
        $table->title = htmlspecialchars_decode($table->title, ENT_QUOTES);
        $table->subtitle = htmlspecialchars_decode($table->subtitle, ENT_QUOTES);
        $table->alias = ApplicationHelper::stringURLSafe($table->alias);
        
        if (empty($table->alias)) {
            $table->alias = ApplicationHelper::stringURLSafe($table->title);
        }
        // Set the values
//         if (empty($table->acq_date)) {
//             //if there are reviews set acq_date to the latest seen date
//             if (!empty($table->last_seen)) {
//                 $table->acq_date = $table->last_seen;
//             } else {
//                 //default to today
//                 $table->acq_date = $date->toSql();
//             }
//         }
//         if (empty($table->last_seen)) {
//             //if there are reviews do we want to force a seen date??? - this will, perhaps make an option
//             if ($table->id>0) { //we must have already saved and have an id
//                 $query=$db->getQuery(true);
//                 $query->select('COUNT(r.id) as revcnt, MAX(r.rev_date) as lastrev')->from('#__xbfilmreviews AS r')
//                 ->where('r.film_id = '.$table->id);
//                 $db->setQuery($query);
//                 $revs=$db->loadAssoc();
//                 if ($revs['revcnt']>0) {
//                     $table->last_seen = $revs['lastrev'];
//                 }
//             }
//         }
        if (empty($table->created)) {
            $table->created = $date->toSql();
        }
        if (empty($table->created_by)) {
        	$table->created_by = Factory::getUser()->id;
        }
        if (empty($table->created_by_alias)) {           
        	$table->created_by_alias = Factory::getUser()->username; //make it an option to use name instead of username
        }
        if (empty($table->id)) {
            
            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $query = $db->getQuery(true)
                ->select('MAX(ordering)')
                ->from($db->quoteName('#__xbfilms'));
                
                $db->setQuery($query);
                $max = $db->loadResult();
                
                $table->ordering = $max + 1;
            }
        } else {
            // not new so set the modified by
            $table->modified    = $date->toSql();
            $table->modified_by = $user->id;
        }
    }
    
    public function publish(&$pks, $value = 1) {
        if (!empty($pks)) {
            foreach ($pks as $item) {
                $db = $this->getDbo();
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__xbfilms'))
                    ->set('state = ' . (int) $value)
                    ->where('id='.$item);
                $db->setQuery($query);
                if (!($db->execute())) {
                    $this->setError($db->getErrorMsg());
                    return false;
                }
            }
            return true;
        }
    }
        
    public function delete(&$pks, $value = 1) {
        if (!empty($pks)) {
            $cnt = 0;
            $table = $this->getTable('film');
            foreach ($pks as $i=>$item) {
                $table->load($item);
                if (!$table->delete($item)) {
                	$filmword = ($cnt == 1)?  JText::_('XBCULTURE_FILM') : JText::_('XBCULTURE_FILMS');
                    Factory::getApplication()->enqueueMessage($cnt.$filmword.' deleted');
                    $this->setError($table->getError());
                    return false;
                }
                $table->reset();
                $cnt++;
            }
            $filmword = ($cnt == 1)? JText::_('XBCULTURE_FILM') : JText::_('XBCULTURE_FILMS');
            Factory::getApplication()->enqueueMessage($cnt.$filmword.' deleted');
            return true;
        }
    }
    
    public function getFilmPeoplelist($role) {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('a.id as person_id, ba.role_note AS role_note');
            $query->from('#__xbfilmperson AS ba');
            $query->innerjoin('#__xbpersons AS a ON ba.person_id = a.id');
            $query->where('ba.film_id = '.(int) $this->getItem()->id);
            $query->where('ba.role = "'.$role.'"');
            $query->order('ba.listorder ASC');
            $db->setQuery($query);
            return $db->loadAssocList();
    }
    
    public function getFilmCharlist() {
            $db = $this->getDbo();
            $query = $db->getQuery(true);
            $query->select('ba.char_id as char_id, ba.actor_id AS actor_id, ba.char_note AS char_note');
            $query->from('#__xbfilmcharacter AS ba');
            $query->innerjoin('#__xbcharacters AS a ON ba.char_id = a.id');
            $query->where('ba.film_id = '.(int) $this->getItem()->id);
            $query->order('ba.listorder ASC');
            $db->setQuery($query);
            return $db->loadAssocList();
    }
    
    public function save($data) {
        $input = Factory::getApplication()->input;
        
        if ($input->get('task') == 'save2copy') {
            $origTable = clone $this->getTable();
            $origTable->load($input->getInt('id'));
            
            if ($data['title'] == $origTable->title) {
                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            } else {
                if ($data['alias'] == $origTable->alias) {
                    $data['alias'] = '';
                }
            }
            // standard Joomla practice is to set the new copy record as unpublished
            $data['published'] = 0;
        }
        // allow nulls for year (therwise empty value defaults to 0)
//        if ($data['rel_year']=='') { $data['rel_year'] = NULL; }
        if (parent::save($data)) {
        	$fid = $this->getState('film.id');
        	// set nulls for empty year and last_read (otherwise empty value defaults to 0000-00-00 00:00:00 which is invalid in latest myql strict mode)
        	if (($data['last_seen']=='') || ($data['rel_year']=='')){
        	    $db = $this->getDbo();
        	    $query= $db->getQuery(true);
        	    $query = 'UPDATE `#__xbfilms`  AS a SET ';
        	    $query .= ($data['rel_year']=='') ? '`rel_year` = NULL ' : '';
        	    $query .= (($data['last_seen']=='') && ($data['rel_year']=='')) ? ',' : '';
        	    $query .= ($data['last_seen']=='')? '`last_seen` =  NULL ' : '';
        	    $query .= 'WHERE a.id  ='.$fid.' ';
        	    $db->setQuery($query);
        	    $db->execute();
        	}
        	$this->storeFilmPersons($fid,'director', $data['directorlist']);
        	$this->storeFilmPersons($fid,'producer', $data['producerlist']);
        	$this->storeFilmPersons($fid,'crew', $data['crewlist']);
        	$this->storeFilmPersons($fid,'actor', $data['castlist']);
        	$this->storeFilmPersons($fid,'appearsin', $data['subjectlist']);
        	$this->storeFilmChars($fid, $data['charlist']);
        	
        	if ($data['quick_rating'] !='')  {
        		$params = ComponentHelper::getParams('com_xbfilms');
        		$date = Factory::getDate();
        	    $db = $this->getDbo();
        	    //need to create a title (unique from rev cnt), alias, filmid, catid (uncategorised), reviewer
        	    $query= $db->getQuery(true);
        	    $query->select('COUNT(r.id) as revcnt')->from('#__xbfilmreviews AS r')
        	    ->where('r.film_id = '.$fid);
        	    $db->setQuery($query);
        	    $revs=$db->loadResult()+1;
        	    $revs = $revs==0 ? '' : ' ('.($revs+1).')';        	    
        	    $rtitle = 'Rating "'.$data['title'].'"';
        	    $ralias = OutputFilter::stringURLSafe($rtitle.'-'.$revs);
        	    $reviewer = Factory::getUser()->name;
        	    if ($params->get('def_new_revcat')>0) {
        	    	$catid=$params->get('def_new_revcat');
        	    } else {
        	    	$catid = XbfilmsHelper::getIdFromAlias('#__categories', 'uncategorised');
        	    }
        	    $qry = 'INSERT INTO '.$db->quoteName('#__xbfilmreviews').' (title, alias, film_id, catid, reviewer, rating, rev_date, created, created_by, state ) ';
        	    $qry .= 'VALUES ('.$db->quote($rtitle).','.$db->quote($ralias).','.$fid.','.$catid.','.$db->quote($reviewer).','.
          	    $data['quick_rating'].','.$db->quote($data['last_seen']).','.$db->quote($date->toSql()).','.$db->quote($data['created_by']).',1)';
        	    $db->setQuery($qry);
        	    $db->execute();
        	}        	       	
        	return true;
        }
        
        return false;
    }
     
    function storeFilmPersons($film_id, $role, $personList) {
        //delete existing role list
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->quoteName('#__xbfilmperson'));
        $query->where('film_id = '.$film_id.' AND role = "'.$role.'"');
        $db->setQuery($query);
        $db->execute();
        //restore the new list
        $listorder=0;
         foreach ($personList as $pers) {
             if ($pers['person_id'] > 0) {
             	$listorder ++;
                $query = $db->getQuery(true);
                $query->insert($db->quoteName('#__xbfilmperson'));
                $query->columns('film_id,person_id,role,role_note,listorder');
                $query->values('"'.$film_id.'","'.$pers['person_id'].'","'.$role.'","'.$pers['role_note'].'","'.$listorder.'"');
                //try
                $db->setQuery($query);
                $db->execute();        
             } else {
             	// Factory::getApplication()->enqueueMessage('<pre>'.print_r($pers,true).'</pre>');
                 //create person
                 //add filmperson with new id
             }
        }
    }

    function storeFilmChars($film_id, $charList) {
        //delete existing char list
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->delete($db->quoteName('#__xbfilmcharacter'));
        $query->where('film_id = '.$film_id);
        $db->setQuery($query);
        $db->execute();
        //restore the new list
        foreach ($charList as $pers) {
            if ($pers['char_id'] > 0) {
                $query = $db->getQuery(true);
                $query->insert($db->quoteName('#__xbfilmcharacter'));
                $query->columns('film_id,char_id,actor_id,char_note,listorder');
                $query->values($db->quote($film_id).','.$db->quote($pers['char_id']).','.$db->quote($pers['actor_id']).','.$db->quote($pers['char_note']).','.$db->quote($pers['listorder']));
                $db->setQuery($query);
                $db->execute();
            }
        }
    }
}
    