<?php 
/*******
 * @package xbFilms
 * @filesource site/views/people/tmpl/default.php
 * @version 0.9.9.4 28th July 2022
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
<div class="xbfilms">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
	    echo XbcultureHelper::sitePageheader($this->header);
	} ?>
	
<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=people'); ?>" method="post" name="adminForm" id="adminForm">
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
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbpeople">	
		<thead>
			<tr>
				<?php if($this->show_pic) : ?>
					<th class="center" style="width:80px">
						<?php echo Text::_( 'XBCULTURE_PORTRAIT' ); ?>
					</th>	
                <?php endif; ?>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Firstname','firstname',$listDirn,$listOrder).' '.
							HTMLHelper::_('searchtools.sort','Lastname','lastname',$listDirn,$listOrder); ?>
				</th>					
				<?php if($this->show_pdates) : ?>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','Dates','sortdate',$listDirn,$listOrder); ?>
					</th>
                <?php endif; ?>
				<?php if($this->show_sum) : ?>
					<th>
						<?php echo Text::_('XBCULTURE_SUMMARY');?>
					</th>
                <?php endif; ?>
                <?php if ($this->showcnts) : ?>
    				<th>
    					<?php echo ucfirst(Text::_('XBCULTURE_FILMS')); ?>
    				</th>
               <?php endif; ?>
				<?php if($this->showcat || $this->showtags) : ?>
    				<th class="hidden-tablet hidden-phone">
    					<?php if ($this->showcat) {
    						echo HtmlHelper::_('searchtools.sort','XBCULTURE_CATEGORY','category_title',$listDirn,$listOrder );
    					}
    					if (($this->showcat) && ($this->showtags)) {
    					    echo ' &amp; ';
    					}
    					if($this->showtags) {
    					    echo ucfirst(Text::_( 'XBCULTURE_TAGS'));
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
						<?php $src = $item->portrait;
						if ((!empty($src)) && (file_exists(JPATH_ROOT.'/'.$src))) : ?>
							<?php 
								$src = Uri::root().$src;
								$tip = '<img src=\''.$src.'\' style=\'max-width:250px;\' />';
							?>
							<img class="img-polaroid hasTooltip xbimgthumb" title="" 
								data-original-title="<?php echo $tip; ?>"
								src="<?php echo $src; ?>" border="0" alt="" />
						<?php endif; ?>						
					</td>
                    <?php endif; ?>
				<td>
					<p class="xbtitlelist">
						<a href="<?php echo Route::_($plink.$item->id);?>" >
							<b><?php echo $this->escape($item->firstname).' '.$this->escape($item->lastname); ?></b>
						</a>
					</p>
				</td>
				<?php if($this->show_pdates) : ?>
					<td>
					<p class="xb095">
					<?php if ($item->year_born != 0) {						
							echo '<span class="xbnit">'.Text::_('XBFILMS_CAPBORN').'</span>: '.$item->year_born.'<br />'; 
						}
						if ($item->year_died != 0) {						
							echo '<span class="xbnit">'.Text::_('XBFILMS_CAPDIED').'</span>: '.$item->year_died; 
						}              
					?>					
					</p>
					</td>
				<?php endif; ?>
				<?php if($this->show_sum) : ?>
    				<td>
    					<p class="xb095">
    						<?php if (!empty($item->summary)) : ?>
    							<?php echo $item->summary; ?>
        					<?php else : ?>
        						<?php if (!empty($item->biography)) : ?>
		        					<span class="xbnit">
        								<?php echo Text::_('Biog. extract'); ?>: 
        							</span>
        							<?php echo XbcultureHelper::makeSummaryText($item->biography,0); ?>
        						<?php else : ?>
		        					<span class="xbnit">
        								<?php echo Text::_('XBFILMS_NO_SUMMARY_SYNOPSIS'); ?>
        							</span>
        						<?php endif; ?>
        					<?php endif; ?>
                        </p>
                        <?php if (!empty($item->biography)) : ?>
                            <p class="xbnit xb09">   
                                 <?php 
                                 	echo Text::_('Biography').' '.str_word_count(strip_tags($item->biography)).' '.Text::_('XBCULTURE_WORDS'); 
                                 ?>
    						</p>
    					<?php endif; ?>
    				</td>
				<?php endif; ?>
                <?php if ($this->showcnts) : ?>
    				<td>
    				<?php if (($this->showlists == 1) && ($item->filmcnt>0)) :?>
    					<span tabindex="<?php echo $item->id; ?>"
							class="xbpop xbcultpop xbfocus" data-trigger="focus"
							title data-original-title="Film List" 
							data-content="<?php echo htmlentities($item->filmlist); ?>"
						>        				
    				<?php  endif; ?>
    					<span class="badge <?php echo ($item->filmcnt>0) ? 'flmcnt' : ''?>"><?php echo $item->filmcnt;?></span>
    				<?php if (($this->showlists == 1) && ($item->filmcnt>0)) :?>
    					</span>
					<?php endif; ?>        					
    				<?php if ($this->showlists == 2) :?>
    					<?php echo $item->filmlist; ?>
    				<?php endif; ?>
    				</td>
				<?php endif; ?>

    				<?php if ($item->bookcnt > 0) {
    						echo '<p class="xbit xb095"><span>'.Text::_('XBCULTURE_LISTED_WITH').'</span>: '.$item->bookcnt.' '.Text::_('XBCULTURE_FILMS').'</p>';
    					}
    				?>
				</td>
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
						<?php if ($this->showtags) : ?>	
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


