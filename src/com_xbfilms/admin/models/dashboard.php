<?php
/*******
 * @package xbFilms
 * @filesource admin/models/dashboard.php
 * @version 0.9.0 7th April 2021
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
    	$result = array('tagcnts' => array('bkcnt' =>0, 'percnt' => 0, 'charcnt' => 0, 'revcnt' => 0), 'tags' => array(), 'taglist' => '' );
    	$db = $this->getDbo();
    	$query =$db->getQuery(true);
    	//first we get the total number of each type of item with one or more tags
    	$query->select('type_alias,core_content_id, COUNT(*) AS numtags')
    	->from('#__contentitem_tag_map')
    	->where('type_alias LIKE '.$db->quote('com_xbfilms%'))
    	->group('core_content_id, type_alias');
    	//not checking that tag is published, not using numtags at this stage - poss in future
    	$db->setQuery($query);
    	$db->execute();
    	$items = $db->loadObjectList();
    	foreach ($items as $it) {
    	    switch ($it->type_alias) {
    	        case 'com_xbfilms.film' :
    	            $result['tagcnts']['bkcnt'] ++;
    	            break;
    	        case 'com_xbpeople.person':
    	            $result['tagcnts']['percnt'] ++;
    	            break;
    	        case 'com_xbpeople.character':
    	            $result['tagcnts']['charcnt'] ++;
    	            break;
    	        case 'com_xbfilms.review':
    	            $result['tagcnts']['revcnt'] ++;
    	            break;
    	    }
    	}
    	//now we get the number of each type of item assigned to each tag
    	$query->clear();
    	$query->select('type_alias,t.id, t.title AS tagname ,COUNT(*) AS tagcnt')
    	->from('#__contentitem_tag_map')
    	->join('LEFT', '#__tags AS t ON t.id = tag_id')
    	->where('type_alias LIKE '.$db->quote('%xbfilms%'))
    	->where('t.published = 1') //only published tags
    	->group('type_alias, tagname');
    	$db->setQuery($query);
    	$db->execute();
    	$tags = $db->loadObjectList();
    	foreach ($tags as $k=>$t) {
    	    if (!key_exists($t->tagname, $result['tags'])) {
    	        $result['tags'][$t->tagname]=array('id' => $t->id, 'tbcnt' =>0, 'tpcnt' => 0, 'tccnt' => 0, 'trcnt' => 0, 'tagcnt'=>0);
    	    }
    	    $result['tags'][$t->tagname]['tagcnt'] += $t->tagcnt;
    	    switch ($t->type_alias) {
    	        case 'com_xbfilms.film' :
    	            $result['tags'][$t->tagname]['tbcnt'] += $t->tagcnt;
    	            break;
    	        case 'com_xbpeople.person':
    	            $result['tags'][$t->tagname]['tpcnt'] += $t->tagcnt;
    	            break;
    	        case 'com_xbpeople.character':
    	            $result['tags'][$t->tagname]['tccnt'] += $t->tagcnt;
    	            break;
    	        case 'com_xbfilms.review':
    	            $result['tags'][$t->tagname]['trcnt'] += $t->tagcnt;
    	            break;
    	    }
    	}
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

/* 	
	public function getOrphanReviewsCnt() {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('COUNT(a.id)')->from('#__xbfilmreviews AS a');
	    $query->join('LEFT','#__xbfilms AS b ON b.id = a.film_id');
	    $query->where('b.id IS NULL');
	    $db->setQuery($query);
	    return $db->loadResult();
	}

	
	public function getOrphanReviews() {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('a.id, a.title')->from('#__xbfilmreviews AS a');
	    $query->join('LEFT','#__xbfilms AS b ON b.id = a.film_id');
	    $query->where('b.id IS NULL');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	}
	
/* 
	public function getOrphanPeopleCnt() {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('COUNT(DISTINCT p.id)')->from('#__xbpersons AS p');
	    $query->join('LEFT','#__xbfilmperson AS bp ON bp.person_id = p.id');
	    $query->where('bp.person_id IS NULL');
	    if ($this->xbbooksStatus) {
	        $query->join('LEFT','#__xbbookperson AS fp ON fp.person_id = p.id');
	        $query->where('fp.person_id IS NULL');
	    }
	    $db->setQuery($query);
	    return $db->loadResult();
	}

	
	public function getOrphanPeople() {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('p.id, CONCAT(p.firstname, " ", p.lastname) AS name')->from('#__xbpersons AS p');
	    $query->join('LEFT','#__xbfilmperson AS bp ON bp.person_id = p.id');
	    $query->where('bp.person_id IS NULL');
	    if ($this->xbbooksStatus) {
	        $query->join('LEFT','#__xbbookperson AS fp ON fp.person_id = p.id');
	        $query->where('fp.person_id IS NULL');
	    }
	    $db->setQuery($query);
	    return $db->loadAssocList();
	}

/* 	
	public function getOrphanCharsCnt() {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('COUNT(DISTINCT p.id)')->from('#__xbcharacters AS p');
	    $query->join('LEFT','#__xbfilmcharacter AS bp ON bp.person_id = p.id');
	    $query->where('bp.person_id IS NULL');
	    if ($this->xbbooksStatus) {
	        $query->join('LEFT','#__xbbookcharacter AS fp ON fp.person_id = p.id');
	        $query->where('fp.person_id IS NULL');
	    }
	    $query->order('name');
	    $db->setQuery($query);
	    return $db->loadResult();
	}
	
	public function getOrphanChars() {
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query->select('p.id, p.name')->from('#__xbcharacters AS p');
	    $query->join('LEFT','#__xbfilmcharacter AS fp ON fp.char_id = p.id');
	    $query->where('fp.char_id IS NULL');
	    if ($this->xbbooksStatus) {
	        $query->join('LEFT','#__xbbookcharacter AS bp ON bp.char_id = p.id');
	        $query->where('bp.char_id IS NULL');
	    }
	    $query->order('name');
	    $db->setQuery($query);
	    return $db->loadAssocList();
	}
 */	
	
}	
