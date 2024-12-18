<?php
 /**
  * @version   $Id: File.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

class RokGallery_File
{
    /**
     * @param  $id
     * @return RokGallery_Model_File
     */
    public function &getSingle($id)
    {
        $query = Doctrine_Query::create()
                ->from('RokGallery_Model_File f')
                ->where('f.id = ?', $id);

        /** @var RokGallery_Model_File $file  */
        $file = $query->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
        return $file;
    }



}
