<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Controller;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\Router\Route;

/**
 * AllVideoShare master display controller.
 *
 * @since  4.1.0
 */
class DisplayController extends BaseController {

	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $default_view = 'videos';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  BaseController|boolean  This object to support chaining.
	 *
	 * @since   4.1.0
	 */
	public function display( $cachable = false, $urlparams = array() ) {
		return parent::display();
	}

	/**
	 * Method to clear API cache.
	 *
	 * @since  4.1.2
	 */
	public function clearcache() {
		$db = Factory::getDbo();

		$query = 'DELETE FROM #__allvideoshare_cache WHERE name LIKE ' . $db->quote( '%' . $db->escape( 'youtube_', true ) . '%' );
		$db->setQuery( $query );
		$db->execute();

		ob_start();
		echo 'success';
		echo ob_get_clean();
        exit;
	}
	
}
