<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JToolBarHelper::title(JText :: _('FWG_IMAGES'), 'fwgallery-images.png');
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList(JText :: _('FWG_ARE_YOU_SURE'));

if (!file_exists(FWG_STORAGE_PATH) and !is_writable(JPATH_SITE.'/images')) {
?>
<p style="color:#f00;"><?php echo JText :: sprintf('Images folder %s is not writable!', JPATH_SITE.'/images') ?></p>
<?php
}
if (file_exists(FWG_STORAGE_PATH) and !is_writable(FWG_STORAGE_PATH)) {
?>
<p style="color:#f00;"><?php echo JText :: sprintf('Images folder %s is not writable!', FWG_STORAGE_PATH) ?></p>
<?php
}
?>
<form action="index.php?option=com_fwgallery&amp;view=files" method="post" name="adminForm" id="adminForm">
	<div style="text-align:right;">
		<?php echo JText :: _('FWG_SEARCH_BY_IMAGE_NAME') ?>:<input name="search" size="20" value="<?php echo $this->escape($this->search); ?>" />&nbsp;<input type="submit" class="inputbox" value="<?php echo JText :: _('FWG_SEARCH'); ?>" onclick="this.form.limitstart.value=0;" />&nbsp;&nbsp;&nbsp;
	    <?php echo JText :: _('FWG_SELECT_GALLERY'); ?>&nbsp;<?php echo JHTML :: _('fwGalleryCategory.getCategories', 'project_id', $this->project_id, 'onchange="this.form.limitstart.value=0;this.form.submit();"', false, JText :: _('FWG_ALL')); ?>&nbsp;&nbsp;&nbsp;
	</div>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th style="width:20px;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
	            <th><?php echo JText :: _('FWG_ID'); ?></th>
<?php
if (count($this->types) > 1) {
?>
	            <th><?php echo JText :: _('FWG_TYPE'); ?></th>
<?php
}
?>
	            <th><?php echo JText :: _('FWG_IMAGE_PREVIEW'); ?></th>
	            <th style="width:20px;"></th>
	            <th style="width:20px;"><?php echo JText :: _('FWG_DEFAULT'); ?></th>
	            <th><?php echo JText :: _('FWG_DATE'); ?></th>
	            <th><?php echo JText :: _('FWG_NAME'); ?></th>
	            <th style="width:110px !important;"><?php echo JText :: _('FWG_ORDER') . JHTML :: _('grid.order', $this->files); ?></th>
	            <th><?php echo JText :: _('FWG_GALLERY'); ?></th>
	            <th style="width:100px;"><?php echo JText :: _('FWG_PUBLISH'); ?></th>
	        </tr>
	    </thead>
	    <tbody>
<?php
if ($this->files) {
	$num = 0;
    foreach ($this->files as $file) {
?>
	        <tr class="row<?php echo $num%2; ?>">
	            <td><?php echo JHTML :: _('grid.id', $num, $file->id); ?></td>
	            <td style="text-align:center;"><?php echo $file->id; ?></td>
<?php
		if (count($this->types) > 1) {
?>
	            <td style="text-align:center;"><?php echo $file->_type_name; ?></td>
<?php
		}
?>
	            <td>
	                <a href="index.php?option=com_fwgallery&amp;view=files&amp;task=edit&amp;cid[]=<?php echo $file->id; ?>">
	                    <img <?php echo $file->selected?'class="current_image" ':''; ?>src="<?php echo JURI::root(true).'/'.JFHelper :: getFileFilename($file, 'th'); ?>" style="padding: 6px;border: none;"/>
	                </a>
	            </td>
	            <td style="text-align:center;">
<?php
		if ($file->type_id == 1 and JFHelper::isFileExists($file, 'th')) {
?>
					<a href="javascript:" onclick="return listItemTask('cb<?php echo $num; ?>','clockwise')" title="<?php echo JText :: _('FWG_ROTATE_CLOCKWISE'); ?>"><img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/arrow_rotate_clockwise.png" /></a>
					<a href="javascript:" onclick="return listItemTask('cb<?php echo $num; ?>','counterclockwise')" title="<?php echo JText :: _('FWG_ROTATE_COUNTERCLOCKWISE'); ?>"><img src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/arrow_rotate_anticlockwise.png" /></a>
<?php
		}
?>
	            </td>
	            <td style="text-align:center;">
	                <?php echo JHTML :: _('fwgGrid.selected', $file, $num); ?>
	            </td>
	            <td style="text-align:center;">
	                <?php echo substr($file->created, 0, 10); ?>
	            </td>
	            <td>
	                <a href="index.php?option=com_fwgallery&amp;view=files&amp;task=edit&amp;cid[]=<?php echo $file->id; ?>">
	                    <?php echo $file->name; ?>
	                </a>
	            </td>
	            <td class="order">
	                <span><?php echo $this->pagination->orderUpIcon($num, $num?true:false, 'orderup', 'Move Up'); ?></span>
	                <span><?php echo $this->pagination->orderDownIcon($num, count($this->files), true, 'orderdown', 'Move Down'); ?></span>
	                <input type="text" name="order[]" size="5" value="<?php echo $file->ordering; ?>" class="inputbox text-area-order" style="text-align: center" />
	            </td>
	            <td>
	                <a href="index.php?option=com_fwgallery&amp;view=fwgallery&amp;task=edit&amp;cid[]=<?php echo $file->project_id; ?>"><?php echo $file->_project_name; ?></a>
	            </td>
	            <td style="text-align:center;">
	                <?php echo JHTML :: _('fwgGrid.published', $file, $num); ?>
	            </td>
	        </tr>
<?php
		$num++;
    }
} else {
?>
			<tr class="row0">
				<td colspan="11"><?php echo JText :: _('FWG_NO_IMAGES'); ?></td>
			</tr>
<?php
}
?>
	    </tbody>
	</table>
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
</form>
<script type="text/javascript">
window.addEvent('domready', function() {
	$$('a.fwgallery-clockwise').each(function(el) {
		el.addEvent('click', function() {

		});
	})
});
</script>