<?php 
/*******
 * @package xbFilms
 * @filesource site/views/film/tmpl/default.php
 * @version 0.9.6.f 11th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

$item = $this->item;
$hide_empty=$this->hide_empty;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbfilmsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbfilms&view=category'.$itemid.'&id=';

$flink = XbfilmsHelperRoute::getFilmsLink();
$filink = XbfilmsHelperRoute::getFilmLink('');

$imgok = (($this->show_image >0) && (JFile::exists(JPATH_ROOT.'/'.$item->poster_img)));
if ($imgok) {
    $src = Uri::root().$item->poster_img;
    $tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
}

?>

<div class="row-fluid">
	<?php if ($imgok && ($this->show_image == 1)) : ?>
		<div class="span2">
			<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>
	<div class="<?php echo $imgok==true ? 'span10' : 'span12'; ?>">
		<div class="row-fluid">
			<div class="span12">
				<div class="xbbox xbboxcyan">
					<div class="pull-right xbmr20" style="text-align:right;">
	                    <div class="xbstar xb12">
						<?php if ($item->revcnt>0) : ?>
							<?php $stars = (round(($item->averat)*2)/2); ?>
								<?php if (($this->zero_rating) && ($stars==0)) : ?>
								    <span class="<?php echo $this->zero_class; ?>" style="color:red;"></span>
								<?php else : ?>
	                                <?php echo str_repeat('<i class="'.$this->star_class.'"></i>',intval($item->averat)); ?>
	                                <?php if (($item->averat - floor($item->averat))>0) : ?>
	                                    <i class="<?php echo $this->halfstar_class; ?>"></i>
	                                    <span style="color:darkgray;"> (<?php echo round($item->averat,1); ?>)</span>                                   
	                                <?php  endif; ?> 
	                             <?php endif; ?> 
	                    <?php else : ?>
	                    	<p> </p>                   
						<?php endif; ?>						
                        </div>
						<h4 ><?php echo $item->country; ?> <?php echo $item->rel_year; ?></h4>
						<?php if($item->runtime>0) : ?>
							<p><i>Running time: </i><?php echo $item->runtime; ?> mins</p>
						<?php endif; ?>
					</div>
					<h2><?php echo $item->title; ?></h2>
			       	<?php if (!$item->subtitle == '') : ?>
						<h3><?php  echo $item->subtitle; ?></h3>
			       	<?php endif; ?>
					<div class="row-fluid">
						<div class="span9">
	                        <?php if ($item->dircnt>0) : ?>
								<h4><span class="xbnit xbmr10">
									<?php echo JText::_(($item->dircnt==1) ? 'XBCULTURE_CAPDIRECTOR' : 'XBCULTURE_CAPDIRECTORS'); ?>
								: </span>
								<?php echo $item->dlist; ?>                          
								</h4>
							<?php else: ?>
								<p class="xbnit"><?php echo JText::_('no director listed'); ?></p>
	                        <?php endif; ?>
						</div>
						<div class="span3">
						</div>
					</div>   
 				</div>
       		</div>        		
		</div>
	    <div class="row-fluid">
			<div class= "span6">
				<?php if (trim($item->summary) != '') : ?>
					<div class="xbbox xbboxwht">
						<div class="pull-left"><span class="xbnit"><?php echo JText::_('XBCULTURE_SUMMARY'); ?> 
						: </span></div>
					 	<div><?php echo $item->summary; ?></div> 
					</div>
				<?php  endif;?>
  			     <?php if (($item->prodcnt>0) || (!$hide_empty)) : ?>
					<div class="pull-left xbnit">
						<?php echo JText::_('XBCULTURE_CAPPRODUCER').': '; ?>
					</div>
					<div class="pull-left">					
			                <?php  echo $item->plist; ?> 
					</div>
              		<div class="clearfix"></div>
		        <?php endif; ?>				        
            	<?php if ((!$item->studio=='') || (!$hide_empty)) : ?>
		 			<div class="pull-left xbnit">
		 				<?php echo JText::_('COM_XBFILMS_CAPSTUDIO').': '; ?>
		 			</div>
           			<div class="pull-left">
						<?php echo $item->studio; ?>
					</div>
              		<div class="clearfix"></div>
             	<?php endif; ?>
				<?php if (($item->crewcnt > 0) || (!$hide_empty)) : ?>
					<div class="pull-left xbnit">
						<?php echo JText::_('Crew').': '; ?>
					</div>
					<div class="pull-left">
						<?php echo ($item->crewcnt > 0) ? '<br />'.$item->crlist : '<i>'.JText::_('none listed').'</i>'; ?>
					</div>
					<div class="clearfix"></div>
				<?php endif; ?>
				<?php if (($item->castcnt > 0) || (!$hide_empty)) : ?>
					<div class="pull-left xbnit">
						<?php echo JText::_('Cast').': '; ?>
					</div>
					<div class="pull-left">
						<?php echo ($item->castcnt > 0) ? '<br />'.$item->alist : '<i>'.JText::_('none listed').'</i>'; ?>
					</div>
					<div class="clearfix"></div>
				<?php endif; ?>
			</div>
			<div class="span1"></div>
			<div class="span5">
            	<?php if ((!$item->country=='') || (!$hide_empty)) : ?>
		 			<div class="pull-left xbnit"><?php echo JText::_('Country').': '; ?></div>
           			<div class="pull-left">
						<?php echo $item->country; ?>
					</div>
              		<div class="clearfix"></div>
             	<?php endif; ?>
	           	<?php if ((!$item->orig_lang=='') || (!$hide_empty)) : ?>
	 				<div class="pull-left xbnit"><?php echo JText::_('COM_XBFILMS_ORIG_LANG').': '; ?></div>
       				<div class="pull-left">
       					<?php echo $item->orig_lang; ?>
                    </div>
					<div class="clearfix"></div> 
           		<?php endif; ?>
            	<?php if ((!$item->setting=='') || (!$hide_empty)) : ?>
		 			<div class="pull-left xbnit"><?php echo JText::_('Setting').': '; ?></div>
           			<div class="pull-left">
						<?php echo $item->setting; ?>
					</div>
              		<div class="clearfix"></div>
             	<?php endif; ?>
            	<?php if ((!$item->cam_format=='') || (!$hide_empty)) : ?>
		 			<div class="pull-left xbnit"><?php echo JText::_('Camera').': '; ?></div>
           			<div class="pull-left">
						<?php echo $item->cam_format; ?>
					</div>
              		<div class="clearfix"></div>
             	<?php endif; ?>
            	<?php if ((!$item->aspect_ratio=='') || (!$hide_empty)) : ?>
		 			<div class="pull-left xbnit"><?php echo JText::_('Aspect Ratio').': '; ?></div>
           			<div class="pull-left">
						<?php echo $item->aspect_ratio; ?>
					</div>
              		<div class="clearfix"></div>
             	<?php endif; ?>
            	<?php if ((!$item->filmcolour=='') || (!$hide_empty)) : ?>
           			<div class="pull-left">
						<?php echo $item->filmcolour; ?>
					</div>
              		<div class="clearfix"></div>
             	<?php endif; ?>
            	<?php if ((!$item->filmsound=='') || (!$hide_empty)) : ?>
           			<div class="pull-left">
						<?php echo $item->filmsound; ?>
					</div>
              		<div class="clearfix"></div>
             	<?php endif; ?>
             	
        	</div>               
		</div>
    </div>
	<?php if ($imgok && ($this->show_image == 2)) : ?>
		<div class="span2">
			<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
				 data-placement="left" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
		</div>
	<?php endif; ?>
    
</div>		
<hr />
<?php if ($item->ext_links_cnt > 0) : ?>
	<div class="row-fluid">
		<div class="span12">
			<div class="pull-left xbnit"><?php echo JText::_('COM_XBFILMS_EXT_LINKS'); ?></div>
			<div class="pull-left">			
				<?php echo $item->ext_links_list; ?>
			</div><div class="clearfix"></div>		
		</div>
	</div>
	<hr />
<?php endif; ?>
<?php if ((!$hide_empty) || ($item->lastseen>0)) : ?>
	<div class="pull-left xbnit"><?php echo ($item->lastseen>0) ? 'Last seen: ' : JText::_('COM_XBFILMS_ADDED_CATALOGUE').': '; ?></div>
	<div class="pull-left">
		<?php echo HtmlHelper::date($item->lastseen , Text::_('D jS M Y')) ; ?>
    </div>
	<div class="clearfix"></div> 
	<hr />
<?php endif; ?>

<div class="row-fluid">
	<?php if (($item->charcnt > 0) || (!$hide_empty)) : ?>
		<div class="span6">
			<div class="pull-left xbnit">
				<?php echo JText::_('COM_XBFILMS_FICTIONAL_CHARS').': '; ?>
			</div>
			<div class="pull-left">
				<?php echo ($item->charcnt > 0) ? '<br />'.$item->chlist : '<i>'.JText::_('none listed').'</i>'; ?>
			</div>
        </div>
    	<div class="span6">
    <?php else: ?>
    	<div class="span12">
	<?php endif; ?>
		<?php if (($item->subjcnt > 0) || (!$hide_empty)) : ?>
			<div class="pull-left xbnit">
				<?php echo JText::_('Subjects &amp; Cameos').': '; ?>
			</div>
			<div class="pull-left">
				<?php echo ($item->subjcnt > 0) ? '<br />'.$item->slist : '<i>'.JText::_('none listed').'</i>'; ?>
			</div>
			<div class="clearfix"></div>
		<?php endif; ?>
	</div>
</div>
<div class="row-fluid">
	<div class="span<?php echo ($this->show_frevs ==0)? 12 : 6; ?>">
		<h4><?php echo JText::_('XBCULTURE_SYNOPSIS'); ?></h4>
		<div class="xbbox xbboxcyan">
			<?php if (empty($item->synopsis)) : ?>
				<p class="xbnit"><?php echo JText::_('COM_XBFILMS_NO_SYNOPSIS');?></p>
			<?php else : ?>
				<?php echo $item->synopsis; ?>
			<?php endif; ?> 
		</div>
        <div class="row-fluid xbmt16">
			<?php if ($this->show_fcat >0) : ?>       
	        	<div class="span4">
					<div class="pull-left xbnit xbmr10"><?php echo JText::_('COM_XBFILMS_FILM_CAT'); ?></div>
					<div class="pull-left">
    					<?php if($this->show_fcat==2) : ?>
    						<a class="label label-success" href="<?php echo Route::_($clink.$item->catid); ?>">
    							<?php echo $item->category_title; ?></a>
    					<?php else: ?>
    						<span class="label label-success">
    							<?php echo $item->category_title; ?></span>
    					<?php endif; ?>		
					</div>
		        </div>
	        <?php endif; ?>
        	<?php if (($this->show_ftags) && (!empty($item->tags))) : ?>
        	<div class="span<?php echo ($this->show_fcat>0) ? '8' : '12'; ?>">
				<div class="pull-left xbnit xbmr10"><?php echo JText::_('COM_XBFILMS_CAPTAGS'); ?>
				</div>
				<div class="pull-left">
					<?php  $tagLayout = new FileLayout('joomla.content.tags');
	    				echo $tagLayout->render($item->tags); ?>
				</div>
        	</div>
			<?php endif; ?>
        </div>
	</div>
	<?php if ($this->show_frevs>0) : ?>
	<div class="span6 xbmb12">
		<h4><?php echo JText::_('XBCULTURE_REVIEWS_U'); ?></h4>
		<?php if(empty($item->reviews)) : ?>
			<p><i><?php echo JText::_( 'COM_XBFILMS_NOREVIEW' ); ?></i></p>
		<?php else : ?>
			<?php foreach ($item->reviews as $rev) : ?>
	<div class="xbrevlist ">
		<div class="xbbox xbboxmag">			
			<?php if ($this->show_frevs>0) : ?>
				<div class="xbstar" style="padding-bottom:5px;">
					<?php if (($this->zero_rating) && ($rev->rating==0)) { ?>
						<span class="<?php echo $this->zero_class; ?>" style="color:red;"></span>
					<?php } else {
						echo str_repeat('<i class="'.$this->star_class.'"></i>',$rev->rating);
					}?>
				</div>
			<?php endif; ?>
			<?php if ($this->show_frevs==2) : ?>
				<?php if (!empty($rev->title) && ((!empty($rev->summary)) || (!empty($rev->review)) )) : ?>
					<p><span class="xbtitle"><?php echo $rev->title; ?></span></p>				
				<?php endif; ?>
			<?php endif; ?>
			<p>
				<?php echo ' by '.$rev->reviewer;
					echo ' on '.HtmlHelper::date($rev->rev_date , Text::_('D jS M Y')).'. ';
					echo $rev->where_seen; 
					if ($rev->subtitled >0) { echo ' '.Text::_('(with subtitles)'); } ?>
			</p>
			<?php if ($this->show_frevs==2) : ?>
				<?php if (empty($rev->review)) {
					if (empty($rev->summary)) {
						echo '<span class="xbnit">'.JText::_('COM_XBFILMS_NO_REV_TEXT').'</span>';
					} else {
						echo $rev->summary;
					}
				} else { //summary doesn't get shown here if there is a review - OK????
					echo $rev->review;
				}  ?>
				<?php //TODO extlinks?>
			<?php endif; ?>
		</div>
        <div class="row-fluid">
 			<?php if ($this->show_rcat) : ?>       
 		       	<div class="span4">
					<div class="pull-left xbnit xbmr10"><?php echo JText::_('COM_XBFILMS_REV_CAT'); ?></div>
					<div class="pull-left">
    					<?php if($this->show_rcat==2) : ?>
							<a class="label label-success" href="<?php echo Route::_($clink.$rev->catid); ?>">
								<?php echo $rev->category_title; ?></a>
						<?php else: ?>
							<span class="label label-success"><?php echo $rev->category_title; ?></a></span>
						<?php endif; ?>
					</div>
                </div>
            <?php endif; ?>
  			<?php if (($this->show_rtags) && ($rev->tagcnt>0)) : ?>       
            	<div class="span<?php echo ($this->show_rcat>0) ? '8' : '12'; ?>">
					<div class="pull-left xbnit xbmr10"><?php echo JText::_('COM_XBFILMS_CAPTAGS'); ?>
					</div>
					<div class="pull-left">	                	
                		<?php $tagLayout = new FileLayout('joomla.content.tags');
                			echo $tagLayout->render($rev->tags); ?>
                	</div>              
            	</div>
			<?php endif; ?>
        </div>
	</div>
	<?php endforeach; ?>
	<?php endif; ?>
	</div>
	<?php endif; ?>
</div>
<div class="row-fluid xbbox xbboxgrey">
	<div class="span2">
		<?php if (($item->prev>0) || ($item->next>0)) : ?>
		<span class="hasTooltip xbinfo" title 
			data-original-title="<?php echo JText::_('COM_XBFILMS_INFO_PREVNEXT'); ?>" >
		</span>&nbsp;
		<?php endif; ?>
		<?php if($item->prev > 0) : ?>
			<a href="<?php echo Route::_(XbfilmsHelperRoute::getFilmLink($item->prev)); ?>" class="btn btn-small">
				<?php echo JText::_('COM_XBFILMS_CAPPREV'); ?></a>
	    <?php endif; ?>
	</div>
	<div class="span8"><center>
		<a href="<?php echo Route::_($flink); ?>" class="btn btn-small">
			<?php echo JText::_('COM_XBFILMS_FILMLIST'); ?></a></center>
	</div>
	<div class="span2">
		<?php if($item->next > 0) : ?>
			<a href="<?php echo Route::_(XbfilmsHelperRoute::getFilmLink($item->next)); ?>" class="btn btn-small pull-right">
				<?php echo JText::_('COM_XBFILMS_CAPNEXT'); ?></a>
	    <?php endif; ?>
	</div>
</div>
