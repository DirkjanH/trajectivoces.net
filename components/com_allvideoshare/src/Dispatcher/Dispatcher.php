<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Dispatcher;

\defined( 'JPATH_PLATFORM' ) or die;

use \Joomla\CMS\Dispatcher\ComponentDispatcher;
use \Joomla\CMS\Language\Text;

/**
 * ComponentDispatcher class for Com_AllVideoShare
 *
 * @since  4.1.0
 */
class Dispatcher extends ComponentDispatcher {

	/**
	 * Dispatch a controller task. Redirecting the user if appropriate.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	public function dispatch() {
		parent::dispatch();
	}
	
}
