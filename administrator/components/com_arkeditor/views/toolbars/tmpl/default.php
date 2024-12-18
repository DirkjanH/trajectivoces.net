<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();
define('ARKEDITOR_COMPONENT_VIEW', ARKEDITOR_COMPONENT. '/views/toolbar');

if( $this->showSideBar )
{
    JHtml::_('behavior.tooltip');
    JHtml::_('formbehavior.chosen', 'select');
}

JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');


$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();

//load stylesheet
$document = JFactory::getDocument();
$document->addStyleSheet( ARKEDITOR_COMPONENT. '/views/list/css/plugins.css' );

?>
<script type="text/javascript">

 Joomla = window.Joomla || {};
 

</script>
<?php if(!$this->showSideBar ): ?>
<script type="text/javascript">
var contextAction = (cbId, task) => {
	document.querySelectorAll('input[name=\"cid[]\"]').forEach(element => {element.removeAttribute('checked');});
	document.querySelector('#' + cbId).setAttribute('checked','checked');
	Joomla.submitbutton(task);
}
</script>
<?php endif; ?>
<form action="<?php echo JRoute::_('index.php?option=com_arkeditor'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar) && $this->showSideBar ): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="main-container" class="span10 js-stools-container-bar">
<?php else : ?>
	<div id="main-container" class="js-stools-container-bar">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo 'Search in';?></label>
				<input type="text" name="filter_search" class="form-select" id="filter_search" placeholder="<?php echo JText::_('COM_ARKEDITOR_SEARCH_IN_TOOLBAR_TITLE'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_ARKEDITOR_SEARCH_IN_TOOLBAR_TITLE'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn js-stools-btn-search" rel="tooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn js-stools-btn-clear" rel="tooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
				<select name="directionTable" id="directionTable" class="form-select input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
					<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
					<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
				</select>
			</div>
			<div class="btn-group pull-right">
				<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
				<select name="sortTable" id="sortTable" class="form-select input-medium" onchange="Joomla.orderTable()">
					<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
				</select>
			</div>
		</div>
		<div class="clr">&nbsp;</div>
		<table class="table table-striped adminlist">
			<thead>
				<tr>
					<th width="20" class="nowrap center hidden-phone">
						<?php echo JText::_( 'Num' ); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th class="nowrap">
						<?php echo JHTML::_('grid.sort', JText::_('JGLOBAL_TITLE'), 't.title', $listDirn, $listOrder ); ?>
					</th>
					<th width="10%" class="nowrap center hidden-phone">
						<?php echo JText::_( 'Default' ); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo JHTML::_('grid.sort', JText::_('COM_ARKEDITOR_LAYOUT_MANAGER_NAME'), 't.name', $listDirn, $listOrder ); ?>
					</th>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHTML::_('grid.sort', JText::_('JGRID_HEADING_ID'), 't.id', $listDirn, $listOrder ); ?>
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
					$ordering   = ($listOrder == 'p.id');
					$canCreate  = $this->user->authorise('core.create',     'com_arkeditor');
					$canEdit    = $this->user->authorise('core.edit',       'com_arkeditor');
					$canDelete  = $this->user->authorise('core.delete',     'com_arkeditor');
					$canCheckin = $this->user->authorise('core.manage',     'com_checkin') || $item->checked_out == $this->user->get('id')|| $item->checked_out == 0;
					$canChange  = $this->user->authorise('core.edit.state', 'com_arkeditor') && $canCheckin;
					$title		= ( $item->title ) ? $item->title : $item->name;
				?>
				<tr>
					<td class="center hidden-phone">
						<?php echo $this->pagination->getRowOffset( $i ); ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="has-context">
					<?php if($this->showSideBar): ?>
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'toolbars.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit && $canCheckin) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_arkeditor&task=toolbars.edit&cid[]='.(int) $item->id); ?>">
									<?php echo $this->escape($title); ?></a>
							<?php else : ?>
									<?php echo $this->escape($title); ?>
							<?php endif; ?>
							<?php if(strtolower($item->name) == strtolower($this->default)) : ?>
							<div class="small">Default: Administrator</div>
							<?php elseif(strtolower($item->name) == strtolower($this->defaultFT)) : ?>
							<div class="small">Default: Site</div>
							<?php endif; ?>
						</div>
						<div class="pull-left">
							<?php
								// Create dropdown items
								if ($canEdit) :
									JHtml::_('dropdown.edit', $item->id . '&cid[]=' . $item->id, 'toolbars.');
									JHtml::_('dropdown.divider');
								endif;

								if ($canDelete) :
									JHtml::_('dropdown.trash', 'cb' . $i, 'toolbars.');
								endif;

								if ($item->checked_out && $canCheckin) :
									JHtml::_('dropdown.divider');
									JHtml::_('dropdown.checkin', 'cb' . $i, 'toolbars.');
								endif;

								// Render dropdown list
								echo JHtml::_('dropdown.render');
							?>
						</div>
					
					<?php else : ?>
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'toolbars.', $canCheckin); ?>
							<?php endif; ?>
							<?php echo $this->escape($title); ?>
							<?php if(strtolower($item->name) == strtolower($this->default)) : ?>
							<div class="small">Default: Administrator</div>
							<?php elseif(strtolower($item->name) == strtolower($this->defaultFT)) : ?>
							<div class="small">Default: Site</div>
							<?php endif; ?>
						    <?php      
							// Create dropdown items
							if ($canEdit) : ?>
								<div class="small">
								<?php
								echo JText::_('Modify') . ': ';
								echo '<a href="' . JRoute::_('index.php?option=com_arkeditor&task=toolbars.edit&cid[]='.(int) $item->id). '" title="' . JText::_('JACTION_EDIT') . '">'.JText::_('JACTION_EDIT').'</a>';
								?>
								</div>
							<?php endif; ?>
                            <?php
							if ($canDelete) : ?>
								<div class="small">
								<?php
								echo JText::_('Delete') . ': ';
								$checkboxId ='cb' . $i;
								$onclick = 'onclick="contextAction(\'' . $checkboxId . '\', \'toolbars.trash\')"';
								echo '<a href="javascript:void(0)" onclick="'.$onclick .'">'. JText::_('JTOOLBAR_TRASH').'</a>';
								?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif;?>	
					</td>
					<td class="center hidden-phone">
						<?php if(strtolower($item->name) == strtolower($this->default) || strtolower($item->name) == strtolower($this->defaultFT)) : ?>
							<i class="icon-star"></i>
						<?php else : ?>
							<i class="icon-star-empty"></i>
						<?php endif; ?>	
					</td>
					<td class="hidden-phone">
						<?php echo $item->name;?>
					</td>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="type" value="toolbars" />
		<input type="hidden" name="view" value="toolbars" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</div>
</form>