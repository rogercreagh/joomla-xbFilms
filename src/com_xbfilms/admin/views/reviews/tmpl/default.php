<?php
/*******
 * @package xbFilms
 * @filesource admin/views/reviews/tmpl/default.php
 * @version 1.0.3.14 17th February 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('XBCULTURE_SELECT_TAGS')));
HTMLHelper::_('formbehavior.chosen', '.multipleCats', null, array('placeholder_text_multiple' => Text::_('XBCULTURE_SELECT_CATS')));
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
	$listOrder='rev_date';
	$listDirn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'),'filmtitle'=>Text::_('XBFILMS_FILMTITLE'),
		'id'=>'id','rev_date'=>Text::_('XBCULTURE_DATES'),'rating'=>Text::_('XBCULTURE_RATING'),
    'category_title'=>Text::_('XBCULTURE_CATEGORY'),'a.created'=>Text::_('XBCULTURE_DATE_ADDED'),
		'published'=>Text::_('XBCULTURE_STATUS'),'a.ordering'=>Text::_('XBCULTURE_ORDERING'));

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbfilms.film');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_xbfilms&task=reviews.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'xbfilmreviewsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$fvlink = 'index.php?option=com_xbfilms&view=film&task=film.edit&id='; //change this to view view when available
$relink = 'index.php?option=com_xbfilms&view=review&task=review.edit&id=';
$cvlink = 'index.php?option=com_xbfilms&view=fcategory&id=';
$tvlink = 'index.php?option=com_xbfilms&view=tag&id=';

?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>
<form action="index.php?option=com_xbfilms&view=reviews" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container">
	<?php endif;?>
 	<div class="pull-right span6 xbtr xbm0">
 			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. Text::_(($fnd==1)?'XBCULTURE_REVIEW':'XBCULTURE_REVIEWS').' '.Text::_('XBCULTURE_FOUND').', ';
			?>
            <?php echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
	</div>
	<div class="clearfix"></div>
    <div class="pull-right pagination xbm0" style="padding-left:10px;">
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
	<?php // Search tools bar
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>
	<?php $search = $this->searchTitle; ?>
	<?php if ($search) {
		echo '<p>Searched for <b>'; 
		if (stripos($search, 'i:') === 0) {
            echo trim(substr($search, 2)).'</b> '.Text::_('XBCULTURE_AS_ID');
		} elseif ((stripos($search, 's:') === 0) || (stripos($search, 'r:') === 0)) {
            echo trim(substr($search, 2)).'</b> '.Text::_('XBFILMS_AS_INSUMREV');
        } else {
			echo trim($search).'</b> '.Text::_('XBFILMS_AS_INTITLE');
		}
		echo '</p>';
	} ?> 

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	
	<table class="table table-striped table-hover" id="xbfilmreviewsList">
			<colgroup>
				<col class="hiddem-phone" style="width:25px;"><!-- ordering -->
				<col class="hiddem-phone" style="width:25px;"><!-- checkbox -->
				<col style="width:55px;"><!-- status -->
				<col ><!-- title -->
				<col ><!-- film, date -->
				<col style="width:105px;"><!-- rating -->
				<col class="hiddem-phone"style="width:230px;" ><!-- summary, extlinks -->
				<col class="hidden-tablet hidden-phone" style="width:230px;"><!-- cats & tags -->
				<col class="hiddem-phone" style="width:45px;"><!-- id -->
			</colgroup>	
		<thead>
			<tr>
				<th class="nowrap center">
					<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', 
					    $listDirn, $listOrder, null, 'asc', 'XBCULTURE_HEADING_ORDERING_DESC', 'icon-menu-2'); ?>
				</th>
        		<th>
        			<?php echo HTMLHelper::_('grid.checkall'); ?>
        		</th>
        		<th class="nowrap center">
        			<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
        		</th>
        		<th>
        			<?php echo HTMLHelper::_('searchtools.sort', 'XBCULTURE_TITLE', 'title', $listDirn, $listOrder); ?>
        		</th>
        		<th>
        			<?php echo HTMLHelper::_('searchtools.sort', 'XBFILMS_REVIEW_CAPFILM', 'filmtitle', $listDirn, $listOrder); ?>
        			&amp; 
        			<?php echo HTMLHelper::_('searchtools.sort', 'date seen', 'rev_date', $listDirn, $listOrder); ?>
        		</th>
        		<th>
        			<?php echo HTMLHelper::_('searchtools.sort', 'XBCULTURE_RATING', 'rating', $listDirn, $listOrder); ?>
        		</th>
        		<th class="hidden-phone">
        			<?php echo Text::_('XBFILMS_REVIEW_SUMMARY_LABEL');?>
        		</th>
 					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_CATS','category_title',$listDirn,$listOrder ).' &amp; '.Text::_( 'XBCULTURE_TAGS_U' ); ?>
					</th>
        		
        		<th class="nowrap">
        			<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
        		</th>
        	</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php if (!empty($this->items)) : ?>
				<?php $star='<span class="icon-star" style="margin-right:0; width:10px; font-size:11px;"></span>';
				?>
				<?php foreach ($this->items as $i => $item) :
    				$canEdit    = $user->authorise('core.edit', 'com_xbfilms.film.'.$item->id);
    				$canCheckin = $user->authorise('core.manage', 'com_checkin')
    				|| $item->checked_out==$userId || $item->checked_out==0;
    				$canEditOwn = $user->authorise('core.edit.own', 'com_xbfilms.film.'.$item->id) && $item->created_by == $userId;
    				$canChange  = $user->authorise('core.edit.state', 'com_xbfilms.film.'.$item->id) && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td class="order nowrap center hidden-phone">
                        <?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            } elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
                            }
                        ?>
                        <span class="sortable-handler<?php echo $iconClass; ?>">
                        	<span class="icon-menu" aria-hidden="true"></span>
                        </span>
                        <?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                        <?php endif; ?>
					</td>
						<td>
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'review.', true, 'cb'); ?>
								<?php if ($item->note!=''){ ?>
									<span class="btn btn-micro active hasTooltip" title="" 
										data-original-title="<?php echo '<b>'.Text::_( 'XBCULTURE_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
										<i class="icon- xbinfo"></i>
									</span>
								<?php } else {?>
									<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
								<?php } ?>
							</div>
						</td>
						<td>
    						<p class="xbtitlelist">
    						<?php if ($item->checked_out) {
    						    $couname = Factory::getUser($item->checked_out)->username;
    						    echo HTMLHelper::_('jgrid.checkedout', $i, Text::_('XBCULTURE_OPENED_BY').': '.$couname, $item->checked_out_time, 'review.', $canCheckin);
    						} ?>
    						<a href="<?php echo Route::_($relink . $item->id); ?>" title="<?php echo Text::_('XBFILMS_EDIT_REVIEW'); ?>">
    							<?php echo $item->title; ?></a>&nbsp;<a href="#ajax-xbmodal" 
								data-toggle="modal" data-target="#ajax-xbmodal" data-backdrop="static" 
								onclick="window.com='films';window.view='filmreview';window.pvid=<?php echo $item->id; ?>;"><i class="far fa-eye"></i></a>
    						<br /><span class="xb08 xbnorm"><i><?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></i></span>
    						</p>
							<br /><span class="xb08 xbnorm"><i>reviewed by: </i><?php echo ''.$item->reviewer.'</span>'; ?> 
							</p>						
						</td>
						<td><?php if ($item->filmtitle == '') :  ?>
							<p class="xbnote">Film not found - orphan review</p>
							<?php  else : ?>
								<p><a href="<?php echo Route::_($fvlink . $item->filmid); ?>">
	    							<?php echo $item->filmtitle; ?></a>&nbsp;<a href="#ajax-xbmodal"
            							data-toggle="modal" data-backdrop="static" data-target="#ajax-xbmodal" 
            							onclick="window.com='films';window.view='film';window.pvid= <?php echo $item->filmid; ?>;"><i class="far fa-eye"></i></a>					
								<br /><span class="xb09">
								<?php if ($item->subtitled) { echo '('.Text::_('XBFILMS_SUBTITLED').')<br />'; } ?>
								<?php if($item->where_seen != '') : ?>
									<i>Seen on</i>: 
									<?php echo HtmlHelper::date($item->rev_date, 'd M Y').' '.$item->where_seen; ?>
								<?php endif; ?>
								</span></p>
							<?php endif; ?>
						</td>
						<td>
							<?php 
							echo '<div style="font-size:10px;width:100%;">';
							if (($this->zero_rating) && ($item->rating==0)) {
								echo '<span class="'.$this->zero_class.' xbzero16"></span>';
							} else {
								echo str_repeat('&#11088',$item->rating);
							} 
							echo '</div>';
							?>
						</td>
						<td class="hidden-phone">
						<p class="xb095">
							<?php if (!empty($item->summary)) : ?>
								<?php echo $item->summary; ?>
    						<?php else : ?>
    							<span class="xbnit">
    							<?php if (!empty($item->review)) : ?>
    								<?php echo Text::_('XBFILMS_REVIEW_EXTRACT'); ?>: </span>
    								<?php echo XbcultureHelper::makeSummaryText($item->review,250); ?>
    							<?php else : ?>
    								<?php echo Text::_('XBFILMS_NO_SUMMARY_REVIEW'); ?></span>
    							<?php endif; ?>
    						<?php endif; ?>
                        </p>
                        <?php if ((!empty($item->review)) && (strlen($item->review)>207)) : ?>
                             <p class="xbnit xb09">   
                             <?php 
                             	echo Text::_('XBFILMS_FULLREVIEW').' '.str_word_count(strip_tags($item->review)).' '.Text::_('XBCULTURE_WORDS'); 
                             ?>
							</p>
						<?php endif; ?>
                                    
						</td>
						<td>
							<p><a  class="label label-success" 	href="<?php echo $cvlink . $item->catid.'&extension=com_xbfilms'; ?>" 
								title="<?php echo Text::_( 'XBCULTURE_VIEW_CATEGORY' );?>::<?php echo $item->category_title; ?>">
									<?php echo $item->category_title; ?>
							</a></p>						
						<ul class="inline">
						<?php foreach ($item->tags as $t) : ?>
							<li><a href="<?php echo $tvlink.$t->id; ?>" class="label label-info">
								<?php echo $t->title; ?></a>
							</li>													
						<?php endforeach; ?>
						</ul>						    											
    					</td>
    					<td class="center hidden-phone">
    						<?php echo $item->id; ?>
    					</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>

<?php echo LayoutHelper::render('xbculture.layoutpvmodal', array(), JPATH_ROOT .'/components/com_xbpeople/layouts');   ?>
