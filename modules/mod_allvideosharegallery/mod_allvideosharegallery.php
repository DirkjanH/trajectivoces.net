<?php
/**
 * @version     4.2.0
 * @package     Com_AllVideoShare
 * @subpackage  Mod_AllVideoShareGallery
 * @author      Vinoth Kumar <admin@mrvinoth.com>
 * @copyright   Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Helper\ModuleHelper;

require ModuleHelper::getLayoutPath( 'mod_allvideosharegallery', $params->get( 'type', 'categories' ) );