<?php
/*******
 * @package xbFilms
 * @filesource admin/controllers/film.php
 * @version 0.3.0 7th February 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

class XbfilmsControllerFilm extends FormController {
		
	public function __construct($config = array()) {
		parent::__construct($config, $factory);
		$this->registerTask('save2NewPer', 'save');
		$this->registerTask('save2NewChar', 'save');
		$this->registerTask('save2NewRev', 'save');
	}		
		
		protected function postSaveHook(JModelLegacy $model, $validData = array()) {
		
		$task = $this->getTask();
		$item = $model->getItem();
		
		if (isset($item->params) && is_array($item->params)) {
			$registry = new Registry($item->params);
			$item->params = (string) $registry;
		}
		
		if (isset($item->metadata) && is_array($item->metadata)) {
			$registry = new Registry($item->metadata);
			$item->metadata = (string) $registry;
		}

		switch ($task) {
			case 'save2NewPer':
				$redirectTo =('index.php?option=com_xbfilms&task=person.edit&id=0');
				$this->setRedirect(Route::_($redirectTo,false ));
				break;
			case 'save2NewChar':
				$redirectTo =('index.php?option=com_xbfilms&task=character.edit&id=0');
				$this->setRedirect(Route::_($redirectTo,false ));
				break;
			case 'save2NewRev':
				$fid = $validData['id'];
				$redirectTo =('index.php?option=com_xbfilms&task=review.edit&id=0&film_id='.$fid);
				$this->setRedirect(Route::_($redirectTo,false ));
				break;
		}
//			$this->setRedirect(
//					\JRoute::_(
//							'index.php?option=' . $this->option . '&view=' . $this->view_item
//							. $this->getRedirectToItemAppend(null, $urlVar), false
//							)
//					);
			
	}
	
	public function publish() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
	    $model = $this->getModel('film');
        $wynik = $model->publish($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=films');
        $this->setRedirect($redirectTo );
    }
    
    public function unpublish() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('film');
        $wynik = $model->publish($pid,0);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=films');
        $this->setRedirect($redirectTo );
    }
    
    public function archive() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('film');
        $wynik = $model->publish($pid,2);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=films');
        $this->setRedirect($redirectTo);
    }
    
    public function delete() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('film');
        $wynik = $model->delete($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=films');
        $this->setRedirect($redirectTo );
    }
    
    public function trash() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('film');
        $wynik = $model->publish($pid,-2);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=films');
        $this->setRedirect($redirectTo );
    }
      
    public function checkin() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('film');
        $wynik = $model->checkin($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=films');
        $this->setRedirect($redirectTo );
    }
    
    public function batch($model = null)
    {
    	$model = $this->getModel('film');
    	$this->setRedirect((string)Uri::getInstance());
    	return parent::batch($model);
    }
    
}
