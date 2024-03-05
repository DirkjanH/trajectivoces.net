<?php
/*------------------------------------------------------------------------
# mod_socialfacebookwidget - Social Facebook Widget
# ------------------------------------------------------------------------
# @author - Social Facebook Widgets
# copyright - All rights reserved by Social FB Widgets
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://socialfbwidgets.com/
# Technical Support:  alex@socialfbwidgets.com
-------------------------------------------------------------------------*/
// no direct access


defined('_JEXEC') or die;


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_socialfacebookwidget', $params->get('layout', 'default'));