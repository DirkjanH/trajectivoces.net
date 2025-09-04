<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewfwGallery extends JViewLegacy {
    function display($tmpl=null) {
        $model = $this->getModel();
		$this->getMenu();

        $this->assign('client', $model->getUserState('client'));
        $this->assign('clients', $model->getClients());
        $this->assign('projects', $model->getProjects());
        $this->assign('pagination', $model->getPagination());
        parent::display($tmpl);
    }

    function edit($tmpl=null) {
        $model = $this->getModel();

		$this->getMenu();
        $this->assign('user', JFactory :: getUser());
        $this->assign('groups', JFHelper :: getGroups());
        $this->assign('clients', $model->getClients());
        $this->assign('obj', $model->getProject());
        parent::display($tmpl);
    }
    function getMenu() {
		JSubMenuHelper::addEntry(
			JText::_('FWG_GALLERIES'),
			'index.php?option=com_fwgallery&view=fwgallery',
			true
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
			false
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