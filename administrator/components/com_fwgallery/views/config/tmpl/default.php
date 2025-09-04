<?php
/**
 * FW Gallery 3.2.2
 * @copyright (C) 2013 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
JToolBarHelper :: title(JText::_('FWG_CONFIG'), 'fwgallery-config.png' );
JToolBarHelper :: custom('edit', 'edit', 'edit', 'edit', false);

?>
<strong><?php echo JText :: _('FWG_CONFIGURATION_HINT'); ?></strong>
<?php
if (!file_exists(FWG_STORAGE_PATH) and !is_writable(JPATH_SITE.'/images')) {
?>
<p style="color:#f00;"><?php echo JText :: sprintf('FWG_IMAGES_FOLDER__S_IS_NOT_WRITABLE_', JPATH_SITE.'/images') ?></p>
<?php
}
if (file_exists(FWG_STORAGE_PATH) and !is_writable(FWG_STORAGE_PATH)) {
?>
<p style="color:#f00;"><?php echo JText :: sprintf('FWG_IMAGES_FOLDER__S_IS_NOT_WRITABLE_', FWG_STORAGE_PATH) ?></p>
<?php
}
if (!function_exists('exif_read_data')) {
?>
<p style="color:#f00;"><?php echo JText :: _('FWG_EXIF_DOES_NOT_ENABLED') ?></p>
<?php
}
?>
<form action="index.php?option=com_fwgallery&amp;view=config" method="post" name="adminForm" id="adminForm">

	<div id="settings1" style="width:30%;float:left;">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_IMAGE_SETTINGS'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_THUMB_WIDTH'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('im_th_w'); ?><?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_THUMB_HEIGHT'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('im_th_h'); ?><?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_MEDIUM_SIZE_WIDTH'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('im_mid_w'); ?><?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_MEDIUM_SIZE_HEIGHT'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('im_mid_h'); ?><?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_BIG_SIZE_WIDTH'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('im_max_w'); ?><?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_BIG_SIZE_HEIGHT'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('im_max_h'); ?><?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td title="" class="key">
                    <?php echo JText::_('FWG_THUMB_AND_MEDIUM_IMAGES_SHRINKING_METHOD'); ?>:
	            </td>
	            <td title="">
					<?php echo JText :: _($this->obj->params->get('im_just_shrink')?'Just shrink':'Shrink and cut'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_GALLERY_BOX_WIDTH'); ?>:
	            </td>
	            <td>
					<?php if ($gallery_box_width = $this->obj->params->get('gallery_box_width')) echo $gallery_box_width.JText :: _('FWG_PX'); else echo JText :: _('FWG_AUTO'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_IMAGE_BOX_WIDTH'); ?>:
	            </td>
	            <td>
					<?php if ($image_box_width = $this->obj->params->get('image_box_width')) echo $image_box_width.JText :: _('FWG_PX'); else echo JText :: _('FWG_AUTO'); ?>
	            </td>
	        </tr>
	    </table>
    </fieldset>
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_LAYOUT_SETTINGS'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_GALLERIES_DEFAULT_COLOR'); ?>:
	            </td>
	            <td>
					<div class="fwg-gallery-color"<?php if ($color = $this->obj->params->get('gallery_color')) { ?> style="background-color:#<?php echo $color; ?>"<?php } ?>></div>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_GALLERIES_IN_A_ROW'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('galleries_a_row'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_IMAGES_IN_A_ROW'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('images_a_row'); ?>
	            </td>
	        </tr>

	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_GALLERIES_PER_PAGE'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('galleries_limit'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_IMAGES_PER_PAGE'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('images_limit'); ?>
	            </td>
	        </tr>

	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DEFAULT_GALLERIES_ORDERING'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _('FWG_ORDERING_'.$this->obj->params->get('ordering_galleries', 'order')); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DEFAULT_IMAGES_ORDERING'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _('FWG_ORDERING_'.$this->obj->params->get('ordering_images', 'order')); ?>
	            </td>
	        </tr>
	    </table>
    </fieldset>
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_WATERMARK'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_USE_WATERMARK'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('use_watermark')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_WATERMARK_POSITION'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('watermark_position', 'left bottom')); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_WATERMARK_FILE'); ?>:
	            </td>
	            <td>
<?php
if ($this->obj->params->get('watermark_file')) {
	if ($path = JFHelper :: getWatermarkFilename()) {
?>
					<img src="<?php echo JURI :: root(true); ?>/<?php echo $path; ?>" /><br/>
<?php
	} else {
?>
					<p style="color:#f00;"><?php echo JText :: _('FWG_WATERMARK_FILE_NOT_FOUND_'); ?></p>
<?php
	}
}
?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_WATERMARK_TEXT'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('watermark_text'); ?>
	            </td>
	        </tr>
		</table>
	</fieldset>
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_VOTING_SYSTEM'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_USE_VOTING'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('use_voting')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_PUBLIC_VOTING'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('public_voting')?'Yes':'No'); ?>
	            </td>
	        </tr>
		</table>
	</fieldset>
	</div>

	<div id="settings2" style="width:30%;float:left;">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_DISPLAYING_SETTINGS'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_TOTAL_GALLERIES_COUNTER'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_total_galleries')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_SHOW__DISPLAY_LIMIT__OPTION_FOR_LIST_OF_GALLERIES'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_galleries_limit_selector')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_SHOW__DISPLAY_LIMIT__OPTION_FOR_SINGLE_GALLERY'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_gallery_limit_selector')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_GALLERY_OWNER_NAME'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_owner_gallery')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_OWNER_NAME'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_owner_image')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_GALLERY_DATE'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_date_gallery')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_ORDER_BY_OPTION'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_gallery_sorting')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_GALLERY_NAME'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_name_gallery')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_NAME'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_name_image')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_DATE'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_date_image')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_VIEWS'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_image_views')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_ALLOW_IMAGE_DOWNLOAD'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('allow_image_download')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_TAGS'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_image_tags')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_ALLOW_PRINT_BUTTON'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('allow_print_button')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_BOTTOM_IMAGES'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('hide_bottom_image')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_USERS_COPYRIGHT'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_user_copyright')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DATE_FORMAT'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('date_format'); ?>
					&nbsp;<a href="http://docs.joomla.org/How_do_you_change_the_date_format%3F" target="_blank"><?php echo JText :: _('FWG_DATE_OPTIONS'); ?></a>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_ICON_NEW_DAYS'); ?>:
	            </td>
	            <td>
					<?php echo $this->obj->params->get('new_days'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText :: _('FWG_DISPLAY_LIGHTBOX_GALLERIES'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_galleries_lightbox')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_MIGNIFIER__LIGHTBOX_EFFECT_'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('hide_mignifier')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_SINGLE_IMAGE_VIEW'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('hide_single_image_view')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_IPHONE_APP_PROMO'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('hide_iphone_app_promo')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_SOCIAL_SHARING'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('display_social_sharing')?'Yes':'No'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_FASTW3B_COPYRIGHT'); ?>:
	            </td>
	            <td>
					<?php echo JText :: _($this->obj->params->get('hide_fw_copyright')?'Yes':'No'); ?>
	            </td>
	        </tr>
		</table>
	</fieldset>
<?php
if ($this->plugins) {
?>
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_ADDITIONAL_SETTINGS'); ?></legend>
<?php
	foreach ($this->plugins as $plugin) if ($plugin) {
?>
		<fieldset class="adminform">
<?php
		echo $plugin;
?>
		</fieldset>
<?php
	}
?>
	</fieldset>
<?php
}
?>
	</div>
	<div id="description" style="width:40%;float:left;">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_FW_GALLERY'); ?></legend>

			<p><img style="margin-right: 10px; float: left !important;" src="<?php echo JURI :: base(true); ?>/components/com_fwgallery/assets/images/01_c_gallery_150.png" /></p>
			<p><span style="text-decoration: underline;"><strong><?php echo JText::_('FWG_PROJECT_DETAILS'); ?></strong></span></p>
			<div><strong><?php echo JText::_('FWG_CREATION_DATE'); ?></strong>:<br/><?php echo JText::_('FWG_JULY_8__2010'); ?></div>
			<div><strong><?php echo JText::_('FWG_VERSION'); ?></strong>:<br/>2.3.2</div>
			<div><strong><?php echo JText::_('FWG_TYPE_EXT'); ?></strong>:<br/><?php echo JText::_('FWG_NON_COMMERCIAL'); ?></div>
			<div><strong><?php echo JText::_('FWG_LICENSE'); ?></strong>: GPLv2</div>
			<div class="clr"></div>

			<p><span style="text-decoration: underline;"><strong><?php echo JText::_('FWG_USEFUL_LINKS'); ?>:</strong></span></p>
			<ul>
				<li><?php echo JText::_('FWG_CHECK_FOR'); ?> <a target="_blank" href="http://fastw3b.net/index.php?option=com_user&amp;view=login&amp;return=aHR0cDovL2Zhc3R3M2IubmV0L2NsaWVudC1zZWN0aW9uLW15LWRvd25sb2Fkcy5odG1s"><?php echo JText::_('FWG_UPDATES'); ?> &gt;&gt;</a> (<?php echo JText::_('FWG_LOGIN_FIRST'); ?>)</li>
				<li><?php echo JText::_('FWG_ANY_IDEAS'); ?> <a href="mailto:wehearyou@fastw3b.net"><?php echo JText::_('FWG_IDEAS_OR_SUGGESTIONS'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_HAVE_ANY'); ?> <a target="_blank" href="http://fastw3b.net/forum.html"><?php echo JText::_('FWG_QUESTIONS'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_OUR_CLIENTS_FEEDBACKS_AND_YOUR_REVIEWS_ON'); ?> <a target="_blank" href="http://extensions.joomla.org/extensions/photos-a-images/photo-gallery/13185"><?php echo JText::_('FWG_JED'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FOLLOW_FW_GALLERY_NEWS_ON'); ?> <a target="_blank" href="http://twitter.com/#!/Fastw3b"><?php echo JText::_('FWG_TWITTER'); ?> &gt;&gt;</a> <?php echo JText :: _('FWG_AND'); ?> <a target="_blank" href="http://www.facebook.com/pages/edit/?id=165610930146118&amp;sk=basic#!/pages/Fastw3b/165610930146118?v=wall"><?php echo JText::_('FWG_FACEBOOK'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_WATCH_OUR_VIDEO_TUTORIAL_ON'); ?> <a target="_blank" href="http://www.youtube.com/user/fastw3b">Youtube &gt;&gt;</a></li>
				<li>Fastw3b <a target="_blank" href="http://fastw3b.net/joomla-extensions/best-offer.html"><?php echo JText::_('FWG_BEST_OFFERS'); ?> &gt;&gt; </a></li>
				<li><?php echo JText::_('FWG_RELATED_SERVICE'); ?>: <a target="_blank" href="http://fastw3b.net/joomla-services/service/4-extensions-customization.html"><?php echo JText::_('FWG_EXTENSIONS_CUSTOMIZATION'); ?> &gt;&gt; </a></li>
			</ul>

			<p><span style="text-decoration: underline;"><strong><?php echo JText::_('FWG_FW_GALLERY_ADD_ONS'); ?>:</strong></span></p>
			<ul>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/60-fw-gallery-light-template.html"><?php echo JText::_('FWG_LIGHT_TEMPLATE'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/61-fw-gallery-light-plus-template.html"><?php echo JText::_('FWG_LIGHT_PLUS_TEMPLATE'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/62-fw-gallery-video-template.html"><?php echo JText::_('FWG_VIDEO_TEMPLATE'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/9-fw-gallery-batch-upload.html"><?php echo JText::_('FWG_BATCH_UPLOAD_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/6-fw-gallery-comment.html"><?php echo JText::_('FWG_COMMENT_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/12-fw-gallery-content-plugin.html"><?php echo JText::_('FWG_CONTENT_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/26-fw-gallery-facebook-plugin.html"><?php echo JText::_('FWG_FACEBOOK_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/36-fw-gallery-front-end-manager.html"><?php echo JText::_('FWG_FRONT_END_MANAGER_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/14-fw-gallery-manual-resize.html"><?php echo JText::_('FWG_MANUAL_RESIZE_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/3-fw-gallery-import.html"><?php echo JText::_('FWG_IMPORT_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/27-fw-gallery-jomsocial-plugin.html"><?php echo JText::_('FWG_JOMSOCIAL_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/13-fw-gallery-latest-module.html"><?php echo JText::_('FWG_LATEST_MODULE'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/2-fw-gallery-fancy-slideshow.html"><?php echo JText::_('FWG_FANCY_SLIDESHOW_MODULE'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/28-fw-gallery-search-plugin.html"><?php echo JText::_('FWG_SEARCH_PLUGIN'); ?> &gt;&gt;</a></li>
				<li><?php echo JText::_('FWG_FW_GALLERY'); ?> <a target="_blank" href="http://fastw3b.net/joomla-extensions/product/51-fw-video-gallery-plugin.html"><?php echo JText::_('FWG_VIDEO_PLUGIN'); ?> &gt;&gt;</a></li>
			</ul>
		</fieldset>
	</div>
	<div style="clear:both;"></div>
	<input type="hidden" name="task" value="" />
</form>
