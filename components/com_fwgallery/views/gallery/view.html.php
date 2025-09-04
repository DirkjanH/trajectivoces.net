<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewGallery extends fwGalleryView {
    function display($tmpl=null) {
        $model = $this->getModel();
        $app = JFactory::getApplication();
        $user = JFactory::getUser();

        /* collect data for a template */
        $this->assign('obj', $model->getObj());
        if (!$this->obj->id) {
        	$app->redirect(
        		JRoute :: _('index.php?option=com_fwgallery&view=galleries&Itemid='.JFHelper :: getItemid('galleries'), false)
    		);
        } elseif ($this->obj->gid and !in_array($this->obj->gid, $user->groups)) {
        	$app->redirect(
        		JRoute :: _('index.php?option=com_fwgallery&view=galleries&Itemid='.JFHelper :: getItemid('galleries'), false),
        		JText :: _('FWG_GALLERY_ACCESS_DENIED')
    		);
        } elseif (!$this->obj->published) {
        	$app->redirect(
        		JRoute :: _('index.php?option=com_fwgallery&view=galleries&Itemid='.JFHelper :: getItemid('galleries'), false),
        		JText :: _('FWG_GALLERY_NOT_PUBLISHED')
    		);
        }

        $this->assign('own', $user->id and (!JRequest::getInt('id') or $user->id == $this->obj->user_id));
		$this->assign('params',  JComponentHelper :: getParams('com_fwgallery'));

        $menu = JMenu :: getInstance('site');
        $active_menu_item = $menu->getActive();

        $pathway = $app->getPathway();

		if (!$active_menu_item or ($active_menu_item and strpos($active_menu_item->link, 'galleries') === false ) and $this->params->get('id') != $this->obj->id) {
	        $pathway->addItem(JText::_('FWG_GALLERIES'), JRoute::_('index.php?option=com_fwgallery&view=galleries'));
		}

		$this->assign('path', $model->loadCategoriesPath($this->obj->parent));
        if ($this->path and $this->params->get('id') != $this->obj->id){
        	 foreach ($this->path as $item) {
        	 	$pathway->addItem($item->name, JRoute::_('index.php?option=com_fwgallery&view=gallery&id='.$item->id.':'.JFilterOutput :: stringURLSafe($item->name).'&Itemid='.JFHelper :: getItemid('gallery', $item->id, JRequest :: getInt('Itemid'))).'#fwgallerytop');
        	 }
        }
        if ($this->params->get('id') != $this->obj->id) $pathway->addItem($this->obj->name);

        $this->setLayout('default');
        $this->assign('order', $model->getUserState('order', $this->params->get('ordering_images')));
        $this->assign('subcategory_order', $model->getUserState('subcategory_order', $this->params->get('ordering_galleries')));
        $this->assign('subcategories', $model->loadSubCategories());
        $this->assign('gpagination', $model->getGPagination());
        $this->assign('list', $model->getList());
        $this->assign('pagination', $model->getPagination());
        $this->assign('user', $user);

        parent::display($tmpl);
    }
    function vote() {
        $model = $this->getModel();
        $user = JFactory::getUser();
        $model->vote();

        $this->assign('user', $user);
        $this->assign('row', $model->getImage());
        $this->assign('msg', $model->getError());
        $this->assign('own', $user->id and (!JRequest::getInt('id') or $user->id == $this->row->_user_id));
		$this->assign('params',  JComponentHelper :: getParams('com_fwgallery'));
        $this->setLayout('default_vote');
        parent :: display();

    }
}
?>