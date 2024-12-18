<?php
 /**
  * @version   $Id: Resize.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

class RokGallery_Manipulation_Action_Resize extends RokGallery_Manipulation_AbstractAction
{
    protected $type = 'resize';

    public $width;
    public $height;

    /**
     * Apply the manipulation with the setup options to the passed in image.
     * This does not do any memory manipulation
     *
     * @param WideImage_Image $image
     * @return WideImage_Image
     */
    public function &apply(WideImage_Image &$image)
    {
        if (!$this->isSetup())
            throw new RokGallery_Manipulation_Exception(rc__('ROKGALLERY_MANIPULATION_WAS_NOT_SETUP_PRIOR_TO_APPLYING'));
        $return_image = $image->resize($this->width, $this->height, 'fill');
        return $return_image;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}
