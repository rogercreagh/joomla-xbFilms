<?php 
/*******
 * @package xbFilms
 * @filesource site/views/film/tmpl/default.php
 * @version 1.0.3.8 12th February 2023
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
<style type="text/css" media="screen">
	.xbpvmodal .modal-content {padding:15px;max-height:calc(100vh - 190px); overflow:scroll; }
</style>
<div class="xbculture">
<div class="xbbox flmbox">
	<div class="row-fluid">
		<?php if ($imgok && ($this->show_image == 1 )) : ?>
			<div class="span2">
				<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
					data-placement="right" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
			</div>
		<?php endif; ?>
		<div class="span<?php echo ($imgok && ($this->show_image > 0 )) ? '10' : '12'; ?>">		
			<div class="pull-right xbmr20" style="text-align:right;">
				<?php if($this->show_frevs) : ?>
    	    	    <div class="xb12">
    					<?php if ($item->revcnt>0) : ?>
    						<?php echo XbcultureHelper::getStarStr($item->averat, 'com_xbfilms'); ?> 
    	                    <br /><span class="xb09" style="color:darkgray;">
    	                    <?php if ($item->revcnt >1) : ?>
    	                    	<?php echo round($item->averat,1); ?> average from <?php echo $item->revcnt; ?> ratings<br />	
    	                    <?php else : ?>                                               
        	                    One review on <?php echo HtmlHelper::date($item->reviews[0]->rev_date ,'d M Y');?>
    	                    <?php endif; ?>
    	                    </span> 
    	                <?php else : ?>
    	                   	<p class="xbnote">no reviews available</p>                   
    					<?php endif; ?>						
                    </div>
                <?php endif; ?>
				<h4 ><?php echo $item->country; ?> <?php echo $item->rel_year; ?></h4>
				<?php if($item->runtime>0) : ?>
					<p><i>Running time: </i><?php echo $item->runtime; ?> mins</p>
				<?php endif; ?>
			</div>
			<h2><?php echo $item->title; ?></h2>
	       	<?php if (!$item->subtitle == '') : ?>
				<h3><?php  echo $item->subtitle; ?></h3>
	       	<?php endif; ?>
            <?php if ($item->dircnt>0) : ?>
				<h4><span class="xbnit xbmr10">
					<?php echo $item->dircnt>1 ? Text::_('XBCULTURE_DIRECTORS') : Text::_('XBCULTURE_DIRECTOR'); ?>
					: </span>
					<?php echo $item->dirlist['commalist']; ?>                          
				</h4>
			<?php else: ?>
				<p class="xbnit"><?php echo Text::_('XBFILMS_NO_DIR_LISTED'); ?></p>
            <?php endif; ?>
            <div class="row-fluid">
            	<div class="span6">
                    <?php if ((!$item->setting =='') || (!$hide_empty)) : ?>
                        <span class="xbnit">Setting</span>:
                        <?php echo $item->setting; ?>
            		<?php endif; ?>
            	</div>
            	<div class="span6">
                    <?php if ((!$item->orig_lang =='') || (!$hide_empty)) : ?>
                        <span class="xbnit">Original language</span>:
                        <?php echo $item->orig_lang; ?>
            		<?php endif; ?>
            	</div>            
            </div>
             <div class="clearfix"></div>
             <?php if (trim($item->summary)!='') {
                 $sum = '<i>'.Text::_('XBCULTURE_SUMMARY').'</i>: '.$item->summary;
             } elseif (trim($item->synopsis)!='') {
                 $sum = '<i>'.Text::_('XBCULTURE_SYNOPSIS_EXTRACT').'</i>: '.XbcultureHelper::makeSummaryText($item->synopsis,200);                
             } else {
                 $sum = '<i>'.Text::_('XBCULTURE_NO_SUMMARY_SYNOPSIS').'</i>';
             } ?>						
			<div class="xbbox xbboxwht" style="max-width:700px; margin:auto;">
				<div><?php echo $sum; ?></div> 
			</div>
			<br />
			<?php if (($item->prodcnt>0) || (!$item->studio=='') || (!$hide_empty)) : ?>
                <div class="row-fluid">
                	<div class="span6">
                        <?php if (($item->prodcnt>0) || (!$hide_empty)) : ?>
                			<p><span class="xbnit xbpr10">
                				<?php echo Text::_('XBCULTURE_PRODUCER').': '; ?>
                				</span>
                				<?php if ($item->prodcnt>0) {
                				    echo $item->prodlist['commalist'];
                				} else {
                				    echo Text::_('XBCULTURE_NONE_LISTED');
                				} ?> 
                    		</p>
                        <?php endif; ?>	
                    </div>
                    <div class="span6">
                    	<?php if ((!$item->studio=='') || (!$hide_empty)) : ?>
                			<p><span class="xbnit xbpr10">
                    			<?php echo Text::_('XBFILMS_CAPSTUDIO').': '; ?>
                				</span>
                				<?php if ($item->studio!='') {
                				    echo $item->studio;
                				} else {
                				    echo Text::_('XBCULTURE_NONE_LISTED');
                				} ?>
                    		</p>
                    	<?php endif; ?>
                    </div>			        
    			</div>
			<?php endif; ?>
		</div>
        <?php if ($imgok && ($this->show_image == 2)) : ?>
        	<div class="span2">
        		<img class="hasTooltip" title="" data-original-title="<?php echo $tip; ?>"
        			 data-placement="left" src="<?php echo $src; ?>" border="0" alt="" style="max-width:100%;" />
        	</div>
        <?php endif; ?>       
	</div>
	<hr style="margin:10px 0;" />
    <?php if ((!$hide_empty) || (($item->castcnt + $item->crewcnt + $item->subjcnt + $item->ccnt) > 0)) : ?>
    	<div class="row-fluid">
    		<?php if((!$hide_empty) || (($item->castcnt + $item->crewcnt)>0)) : ?>
                <div class="span<?php echo ((!$hide_empty) || (($item->subjcnt + $item->ccnt) > 0))? '6' : '12'; ?>">
        			<?php if (($item->castcnt > 0) || (!$hide_empty)) : ?>
        				<p class="xbnit"><b><?php echo Text::_('XBCULTURE_CAST').': '; ?></b>
        				<?php if ($item->castcnt==0) {
        				    echo Text::_('XBCULTURE_NONE_LISTED');
        				} else {
        				    echo $item->castlist['ullist'];
        				}?>
        				</p>
        			<?php endif; ?>
        			<?php if (($item->crewcnt > 0) || (!$hide_empty)) : ?>
        				<p class="xbnit"><b><?php echo Text::_('XBCULTURE_CREW').': '; ?></b>
        				<?php if ($item->crewcnt==0) {
        				    echo Text::_('XBCULTURE_NONE_LISTED');
        				} else {
        				    echo $item->crewlist['ullist'];
        				}?>
        				</p>
        			<?php endif; ?>
    			</div>
    		<?php endif; ?>
    		<?php if((!$hide_empty) || (($item->subjcnt + $item->gcnt + $item->ccnt)>0)) : ?>
                <div class="span<?php echo ((!$hide_empty) || (($item->castcnt + $item->crewcnt) > 0))? '6' : '12'; ?>">
        			<?php if (($item->subjcnt > 0) || (!$hide_empty)) : ?>
        				<p class="xbnit"><b><?php echo Text::_('XBFILMS_SUBJECTS_CAMEOS').': '; ?></b>
        				<?php if ($item->subjcnt==0) {
        				    echo Text::_('XBCULTURE_NONE_LISTED');
        				} else {
        				    echo $item->subjlist['ullist'];
        				}?>
        				</p>
        			<?php endif; ?>
        			<?php if (($item->gcnt > 0) || (!$hide_empty)) : ?>
        				<p class="xbnit"><b><?php echo Text::_('XBCULTURE_GROUPS').': '; ?></b>
        				<?php if ($item->gcnt==0) {
        				    echo Text::_('XBCULTURE_NONE_LISTED');
        				} else {
        				    echo $item->groupslist['ullist'];
        				}?>
        				</p>
        			<?php endif; ?>
        			<?php if (($item->ccnt > 0) || (!$hide_empty)) : ?>
        				<p class="xbnit"><b><?php echo Text::_('XBCULTURE_CHARACTERS_U').': '; ?></b>
        				<?php if ($item->ccnt==0) {
        				    echo Text::_('XBCULTURE_NONE_LISTED');
        				} else {
        				    echo $item->charslist['ullist'];
        				}?>
        				</p>
        			<?php endif; ?>
        		</div>
    		<?php endif; ?>
    	</div>	
    	<hr  style="margin:10px 0;" />
    <?php endif; ?>
    <?php if ((!$item->aspect_ratio=='') || (!$item->filmcolour=='') || (!$item->filmsound=='') || (!$hide_empty)) : ?>
        <div class="row-fluid">
        	<div class="span4">
            	<?php if ((!$item->aspect_ratio=='') || (!$hide_empty)) : ?>
        			<p><span class="xbnit xbpr10">
         			<?php echo Text::_('XBFILMS_ASPECT_RATIO').': '; ?>
         				</span>
        				<?php echo $item->aspect_ratio; ?>
        			</p>
             	<?php endif; ?>
        	</div>
        	<div class="span4">
            	<?php if ((!$item->filmcolour=='') || (!$hide_empty)) : ?>
        			<p><span class="xbnit xbpr10">
        	 			<?php echo Text::_('XBFILMS_COLOUR').': '; ?>
         				</span>
        				<?php echo $item->filmcolour; ?>
        			</p>
             	<?php endif; ?>
            </div>
       		<div class="span4">
            	<?php if ((!$item->filmsound=='') || (!$hide_empty)) : ?>
        			<p><span class="xbnit xbpr10">
        	 			<?php echo Text::_('XBFILMS_SOUND').': '; ?>
         				</span>
        				<?php echo $item->filmsound; ?>
        			</p>
             	<?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
	<?php if ((!$item->cam_format=='') || (!$item->tech_notes=='') || (!$hide_empty)) : ?>
        <div class="row-fluid">
        	<div class="span4">
               	<?php if ((!$item->cam_format=='') || (!$hide_empty)) : ?>
        			<p><span class="xbnit xbpr10">
        	 			<?php echo Text::_('XBFILMS_CAMERA').': '; ?>
         				</span>
        				<?php echo $item->cam_format; ?>
        			</p>
             	<?php endif; ?>	
    		</div>
    		<div class="span8">
            	<?php if ((!$item->tech_notes=='') || (!$hide_empty)) : ?>
        			<p><span class="xbnit xbpr10">
         				<?php echo Text::_('XBFILMS_TECH_NOTES').': '; ?>
         				</span>
        				<?php echo $item->tech_notes; ?>
        			</p>
             	<?php endif; ?>
        	</div>
        </div>
	<?php endif; ?>
	<hr  style="margin:10px 0;" />
    <?php if ($item->ext_links_cnt > 0) : ?>
        <div class="row-fluid">
        	<div class="span12">
        		<p><b><i><?php echo Text::_('XBFILMS_EXT_LINKS'); ?></i></b></p>   					
        		<?php echo $item->ext_links_list; ?>		
        	</div>
        </div>
    	<hr style="margin:10px 0;" />
    <?php endif; ?>
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
    	<?php if (($this->show_ftags>0) && (!empty($item->tags))) : ?>
    	<div class="span<?php echo ($this->show_fcat>0) ? '8' : '12'; ?>">
			<div class="pull-left xbnit xbmr10"><?php echo Text::_('XBCULTURE_TAGS_U'); ?>
			</div>
			<div class="pull-left">
				<?php  $tagLayout = new FileLayout('joomla.content.tags');
    				echo $tagLayout->render($item->tags); ?>
			</div>
    	</div>
		<?php endif; ?>
    </div>
    <hr style="margin:10px 0;" />
    <?php if ($this->show_fdates) : ?>
    	<div class="row-fluid">
    		<div class="span4">
    			<span class="xbnit"><?php echo  Text::_('XBFILMS_FIRST_SEEN').': '; ?>
    			</span>
    			<?php if($item->first_seen) : ?>
    				<?php $datefmt = xbCultureHelper::getDateFmt($item->first_seen, 'D jS M Y');
    				echo HtmlHelper::date($item->first_seen , $datefmt) ; ?>
    			<?php else: 
    			     echo Text::_('XBCULTURE_UNKNOWN');
    			endif; ?>
    		</div>
    		<div class="span4">
    	    	<?php if (($item->last_seen) && ($item->last_seen <> $item->first_seen)) : ?>
    	    		<span class="xbnit"><?php echo  Text::_('XBFILMS_LAST_SEEN').': '; ?>
    	    		</span>
    	    		<?php $datefmt = xbCultureHelper::getDateFmt($item->last_seen, 'D jS M Y');
    	    		echo HtmlHelper::date($item->last_seen , $datefmt) ; ?>
        		<?php endif; ?>
    		</div>
    		<div class="span4">
    			   <span class="xbnit xbgrey"><?php echo  Text::_('XBCULTURE_CATALOGUED').': '.HtmlHelper::date($item->created ,'jS M Y'); ?>
    	    		</span>
    		</div>
    	</div>
        <hr style="margin:10px 0;" />
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
        		<?php if ($item->revcnt == 0) : ?>
        			<p><i><?php echo Text::_( 'XBFILMS_NOREVIEW' ); ?></i></p>
        		<?php else : ?>
        			<?php if ($item->revcnt>1) : ?>
        				<span class="xb09 xbnit"><?php echo $item->revcnt; ?> reviews</span>
        			<?php endif; ?>
        			<?php foreach ($item->reviews as $rev) : ?>
                    	<div class="xbrevlist ">
                    		<div class="xbbox xbboxmag">
                				<div class="xbstar" style="padding-bottom:5px;">
                					<?php echo XbcultureHelper::getStarStr($rev->rating, 'com_xbfilms'); ?>
                				</div>
                    			<p>
                    				<?php echo ' by '.$rev->reviewer;
                    					echo ' on '.HtmlHelper::date($rev->rev_date , 'D jS M Y').' ';
                    					echo $rev->where_seen; 
                    					if ($rev->subtitled >0) { echo ' ('.Text::_('XBFILMS_SUBTITLED').')'; } ?>
                    			</p>
                                <?php if ($this->show_frevs>1) : ?>
                    				<?php if ((($rev->summary=='')) && (($rev->review=='')))  : ?>
                                      	<p><i>Rating only, no text</i></p>
                                    <?php else : ?>
                    					<p><span class="xbtitle"><?php echo $rev->title; ?></span></p>	
                    					<?php if ($this->show_frevs==2) : ?>
                    						<?php if (!empty($rev->summary)) : ?>
                          						<p class="xbnit">Summary</p>
                         						<?php echo $rev->summary; ?>
                         					<?php else : ?>
                             				    <p class="xbnit">Synopsis extract</p>
                             				    <?php echo XbcultureHelper::makeSummaryText($rev->review,0); ?>
                    						<?php endif; ?>      
                    					<?php endif; ?>              					
                            			<?php if ($this->show_frevs==3) : ?>
                    						<?php if (!empty($rev->summary)) : ?>
                          						<p class="xbnit">Summary</p>
                         						<?php echo $rev->summary; ?>
                         						<br />
                         					<?php endif; ?>
                         					<?php if (!empty($rev->review)) : ?>
                                			    <p class="xbnit">Full review</p>
                                				<?php echo $rev->review;
                                			endif; ?>
                                		<?php endif; ?>   					                    					
                    				<?php endif; ?>
                    				<?php if (($this->show_rcat) || ($this->show_rtags)) echo '<hr style="margin:10px 0;" />'; ?>
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
                    			<?php endif; ?>
                    		</div>
                    	</div>
            		<?php endforeach; ?>
        		<?php endif;  ?>
        	</div>
    	<?php endif; ?>
    </div>	
</div>

<?php if($this->tmpl != 'component') : ?>
    <div class="xbbox xbboxgrey">
    <div class="row-fluid ">
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
    </div>
    <div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
<?php endif; ?>
</div>
<?php if($this->tmpl != 'component') : ?>
	<?php echo LayoutHelper::render('xbculture.modalpvlayout', array('show' => 'pgcr'), JPATH_ROOT .'/components/com_xbpeople/layouts');   ?>
<?php endif; ?>

