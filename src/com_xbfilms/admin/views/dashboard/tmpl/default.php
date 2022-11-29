<?php
/*******
 * @package xbFilms
 * @filesource admin/views/dashboard/tmpl/default.php
 * @version 0.9.9.9 2nd November 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

//use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

//jimport('joomla.html.html.bootstrap');

$relink='index.php?option=com_xbfilms&view=review&layout=edit&id=';
$pelink='index.php?option=com_xbfilms&view=person&layout=edit&id=';
$chelink='index.php?option=com_xbfilms&view=character&layout=edit&id=';

if (!$this->xbpeople_ok) : ?>
    <div class="alert alert-error">
    	<?php echo Text::_('XBFILMS_PEOPLE_WARNING'); ?>
    </div>
<?php else: ?>

<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=dashboard'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	<hr />
    <div class="xbinfopane">
      	<div class="row-fluid hidden-phone">
        	<?php echo HtmlHelper::_('bootstrap.startAccordion', 'slide-dashboard', array('active' => 'sysinfo')); ?>
        		<?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBFILMS_SYSINFO'), 'sysinfo'); ?>
        			<p><b><?php echo Text::_( 'XBFILMS_COMPONENT' ); ?></b>
						<br /><?php echo Text::_('XBCULTURE_VERSION').': <b>'.$this->xmldata['version'].'</b> '.
							$this->xmldata['creationDate'];?>
                              <br /><i></i>
                              <?php  if (XbcultureHelper::penPont()) {
                                  echo Text::_('XBCULTURE_BEER_THANKS'); 
                              } else {
                                  echo Text::_('XBCULTURE_BEER_LINK');
                              }?>
                              </i></p>
                              <?php echo Text::_('XBCULTURE_OTHER_COMPS'); ?>
                              <ul>
                          	<?php $coms = array('com_xbbooks','com_xbevents','com_xbpeople');
                          	foreach ($coms as $element) {
                          	    echo '<li>';
                              	$ext = XbcultureHelper::getExtensionInfo($element);
                              	if ($ext) {
                              	    //todo add mouseover description
                              	    echo $ext['name'].' v'.$ext['version'].' '.Text::_('XBCULTURE_INSTALLED');
                              	    if (!$ext['enabled']) echo '<b><i>'.Text::_('XBCULTURE_NOT_ENABLED').'</i></b>';
                              	} else {
                              	    echo '<i>'.$element.' '.Text::_('XBCULTURE_NOT_INSTALLED').'</i>';
                              	}
                                echo '</li>';
                          	}
                          	
                          	?>
                          	</ul>
                              <?php echo Text::_('XBCULTURE_MODULES'); ?>
                          	<ul>
                          	<?php $mods = array('mod_xbculture_list','mod_xbculture_randimg','mod_xbculture_recent');
                          	foreach ($mods as $element) {
                          	    echo '<li>';
                              	$mod = XbcultureHelper::getExtensionInfo($element);
                              	if ($mod) {
                              	    echo $mod['name'].' v'.$mod['version'].' '.Text::_('XBCULTURE_INSTALLED');
                              	    if (!$mod['enabled']) echo '<b><i>'.Text::_('XBCULTURE_NOT_ENABLED').'</i></b>';
                              	} else {
                              	    echo '<i>'.$element.' '.Text::_('XBCULTURE_NOT_INSTALLED').'</i>';
                              	}
                                echo '</li>';
                          	}                             	
                          	?>
                          	</ul>
                      	</p>
                      	<p><b><?php echo Text::_( 'XBCULTURE_CLIENT'); ?></b>
                          <br/><?php echo Text::_( 'XBCULTURE_PLATFORM' ).' '.$this->client['platform'].'<br/>'.Text::_( 'XBCULTURE_BROWSER').' '.$this->client['browser']; ?>
                     	</p>
				<?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
                  <?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBCULTURE_ABOUT'), 'about'); ?>
                      <p><?php echo Text::_( 'XBFILMS_ABOUT_INFO' ); ?></p>
                  <?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
                  <?php echo HtmlHelper::_('bootstrap.addSlide', 'slide-dashboard', Text::_('XBCULTURE_LICENSE'), 'license'); ?>
                      <p><?php echo Text::_( 'XBCULTURE_LICENSE_GPL' ); ?>
                      	<br><?php echo Text::sprintf('XBCULTURE_LICENSE_INFO','xbFilms');?>
                          <br /><?php echo $this->xmldata['copyright']; ?>
                      </p>
                  <?php echo HtmlHelper::_('bootstrap.endSlide'); ?>
 				<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
			</div>
       	</div>
    </div>
	<div id="j-main-container" >
    	<h4><?php echo Text::_( 'XBCULTURE_SUMMARY' ); ?></h4>
    	<div class="row-fluid">
    		<div class="span5">
    			<div class="xbbox xbboxcyan">
    				<h2 class="xbtitle">
    					<span class="badge badge-info pull-right"><?php echo $this->filmStates['total']; ?></span> 
    					<?php echo Text::_('XBCULTURE_FILMS_U'); ?>
    				</h2>
    				<div class="row-striped">
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge badge-success xbmr10"><?php echo $this->filmStates['published']; ?></span>
    							<?php echo Text::_('XBFILMS_PUBLISHED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->filmStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->filmStates['unpublished']; ?></span>
    							<?php echo Text::_('XBFILMS_UNPUBLISHED'); ?>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge <?php echo $this->filmStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->filmStates['archived']; ?></span>
    							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->filmStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->filmStates['trashed']; ?></span>
    							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span6">
    							<?php echo Text::_('XBFILMS_REVIEWED'); ?>
    							<span class="badge badge-success xbml10"><?php echo $this->films['reviewed']; ?></span>
    						</div>
    						<div class="span6">
    							<?php echo Text::_('XBFILMS_UNREVIEWED'); ?>
    							<?php $notrev =  $this->filmStates['total']-$this->films['reviewed'];?>
    							<span class="badge <?php echo $notrev>0 ? 'badge-important' :''; ?> xbml10"><?php echo $notrev; ?></span>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="xbbox xbboxmag">
    				<h2 class="xbtitle">
    					<span class="badge badge-info pull-right">
    						<?php echo $this->revStates['total']; ?>
    					</span> 
    					<?php echo Text::_('XBCULTURE_REVIEWS_U'); ?>
    				</h2>
    				<div class="row-striped">
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge badge-success xbmr10"><?php echo $this->revStates['published']; ?></span>
    							<?php echo Text::_('XBFILMS_PUBLISHED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->revStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->revStates['unpublished']; ?></span>
    							<?php echo Text::_('XBFILMS_UNPUBLISHED'); ?>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge <?php echo $this->revStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->revStates['archived']; ?></span>
    							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->revStates['archived']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->revStates['trashed']; ?></span>
    							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
    						</div>
    					</div>
    				</div>
    				<h2 class="xbsubtitle"><?php echo Text::_('XBCULTURE_PUBRATINGS');?></h2>
    				<div class="xbratingrow">
    					<div class="row-fluid clearfix">
                        	<table style="width:100%;">
                        		<tr>
    	     						<?php $s = $this->zero_rating ? 0 : 1;
    	     						for ($i = $s; $i < 8; $i++): ?>
    		                     		<td class="center xbstarcell">
    							        	<?php if (($this->zero_rating) && ($i==0)) {
    							            	echo '<span class="'.$this->zero_class.' xbzero16"></span>';
    							        	} else { ?>
    			                      			<span style="font-size:9px;">
    			                      				<?php echo str_repeat('&#11088',$i); ?>
    			                      			</span>
    			                          	<?php } //endif; ?>
    		                      		</td>
    	                      		<?php endfor; ?>
                          		</tr><tr>
    								<?php for ($i = $s; $i < 8; $i++): ?>
                         				<td class="center" style="padding-top:5px;">
                           					<span class="badge <?php echo (key_exists($i,$this->ratCnts)) ? 'badge-info':''; ?> " >
                           					<?php echo (key_exists($i,$this->ratCnts))? $this->ratCnts[$i]:'0';?></span>
    	                    			</td>
                          			<?php endfor; ?>
    							</tr>
    						</table>
    					</div>
    				</div>
    			</div>			
    			<div class="xbbox xbboxgrn">
    				<h2 class="xbtitle"><?php echo Text::_('XBCULTURE_PEOPLE_U'); ?>
    					 <span class="pull-right"><span class="xbnit xbmr10 xb09">Total: </span><span class="badge percnt xbmr20"><?php echo $this->totPeople;?></span>
    					 <span class="xbnit xbmr10 xb09">In Films: </span><span class="badge badge-info "><?php echo $this->perStates['total'];?></span></span>	
    				</h2>
    				<div class="row-striped">
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge badge-success xbmr10"><?php echo $this->perStates['published']; ?></span>
    							<?php echo Text::_('XBFILMS_PUBLISHED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->perStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->perStates['unpublished']; ?></span>
    							<?php echo Text::_('XBFILMS_UNPUBLISHED'); ?>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->perStates['archived']; ?></span>
    							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->perStates['trashed']; ?></span>
    							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span1"></div><div class="span11">
    							<span class="badge badge-info xbmr10"><?php echo $this->people['dirpub']+$this->people['dirunpub'];?></span>
    							<?php echo Text::_('XBCULTURE_DIRECTORS'); ?>
    							<span class="pull-right xb08">
    							 	<?php echo Text::_('XBCULTURE_PUBCOLON'); ?> 				
    							 	<span class="badge badge-success xbmr10"><?php echo $this->people['dirpub'];?></span>
    								<?php echo Text::_('XBCULTURE_UNPUBCOLON'); ?>
    								<span class="badge <?php echo $this->people['dirunpub']>0 ?'badge-important' : ''; ?>"><?php echo $this->people['dirunpub'];?></span>
    							</span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span1"></div><div class="span11">
    							<span class="badge badge-info xbmr10"><?php echo $this->people['prodpub']+$this->people['produnpub'];?></span>
    							<?php echo Text::_('XBCULTURE_PRODUCERS'); ?>
    							<span class="pull-right xb08">
    							 	<?php echo Text::_('XBCULTURE_PUBCOLON'); ?> 				
    							 	<span class="badge badge-success xbmr10"><?php echo $this->people['prodpub'];?></span>
    								<?php echo Text::_('XBCULTURE_UNPUBCOLON'); ?>
    								<span class="badge <?php echo $this->people['produnpub']>0 ?'badge-important' : ''; ?>"><?php echo $this->people['produnpub'];?></span>
    							</span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span1"></div><div class="span11">
    							<span class="badge badge-info xbmr10"><?php echo $this->people['crewpub']+$this->people['crewunpub'];?></span>
    							<?php echo Text::_('XBCULTURE_CREW'); ?>
    							<span class="pull-right xb08">
    							 	<?php echo Text::_('XBCULTURE_PUBCOLON'); ?> 				
    							 	<span class="badge badge-success xbmr10"><?php echo $this->people['crewpub'];?></span>
    								<?php echo Text::_('XBCULTURE_UNPUBCOLON'); ?>
    								<span class="badge <?php echo $this->people['crewunpub']>0 ?'badge-important' : ''; ?>"><?php echo $this->people['crewunpub'];?></span>
    							</span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span1"></div><div class="span11">
    							<span class="badge badge-info xbmr10"><?php echo $this->people['castpub']+$this->people['castunpub'];?></span>
    							<?php echo Text::_('XBCULTURE_CAST'); ?>
    							<span class="pull-right xb08">
    							 	<?php echo Text::_('XBCULTURE_PUBCOLON'); ?> 				
    							 	<span class="badge badge-success xbmr10"><?php echo $this->people['castpub'];?></span>
    								<?php echo Text::_('XBCULTURE_UNPUBCOLON'); ?>
    								<span class="badge <?php echo $this->people['castunpub']>0 ?'badge-important' : ''; ?>"><?php echo $this->people['castunpub'];?></span>
    							</span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span1"></div><div class="span11">
    							<span class="badge badge-info xbmr10"><?php echo $this->people['apppub']+$this->people['appunpub'];?></span>
    							<?php echo Text::_('XBCULTURE_APPEARANCES'); ?>
    							<span class="pull-right xb08">
    							 	<?php echo Text::_('XBCULTURE_PUBCOLON'); ?> 				
    							 	<span class="badge badge-success xbmr10"><?php echo $this->people['apppub'];?></span>
    								<?php echo Text::_('XBCULTURE_UNPUBCOLON'); ?>
    								<span class="badge <?php echo $this->people['appunpub']>0 ?'badge-important' : ''; ?>"><?php echo $this->people['appunpub'];?></span>
    							</span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span1"></div><div class="span11">
    							<span class="badge badge-info xbmr10"><?php echo $this->people['otherpub']+$this->people['otherunpub'];?></span>
    							<?php echo Text::_('XBCULTURE_OTHERS'); ?>
    							<span class="pull-right" style="text-transform:none;font-weight:normal;font-size:10px;">
    							 	<?php echo Text::_('XBCULTURE_PUBCOLON'); ?> 				
    							 	<span class="badge badge-success xbmr10"><?php echo $this->people['otherpub'];?></span>
    								<?php echo Text::_('XBCULTURE_UNPUBCOLON'); ?>
    								<span class="badge <?php echo $this->people['otherunpub']>0 ?'badge-important' : ''; ?>"><?php echo $this->people['otherunpub'];?></span>
    							</span>
    							<?php if(!empty($this->otherRoles)) :?>
    								<br /><span class="xbnit xbmr10">Other roles:</</span>
    								<?php echo implode(', ',$this->otherRoles); ?>
    							<?php endif; ?>
    						</div>
    					</div>
    				</div>
    			</div>
    			<div class="xbbox xbboxcyan">
    				<h2 class="xbtitle">
    					 <span class="pull-right"><span class="xbnit xbmr10 xb09">Total: </span><span class="badge chcnt xbmr20"><?php echo $this->totChars;?></span>
    					 <span class="xbnit xbmr10 xb09">In Films: </span><span class="badge badge-info "><?php echo $this->charStates['total'];?></span></span>	
    					<?php echo Text::_('XBCULTURE_CHARACTER_U'); ?>
    				</h2>
    				<div class="row-striped">
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge badge-success xbmr10"><?php echo $this->charStates['published']; ?></span>
    							<?php echo Text::_('XBFILMS_PUBLISHED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->charStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->charStates['unpublished']; ?></span>
    							<?php echo Text::_('XBFILMS_UNPUBLISHED'); ?>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge <?php echo $this->charStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->charStates['archived']; ?></span>
    							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->charStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->charStates['trashed']; ?></span>
    							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
    						</div>
    					</div>
    				</div>
    			</div>
    			<?php if((!empty($this->orphanrevs)) || (!empty($this->orphanpeep)) || (!empty($this->orphanchar))) : ?>
    			<div class="xbbox xbboxred">
    				<h2 class="xbtitle">
    					<?php echo Text::_('XBCULTURE_ORPHANS'); ?>
    				</h2>
                   <?php if(!empty($this->orphanrevs)) : ?>
    				<div class="row-striped">
    					<span class="badge badge-important pull-right"><?php echo count($this->orphanrevs); ?></span>
    					<?php echo Text::_('XBCULTURE_REVIEWS_U'); ?>
    					<?php foreach($this->orphanrevs as $rev) {
    					        echo '<br /><a class="xbml10" href="'.$relink.$rev['id'].'">'.$rev['title'].' ('.$rev['id'].')</a> ';
    					}?>
    				</div>
                    <?php endif; ?>
                    <?php if(!empty($this->orphanpeep)) : ?>
    				<div class="row-striped">
    					<span class="badge badge-important pull-right"><?php echo count($this->orphanpeep); ?></span>
    					<?php echo Text::_('XBCULTURE_PEOPLE_U'); ?>
    					<?php foreach($this->orphanpeep as $rev) {
    						echo '<br /><a class="xbml10" href="'.$pelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
    					}?>
    				</div>
                    <?php endif; ?>
                    <?php if(!empty($this->orphanchars)) : ?>
    				<div class="row-striped">
    					<span class="badge badge-important pull-right"><?php echo count($this->orphanchars); ?></span>
    					<?php echo Text::_('XBCULTURE_CHARACTERS_U'); ?>
    					<?php foreach($this->orphanchars as $rev) {
    						echo '<br /><a class="xbml10" href="'.$chelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
    					}?>
    				</div>
                    <?php endif; ?>
    			</div>
    			<?php  endif; ?>
    		</div>
    		<div class="span5">
    			<div class="xbbox xbboxyell">
    				<h2 class="xbtitle">
    					<span class="badge badge-info pull-right">
    						<?php echo $this->catStates['total']; ?></span> 
    					<?php echo Text::_('XBFILMS_FILM_CATS'); ?>
    				</h2>
    				<div class="row-striped">
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge badge-success xbmr10"><?php echo $this->catStates['published']; ?></span>
    							<?php echo Text::_('XBFILMS_PUBLISHED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->catStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->catStates['unpublished']; ?></span>
    							<?php echo Text::_('XBFILMS_UNPUBLISHED'); ?>
    						</div>
    					</div>
     					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge <?php echo $this->catStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->catStates['archived']; ?></span>
    							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->catStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->catStates['trashed']; ?></span>
    							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
    						</div>
    					</div>
                     </div>
                     <h3 class="xbsubtitle">Counts per category<span class="xb09 xbnorm"> <i>(films:reviews:people)</i></span></h3>
                     <div class="row-striped">
    					<div class="row-fluid">
    						    <?php echo $this->catlist; ?>
    					</div>
    				</div>
    				<br />
    <?php if ($this->xbpeople_ok !==false) : ?>
     				<h2 class="xbtitle">
    					<span class="badge badge-info pull-right">
    						<?php echo $this->pcatStates['total']; ?></span> 
    					<?php echo Text::_('XBCULTURE_PEOPLE_CATEGORIES'); ?>
    				</h2>
    				<div class="row-striped">
    					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge badge-success xbmr10"><?php echo $this->pcatStates['published']; ?></span>
    							<?php echo Text::_('XBFILMS_PUBLISHED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->pcatStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->pcatStates['unpublished']; ?></span>
    							<?php echo Text::_('XBFILMS_UNPUBLISHED'); ?>
    						</div>
     					</div>
     					<div class="row-fluid">
    						<div class="span6">
    							<span class="badge <?php echo $this->pcatStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->pcatStates['archived']; ?></span>
    							<?php echo Text::_('XBCULTURE_ARCHIVED'); ?>
    						</div>
    						<div class="span6">
    							<span class="badge <?php echo $this->pcatStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->pcatStates['trashed']; ?></span>
    							<?php echo Text::_('XBCULTURE_TRASHED'); ?>
    						</div>
    					</div>
                     </div>
                     <h3 class="xbsubtitle"><?php echo Text::_('XBCULTURE_COUNTS_CATEGORY'); ?><span class="xb09 xbnorm"> <i>(people:characters)</i></span></h3>
                     <div class="row-striped">
    					<div class="row-fluid">
    						    <?php echo $this->pcatlist; ?>
    					</div>
    				</div>
    <?php endif; ?>
    			</div>
     			<div class="xbbox xbboxgrey">
    				<div class="row-fluid"><div class="span12">
    					<h2 class="xbtitle"><?php echo Text::_('XBCULTURE_NUM_ITEMS_TAGGED'); ?>
    						<span class="pull-right">
    							<span class="xbnit xbmr10 xb09"><?php echo Text::_('XBCULTURE_TOTAL'); ?>: </span>
            					<span class="badge badge-info" style="border: blue solid 1px;"><?php echo ($this->tags['filmscnt']  + $this->tags['revscnt']) ; ?></span> 
    						</span>
    					</h2>
    				</div></div>
    				<div class="row-striped">
    					<h4>Items tagged per type</h4>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBCULTURE_FILMS_U'); ?>:
    						</div>
    						<div class="span4">
    							<span class="flmcnt badge  pull-right"><?php echo $this->tags['filmscnt']; ?></span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBFILMS_XBFILM_REVIEWS'); ?>:
    						</div>
    						<div class="span4">
    							<span class="revcnt badge  pull-right"><?php echo $this->tags['revscnt']; ?></span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBCULTURE_PEOPLE_U'); ?>:
    						</div>
    						<div class="span4">
    							<span class="percnt badge  pull-right"><?php echo $this->tags['filmper']; ?></span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBCULTURE_CHARACTERS_U'); ?>:
    						</div>
    						<div class="span4">
    							<span class="chcnt badge  pull-right"><?php echo $this->tags['filmchar']; ?></span>
    						</div>
    					</div>
    				</div>
     				<hr style="margin: 8px 0;" />
     				<div class="row-striped">
     					<h4>Tags used per type</h4>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBCULTURE_FILMS_U'); ?>:
    						</div>
    						<div class="span4">
    							<span class="flmcnt badge  pull-right"><?php echo $this->tags['filmtags']; ?></span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBFILMS_XBFILM_REVIEWS'); ?>:
    						</div>
    						<div class="span4">
    							<span class="revcnt badge  pull-right"><?php echo $this->tags['revtags']; ?></span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBCULTURE_PEOPLE_U'); ?>:
    						</div>
    						<div class="span4">
    							<span class="percnt badge  pull-right"><?php echo $this->tags['filmpertags']; ?></span>
    						</div>
    					</div>
    					<div class="row-fluid">
    						<div class="span8"><?php echo Text::_('XBCULTURE_CHARACTERS_U'); ?>:
    						</div>
    						<div class="span4">
    							<span class="chcnt badge  pull-right"><?php echo $this->tags['filmchartags']; ?></span>
    						</div>
    					</div>
    				</div>
    			</div>
    		</div>
        	<div class="span2">
        		<div class="xbbox xbboxwht">
    				<h4><?php echo Text::_('XBCULTURE_CONFIG_OPTIONS'); ?></h4>
    				<p>
    					<?php echo ($this->savedata) ? 'Data not deleted on unistall' : '<b>Uninstall deletes all component data</b>'; ?>
    				</p>
            			<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_CATEGORIES_U').'</b><br />';
    	        		if (($this->show_cat==0) || 
    	        		    (($this->show_filmcat==0) && ($this->show_revcat==0) && ($this->show_percat==0))) {
    	        		    echo '<i>'.Text::_('XBCULTURE_CATS_HIDDEN_ALL').'</i>';
    	        		} else {
    	        		    echo Text::_('XBCULTURE_SHOW_FOR').' ';
    	        		    echo ($this->show_filmcat) ? Text::_('XBCULTURE_FILMS').' ' : '';
    	        		    echo ($this->show_revcat) ? Text::_('XBCULTURE_REVIEWS').' ' : '';
    	        		    echo ($this->show_percat) ? Text::_('XBCULTURE_PEOPLE').' ' : '';
    	        		}
    	        		?>
    	        		</p>
    	        		<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_TAGS_U').'</b><br />';
    	        		if (($this->show_tags==0) || 
    	        		    (($this->show_filmtags==0) && ($this->show_revtags==0) && ($this->show_pertags==0))) {
    	        		    echo '<i>'.Text::_('XBCULTURE_TAGS_HIDDEN_ALL').'</i>';
    	        		} else {
    	        		    echo Text::_('XBCULTURE_SHOW_FOR').' ';
    	        		    echo ($this->show_filmtags) ? Text::_('XBCULTURE_FILMS').' ' : '';
    	        		    echo ($this->show_revtags) ? Text::_('XBCULTURE_REVIEWS').' ' : '';
    	        		    echo ($this->show_pertags) ? Text::_('XBCULTURE_PEOPLE').' ' : '';
    	        		}
    	        		?>
    	        		</p>
    	        		<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_ALLOW_SEARCH').': </b>';
    	        		    echo ($this->show_search==0)? Text::_('JNO') : Text::_('JYES'); ?>
    	        		</p>
    	        		<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_HIDE_EMPTY_FIELDS').': </b>';
    	        		    echo ($this->hide_empty==0)? Text::_('JNO') : Text::_('JYES'); ?>
    	        		</p>    		        		
    	        		<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_IMAGE_FOLDERS').'</b><br />';
    		        		echo Text::_('XBCULTURE_FILM_POSTERS').': <code>'.$this->posters.'</code><br />';
    		        		echo Text::_('XBCULTURE_PORTRAITS').': <code>'.$this->portraits.'</code> ';
    	        		?>	
    	        		</p>
    	        		<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_SHOW_COVERS').'</b><br />'; 
    		        		echo Text::_('XBCULTURE_IN_LISTS').': ';
    		        		echo ($this->show_filmlist_posters==0)? Text::_('JNO') : Text::_('JYES');
    		        		echo '<br />';
    		        		echo Text::_('XBCULTURE_IN_FILMS').': ';
    		        		echo ($this->show_film_poster==0)? Text::_('JNO') : Text::_('JYES');
    		        		echo '<br />';
    		        		echo Text::_('XBCULTURE_IN_REVIEWS').': ';
    		        		echo ($this->show_review_poster==0)? Text::_('JNO') : Text::_('JYES');
    	        		?>	        		
    	        		</p>
    	        		<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_SHOW_PORTRAITS').'</b><br />'; 
    		        		echo Text::_('XBCULTURE_IN_LISTS').': ';
    		        		echo ($this->show_people_portraits==0)? Text::_('JNO') : Text::_('JYES');
    		        		echo '<br />';
    		        		echo Text::_('XBCULTURE_IN_PEOPLE').': ';
    		        		echo ($this->show_person_portrait==0)? Text::_('JNO') : Text::_('JYES');
    	        		?>	        		
    	        		</p>
    	        		<p>
    	        		<?php echo '<b>'.Text::_('XBCULTURE_RATINGS_REVIEWS').'</b><br />'; 
    		        		echo Text::_('XBCULTURE_ALLOW_ZERO').': ';
    		        		echo ($this->zero_rating==0)? Text::_('JNO') : Text::_('JYES').', <i>icon: </i> <i class="'.$this->zero_class.'"></i>';
    		        		echo '<br />';
    		        		echo Text::_('XBCULTURE_SHOW_FILMLIST_RATINGS').': ';
    		        		echo ($this->show_filmlist_rating==0)? Text::_('JNO') : Text::_('JYES');
    		        		echo '<br />';
    		        		echo Text::_('XBCULTURE_SHOW_BOOK_REVIEWS').' ';
    		        		echo ($this->show_film_review==0)? Text::_('JNO') : Text::_('JYES'); ?>
    	        		</p>
            		</div>    		
        		</div>
        	</div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo HtmlHelper::_('form.token'); ?>
	
</form>

<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
<?php endif; ?>

