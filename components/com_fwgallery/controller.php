<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryController extends JControllerLegacy {
    function __construct($config = array())    {
        parent::__construct($config);
        $this->registerTask('add', 'edit');
        $this->registerDefaultTask('galleries');
    }
    function galleries() {
        JRequest::setVar('view',JRequest::getVar('view', 'galleries'));
        parent::display();
    }
    function vote() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        $view = $this->getView($view_name, 'html');
        $view->setModel($model, true);
    	if (method_exists($view, 'vote')) {
    		$view->vote();
    	} else {
    		parent :: display();
    	}
    }
    function download() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        $view = $this->getView($view_name, 'html');
        $view->setModel($model, true);
    	if (method_exists($view, 'download')) {
    		$view->download();
    	} else {
    		parent :: display();
    	}
    }
    function edit() {
    	$data = explode('_', JRequest :: getCmd('layout'));
		if (count($data) == 2) {
			JRequest :: setVar('layout', 'edit_'.$data[1]);
		} else JRequest :: setVar('layout', 'edit');
		parent :: display();
    }
    function apply() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);

        if (method_exists($model, 'save')) {
            $id = $model->save();
            $msg = $model->getError();
        } else {
            $id = JArrayHelper :: getValue(JRequest :: getVar('cid', array(), 'post', 'ARRAY'), 0);
            $msg = JText::_('FWG_METHOD_APPLY_IS_NOT_EXISTS');
        }
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.'&layout=edit&cid[]='.$id, false), $msg);
    }
    function save() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);

        if (method_exists($model, 'save')) {
            $model->save();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_SAVE_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function remove() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'remove')) {
            $model->remove();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_REMOVE_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function saveorder() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'saveorder')) {
            $model->saveorder();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_SAVEORDER_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function orderup() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'orderup')) {
            $model->orderup();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_ORDERUP_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function orderdown() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'orderdown')) {
            $model->orderdown();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_ORDERDOWN_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function publish() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'publish')) {
            $model->publish();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_PUBLISH_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }

    function unpublish() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'unpublish')) {
            $model->unpublish();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_UNPUBLISH_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
	    $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function select() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'select')) {
            $model->select();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_SELECT_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function unselect() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'unselect')) {
            $model->unselect();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_UNSELECT_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function clockwise() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'clockwise')) {
	    	$model->clockwise();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_CLOCKWISE_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function counterclockwise() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'counterClockwise')) {
	    	$model->counterClockwise();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_COUNTERCLOCKWISE_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
    function processPlugin() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'processPlugin')) {
	    	$model->processPlugin();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD_PROCESS_PLUGIN_IS_NOT_EXISTS');
        }
    	$limitstart = JRequest :: getInt('limitstart');
    	$layout = JRequest :: getCmd('layout');
        $this->setRedirect(JRoute :: _('index.php?option=com_fwgallery&view='.$view_name.($limitstart?'&limitstart='.$limitstart:'').($layout?'&layout='.$layout:''), false), $msg);
    }
}
?>