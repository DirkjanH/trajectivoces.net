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
        $this->registerTask('add','edit');
    }
    function edit() {
        $viewName = JRequest::getCmd('view', $this->getName());
        $view = $this->getView($viewName, 'html');
        $view->setModel($this->getModel($viewName), true);
        $view->setLayout('edit');
        $view->edit();
    }
    function apply() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);

        if (method_exists($model, 'save')) {
            $id = $model->save();
            $msg = $model->getError();
        } else {
            $id = JArrayHelper :: getValue(JRequest :: getVar('cid', array(), 'post', 'ARRAY'), 0);
            $msg = JText::_('FWG_METHOD__APPLY__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name.'&task=edit&cid[]='.$id, $msg);
    }
    function install() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);

        if (method_exists($model, 'install')) {
            $model->install();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__INSTALL__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function save() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);

        if (method_exists($model, 'save')) {
            $model->save();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__SAVE__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function remove() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'remove')) {
            $model->remove();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__REMOVE__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function saveorder() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'saveorder')) {
            $model->saveorder();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__SAVEORDER__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function orderup() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'orderup')) {
            $model->orderup();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__ORDERUP__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function orderdown() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'orderdown')) {
            $model->orderdown();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__ORDERDOWN__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function publish() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'publish')) {
            $model->publish();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__PUBLISH__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }

    function unpublish() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'unpublish')) {
            $model->unpublish();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__UNPUBLISH__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function select() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'select')) {
            $model->select();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__SELECT__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function unselect() {
        $view_name = JRequest::getString('view');
        $model = $this->getModel($view_name);
        if (method_exists($model, 'unselect')) {
            $model->unselect();
            $msg = $model->getError();
        } else {
            $msg = JText::_('FWG_METHOD__UNSELECT__IS_NOT_EXISTS');
        }
        $this->setRedirect('index.php?option=com_fwgallery&view='.$view_name, $msg);
    }
    function clockwise() {
        $model = $this->getModel('files');
    	$model->clockwise();
        $this->setRedirect('index.php?option=com_fwgallery&view=files', $model->getError());
    }
    function counterclockwise() {
        $model = $this->getModel('files');
    	$model->counterClockwise();
        $this->setRedirect('index.php?option=com_fwgallery&view=files', $model->getError());
    }
}
?>