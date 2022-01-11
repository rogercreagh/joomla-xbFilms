<?php 
/*******
 * @package xbFilms
 * @filesource site/views/characters/tmpl/default.php
 * @version 0.9.6.f 11th January 2022
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
$clink = 'index.php?option=com_xbfilms&view=category'.$itemid.'&id=';

$itemid = XbfilmsHelperRoute::getCharsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$plink = 'index.php?option=com_xbfilms&view=character'.$itemid.'&id=';

?>
<div class="xbfilms">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbfilmsHelper::sitePageheader($this->header);
	} ?>
	
<form action="<?php echo JRoute::_('index.php?option=com_xbfilms&view=characters'); ?>" method="post" name="adminForm" id="adminForm">
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ((!$this->show_cat) || ($this->hide_cat)) { $hide .= 'filter_category_id, filter_subcats,';}
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
		<thead>
			<tr>
				<?php if($this->show_pic) : ?>
					<th class="center" style="width:80px">
						<?php echo Text::_( 'COM_XBFILMS_CAPPICTURE' ); ?>
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
				<th class="hidden-phone">
					<?php echo Text::_('XBCULTURE_FILMS_U'); ?>
				</th>
				<?php if($this->show_cat || $this->show_tags) : ?>
    				<th class="hidden-tablet hidden-phone">
    					<?php if ($this->show_cat) {
    						echo HTMLHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder ).' &amp; ';
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
						<a href="<?php echo JRoute::_($plink.$item->id);?>" >
							<b><?php echo $this->escape($item->name); ?></b>
						</a>
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
    							<span class="xbnit xb09"><?php echo Text::_('No description available'); ?></span>
    						<?php endif; ?>
    					<?php endif; ?>
                    </p>
                    <?php if (!empty($item->description)) : ?>
                        <p class="xbnit xb09">   
                             <?php 
                             echo Text::_('Description').' '.str_word_count(strip_tags($item->description)).' '.Text::_('XBCULTURE_WORDS'); 
                            ?>
						</p>
					<?php endif; ?>
				</td>
                <?php endif; ?>
				<td class="hidden-phone" >
					<?php 
					if ($item->ccnt > 0) {
					    echo '<p><span class="xbnit">'.Text::_('COM_XBFILMS_CHAR_IN').'</span>: '.$item->clist.'</p>';
					}
					?>
				</td>
    			<?php if(($this->show_cat) || ($this->show_tags)) : ?>
					<td class="hidden-phone">
 						<?php if (($this->show_cat) && ($this->xbpeople_ok)) : ?>												
							<p>
								<?php if($this->show_cat == 2) : ?>
    								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>">
    									<?php  echo $item->category_title; ?></a>		
    							<?php else: ?>
    								<span class="label label-success"><?php  echo $item->category_title; ?></span>
								<?php endif; ?>
							</p>
						<?php endif; ?>
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

