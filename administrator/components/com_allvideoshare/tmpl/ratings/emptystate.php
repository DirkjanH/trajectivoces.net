<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_ALLVIDEOSHARE_RATINGS',
	'formURL' => 'index.php?option=com_allvideoshare&view=ratings',
	'icon' => 'icon-copy',
];

echo LayoutHelper::render( 'joomla.content.emptystate', $displayData );
