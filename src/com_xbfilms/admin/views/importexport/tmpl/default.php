<?php
/*******
 * @package xbFilms
 * @filesource admin/views/importexport/tmpl/default.php
 * @version 0.9.9.6 31st August 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.tabState');
HTMLHelper::_('formbehavior.chosen', 'select');
use Joomla\CMS\Router\Route;

?>
<script language="JavaScript" type="text/javascript">
/* use hash to force tab irrespective of tabstate */	
	var url = document.location.toString();
	if (url.match('#')) {
    	$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
	} 

	function confirmExport() {
		document.getElementById('task').value='importexport.export';
		return true;
	}
	function confirmImport(){
		document.getElementById('task').value='importexport.import';
		return true;
	}
	
	function confirmDelete(){
		if (confirm('This will delete stuff. \n Are you really sure?')){
			document.getElementById('task').value='importexport.delete';
			return true;
		} else {
			return false;
		}
	}
		
</script>


<form action="<?php echo Route::_('index.php?option=com_xbfilms&view=importexport'); ?>" 
	method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
                <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container">
	<?php endif;?>
    <div class="row-fluid form-horizontal">
      <div class="span12">
		<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'exp')); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'imp', JText::_('XBFILMS_IMPORT_TAB')); ?>	
	<div class="row-fluid">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('XBFILMS_IMPORT_LEGEND'); ?></legend>
			<div class="alert alert-info"><?php echo JText::_('XBFILMS_IMP_INFO'); ?></div>
			<div class="row-fluid">
				<div class="span6">
					<fieldset>
						<?php echo $this->form->renderField('imptype'); ?>
              
						<?php echo $this->form->renderField('impcat'); ?>
						<?php echo $this->form->renderField('impcatcsv'); ?>
              
						<?php echo $this->form->renderField('imppcat'); ?>
						<?php echo $this->form->renderField('imppcatcsv'); ?>
              
						<?php echo $this->form->renderField('setpub'); ?>						
						<?php echo $this->form->renderField('img_path'); ?>
						<?php echo $this->form->renderField('reviewer'); ?>              
 						<?php echo $this->form->renderField('prependnote'); ?>
              
						<?php echo $this->form->renderField('import_file'); ?>
						<?php echo $this->form->renderField('import_filecsv'); ?>
					</fieldset>
					<button class="btn btn-warning" type="submit" 
						onclick="if(confirmImport()) {this.form.submit();}" />
						<i class="icon-upload icon-white"></i> 
						<?php echo JText::_('XBFILMS_IMPORT_BTN'); ?>
					</button>
				</div>
				<div class="span6">
					<div class="alert alert-info">
						<b><?php echo JText::_('XBFILMS_IMP_MYSQL_MERGE');?></b>: 
							<?php echo JText::_('XBFILMS_IMP_MYSQL_MERGE_TIP');?><br />
						<b><?php echo JText::_('XBFILMS_IMP_CSV_TABLE');?></b>: 
							<?php echo JText::_('XBFILMS_IMP_CSV_TABLE_TIP');?><br />
						<b><?php echo JText::_('XBCULTURE_INSTALL_SAMPLE');?></b>: 
							<?php echo JText::_('XBFILMS_INSTALL_SAMPLE_TIP');?><br />
					</div>
				</div>
			</div>
		</fieldset>
	</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'exp', JText::_('XBFILMS_EXPORT_TAB')); ?>
		<div class="row-fluid">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_('XBFILMS_EXPORT_LEGEND'); ?></legend>
			<div class="alert alert-success"><?php echo JText::_('XBFILMS_EXP_INFO'); ?></div>
			<div class="row-fluid">
				<div class="span6">
					<fieldset>
             			<?php echo $this->form->renderField('exptype'); ?>             
             			<?php echo $this->form->renderField('exptables'); ?>              
              			<?php echo $this->form->renderField('expcat'); ?>
             			<?php echo $this->form->renderField('exppcat'); ?>             			
					</fieldset>
					<button class="btn btn-primary" type="submit" 
						onclick="if (confirmExport()) { this.form.submit();}" />
						<i class="icon-download icon-white"></i> 
						<?php echo JText::_('XBFILMS_EXPORT_BTN'); ?>
					</button>
				</div>
				<div class="span6">
					<div class="alert alert-success">
					<b><?php echo JText::_('XBFILMS_EXP_MYSQL_FULL');?></b>: 
						<?php echo JText::_('XBFILMS_EXP_MYSQL_FULL_TIP');?><br />
					<b><?php echo JText::_('XBFILMS_EXP_MYSQL_TABLE');?></b>: 
						<?php echo JText::_('XBFILMS_EXP_MYSQL_TABLE_TIP');?><br />
					<b><?php echo JText::_('XBFILMS_EXP_CSV_TABLE');?></b>: 
						<?php echo JText::_('XBFILMS_EXP_CSV_TABLE_TIP');?><br />
					</div>
				</div>
			</div>
		</fieldset>
		</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

		<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'del', JText::_('XBFILMS_DELETE_TAB')); ?>
	<div class="row-fluid">
		<fieldset class="form-horizontal">
			<legend><?php echo JText::_( 'XBFILMS_DELETE_LEGEND' ); ?></legend>
			<div class="alert alert-warning"><?php echo JText::_('XBFILMS_DEL_INFO'); ?></div>
             
			<div class="row-fluid">
				<div class="span6">
		            <fieldset>
						<?php echo $this->form->renderField('deltype'); ?>
						<?php echo $this->form->renderField('delallitems'); ?>
						<?php echo $this->form->renderField('delstatenote'); ?>
						<?php echo $this->form->renderField('delstate'); ?>
						<?php echo $this->form->renderField('delorphansnote'); ?>		              
						<?php echo $this->form->renderField('delorphrev'); ?>		              
						<?php echo $this->form->renderField('delorphpeep'); ?>		              
						<?php echo $this->form->renderField('delorphchar'); ?>
						<?php echo $this->form->renderField('dellinknote'); ?>
						<?php echo $this->form->renderField('delcat'); ?>		              
						<?php echo $this->form->renderField('delpcat'); ?>		 		              	              
						<?php echo $this->form->renderField('delrevs'); ?>
						<?php echo $this->form->renderField('delallnote'); ?>		              
					</fieldset>
					<button class="btn btn-danger" type="submit" 
						onclick="if(confirmDelete()) {this.form.submit();}" />
						<i class="icon-delete icon-white"></i> 
						<?php echo JText::_('XBFILMS_DELETE_BTN'); ?>
					</button>
				</div>
				<div class="span6">
					<div class="alert alert-warning">
						<b><?php echo JText::_('XBFILMS_CLEAN');?></b>: 
							<?php echo JText::_('XBFILMS_CLEAN_TIP');?><br />
						<b><?php echo JText::_('XBFILMS_DELETE_FILMS');?></b>: 
							<?php echo JText::_('XBFILMS_DELETE_FILMS_TIP');?><br />
						<b><?php echo JText::_('XBFILMS_DELETE_REVS');?></b>: 
							<?php echo JText::_('XBFILMS_DELETE_REVS_TIP');?><br />
						<b><?php echo JText::_('XBFILMS_DELETE_PEOPLE');?></b>: 
							<?php echo JText::_('XBFILMS_DELETE_PEOPLE_TIP');?><br />
						<b><?php echo JText::_('XBFILMS_DELETE_ALL');?></b>: 
							<?php echo JText::_('XBFILMS_DELETE_ALL_TIP');?><br />
					</div>
				</div>
			</div>
		</fieldset>
	</div>
		<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
	</div>
	</div>
	</div>
	<?php echo HTMLHelper::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_xbfilms" />
	<input type="hidden" name="task" id="task" value="xxx" />
	<input type="hidden" name="controller" value="importexport" />
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
