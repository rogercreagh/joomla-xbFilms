<?php 
/*******
 * @package xbFilms
 * @filesource site/views/film/tmpl/default.php
 * @version 0.9.11.2 17th November 2022
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

$imgok = (($this->show_image >0) && (JFile::exists(JPATH_ROOT.'/'.$item->poster_img)));
if ($imgok) {
    $src = Uri::root().$item->poster_img;
    $tip = '<img src=\''.$src.'\' style=\'width:400px;\' />';
}

?>
<div class="xbbox xbboxcyan">
	<div class="row-fluid">
		<?php if ($imgok && ($this->show_image == 1 )) : ?>
			<div class="span2">
				<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
					data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
			</div>
		<?php endif; ?>
		<div class="<?php echo $imgok==true ? 'span10' : 'span12'; ?>">
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
							<?php echo $item->dircnt>1 ? Text::_('XBCULTURE_DIRECTORS') : Text::_('XBCULTURE_DIRECTOR'); ?>
							: </span>
							<?php echo $item->dirlist; ?>                          
						</h4>
					<?php else: ?>
						<p class="xbnit"><?php echo Text::_('XBFILMS_NO_DIR_LISTED'); ?></p>
                    <?php endif; ?>
				</div>
				<div class="span3">
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
</div>
<?php if (trim($item->summary) != '') : ?>
    <div class="row-fluid">
    	<div class="span2"></div>
		<div class= "span8">
			<div class="xbbox xbboxwht">
				<div class="pull-left">
					<span class="xbnit"><?php echo Text::_('XBCULTURE_SUMMARY'); ?> : </span>
				</div>
				<div><?php echo $item->summary; ?></div> 
			</div>
		</div>
    	<div class="span2"></div>
	</div>		
<?php  endif;?>
<div class="row-fluid">
	<div class="span6">
        <?php if (($item->prodcnt>0) || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
				<?php echo Text::_('XBCULTURE_PRODUCER').': '; ?>
				</span>
				<?php  echo $item->prodlist; ?> 
    		</p>
        <?php endif; ?>				        
    	<?php if ((!$item->studio=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
    			<?php echo Text::_('XBFILMS_CAPSTUDIO').': '; ?>
				</span>
				<?php echo $item->studio; ?>
    		</p>
    	<?php endif; ?>
    	<?php if ((!$item->country=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
 				<?php echo Text::_('XBCULTURE_COUNTRY').': '; ?>
				</span>
				<?php echo $item->country; ?>
			</p>
     	<?php endif; ?>
       	<?php if ((!$item->orig_lang=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
				<?php echo Text::_('XBFILMS_ORIG_LANG').': '; ?>
				</span>
				<?php echo $item->orig_lang; ?>
            </p>
   		<?php endif; ?>
    	<?php if ((!$item->setting=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
 				<?php echo Text::_('XBFILMS_SETTING').': '; ?>
 				</span>
				<?php echo $item->setting; ?>
			</p>
     	<?php endif; ?>
	</div>
	<div class="span1"></div>
	<div class="span5">
    	<?php if ((!$item->aspect_ratio=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
 			<?php echo Text::_('XBFILMS_ASPECT_RATIO').': '; ?>
 				</span>
				<?php echo $item->aspect_ratio; ?>
			</p>
     	<?php endif; ?>
    	<?php if ((!$item->filmcolour=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
	 			<?php echo Text::_('XBFILMS_COLOUR').': '; ?>
 				</span>
				<?php echo $item->filmcolour; ?>
			</p>
     	<?php endif; ?>
    	<?php if ((!$item->filmsound=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
	 			<?php echo Text::_('XBFILMS_SOUND').': '; ?>
 				</span>
				<?php echo $item->filmsound; ?>
			</p>
     	<?php endif; ?>
       	<?php if ((!$item->cam_format=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
	 			<?php echo Text::_('XBFILMS_CAMERA').': '; ?>
 				</span>
				<?php echo $item->cam_format; ?>
			</p>
     	<?php endif; ?>	
    	<?php if ((!$item->tech_notes=='') || (!$hide_empty)) : ?>
			<p><span class="xbnit xbpr10">
 				<?php echo Text::_('Tech Notes').': '; ?>
 				</span>
				<?php echo $item->tech_notes; ?>
			</p>
     	<?php endif; ?>
	</div>
</div>
<hr />
<?php if ((!$hide_empty) || (($item->castcnt + $item->crewcnt + $item->subjcnt + $item->charcnt) > 0)) : ?>
	<div class="row-fluid">
		<?php if((!$hide_empty) || (($item->castcnt + $item->crewcnt)>0)) : ?>
            <div class="span<?php echo ((!$hide_empty) || (($item->subjcnt + $item->charcnt) > 0))? '6' : '12'; ?>">
            	<p><b><i>Cast &amp; Crew</i></b></p>
    			<?php if (($item->castcnt > 0) || (!$hide_empty)) : ?>
    				<p class="xbnit"><?php echo Text::_('XBCULTURE_CAST').': '; ?>
    				<?php if ($item->castcnt==0) {
    				    echo Text::_('XBCULTURE_NONE_LISTED');
    				} else {
    				    echo '<ul class="xbdetails">'. $item->castlist .'</ul>';
    				}?>
    				</p>
    			<?php endif; ?>
    			<?php if (($item->crewcnt > 0) || (!$hide_empty)) : ?>
    				<p class="xbnit"><?php echo Text::_('XBCULTURE_CREW').': '; ?>
    				<?php if ($item->crewcnt==0) {
    				    echo Text::_('XBCULTURE_NONE_LISTED');
    				} else {
    				    echo '<ul class="xbdetails">'. $item->crewlist.'</ul>';
    				}?>
    				</p>
    			<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if((!$hide_empty) || (($item->subjcnt + $item->charcnt)>0)) : ?>
            <div class="span<?php echo ((!$hide_empty) || (($item->castcnt + $item->crewcnt) > 0))? '6' : '12'; ?>">
    			<p><b><i>People Appearing &amp; Characters</i></b></p>			
    			<?php if (($item->subjcnt > 0) || (!$hide_empty)) : ?>
    				<p class="xbnit"><?php echo Text::_('XBFILMS_SUBJECTS_CAMEOS').': '; ?>
    				<?php if ($item->subjcnt==0) {
    				    echo Text::_('XBCULTURE_NONE_LISTED');
    				} else {
    				    echo '<ul class="xbdetails">'. $item->subjlist.'</ul>';
    				}?>
    				</p>
        			<?php endif; ?>
        			<?php if (($item->charcnt > 0) || (!$hide_empty)) : ?>
        				<p class="xbnit"><?php echo Text::_('XBCULTURE_CHARACTERS_U').': '; ?>
        				<?php if ($item->charcnt==0) {
        				    echo Text::_('XBCULTURE_NONE_LISTED');
        				} else {
        				    echo '<br />'. $item->charlist;
        				}?>
        				</p>
        			<?php endif; ?>
    			<?php endif; ?>
    		</div>
		<?php endif; ?>
	</div>	
	<hr />
<?php //endif; ?>
<?php if ($item->ext_links_cnt > 0) : ?>
    <div class="row-fluid">
    	<div class="span12">
    		<p><b><i><?php echo Text::_('XBFILMS_EXT_LINKS'); ?></i></b></p>   					
    		<?php echo $item->ext_links_list; ?>		
    	</div>
    </div>
	<hr />
<?php endif; ?>

<?php if ($this->show_fdates) : ?>
	<div class="row-fluid">
		<div class="span5">
			<span class="xbnit"><?php echo  Text::_('XBFILMS_DATE_FIRST_SEEN').': '; ?>
			</span>
			<?php $datefmt = xbCultureHelper::getDateFmt($item->first_seen, 'D jS M Y');
			echo HtmlHelper::date($item->first_seen , $datefmt) ; ?>
		</div>
		<div class="span5">
	    	<?php if (($item->last_seen) && ($item->last_seen <> $item->first_seen)) : ?>
	    		<span class="xbnit"><?php echo  Text::_('XBFILMS_DATE_LAST_SEEN').': '; ?>
	    		</span>
	    		<?php $datefmt = xbCultureHelper::getDateFmt($item->last_seen, 'D jS M Y');
	    		echo HtmlHelper::date($item->last_seen , $datefmt) ; ?>
    		<?php endif; ?>
		</div>
		<div class="span1"></div>
		<div class="span4">
			   <span class="xbnit xbgrey"><?php echo  Text::_('XBCULTURE_CATALOGUED').': '.HtmlHelper::date($item->created ,'jS M Y'); ?>
	    		</span>
		</div>
	</div>
    <hr />
<?php endif; ?>
<div class="row-fluid">
	<div class="span<?php echo ($this->show_frevs ==0)? 12 : 6; ?>">
		<h4><?php echo Text::_('XBCULTURE_SYNOPSIS'); ?></h4>
		<div class="xbbox xbboxcyan">
			<?php if (empty($item->synopsis)) : ?>
				<p class="xbnit"><?php echo Text::_('XBFILMS_NO_SYNOPSIS');?></p>
			<?php else : ?>
				<?php echo $item->synopsis; ?>
			<?php endif; ?> 
		</div>
        <div class="row-fluid xbmt16">
			<?php if ($this->show_fcat >0) : ?>       
	        	<div class="span4">
					<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBFILMS_FILM_CAT'); ?></div>
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
				<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBFILMS_CAPTAGS'); ?>
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
    		<h4><?php echo Text::_('XBCULTURE_REVIEWS_U'); ?></h4>
    		<?php if(empty($item->reviews)) : ?>
    			<p><i><?php echo Text::_( 'XBFILMS_NOREVIEW' ); ?></i></p>
    		<?php else : ?>
    			<?php foreach ($item->reviews as $rev) : ?>
                	<div class="xbrevlist ">
                		<div class="xbbox xbboxmag">			
            				<?php if ((($rev->summary=='')) && (($rev->review=='')))  : ?>
            					<p><span class="xbtitle"><?php echo $rev->title; ?></span></p>	
                            <?php else : ?>
                              	<p><i>Rating only, no text</i></p>
            				<?php endif; ?>
            				<div class="xbstar" style="padding-bottom:5px;">
            					<?php if (($this->zero_rating) && ($rev->rating==0)) { ?>
            						<span class="<?php echo $this->zero_class; ?>" style="color:red;"></span>
            					<?php } else {
            						echo str_repeat('<i class="'.$this->star_class.'"></i>',$rev->rating);
            					}?>
            				</div>
                			<p>
                				<?php echo ' by '.$rev->reviewer;
                					echo ' on '.HtmlHelper::date($rev->rev_date , 'D jS M Y').' ';
                					echo $rev->where_seen; 
                					if ($rev->subtitled >0) { echo ' ('.Text::_('XBFILMS_SUBTITLED').')'; } ?>
                			</p>
                			<?php if ($this->show_frevs==2) : ?>
                				<?php if (empty($rev->summary)) {
                					if (empty($rev->review)) {
                						echo '<span class="xbnit">'.Text::_('XBFILMS_NO_REV_TEXT').'</span>';
                					} else {
                					    echo XbcultureHelper::makeSummaryText($rev->review,0);
                					}
                				} else { 
                					echo $rev->summary;
                				}  ?>
                				
                			<?php endif; ?>
                			<?php if ($this->show_frevs==3) : ?>
                				<?php if (empty($rev->review)) {
                					if (empty($rev->summary)) {
                						echo '<span class="xbnit">'.Text::_('XBFILMS_NO_REV_TEXT').'</span>';
                					} else {
                					    echo $rev->review;
                					}
                				} else { 
                					echo $rev->summary;
                				}  ?>
                				
                			<?php endif; ?>
                		</div>
                        <div>
                 			<?php if ($this->show_rcat) : ?>       
                 		       	<div class="row-fluid">
                					<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBFILMS_REV_CAT'); ?></div>
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
                            	<div class="row-fluid">
                					<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBFILMS_CAPTAGS'); ?>
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
    		<?php endif;  ?>
    	</div>
	<?php endif; ?>
</div>
<div class="row-fluid xbbox xbboxgrey">
	<div class="span2">
		<?php if (($item->prev>0) || ($item->next>0)) : ?>
    		<span class="hasTooltip xbinfo" title 
    			data-original-title="<?php echo Text::_('XBFILMS_INFO_PREVNEXT'); ?>" >
    		</span>&nbsp;
		<?php endif; ?>
		<?php if($item->prev > 0) : ?>
			<a href="<?php echo Route::_(XbfilmsHelperRoute::getFilmLink($item->prev)); ?>" class="btn btn-small">
				<?php echo Text::_('XBCULTURE_PREV'); ?></a>
	    <?php endif; ?>
	</div>
	<div class="span8"><center>
		<a href="<?php echo Route::_($flink); ?>" class="btn btn-small">
			<?php echo Text::_('XBFILMS_FILMLIST'); ?></a></center>
	</div>
	<div class="span2">
		<?php if($item->next > 0) : ?>
			<a href="<?php echo Route::_(XbfilmsHelperRoute::getFilmLink($item->next)); ?>" class="btn btn-small pull-right">
				<?php echo Text::_('XBCULTURE_NEXT'); ?></a>
	    <?php endif; ?>
	</div>
</div>
