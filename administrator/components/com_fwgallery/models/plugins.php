<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelPlugins extends JModelLegacy {
	function getPlugins() {
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper :: importPlugin('fwgallery');
		return $dispatcher->trigger('fwGetAdminForm');
	}
	function getInstalledPlugins() {
		$db = JFactory :: getDBO();
		$db->setQuery('SELECT extension_id AS id, element, folder, enabled AS published, \'\' AS `version` FROM #__extensions WHERE `type` = \'plugin\' AND (`folder` = \'fwgallery\' OR `name` LIKE \'%FW Gallery%\') ORDER BY ordering');

		$plugins = $db->loadObjectList();
		if ($plugins) foreach ($plugins as $i=>$plugin) {
			$filename = JPATH_PLUGINS.'/'.$plugin->folder.'/'.$plugin->element.'/'.$plugin->element.'.xml';
			if (file_exists($filename)) {
				$file = file_get_contents($filename);
				if (preg_match('#<name>(.*?)</name>#i', $file, $m)) $plugins[$i]->element = $m[1];
				if (preg_match('#<version>(.*?)</version>#i', $file, $m)) $plugins[$i]->version = $m[1];
			}
		}
		return $plugins;
	}
	function checkGalleryPlugins($installed_plugins) {
		if ($installed_plugins) foreach ($installed_plugins as $plugin) {
			if ($plugin->folder == 'fwgallery') return true;
		}
	}
	function getPlugin() {
		if ($plugin = JRequest :: getString('plugin'))
			return JPluginHelper :: getPlugin('fwgallery', $plugin);
	}
	function processPlugin(&$plugin) {
		if (JPluginHelper :: importPlugin('fwgallery', $plugin->name)) {
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger('fwProcess');
		}
	}
	function publish() {
		$plugin = JTable :: getInstance('Extension', 'JTable');
		$plugin->publish(JRequest :: getVar('cid'), 1);
	}
	function unpublish() {
		$plugin = JTable :: getInstance('Extension', 'JTable');
		$plugin->publish(JRequest :: getVar('cid'), 0);
	}
}
?>