<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JToolBarHelper :: title(JText::_('FWG_TEMPLATES'), 'fwgallery-templates.png' );
JToolBarHelper :: custom('save', 'apply', 'apply', 'Default', false);
JToolBarHelper :: custom('remove', 'delete', 'delete', 'Uninstall', false);
JHTML::_('behavior.tooltip');
?>
<form action="index.php?option=com_fwgallery&amp;view=templates" method="post" name="adminForm" id="adminForm">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_TEMPLATES'); ?></legend>
        <table class="table table-striped">
			<thead>
				<tr>
					<th width="10" class="title">#</th>
					<th class="title" colspan="2"><?php echo JText::_('FWG_TEMPLATE_NAME'); ?></th>
					<th width="5%"><?php echo JText::_('FWG_DEFAULT'); ?></th>
					<th width="10%" align="center"><?php echo JText::_('FWG_VERSION'); ?></th>
					<th width="25%"  class="title"><?php echo JText::_('FWG_AUTHOR'); ?></th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($this->templates as $num=>$item) {
	$selected = $item->folder == $this->template;
?>
				<tr class="row<?php echo $num%2; ?>">
					<td><?php echo $num + 1; ?></td>
					<td width="20">
					    <input type="radio" id="cb0" name="cid[]" value="<?php echo $item->folder; ?>"<?php if ($selected) { ?> checked<?php } ?> />
					</td>
					<td>
						<span class="hasTip" title="<?php echo $item->name; ?>::<?php if ($item->preview) { ?><img src=&quot;<?php echo $item->preview; ?>&quot; /><?php } else { echo JText::_( 'No preview available' ); } ?>"><?php echo $item->name; ?></span>
					</td>
					<td align="center">
					    <?php if ($selected) { ?><img src="components/com_fwgallery/assets/images/icon-16-default.png" alt="<?php echo JText::_( 'Default' ); ?>" /><?php } ?>
					</td>
					<td align="center"><?php echo $item->version; ?></td>
					<td><?php echo $item->author; ?></td>
				</tr>
<?php
}
?>
			</tbody>
	    </table>
    </fieldset>
	<input type="hidden" name="task" value="" />
</form>
<form action="index.php?option=com_fwgallery&amp;view=templates" method="post" name="uploadForm" id="uploadForm" enctype="multipart/form-data">
	<table class="adminform">
		<tbody>
			<tr>
				<th colspan="2"><?php echo JText :: _('FWG_UPLOAD_PACKAGE_FILE'); ?></th>
			</tr>
			<tr>
				<td width="120">
					<?php echo JText :: _('FWG_PACKAGE_FILE'); ?>:
				</td>
				<td>
					<input type="file" size="57" name="install_package" id="install_package" class="input_box">
					<input type="submit" value="Upload File &amp; Install" class="button">
				</td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" value="install" name="task">
</form>