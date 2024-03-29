<?php 
/*******
 * @package xbFilms
 * @filesource site/views/tag/view.html.php
 * @version 0.9.9.8 18th October 2022
 * @since v0.5.1
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

class XbfilmsViewTag extends JViewLegacy {

	public function display($tpl = null) {
		
		$this->item = $this->get('Item');
		
		$this->state = $this->get('State');
		$this->params = $this->state->get('params');

		if (count($errors = $this->get('Errors'))) {
			Factory::getApplication()->enqueueMessage(implode('<br />', $errors),'error');
			return false;
		}
		
		$this->hide_empty = $this->params->get('hide_empty','','int');
		$this->show_tagpath = $this->params->get('show_tagpath',1,'int');
		
		$document = $this->document; //Factory::getDocument();
		$document->setTitle('Tag view: '.$this->item->title);
		$document->setMetaData('title', JText::_('XBCULTURE_TAG_DETAILS').' '.$this->item->title);
		$metadata = json_decode($this->item->metadata,true);
		if (!empty($metadata['metadesc'])) { $document->setDescription($metadata['metadesc']); }
		if (!empty($metadata['metakey'])) { $document->setMetaData('keywords', $metadata['metakey']);}
		if (!empty($metadata['rights'])) { $document->setMetaData('rights', $metadata['rights']);}
		if (!empty($metadata['robots'])) { $document->setMetaData('robots', $metadata['robots']);}
		if (!empty($metadata['author'])) { $document->setMetaData('author', $metadata['author']);}
		
		parent::display($tpl);
	}
	
}

