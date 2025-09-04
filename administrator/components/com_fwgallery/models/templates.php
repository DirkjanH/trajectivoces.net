<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelTemplates extends JModelLegacy {
	function save () {
    	$cid = (array)JRequest::getVar('cid');
    	if (is_array($cid)) {
    		$params = JComponentHelper :: getParams('com_fwgallery');
    		$params->set('template', strtolower(trim($cid[0])));
    		$db = JFactory :: getDBO();
	    	$db->setQuery('UPDATE #__extensions SET params = '.$db->quote($params->toString()).' WHERE `element` = \'com_fwgallery\' AND `type` = \'component\'');
	    	if ($db->query()) return true;
	    	else $this->setError($db->getErrorMsg());
    	}
	}
	function install() {
		if ($file = JRequest :: getVar('install_package', array(), 'FILES', 'ARRAY') and empty($file['error']) and !empty($file['name'])) {
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');
			$path = JPATH_SITE.'/tmp/';
			if (!file_exists($path)) JFolder :: create($path);
			if (file_exists($path)) {
				$filename = '';
				$ext = JFile :: getExt($file['name']);
				do {
					$filename = md5(rand());
				} while (file_exists($path.$filename));

				if (JFolder :: create($path.$filename) and JFile :: copy($file['tmp_name'], $path.$filename.'/'.$filename.'.'.$ext)) {
					jimport('joomla.filesystem.archive');
					if (JArchive :: extract($path.$filename.'/'.$filename.'.'.$ext, $path.$filename)) {
						jimport('joomla.installer.installer');
						$installer = JInstaller::getInstance();
						if ($installer->install($path.$filename)) {
							JFolder :: delete($path.$filename);
							$this->setError(JText :: _('FWG_EXTENSION_SUCCESFULLY_INSTALLED'));
							return true;
						} else $this->setError($installer->getError());
					} else $this->setError(JText :: _('FWG_CAN__T_UNPACK_THE_PACKAGE'));
				} else $this->setError(JText :: _('FWG_NO_COPY_UPLOADED_FILE'));
			} else $this->setError(JText :: _('FWG_NO_WRITE_ACCESS_TO_TMP_FOLDER'));
		}
	}
	function remove() {
		if ($name = JArrayHelper :: getValue(JRequest :: getVar('cid'), 0)) {
			$db = JFactory :: getDBO();
	    	$db->setQuery('SELECT extension_id FROM #__extensions WHERE `element` = \'com_fwgallerytmpl'.$db->escape($name).'\' AND `type` = \'component\'');
			if ($id = $db->loadResult()) {
				jimport('joomla.installer.installer');
				$installer = new JInstaller();
				if ($installer->uninstall('component', $id)) {
					echo JText::_('FWG_TEMPLATE_SUCCESFULLY_UNINSTALLED');
					return true;
				} else
					echo JText::sprintf('FWG_COULD_NOT_UNINSTALL_THE_TEMPLATE___S', $installer->getError());
			} else
				echo JText::_('FWG_TEMPLATE_NOT_FOUND');
		}
	}
}
?>