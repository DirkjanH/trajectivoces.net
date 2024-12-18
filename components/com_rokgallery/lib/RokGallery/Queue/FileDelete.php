<?php
 /**
  * @version   $Id: FileDelete.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

class RokGallery_Queue_FileDelete
{
    protected static $_instance;

    protected $_queue = array();

    /**
     * @return RokGallery_FileDeleteQueue
     */
    public static function &getInstance()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new RokGallery_Queue_FileDelete();
        }
        return self::$_instance;
    }

    /**
     *
     */
    protected function __construct()
    {

    }

    /**
     * @param $path the path of the file to queue for deletion
     */
    public static function add($path)
    {
        $instance = self::getInstance();
        $realpath = realpath($path);
        if (!in_array($realpath, $instance->_queue))
        {
            $instance->_queue[] = $realpath;
        }
    }

    /**
     * clear the delete queue
     * @static
     */
    public static function clear()
    {
        $instance = self::getInstance();
        $instance->_queue = array();
    }

    /**
     * gets the contents of the delete queue
     * @static
     * @return array
     */
    public static function get()
    {
        $instance = self::getInstance();
        return $instance->_queue;
    }

    public static function process()
    {
        // process the file delete queue
        $instance = self::getInstance();
        foreach($instance->get() as $delete_file)
        {
            if (file_exists($delete_file))
            {
                @unlink($delete_file);
            }
        }
        $instance->clear();
    }
}
