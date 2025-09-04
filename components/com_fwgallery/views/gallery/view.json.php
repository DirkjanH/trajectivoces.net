<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryViewGallery extends JViewLegacy {
    function display($tmpl=null) {
        $model = $this->getModel();

        JRequest :: setVar('limit', '0');
        JRequest :: setVar('limitstart', '0');
        $list = array(
        	'images' => array()
        );
        if ($list['images'] = (array)$model->getList()) foreach ($list['images'] as $i=>$row) {
			$list['images'][$i]->descr = htmlentities(strip_tags($row->descr));
			$list['images'][$i]->link = JURI :: root(false).JFHelper::getFileFilename($row);
			$list['images'][$i]->color = JFHelper :: getGalleryColor($row->project_id);
        } else {
        	$image = new stdclass;
        	$image->descr = '';
        	$image->link = '';
        	$image->color = '';
        	$image->id = 0;
        	$image->project_id = 0;
        	$image->type_id = 0;
        	$image->user_id = 0;
        	$image->published = '1';
        	$image->ordering = 0;
        	$image->hits = 0;
        	$image->name = '';
        	$image->filename = '';
        	$image->created = date('Y-m-d');
        	$image->longitude = 0;
        	$image->latitude = 0;
        	$image->selected = 0;
        	$image->copyright = '';
        	$image->link = JURI :: root(false).'components/com_fwgallery/assets/images/no_image.jpg';
        	$list['images']['1'] = $image;
        }
        echo json_encode($list);
        die();
    }
}
?>