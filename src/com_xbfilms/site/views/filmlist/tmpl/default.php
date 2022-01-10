<?php 
/*******
 * @package xbFilms
 * @filesource site/views/filmlist/tmpl/default.php
 * @version 0.9.6.f 10th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='cat_date';
    $listDirn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'),'rel_year'=>Text::_('COM_XBFILMS_YEAR_RELEASED'), 
		'averat'=>Text::_('COM_XBFILMS_AVERAGE_RATING'), 'cat_date'=>Text::_('COM_XBFILMS_LAST_SEEN'),'lastseen'=>'last seen',
    'category_title'=>Text::_('XBCULTURE_CATEGORY'));

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
<div class="xbfilms">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbfilmsHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_xbfilms&view=filmlist'); ?>" method="post" name="adminForm" id="adminForm">       
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ((!$this->show_cat) || ($this->hide_cat)) { $hide .= 'filter_category_id, filter_subcats,';}
				if ($this->hide_peep) { $hide .= 'filter_perfilt,filter_prole,';}
				if ($this->hide_char) { $hide .= 'filter_charfilt,';}
				if ((!$this->show_tags) || $this->hide_tag) { $hide .= 'filter_tagfilt,filter_taglogic,';}
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
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>

	<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbfilmlist">	
		<thead>
			<tr>
				<?php if($this->show_pic) : ?>
					<th class="center" style="width:80px">
						<?php echo JText::_( 'COM_XBFILMS_POSTER' ); ?>
					</th>	
                <?php endif; ?>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_TITLE','title',$listDirn,$listOrder).				
    						', '.JText::_('XBCULTURE_CAPDIRECTOR').', '.
    						HTMLHelper::_('searchtools.sort','COM_XBFILMS_RELYEARCOL','rel_year',$listDirn,$listOrder );
					?>
				</th>					
				<?php if($this->show_sum) : ?>
    				<th class="hidden-phone">
    					<?php echo JText::_('XBCULTURE_SUMMARY');?>
    				</th>
                <?php endif; ?>
                <?php if ($this->show_rev != 0 ) : ?>
					<th class="xbtc">
						<?php echo HTMLHelper::_('searchtools.sort','Rating','averat',$listDirn,$listOrder); ?>
					</th>
				<?php endif; ?>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Last Review','lastseen',$listDirn,$listOrder ); ?>
				</th>
				<?php if($this->show_cat || $this->show_tags) : ?>
    				<th class="hidden-tablet hidden-phone">
    					<?php if ($this->show_cat) {
    						echo HTMLHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder );
    					}
    					if (($this->show_cat) && ($this->show_tags)) {
    					    echo ' &amp; ';
    					}
    					if($this->show_tags) {
    					    echo Text::_( 'COM_XBFILMS_CAPTAGS' ); 
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
								<img class="img-polaroid hasTooltip xbimgthumb" title="" 
									data-original-title="<?php echo $tip; ?>" data-placement="right"
									src="<?php echo $src; ?>"
									border="0" alt="" />							                          
	                    	<?php  endif; ?>	                    
						</td>
                    <?php endif; ?>
					<td>
						<p class="xbtitle">
							<a href="<?php echo JRoute::_($blink.$item->id);?>" >
								<b><?php echo $this->escape($item->title); ?></b></a></p> 
						<?php if (!empty($item->subtitle)) :?>
                        	<p><?php echo $this->escape($item->subtitle); ?></p>
                        <?php endif; ?>
						<p>
                        	<?php if ($item->dircnt==0) {
                        		echo '<span class="xbnit">'.JText::_('COM_XBFILMS_NODIRECTOR').'</span>';
                        	} else { ?> 
	                        	<span class="xbnit">
	                        		<?php echo JText::_($item->dircnt>1 ? 'XBCULTURE_CAPDIRECTORS' : 'XBCULTURE_CAPDIRECTOR' ); ?>
	                        	</span>: 
                        		<?php echo $item->alist; 
                        	} ?>                          	
						</br>
						<span class="xb09">
							<?php if($item->rel_year > 0) {
								echo '<span class="xbnit">'.JText::_('COM_XBFILMS_CAPRELEASED').'</span>: '.$item->rel_year.'<br />'; 
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
        								<span class="xbnit"><?php echo Text::_('COM_XBFILMS_SYNOPSIS_EXTRACT'); ?>: </span>
        								<?php echo XbcultureHelper::makeSummaryText($item->synopsis,0); ?>
        							<?php else : ?>
        							<span class="xbnote">
        								<?php echo Text::_('COM_XBFILMS_NO_SUMMARY_SYNOPSIS'); ?>
        							</span></span>
        							<?php endif; ?>
        						<?php endif; ?>
                            </p>
                            <?php if (!empty($item->synopsis)) : ?>
                            	<p class="xbnit xb09">   
                                 <?php 
                                 	echo Text::_('COM_XBFILMS_FULLSYNOPSIS').' '.str_word_count(strip_tags($item->synopsis)).' '.Text::_('XBCULTURE_WORDS'); 
                                 ?>
    							</p>
    						<?php endif; ?>
    					</td>
					<?php endif; ?>
					<?php if ($this->show_rev != 0 ) : ?>
    					<td>
    						<?php if ($item->revcnt==0) : ?>
    						   <i><?php  echo ($this->show_rev == 1)? JText::_( 'Not rated yet' ) : JText::_( 'COM_XBFILMS_NOREVIEW' ); ?></i><br />
    						<?php else : ?> 
	                        	<?php $stars = (round(($item->averat)*2)/2); ?>
	                            <div class="xbstar">
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
                                    	<?php $poptip = (empty($rev->summary)) ? 'hasTooltip' : 'hasPopover'; ?> 
										<div class="<?php echo $poptip; ?> xbmb8 xb09"  title 
											data-content="<?php echo htmlentities($rev->summary); ?>"
											data-original-title="<?php echo htmlentities($rev->title); ?>" 
                                		>
    										<?php if ($item->revcnt>1) : ?>
    											<?php echo $rev->rating;?><span class="xbstar"><i class="<?php echo $this->star_class; ?>"></i></span> 
    			                            <?php endif; ?>
    			                            <a href="<?php echo JRoute::_($rlink.$rev->id); ?>">
	    	                                	<i>by</i> <?php echo $rev->reviewer; ?> 
	    	                                	<i>on</i> <?php  echo HtmlHelper::date($rev->rev_date , Text::_('d M Y')); ?>
    			                            </a>
        								</div>
        							<?php endforeach; ?> 
        						<?php endif; ?>
     						<?php endif; ?>   											
    					</td>
    				<?php endif; ?>
    				<td>
    					<p><?php if($item->lastseen=='') {
    						echo '<span class="xbnit">(catalogued)<br />('.HtmlHelper::date($item->cat_date , Text::_('d M Y')).')</span>';
    					} else {
    						echo HtmlHelper::date($item->lastseen , Text::_('d M Y')); 
    					}?> </p>
     				</td>
    				<?php if($this->show_cat || $this->show_tags) : ?>
    					<td class="hidden-phone">
     						<?php if($this->show_cat) : ?>	
     							<p>
     							<?php if($this->show_cat==2) : ?>											
    								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>"><?php echo $item->category_title; ?></a>
    							<?php else: ?>
    								<span class="label label-success"><?php echo $item->category_title; ?></span>
    							<?php endif; ?>
    							</p>
    						<?php endif; ?>
    						<?php if($this->show_tags) {
    							$tagLayout = new JLayoutFile('joomla.content.tags');
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
