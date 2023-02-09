<?php 
/*******
 * @package xbFilms
 * @filesource site/views/filmlist/tmpl/detailslist.php
 * @version 1.0.3.6 8th February 2023
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
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('XBCULTURE_SELECT_TAGS')));
HTMLHelper::_('formbehavior.chosen', '.multipleCats', null, array('placeholder_text_multiple' => Text::_('XBCULTURE_SELECT_CATS')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='last_seen';
    $orderDrn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'),'pubyear'=>Text::_('XBBOOKS_YEARPUB'), 'averat'=>Text::_('XBCULTURE_AVERAGE_RATING'), 
    'first_seen'=>Text::_('XBBOOKS_FIRST_READ'),'last_seen'=>Text::_('XBBOOKS_LAST_READ'), 'category_title'=>Text::_('XBCULTURE_CATEGORY'));

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
<style type="text/css" media="screen">
	.xbpvmodal .modal-content {padding:15px;max-height:calc(100vh - 190px); overflow:scroll; }
</style>
<div class="xbculture ">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=filmlist&layout=detailslist'); ?>" method="post" name="adminForm" id="adminForm">       
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ($this->hide_fict) { $hide .= 'filter_fictionfilt,';}
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

	<table class="table table-hover" style="table-layout:fixed;width:100%;" id="xbfilmlist">	
		<thead>
			<tr>
				<th>
					<?php echo HtmlHelper::_('searchtools.sort','XBCULTURE_TITLE','title',$listDirn,$listOrder).				
    						', '.Text::_('XBCULTURE_AUTHOR').', '.
    						HtmlHelper::_('searchtools.sort','XBBOOKS_PUBYEARCOL','pubyear',$listDirn,$listOrder );
					?>
					<?php echo HtmlHelper::_('searchtools.sort','First Read','first_seen',$listDirn,$listOrder).', ';
					   echo HtmlHelper::_('searchtools.sort','Last Read','last_seen',$listDirn,$listOrder); ?>
				</th>					
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $reviews = ''; ?>
				<tr class="xbrow<?php echo $i % 2; ?>">	
					<td>
						<h3>
							<a href="<?php echo Route::_(XbfilmsHelperRoute::getFilmLink($item->id)) ;?>" >
								<b><?php echo $this->escape($item->title); ?></b></a>
								&nbsp;<a href="" data-toggle="modal" data-target="#ajax-bpvmodal" data-backdrop="static" onclick="window.pvid=<?php echo $item->id; ?>;"><i class="far fa-eye"></i></a>
						<?php if (!empty($item->subtitle)) :?>
                        	<br /><span class="xb08" style="padding-left:15px;"><?php echo $this->escape($item->subtitle); ?></span>
                        <?php endif; ?>
						</h3>
						<table style="width:100%;">
						<tr>
                  		<?php if($this->show_pic) : ?>
                  			<td style="width:100px;padding-right:20px;">
    							<?php  $src = trim($item->cover_img);
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
							<i class="fas fa-user xbpr10"></i>&nbsp;
                        	<?php if ($item->authcnt==0) {
                        		echo '<span class="xbnit">'.Text::_('XBBOOKS_NOAUTHOR').'</span>';
                        	} else { ?> 
	                        	<span class="xbnit">
	                        		<?php echo $item->authcnt>1 ? Text::_('XBCULTURE_AUTHORS') : Text::_('XBCULTURE_AUTHOR' ); ?>
	                        	</span>: 
                        		<?php echo $item->authlist['commalist']; 
                        	} ?>                          	
							<br />
							<?php if ($item->editcnt >0 ) : ?>
	                        	<i class="fas fa-user-edit xbpr10"></i>&nbsp;<span class="xbnit">
	                        		<?php echo $item->editcnt>1 ? Text::_('XBCULTURE_EDITORS') : Text::_('XBCULTURE_EDITOR' ); ?>
	                        	</span>: 
                        		<?php echo $item->editlist['commalist']; ?>
                        	<br />
							<?php endif; ?>
							<?php if ($item->othercnt>0) : ?>
								<div class="pull-left"><i class="fas fa-user-friends xbpr10"></i>&nbsp;</div>
								<div class="pull-left">
								<details>
    								<summary><span class="xbnit"><?php echo $item->othercnt.' '.Text::_('XBCULTURE_OTHER_PEOPLE_LISTED'); ?></span>
    								</summary>
    								<?php echo $item->otherlist['ullist']; ?>
    							</details>
								</div>
								<div class="clearfix"></div>
							<?php endif; ?>
							<?php if ($item->mencnt>0) : ?>
								<div class="pull-left"><i class="far fa-user xbpr10"></i>&nbsp;</div>
								<div class="pull-left">
								<details>
    								<summary><span class="xbnit"><?php echo $item->mencnt.' '.Text::_('subjects of film, or mentioned in it'); ?></span>
    								</summary>
    								<?php echo $item->menlist['ullist']; ?>
    							</details>
								</div>
								<div class="clearfix"></div>
							<?php endif; ?>
							<?php if ($item->gcnt>0) : ?>
								<div class="pull-left"><i class="fas fa-users xbpr10"></i>&nbsp;</div>
								<div class="pull-left">
								<details>
    								<summary><span class="xbnit"><?php echo $item->gcnt.' '.Text::_('XBCULTURE_GROUPS'); ?></span>
    								</summary>
    								<?php echo ($item->gcnt>0) ? $item->grouplist['ullist'] : ''; ?>
    							</details>
								</div>
								<div class="clearfix"></div>
							<?php endif; ?>
							<?php if ($item->ccnt>0) : ?>
								<div class="pull-left"><i class="fas fa-theater-masks xbpr10"></i>&nbsp;</div>
								<div class="pull-left">
								<details>
    								<summary><span class="xbnit"><?php echo $item->ccnt.' '.Text::_('XBCULTURE_CHARS'); ?></span>
    								</summary>
    								<?php echo ($item->ccnt>0) ? $item->charlist['ullist'] : ''; ?>
    							</details>
								</div>
								<div class="clearfix"></div>
							<?php endif; ?>
							<span class="icon-calendar xbpr10"></span>&nbsp;<span class="xbnit">
								<?php echo Text::_('XBCULTURE_PUBLISHED'); ?>
							</span>
							<?php if($item->pubyear > 0) { echo ': '.$item->rel_year; } else { echo '<i>'.Text::_('XBCULTURE_UNKNOWN').'</i>';}?>	
							<br />
							<i class="fas fa-film xbpr10"></i>&nbsp;
                            <?php if($this->show_sum) : ?>
    							<?php if (!empty($item->summary)) : ?>
    								<?php echo '<i>'.Text::_('XBCULTURE_SUMMARY').'</i>: '.$item->summary; ?>
        						<?php else : ?>
        							<span class="xbnit">
        							<?php if (!empty($item->synopsis)) : ?>
        								<?php echo Text::_('XBCULTURE_SYNOPSIS_EXTRACT'); ?>: </span>
        								<?php echo XbcultureHelper::makeSummaryText($item->synopsis,250); ?>
        							<?php else : ?>
                						<span class="xbnote">
        								<?php echo Text::_('XBCULTURE_NO_SUMMARY_SYNOP'); ?>
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
    							<br />	
                        	<?php endif; ?>
							<?php if($this->show_revs) : ?>
								<div class="pull-left"><i class="fas fa-book-reader xbpr10"></i>&nbsp;</div>
								<div class="pull-left">
								<?php if ($item->revcnt==0) : ?>
									<i><?php echo Text::_('XBCULTURE_NO_REVIEWS_AVAILABLE'); ?></i>
								<?php else : ?>
									
								    <?php if($item->revcnt==1) : ?>
								        
								        <?php $stars = (round(($item->averat)*2)/2);
								        if (($this->zero_rating) && ($stars==0)) : ?>
    								    	<span class="<?php echo $this->zero_class; ?>" style="color:red;"></span>
    									<?php else : 
    								        echo str_repeat('<i class="'.$this->star_class.'"></i>',intval($item->averat)); 
    	                                endif;  
    	                                echo ' on '.HtmlHelper::date($item->reviews[0]->rev_date , 'd M Y');?>
    	                                &nbsp;<a href="" data-toggle="modal" data-target="#ajax-rpvmodal" data-backdrop="static" onclick="window.pvid=<?php echo $item->reviews[0]->id; ?>;"><i class="far fa-eye"></i></a> 
    	                                
								    <?php else : ?>
								        <details><summary><i>
								        <?php echo $item->revcnt.' '.Text::_('XBCULTURE_REVIEWS_AVE_RATING');?></i>								    
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
        	                             </summary>
        	                             <?php foreach ($item->reviews as $rev) : ?>
        	                                 <?php if (($this->zero_rating) && ($rev->rating==0)) : ?>
    								    		<span class="<?php echo $this->zero_class; ?>" style="color:red;"></span>
    										<?php else : 
    								            echo str_repeat('<i class="'.$this->star_class.'"></i>',$rev->rating); 
    	                                   endif;  ?>  
    	                                   <?php if (($rev->summary.$rev->review)=='') : 
    	                                       echo Text::_('Rating only on ').HtmlHelper::date($rev->rev_date , 'd M Y');    
    	                                   else :
        	                                   echo ' on '.HtmlHelper::date($rev->rev_date , 'd M Y');
    	                                       echo ' by '.$rev->reviewer; ?>
        	                                   &nbsp;
        	                                   <a href="" data-toggle="modal" data-target="#ajax-rpvmodal" data-backdrop="static" onclick="window.pvid=<?php echo $rev->id; ?>;">
        	                                   		<i class="far fa-eye"></i>
        	                                   </a> 
    	                                   <?php endif; ?>                    								    
    	                                   <br />
        	                             <?php endforeach; ?>
        	                             </details>
        	                         <?php endif; ?>                    								    
								<?php endif; ?>
								</div>
								<div class="clearfix"></div>
							<?php endif; ?>					
		                    <?php if(($this->showcat) || ($this->showtags)) : ?>
         						<?php if($this->showcat) : ?>	
		     						<i class="fas fa-folder xbpr10"></i>&nbsp;	
         							<?php if($this->showcat==2) : ?>											
        								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>"><?php echo $item->category_title; ?></a>
        							<?php else: ?>
        								<span class="label label-success"><?php echo $item->category_title; ?></span>
        							<?php endif; ?>
        						<?php endif; ?>
        						<?php if($this->showtags) : ?>
        							<br />
        						    <i class="fas fa-tags xbpr10"></i>&nbsp;
        							<?php $tagLayout = new FileLayout('joomla.content.tagline');
            						echo $tagLayout->render($item->tags); ?>
        						<?php endif; ?>
        						<br />
	                		<?php endif; ?>
	                		<?php if ($this->show_bdates) : ?> 
	                			<i class="far fa-eye xbpr10"></i>&nbsp;      				
        						<?php if($item->first_seen) {
        						    $datefmt = xbCultureHelper::getDateFmt($item->first_seen, 'D jS M Y');
        						    echo '<i>'.Text::_('XBBOOKS_FIRST_READ').'</i>: '.HtmlHelper::date($item->first_seen , $datefmt); 
								}
								if(($item->last_seen) && ($item->last_seen != $item->first_seen)) {
								    $datefmt = xbCultureHelper::getDateFmt($item->last_seen, 'D jS M Y');
								    echo ' -&nbsp;<i>'.Text::_('XBBOOKS_LAST_READ').'</i>: '.HtmlHelper::date($item->last_seen , $datefmt); 
        					   }
        					   if((!$item->last_seen) && (!$item->first_seen)) {
        					       echo '<i>'.Text::_('XBBOOKS_NOT_YET_READ').'</i>';
        					   }
        					?>
							<?php endif; ?>
	                	
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
<script>
jQuery(document).ready(function(){
//for preview modals
    // Load view vith AJAX
    jQuery('#ajax-ppvmodal').on('show', function () {
      jQuery(this).find('.modal-content').load('/index.php?option=com_xbpeople&view=person&layout=default&tmpl=component&id='+window.pvid);
    })
    jQuery('#ajax-gpvmodal').on('show', function () {
      jQuery(this).find('.modal-content').load('/index.php?option=com_xbpeople&view=group&layout=default&tmpl=component&id='+window.pvid);
    })
    jQuery('#ajax-cpvmodal').on('show', function () {
      jQuery(this).find('.modal-content').load('/index.php?option=com_xbpeople&view=character&layout=default&tmpl=component&id='+window.pvid);
    })
    jQuery('#ajax-bpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbfilms&view=film&layout=default&tmpl=component&id='+window.pvid);
    })
    jQuery('#ajax-rpvmodal').on('show', function () {
       jQuery(this).find('.modal-content').load('/index.php?option=com_xbfilms&view=filmreview&layout=default&tmpl=component&id='+window.pvid);
    })
    jQuery('#ajax-bpvmodal,#ajax-ppvmodal,#ajax-gpvmodal,#ajax-cpvmodal,#ajax-rpvmodal').on('hidden', function () {
       document.location.reload(true);
    })    
});
</script>
<!-- preview modal windows -->
<div class="modal fade xbpvmodal" id="ajax-ppvmodal" style="max-width:800px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Person</h4>
        </div>
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<div class="modal fade xbpvmodal" id="ajax-gpvmodal" style="max-width:800px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Group</h4>
        </div>
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<div class="modal fade xbpvmodal" id="ajax-cpvmodal" style="max-width:800px">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Character</h4>
        </div>
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<div class="modal fade xbpvmodal" id="ajax-bpvmodal" style="max-width:1000px">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Film</h4>
        </div>
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
<div class="modal fade xbpvmodal" id="ajax-rpvmodal" style="max-width:800px">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" 
            	style="opacity:unset;line-height:unset;border:none;">&times;</button>
             <h4 class="modal-title" style="margin:5px;">Preview Film Review</h4>
        </div>
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>
