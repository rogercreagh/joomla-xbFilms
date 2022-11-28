<?php 
/*******
 * @package xbFilms
 * @filesource site/views/filmlist/tmpl/default.php
 * @version 0.10.0.4 28th November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\FileLayout;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='last_seen';
    $listDirn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'),'rel_year'=>Text::_('XBFILMS_YEAR_RELEASED'), 
		'averat'=>Text::_('XBFILMS_AVERAGE_RATING'), 'first_seen'=>Text::_('First Seen'),
        'last_seen'=>Text::_('Last Seen'), 'category_title'=>Text::_('XBCULTURE_CATEGORY'));

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbfilms&view=category'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getFilmsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$blink = 'index.php?option=com_xbfilms&view=film'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getReviewsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$rlink = 'index.php?option=com_xbfilms&view=filmreview'.$itemid.'&id=';

?>
<div class="xbculture">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=filmlist'); ?>" method="post" name="adminForm" id="adminForm">       
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ((!$this->showcat) || ($this->hide_cat)) { $hide .= 'filter_category_id, filter_subcats,';}
				if ($this->hide_peep) { $hide .= 'filter_perfilt,filter_prole,';}
				if ($this->hide_char) { $hide .= 'filter_charfilt,';}
				if ((!$this->showtags) || $this->hide_tag) { $hide .= 'filter_tagfilt,filter_taglogic,';}
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

	<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbfilmlist">	
		<thead>
			<tr>
				<?php if($this->show_pic) : ?>
					<th class="center" style="width:80px">
						<?php echo Text::_( 'XBFILMS_POSTER' ); ?>
					</th>	
                <?php endif; ?>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_TITLE','title',$listDirn,$listOrder).				
    						', '.Text::_('XBCULTURE_DIRECTOR').', '.
    						HTMLHelper::_('searchtools.sort','XBFILMS_RELYEARCOL','rel_year',$listDirn,$listOrder );
					?>
				</th>					
				<?php if($this->show_sum) : ?>
    				<th class="hidden-phone">
    					<?php echo Text::_('XBCULTURE_SUMMARY');?>
    				</th>
                <?php endif; ?>
                <?php if ($this->show_rev != 0 ) : ?>
					<th class="xbtc">
						<?php echo HTMLHelper::_('searchtools.sort','Rating','averat',$listDirn,$listOrder); ?>
					</th>
				<?php endif; ?>
                <?php if ($this->show_fdates) : ?>
    				<th>
    					<?php echo HTMLHelper::_('searchtools.sort','First','first_seen',$listDirn,$listOrder ).'/'; ?>
    					<?php echo HTMLHelper::_('searchtools.sort','Last','last_seen',$listDirn,$listOrder ).' seen'; ?>
    				</th>
				<?php endif; ?>
				<?php if($this->showcat || $this->showtags) : ?>
    				<th class="hidden-tablet hidden-phone">
    					<?php if ($this->showcat) {
    						echo HTMLHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder );
    					}
    					if (($this->showcat) && ($this->showtags)) {
    					    echo ' &amp; ';
    					}
    					if($this->showtags) {
    					    echo Text::_( 'XBFILMS_CAPTAGS' ); 
    					} ?>                
    				</th>
                <?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $reviews = ''; ?>
				<tr class="row<?php echo $i % 2; ?>">	
              		<?php if($this->show_pic) : ?>
						<td>
						<?php  $src = trim($item->poster_img);
							if ((!$src=='') && (file_exists(JPATH_ROOT.'/'.$src))) :
								$src = Uri::root().$src;
								$tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
								?>
								<img class="img-polaroid hasPopover xbimgthumb" title="" 
									data-original-title="<?php echo $tip; ?>" data-placement="right"
									src="<?php echo $src; ?>"
									border="0" alt="" />							                          
	                    	<?php  endif; ?>	                    
						</td>
                    <?php endif; ?>
					<td>
						<p class="xbtitle">
							<a href="<?php echo Route::_($blink.$item->id);?>" >
								<b><?php echo $this->escape($item->title); ?></b></a> 
						<?php if (!empty($item->subtitle)) :?>
                        	<br /><span class="xb09"><?php echo $this->escape($item->subtitle); ?></span>
                        <?php endif; ?>
						</p><p>
                        	<?php if ($item->dircnt==0) {
                        		echo '<span class="xbnit">'.Text::_('XBFILMS_NODIRECTOR').'</span>';
                        	} else { ?> 
	                        	<span class="xbnit">
	                        		<?php echo $item->dircnt>1 ? Text::_('XBCULTURE_DIRECTORS') : Text::_('XBCULTURE_DIRECTOR' ); ?>
	                        	</span>: 
                        		<?php echo $item->dirlist; 
                        	} ?>                          	
						</br>
						<span class="xb09">
							<?php if($item->rel_year > 0) {
								echo '<span class="xbnit">'.Text::_('XBFILMS_CAPRELEASED').'</span>: '.$item->rel_year.'<br />'; 
							}?>																			
						</span></p>
					</td>
    				<?php if($this->show_sum) : ?>
    					<td class="hidden-phone">
    						<p class="xb095">
    							<?php if (!empty($item->summary)) : ?>
    								<?php echo $item->summary; ?>
        						<?php else : ?>
        							<?php if (!empty($item->synopsis)) : ?>
        								<span class="xbnit"><?php echo Text::_('XBFILMS_SYNOPSIS_EXTRACT'); ?>: </span>
        								<?php echo XbcultureHelper::makeSummaryText($item->synopsis,0); ?>
        							<?php else : ?>
        							<span class="xbnote">
        								<?php echo Text::_('XBFILMS_NO_SUMMARY_SYNOPSIS'); ?>
        							</span></span>
        							<?php endif; ?>
        						<?php endif; ?>
                            </p>
                            <?php if (!empty($item->synopsis)) : ?>
                            	<p class="xbnit xb09">   
                                 <?php 
                                 	echo Text::_('XBFILMS_FULLSYNOPSIS').' '.str_word_count(strip_tags($item->synopsis)).' '.Text::_('XBCULTURE_WORDS'); 
                                 ?>
    							</p>
    						<?php endif; ?>
    					</td>
					<?php endif; ?>
					<?php if ($this->show_rev != 0 ) : ?>
    					<td>
    						<?php if ($item->revcnt==0) : ?>
    						   <i><?php  echo ($this->show_rev == 1)? Text::_( 'XBCULTURE_NO_RATING' ) : Text::_( 'XBCULTURE_NO_REVIEW' ); ?></i><br />
    						<?php else : ?> 
	                        	<?php $stars = (round(($item->averat)*2)/2); ?>
	                            <div class="xbstar
    	                            <?php if ($item->revcnt>1) : ?>
    	                            	 xbmb8 xbbb1">Average
                              		<?php else: ?>
                                      ">
    	                            <?php endif; ?>
    								<?php if (($this->zero_rating) && ($stars==0)) : ?>
    								    <span class="<?php echo $this->zero_class; ?>" style="color:red;"></span>
    								<?php else : ?>
    	                                <?php echo str_repeat('<i class="'.$this->star_class.'"></i>',intval($item->averat)); ?>
    	                                <?php if (($item->averat - floor($item->averat))>0) : ?>
    	                                    <i class="<?php echo $this->halfstar_class; ?>"></i>
    	                                    <span style="color:darkgray;"> (<?php echo round($item->averat,1); ?>)</span>                                   
    	                                <?php  endif; ?> 
    	                             <?php endif; ?>                        
	                            </div>
     							<?php if ($this->show_rev == 2) : ?>
                                    <?php foreach ($item->reviews as $rev) : ?>
                                    	<?php $summary = $rev->summary; 
                                    	if ((empty($summary) && ($rev->review))) {
                                    	    $summary = XbcultureHelper::makeSummaryText($rev->review,0);
                                    	}
                                    	if (empty($summary)) {
                                    	    $summary = '<i>Rating only, no review text</i>';
                                    	}
                                    	?>
										<div class="hasPopover xbmb8 xb09"  title 
											data-content="<?php echo htmlentities($summary); ?>"
											data-original-title="<?php echo htmlentities($rev->title); ?>" 
                                		>
    										<?php if ($item->revcnt>1) : ?>
                                            	<?php if($rev->rating == 0) {
                                                	echo '<span class="'.$this->zero_class.'" style="color:red;"></span>';
                                                } else {
                                                	echo str_repeat('<i class="'.$this->star_class.'"></i>',$rev->rating);
                                                } 
                                            	echo '<br />';?>
                                                
     			                            <?php endif; ?>
    			                            <a href="<?php echo Route::_($rlink.$rev->id); ?>">
	    	                                	 <?php echo $rev->reviewer; ?> 
	    	                                	 <?php echo HtmlHelper::date($rev->rev_date , 'd M y'); ?>
    			                            </a>
        								</div>
        							<?php endforeach; ?> 
        						<?php endif; ?>
     						<?php endif; ?>   											
    					</td>
    				<?php endif; ?>
                   <?php if ($this->show_fdates) : ?>
        				<td>
        					<p><?php if($item->first_seen) {
        					    //if more earlier than say 2010 then if day is 01 display month-year if day-mon is 01-01 only display year
        					    $datefmt = xbCultureHelper::getDateFmt($item->first_seen);
						        echo HtmlHelper::date($item->first_seen , $datefmt);
        					}
    					    echo '<br />';
        					if(($item->last_seen) && ($item->last_seen != $item->first_seen)) {
        					    $datefmt = xbCultureHelper::getDateFmt($item->last_seen);
        					   echo HtmlHelper::date($item->last_seen , $datefmt); 
        					} ?> </p>
         				</td>
     				<?php endif; ?>
    				<?php if($this->showcat || $this->showtags) : ?>
    					<td class="hidden-phone">
     						<?php if($this->showcat) : ?>
     							<p>
     							<?php if($this->showcat==2) : ?>											
    								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>"><?php echo $item->category_title; ?></a>
    							<?php else: ?>
    								<span class="label label-success"><?php echo $item->category_title; ?></span>
    							<?php endif; ?>
    							</p>
    						<?php endif; ?>
    						<?php if($this->showtags) {
    							$tagLayout = new FileLayout('joomla.content.tags');
    							echo $tagLayout->render($item->tags);
    						}
        					?>
    					</td>
					<?php endif; ?>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>
	<?php endif; ?>
	<?php echo HTMLHelper::_('form.token'); ?>
      </div>
      </div>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
</div>
