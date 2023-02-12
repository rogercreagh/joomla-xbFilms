<?php
/*******
 * @package xbFilms
 * @filesource site/views/filmlist/tmpl/compact.php
 * @version 1.0.3.8 10th February 2023
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

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', '.multipleCats', null, array('placeholder_text_multiple' => Text::_('XBCULTURE_SELECT_CATS')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='last_seen';
    $orderDrn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'), 'averat'=>'Average Rating', 
    'first_seen'=>Text::_('XBFILMS_FIRST_SEEN'), 'last_seen'=>Text::_('XBFILMS_LAST_SEEN'), );

require_once JPATH_COMPONENT.'/helpers/route.php';
$itemid = XbfilmsHelperRoute::getFilmsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$flink = 'index.php?option=com_xbfilms&view=film'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getReviewsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$rlink = 'index.php?option=com_xbfilms&view=filmreview'.$itemid.'&id=';

?>
<style type="text/css" media="screen">
	.xbpvmodal .modal-content {padding:15px;max-height:calc(100vh - 190px); overflow:scroll; }
</style>
<div class="xbculture">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=filmlist&layout=compact'); ?>" method="post" name="adminForm" id="adminForm">       
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

	<table class="table table-striped table-hover"  id="xbfilmlist">	
		<colgroup>
			<col ><!-- title -->
			<col ><!-- director -->
            <?php if ($this->show_revs != 0 ) : ?>			
				<col class="hidden-phone" style="width:150px;" ><!-- ratings -->
            <?php endif; ?>
            <?php if ($this->show_fdates) : ?>
				<col class="hidden-phone" style="width:105px;" ><!-- seendates -->
            <?php endif; ?>
			</colgroup>		
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_TITLE','title',$listDirn,$listOrder);				
					?>
				</th>					
				<th>
					<?php echo Text::_('XBCULTURE_DIRECTOR');?>
				</th>
                <?php if ($this->show_revs != 0 ) : ?>
    				<th class="xbtc">
    					<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_RATING','averat',$listDirn,$listOrder); ?>
    				</th>
                <?php endif; ?>
                <?php if ($this->show_fdates) : ?>
    				<th>
    					<?php echo HTMLHelper::_('searchtools.sort','XBFILMS_FIRST_SEEN','first_seen',$listDirn,$listOrder ); ?><br/>
    					<?php echo HTMLHelper::_('searchtools.sort','XBFILMS_LAST_SEEN','last_seen',$listDirn,$listOrder ); ?>
    				</th>
                <?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $reviews = ''; ?>
				<tr class="row<?php echo $i % 2; ?>">	
					<td>
						<p class="xbtitle">
							<a href="<?php echo Route::_($flink.$item->id);?>" >
								<b><?php echo $this->escape($item->title); ?></b></a>&nbsp;
    						<a href="" data-toggle="modal" data-target="#ajax-fpvmodal" data-backdrop="static"  onclick="window.pvid=<?php echo $item->id; ?>;">
                				<i class="far fa-eye"></i>
                			</a>					
						<?php if (!empty($item->subtitle)) :?>
                        	<br /><span class="xb09 xbnorm"><?php echo $this->escape($item->subtitle); ?></span>
                        <?php endif; ?>
						</p>
					</td>
					<td>
						<p>
                        	<?php if ($item->dircnt==0) {
                        		echo '<span class="xbnit">'.Text::_('XBFILMS_NODIRECTOR').'</span>';
                        	} else { ?> 
                        		<?php echo $item->dirlist['commalist']; 
                        	} ?>                          	
						</p>
					</td>
					<?php if ($this->show_revs != 0 ) : ?>
    					<td>
    						<?php if ($item->revcnt==0) : ?>
    						   <i><?php  echo ($this->show_revs == 1)? Text::_( 'XBCULTURE_NO_RATING' ) : Text::_( 'XBCULTURE_NO_REVIEW' ); ?></i><br />
    						<?php else : 
                                $stars = XbcultureHelper::getStarStr($item->averat,'com_xbfilms'); ?>
         					<?php endif; ?>											
	    					<?php if($item->revcnt == 1) : ?>
    							<?php echo $stars; ?>&nbsp;	
        						<a href="" data-toggle="modal" data-target="#ajax-rpvmodal" data-backdrop="static" onclick="window.pvid=<?php echo $item->reviews[0]->id; ?>;">
                    				<i class="far fa-eye"></i>
                    			</a>					
        					<?php elseif ($item->revcnt>1) : ?> 
	                             <details>
	                             	<summary>
    	                             <?php echo $stars; ?>
    	                             <br /><span class="xbnit"><?php echo round($item->averat,1); ?>
	                             	 from <?php echo $item->revcnt; ?> Rating(s)</span>
	                             	</summary>
    	                            <?php foreach ($item->reviews as $rev) : 
    	                               $stars = XbcultureHelper::getStarStr($rev->rating,'com_xbfilms');
    	                               echo $stars; ?>
                						&nbsp;<a href="" data-toggle="modal" data-target="#ajax-rpvmodal" data-backdrop="static" onclick="window.pvid=<?php echo $rev->id; ?>;">
                            				<i class="far fa-eye"></i>
                            			</a>&nbsp;
										<br />					
    	                            <?php  endforeach; ?>
	                             </details>                                   
         					<?php endif; ?>   
    					</td>
    				<?php endif; ?>
    				<?php if ($this->show_fdates ) : ?>   				
						<td class="hidden-phone">
        					<p><?php if($item->first_seen) {
        					    $datefmt = xbCultureHelper::getDateFmt($item->first_seen,'j M \'y');
        					    echo HtmlHelper::date($item->first_seen , $datefmt);
        					}
        					   if(($item->last_seen) && ($item->last_seen != $item->first_seen)) {
        					       echo '<br />';
        					       $datefmt = xbCultureHelper::getDateFmt($item->last_seen,'j M y');
        					       echo HtmlHelper::date($item->last_seen , $datefmt);
        					   }
        					?> </p>
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
</div>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
 
<?php echo LayoutHelper::render('xbculture.modalpvlayout', array('show' => 'pfi'), JPATH_ROOT .'/components/com_xbpeople/layouts');   ?>
