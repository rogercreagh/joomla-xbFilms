<?php
/*******
 * @package xbFilms
 * @filesource site/views/category/tmpl/default.php
 * @version 0.9.9.4 28th July 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getFilmsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$blink = 'index.php?option=com_xbfilms&view=film'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getPeopleRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = 'index.php?option=com_xbpeople&view=person'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getCharsRoute() ;
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = 'index.php?option=com_xbpeople&view=character'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getReviewsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$rlink = 'index.php?option=com_xbfilms&view=filmreview'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getCategoriesRoute();
if ($itemid !== null) {
    $cclink = 'index.php?option=com_xbfilms&Itemid='.$itemid.'';
} else {
    $cclink = 'index.php?option=com_xbfilms&view=categories';
}

$show_catdesc = $this->params->get('show_catdesc',1);

?>
<div class="row-fluid" style="margin-bottom:20px;">
	<div class="span3">
		<h4><?php echo JText::_('XBFILMS_ITEMSCAT'); ?></h4>		
	</div>	
	<div class="span9">
          <div class="badge badge-success pull-left"><h3><?php echo $item->title; ?></h3></div>
          
		<?php if ((!$this->hide_empty) && (strpos($item->path,'/')!==false)) : ?>
			<div class="xb11 pull-left" style="padding-top:20px;margin-left:40px;">
				<i><?php echo JText::_('XBCULTURE_HEIRARCHY_U'); ?>:</i> 
				<?php  $path = substr($item->path, 0, strrpos($item->path, '/'));
					$path = str_replace('/', ' - ', $path);
					echo $path; ?>
        	</div>
        <?php endif; ?>
	</div>	
</div>
<?php if (($show_catdesc) && ($item->description != '')) : ?>
	<div class="row-fluid">
		<div class= "span2">
			<p><i>Description:</i></p>
		</div>
		<div class="span10">
			<?php echo $item->description; ?>
		</div>
	</div>
<?php endif; ?>
<div class="row-fluid">
	<?php if($item->extension == 'com_xbfilms') : ?>
    	<div class= "span6">
    		<div class="xbbox xbboxcyan xbyscroll xbmh300">
    			<p><?php echo $item->bcnt; ?> films</p>
    			<?php if ($item->bcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->bks as $i=>$bk) { 
    					echo '<li><a href="'.$blink.$bk->bid.'">'.$bk->title.'</a></li> ';
    				} ?>				
    				</ul>
    			<?php else: ?>
    				<p class="xbnit"><?php echo Text::_('no items assigned to category')?></p>
    			<?php endif; ?>
    		</div>
    	</div>
    	<div class= "span6">
    		<div class="xbbox xbboxmag xbyscroll xbmh300">
    			<p><?php echo $item->rcnt; ?> reviews</p>
    			<?php if ($item->rcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->revs as $i=>$rev) { 
    					echo '<li><a href="'.$rlink.$rev->rid.'">'.$rev->title.'</a></li> ';
    				} ?>				
    				</ul>
    			<?php else: ?>
    				<p class="xbnit"><?php echo Text::_('no items assigned to category')?></p>
    			<?php endif; ?>
    		</div>
    	</div>
	<?php else: ?>
    	<div class= "span6">
    		<div class="xbbox xbboxgrn xbyscroll xbmh300">
    			<p><?php echo $item->pcnt; ?> people</p>
    			<?php if ($item->pcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->people as $i=>$per) { 
    					echo '<li><a href="'.$plink.$per->pid.'">'.$per->title.'</a></li> ';
    				} ?>				
    				</ul>
    			<?php else: ?>
    				<p class="xbnit"><?php echo Text::_('no items assigned to category')?></p>
    			<?php endif; ?>
    		</div>
    	</div>
    	<div class= "span6">
    		<div class="xbbox xbboxcyan xbyscroll xbmh300">
    			<p><?php echo $item->chcnt; ?> characters</p>
    			<?php if ($item->chcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->chars as $i=>$char) { 
    					echo '<li><a href="'.$clink.$char->pid.'">'.$char->title.'</a></li> ';
    				} ?>			
    				</ul>
    			<?php else: ?>
    				<p class="xbnit"><?php echo Text::_('no items assigned to category')?></p>
    			<?php endif; ?>
    		</div>
    	</div>
	<?php endif; ?>
</div>
<div class="clearfix"></div>
<p class="xbtc xbmt16">
	<a href="<?php echo $cclink; ?>" class="btn btn-small">
		<?php echo JText::_('XBFILMS_CAT_COUNTS'); ?>
	</a>
</p>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>


