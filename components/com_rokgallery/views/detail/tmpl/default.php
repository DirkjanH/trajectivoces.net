<?php
 /**
  * @version   $Id: default.php 10868 2013-05-30 04:05:27Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */

defined('_JEXEC') or die('Restricted access');
RokCommon_Composite::get($this->context)->loadAll('includes.php', array('that'=>$this));
echo RokCommon_Composite::get($this->context)->load('default.php', array('that'=>$this));