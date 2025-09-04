<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewGalleries extends fwGalleryView {
    function display($tmpl=null) {
        $model = $this->getModel();
        $user = JFactory::getUser();

        $this->assign('obj', $model->getObj());
        $this->assign('own', (bool)$user->id and ($user->id == $this->obj->id));
        $this->assign('list', $model->getList());
        $this->assign('pagination', $model->getPagination());
        $this->assign('title', $model->getTitle());
		$this->assign('params',  JComponentHelper :: getParams('com_fwgallery'));
        $this->assign('order', $model->getUserState('order', $this->params->get('ordering_galleries')));

        if ($this->obj->id) {
            /* set correct breadcrump */
            $app = JFactory::getApplication();
            $pathway = $app->getPathway();
            $pathway->addItem('Galleries');
        }

        parent::display($tmpl);
    }
}
?>