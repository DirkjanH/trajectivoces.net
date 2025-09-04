<?php
/**
 * FW Real Estate x.x.x - Joomla! Property Manager
 * @copyright (C) 2011 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewLanguage extends JViewLegacy {
    function display($tmpl=null) {
        $model = $this->getModel();

		$this->getMenu();

		$this->assignRef('type', $model->getUserState('type', 0, 'int'));
        $this->assignRef('search', $model->getUserState('search', '', 'string'));
        $this->assignRef('language', JFHelper :: getLanguage());
        $this->assignRef('languages', JFHelper :: getInstalledLanguages());
        $this->assignRef('data', $model->getLanguageData());
        parent::display($tmpl);
    }
    function getMenu() {
		JSubMenuHelper::addEntry(
			JText::_('FWG_GALLERIES'),
			'index.php?option=com_fwgallery&view=fwgallery',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_IMAGES'),
			'index.php?option=com_fwgallery&view=files',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_PLUGINS'),
			'index.php?option=com_fwgallery&view=plugins',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_TEMPLATES'),
			'index.php?option=com_fwgallery&view=templates',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_LANGUAGE'),
			'index.php?option=com_fwgallery&view=language',
			true
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_CONFIGURATION'),
			'index.php?option=com_fwgallery&view=config',
			false
		);
		JSubMenuHelper::addEntry(
			JText::_('FWG_IPHONE_APP'),
			'index.php?option=com_fwgallery&view=iphone',
			false
		);
    }
}
?>