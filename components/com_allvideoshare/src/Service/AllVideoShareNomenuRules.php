<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Service;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Component\Router\RouterView;
use \Joomla\CMS\Component\Router\Rules\RulesInterface;

/**
 * Class AllVideoShareNomenuRules.
 *
 * @since  4.1.0
 */
class AllVideoShareNomenuRules implements RulesInterface {

	/**
	 * Router this rule belongs to
	 *
	 * @var    RouterView
	 * @since  4.1.0
	 */
	protected $router;

	/**
	 * Class constructor.
	 *
	 * @param  RouterView  $router  Router this rule belongs to
	 *
	 * @since  4.1.0
	 */
	public function __construct( RouterView $router ) {
		$this->router = $router;
	}

	/**
	 * Dummy method to fullfill the interface requirements
	 *
	 * @param   array  &$query  The query array to process
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	public function preprocess( &$query ) {
		// Do nothing
	}

	/**
	 * Parse an URL
	 *
	 * @param   array  &$segments  The URL segments to parse
	 * @param   array  &$vars      The vars that result from the segments
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	public function parse( &$segments, &$vars ) {
		if ( $count = count( $segments ) ) {
			$vars['view'] = $segments[0];
			
			if ( $count == 2 ) {
				$vars['slg']  = $segments[1];
			}

			$segments = array();
		}

		return;
	}

	/**
	 * Build an URL
	 *
	 * @param   array  &$query     The vars that should be converted
	 * @param   array  &$segments  The URL segments to create
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	public function build( &$query, &$segments ) {				
		$itemid = isset( $query['Itemid'] ) ? $query['Itemid'] : '';
		$view   = '';
		$slug   = '';

		if ( isset( $query['view'] ) ) {
			$view = $query['view'];

			$segments[] = $query['view'];
			unset( $query['view'] );
		}
		
		if ( isset( $query['slg'] ) ) {
			if ( ! empty( $query['slg'] ) ) {
				$slug = $query['slg'];

				if ( ! empty( $view ) && ! empty( $slug ) ) {
					if ( ! empty( $itemid ) ) {
						$db = Factory::getDbo();
				
						$sql = 'SELECT id FROM #__menu WHERE link=' . $db->quote( "index.php?option=com_allvideoshare&view={$view}&slg={$slug}" ) . ' AND published=1 LIMIT 1';
						$db->setQuery( $sql );
						$id = $db->loadResult();

						if ( $id == $itemid ) {
							// Do nothing
							array_pop( $segments );
						} else {
							$segments[] = $query['slg'];
						}
					} else {
						$segments[] = $query['slg'];
					}
				}
			}
			
			unset( $query['slg'] );
		}
	}

}
