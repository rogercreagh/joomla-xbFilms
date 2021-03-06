<?php
/*******
 * @package xbFilms
 * @filesource admin/controlers/review.php
 * @version 0.9.8.3 25th May 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

class XbfilmsControllerReview extends JControllerForm {
	
	public function __construct($config = array()) {
		
		$app = Factory::getApplication();
		$fid =  $app->input->get->get('film_id');
		$app->setUserState('fid', $fid);		
		parent::__construct($config);
		$this->registerTask('saveback', 'save');
		
	}	
 
	protected function postSaveHook(JModelLegacy $model, $validData = array()) {

	    $item = $model->getItem();		
		if (isset($item->params) && is_array($item->params)) {
			$registry = new Registry($item->params);
			$item->params = (string) $registry;
		}
		
		if (isset($item->metadata) && is_array($item->metadata)) {
			$registry = new Registry($item->metadata);
			$item->metadata = (string) $registry;
		}

	    $task = $this->getTask();
		switch ($task) {
		    case 'saveback':
		        $fid = $validData['film_id'];
		        $redirectTo =('index.php?option=com_xbfilms&task=film.edit&id='.$fid);
		        $this->setRedirect(Route::_($redirectTo,false ));
		        break;
		}
		
	}
	
	public function publish() {
		$jip =  Factory::getApplication()->input;
		$pid =  $jip->get('cid');
		$model = $this->getModel('review');
        $wynik = $model->publish($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=reviews');
        $this->setRedirect($redirectTo, $msg );
    }
    
    public function unpublish() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('review');
        $wynik = $model->publish($pid,0);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=reviews');
        $this->setRedirect($redirectTo, $msg );
    }
    
    public function archive() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('review');
        $wynik = $model->publish($pid,2);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=reviews');
        $this->setRedirect($redirectTo, '' );
    }
    
    public function delete() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('review');
        $wynik = $model->delete($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=reviews');
        $this->setRedirect($redirectTo, '' );
    }
    
    public function trash() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('review');
        $wynik = $model->publish($pid,-2);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=reviews');
        $this->setRedirect($redirectTo, '' );
    }
    
    
    public function checkin() {
    	$jip =  Factory::getApplication()->input;
    	$pid =  $jip->get('cid');
    	$model = $this->getModel('review');
        $wynik = $model->checkin($pid);
        $redirectTo =('index.php?option='.$jip->get('option').'&task=display&view=reviews');
        $this->setRedirect($redirectTo, '' );
    }
    
    public function batch($model = null)
    {
    	$model = $this->getModel('review');
    	$this->setRedirect((string)Uri::getInstance());
    	return parent::batch($model);
    }
    
    
}