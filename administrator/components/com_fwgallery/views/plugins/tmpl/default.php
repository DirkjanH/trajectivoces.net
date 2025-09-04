<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JToolBarHelper :: title(JText::_('FWG_PLUGINS'), 'fwgallery-plugins.png' );

?>
<table style="width:100%;">
	<tr style="vertical-align:top;">
		<td style="width:60%">
			<h3><?php echo JText :: _('FWG_FW_GALLERY_PLUGINS'); ?></h3>
			<p><?php echo JText :: _('FWG_PLUGIN_SECTION'); ?></p>
<?php
if ($this->plugins) {
	foreach ($this->plugins as $plugin) if ($plugin) {
?>
			<fieldset class="adminform">
<?php
		echo $plugin;
?>
			</fieldset>
<?php
	}
} elseif ($this->fwgallery_plugins_installed) {
	echo JText :: _('FWG_PUBLISH_FW_GALLERY_PLUGIN_S__TO_USE_IT');
} else {
	echo JText :: _('FWG_NO_FW_GALLERY_PLUGINS_INSTALLED');
}
?>
		</td>
		<td>
			<h3><?php echo JText :: _('FWG_LIST_OF_INSTALLED_FW_GALLERY_PLUGINS'); ?></h3>
<?php
if ($this->installed_plugins) {
?>
			<table class="table table-striped">
				<thead>
					<th><?php echo JText :: _('FWG_PLUGIN_NAME'); ?></th>
					<th><?php echo JText :: _('FWG_PLUGIN_VERSION'); ?></th>
					<th><?php echo JText :: _('FWG_PLUGIN_PUBLISH'); ?></th>
				</thead>
				<tbody>
<?php
	foreach ($this->installed_plugins as $i=>$plugin) {
?>
					<tr class="row<?php echo $i%2; ?>">
						<td><?php echo $plugin->element; ?> [<a href="index.php?option=com_plugins&amp;task=plugin.edit&amp;extension_id=<?php echo $plugin->id; ?>"><?php echo JText :: _('FWG_DETAILS'); ?></a>]</td>
						<td style="text-align:center;"><?php echo $plugin->version; ?></td>
						<td style="text-align:center;"><a href="index.php?option=com_fwgallery&amp;view=plugins&task=<?php echo $plugin->published?'unpublish':'publish'; ?>&cid[]=<?php echo $plugin->id; ?>" alt="<?php echo JText :: _($plugin->published?'Click to unpuplish plugin':'Click to publish plugin'); ?>"><img src="components/com_fwgallery/assets/images/<?php echo $plugin->published?'tick':'publish_x'; ?>.png"/></a></td>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
<?php
}
?>
		</td>
	</tr>
</table>
