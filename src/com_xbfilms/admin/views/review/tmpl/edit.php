<?php
/*******
 * @package xbFilms
 * @filesource admin/views//tmpl/edit.php
 * @version 0.2.0b 24th November 2020
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
		<div class="span11">
			<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
		</div>
		<div class="span1"><?php echo $this->form->renderField('id'); ?></div>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<?php echo $this->form->renderField('film_id'); ?>
	 		<?php echo $this->form->renderField('rating'); ?>
		</div>
		<div class="span6">
			<?php echo $this->form->renderField('summary'); ?>
		</div>
	</div>     
    <div class="row-fluid">
      <div class="span12">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_XBFILMS_FIELDSET_GENERAL')); ?>
			<div class="span6">
				<fieldset class="adminform">
					<div class="row-fluid">				
     					<div class="span12">
    						<fieldset class="form-vertical">
                				<?php echo $this->form->renderField('review'); ?>
                			</fieldset>
    					</div>
					</div>
				</fieldset>
			</div>
			<div class="span3">
                 				<?php echo $this->form->renderField('reviewer'); ?>
                 				<?php echo $this->form->renderField('rev_date'); ?>
                				<?php echo $this->form->renderField('where_seen'); ?>
                				<?php echo $this->form->renderField('subtitled'); ?>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
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
    <input type="hidden" name="task" value="review.edit" />
    <?php echo JHtml::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbfilmsGeneral::credit();?></p>
