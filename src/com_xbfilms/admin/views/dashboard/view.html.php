<?php
/*******
 * @package xbFilms
 * @filesource admin/views/dashboard/view.html.php
 * @version 0.12.0.1 11th December 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class XbfilmsViewDashboard extends JViewLegacy
{
 //   protected $buttons;
	protected $films; //why?
	protected $categories;
 
	public function display($tpl = null) {
	    $app = Factory::getApplication();
	    $err = $app->input->getString('err'.'');
	    if ($err!='') {
	        $app->enqueueMessage(urldecode($err),'Error');
	    }
	    $this->xbpeople_ok = Factory::getSession()->get('xbpeople_ok');
		$this->xbbooks_ok = Factory::getSession()->get('xbbooks_ok');
		$this->xbevents_ok = Factory::getSession()->get('xbevents_ok');
		
		if ($this->xbpeople_ok) {
			
			$this->filmStates = $this->get('FilmStates');
			$this->catStates = $this->get('CatStates');
			$this->pcatStates = $this->get('PcatStates');
			$this->revStates = $this->get('RevStates');
			$this->perStates = $this->get('PerStates');
			$this->charStates = $this->get('CharStates');
			$this->totPeople = XbcultureHelper::getItemCnt('#__xbpersons');
			$this->totChars = XbcultureHelper::getItemCnt('#__xbcharacters');
			$this->otherRoles = $this->get('OtherRoles');
						
			$this->ratCnts = $this->get('RatCnts');
			$this->films = $this->get('FilmCnts');
			$this->people = $this->get('RoleCnts');
			
			$this->cats = $this->get('Cats');
			$this->pcats = $this->get('PeopleCats');
			$this->tags = $this->get('Tagcnts');
			
			$this->xmldata = Installer::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR . '/xbfilms.xml');
			$this->client = $this->get('Client');
			
			$params = ComponentHelper::getParams('com_xbfilms');
			
			$this->savedata = $params->get('savedata',1);
			
			$this->show_sample = $params->get('show_sample');
			$this->zero_rating = $params->get('zero_rating');
			$this->zero_class = $params->get('zero_class');
			
			$this->show_revs = $params->get('show_revs',1);
			
			$this->show_cat = $params->get('show_cats',1);
			$this->show_filmcat = $params->get('show_fcat',1);
			$this->show_revcat = ($this->show_revs) ? $params->get('show_rcat'):0;
			$this->show_percat = $params->get('show_pcat',1);
			
			$this->show_tags = $params->get('show_tags',1);
			$this->show_filmtags = $params->get('show_ftags',1);
			$this->show_revtags = ($this->show_revs) ? $params->get('show_rtags',1) : 0;
			$this->show_pertags = $params->get('show_ptags');
			
			$this->show_search = $params->get('search_bar');
			
			$this->hide_empty = $params->get('hide_empty');
			
			$this->posters = $params->get('poster_path');
			$this->portraits = $params->get('portrait_path');
			$this->show_filmlist_posters = $params->get('show_fpiccol');
			$this->show_film_poster = $params->get('show_fimage');
			$this->show_review_poster = $params->get('show_rimage');
			
			$this->show_people_portraits = $params->get('show_ppiccol');
			$this->show_person_portrait = $params->get('show_pimage');
			
			$this->show_filmlist_rating = $params->get('show_frevcol');
			$this->show_film_review = $params->get('show_frevs');
			
			XbfilmsHelper::addSubmenu('dashboard');
			
	        // Check for errors.
	        if (count($errors = $this->get('Errors'))) {
	            throw new Exception(implode("\n", $errors), 500);
	        }
	
	        $clink='index.php?option=com_xbfilms&view=fcategory&id=';
	        $this->catlist = '<ul style="list-style-type: none;">';
	        foreach ($this->cats as $key=>$value) {
	        	if ($value['level']==1) {
	        		$this->catlist .= '<li>';
	        	} else {
	        		$this->catlist .= str_repeat('-&nbsp;', $value['level']-1);
	        	}
	        	$lbl = $value['published']==1 ? 'label-success' : '';
	        	$this->catlist .='<a class="label label-success" href="'.$clink.$value['id'].'">'.$value['title'].'</a>&nbsp;(<i>'.$value['filmcnt'].':'.$value['revcnt'].'</i>) ';
	        	if ($value['level']==1) {
	        		$this->catlist .= '</li>';
	        	}
	        }
	        $this->catlist .= '</ul>'; 
	        
	        $this->pcatlist = '<ul style="list-style-type: none;">';
	        foreach ($this->pcats as $key=>$value) {
	            if ($value['level']==1) {
	                $this->pcatlist .= '<li>';
	            } else {
	                $this->pcatlist .= str_repeat('-&nbsp;', $value['level']-1);
	            }
	            $lbl = $value['published']==1 ? 'label-success' : '';
	            $this->pcatlist .='<a class="label label-success" href="'.$clink.$value['id'].'">'.$value['title'].'</a>&nbsp;(<i>'.$value['percnt'].':'.$value['chrcnt'].'</i>) ';
	            if ($value['level']==1) {
	                $this->pcatlist .= '</li>';
	            }
	        }
	        $this->pcatlist .= '</ul>';
	        
// 	        $tlink='index.php?option=com_xbfilms&view=tag&id=';
// 	        $this->taglist = '<ul class="inline">';
// 	        foreach ($this->tags['tags'] as $key=>$value) {
// 	        	//       	$result[$key] = $t->tagcnt;
// 	            $this->taglist .= '<li><a class="label label-info" href="'.$tlink.$value['id'].'">'.$key.'</a>&nbsp;(<i>'.$value['tbcnt'].':'.$value['trcnt'].':'.$value['tpcnt'].':'.$value['tccnt'].')</i></li> ';
// 	        }
// 	        $this->taglist .= '</ul>';
	        //        $result['taglist'] = trim($result['taglist'],', ');
	        
	        $this->sidebar = JHtmlSidebar::render();
		}
		
        $this->addToolbar();
        parent::display($tpl);
        // Set the document
        $this->setDocument();
	}

    protected function addToolbar() {
        $canDo = XbfilmsHelper::getActions();
        
        
        if ($this->xbpeople_ok) {
            ToolbarHelper::title(Text::_( 'XBCULTURE_XBFILMS' ).': '.Text::_('XBCULTURE_DASHBOARD'),'info-2');
            $samplesexist = XbcultureHelper::getIdFromAlias('#__categories', 'sample-films','com_xbfilms');
	        if ($this->show_sample==1) {
	        	if ($samplesexist > 0) {
	        		ToolbarHelper::custom('dashboard.unsample', 'file-minus', '', 'XBCULTURE_REMOVE_SAMPLE', false) ;
	        	} else {
	        		ToolbarHelper::custom('dashboard.sample', 'file-plus', '', 'XBCULTURE_INSTALL_SAMPLE', false) ;
	        	}
		        ToolbarHelper::custom(); //spacer
	        }
	        ToolbarHelper::custom('dashboard.people', 'info-2', '', 'xbPeople', false) ;
	        
	        ToolbarHelper::custom('dashboard.books', 'book', '', 'xbBooks', false) ;
	        ToolbarHelper::custom('dashboard.live', 'music', '', 'xbEvents', false) ;
	        if ($canDo->get('core.admin')) {
	            ToolbarHelper::preferences('com_xbfilms');
	        }
	        ToolbarHelper::help( '', false,'https://crosborne.uk/xbfilms/doc?tmpl=component#admin-panel' );
        } else {
            ToolbarHelper::title('xbFilms - please install xbPeople to proceed','info-2');
            ToolBarHelper::help( '', false,'https://www.crosborne.uk/downloads/file/11-xbpeople-component?tmpl=component' );
        }
    }
    
    protected function setDocument() {
    	$document = Factory::getDocument();
    	$document->setTitle(Text::_('XBFILMS_ADMIN_DASHBOARD'));
    }
    
}
