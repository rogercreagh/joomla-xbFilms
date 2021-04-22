<?php
/*******
 * @package xbFilms
 * @filesource admin/views/cpanel/tmpl/default.php
 * @version 0.9.0 8th April 2021
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

jimport('joomla.html.html.bootstrap');

$relink='index.php?option=com_xbfilms&view=review&layout=edit&id=';
$pelink='index.php?option=com_xbfilms&view=person&layout=edit&id=';
$chelink='index.php?option=com_xbfilms&view=character&layout=edit&id=';

if (!$this->xbpeople_ok) : ?>
    <div class="alert alert-error">
        <h4>Warning - xbPeople Component appears not to be installed</h4>
        <p>It should have been installed with pkg_xbfilms_xxx.zip. Without it xbFilms will not work correctly. All front-end xbFilms and other xbCulture pages will generate a 404 error.
        <br />To install xbPeople either reinstall the xbFilms pkg or copy this url <b> http://www.crosborne.uk/downloads?download=11 </b>, and use it on the 
        	<a href="index.php?option=com_installer&view=install#url">Install from URL</a> page.
		</p>
    </div>
<?php else: ?>

<form action="<?php echo JRoute::_('index.php?option=com_xbfilms&view=cpanel'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="<?php echo ($this->client['mobile']? 'span3' : 'span2'); ?>">
		<?php echo $this->sidebar; ?>
		<p> </p>
		<div class="row-fluid hidden-phone">
        	<?php echo JHtml::_('bootstrap.startAccordion', 'slide-cpanel', array('active' => 'sysinfo')); ?>
        		<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', Text::_('COM_XBFILMS_SYSINFO'), 'sysinfo'); ?>
        			<p><b><?php echo Text::_( 'COM_XBFILMS_COMPONENT' ); ?></b>
						<br /><?php echo Text::_('XBCULTURE_VERSION').': '.$this->xmldata['version'].'<br/>'.
							$this->xmldata['creationDate'];?>
						<br /><?php if ($this->xbpeople_ok) {
						          echo Text::_('COM_XBFILMS_PEOPLEOK') ;
						      }?>
						<br /><?php if ($this->xbbooks_ok) {
						          echo Text::_('COM_XBFILMS_BOOKSOK') ;
						      }?>
						      
					</p>
					<p><b><?php echo Text::_( 'XBCULTURE_CAPCLIENT' ); ?></b>
						<br/><?php echo $this->client['platform'].'<br/>'.$this->client['browser']; ?>
					</p>
        		<?php echo JHtml::_('bootstrap.endSlide'); ?>
        		<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', Text::_('COM_XBFILMS_ABOUT'), 'about'); ?>
        			<p><?php echo Text::_( 'COM_XBFILMS_ABOUT_INFO' ); ?></p>
        		<?php echo JHtml::_('bootstrap.endSlide'); ?>
        		<?php echo JHtml::_('bootstrap.addSlide', 'slide-cpanel', Text::_('COM_XBFILMS_LICENSE'), 'license'); ?>
        			<p><?php echo Text::_( 'COM_XBFILMS_LICENSE_INFO' ); ?>
        				<br /><?php echo $this->xmldata['copyright']; ?>
        			</p>
				<?php echo JHtml::_('bootstrap.endSlide'); ?>
		</div>
	</div>
</div>
<div class="<?php echo ($this->client['mobile']? 'span9' : 'span10'); ?>">
<h4><?php echo Text::_( 'XBCULTURE_CAPSUMMARY' ); ?></h4>
	<div class="row-fluid">
		<div class="span6">
			<div class="xbbox xbboxcyan">
				<h2 class="xbtitle">
					<span class="badge badge-info pull-right"><?php echo $this->filmStates['total']; ?></span> 
					<?php echo Text::_('XBCULTURE_CAPFILMS'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->filmStates['published']; ?></span>
							<?php echo Text::_('COM_XBFILMS_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->filmStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->filmStates['unpublished']; ?></span>
							<?php echo Text::_('COM_XBFILMS_UNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->filmStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->filmStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->filmStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->filmStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<?php echo Text::_('COM_XBFILMS_REVIEWED'); ?>
							<span class="badge badge-success xbml10"><?php echo $this->films['reviewed']; ?></span>
						</div>
						<div class="span6">
							<?php echo Text::_('COM_XBFILMS_UNREVIEWED'); ?>
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
					<?php echo Text::_('XBCULTURE_CAPREVIEWS'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->revStates['published']; ?></span>
							<?php echo Text::_('COM_XBFILMS_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->revStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->revStates['unpublished']; ?></span>
							<?php echo Text::_('COM_XBFILMS_UNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->revStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->revStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->revStates['archived']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->revStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
						</div>
					</div>
				</div>
				<h2 class="xbsubtitle"><?php echo Text::_('COM_XBFILMS_PUBRATINGS');?></h2>
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
				<h2 class="xbtitle"><?php echo Text::_('XBCULTURE_CAPPEOPLE'); ?>
					 <span class="pull-right"><span class="xbnit xbmr10 xb09">Total: </span><span class="badge percnt xbmr20"><?php echo $this->totPeople;?></span>
					 <span class="xbnit xbmr10 xb09">In Films: </span><span class="badge badge-info "><?php echo $this->perStates['total'];?></span></span>	
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->perStates['published']; ?></span>
							<?php echo Text::_('COM_XBFILMS_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->perStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->perStates['unpublished']; ?></span>
							<?php echo Text::_('COM_XBFILMS_UNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->perStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->perStates['archived']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->perStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span1"></div><div class="span11">
							<span class="badge badge-info xbmr10"><?php echo $this->people['dirpub']+$this->people['dirunpub'];?></span>
							<?php echo Text::_('XBCULTURE_CAPDIRECTORS'); ?>
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
							<?php echo Text::_('XBCULTURE_CAPPRODUCERS'); ?>
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
							<?php echo Text::_('XBCULTURE_CAPCREW'); ?>
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
							<?php echo Text::_('XBCULTURE_CAPCAST'); ?>
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
							<?php echo Text::_('XBCULTURE_CAPAPPEARANCES'); ?>
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
							<?php echo Text::_('XBCULTURE_CAPOTHERS'); ?>
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
					<?php echo Text::_('XBCULTURE_CAPCHARACTERS'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->charStates['published']; ?></span>
							<?php echo Text::_('COM_XBFILMS_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->charStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->charStates['unpublished']; ?></span>
							<?php echo Text::_('COM_XBFILMS_UNPUBLISHED'); ?>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->charStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->charStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->charStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->charStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
						</div>
					</div>
				</div>
			</div>
			<?php if((!empty($this->orphanrevs)) || (!empty($this->orphanpeep)) || (!empty($this->orphanchar))) : ?>
			<div class="xbbox xbboxred">
				<h2 class="xbtitle">
					<?php echo Text::_('XBCULTURE_CAPORPHANS'); ?>
				</h2>
               <?php if(!empty($this->orphanrevs)) : ?>
				<div class="row-striped">
					<span class="badge badge-important pull-right"><?php echo count($this->orphanrevs); ?></span>
					<?php echo Text::_('XBCULTURE_CAPREVIEWS'); ?>
					<?php foreach($this->orphanrevs as $rev) {
					        echo '<br /><a class="xbml10" href="'.$relink.$rev['id'].'">'.$rev['title'].' ('.$rev['id'].')</a> ';
					}?>
				</div>
                <?php endif; ?>
                <?php if(!empty($this->orphanpeep)) : ?>
				<div class="row-striped">
					<span class="badge badge-important pull-right"><?php echo count($this->orphanpeep); ?></span>
					<?php echo Text::_('XBCULTURE_CAPPEOPLE'); ?>
					<?php foreach($this->orphanpeep as $rev) {
						echo '<br /><a class="xbml10" href="'.$pelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
					}?>
				</div>
                <?php endif; ?>
                <?php if(!empty($this->orphanchars)) : ?>
				<div class="row-striped">
					<span class="badge badge-important pull-right"><?php echo count($this->orphanchars); ?></span>
					<?php echo Text::_('XBCULTURE_CAPCHARACTERS'); ?>
					<?php foreach($this->orphanchars as $rev) {
						echo '<br /><a class="xbml10" href="'.$chelink.$rev['id'].'">'.$rev['name'].' ('.$rev['id'].')</a> ';
					}?>
				</div>
                <?php endif; ?>
			</div>
			<?php  endif; ?>
		</div>
		<div class="span6">
			<div class="xbbox xbboxyell">
				<h2 class="xbtitle">
					<span class="badge badge-info pull-right">
						<?php echo $this->catStates['total']; ?></span> 
					<?php echo Text::_('Film Categories'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->catStates['published']; ?></span>
							<?php echo Text::_('COM_XBFILMS_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->catStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->catStates['unpublished']; ?></span>
							<?php echo Text::_('COM_XBFILMS_UNPUBLISHED'); ?>
						</div>
					</div>
 					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->catStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->catStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->catStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->catStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
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
					<?php echo Text::_('People Categories'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6">
							<span class="badge badge-success xbmr10"><?php echo $this->pcatStates['published']; ?></span>
							<?php echo Text::_('COM_XBFILMS_PUBLISHED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['unpublished']>0 ?'badge-yellow' : ''; ?> xbmr10"><?php echo $this->pcatStates['unpublished']; ?></span>
							<?php echo Text::_('COM_XBFILMS_UNPUBLISHED'); ?>
						</div>
 					</div>
 					<div class="row-fluid">
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['archived']>0 ?'badge-warning' : ''; ?> xbmr10"><?php echo $this->pcatStates['archived']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPARCHIVED'); ?>
						</div>
						<div class="span6">
							<span class="badge <?php echo $this->pcatStates['trashed']>0 ?'badge-important' : ''; ?> xbmr10"><?php echo $this->pcatStates['trashed']; ?></span>
							<?php echo Text::_('XBCULTURE_CAPTRASHED'); ?>
						</div>
					</div>
                 </div>
                 <h3 class="xbsubtitle">Counts per category<span class="xb09 xbnorm"> <i>(people:characters)</i></span></h3>
                 <div class="row-striped">
					<div class="row-fluid">
						    <?php echo $this->pcatlist; ?>
					</div>
				</div>
<?php endif; ?>
			</div>
			<div class="xbbox xbboxgrey">
				<h2 class="xbtitle">
					<span class="badge badge-info pull-right"><?php echo ($this->tags['tagcnts']['bkcnt'] + $this->tags['tagcnts']['percnt']  + $this->tags['tagcnts']['revcnt']) ; ?></span> 
					<?php echo Text::_('Tagged Items'); ?>
				</h2>
				<div class="row-striped">
					<div class="row-fluid">
                      <?php echo 'Films: ';
						echo '<span class="bkcnt badge  pull-right">'.$this->tags['tagcnts']['bkcnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'People: ';
						echo '<span class="percnt badge pull-right">'.$this->tags['tagcnts']['percnt'].'</span>'; ?>
                    </div>  
                    <div class="row-fluid">
                      <?php echo 'Reviews: ';
						echo '<span class="revcnt badge pull-right">'.$this->tags['tagcnts']['revcnt'].'</span>'; ?>
                    </div>  
                 </div>
				 <h2 class="xbtitle">Tag counts <span class="xb09 xbnorm"><i>(films:reviews:people)</i></span></h2>
              <div class="row-fluid">
                 <div class="row-striped">
					<div class="row-fluid">
						<?php echo $this->taglist; ?>
                   </div>
                 </div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
		</div>
		<div class="span6">
		</div>
	</div>
</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<div class="clearfix"></div>
<p><?php echo XbfilmsGeneral::credit();?></p>
<?php endif; ?>

