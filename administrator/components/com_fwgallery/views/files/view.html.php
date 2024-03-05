<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewFiles extends JViewLegacy {
    function display($tmpl=null) {
        $model = $this->getModel();

		$this->getMenu();
        $this->assign('types', JFHelper :: loadTypes($published_only = false));
        $this->assign('search', $model->getUserState('search', '', 'string'));
        $this->assign('project_id', $model->getUserState('project_id'));
        $this->assign('projects', $model->getProjects());
        $this->assign('files', $model->getFiles());
        $this->assign('pagination', $model->getPagination());
        parent::display($tmpl);
    }

    function edit($tmpl=null) {
        $model = $this->getModel();
        $this->assign('projects', $model->getProjects());
        if (!$this->projects) {
			$app = JFactory :: getApplication();
			$app->redirect(JRoute :: _('index.php?option=com_fwgallery&view=fwgallery', JText :: _('FWG_CREATE_A_GALLERY_FIRST')));
        }

		$this->getMenu();
        $this->assign('types', JFHelper :: loadTypes($published_only = true));
        $this->assign('user', JFactory :: getUser());
        $this->assign('clients', $model->getClients());
        $this->assign('obj', $model->getFile());
        $this->assign('params', JComponentHelper :: getParams('com_fwgallery'));
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
			true
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