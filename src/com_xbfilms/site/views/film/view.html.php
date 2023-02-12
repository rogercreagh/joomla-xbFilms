<?php 
/*******
 * @package xbFilms
 * @filesource site/views/film/view.html.php
 * @version 1.0.3.8 12th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;

class XbfilmsViewFilm extends JViewLegacy {
	
	protected $item;
	
	public function display($tpl = null) {
		$this->item 		= $this->get('Item');
		$this->state		= $this->get('State');
		$this->params      = $this->state->get('params');

		$this->hide_empty = $this->params->get('hide_empty',1);
		$this->show_image = $this->params->get('show_cimage',1);
		
		$show_cats = $this->params->get('show_cats','1','int');
		$this->show_fcat = ($show_cats) ? $this->params->get('show_fcat','2','int') :0;
		$show_tags = $this->params->get('show_tags','1','int');
		$this->show_ftags = ($show_tags) ? $this->params->get('show_ftags','1','int') : 0;
		$this->show_rcat = ($show_cats) ? $this->params->get('show_rcat','1','int') :0;
		$this->show_rtags = ($show_tags) ? $this->params->get('show_rtags','1','int') :0;

		$this->show_fdates = $this->params->get('show_fdates','1','int');
		
		$show_revs = $this->params->get('show_revs','1','int');		
		$this->show_frevs = $show_revs ? $this->params->get('show_frevs',1) : 0;
		
// 		$this->zero_rating = $this->params->get('zero_rating',1);
// 		$this->zero_class = $this->params->get('zero_class','fas fa-thumbs-down xbred');
// 		$this->star_class = $this->params->get('star_class','fa fa-star xbgold');
// 		$this->halfstar_class = $this->params->get('halfstar_class','fa fa-star-half xbgold');
		
		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}

		//the userstate films.sortorder will be updated by whatever list (films or category) was last viewed
		//if we have arrived here directly then we probably ought to load a default sort to determine prev/next
		//we also need to determine where we need to go back to (catlist of allfilmslist)
		$app = Factory::getApplication();
		$this->tmpl = $app->input->getCmd('tmpl');
		$srt = $app->getUserState('films.sortorder');
		if (!empty($srt)) {
	      	$i = array_search($this->item->id, $srt);
	      	if ($i<count($srt)-1) {
	      		$this->item->next = $srt[$i+1];		    
			} else { $this->item->next = 0; }
			if ($i>0) {
				$this->item->prev = $srt[$i-1];
			} else { $this->item->prev = 0; }
	       
	      } else {
			$this->item->prev = 0;
			$this->item->next = 0; 
	      }
		//TODO now test pagination for next page 
		
		$tagsHelper = new TagsHelper;
		$this->item->tags = $tagsHelper->getItemTags('com_xbfilms.film' , $this->item->id);
		
		$document = $this->document; //Factory::getDocument();
		$document->setTitle($this->item->title);
		$document->setMetaData('title', JText::_('XBCULTURE_DETAILS_OF').' '.$this->item->title);
		$metadata = json_decode($this->item->metadata,true);
		if (!empty($metadata['metadesc'])) { $document->setDescription($metadata['metadesc']); }
		if (!empty($metadata['metakey'])) { $document->setMetaData('keywords', $metadata['metakey']);}
		if (!empty($metadata['rights'])) { $document->setMetaData('rights', $metadata['rights']);}
		if (!empty($metadata['robots'])) { $document->setMetaData('robots', $metadata['robots']);}
		if (!empty($metadata['author'])) { $document->setMetaData('author', $metadata['author']);}
		
		parent::display($tpl);
	} // end function display()
	
	
}
