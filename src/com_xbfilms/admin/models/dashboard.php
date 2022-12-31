<?php
/*******
 * @package xbFilms
 * @filesource admin/models/dashboard.php
 * @version 0.9.9.8 25th October 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
//use Joomla\CMS\Table\Observer\Tags;

class XbfilmsModelDashboard extends JModelList {
    
    protected $xbbooksStatus;
    
    public function __construct() {
        //$this->xbbooksStatus = XbcultureHelper::checkComponent('com_xbbooks');
        $this->xbbooksStatus = Factory::getSession()->get('xbbooks_ok',false);
        parent::__construct();
    }
    
    public function getFilmStates() {
    	return $this->stateCnts('#__xbfilms');
    }
    
    public function getCatStates() {
    	return $this->stateCnts('#__categories','published','com_xbfilms');
    }
    
    public function getPcatStates() {
     	return $this->stateCnts('#__categories','published','com_xbpeople');
    }
        
    public function getRevStates() {
    	return $this->stateCnts('#__xbfilmreviews');
    }
    
    public function getPerStates() {
     	return $this->stateCnts('#__xbpersons');
    }
    
    public function getCharStates() {
    	return $this->stateCnts('#__xbcharacters');
    }
    
    public function getFilmCnts() {
    	$films = array();
    	$db = $this->getDbo();
    	
    	$query =$db->getQuery(true);
    	$query->select('COUNT(DISTINCT film_id)')->from('#__xbfilmreviews');
    	$db->setQuery($query);
    	$films['reviewed'] = $db->loadResult();
    	
    	return $films;
    }
        
    public function getCats() {
    	$db = $this->getDbo();
    	$query = $db->getQuery(true);
    	$query->select('a.*')
    	->select('(SELECT COUNT(*) FROM #__xbfilms AS b WHERE b.catid=a.id) AS filmcnt')
    	->select('(SELECT COUNT(*) FROM #__xbfilmreviews AS r WHERE r.catid=a.id) AS revcnt')
    	->from('#__categories AS a')
    	->where('a.extension = '.$db->quote("com_xbfilms"))
    	->order($db->quoteName('path') . ' ASC');
    	$db->setQuery($query);
    	return $db->loadAssocList('alias');    	
    }
    
    public function getPeopleCats() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('a.*')
        ->select('(SELECT COUNT(*) FROM #__xbcharacters AS c WHERE c.catid=a.id) AS chrcnt')
        ->select('(SELECT COUNT(*) FROM #__xbpersons AS p WHERE p.catid=a.id) AS percnt')
        ->from('#__categories AS a')
        ->where('a.extension = '.$db->quote("com_xbpeople"))
        ->order($db->quoteName('path') . ' ASC');
        $db->setQuery($query);
        return $db->loadAssocList('alias');
    }
    
    public function getRoleCnts() {
        
        $result = array();
        
        $wynik = $this->roleCnts('director');
        $result['dirpub'] = $wynik['pub'];
        $result['dirunpub'] = $wynik['unpub'];
        
        $wynik = $this->roleCnts('producer');
        $result['prodpub'] = $wynik['pub'];
        $result['produnpub'] = $wynik['unpub'];
        
        $wynik = $this->roleCnts('crew');
        $result['crewpub'] = $wynik['pub'];
        $result['crewunpub'] = $wynik['unpub'];
        
        $wynik = $this->roleCnts('actor');
        $result['castpub'] = $wynik['pub'];
        $result['castunpub'] = $wynik['unpub'];
        
        $wynik = $this->roleCnts('appearsin');
        $result['apppub'] = $wynik['pub'];
        $result['appunpub'] = $wynik['unpub'];
        
        $wynik = $this->roleCnts('other_roles');
        $result['otherpub'] = $wynik['pub'];
        $result['otherunpub'] = $wynik['unpub'];        
        
        return $result;
    }
    
    public function getRatCnts() {
    	$db = $this->getDbo();
    	$query =$db->getQuery(true);
        $query->select('rating');
        $query->from('#__xbfilmreviews');
        $query->where('state = 1');
        $db->setQuery($query);
		return array_count_values($db->loadColumn());
    }
    
    public function getClient() {
    	$result = array();
    	$client = Factory::getApplication()->client;
    	$class = new ReflectionClass('Joomla\Application\Web\WebClient');
    	$constants = array_flip($class->getConstants());
    	
    	$result['browser'] = $constants[$client->browser].' '.$client->browserVersion;
    	$result['platform'] = $constants[$client->platform].($client->mobile ? ' (mobile)' : '');
    	$result['mobile'] = $client->mobile;
    	return $result;   	
    }
    
    public function getTagcnts() {
        //we need number of films tagged, number of reviews tagged, number of tags used for films, number of tags used for reviews
        // people tagged, chars tagged, people tags, char tags
        $result = array('filmscnt' => 0, 'revscnt' =>0, 'filmtags' => 0, 'revtags' => 0,
            'filmper' => 0, 'filmchar' => 0, 'filmpertags' => 0, 'filmchartags' => 0 );
        
        $result['filmscnt'] = XbcultureHelper::getTagtypeItemCnt('com_xbfilms.film','');
        $result['revscnt'] = XbcultureHelper::getTagtypeItemCnt('com_xbfilms.review','');
        $result['filmtags']= XbcultureHelper::getTagtypeTagCnt('com_xbfilms.film','');
        $result['revtags']= XbcultureHelper::getTagtypeTagCnt('com_xbfilms.review','');
        $result['filmper'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.person','film');
        $result['filmchar'] = XbcultureHelper::getTagtypeItemCnt('com_xbpeople.character','film');
        $result['filmpertags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.person','film');
        $result['filmchartags']= XbcultureHelper::getTagtypeTagCnt('com_xbpeople.character','film');
        return $result;
        
    }
    
    /**
     * @name getOtherRoles()
     * @desc get an array of other roles in filmperson table
     * @return array
     */
    public function getOtherRoles() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT role_note')->from($db->quoteName('#__xbfilmperson'))->where('role = '.$db->quote('other'));
        $db->setQuery($query);
        $res=array();
        try {
            $res = $db->loadColumn();
        } catch (Exception $e) {
            $dberr = $e->getMessage();
            Factory::getApplication()->enqueueMessage($dberr.'<br />Query: '.$query, 'error');
        }
        return $res;
    }
    
    private function stateCnts(string $table, string $colname = 'state', string $ext='com_xbfilms') {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('DISTINCT a.'.$colname.', a.alias')
        ->from($db->quoteName($table).' AS a');
        if ($table == '#__categories') {
            $query->where('extension = '.$db->quote($ext));
        }
        if ($table == '#__xbpersons') {
            $query->join('LEFT','#__xbfilmperson AS fp ON fp.person_id = a.id')->where('fp.id IS NOT NULL');
        }
        if ($table == '#__xbcharacters') {
            $query->join('LEFT','#__xbfilmcharacter AS fc ON fc.char_id = a.id')->where('fc.id IS NOT NULL');
        }
        $db->setQuery($query);
        $col = $db->loadColumn();
        $vals = array_count_values($col);
        $result['total'] = count($col);
        $result['published'] = key_exists('1',$vals) ? $vals['1'] : 0;
        $result['unpublished'] = key_exists('0',$vals) ? $vals['0'] : 0;
        $result['archived'] = key_exists('2',$vals) ? $vals['2'] : 0;
        $result['trashed'] = key_exists('-2',$vals) ? $vals['-2'] : 0;
        return $result;
    }
	
	private function roleCnts($role='') {
		$wynik = array();
		$db = $this->getDbo();
		$exclude = "('director','producer','crew','actor','appearsin')";
		
		$query = $db->getQuery(true);
		$query->select('COUNT(DISTINCT a.id) AS pcnt')
		->from($db->quoteName('#__xbpersons','a'));
		if (!empty($role)) {
			if ($role == 'other_roles') {
				$query->join('LEFT',$db->quoteName('#__xbfilmperson','b').' ON '.$db->quoteName('b.person_id').' = '.$db->quoteName('a.id'))
				->where('b.role NOT IN '.$exclude);
				
			} else {
				$query->join('LEFT',$db->quoteName('#__xbfilmperson','b').' ON '.$db->quoteName('b.person_id').' = '.$db->quoteName('a.id'))
				->where('b.role='.$db->quote($role));
			}
		}
		$query->where('a.state=1');
		$db->setQuery($query);
		$db->execute();
		$wynik['pub'] = $db->loadResult();
		
		$query = $db->getQuery(true);
		$query->select('COUNT(DISTINCT a.id) AS pcnt')
		->from('#__xbpersons AS a');
		if (!empty($role)) {
			if ($role == 'other_roles') {
				$query->join('LEFT',$db->quoteName('#__xbfilmperson','b').' ON '.$db->quoteName('b.person_id').' = '.$db->quoteName('a.id'))
				->where('b.role NOT IN '.$exclude);
				
			} else {
				$query->leftJoin('#__xbfilmperson AS b ON a.id = b.person_id')
				->where('b.role='.$db->quote($role));
			}
		}
		$query->where('a.state!=1'); //this will include archived and trashed
		$db->setQuery($query);
		$db->execute();
		$wynik['unpub'] = $db->loadResult();
		
		return $wynik;
	}
	
}	
