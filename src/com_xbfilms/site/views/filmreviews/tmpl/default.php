<?php
/*******
 * @package xbFilms
 * @filesource site/views/filmreviews/tmpl/default.php
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
$orderNames = array('title'=>Text::_('XBCULTURE_TITLE'),'filmtitle'=>Text::_('XBFILMS_FILM_TITLE'),
		'id'=>'id','rev_date'=>Text::_('XBCULTURE_REVIEW_DATE'),'category_title'=>Text::_('XBCULTURE_CATEGORY'),
		'published'=>Text::_('XBCULTURE_STATUS'),'ordering'=>Text::_('XBCULTURE_ORDERING'),
		'rating'=>Text::_('XBCULTURE_RATING'),'a.created'=>Text::_('XBCULTURE_DATE_ADDED')
);

$fvlink = 'index.php?option=com_xbfilms&view=film&id=';
$rvlink = 'index.php?option=com_xbfilms&view=filmreview&id=';
$cvlink = 'index.php?option=com_xbfilms&view=category&id=';
$tvlink = 'index.php?option=com_xbfilms&view=tag&id=';

?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>
<div class="xbculture ">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=filmreviews'); ?>" method="post" id="adminForm" name="adminForm">
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
		if ($this->search_bar) {
		    $hide = '';
		    if ($this->hide_cat) { $hide .= 'filter_category_id,';}
		    if ($this->hide_tag) { $hide .= 'filter_tagfilt,filter_taglogic,';}
		    echo '<div class="row-fluid"><div class="span12">';
		    echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this,'hide'=>$hide));
		}
	?>
	<div class="clearfix"></div>
	<?php //$search = strip_tags($this->searchTitle); ?>

	<?php //if ($search) {
		//echo '<p>Searched for <b>'; 
		//if (stripos($search, 'i:') === 0) {
        //    echo trim(substr($search, 2)).'</b> '.Text::_('XBCULTURE_AS_ID');
		//} elseif ((stripos($search, 's:') === 0) || (stripos($search, 'r:') === 0)) {
        //    echo trim(substr($search, 2)).'</b> '.Text::_('XBCULTURE_IN_REVIEWS');
        //} else {
		//	echo trim($search).'</b> '.Text::_('XBCULTURE_IN_TITLE');
		//}
		//echo '</p>';
	//} ?> 

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	
	<table class="table table-striped table-hover" id="xbfilmreviewsList">
		<colgroup>
			<?php if($this->show_pic) : ?>
				<col style="width:80px"><!-- picture -->
            <?php endif; ?>
			<col ><!-- title -->
			<col ><!-- book -->
			<col style="width:150px;"><!-- rating -->
			<?php if($this->show_sum) : ?>
				<col class="hidden-phone" style="width:230px;"><!-- summary -->
            <?php endif; ?>
			<?php if($this->showcat || $this->showtags) : ?>
				<col class="hidden-tablet hidden-phone"><!-- cats&tags -->
			<?php endif; ?>
		</colgroup>
		<thead>
			<tr>
				<?php if($this->show_pic) : ?>
					<th class="center" style="width:80px">
						<?php echo Text::_( 'XBFILMS_POSTER' ); ?>
					</th>	
                <?php endif; ?>
        		<th>
        			<?php echo HTMLHelper::_('searchtools.sort', 'Review Title', 'title', $listDirn, $listOrder); ?>
        		</th>
        		<th>
        			<?php echo HTMLHelper::_('searchtools.sort', 'XBCULTURE_FILM_U', 'filmtitle', $listDirn, $listOrder); ?>
        		</th>
        		<th>
        			<?php echo HTMLHelper::_('searchtools.sort', 'XBCULTURE_RATING', 'rating', $listDirn, $listOrder); ?>
        			<br />
        			<?php echo HTMLHelper::_('searchtools.sort', 'XBCULTURE_DATE', 'rev_date', $listDirn, $listOrder); ?>
        		</th>
        		<th class="hidden-phone">
        			<?php echo Text::_('XBCULTURE_REVIEW_SUMMARY');?>
        		</th>
				<th class="hidden-tablet hidden-phone" style="width:15%;">
					<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder ).'<br />'.
					Text::_( 'XBCULTURE_TAGS_U' ); ?>
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
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
              		<?php if($this->show_pic) : ?>
						<td>
						<?php  $src = trim($item->picture);
							if ((!$src=='') && (file_exists(JPATH_ROOT.'/'.$src))) : 
								$src = Uri::root().$src; 
								$tip = '<img src=\''.$src.'\' style=\'max-width:250px;\' />'; 
								?>
								<img class="img-polaroid hasTooltip xbimgthumb" title="" 
									data-original-title="<?php echo $tip; ?>" data-placement="right"
									src="<?php echo $src; ?>" border="0" alt="" />							                          
	                    	<?php  endif; ?>	                    
						</td>
                    <?php endif; ?>
						<td>
    						<p class="xbtitlelist">
    						<a href="<?php echo Route::_($rvlink . $item->id); ?>" title="<?php echo Text::_('XBCULTURE_REVIEW'); ?>">
    							<?php echo $item->title; ?>
    						</a>&nbsp;<a href="#ajax-xbmodal" data-toggle="modal" data-target="#ajax-xbmodal" data-backdrop="static" 
    							onclick="window.com='films';window.view='filmreview';window.pvid= <?php echo $item->id; ?>;">
                				<i class="far fa-eye"></i>
                			</a>					
    						</p>
							<p>
								<i>by: </i><b><?php echo $item->reviewer; ?></b> 
							</p>						
						</td>
						<td><?php if ($item->filmtitle == '') :  ?>
							<p class="xbnote">Film not found - orphan review</p>
							<?php  else : ?>
								<p><a href="<?php echo Route::_($bvlink . $item->filmid); ?>">
	    							<?php echo $item->filmtitle; ?>
								</a>&nbsp;<a href="#ajax-xbmodal" data-toggle="modal" data-target="#ajax-xbmodal" data-backdrop="static" 
									onclick="window.com='films';window.view='film';window.pvid= <?php echo $item->filmid; ?>;">
                    				<i class="far fa-eye"></i>
                    			</a>					
								</p>
							<?php endif; ?>
						</td>
						<td>
							<div style="font-size:10px;width:100%;">
							<?php if (($this->zero_rating) && ($item->rating==0)) {
									echo '<span class="'.$this->zero_class.' "></span>';
								} else {
									echo str_repeat('&#11088',$item->rating);
								} 
							 ?>
							</div>
							<p class="xb09"><i>on: </i><?php echo HtmlHelper::date($item->rev_date , 'd M Y'); ?>
							</p>
						</td>
						<td class="hidden-phone">
						<p class="xb095">
							<?php if (!empty($item->summary)) : ?>
								<?php  echo $item->summary; ?>
    						<?php else : ?>
    							<span class="xbnit">
    							<?php if (!empty($item->review)) : ?>
    								<?php echo Text::_('XBCULTURE_REVIEW_EXTRACT'); ?>: </span>
    								<?php echo XbcultureHelper::makeSummaryText($item->review,250); ?>
    							<?php else : ?>
    								<?php echo Text::_('XBCULTURE_NO_SUMMARY_DESC'); ?></span>
    							<?php endif; ?>
    						<?php endif; ?>
                        </p>
                        <?php if ((!empty($item->review)) && (strlen($item->review)>210)) : ?>
                             <p class="xbnit xb09">   
                             <?php 
                             	echo Text::_('XBCULTURE_REVIEW_FULL').' '.str_word_count(strip_tags($item->review)).' '.Text::_('XBCULTURE_WORDS'); 
                             ?>
							</p>
						<?php endif; ?>
                                    
						</td>
						<td>
    						<p><a  class="label label-success" 	href="<?php echo $cvlink . $item->catid; ?>" 
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
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
		<?php echo $this->pagination->getListFooter(); ?>
	<?php endif; ?>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>

<?php echo LayoutHelper::render('xbculture.layoutpvmodal', array(), JPATH_ROOT .'/components/com_xbpeople/layouts');   ?>

