<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML :: _('behavior.framework', true);
JHTML :: _('behavior.formvalidation');
JHTML :: script('administrator/components/com_fwgallery/assets/js/moorainbow.1.2b2.js');

JToolBarHelper :: title(JText::_('FWG_CONFIG').': <small><small>[ '.JText::_('FWG_EDIT').' ]</small></small>', 'fwgallery-config.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();
$color = $this->obj->params->get('gallery_color');
if (preg_match('/[0-9a-fA-F]{3,6}/', $color)) $color = '#'.$color;
else $color = '';
?>
<form action="index.php?option=com_fwgallery&amp;view=config" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
	<div id="settings1" style="width:50%;float:left;">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_IMAGE_SETTINGS'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_WIDTH_FOR_THE_THUMBNAILS__THAT_WILL_SUIT_YOUR_NEEDS_MOST'); ?>" class="key">
                    <?php echo JText::_('FWG_THUMB_WIDTH'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_WIDTH_FOR_THE_THUMBNAILS__THAT_WILL_SUIT_YOUR_NEEDS_MOST'); ?>">
					<input class="inputbox required validate-numeric" name="config[im_th_w]" value="<?php echo $this->obj->params->get('im_th_w', 160); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_HEIGHT_FOR_THE_THUMBNAILS__THAT_WILL_SUIT_YOUR_NEEDS_MOST'); ?>" class="key">
                    <?php echo JText::_('FWG_THUMB_HEIGHT'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_HEIGHT_FOR_THE_THUMBNAILS__THAT_WILL_SUIT_YOUR_NEEDS_MOST'); ?>">
					<input class="inputbox required validate-numeric" name="config[im_th_h]" value="<?php echo $this->obj->params->get('im_th_h', 120); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_THE_WIDTH_FOR_THE_MEDIUM_SIZE_IMAGE_DURING_FULL_SCREEN_VIEW'); ?>" class="key">
                    <?php echo JText::_('FWG_MEDIUM_SIZE_WIDTH'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText::_('FWG_THE_WIDTH_FOR_THE_MEDIUM_SIZE_IMAGE_DURING_FULL_SCREEN_VIEW'); ?>">
					<input class="inputbox required validate-numeric" name="config[im_mid_w]" value="<?php echo $this->obj->params->get('im_mid_w', 340); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_THE_HEIGHT_FOR_THE_MEDIUM_SIZE_IMAGE_DURING_FULL_SCREEN_VIEW'); ?>" class="key">
                    <?php echo JText::_('FWG_MEDIUM_SIZE_HEIGHT'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText::_('FWG_THE_HEIGHT_FOR_THE_MEDIUM_SIZE_IMAGE_DURING_FULL_SCREEN_VIEW'); ?>">
					<input class="inputbox required validate-numeric" name="config[im_mid_h]" value="<?php echo $this->obj->params->get('im_mid_h', 255); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_THE_PARAMETERS_OF_THE_IMAGE_FOR_LIGHTBOX_EFFECT_VIEW'); ?>" class="key">
                    <?php echo JText::_('FWG_BIG_SIZE_WIDTH'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText::_('FWG_THE_PARAMETERS_OF_THE_IMAGE_FOR_LIGHTBOX_EFFECT_VIEW'); ?>">
					<input class="inputbox required validate-numeric" name="config[im_max_w]" value="<?php echo $this->obj->params->get('im_max_w', 800); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_THE_PARAMETERS_OF_THE_IMAGE_FOR_LIGHTBOX_EFFECT_VIEW'); ?>" class="key">
                    <?php echo JText::_('FWG_BIG_SIZE_HEIGHT'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText::_('FWG_THE_PARAMETERS_OF_THE_IMAGE_FOR_LIGHTBOX_EFFECT_VIEW'); ?>">
					<input class="inputbox required validate-numeric" name="config[im_max_h]" value="<?php echo $this->obj->params->get('im_max_h', 600); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="" class="key">
                    <?php echo JText::_('FWG_THUMB_AND_MEDIUM_IMAGES_SHRINKING_METHOD'); ?>:
	            </td>
	            <td title="">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'im_just_shrink', '', $this->obj->params->get('im_just_shrink'), JText :: _('FWG_JUST_SHRINK'), JText :: _('FWG_SHRINK_AND_CUT')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_THE_FRAME_FOR_THE_GALLERY_DEFAULT_IMAGE__IT_SHOULD_BE_AT_LEAST_20PX_BIGGER_THAN_THE_IMAGES___SIZE'); ?>" class="key">
                    <?php echo JText::_('FWG_GALLERY_BOX_WIDTH'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_THE_FRAME_FOR_THE_GALLERY_DEFAULT_IMAGE__IT_SHOULD_BE_AT_LEAST_20PX_BIGGER_THAN_THE_IMAGES___SIZE'); ?>">
					<input class="inputbox validate-numeric" name="config[gallery_box_width]" value="<?php echo $this->obj->params->get('gallery_box_width'); ?>" size="5"/> <?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_THE_FRAME_FOR_THE_GALLERY_IMAGES__IT_SHOULD_BE_AT_LEAST_20_PX_BIGGER_THAN_THE_IMAGES___SIZE'); ?>" class="key">
                    <?php echo JText::_('FWG_IMAGE_BOX_WIDTH'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_THE_FRAME_FOR_THE_GALLERY_IMAGES__IT_SHOULD_BE_AT_LEAST_20_PX_BIGGER_THAN_THE_IMAGES___SIZE'); ?>">
					<input class="inputbox validate-numeric" name="config[image_box_width]" value="<?php echo $this->obj->params->get('image_box_width'); ?>" size="5"/> <?php echo JText :: _('FWG_PX'); ?>
	            </td>
	        </tr>
	    </table>
    </fieldset>

    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_LAYOUT_SETTINGS'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td title="<?php echo JText :: _('FWG_SELECT_ANY_COLOR_FROM_THE_COLOR_PALETTE_OR_JUST_TYPE_THE_COLOR__S_CODE__IF_YOU_KNOW_IT'); ?>" class="key">
                    <?php echo JText::_('FWG_GALLERIES_DEFAULT_COLOR'); ?>:
	            </td>
	            <td title="<?php echo JText :: _('FWG_SELECT_ANY_COLOR_FROM_THE_COLOR_PALETTE_OR_JUST_TYPE_THE_COLOR__S_CODE__IF_YOU_KNOW_IT'); ?>" id="color-row"<?php if ($color) { ?> style="background-color:<?php echo $color; ?>"<?php } ?>>
					<img id="myRainbow" src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/rainbow.png" alt="[r]" width="16" height="16" />
					<input id="color" name="config[gallery_color]" type="text" size="13" value="<?php echo $color; ?>" />
					<button type="button" id="myRainbowButton"><?php echo JText :: _('FWG_SELECT'); ?></button>
					<button type="button" id="myRainbowClearButton"><?php echo JText :: _('FWG_CLEAR'); ?></button>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText :: _('FWG_THE_NUMBER_DEPENDS_ON_THE_THUMB_SIZES_AND_THE_TEMPLATE_OF_YOUR_SITE__OBLIGATORY_FIELD'); ?>" class="key">
                    <?php echo JText::_('FWG_GALLERIES_IN_A_ROW'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText :: _('FWG_THE_NUMBER_DEPENDS_ON_THE_THUMB_SIZES_AND_THE_TEMPLATE_OF_YOUR_SITE__OBLIGATORY_FIELD'); ?>">
					<input class="inputbox required validate-numeric" name="config[galleries_a_row]" value="<?php echo $this->obj->params->get('galleries_a_row', 3); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText :: _('FWG_THE_NUMBER_DEPENDS_ON_THE_THUMB_SIZES_AND_THE_TEMPLATE_OF_YOUR_SITE__OBLIGATORY_FIELD'); ?>" class="key">
                    <?php echo JText::_('FWG_IMAGES_IN_A_ROW'); ?> <span class="required">*</span>:
	            </td>
	            <td title="<?php echo JText :: _('FWG_THE_NUMBER_DEPENDS_ON_THE_THUMB_SIZES_AND_THE_TEMPLATE_OF_YOUR_SITE__OBLIGATORY_FIELD'); ?>">
					<input class="inputbox required validate-numeric" name="config[images_a_row]" value="<?php echo $this->obj->params->get('images_a_row', 3); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_NUMBER_OF_GALLERIES__YOU_WANT_TO_BE_DISPLAYED_PER_PAGE'); ?>" class="key">
                    <?php echo JText::_('FWG_GALLERIES_PER_PAGE'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_NUMBER_OF_GALLERIES__YOU_WANT_TO_BE_DISPLAYED_PER_PAGE'); ?>">
					<input class="inputbox required validate-numeric" name="config[galleries_limit]" value="<?php echo $this->obj->params->get('galleries_limit', 20); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_NUMBER_OF_IMAGES_YOU_WANT_TO_BE_DISPLAYED_PER_PAGE'); ?>" class="key">
                    <?php echo JText::_('FWG_IMAGES_PER_PAGE'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_TYPE_THE_NUMBER_OF_IMAGES_YOU_WANT_TO_BE_DISPLAYED_PER_PAGE'); ?>">
					<input class="inputbox required validate-numeric" name="config[images_limit]" value="<?php echo $this->obj->params->get('images_limit', 20); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SELECT_ONE_OF_THE_FOUR_GALLERIES___ORDER_TYPES__THIS_OPTION_IS_AVAILABLE_ON_FRONT_END_TOO'); ?>" class="key">
                    <?php echo JText::_('FWG_DEFAULT_GALLERIES_ORDERING'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SELECT_ONE_OF_THE_FOUR_GALLERIES___ORDER_TYPES__THIS_OPTION_IS_AVAILABLE_ON_FRONT_END_TOO'); ?>">
					<?php echo JHTML :: _('select.genericlist', array(
						JHTML :: _('select.option', 'name', JText :: _('FWG_ALPHABETICALLY'), 'id', 'name'),
						JHTML :: _('select.option', 'new', JText :: _('FWG_NEWEST_FIRST'), 'id', 'name'),
						JHTML :: _('select.option', 'old', JText :: _('FWG_OLDEST_FIRST'), 'id', 'name'),
						JHTML :: _('select.option', 'order', JText :: _('FWG_ORDERING'), 'id', 'name')
					), 'config[ordering_galleries]', '', 'id', 'name', $this->obj->params->get('ordering_galleries', 'order')); ?>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SELECT_ONE_OF_THE_FOUR_POSSIBLE_TYPES_OF_ORDER_FOR_IMAGES__THE_OPTION_IS_AVAILABLE_ON_FRONT_END_TOO'); ?>" class="key">
                    <?php echo JText::_('FWG_DEFAULT_IMAGES_ORDERING'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SELECT_ONE_OF_THE_FOUR_POSSIBLE_TYPES_OF_ORDER_FOR_IMAGES__THE_OPTION_IS_AVAILABLE_ON_FRONT_END_TOO'); ?>">
					<?php echo JHTML :: _('select.genericlist', array(
						JHTML :: _('select.option', 'name', JText :: _('FWG_ALPHABETICALLY'), 'id', 'name'),
						JHTML :: _('select.option', 'new', JText :: _('FWG_NEWEST_FIRST'), 'id', 'name'),
						JHTML :: _('select.option', 'old', JText :: _('FWG_OLDEST_FIRST'), 'id', 'name'),
						JHTML :: _('select.option', 'order', JText :: _('FWG_ORDERING'), 'id', 'name'),
						JHTML :: _('select.option', 'voting', JText :: _('FWG_VOTING'), 'id', 'name')
					), 'config[ordering_images]', '', 'id', 'name', $this->obj->params->get('ordering_images', 'order')); ?>
	            </td>
	        </tr>
	    </table>
    </fieldset>

    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_WATERMARK'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__IF_YOU_DON__T_WANT_TO_USE_IT__THEN_SELECT__QUOT_NO_QUOT__HERE'); ?>" class="key">
                    <?php echo JText::_('FWG_USE_WATERMARK'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__IF_YOU_DON__T_WANT_TO_USE_IT__THEN_SELECT__QUOT_NO_QUOT__HERE'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'use_watermark', '', $this->obj->params->get('use_watermark')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SELECT_ONE_OF_THE_5_POSSIBLE_LOCATIONS_OF_THE_WATERMARK_SIGN_ON_IMAGES'); ?>" class="key">
                    <?php echo JText::_('FWG_WATERMARK_POSITION'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SELECT_ONE_OF_THE_5_POSSIBLE_LOCATIONS_OF_THE_WATERMARK_SIGN_ON_IMAGES'); ?>">
					<?php echo JHTML :: _('select.genericlist', array(
						JHTML :: _('select.option', 'center', JText :: _('FWG_CENTER'), 'id', 'name'),
						JHTML :: _('select.option', 'left top', JText :: _('FWG_LEFT_TOP'), 'id', 'name'),
						JHTML :: _('select.option', 'right top', JText :: _('FWG_RIGHT_TOP'), 'id', 'name'),
						JHTML :: _('select.option', 'left bottom', JText :: _('FWG_LEFT_BOTTOM'), 'id', 'name'),
						JHTML :: _('select.option', 'right bottom', JText :: _('FWG_RIGHT_BOTTOM'), 'id', 'name')
					), 'config[watermark_position]', '', 'id', 'name', $this->obj->params->get('watermark_position', 'left bottom')); ?>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_UPLOAD_A_FILE__THAT_WILL_BE_USED_AS_A_WATERMARK_ON_IMAGES'); ?>" class="key">
                    <?php echo JText::_('FWG_WATERMARK_FILE'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_UPLOAD_A_FILE__THAT_WILL_BE_USED_AS_A_WATERMARK_ON_IMAGES'); ?>">
<?php
if ($this->obj->params->get('watermark_file')) {
	if ($path = JFHelper :: getWatermarkFilename()) {
?>
					<img src="<?php echo JURI :: root(true); ?>/<?php echo $path; ?>" /><br/>
					<input type="checkbox" name="delete_watermark" value="1" /> <?php echo JText :: _('FWG_REMOVE_WATERMARK'); ?><br/>
<?php
	} else {
?>
					<p style="color:#f00;"><?php echo JText :: _('FWG_WATERMARK_FILE_NOT_FOUND_'); ?></p>
<?php
	}
}
?>
					<input id="watermark_file" type="file" class="inputbox" name="watermark_file" />
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_TYPE_A_TEXT__THAT_WILL_BE_USED_AS_A_WATERMARK__IF_YOU_UPLOAD_A_FILE__THEN_TEXT_WON__T_BE_DISPLAYED'); ?>" class="key">
                    <?php echo JText::_('FWG_WATERMARK_TEXT'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_TYPE_A_TEXT__THAT_WILL_BE_USED_AS_A_WATERMARK__IF_YOU_UPLOAD_A_FILE__THEN_TEXT_WON__T_BE_DISPLAYED'); ?>">
					<input class="inputbox" name="config[watermark_text]" value="<?php echo $this->obj->params->get('watermark_text'); ?>" size="35"/>
	            </td>
	        </tr>
		</table>
	</fieldset>

    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_VOTING_SYSTEM'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT__IF_YOU_DON__T_WANT_TO_USE_VOTING_AT_ALL'); ?>" class="key">
                    <?php echo JText::_('FWG_USE_VOTING'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT__IF_YOU_DON__T_WANT_TO_USE_VOTING_AT_ALL'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'use_voting', '', $this->obj->params->get('use_voting')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_LET_THE_VISITORS_PUBLIC_POLLS'); ?>" class="key">
                    <?php echo JText::_('FWG_PUBLIC_VOTING'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_LET_THE_VISITORS_PUBLIC_POLLS'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'public_voting', '', $this->obj->params->get('public_voting')); ?>
					</fieldset>
	            </td>
	        </tr>
		</table>
	</fieldset>
	</div>

	<div id="settings2" style="width:50%;float:left;">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_DISPLAYING_SETTINGS'); ?></legend>
        <table class="admintable">
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_TOTAL_GALLERIES_COUNTER'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_total_galleries', '', $this->obj->params->get('display_total_galleries')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>" class="key">
                    <?php echo JText::_('FWG_SHOW__DISPLAY_LIMIT__OPTION_FOR_LIST_OF_GALLERIES'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_galleries_limit_selector', '', $this->obj->params->get('display_galleries_limit_selector')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>" class="key">
                    <?php echo JText::_('FWG_SHOW__DISPLAY_LIMIT__OPTION_FOR_SINGLE_GALLERY'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_gallery_limit_selector', '', $this->obj->params->get('display_gallery_limit_selector')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_GALLERY_OWNER_NAME'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_owner_gallery', '', $this->obj->params->get('display_owner_gallery')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_OWNER_NAME'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_owner_image', '', $this->obj->params->get('display_owner_image')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_CREATION_DATE_OF_THE_GALLERY__BY_SELECTING_YES_NO'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_GALLERY_DATE'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_CREATION_DATE_OF_THE_GALLERY__BY_SELECTING_YES_NO'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_date_gallery', '', $this->obj->params->get('display_date_gallery')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT_IN_THE_FRONT_END'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_ORDER_BY_OPTION'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_YES_NO_OPTION__SELECT__QUOT_NO_QUOT___IF_YOU_WANT_TO_HIDE_IT_IN_THE_FRONT_END'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_gallery_sorting', '', $this->obj->params->get('display_gallery_sorting')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_NAME_OF_THE_GALLERY__CLICKING_YES_NO'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_GALLERY_NAME'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_NAME_OF_THE_GALLERY__CLICKING_YES_NO'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_name_gallery', '', $this->obj->params->get('display_name_gallery')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_NAME_OF_THE_IMAGES___ADDING_TO_THE_GALLERY__CLICKING_YES_NO'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_NAME'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_NAME_OF_THE_IMAGES___ADDING_TO_THE_GALLERY__CLICKING_YES_NO'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_name_image', '', $this->obj->params->get('display_name_image')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_DATE_OF_THE_IMAGES___ADDING_TO_THE_GALLERY__CLICKING_YES_NO'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_DATE'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_DATE_OF_THE_IMAGES___ADDING_TO_THE_GALLERY__CLICKING_YES_NO'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_date_image', '', $this->obj->params->get('display_date_image')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_NUMBER_OF_IMAGE_VIEWS__SELECTING_YES_NO_OPTIONS'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_VIEWS'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_THE_NUMBER_OF_IMAGE_VIEWS__SELECTING_YES_NO_OPTIONS'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_image_views', '', $this->obj->params->get('display_image_views')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_ALLOW_IMAGE_DOWNLOAD'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'allow_image_download', '', $this->obj->params->get('allow_image_download')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_IMAGE_TAGS'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_image_tags', '', $this->obj->params->get('display_image_tags')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_ALLOW_PRINT_BUTTON'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'allow_print_button', '', $this->obj->params->get('allow_print_button')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_BOTTOM_IMAGES'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'hide_bottom_image', '', $this->obj->params->get('hide_bottom_image')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_USERS_COPYRIGHT_SELECTING_YES_NO'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_USERS_COPYRIGHT'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_SHOW_OR_HIDE_USERS_COPYRIGHT_SELECTING_YES_NO'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_user_copyright', '', $this->obj->params->get('display_user_copyright')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_SET_A_SUITABLE_DATE_FORMAT_FOR_THE_FRONT_END__MONTH__DAY_AND_YEAR'); ?>" class="key">
                    <?php echo JText::_('FWG_DATE_FORMAT'); ?>
	            </td>
	            <td title="<?php echo JText::_('FWG_SET_A_SUITABLE_DATE_FORMAT_FOR_THE_FRONT_END__MONTH__DAY_AND_YEAR'); ?>">
					<input lass="inputbox" name="config[date_format]" value="<?php echo $this->obj->params->get('date_format', '%B %d, %Y'); ?>" size="15"/>
					&nbsp;<a href="http://docs.joomla.org/How_do_you_change_the_date_format%3F" target="_blank"><?php echo JText::_('FWG_DATE_OPTIONS'); ?></a>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_THE_NUMBER_OF_DAYS__WHEN_ADDED_IMAGE_WILL_HAVE_THE_ICON__QUOT_NEW_QUOT__IN_THE_LEFT_TOP'); ?>" class="key">
                    <?php echo JText::_('FWG_DISPLAY_ICON_NEW_DAYS'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_THE_NUMBER_OF_DAYS__WHEN_ADDED_IMAGE_WILL_HAVE_THE_ICON__QUOT_NEW_QUOT__IN_THE_LEFT_TOP'); ?>">
					<input class="inputbox" name="config[new_days]" value="<?php echo $this->obj->params->get('new_days', 7); ?>" size="5"/>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText :: _('FWG_DISPLAY_LIGHTBOX_GALLERIES'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_galleries_lightbox', '', $this->obj->params->get('display_galleries_lightbox')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_MIGNIFIER__LIGHTBOX_EFFECT_'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'hide_mignifier', '', $this->obj->params->get('hide_mignifier')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_SINGLE_IMAGE_VIEW'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'hide_single_image_view', '', $this->obj->params->get('hide_single_image_view')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_HIDE_IPHONE_APP_PROMO'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'hide_iphone_app_promo', '', $this->obj->params->get('hide_iphone_app_promo')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
                    <?php echo JText::_('FWG_DISPLAY_SOCIAL_SHARING'); ?>:
	            </td>
	            <td>
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'display_social_sharing', '', $this->obj->params->get('display_social_sharing')); ?>
					</fieldset>
	            </td>
	        </tr>
	        <tr>
	            <td title="<?php echo JText::_('FWG_IF_YOU_DON__T_WANT_TO_DISPLAY_FASTW3B_COPYRIGHT_ON_THE_FRONT_END_OF_YOUR_WEBSITE__THEN_SELECT__QUOT_NO_QUOT__HERE'); ?>" class="key">
                    <?php echo JText::_('FWG_HIDE_FASTW3B_COPYRIGHT'); ?>:
	            </td>
	            <td title="<?php echo JText::_('FWG_IF_YOU_DON__T_WANT_TO_DISPLAY_FASTW3B_COPYRIGHT_ON_THE_FRONT_END_OF_YOUR_WEBSITE__THEN_SELECT__QUOT_NO_QUOT__HERE'); ?>">
					<fieldset class="radio btn-group">
						<?php echo JHTML :: _('select.booleanlist', 'hide_fw_copyright', '', $this->obj->params->get('hide_fw_copyright')); ?>
					</fieldset>
					<div id="fw-copyright-donation" style="display:none">
						<?php echo JText :: _('FWG_FWGALLERY_COPYRIGHT_HIDE'); ?>
					</div>
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
	<div style="clear:both;"></div>
	<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">
var copyrightEff;
var fwgRainbow;

window.addEvent('domready', function() {

	$('myRainbowButton').addEvent('click', function(ev) {
		$('myRainbow').fireEvent('click', ev);
	});
	$('myRainbowClearButton').addEvent('click', function(ev) {
		$('color').value = '';
		$('color-row').setStyle('background-color', '');
	});
	$('color').addEvent('keyup', function() {
		if (this.value.match(/^#[0-9a-fA-F]{3,6}$/)) {
			$('color-row').setStyle('background-color', this.value);
			fwgRainbow.manualSet(this.value, 'hex');
		} else {
			$('color-row').setStyle('background-color', '');
		}
	});
	fwgRainbow = new MooRainbow('myRainbow', {
		wheel: true,
		imgPath: '<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/',
		onChange: function(color) {
			$('color').value = color.hex;
			$('color-row').setStyle('background-color', color.hex);
		},
		onComplete: function(color) {
			$('color').value = color.hex;
			$('color-row').setStyle('background-color', color.hex);
		}
	});
<?php
if ($color) {
?>
	fwgRainbow.manualSet('<?php echo $color; ?>', 'hex');
<?php
}
?>
	$('fw-copyright-donation').setStyle('display', '');
	$$('#adminForm input').each(function (el) {
		if (el.name == 'config[hide_fw_copyright]') {
			el.addEvent('click', check_copyright);
			el.addEvent('change', check_copyright);
		}
	});
	copyrightEff = new Fx.Slide('fw-copyright-donation');
	copyrightEff.hide();
	check_copyright();
});
function check_copyright() {
	$$('#adminForm input').each(function (el) {
		if (el.name == 'config[hide_fw_copyright]' && el.checked) {
			if (el.value == '0') copyrightEff.slideOut();
			else copyrightEff.slideIn()
		}
	});
}
function submitbutton(task) {
	if (task == 'save') {
		if (!document.formvalidator.isValid($('adminForm'))) {
			alert('<?php echo JText :: _('FWG_NOT_ALL_REQUIRED_FIELDS_PROPERLY_FILLED_IN'); ?>');
			return;
		}
		if ($('watermark_file') && $('watermark_file').value && !$('watermark_file').value.match(/\.png$/i)) {
			alert('<?php echo JText :: _('FWG_ALLOWED_PNG_AND_GIF_FILES_AS_WATERMARK_IMAGES_ONLY'); ?>');
			return;
		}
	}
	$('adminForm').task.value = task;
	$('adminForm').submit();
}
</script>