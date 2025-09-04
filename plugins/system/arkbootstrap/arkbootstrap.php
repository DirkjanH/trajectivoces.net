<?php
/**
 * @version		$Id: cache.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * ArkBootstrap Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.arkbootstrap
 */
 
class plgSystemArKBootstrap extends JPlugin
{

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 * @since	1.0
	 */

    function onAfterRoute()  
	{
		//Do Nothing
	}
}