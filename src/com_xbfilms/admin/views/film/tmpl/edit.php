<?php
/*******
 * @package xbFilms
 * @filesource admin/views/film/tmpl/edit.php
 * @version 0.9.9.6 31st August 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HtmlHelper::_('behavior.tabState');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HTMLHelper::_('formbehavior.chosen', 'select');

$document = Factory::getDocument();
$style = '.controls .btn-group > .btn  {min-width: unset;padding:3px 12px 4px;}'
    .' .modal-body {height:262px;} .modal-body iframe { height:232px;}' ;
$document->addStyleDeclaration($style);
?>
<form action="<?php echo Route::_('index.php?option=com_xbfilms&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
 	<div class="row-fluid">
		<div class="span10">
         	<div class="row-fluid">
        		<div class="span11">
        			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        		</div>
        		<div class="span1"><?php echo $this->form->renderField('id'); ?></div>
        	</div>
         	<div class="row-fluid">
        		<div class="span6">
        			<?php echo $this->form->renderField('subtitle'); ?>
                	<div class="row-fluid xbbox xbboxgrey">
						<div class="span6">
							<?php if (($this->item->id > 0) && (!empty($this->item->lastrat))) { 
								echo Text::_('XBCULTURE_LAST_RATED').' ';
								if ($this->item->lastrat['rate']>0) {
									echo str_repeat('<i class="'.$this->star_class.' "></i>',(int)($this->item->lastrat['rate']));
								} else {
									echo '<i class="'.$this->zero_class.' "></i>';
								}
								echo ' on '.HtmlHelper::date($this->item->lastrat['seen'] , 'd M Y');
                            } else { 
                                echo Text::_('XBCULTURE_NO_RATING');
                            } ?>
                        </div>
                        <div class="span6">
							<?php echo $this->form->renderField('quick_rating'); ?>
							<?php echo $this->form->renderField('qratnote'); ?>
						</div>
					</div>
        		</div>
        		<div class="span6">
        			<?php echo $this->form->renderField('summary'); ?>
        		</div>
        	</div> 
        	<div class="row-fluid">
        		<div class="span9">
					<?php echo $this->form->renderField('directorlist'); ?>
        		</div>
        		<div class="span3 xbbox xbboxwht">
         			<h4><?php echo Text::_('XBCULTURE_QUICK_P_ADD');?></h4>
        			<p class="xbnote"><?php echo Text::_('XBCULTURE_QUICK_P_NOTE');?></p> 
					<a class="btn btn-small" data-toggle="modal" 
						href="index.php?option=com_xbfilms&view=film&layout=modal&tmpl=component" 
						data-target="#ajax-modal"><i class="icon-new">
						</i><?php echo Text::_('XBCULTURE_ADD_NEW_P');?></a>
         		</div>
         	</div>
        </div>    
        <div class="span2">
    		<?php if($this->form->getValue('poster_img')){?>
    			<div class="control-group">
    				<img class="img-polaroid hidden-phone" style="max-width:100%;" 
        				src="<?php echo Uri::root() . $this->form->getValue('poster_img');?>" />
    			</div>
    		<?php } else {?>
    			<div class="xbbox xbboxwht xbnit" style="width:100px;height:133%;"><?php echo Text::_('XBFILMS_NO_POSTER_IMAGE'); ?></div>
    		<?php } ?>
        </div>
    </div>
    <div class="row-fluid form-horizontal">
      <div class="span12">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('XBCULTURE_DETAILS')); ?>
		<div class="row-fluid">
    		<div class="span6">
          		<h4>Content</h4>
          		<div class="form-horizontal">
         			<?php echo $this->form->renderField('setting'); ?>
          		</div>
          		<p>Synopsis</p>
    			<?php echo $this->form->getInput('synopsis'); ?>
    		</div>
    		<div class="span3 form-vertical">
          		<h4>Production Info</h4>
         		<?php echo $this->form->renderField('acq_date'); ?>
         		<?php echo $this->form->renderField('last_seen'); ?>
        		<?php echo $this->form->renderField('rel_year'); ?>
         		<?php echo $this->form->renderField('runtime'); ?>
         		<?php echo $this->form->renderField('country'); ?>
        		<?php echo $this->form->renderField('orig_lang'); ?>
         		<?php echo $this->form->renderField('studio'); ?>
         		<?php echo $this->form->renderField('filmcolour'); ?>
         		<?php echo $this->form->renderField('aspect_ratio'); ?>
         		<?php echo $this->form->renderField('cam_format'); ?>
         		<?php echo $this->form->renderField('filmsound'); ?>
   			</div>
			<div class="span3">
 				<fieldset class="form-vertical">
           			<?php echo $this->form->renderField('poster_img'); ?>
 				</fieldset>
				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
			</div>					
		</div>	
			<hr />
			<?php echo $this->form->renderField('ext_links'); ?>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		
		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'people', Text::_('XBCULTURE_PEOPLE_U')); ?>
			<div class="row-fluid">				
	    		<div class="span6 form-vertical">
				<h4><?php echo Text::_('XBCULTURE_FILM_U').' '.Text::_('XBCULTURE_PEOPLE_U');?></h4>
				<p class="xbnote"><?php echo Text::_('XBCULTURE_ADD_PEEP_NOTE');?> </p>
					<?php echo $this->form->renderField('producerlist'); ?>
					<?php echo $this->form->renderField('crewlist'); ?>
	    			<?php echo $this->form->renderField('subjectlist'); ?>
	    			<?php echo $this->form->renderField('castlist'); ?>
 				</div>
    			<div class="span6 form-vertical">
				<h4><?php echo Text::_('XBCULTURE_FILM_U').' '.Text::_('XBCULTURE_CHARACTERS_U');?></h4>
				<p class="xbnote"><?php echo Text::_('XBCULTURE_ADD_CHAR_NOTE');?> </p>
				<?php echo $this->form->renderField('charlist'); ?>
				</div>
			</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'publishing', Text::_('XBFILMS_FIELDSET_PUBLISHING')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
    </div>
    </div>
    <input type="hidden" name="task" value="film.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
<script>
jQuery(document).ready(function(){
    jQuery('#ajax-modal').on('show', function () {
        // Load view vith AJAX
        jQuery(this).find('.modal-content').load(jQuery('a[data-target="#'+jQuery(this).attr('id')+'"]').attr('href'));
    })
    jQuery('#ajax-modal').on('hidden', function () {
     //document.location.reload(true);
     Joomla.submitbutton('film.apply');
    })
});
</script>
<div class="modal fade" id="ajax-modal" style="max-width:1000px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Ajax content will be loaded here -->
        </div>
    </div>
</div>

