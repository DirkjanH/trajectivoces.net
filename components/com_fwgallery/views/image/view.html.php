<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewImage extends fwGalleryView {
    function display($tmpl=null) {
        $model = $this->getModel();
        $app = JFactory::getApplication();
        $this->assign('row', $model->getObj());
        if (!$this->row->id) {
        	$app->redirect(
        		JRoute :: _('index.php?option=com_fwgallery&view=galleries&Itemid='.JFHelper :: getItemid('galleries'), false),
        		JText :: _('FWG_IMAGE_NOT_FOUND_OR_ACCESS_DENIED')
    		);
        }

        $this->assign('user', JFactory::getUser());
        $this->assign('own', $this->row->_user_id == $this->user->id);
        $this->assign('previous_image', $model->getPreviousImage($this->row));
        $this->assign('next_image', $model->getNextImage($this->row));
        $this->assign('comments', $model->loadCommentingSystem($this->row));
		$this->assign('params',  JComponentHelper :: getParams('com_fwgallery'));
		$this->assign('plugin_output', $model->getPluginOutput($this->row));
		$this->assign('image_plugins', $model->loadFrontendImagePlugins($this->row));
		$doc = JFactory :: getDocument();
		$doc->setTitle($this->row->name);

        $pathway = $app->getPathway();
        if ($this->params->get('id') != $this->row->id) {
        	if ($path = $model->loadCategoriesPath($this->row->project_id)) foreach ($path as $item) {
		        $pathway->addItem(
		        	$item->name,
		        	JRoute::_('index.php?option=com_fwgallery&view=gallery&id='.$item->id.':'.JFilterOutput :: stringURLSafe($item->name).
						'&Itemid='.JFHelper :: getItemid('gallery', $item->id, JRequest :: getInt('Itemid')).'#fwgallerytop')
				);
        	}
	        $pathway->addItem($this->row->name);
        }
        if ($this->params->get('display_social_sharing')) {
			if ($doc->_links) foreach ($doc->_links as $key => $val) {
				if ($val['relation'] == 'canonical') {
					unset($doc->_links[$key]);
					break;
				}
			}
			$uri = JURI :: getInstance();
			$doc->addCustomTag('<meta property="og:url" content="'.$uri->toString().'"/>');
			$doc->addCustomTag('<meta property="og:image" content="'.JURI :: root(false).JFHelper::getFileFilename($this->row).'"/>');
			$doc->addCustomTag('<meta property="og:title" content="'.str_replace('"', '&quot;', $this->row->name).'"/>');
			if ($this->row->descr) $doc->addCustomTag('<meta property="og:description" content="'.str_replace(array('"', "\r", "\n"), array('&quot;', ' ',  ' '), strip_tags($this->row->descr)).'"/>');
        }
        parent::display($tmpl);
    }
    function download() {
        $model = $this->getModel();
        $image = $model->getObj();
        if ($image->id and JFHelper :: isFileExists($image, 'orig')) {
			if (!headers_sent()) {
				jimport('joomla.filesystem.file');
				$ext = strtolower(JFile :: getExt($image->filename));
				if ($ext == 'jpeg') $ext = 'jpg';
				header('Content-type: image/'.$ext);
				header('Content-Disposition: attachment; filename="'.$image->filename.'"');
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private', false);
			}
			echo file_get_contents(JFHelper :: getFileFilename($image, 'orig'));
	    	die();
        } else {
        	$app = JFactory :: getApplication();
        	$app->redirect(JRoute :: _('index.php?option=com_fwgallery&view=galleries', false), JText :: _('FWG_IMAGE_NOT_FOUND'));
        }
    }
}
?>
