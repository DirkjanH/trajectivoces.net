<?php
/**
 * @version   $Id: rokgallery.php 10868 2013-05-30 04:05:27Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$include_file = realpath(dirname(__FILE__) . '/include.php');

$included_files = get_included_files();
if (!in_array($include_file, $included_files) && ($libret = require_once($include_file)) !== 'JOOMLA_ROKGALLERY_LIB_INCLUDED') {
    JError::raiseWarning(100, 'RokGallery: ' . implode('<br /> - ', $loaderrors));
    return;
}

RokGallery_Doctrine::addModelPath(JPATH_SITE . '/components/com_rokgallery/lib');
RokGallery_Doctrine::useMemDBCache('RokGallery');
RokCommon_Composite::addPackagePath('rokgallery', JPATH_COMPONENT . '/templates');

// Require the base controller
require_once JPATH_COMPONENT . DS . 'controller.php';

// Initialize the controller
$controller = new RokGalleryController();
$controller->execute(JFactory::getApplication()->input->getCmd('task'));

$controller->redirect();
