<?php 
/*******
 * @package xbFilms
 * @filesource site/views/people/tmpl/compact.php
 * @version 1.0.3.14 17th February 2023
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
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='lastname';
    $listDirn = 'ascending';
}
$orderNames = array('lastname'=>Text::_('XBCULTURE_LASTNAME'),'firstname'=>Text::_('XBCULTURE_FIRSTNAME'),
		'sortdate'=>Text::_('XBCULTURE_DATES'),'category_title'=>Text::_('XBCULTURE_CATEGORY'),'fcnt'=>'Number of films');

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getPeopleRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = 'index.php?option=com_xbpeople&view=person' . $itemid.'&id=';

$itemid = XbfilmsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbpeople&view=category' . $itemid.'&id=';

?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>
<div class="xbculture">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=people&layout=compact'); ?>" method="post" name="adminForm" id="adminForm">
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ($this->hide_prole) { $hide .= 'filter_prole,';}
				if ((!$this->showcat) || ($this->hide_cat)) { $hide .= 'filter_category_id,filter_subcats,';}
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
	<?php if (empty($this->items)) { ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php } else { ?>
		<table class="table table-striped table-hover" id="xbpeople">	
    		<colgroup>
    			<col ><!-- title -->
				<?php if($this->show_pdates) : ?>
    				<col class="hidden-phone"><!-- dates -->
    			<?php endif; ?>
                <?php if ($this->showcnts) : ?>
    				<col><!-- books -->
    			<?php endif; ?>
    			<?php if($this->showcat) : ?>
    				<col class="hidden-tablet hidden-phone"><!-- cats&tags -->
    			<?php endif; ?>
    			<?php if($this->showtags) : ?>
    				<col class="hidden-tablet hidden-phone"><!-- cats&tags -->
    			<?php endif; ?>
    		</colgroup>
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Firstname','firstname',$listDirn,$listOrder).' '.
						HTMLHelper::_('searchtools.sort','Lastname','lastname',$listDirn,$listOrder)  ?>
				</th>					
				<?php if($this->show_pdates) : ?>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','Dates','sortdate',$listDirn,$listOrder); ?>
					</th>
                <?php endif; ?>
                <?php if ($this->showcnts) : ?>
    				<th>
    					<?php echo HTMLHelper::_('searchtools.sort','Films','fcnt',$listDirn,$listOrder); ?>
    				</th>
				<?php endif; ?>
				<?php if ($this->showcat) : ?>
    				<th>
    					<?php echo HtmlHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder ); ?>
					</th>
    			<?php endif; ?>
				<?php if ($this->showtags) : ?>
    				<th>
    					<?php echo ucfirst(Text::_( 'XBCULTURE_TAGS'));  ?>                
    				</th>
    			<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
				<td>
					<p class="xbtitlelist">
						<a href="<?php echo Route::_($plink.$item->id);?>" >
							<b><?php echo $this->escape($item->firstname).' '.$this->escape($item->lastname); ?></b>
						</a>&nbsp;<a href="#ajax-xbmodal" data-toggle="modal" data-target="#ajax-xbmodal" data-backdrop="static"  
							onclick="window.com='people';window.view='person';window.pvid=<?php echo $item->id; ?>;
							"><i class="far fa-eye"></i></a>					
					</p>
				</td>
				<?php if($this->show_pdates) : ?>
					<td class="hidden-phone">
						<p><?php if ($item->year_born != 0) {						
								echo $item->year_born; 
							}
							if ($item->year_died != 0) {						
								echo ($item->year_born == 0) ? '???? - ': ' - ';
								echo $item->year_died; 
							}              
						?></p>
					</td>
				<?php endif; ?>
                <?php if ($this->showcnts) : ?>
    				<td>
    					<details>
    						<summary><span class="xbnit">
								<?php echo $item->fcnt.' ';
								    echo $item->fcnt ==1 ? Text::_('XBCULTURE_FILM') : Text::_('XBCULTURE_FILMS'); ?>       					
    							<?php if ($item->frolecnt > $item->fcnt ) : ?>
             					    <span class="xbit xbnorm"> (
             					    	<?php echo $item->frolecnt.' '.Text::_('XBCULTURE_ROLES');?>
             					    )</span>
             					<?php endif; ?>
    						</span></summary>
    						<?php echo $item->filmlist['ullist']; ?>    						
    					</details>   				
 					</td>
				<?php endif; ?>
				<?php if ($this->showcat) : ?>												
					<td class="hidden-phone">
						<?php if($this->showcat == 2) : ?>
							<a class="label label-success" href="<?php echo $clink.$item->catid; ?>">
								<?php  echo $item->category_title; ?></a>		
						<?php else: ?>
							<span class="label label-success"><?php  echo $item->category_title; ?></span>
						<?php endif; ?>
					</td>
				<?php endif; ?>
				<?php if ($this->showtags) : ?>	
					<td>
						<?php  $tagLayout = new FileLayout('joomla.content.tags');
    						echo $tagLayout->render($item->tags);?>
					</td>
				<?php endif; ?>	
				</tr>
			<?php } // endforeach; ?>
		</tbody>
		</table>
	<?php echo $this->pagination->getListFooter(); ?>
	<?php } //endif; ?>
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	</div>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
</div>

<?php echo LayoutHelper::render('xbculture.layoutpvmodal', array(), JPATH_ROOT .'/components/com_xbpeople/layouts');   ?>



