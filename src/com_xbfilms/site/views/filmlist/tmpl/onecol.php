<?php 
/*******
 * @package xbFilms
 * @filesource site/views/filmlist/tmpl/onecol.php
 * @version 0.10.0.5 30th November 2022
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
    $orderDrn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'),'pubyear'=>Text::_('XBFILMS_YEARPUB'), 'averat'=>Text::_('XBCULTURE_AVERAGE_RATING'), 
    'first_seen'=>Text::_('XBFILMS_FIRST_SEEN'),'last_seen'=>Text::_('XBFILMS_LAST_SEEN'), 'category_title'=>Text::_('XBCULTURE_CATEGORY'));

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
	
	<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=filmlist&layout=onecol'); ?>" method="post" name="adminForm" id="adminForm">       
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ($this->hide_peep) { $hide .= 'filter_perfilt,filter_prole,';}
				if ($this->hide_char) { $hide .= 'filter_charfilt,';}
				if ($this->hide_cat) { $hide .= 'filter_category_id,filter_subcats,';}
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

	<table class="table table-hover" style="table-layout:fixed;" id="xbfilmlist">	
		<thead>
			<tr>
				<th>
					<?php echo HtmlHelper::_('searchtools.sort','XBCULTURE_TITLE','title',$listDirn,$listOrder).				
    						', '.Text::_('XBCULTURE_DIRECTOR').', '.
    						HtmlHelper::_('searchtools.sort','RelYear','rel_year',$listDirn,$listOrder ).', ';
					?>
					<?php echo HtmlHelper::_('searchtools.sort','XBFILMS_FIRST_SEEN','first_seen',$listDirn,$listOrder).', ';
					   echo HtmlHelper::_('searchtools.sort','XBFILMS_LAST_SEEN','last_seen',$listDirn,$listOrder); ?>
				</th>					
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $reviews = ''; ?>
				<tr class="xbrow<?php echo $i % 2; ?>">	
					<td>
						<h3>
							<a href="<?php echo Route::_(XbfilmsHelperRoute::getFilmLink($item->id)) ;?>" >
								<b><?php echo $this->escape($item->title); ?></b>
							</a>
    						<?php if (!empty($item->subtitle)) :?>
                            	<br /><span class="xb08" style="padding-left:15px;"><?php echo $this->escape($item->subtitle); ?></span>
                            <?php endif; ?>
						</h3>
						<table>
						<tr class="xbrow<?php echo $i % 2; ?>">
                  		<?php if($this->show_pic) : ?>
                  			<td style="width:100px;padding-right:20px;">
    							<?php  $src = trim($item->poster_img);
    							if ((!$src=='') && (file_exists(JPATH_ROOT.'/'.$src))) : 
    								$src = Uri::root().$src; 
    								$tip = '<img src=\''.$src.'\' style=\'max-width:250px;\' />'; ?>
    								<img class="img-polaroid hasPopover" title="" 
    									data-original-title="" data-content="<?php echo $tip; ?> data-placement="right"
    									src="<?php echo $src; ?>" border="0" alt="" 
    								/>
    	                  		<?php  endif; ?>
                          </td>   
                        <?php endif; ?>
                        <td>
						<p><span class="<?php echo ($item->dircnt>1) ? 'icon-users' : 'icon-user'; ?>"></span>&nbsp;
                        	<?php if ($item->dircnt==0) {
                        		echo '<span class="xbnit">'.Text::_('XBFILMS_NODIRECTOR').'</span>';
                        	} else { ?> 
	                        	<span class="xbnit">
	                        		<?php echo $item->dircnt>1 ? Text::_('XBCULTURE_DIRECTORS') : Text::_('XBCULTURE_DIRECTOR' ); ?>
	                        	</span>: 
                        		<?php echo $item->dirlist; 
                        	} ?>                          	
							<br />
							<?php if ($item->prodcnt >0 ) : ?>
	                        	<span class="xbnit">
	                        		<?php echo $item->prodcnt>1 ? Text::_('XBCULTURE_PRODUCERS') : Text::_('XBCULTURE_PRODUCER' ); ?>
	                        	</span>: 
                        		<?php echo $item->prodlist; ?>
                        	<br />
							<?php endif; ?>
							<?php $othcnt = $item->crewcnt + $item->castcnt + $item->subjcnt;
							if ($othcnt>0) : ?>
								<span class="icon-users"></span>&nbsp;
								<span class="xbnit"><?php echo $othcnt.' '.Text::_('XBCULTURE_OTHER_PEOPLE_LISTED'); ?></span>
								<br />
							<?php endif; ?>	
							<span class="icon-calendar"></span>&nbsp;<span class="xbnit">
								<?php echo Text::_('XBFILMS_CAPRELEASED'); ?>
							</span>
							<?php if($item->rel_year > 0) { echo ': '.$item->rel_year; } else { echo '<i>'.Text::_('XBCULTURE_UNKNOWN').'</i>';}?>	
							<br />
							<span class="icon-screen"></span>&nbsp;
                            <?php if($this->show_sum) : ?>
    							<?php if (!empty($item->summary)) : ?>
    								<?php echo '<i>'.Text::_('XBCULTURE_SUMMARY').'</i>: '.$item->summary; ?>
        						<?php else : ?>
        							<span class="xbnit">
        							<?php if (!empty($item->synopsis)) : ?>
        								<?php echo Text::_('XBFILMS_SYNOPSIS_EXTRACT'); ?>: </span>
        								<?php echo XbcultureHelper::makeSummaryText($item->synopsis,250); ?>
        							<?php else : ?>
                						<span class="xbnote">
        								<?php echo Text::_('XBFILMS_NO_SUMMARY_SYNOPSIS'); ?>
        								</span></span>
        							<?php endif; ?>
        						<?php endif; ?>
                                <?php if (!empty($item->synopsis)) : ?>
                                	&nbsp;<span class="xbnit xb09">   
                                     <?php 
                                     	echo Text::_('XBCULTURE_SYNOPSIS').' '.str_word_count(strip_tags($item->synopsis)).' '.Text::_('XBCULTURE_WORDS'); 
                                     ?>
                                     </span>
        						<?php endif; ?>
                        	<?php endif; ?>
							<br />	
							<?php if($this->show_rev) : ?>
								<span class="icon-pencil-2"></span> &nbsp;
								<?php if ($item->revcnt==0) : ?>
									<i><?php echo Text::_('XBCULTURE_NO_REVIEWS_AVAILABLE'); ?></i>
								<?php else : ?>
									<i>
								    <?php if($item->revcnt==1) {
								        echo $item->revcnt.' '.Text::_('XBCULTURE_REVIEW_RATING');
								    } else {
								        echo $item->revcnt.' '.Text::_('XBCULTURE_REVIEWS_AVE_RATING');
								    } ?>
									</i> &nbsp;
								    <?php $stars = (round(($item->averat)*2)/2); 
								    if (($this->zero_rating) && ($stars==0)) : ?>
    								    <span class="<?php echo $this->zero_class; ?>" style="color:red;"></span>
    								<?php else : 
    								    echo str_repeat('<i class="'.$this->star_class.'"></i>',intval($item->averat)); 
    								    if (($item->averat - floor($item->averat))>0) : ?>
    	                                    <i class="<?php echo $this->halfstar_class; ?>"></i>
    	                                    <span style="color:darkgray;"> (<?php echo round($item->averat,1); ?>)</span>                                   
    	                                <?php  endif; ?> 
    	                             <?php endif; ?>                        								    
								<?php endif; ?>
								<br />
							<?php endif; ?>					
		                    <?php if(($this->showcat) || ($this->showtags)) : ?>
         						<?php if($this->showcat) : ?>	
		     						<span class="icon-folder"></span> &nbsp;	
         							<?php if($this->showcat==2) : ?>											
        								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>"><?php echo $item->category_title; ?></a>
        							<?php else: ?>
        								<span class="label label-success"><?php echo $item->category_title; ?></span>
        							<?php endif; ?>
        						<?php endif; ?>
        						<?php if($this->showtags) : ?>
        							<br />
        						    <span class="icon-tags"></span> &nbsp;
        							<?php $tagLayout = new FileLayout('joomla.content.tagline');
            						echo $tagLayout->render($item->tags); ?>
        						<?php endif; ?>
	                		<?php endif; ?>
	                		<?php if ($this->show_fdates) : ?>       				
        						<br />
        						<?php if($item->first_seen) {
        						    $datefmt = xbCultureHelper::getDateFmt($item->first_seen, 'D jS M Y');
        						    echo '<span class="icon-eye"></span> &nbsp;<i>'.Text::_('XBFILMS_FIRST_SEEN').'</i>: '.HtmlHelper::date($item->first_seen , $datefmt); 
								}
        					   if(($item->last_seen) && ($item->last_seen != $item->first_seen)) {
        					       $datefmt = xbCultureHelper::getDateFmt($item->last_seen, 'D jS M Y');
        					       echo ' -&nbsp;<i>'.Text::_('XBFILMS_LAST_SEEN').'</i>: '.HtmlHelper::date($item->last_seen , $datefmt); 
        					   }
        					   if((!$item->last_seen) && (!$item->first_seen)) {
        					       echo '<i>'.Text::_('XBFILMS_NOT_SEEN_YET').'</i>';
        					   }
        					?>
							<?php endif; ?>
	                	</p>
						</td></tr></table>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>
	<?php endif; ?>
	<?php echo HtmlHelper::_('form.token'); ?>
      </div>
      </div>
</form>
</div>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>

