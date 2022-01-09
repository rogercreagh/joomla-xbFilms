<?php
/*******
 * @package xbFilms
 * @filesource admin/views/person/tmpl/edit.php
 * @version 0.2.1b 30th November 2020
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
JHtml::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
JHtml::_('formbehavior.chosen', 'select');

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbfilms&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
    	<div class="span9">
    		<div class="row-fluid form-vertical">
        		<div class="pull-left" >
             		<?php echo $this->form->renderField('firstname'); ?>
        		</div>
        		<div class="pull-left xbml15">
            		<?php echo $this->form->renderField('lastname'); ?>
        		</div>
           	</div>
			<div class="row-fluid form-vertical">
               <div class="span4">
                   <?php echo $this->form->renderField('alias'); ?> 
               </div>
              <div class="span4">
                   <?php echo $this->form->renderField('id'); ?>
              </div>
              <div class="span4">
                   <?php echo $this->form->renderField('summary'); ?>
              </div>
          	</div>
          </div>
     <div class="pull-right span3">
		<?php if($this->form->getValue('portrait')){?>
			<div class="control-group">
				<img class="img-polaroid hidden-phone" style="max-height:200px;min-width:24px;" 
    				src="<?php echo JUri::root() . $this->form->getValue('portrait');?>" />
			</div>
		<?php } ?>
    </div>
    </div>
    
    <div class="row-fluid">
      <div class="span12">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_XBFILMS_FIELDSET_GENERAL')); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid">
					<div class="span5">
						<fieldset class="form-vertical">
    	     				<?php echo $this->form->renderField('year_born'); ?>
        	 				<?php echo $this->form->renderField('year_died'); ?>
         					<?php echo $this->form->renderField('nationality'); ?>
						</fieldset>
    				</div>
        			<div class="span7">
        				<fieldset class="form-vertical">
        					<?php echo $this->form->renderField('filmdirectorlist'); ?>
        					<?php echo $this->form->renderField('filmproducerlist'); ?>
	       					<?php echo $this->form->renderField('filmcrewlist'); ?>
	       					<?php echo $this->form->renderField('filmactorlist'); ?>
	       					<?php echo $this->form->renderField('filmappearslist'); ?>
        				</fieldset>
        			</div>
        		</div>
          		<div class="row-fluid">
					<div class="span12">
						<fieldset class="form-horizontal">
          					<hr />
							<?php echo $this->form->renderField('ext_links'); ?>					
						</fieldset>
					</div>        		
        		</div>		
			</div>
			<div class="span3">
 				<fieldset class="form-vertical">
           			<?php echo $this->form->renderField('portrait'); ?>
 				</fieldset>
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'biog', JText::_('XBCULTURE_BIOGRAPHY')); ?>
			<fieldset class="form-horizontal">
				<div style="max-width:1200px;"><?php echo $this->form->renderField('biography'); ?></div>
			</fieldset>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_XBFILMS_FIELDSET_PUBLISHING')); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	</div>
  </div>

    <input type="hidden" name="task" value="person.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
