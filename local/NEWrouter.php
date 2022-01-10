<?php
/*******
 * @package xbFilms
 * @filesource site/router.php
 * @version 0.9.6.f 10th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;

class XbfilmsRouter extends JComponentRouterView {

	public function __construct(CMSApplication $app = null, AbstractMenu $menu = null)
	{
		$film = new JComponentRouterViewconfiguration('film');
		$film->setKey('id');
		$this->registerView($film);
		$review = new JComponentRouterViewconfiguration('filmreview');
		$review->setKey('id');
		$this->registerView($review);
		$person = new JComponentRouterViewconfiguration('person');
		$person->setKey('id');
		$this->registerView($person);
		$character = new JComponentRouterViewconfiguration('character');
		$character->setKey('id');
		$this->registerView($character);
		$tag = new JComponentRouterViewconfiguration('tag');
		$tag->setKey('id')->setNestable();
		$this->registerView($tag);
		$category = new JComponentRouterViewconfiguration('caategory');
		$category->setKey('id')->setNestable();
		$this->registerView($category);
		$this->registerView(new JComponentRouterViewconfiguration('filmlist'));
		$this->registerView(new JComponentRouterViewconfiguration('blog'));
		$this->registerView(new JComponentRouterViewconfiguration('filmreviews'));
		$this->registerView(new JComponentRouterViewconfiguration('people'));
		$this->registerView(new JComponentRouterViewconfiguration('characters'));
		$this->registerView(new JComponentRouterViewconfiguration('categories'));
		$this->registerView(new JComponentRouterViewconfiguration('tags'));
		
		parent::__construct($app, $menu);
		
		$this->attachRule(new JComponentRouterRulesMenu($this));
		$this->attachRule(new JComponentRouterRulesStandard($this));
		$this->attachRule(new JComponentRouterRulesNomenu($this));
		
	}
	
	
	
	public function build(&$query)
	{
		//      Factory::getApplication()->enqueueMessage('<pre>'.print_r($query,true).'</pre>','build');
		$segments = array();
		if (isset($query['view']))
		{
			$segments[] = $query['view'];
			unset($query['view']);
		}
		if (isset($query['id']))
		{
			$db = Factory::getDbo();
			$qry = $db->getQuery(true);
			$qry->select('alias');
			switch($segments[0])
			{
				case 'film':
					$qry->from('#__xbfilms');
					break;
				case 'person':
					$qry->from('#__xbpersons');
					break;
				case 'character':
					$qry->from('#__xbcharacters');
					break;
				case 'category':
					$qry->from('#__categories');
					break;
				case 'review':
					$qry->from('#__filmreviews');
					break;
				case 'tag':
					$qry->from('#__tags');
					break;
			}
			$qry->where('id = ' . $db->quote($query['id']));
			$db->setQuery($qry);
			$alias = $db->loadResult();
			$segments[] = $alias;
			unset($query['id']);
		}
		return $segments;
	}
	
	public function parse(&$segments)
	{
		//      Factory::getApplication()->enqueueMessage('<pre>'.print_r($segments,true).'</pre>','parse');
		$vars = array();
		
		$db = Factory::getDbo();
		$qry = $db->getQuery(true);
		$qry->select('id');
		switch($segments[0])
		{
			case 'filmlist':
				$vars['view'] = 'filmlist';
				break;
			case 'blog':
				$vars['view'] = 'blog';
				break;
			case 'people':
				$vars['view'] = 'people';
				break;
			case 'characters':
				$vars['view'] = 'characters';
				break;
			case 'categories':
				$vars['view'] = 'categories';
				break;
			case 'reviews':
				$vars['view'] = 'reviews';
				break;
			case 'tags':
				$vars['view'] = 'tags';
				break;
			case 'film':
				$vars['view'] = 'film';
				$qry->from('#__xbfilms');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'review':
				$vars['view'] = 'review';
				$qry->from('#__xbfilmreviews');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'person':
				$vars['view'] = 'person';
				$qry->from('#__xbpersons');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'characters':
				$vars['view'] = 'character';
				$qry->from('#__xbcharacters');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'category':
				$vars['view'] = 'category';
				$qry->from('#__categories');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$qry->where('extension = ' . $db->quote('com_xbfilms'));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'tag':
				$vars['view'] = 'tag';
				$qry->from('#__tags');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
		}
		
		
		return $vars;
	}
	
	public function preprocess($query)
	{
		return $query;
	}
	
}
