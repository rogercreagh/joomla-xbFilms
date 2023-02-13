<?php
/*******
 * @package xbFilms
 * @filesource site/views/filmreview/tmpl/default.php
 * @version 1.0.3.9 13th Febrary 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

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
<style type="text/css" media="screen">
	.xbpvmodal .modal-content {padding:15px;max-height:calc(100vh - 190px); overflow:scroll; }
</style>
<div class="xbculture">
<div class="xbbox revbox">
	<div class="row-fluid">
		<?php if ($imgok && ($this->show_image == 1)) : ?>
			<div class="span2 xbmb12">
				<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
					data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
			</div>
		<?php endif; ?>
		<div class="<?php echo $imgok==true ? 'span10' : 'span12'; ?>">
			<div class="pull-right xbmr20" style="text-align:right;">
	    	    <div class="xb12">
					<?php echo XbcultureHelper::getStarStr($item->rating, 'com_xbfilms'); ?> 
                </div>
                <?php if($item->reviewer != '') : ?>
        			<p><span class="xbnit"><?php echo Text::_('Reviewed by'); ?></span>
        				<?php echo $item->reviewer.', '; ?>
        			</p>
                <?php endif; ?>
                <p><?php echo Text::_('Seen on').' '.HtmlHelper::date($item->rev_date ,'d M Y'); ?>
                <br /><span class="xbnit"><?php echo Text::_('XBCULTURE_WHERE_SEEN'); ?>: </span>
                <?php echo $item->where_seen; ?></p>
			</div>
			<h2><?php echo $item->title; ?></h2>
			<h3><span class="xbnit"><?php echo Text::_('XBFILMS_REVIEWOF'); ?></span>"
				<a href="<?php echo XbfilmsHelperRoute::getFilmLink($item->film_id); ?>" >
				 	<?php echo $item->film_title; ?>
				</a>&nbsp;
				<a href="" data-toggle="modal" data-target="#ajax-fpvmodal" data-backdrop="static"  onclick="window.pvid=<?php echo $item->id; ?>;">
    				<i class="far fa-eye"></i>
    			</a>					
			</h3>
			<?php if($item->dircnt>0) :?>
				<p><span class="xbnit">Film directed by</span>: 
					<?php echo $item->dirlist['commalist']; ?>
    			</p>	
			<?php endif; ?> 
			<p><span class="xbnit"> Film released</span>: <b><?php echo $item->rel_year; ?></b></p>
             <div class="clearfix"></div>
             <?php if ((trim($item->review)=='') && (trim($item->summary)=='')) : ?>
            	<div style="margin:auto;"><h4><i>No review text - only rating provided</i></h4></div>
             <?php else : ?>
                 <?php if (trim($item->summary)!='') : ?>
                 	<span class="xbnit"><?php echo (trim($item->review)!='') ? Text::_('XBCULTURE_SUMMARY') : Text::_('XBCULTURE_SHORT_REVIEW'); ?>
    				<div class="xbbox xbboxwht" style="max-width:600px; margin:auto;">
    					<div><?php echo $item->summary; ?></div> 
    				</div>
                 <?php endif; ?>						
             <?php endif; ?>
					
		</div>
		<?php if ($imgok && ($this->show_image == 2)) : ?>
			<div class="span2 xbmb12">
				<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
					 data-placement="left" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
			</div>
		<?php endif; ?>
	</div>
    <div class="row-fluid"><!-- review text -->
        	<div class="span<?php echo (count($item->reviews) > 1) ? '9' : '12'; ?>">
            		<?php if (empty($item->review)) : ?>
            			<?php if (!empty($item->summary)) : ?>
                			<p class="xbnit"><?php echo Text::_('No long review text provided'); ?></p>
                		<?php endif; ?>
            		<?php else : ?>
            			<p class="xbnit xbmb8"><?php echo Text::_('XBCULTURE_REVIEW_U');?></p>
            			<div class="xbbox xbboxmag" style="max-width:700px; margin:auto;"><?php echo $item->review; ?></div>
                    <?php endif; ?>
        	</div>
            <?php if (count($item->reviews) > 1) : ?>
        		<div class="span3">
                	<div class="row-fluid">
                		<div class="span12">
                			<div class="xbbox xbboxwht">
                				<span class="xbnit"><?php echo Text::_('XBCULTURE_OTHER_REVIEWS_OF').' <b>'.$item->film_title.'</b>'; ?>: </span>
                				<p>
                				<?php foreach ($item->reviews as $rev) : ?>
                					<?php if ($rev->id != $item->id) : ?>
                						<div style="min-width:110px; margin:0 10px 0 30px">
											<?php echo XbcultureHelper::getStarStr($rev->rating, 'com_xbfilms'); ?> 
                	                    </div>
                	                    <a href="<?php echo $frlink.$rev->id; ?>"><b><?php echo HtmlHelper::date($rev->rev_date , 'd M Y'); ?></b></a>&nbsp;
                	                    <a href="" data-toggle="modal" data-target="#ajax-rpvmodal" data-backdrop="static"  onclick="window.pvid=<?php echo $rev->id; ?>;">
                            				<i class="far fa-eye"></i>
                            			</a>					                	                    
                	                    <br />
                	                <?php endif; ?>
                				<?php endforeach; ?>
                				</p>
                	        </div>
                	    </div>
                	</div>
            	</div>
            <?php endif; ?>
        </div>
        <div class="row-fluid xbmt16">
        	<div class="span4">
        		<?php if ($this->show_cat >0) : ?>       
                	<div>
        				<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBCULTURE_CATEGORY'); ?></div>
        				<div class="pull-left">
        					<?php if($this->show_cat==2) : ?>
        						<a class="label label-success" href="<?php echo Route::_($clink.$item->catid); ?>">
        							<?php echo $item->category_title; ?></a>
        					<?php else: ?>
        						<span class="label label-success">
        							<?php echo $item->category_title; ?></span>
        					<?php endif; ?>		
        				</div>
        	        </div>
                <?php endif; ?>
            </div>
            <div class="span8">
            	<?php if (($this->show_tags) && (!empty($item->tags))) : ?>
                	<div>
            			<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBFILMS_CAPTAGS'); ?>
            			</div>
            			<div class="pull-left">
            				<?php  $tagLayout = new FileLayout('joomla.content.tags');
                				echo $tagLayout->render($item->tags); ?>
            			</div>
                	</div>
        		<?php endif; ?>
        	</div>
        			
        </div>
	</div>
<br />    
<?php if($this->tmpl != 'component') : ?>
	<div class="xbbox xbboxgrey">
        <div class="row-fluid"><!-- prev/next -->
			<div class="row-fluid">
        			<div class="span2">
        				<?php if (($item->prev>0) || ($item->next>0)) : ?>
        				<span class="hasTooltip xbhelp" title 
        					data-original-title="<?php echo Text::_('XBFILMS_INFO_PREVNEXT'); ?>" >
        				</span>&nbsp;
        				<?php endif; ?>
        				<?php if($item->prev > 0) : ?>
        					<a href="index.php?option=com_xbfilms&view=filmreview&id=<?php echo $item->prev ?>" class="btn btn-small">
        						<?php echo Text::_('XBCULTURE_PREV'); ?></a>
        			    <?php endif; ?>
        			</div>
        			<div class="span8"><center>
        				<a href="index.php?option=com_xbfilms&view=filmreviews" class="btn btn-small">
        					<?php echo Text::_('XBFILMS_REVIEWLIST'); ?></a></center>
        			</div>
        			<div class="span2">
        			<?php if($item->next > 0) : ?>
        				<a href="index.php?option=com_xbfilms&view=filmreview&id=<?php echo $item->next ?>" class="btn btn-small pull-right">
        					<?php echo Text::_('XBCULTURE_NEXT'); ?></a>
        		    <?php endif; ?>
        			</div>
			</div>
		</div>
    </div>
	<div class="clearfix"></div>
	<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
<?php endif; ?>
</div>
<?php if($this->tmpl != 'component') : ?>
	<?php echo LayoutHelper::render('xbculture.modalpvlayout', array('show' => 'fip'), JPATH_ROOT .'/components/com_xbpeople/layouts');   ?>
<?php endif; ?>

