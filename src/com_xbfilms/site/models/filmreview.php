<?php
/*******
 * @package xbFilms
 * @filesource site/models/film.php
 * @version 0.9.11.0 15th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class XbfilmsModelFilmreview extends JModelItem {
		
    public function __construct($config = array()) {
        $showrevs = ComponentHelper::getParams('com_xbfilms')->get('show_revs',1);
        if (!$showrevs) {
            header('Location: index.php?option=com_xbfilms&view=filmlist');
            exit();
        }
        parent::__construct($config);
    }
    
    protected function populateState() {
		$app = Factory::getApplication('site');
		
		// Load state from the request.
		$id = $app->input->getInt('id');
		$this->setState('filmreview.id', $id);
		
		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		
	}
	
	public function getItem($id = null) {
		if (!isset($this->item) ) { //|| !is_null($id)
			$id    = is_null($id) ? $this->getState('filmreview.id') : $id;
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select('a.id AS id, a.title AS title, 
				a.film_id AS film_id, b.poster_img AS poster_img, b.title AS film_title,
				a.rev_date AS rev_date, a.where_seen AS where_seen, a.rating AS rating,
				a.summary AS summary, a.review AS review, a.reviewer AS reviewer, a.subtitled AS subtitled,
				a.state AS published, a.catid AS catid, a.params AS params, a.metadata AS metadata  ');
			$query->from('#__xbfilmreviews AS a');
			$query->select('c.title AS category_title');
			$query->leftJoin('#__categories AS c ON c.id = a.catid');
			$query->join('LEFT', '#__xbfilms AS b ON b.id = a.film_id');
			$query->where('a.id = '.$id);
			$db->setQuery($query);
			
			if ($this->item = $db->loadObject()) {
				
				$item = &$this->item;
				// Load the JSON string
				$params = new Registry;
				$params->loadString($item->params, 'JSON');
				$item->params = $params;
				
				// Merge global params with item params
				$params = clone $this->getState('params');
				$params->merge($item->params);
				$item->params = $params;				
				
				$item->people = XbfilmsGeneral::getFilmPeople($item->film_id);
				//get counts for director,producers,cast,crew,appearances
				$roles = array_column($item->people,'role');
				$item->dircnt = count(array_keys($roles, 'director'));
				
				//make author/editor list
				$item->dirlist = '<i>';
			    if ($item->dircnt == 0){
			        $item->dirlist .= Text::_( 'No Director Listed' ).'</i>';
			    } else {
			        $item->dirlist .= ($item->authcnt>1)?Text::_('XBCULTURE_DIRECTORS'):Text::_('XBCULTURE_DIRECTOR');
			        $item->dirlist .= '</i>: '.XbcultureHelper::makeLinkedNameList($item->people,'director','comma',false);
			    }
				
				//get other reviews
				$item->reviews = XbfilmsGeneral::getFilmReviews($item->film_id);				
			} //end if loadobject			
            return $this->item;			
		} //end if item not set already and we have an id				
	} //end getitem()
	
}