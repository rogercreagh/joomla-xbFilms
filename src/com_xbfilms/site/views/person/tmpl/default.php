<?php
/*******
 * @package xbFilms
 * @filesource site/views/person/tmpl/default.php
 * @version 0.5.1 12th March 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$item = $this->item;

$imgok = (($this->show_image >0) && (JFile::exists(JPATH_ROOT.'/'.$item->portrait)));
if ($imgok) {
    $src = Uri::root().$item->portrait;
    $tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
}

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbfilms&view=category' . $itemid.'&id=';

?>
<div class="xbfilms">
<div class="row-fluid">
	<?php if ($imgok && ($this->show_image == 1)) : ?>
		<div class="span2">
			<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>
	<div class="span<?php echo $imgok==true ? '10' : '12'; ?>">
		<div class="xbbox xbboxgrn">
			<h3><?php echo $item->firstname; ?> <?php echo $item->lastname; ?>
			</h3>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<?php if ((!$item->nationality == '') || (!$this->hide_empty)) : ?>
					<p><span class="xbnit"><?php echo JText::_('XBCULTURE_NATIONALITY').': '; ?> </span> 
						<?php echo $item->nationality; ?></p>
				<?php endif; ?>
				<?php if (($item->year_born == 0) && ($item->year_died == 0)) : ?>
					<?php if(!$this->hide_empty) : ?>
						<p><span class="xbnit">
							<?php echo JText::_('COM_XBFILMS_DATES_UNKNOWN'); ?>
						</span></p>
					<?php endif; ?>
				<?php  else : ?>
					<?php if ($item->year_born != 0) : ?>
						<p><span class="xbnit"><?php echo JText::_('COM_XBFILMS_CAPBORN').': '; ?> </span> 
							<?php echo $item->year_born; ?></p>
					<?php endif; ?>
					<?php if ($item->year_died != 0) : ?>
						<p><span class="xbnit"><?php echo JText::_('COM_XBFILMS_CAPDIED').': '; ?> </span> 
							<?php echo $item->year_died; ?></p>
					<?php endif; ?>
				<?php endif; ?>	
     		<div class="pull-left"><b><?php echo JText::_('XBCULTURE_FILMS_U'); ?></b></div>
    		<div class="clearfix"></div>
            	<?php if ($item->bcnt>0) : ?>
        			<div class="pull-left xbml15">
        				<?php if($item->dircnt > 0) : ?>
        					<p><span class="xbnit"><?php echo JText::_('Director of').': '; ?></span>
    						<?php echo ($item->dircnt>1) ? '<br />' : ''; ?>
        					<?php echo $item->dirlist; ?></p>
        				<?php endif; ?>
        				<?php if($item->prdcnt > 0) : ?>
    						<?php echo ($item->prdcnt>1) ? '<br />' : ''; ?>
        					<p><span class="xbnit"><?php echo JText::_('Producer of').': '; ?></span>
        					<?php echo $item->prdlist; ?></p>
        				<?php endif; ?>
        				<?php if($item->crewcnt > 0) : ?>
        					<p><span class="xbnit"><?php echo JText::_('Crew on').': '; ?></span><br />
        					<?php echo $item->crewlist; ?></p>
        				<?php endif; ?>
        				<?php if($item->appcnt > 0) : ?>
        					<p><span class="xbnit"><?php echo JText::_('Appearances').': '; ?></span><br />
        					<?php echo $item->applist; ?></p>
        				<?php endif; ?>
        				<?php if($item->actcnt > 0) : ?>
        					<p><span class="xbnit"><?php echo JText::_('Actor in').': '; ?></span><br />
        					<?php echo $item->actlist; ?></p>
        				<?php endif; ?>
        			</div>
            	<?php else: ?>
            		<p class="xbnit">No films listed for this person</p>
            	<?php endif; ?>	
            	<?php if ($item->bookcnt>0) : ?>
            		<p class="xbnit">
            		<?php echo Text::_('listed with ').$item->bookcnt.Text::_(' books'); ?>
            		</p>
            	<?php endif; ?>
            	<?php if ($item->ext_links_cnt > 0) : ?>
            		<div class="span6">
            			<div class="xbnit xbmb8"><?php echo JText::_('COM_XBFILMS_EXT_LINKS'); ?></div>
            			<div>			
            				<?php echo $item->ext_links_list; ?>
            			</div><div class="clearfix"></div>
            		</div>
            	<?php endif; ?>
			</div>
			<div class="span6">
				<?php if ((trim($item->summary) != '') && (!empty($item->biography))) : ?>
					<div class="xbnit xbmb8"><?php echo JText::_('XBCULTURE_SUMMARY'); ?></div>
					<div class="xbbox xbboxwht">
					 	<div><?php echo $item->summary; ?></div> 
					</div>
				<?php  endif;?>
				<p>&nbsp;</p>
				<?php if ((empty($item->biography)) && (trim($item->summary) == '')) : ?>
					<p class="xbnit"><?php echo JText::_('COM_XBFILMS_NO_BIOG'); ?></p>
				<?php else : ?>
					<div class="xbnit xbmb8"><?php echo JText::_('XBCULTURE_BIOGRAPHY');?></div>
					<div class="xbbox xbboxgrn">
						<?php if (!empty($item->biography)) {
					    	echo $item->biography;
						} else {
	            			echo $item->summary; 
						} ?>
					</div>
				<?php  endif; ?>
			</div>
		</div>
	</div>
	<?php if ($imgok && ($this->show_image == 2)) : ?>
		<div class="span2 xbmb12">
			<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="left" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>
	
</div>
<div class="row-fluid xbmt16">
	<?php if ($this->show_cat) : ?>
		<div class="span5">
			<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBCULTURE_CAPCATEGORY'); ?></div>
			<div class="pull-left label label-success">
				<?php if ($this->show_cat==2) : ?>
					<a href="<?php echo $clink.$item->catid; ?>"><?php echo $item->category_title; ?></a>
				<?php else : ?>
					<?php echo $item->category_title; ?>
				<?php endif; ?>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php endif; ?>
	<?php if(($this->show_tags) && (!empty($item->tags))) : ?>
		<div class="span<?php $this->showcat ? '7' : '12';?>">
			<div class="pull-left xbnit xbmr10"><?php echo JText::_('COM_XBFILMS_CAPTAGS'); ?></div>
			<div class="pull-left">
				<?php  $tagLayout = new JLayoutFile('joomla.content.tags');
			    	echo $tagLayout->render($item->tags);
			    ?>
			</div>	
		</div>
	<?php endif; ?>			
</div>
<div class="row-fluid">
	<div class="span12 xbbox xbboxgrey">
		<div class="row-fluid">
			<div class="span2">
				<?php if (($item->prev>0) || ($item->next>0)) : ?>
				<span class="hasTooltip xbinfo" title 
					data-original-title="<?php echo JText::_('COM_XBFILMS_INFO_PREVNEXT'); ?>" >
				</span>&nbsp;
				<?php endif; ?>
				<?php if($item->prev > 0) : ?>
					<a href="index.php?option=com_xbfilms&view=person&id=<?php echo $item->prev ?>" class="btn btn-small">
						<?php echo JText::_('COM_XBFILMS_CAPPREV'); ?></a>
			    <?php endif; ?>
			</div>
			<div class="span8"><center>
				<a href="index.php?option=com_xbfilms&view=people" class="btn btn-small">
					<?php echo JText::_('COM_XBFILMS_PEOPLELIST'); ?></a></center>
			</div>
			<div class="span2">
			<?php if($item->next > 0) : ?>
				<a href="index.php?option=com_xbfilms&view=person&id=<?php echo $item->next ?>" class="btn btn-small pull-right">
					<?php echo JText::_('COM_XBFILMS_CAPNEXT'); ?></a>
		    <?php endif; ?>
			</div>
	      </div>
      </div>
</div>
<div class="clearfix"></div>
<p><?php echo XbfilmsGeneral::credit();?></p>
</div>

