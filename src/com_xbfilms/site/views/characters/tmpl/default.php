<?php 
/*******
 * @package xbFilms
 * @filesource site/views/characters/tmpl/default.php
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
    $listOrder='name';
    $orderDrn = 'asscending';
}
$orderNames = array('name'=>Text::_('XBCULTURE_NAME'),'category_title'=>Text::_('XBCULTURE_CATEGORY'));

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbpeople&view=category'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getCharsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = 'index.php?option=com_xbpeople&view=character'.$itemid.'&id=';

?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>
<div class="xbculture">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=characters'); ?>" method="post" name="adminForm" id="adminForm">
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ((!$this->showcat) || ($this->hide_cat)) { $hide .= 'filter_category_id, filter_subcats,';}
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
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbcharacters">	
    		<colgroup>
    			<?php if($this->show_pic) : ?>
    				<col style="width:80px"><!-- picture -->
                <?php endif; ?>
    			<col ><!-- title -->
    			<?php if($this->show_sum) : ?>
    				<col class="hidden-phone" style="width:230px;"><!-- summary -->
                <?php endif; ?>
                <?php if ($this->showccnts) : ?>
    				<col class="hidden-phone"><!-- rating -->
    			<?php endif; ?>
    			<?php if($this->showcat || $this->showtags) : ?>
    				<col class="hidden-tablet hidden-phone"><!-- cats&tags -->
    			<?php endif; ?>
    		</colgroup>
		<thead>
			<tr>
				<?php if($this->show_pic) : ?>
					<th class="center">
						<?php echo Text::_( 'XBFILMS_CAPPICTURE' ); ?>
					</th>	
                <?php endif; ?>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Name','name',$listDirn,$listOrder); ?>
				</th>					
				<?php if($this->show_sum) : ?>
    				<th>
    					<?php echo Text::_('XBCULTURE_SUMMARY');?>
    				</th>
                <?php endif; ?>
               <?php if ($this->showccnts) : ?>
    				<th>
    					<?php echo HTMLHelper::_('searchtools.sort','Films','fcnt',$listDirn,$listOrder); ?>
    				</th>
                <?php endif; ?>
				<?php if($this->showcat || $this->showtags) : ?>
    				<th>
    					<?php if ($this->showcat) {
    						echo HTMLHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder ).' &amp; ';
    					}
    					if (($this->showcat) && ($this->showtags)) {
    					    echo ' &amp; ';
    					}
    					if($this->showtags) {
    					    echo Text::_( 'XBCULTURE_TAGS' ); 
    					} ?>                
    				</th>
                <?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
				<tr class="row<?php echo $i % 2; ?>">
				<?php if($this->show_pic) : ?>
					<td>
	 					<?php $src = trim($item->image);
							if ((!$src=='') && (file_exists(JPATH_ROOT.'/'.$src))) :
								$src = Uri::root().$src;
								$tip = '<img src=\''.$src.'\' style=\'max-width:250px;\' />';
							?>
							<img class="img-polaroid hasTooltip xbimgthumb" title="" 
        							data-original-title="<?php echo $tip; ?>"
        							src="<?php echo $src; ?>"
        							border="0" alt="" />						
	                  		<?php  endif; ?>	                    
					</td>
                <?php endif; ?>
				<td>
					<p class="xbtitlelist">
						<a href="<?php echo Route::_($plink.$item->id);?>" >
							<b><?php echo $this->escape($item->name); ?></b>
						</a>&nbsp;<a href="#ajax-xbmodal" data-toggle="modal" data-target="#ajax-xbmodal" data-backdrop="static"  
							onclick="window.com='people';window.view='character';window.pvid=<?php echo $item->id; ?>;"
							><i class="far fa-eye"></i></a>					
					</p>
				</td>
				<?php if($this->show_sum) : ?>
				<td>
					<p class="xb095">
						<?php if (!empty($item->summary)) : ?>
							<?php echo $item->summary; ?>
    					<?php else : ?>
    						<?php if (!empty($item->description)) : ?>
    							<?php echo XbcultureHelper::makeSummaryText($item->description,0); ?>
    						<?php else : ?>
    							<span class="xbnit xb09"><?php echo Text::_('XBCULTURE_NO_DESCRIPTION'); ?></span>
    						<?php endif; ?>
    					<?php endif; ?>
                    </p>
                    <?php if (!empty($item->description)) : ?>
                        <p class="xbnit xb09">   
                             <?php 
                             echo Text::_('XBCULTURE_DESCRIPTION').' '.str_word_count(strip_tags($item->description)).' '.Text::_('XBCULTURE_WORDS'); 
                            ?>
						</p>
					<?php endif; ?>
				</td>
                <?php endif; ?>
                <?php if ($this->showccnts) : ?>
    				<td>
						<?php if ($item->fcnt>0) :?>
    					<details>
    						<summary><span class="xbnit">
								<?php echo $item->fcnt.' ';
								    echo $item->fcnt ==1 ? Text::_('XBCULTURE_FILM') : Text::_('XBCULTURE_FILMS'); ?>       					
    						</span></summary>
    						<?php echo $item->filmlist['ullist']; ?>    						
    					</details>
    					<?php endif; ?>
	    				<?php if ($item->bcnt > 0) : ?>
    						<p class="xbit xb095 xbmt10">
    							<?php echo Text::_('XBCULTURE_ALSO').' '.$item->bcnt.' '; 
    						  echo $item->bcnt == 1 ? Text::_('XBCULTURE_BOOK') :Text::_('XBCULTURE_BOOKS'); ?>
    						  </p>
    					<?php endif; ?>

    				</td>
				<?php endif; ?>
    			<?php if(($this->showcat) || ($this->showtags)) : ?>
					<td class="hidden-phone">
 						<?php if (($this->showcat) && ($this->xbpeople_ok)) : ?>												
							<p>
								<?php if($this->showcat == 2) : ?>
    								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>">
    									<?php  echo $item->category_title; ?></a>		
    							<?php else: ?>
    								<span class="label label-success"><?php  echo $item->category_title; ?></span>
								<?php endif; ?>
							</p>
						<?php endif; ?>
						<?php if($this->showtags) : ?>
    						<?php  $tagLayout = new FileLayout('joomla.content.tags');
        							echo $tagLayout->render($item->tags);?>
    					<?php endif; ?>
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


