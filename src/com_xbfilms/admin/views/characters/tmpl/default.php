<?php
/*******
 * @package xbFilms
 * @filesource admin/views/characters/tmpl/default.php
 * @version 0.9.6.f 10th January 2022
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
	$listOrder='name';
	$listDirn = 'ascending';
}
$orderNames = array('name'=>Text::_('XBCULTURE_NAME'),
		'id'=>'id','category_title'=>Text::_('XBCULTURE_CATEGORY'),
		'published'=>Text::_('XBCULTURE_PUBSTATE'),'a.ordering'=>Text::_('XBCULTURE_ORDERING'));

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbfilms.film');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_xbfilms&task=characters.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'xbcharactersList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$nofile = "media/com_xbfilms/images/nofile.jpg";

$pelink = 'index.php?option=com_xbfilms&view=person&task=character.edit&id=';
$pvlink = 'index.php?option=com_xbfilms&view=person&task=character.edit&id='; //change this to view view when available
$celink = 'index.php?option=com_categories&task=category.edit&id=';
$cvlink = 'index.php?option=com_xbfilms&view=fcategory&id=';
$telink = 'index.php?option=com_tags&view=tag&task=tag.edit&id=';
$tvlink = 'index.php?option=com_xbfilms&view=tag&id=';

?>
<form action="index.php?option=com_xbfilms&view=characters" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container">
	<?php endif;?>
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. Text::_(($fnd==1)?'XBCULTURE_PERSON':'XBCULTURE_PEOPLE').' '.Text::_('XBCULTURE_FOUND');			
            ?>
		</p>
	</div>
	<div class="clearfix"></div>
	<?php
        // Search tools bar
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>
	<?php $search = $this->searchTitle; ?>

	<?php if ($search) {
		echo '<p>Searched for <b>'; 
		if (stripos($search, 'i:') === 0) {
            echo trim(substr($search, 2)).'</b> '.Text::_('as character Id');
		} elseif ((stripos($search, 's:') === 0) || (stripos($search, 'd:') === 0)) {
            echo trim(substr($search, 2)).'</b> '.Text::_('XBFILMS_AS_INBIOG');
        } else {
			echo trim($search).'</b> '.Text::_('XBFILMS_AS_INNAMES');
		}
		echo '</p>';
	} ?> 
	<div class="pagination">
		<?php  echo $this->pagination->getPagesLinks(); ?>
		<br />
	    <?php echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
	</div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	
	<table class="table table-striped table-hover" id="xbcharactersList">
		<thead>
			<tr>
				<th class="nowrap center hidden-phone" style="width:25px;">
					<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', 
					    $listDirn, $listOrder, null, 'asc', 'XBCULTURE_HEADING_ORDERING_DESC', 'icon-menu-2'); ?>
				</th>
    			<th class="hidden-phone" style="width:25px;">
    				<?php echo HTMLHelper::_('grid.checkall'); ?>
    			</th>
    			<th class="nowrap center" style="width:55px">
					<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
    			</th>
    			<th class="center" style="width:80px">
    				<?php echo Text::_('XBCULTURE_PORTRAIT') ;?>
    			</th>
    			<th >
					<?php echo HTMLHelper::_('searchtools.sort', 'XBCULTURE_NAME', 'name', $listDirn, $listOrder); ?>					
    			</th>
    			<th>
    				<?php echo Text::_('Description'); ?>
    			</th>
    			<th >
    				<?php echo Text::_('XBCULTURE_FILMS_U') ;?>
    			</th>
    			<th class="hidden-tablet hidden-phone" style="width:15%;">
						<?php if ($this->xbpeople_ok!==false) {
							echo HTMLHelper::_('searchtools.sort','XBCULTURE_CATS','category_title',$listDirn,$listOrder ).' &amp; ';
						}
						echo Text::_( 'Tags' ); ?>
					</th>
    			
    			<th class="nowrap hidden-phone" style="width:45px;">
					<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
    			</th>
    		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
    			$canEdit    = $user->authorise('core.edit', 'com_xbfilms.character.'.$item->id);
    			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$userId || $item->checked_out==0;
    			$canEditOwn = $user->authorise('core.edit.own', 'com_xbfilms.character.'.$item->id) && $item->created_by == $userId;
    			$canChange  = $user->authorise('core.edit.state', 'com_xbfilms.character.'.$item->id) && $canCheckin;
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
					<td class="order nowrap center hidden-phone">
                        <?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            } elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
                            }
                        ?>
                        <span class="sortable-handler<?php echo $iconClass; ?>">
                        	<span class="icon-menu" aria-hidden="true"></span>
                        </span>
                        <?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                        <?php endif; ?>
					</td>
					<td>
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'person.', true, 'cb'); ?>
							<?php if ($item->note!=''){ ?>
								<span class="btn btn-micro active hasTooltip" title="" 
									data-original-title="<?php echo '<b>'.Text::_( 'XBCULTURE_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
									<i class="icon- xbinfo"></i>
								</span>
							<?php } else {?>
								<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
							<?php } ?>
    					</div>
    				</td>
					<td>
						<?php if(!empty($item->image)) : ?>
							<?php 
    							$src = $item->image;
    							if (!file_exists(JPATH_ROOT.'/'.$src)) {
    								$src = $nofile;
    							}
    							$src = Uri::root().$src;
							?>
							<img class="img-polaroid hasTooltip xbimgthumb" title="" 
								data-original-title="<?php echo $item->image;?>"
								src="<?php echo $src; ?>" border="0" alt="" />
						<?php endif; ?>						
					</td>
					<td>
						<p class="xbtitlelist">
							<?php if ($item->checked_out) {
							    $couname = Factory::getUser($item->checked_out)->username;
							    echo HTMLHelper::_('jgrid.checkedout', $i, Text::_('XBCULTURE_OPENED_BY').':,'.$couname, $item->checked_out_time, 'person.', $canCheckin); 
							} ?>
							
							<a href="<?php echo $pelink.$item->id; ?>" title="<?php echo Text::_('XBFILMS_EDIT_PERSON'); ?>">
								<?php echo ' '.$item->name; ?> 
							</a>
							<br />
							<span class="xb08 xbnorm"><i><?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></i></span>
						</p>
					</td>
					<td>						
						<p class="xb095">
							<?php if (!empty($item->summary)) : ?>
								<?php echo $item->summary; ?>
    						<?php else : ?>
    							<span class="xbnit">
    							<?php if (!empty($item->description)) : ?>
    								<?php echo Text::_('XBFILMS_BIOG_EXTRACT'); ?>: </span>
    								<?php echo XbcultureHelper::makeSummaryText($item->description,200); ?>
    							<?php else : ?>
    								<?php echo Text::_('XBFILMS_NO_SUMMARY_BIOG'); ?></span>
    							<?php endif; ?>
    						<?php endif; ?>
                        </p>
                        <?php if ((!empty($item->description)) && (strlen(strip_tags($item->description))>200)) : ?>
                        	<p class="xbnit xb09">   
                             <?php 
                             echo Text::_('XBCULTURE_BIOGRAPHY').' '.str_word_count(strip_tags($item->description)).' '.Text::_('XBCULTURE_WORDS'); 
                             ?>
							</p>
						<?php endif; ?>
                    </td>
					<td>
						<?php if (count($item->films)>0) { 
						    echo '<span class="xb09 xbnorm"><i>';
						    echo Text::_('Appers in').' '.count($item->films).' ';
						    echo Text::_((count($item->films)==1)?'XBCULTURE_FILM':'XBCULTURE_FILMS'); 
						    echo '</i><br />';
						    echo XbfilmsGeneral::makeLinkedNameList($item->films,'','<br />',true).'</span><br />';
						}?> 
					</td>
					<td>
						<?php if ($this->xbpeople_ok!==false): ?>						
						<p><a  class="label label-success" href="<?php echo $cvlink.$item->catid.'&extension=com_xbpeople'; ?>" 
							title="<?php echo Text::_( 'XBCULTURE_VIEW_CATEGORY' );?>::<?php echo $item->category_title; ?>">
								<?php echo $item->category_title; ?>
						</a></p>						
						<?php endif; ?>
						<ul class="inline">
						<?php foreach ($item->tags as $t) : ?>
							<li><a href="<?php echo $tvlink.$t->id; ?>" class="label label-info">
								<?php echo $t->title; ?></a>
							</li>												
						<?php endforeach; ?>
						</ul>						    											
					</td>					
					<td align="center">
						<?php echo $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
        <?php // load the modal for displaying the batch options
            echo HTMLHelper::_(
            'bootstrap.renderModal',
            'collapseModal',
            array(
                'title' => Text::_('XBCULTURE_BATCH_TITLE'),
                'footer' => $this->loadTemplate('batch_footer')
            ),
            $this->loadTemplate('batch_body')
        ); ?>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbcultureHelper::credit('xbFilms');?></p>
