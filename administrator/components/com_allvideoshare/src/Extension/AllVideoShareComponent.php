<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Extension;

\defined( 'JPATH_PLATFORM' ) or die;

use \Joomla\CMS\Application\SiteApplication;
use \Joomla\CMS\Association\AssociationServiceInterface;
use \Joomla\CMS\Association\AssociationServiceTrait;
use \Joomla\CMS\Categories\CategoryServiceTrait;
use \Joomla\CMS\Component\Router\RouterServiceInterface;
use \Joomla\CMS\Component\Router\RouterServiceTrait;
use \Joomla\CMS\Extension\BootableExtensionInterface;
use \Joomla\CMS\Extension\MVCComponent;
use \Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use \Joomla\CMS\Tag\TagServiceTrait;
use \Psr\Container\ContainerInterface;

/**
 * Component class for AllVideoShare
 *
 * @since  4.1.0
 */
class AllVideoShareComponent extends MVCComponent implements RouterServiceInterface {
	
	use AssociationServiceTrait;
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;
	use CategoryServiceTrait, TagServiceTrait {
		CategoryServiceTrait::getTableNameForSection insteadof TagServiceTrait;
		CategoryServiceTrait::getStateColumnForSection insteadof TagServiceTrait;
	}

}