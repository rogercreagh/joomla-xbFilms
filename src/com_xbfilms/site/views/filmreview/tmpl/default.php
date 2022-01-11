<?php
/*******
 * @package xbFilms
 * @filesource site/views/filmreview/tmpl/default.php
 * @version 0.9.6.f 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbfilms&view=category'.$itemid.'&id=';

$flink = XbfilmsHelperRoute::getFilmLink($item->film_id);

$itemid = XbfilmsHelperRoute::getReviewsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$frlink = 'index.php?option=com_xbfilms&view=filmreview'.$itemid.'&id=';

$imgok = (($this->show_image >0) && (JFile::exists(JPATH_ROOT.'/'.$item->poster_img)));
if ($imgok) {
    $src = Uri::root().$item->poster_img;
    $tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
}

?>
<div class="row-fluid">
	<?php if ($imgok && ($this->show_image == 1)) : ?>
		<div class="span2 xbmb12">
			<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>
	<div class="<?php echo $imgok==true ? 'span10' : 'span12'; ?>">
		<div class="row-fluid"> <!-- review title -->
			<div class="span12">
				<div class="xbbox xbboxmag">
					<h3><?php echo $item->title; ?></h3>
					<h4><span class="xbnit"><?php echo JText::_('COM_XBFILMS_REVIEWOF'); ?></span>"
						 <?php echo '<a href="'.$flink;
						 if (!empty($item->edauths)) {
						 	echo '" class="hasTooltip" title data-original-title="'.$item->edauths;
						 }
							echo '">'.$item->film_title.'</a>'; ?>" 
						<span class="xbnit"><?php echo JText::_('XBCULTURE_BY'); ?></span>
						 <?php echo $item->reviewer.', '.
								 HtmlHelper::date($item->rev_date , Text::_('D jS M Y')); ?>
					</h4>
				</div>
			</div>
		</div>
    	<div class="row-fluid"><!-- rating -->
    		<div class="span12 xbmt16 center">
    			<span class="xbnit"><?php echo JText::_('XBCULTURE_RATING'); ?>: </span>
    	        <?php if (($this->zero_rating) && ($item->rating==0)) : ?>
    	            <span class="<?php echo $this->zero_class; ?> zero24" style="color:red;"></span>
    	        <?php else: ?>
    	          	<span class="xb15 xbstar">
    	          	<?php echo str_repeat('<i class="'.$this->star_class.'"></i>',$item->rating); ?>
    	          	</span>
    	        <?php endif; ?>	
    		</div>
    	</div>
	</div>
	<?php if ($imgok && ($this->show_image == 2)) : ?>
		<div class="span2 xbmb12">
			<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="left" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>

</div>
<?php if ((!empty($item->summary)) && (!empty($item->review))) : ?>
	<div class="row-fluid"><!-- show summary if review text exists -->
		<div class="span2">
			<div class=" pull-right xbnit"><?php echo JText::_('XBCULTURE_SUMMARY'); ?>
			</div>					
		</div>
		<div class="span9">
			<div class="xbbox xbboxwht">
		 		<div><?php echo $item->summary; ?></div> 
			</div>
		</div>
	</div>
<?php  endif;?>
<div class="row-fluid"><!-- other bits -->
	<?php if(!($this->hide_empty) && ($item->where_seen =='')): ?>
		<div class="span6">
			<span class="xbnit"><?php echo JText::_('Where seen'); ?>: </span>
			<?php echo $item->where_seen; ?>
		</div>
	<?php endif; ?>
<!-- insert reading notes here -->
</div>
<div class="row-fluid"><!-- review text -->
	<div class="span12">
    		<?php if (empty($item->review)) : ?>
    			<?php if (empty($item->summary)) : ?>
        			<p class="xbnit">No review text provided</p>
    			<?php else : ?>
    				<p class="xbnit"><?php echo JText::_('Short Review'); ?></p>
    				<div class="xbbox xbboxmag"><?php echo $item->summary; ?></div>
    			<?php endif; ?>
		<?php else : ?>
			<p class="xbnit xbmb8"><?php echo JText::_('XBCULTURE_REVIEW_U');?></p>
			<div class="xbbox xbboxmag"><?php echo $item->review; ?></div>
        <?php endif; ?>
	</div>
</div>
    
<div class="row-fluid xbmt16">
			<?php if ($this->show_cat >0) : ?>       
	        	<div class="span4">
					<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBCULTURE_CATEGORY'); ?></div>
					<div class="pull-left">
    					<?php if($this->show_cat==2) : ?>
    						<a class="label label-success" href="<?php echo JRoute::_($clink.$item->catid); ?>">
    							<?php echo $item->category_title; ?></a>
    					<?php else: ?>
    						<span class="label label-success">
    							<?php echo $item->category_title; ?></span>
    					<?php endif; ?>		
					</div>
		        </div>
	        <?php endif; ?>
        	<?php if (($this->show_tags) && (!empty($item->tags))) : ?>
        	<div class="span<?php echo ($this->show_fcat>0) ? '8' : '12'; ?>">
				<div class="pull-left xbnit xbmr10"><?php echo JText::_('COM_XBFILMS_CAPTAGS'); ?>
				</div>
				<div class="pull-left">
					<?php  $tagLayout = new FileLayout('joomla.content.tags');
	    				echo $tagLayout->render($item->tags); ?>
				</div>
        	</div>
			<?php endif; ?>
			
</div>
<?php if (count($item->reviews) > 1) : ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="xbbox xbboxwht">
				<span class="xbnit"><?php echo JText::_('Other reviews of').' '.$item->film_title; ?>: </span>
				<p>
				<?php foreach ($item->reviews as $rev) : ?>
					<?php if ($rev->id != $item->id) : ?>
						<div class="pull-left" style="min-width:110px; margin:0 10px 0 30px">
	                     	<?php if (($this->zero_rating) && ($rev->rating==0)) : ?>
                	            <span class="<?php echo $this->zero_class; ?> zero16" style="color:red;"></span>
                	        <?php else: ?>
                	          	<span class="xbstar">
                	          	<?php echo str_repeat('<i class="'.$this->star_class.'"></i>',$item->rating); ?>
                	          	</span>
		        			<?php endif; ?> 	
	                    </div>
	                    <a href="<?php echo $frlink.$rev->id; ?>"><b><?php echo $rev->title; ?></b></a>
	                     by <?php echo $rev->created_by_alias.', '.HtmlHelper::date($rev->created , Text::_('d M Y')); ?>
	                     <br />
	                <?php endif; ?>
				<?php endforeach; ?>
				</p>
	        </div>
	    </div>
	</div>
<?php endif; ?>
<div class="row-fluid xbmt16"><!-- prev/next -->
	<div class="span12 xbbox xbboxgrey">
		<div class="row-fluid">
			<div class="span2">
				<?php if (($item->prev>0) || ($item->next>0)) : ?>
				<span class="hasTooltip xbhelp" title 
					data-original-title="<?php echo JText::_('COM_XBFILMS_INFO_PREVNEXT'); ?>" >
				</span>&nbsp;
				<?php endif; ?>
				<?php if($item->prev > 0) : ?>
					<a href="index.php?option=com_xbfilms&view=filmreview&id=<?php echo $item->prev ?>" class="btn btn-small">
						<?php echo JText::_('COM_XBFILMS_CAPPREV'); ?></a>
			    <?php endif; ?>
			</div>
			<div class="span8"><center>
				<a href="index.php?option=com_xbfilms&view=filmreviews" class="btn btn-small">
					<?php echo JText::_('COM_XBFILMS_REVIEWLIST'); ?></a></center>
			</div>
			<div class="span2">
			<?php if($item->next > 0) : ?>
				<a href="index.php?option=com_xbfilms&view=filmreview&id=<?php echo $item->next ?>" class="btn btn-small pull-right">
					<?php echo JText::_('COM_XBFILMS_CAPNEXT'); ?></a>
		    <?php endif; ?>
			</div>
	      </div>
      </div>
</div>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>

