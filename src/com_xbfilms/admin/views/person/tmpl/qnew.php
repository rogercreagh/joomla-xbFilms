<?php
/*******
 * @package xbFilms
 * @filesource admin/views/person/tmpl/qnew.php
 * @version 0.9.6.f 10th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', 'select');

?>
<div class="xbml20 xbmr20">
<form action="<?php echo Route::_('index.php?option=com_xbfilms&layout=qnew&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm">
    <div class="row-fluid">
    	<div class="span12">
    		<div class="row-fluid form-vertical">
        		<div class="pull-left" >
             		<?php echo $this->form->renderField('firstname'); ?>
        		</div>
        		<div class="pull-left xbml15">
            		<?php echo $this->form->renderField('lastname'); ?>
        		</div>
           	</div>
        </div>
    </div>
    <div class="row-fluid">
    	<div class="span4">
			<?php echo $this->form->renderField('catid'); ?> 
		</div>
		<div class="span4">
			<?php echo $this->form->renderField('state'); ?>
		</div>
		<div class="span4">
			<?php echo $this->form->renderField('tags'); ?>
		</div>
    </div>
    
    <input type="hidden" name="task" value="person.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
