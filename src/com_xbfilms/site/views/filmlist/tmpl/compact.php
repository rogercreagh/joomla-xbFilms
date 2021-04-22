<?php
/*******
 * @package xbFilms
 * @filesource site/views/filmlist/tmpl/compact.php
 * @version 0.5.3 15th March 2021
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
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
    $listOrder='cat_date';
    $orderDrn = 'descending';
}
$orderNames = array('title'=>Text::_('XBCULTURE_CAPTITLE'), 'averat'=>'Average Rating', 'cat_date'=>'Last Seen');

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getFilmsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$blink = 'index.php?option=com_xbfilms&view=film'.$itemid.'&id=';

?>
<div class="xbfilms">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbfilmsHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_xbfilms&view=filmlist&layout=compact'); ?>" method="post" name="adminForm" id="adminForm">       
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ($this->hide_peep) { $hide .= 'filter_perfilt,filter_prole,';}
				if ($this->hide_char) { $hide .= 'filter_charfilt,';}
				if ((!$this->show_cat) || $this->hide_cat) { $hide .= 'filter_category_id,filter_subcats,';}
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
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>

	<table class="table table-striped table-hover"  id="xbfilmlist">	
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_CAPTITLE','title',$listDirn,$listOrder);				
					?>
				</th>					
				<th>
					<?php echo Text::_('Director');?>
				</th>
				<th class="hidden-phone xbtc">
					<?php echo HTMLHelper::_('searchtools.sort','XBCULTURE_CAPRATING','averat',$listDirn,$listOrder); ?>
				</th>
				<th class="hidden-phone">
					<?php echo HTMLHelper::_('searchtools.sort','COM_XBFILMS_DATE_SEEN','cat_date',$listDirn,$listOrder ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php $reviews = ''; ?>
				<tr class="row<?php echo $i % 2; ?>">	
					<td>
						<p class="xbtitle">
							<a href="<?php echo JRoute::_(XbfilmsHelperRoute::getFilmLink($item->id));?>" >
								<b><?php echo $this->escape($item->title); ?></b></a> 
						<?php if (!empty($item->subtitle)) :?>
                        	<br /><span class="xb095 xbnorm"><?php echo $this->escape($item->subtitle); ?></span>
                        <?php endif; ?>
						</p>
					</td>
					<td>
						<p>
                        	<?php if ($item->dircnt==0) {
                        		echo '<span class="xbnit">'.Text::_('COM_XBFILMS_NODIRECTOR').'</span>';
                        	} else { ?> 
	                        	<span class="xbnit">
	                        		<?php echo Text::_($item->dircnt>1 ? 'XBCULTURE_CAPDIRECTORS' : 'XBCULTURE_CAPDIRECTOR' ); ?>
	                        	</span>: 
                        		<?php echo $item->alist; 
                        	} ?>                          	
						</p>
					</td>
					<?php if ($this->show_rev != 0 ) : ?>
    					<td class="hidden-phone">
    						<?php if ($item->revcnt==0) : ?>
    						   <i><?php  echo ($this->show_rev == 1)? Text::_( 'Not rated yet' ) : Text::_( 'COM_XBFILMS_NOREVIEW' ); ?></i><br />
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
     						<?php endif; ?>   											
    					</td>
    				<?php endif; ?>
					<td class="hidden-phone">
						<p class="xb09"><?php echo $item->cat_date > 0 ? HtmlHelper::date($item->cat_date , Text::_('M Y')) : ''; ?></p>					
					</td>
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
