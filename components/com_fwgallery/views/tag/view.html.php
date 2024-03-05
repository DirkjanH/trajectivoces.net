<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewTag extends fwGalleryView {
    function display($tmpl=null) {
        $model = $this->getModel();
        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        /* collect data for a template */
        $this->assign('obj', $model->getObj());

		$this->assign('params',  JComponentHelper :: getParams('com_fwgallery'));

        $this->setLayout('default');
        $this->assign('order', $model->getUserState('order', $this->params->get('ordering_images')));
        $this->assign('list', $model->getList());
        $this->assign('pagination', $model->getPagination());
        $this->assign('user', $user);

        parent::display($tmpl);
    }
}
?>