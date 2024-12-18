<?php
/**
 * @version   $Id: Config.php 10871 2013-05-30 04:06:26Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokGallery_Config
{

    const OPTION_ROOT_PATH             = 'root_path';
    const OPTION_THUMBNAIL_BASE_URL    = 'thumbnail_base_url';
    const OPTION_BASE_URL              = 'base_url';
    const OPTION_MISSING_IMAGE_PATH    = 'missing_image_path';
    const OPTION_JOB_QUEUE_PATH        = 'job_queue_path';
    const OPTION_ALLOW_DUPLICATE_FILES = 'allow_duplicate_files';

    const OPTION_GALLERY_REMOVE_SLICES             = 'gallery_remove_slice';
    const OPTION_GALLERY_AUTOPUBLISH               = 'gallery_autopublish';
    const OPTION_SLICE_AUTOPUBLISH_ON_FILE_PUBLISH = 'publish_slices_on_file_publish';

    const OPTION_ADMIN_ITEMS_PER_ROW        = 'admin_items_per_row';
    const OPTION_ADMIN_ITEMS_PER_PAGE       = 'admin_items_per_page';
    const OPTION_AUTO_CLEAR_SUCCESSFUL_JOBS = 'autoclear_successful_jobs';
    const OPTION_JPEG_QUALITY               = 'jpeg_quality';
    const OPTION_PNG_COMPRESSION            = 'png_compression';
    const OPTION_LOVE_TEXT                  = 'love_text';
    const OPTION_UNLOVE_TEXT                = 'unlove_text';
    const OPTION_ADMIN_THUMB_BACKGROUND     = 'admin_thumb_background';
    const OPTION_DEFAULT_THUMB_XSIZE        = 'default_thumb_xsize';
    const OPTION_DEFAULT_THUMB_YSIZE        = 'default_thumb_ysize';
    const OPTION_DEFAULT_THUMB_KEEP_ASPECT  = 'default_thumb_keep_aspect';
    const OPTION_DEFAULT_THUMB_BACKGROUND   = 'default_thumb_background';


    const DEFAULT_GALLERY_AUTOPUBLISH       = true;
    const DEFAULT_DEFAULT_THUMB_XSIZE       = 150;
    const DEFAULT_DEFAULT_THUMB_YSIZE       = 150;
    const DEFAULT_DEFAULT_THUMB_BACKGROUND  = null;
    const DEFAULT_DEFAULT_THUMB_KEEP_ASPECT = true;
    const DEFAULT_ADMIN_THUMB_XSIZE         = 300;
    const DEFAULT_ADMIN_THUMB_YSIZE         = 180;
    const DEFAULT_MINI_ADMIN_THUMB_XSIZE    = 50;
    const DEFAULT_MINI_ADMIN_THUMB_YSIZE    = 50;
    const MISSING_ADMIN_SLICE_ID            = -1;
    const MISSING_FRONT_SLICE_ID            = -2;

    protected $platform_instance;

    public function __construct($platform_instance)
    {
        $this->platform_instance = $platform_instance;
    }

    protected function _getOption($name, $default = null, $context = null)
    {
        return $this->platform_instance->getOption($name, $default, $context);
    }

    public static function getOption($name, $default = null, $context = null)
    {
        $container = RokCommon_Service::getContainer();
        /** @var $instance RokGallery_Config */
        $instance  = $container->rokgallery_config;
        return $instance->_getOption($name, $default, $context);
    }
}

