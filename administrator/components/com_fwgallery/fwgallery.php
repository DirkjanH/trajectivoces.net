<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );
error_reporting(E_ALL);
JHTML :: stylesheet('administrator/components/com_fwgallery/assets/css/styles.css');
JHTML::addIncludePath(JPATH_COMPONENT_SITE.'/helpers');
JHTML::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers');
JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');

require_once(JPATH_COMPONENT_SITE.'/helpers/helper.php');
require_once(JPATH_COMPONENT.'/controller.php');

$controller = JControllerLegacy :: getInstance('fwGallery');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();

?>