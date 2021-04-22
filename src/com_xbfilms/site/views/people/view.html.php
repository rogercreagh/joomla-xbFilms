<?php 
/*******
 * @package xbFilms
 * @filesource site/views/people/view.html.php
 * @version 0.5.4 18th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class XbfilmsViewPeople extends JViewLegacy {
	
	public function display($tpl = null) {
		$this->items 		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->params      = $this->state->get('params');
		$this->filterForm    	= $this->get('FilterForm');
		$this->activeFilters 	= $this->get('ActiveFilters');
		$this->searchTitle = $this->state->get('filter.search');
		
		$this->header = array();
		$this->header['showheading'] = $this->params->get('show_page_heading',0,'int');
		$this->header['heading'] = $this->params->get('page_heading','','text');
		if ($this->header['heading'] =='') {
			$this->header['heading'] = $this->params->get('page_title','','text');
		}
		$this->header['title'] = $this->params->get('list_title','','text');
		$this->header['subtitle'] = $this->params->get('list_subtitle','','text');
		$this->header['text'] = $this->params->get('list_headtext','','text');
		
		$this->search_bar = $this->params->get('search_bar','','int');
		$this->hide_film = $this->params->get('menu_film',0)>0 ? true : false;
		$this->hide_cat = $this->params->get('menu_category_id',0)>0 ? true : false;
		$this->hide_prole = $this->params->get('menu_prole',0)>0 ? true : false;
		$this->hide_tag = (!empty($this->params->get('menu_tag',''))) ? true : false;
		
		$this->xbpeople_ok = Factory::getSession()->get('xbpeople_ok');
		$show_cats = ($this->xbpeople_ok) ? $this->params->get('show_cats','1','int') : 0;
		$this->show_cat = ($show_cats) ? $this->params->get('show_pcat','2','int') :0;
		$show_tags = $this->params->get('show_tags','1','int');
		$this->show_tags = ($show_tags) ? $this->params->get('show_ptags','1','int') : 0;
		
		
		$this->show_pic = $this->params->get('show_ppiccol','1','int');
		$this->show_pdates = $this->params->get('show_pdates','1');
		$this->show_sum = $this->params->get('show_psumcol','1','int');
		$this->show_films = $this->params->get('show_films','1');
		$this->show_cfilms = $this->params->get('show_cfilms','1');
		//NB for compact list only option 3 (linked list) is not available and shows as popup list
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		//set metadata
		$document=$this->document;
		$document->setMetaData('title', JText::_('People catalogue').': '.$document->title);
		$metadesc = $this->params->get('menu-meta_description');
		if (!empty($metadesc)) { $document->setDescription($metadesc); }
		$metakey = $this->params->get('menu-meta_keywords');
		if (!empty($metakey)) { $document->setMetaData('keywords', $metakey);}
		$metarobots = $this->params->get('robots');
		if (!empty($metarobots)) { $document->setMetaData('robots', $metarobots);}
		$document->setMetaData('generator', $this->params->get('def_generator'));
		$metaauthor = $this->params->get('def_author');
		if (!empty($metaauthor)) { $document->setMetaData('author', $metadata['author']);}
		
		parent::display($tpl);
	} // end function display()
	
	
}
