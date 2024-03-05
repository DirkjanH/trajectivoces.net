<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2011 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelConfig extends JModelLegacy {
    function loadObj() {
    	$obj = new stdclass;
    	$obj->params = JComponentHelper :: getParams('com_fwgallery');
        return $obj;
    }
    function getPlugins($config) {
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper :: importPlugin('fwgallery');
		return $dispatcher->trigger('getAdminConfig', array($config));
    }
    function getFormPlugins($config) {
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper :: importPlugin('fwgallery');
		return $dispatcher->trigger('getAdminConfigForm', array($config));
    }

    function save() {
    	$params = JComponentHelper :: getParams('com_fwgallery');
		$data = (array)JRequest :: getVar('config');
    	$fields = array(
			'im_just_shrink',
			'use_watermark',
			'use_voting',
			'public_voting',
			'display_total_galleries',
			'display_galleries_limit_selector',
			'display_gallery_limit_selector',
			'display_owner_gallery',
			'display_owner_image',
			'display_date_gallery',
			'display_gallery_sorting',
			'display_name_gallery',
			'display_name_image',
			'display_date_image',
			'display_image_views',
			'allow_image_download',
			'allow_print_button',
			'hide_bottom_image',
			'display_user_copyright',
			'hide_mignifier',
			'hide_single_image_view',
			'hide_iphone_app_promo',
			'hide_fw_copyright',
			'display_galleries_lightbox',
			'display_social_sharing',
			'display_image_tags'
		);
		foreach ($fields as $field) $data[$field] = JRequest :: getVar($field);
    	$params->loadArray($data);

		$cache = JFactory :: getCache('_system', 'callback');
    	$cache->clean();

    	$params->set('gallery_color', trim($params->get('gallery_color'), '#'));
    	if (!$params->get('gallery_box_width')) $params->set('gallery_box_width', $params->get('im_th_w'));
    	if (!$params->get('image_box_width')) $params->set('image_box_width', $params->get('im_mid_w'));

		if (JRequest :: getInt('delete_watermark') and $filename = $params->get('watermark_file')) {
			if (file_exists(FWG_STORAGE_PATH.$filename)) @unlink(FWG_STORAGE_PATH.$filename);
			$params->set('watermark_file', '');
		}

    	if ($file = JRequest :: getVar('watermark_file', '', 'files')
    	 and $name = JArrayHelper :: getValue($file, 'name')
    	  and !JArrayHelper :: getValue($file, 'error')
    	   and preg_match('/\.png|gif$/i', $name)
    	   	and move_uploaded_file(JArrayHelper :: getValue($file, 'tmp_name'), FWG_STORAGE_PATH.$name)) {
    		$params->set('watermark_file', $name);
    	}

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper :: importPlugin('fwgallery');
		$dispatcher->trigger('handleConfigStore', $buff = array(&$params));

    	$db = JFactory :: getDBO();
    	$db->setQuery('UPDATE #__extensions SET params = '.$db->quote($params->toString()).' WHERE `element` = \'com_fwgallery\' AND `type` = \'component\'');
    	return $db->query();
    }
}
?>