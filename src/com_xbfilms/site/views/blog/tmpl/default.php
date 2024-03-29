<?php 
/*******
 * @package xbFilms
 * @filesource site/views/blog/tmpl/default.php
 * @version 1.0.3.11 14th February 2023
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

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='rev_date';
    $orderDrn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'),'film_title'=>Text::_('XBFILMS_FILM_TITLE'),
    'rating'=>Text::_('XBCULTURE_RATING'), 'rev_date'=>Text::_('XBFILMS_DATE_SEEN'),
    'category_title'=>Text::_('XBCULTURE_CATEGORY'));

require_once JPATH_COMPONENT.'/helpers/route.php';


$itemid = XbfilmsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbfilms&view=category' . $itemid.'&id=';

?>
<div class="xbfilms">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=blog'); ?>" method="post" name="adminForm" id="adminForm">
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ((!$this->show_fcat) || ($this->hide_cat)) { $hide .= 'filter_fcategory_id,';}
				if ((!$this->show_rcat) || ($this->hide_cat)) { $hide .= 'filter_category_id,';}
				if (((!$this->show_rcat) && (!$this->show_fcat)) || ($this->hide_cat)) { $hide .= 'filter_subcats,';}
				if ($this->hide_tag) { $hide .= 'filter_tagfilt,filter_taglogic,';}
				echo '<div class="row-fluid"><div class="span12">';
				echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this,'hide'=>$hide));
				echo '</div></div>';
			} 
		?>
		<div class="row-fluid pagination" style="margin-bottom:10px 0;">
			<div class="pull-right">
				<p class="counter" style="text-align:right;margin-left:10px;">
					<?php echo $this->pagination->getResultsCounter().'.&nbsp;&nbsp;'; 
					   echo $this->pagination->getPagesCounter().'&nbsp;&nbsp;'.$this->pagination->getLimitBox().' per page'; ?>
				</p>
			</div>
			<div>
				<?php  echo $this->pagination->getPagesLinks(); ?>
            	<?php echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
			</div>
		</div>

		<div class="row-fluid">
        	<div class="span12">	
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<?php $evenrow = false; ?>
	<?php foreach ($this->items as $i => $item) : ?>
		<?php $imgok = (JFile::exists(JPATH_ROOT.'/'.$item->poster_img));
			if ($imgok) {
				$src = Uri::root().$item->poster_img;
				$tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
			}
			$rowcol = ($evenrow) ? 'xbboxcyan' : 'xbboxmag';
			$evenrow = !$evenrow;
		?>
        <div class="xbbox xbboxwht">
        <h4><?php echo HtmlHelper::date($item->rev_date , 'l jS F Y'); ?></h4>
		<div class="row-fluid">
			<div class="xbbox <?php echo $rowcol; ?>">
				<div class="row-fluid">
					<?php if ($imgok) : ?>
						<div class="span2">
							<img class="img-polaroid hasTooltip xbimgthumb" title="" 
								data-original-title="<?php echo $tip; ?>" data-placement="right"
								src="<?php echo $src; ?>"
								border="0" alt="" />							                          
						</div>
					<?php endif; ?>
					<div class="span<?php echo ($imgok) ? '9' : '11'; ?>" >
						<div class="pull-right xbmr10" style="text-align:right;">
	                    	<div class="xbstar">
	                    		<p></p>
								<?php if($item->ratcnt > 1) { 
									echo 'Average rating from '.$item->ratcnt.' reviews';} 
									$thisrat = $item->averat;
								?>
								<?php if (($this->zero_rating) && ($thisrat==0)) : ?>
								    <span class="<?php echo $this->zero_class; ?>" style="color:red;font-size=1.5em;"></span>
								<?php else : ?>
	                                <?php echo str_repeat('<i class="'.$this->star_class.' xb12"></i>',$thisrat); ?>
								<?php endif; ?>                        
	                        </div>
							<h4 ><?php echo $item->country; ?> <?php echo $item->rel_year; ?></h4>
								<?php if($item->runtime>0) : ?>
									<p><i>Running time: </i><?php echo $item->runtime; ?> mins</p>
								<?php endif; ?>
						</div>
						<?php $flink = XbfilmsHelperRoute::getFilmLink($item->film_id);	?>
						<h2><a href="<?php echo Route::_($flink);?>"><?php echo $item->film_title; ?></a></h2>
				       	<?php if (!$item->subtitle == '') : ?>
							<h3><?php  echo $item->subtitle; ?></h3>
				       	<?php endif; ?>
						<div class="row-fluid">
							<div class="span9">
		                        <?php if ($item->dircnt>0) : ?>
									<h4><span class="xbnit xbmr10">
										<?php echo $item->dircnt>1 ? Text::_('XBCULTURE_DIRECTORS') : Text::_('XBCULTURE_DIRECTOR'); ?>
										: </span>
										<?php echo $item->dirlist['commalist']; ?>                          
									</h4>
								<?php else: ?>
									<p class="xbnit"><?php echo Text::_('XBFILMS_NODIRECTOR'); ?></p>
		                        <?php endif; ?>
							</div>
						</div>   						
					</div>
				</div>
			</div>
		</div>
		<?php if ((trim($item->film_summary) != '') || (trim($item->synopsis) != '')): ?>
		<div class="row-fluid">
			<div class="span6">
				<?php $sumtext =  trim($item->film_summary);
				$sumlabel = 'Film Summary';
				if ($sumtext == '') {
					$sumtext = XbcultureHelper::makeSummaryText($item->synopsis, 0);
					$sumlabel='Synopsis extract';
				}
				if ( $sumtext != '') : ?>
					<div class="xbbox xbboxwht">
						<div class="pull-left"><span class="xbnit"><?php echo Text::_($sumlabel); ?> 
						: </span></div>
					 	<div><?php echo $sumtext; ?></div> 
					</div>
				<?php  endif;?>
			</div>
			<div class="span6">
				<?php if ($this->show_fcat) : ?>
					<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBFILMS_FILM_CAT'); ?></div>
					<div class="pull-left">
					<?php if ($this->show_fcat == 2) : ?>
    					<a class="label label-success" href="<?php echo Route::_($clink.$item->fcatid); ?>">
    						<?php echo $item->fcat_title; ?></a>
    				<?php else : ?>
    					<span class="label label-success">
    					<?php echo $item->fcat_title; ?></a>
    					</span>
					<?php endif; ?>
	    			</div>	
	                <div class="clearfix"></div>
				<?php endif; ?>
				<?php if ($this->show_ftags) : ?>
					<?php if (!empty($item->ftags)) : ?>
						<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBFILMS_CAPTAGFILMS'); ?>
						</div>
						<div class="pull-left">
							<?php  $tagLayout = new FileLayout('joomla.content.tags');
			    				echo $tagLayout->render($item->ftags); ?>
						</div>
	                	<div class="clearfix"></div>
					<?php endif; ?>    
				<?php endif; ?>
			</div>
		</div>	
		<?php endif; ?>	
		<div class="row-fluid"><div class="span1"></div>
			<div class="span5">
				<p><span class="xbnit"> 
					<?php echo Text::_(trim($item->review != '') ? 'XBCULTURE_REVIEWED' : 'XBCULTURE_RATED').' by '; ?> </span>
					<b><?php echo $item->reviewer; ?></b>,  
					<?php echo $item->where_seen; ?>
					<?php echo Text::_('XBFILMS_ON').'&nbsp;'.HtmlHelper::date($item->rev_date , 'd M Y') ; ?> 
				</p> 
			</div>
			<div class="span6">
				<?php $sumtext =  trim($item->summary);
				if ( $sumtext != '') : ?>
					<div class="xbbox xbboxwht">
						<div class="pull-left"><span class="xbnit"><?php echo Text::_('XBCULTURE_REVIEW_SUMMARY'); ?> 
						: </span></div>
					 	<div><?php echo $sumtext; ?></div> 
					</div>
				<?php else: ?>
				<?php  endif;?>
			</div>
		</div>	
		<?php if (trim($item->review != '')) : ?>
		<div class="xbbox <?php echo $rowcol; ?>">
			<div class="row-fluid">
				<div class="span2">
					<div class="xbstar">
             			<h3></h3>
						<?php if (($this->zero_rating) && ($item->rating==0)) : ?>
					    	<span class="<?php echo $this->zero_class; ?> xb12" style="color:red;"></span>
						<?php else : ?>
							<?php echo str_repeat('<i class="'.$this->star_class.'"></i>',$item->rating); ?>
						<?php endif; ?>                        
					</div>
				</div>
				<div class="span8">
					<h3><?php echo $item->title; ?></h3>
					<?php echo $item->review; ?>
				</div>
			</div>
			<?php if (($this->show_rcat) || ($this->show_rtags)) { echo '<hr />'; } ?>
			<div class="row-fluid">
			<?php if($this->show_rcat) : ?>
				<div class="span4">
					<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBCULTURE_REVIEW_CATEGORY'); ?></div>
					<div class="pull-left">
						<?php if($this->show_rcat ==2) : ?>
	    					<a class="label label-success" href="<?php echo Route::_($clink.$item->catid); ?>">
	    						<?php echo $item->category_title; ?></a>
	    				<?php else : ?>
    						<span class="label label-success">
	    					<?php echo $item->category_title; ?></a>
	    					</span>
						<?php endif; ?>
	    			</div>	
	                <div class="clearfix"></div>
				</div>
			<?php endif; ?>
			<?php if ($this->show_rtags) : ?>
		       	<div class="span<?php echo ($this->show_fcat) ? '8' : '12'; ?>">
				<?php if (!empty($item->tags)) : ?>
					<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBCULTURE_REVIEW_TAGS'); ?>
					</div>
					<div class="pull-left">
						<?php  $tagLayout = new FileLayout('joomla.content.tags');
		    				echo $tagLayout->render($item->tags); ?>
					</div>
				<?php endif; ?>    
	                <div class="clearfix"></div>
				</div>
			<?php  endif; ?>
			</div>
		</div>
		<?php endif; ?>														
		</div>
		<br /><hr /><br />
	<?php endforeach; ?>
	<?php echo $this->pagination->getListFooter(); ?>
<?php endif; ?>
<?php echo HTMLHelper::_('form.token'); ?>
</div>
	</div>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
</div>

