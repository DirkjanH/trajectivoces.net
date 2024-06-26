<?php
/**
  * @version   $Id: Item.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

class RokGallery_Filter_Item
{
    public $type;
    public $operator;
    public $query;

    public static function &createFromJson($json)
    {
        $query = null;
        if (isset($json->query)) $query = $json->query;
        $item = new RokGallery_Filter_Item($json->type, $json->operator, $query);
        return $item;
    }

    public function __construct($type = null, $operator = null, $query = null)
    {
        $this->type = $type;
        $this->operator = $operator;
        if (null != $query && !empty($query))$this->query = strtolower($query);
    }
}
