<?php
/**
 * @version     4.2.0
 * @package     Com_AllVideoShare
 * @author      Vinoth Kumar <admin@mrvinoth.com>
 * @copyright   Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Helper;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

/**
 * Class AllVideoShareRoute.
 *
 * @since  4.1.0
 */
abstract class AllVideoShareRoute {

	/**
	 * Get the URL route for categories
	 *	 
	 * @param   int     $cat_slug  The category slug.
	 *
	 * @return  string  $route     The link to the categories
	 *
	 * @since   4.2.0
	 */
	public static function getCategoriesRoute( $cat_slug = '' ) {
		$route = '';

		if ( ! empty( $cat_slug ) ) {
			if ( $item = AllVideoShareQuery::getCategory( $cat_slug, 'alias' ) ) {
				$route = self::getCategoryRoute( $item );		
			}
		}

		if ( empty( $route ) ) {
			$route = self::__findRoute( 'category' );
		}

		return $route;
	}

	/**
	 * Get the URL route for a category
	 *
	 * @param   object  $item    The category object.
	 * @param   int     $itemid  The id of the menu item.
	 *
	 * @return  string  $route   The link to the category
	 *
	 * @since   4.2.0
	 */
	public static function getCategoryRoute( $item, $itemid = 0 ) {
		if ( empty( $itemid ) ) {
			$params = ComponentHelper::getParams( 'com_allvideoshare' );
			$itemid = (int) $params->get( 'itemid_category' );
		}

		if ( $itemid == -1 ) {
			$route = self::__findRoute( 'category', $item );
		} else {
			$route = "index.php?option=com_allvideoshare&view=category&slg={$item->slug}";

			if ( $itemid > 1 ) {
				$route .= "&Itemid={$itemid}";
			}
		}

		return $route;
	}

	/**
	 * Get the URL route for videos
	 *
	 * @param   int     $catid   The category ID
	 * @param   int     $itemid  The id of the menu item.
	 *
	 * @return  string  $route   The link to the videos
	 *
	 * @since   4.2.0
	 */
	public static function getVideosRoute( $cat_slug = '' ) {
		$route = '';

		if ( ! empty( $cat_slug ) ) {
			if ( $item = AllVideoShareQuery::getCategory( $cat_slug, 'alias' ) ) {
				$route = self::getCategoryRoute( $item );		
			}
		}

		if ( empty( $route ) ) {
			$route = self::__findRoute( 'video' );
		}

		return $route;
	}

	/**
	 * Get the URL route for a video
	 *
	 * @param   object  $item    The video object.
	 * @param   int     $itemid  The id of the menu item.
	 *
	 * @return  string  $route   The link to the video
	 *
	 * @since   4.2.0
	 */
	public static function getVideoRoute( $item, $itemid = 0 ) {
		if ( empty( $itemid ) ) {
			$params = ComponentHelper::getParams( 'com_allvideoshare' );
			$itemid = (int) $params->get( 'itemid_video' );
		}

		if ( $itemid == -1 ) {
			$route = self::__findRoute( 'video', $item );
		} else {
			$route = "index.php?option=com_allvideoshare&view=video&slg={$item->slug}";

			if ( $itemid > 1 ) {
				$route .= "&Itemid=$itemid";
			}
		}

		return $route;
	}

	/**
	 * Get the URL route for a search
	 *
	 * @param   integer  $itemid  The id of the menu item.
	 *
	 * @return  string   $route   The link to the search
	 *
	 * @since   4.2.0
	 */
	public static function getSearchRoute( $itemid = 0) {
		$route = "index.php?option=com_allvideoshare&view=search";

		if ( $itemid > 1 ) {
			$route .= "&Itemid={$itemid}";
		}

		return $route;
	}

	private static function __findRoute( $view, $item = '' ) { 		
		$itemid = 0;
		$is_exact_match_found = 0;
		
		// Check if there is a menu item with the given SLUG value for the view
		if ( ! empty( $item ) ) {
			$itemid = self::__findMenuItem( "index.php?option=com_allvideoshare&view={$view}&slg={$item->slug}" );
			
			if ( $itemid > 0 ) {
				$is_exact_match_found = 1;
			}
		}
		
		// Check if there is a menu item atleast for the view
		if ( empty( $itemid ) ) {
			$itemid = self::__findMenuItem( "index.php?option=com_allvideoshare&view={$view}" );

			if ( empty( $item ) && $itemid > 0 ) {
				$is_exact_match_found = 1;
			}
		}

		if ( empty( $itemid ) ) {
			$itemid = self::__findMenuItem( "index.php?option=com_allvideoshare&view={$view}&slg=0" );

			if ( empty( $item ) && $itemid > 0 ) {
				$is_exact_match_found = 1;
			}
		}
		
		// Fallback to the current itemid
		if ( empty( $itemid ) ) {
			$itemid = Factory::getApplication()->input->getInt( 'Itemid', 0 );
		}

		// Build route
		if ( $is_exact_match_found ) {
			$route = "index.php?Itemid={$itemid}";
		} else {
			$route = "index.php?option=com_allvideoshare&view={$view}";

			if ( ! empty( $item ) ) {
				$route .= "&slg={$item->slug}";
			}

			if ( $itemid > 0 ) {
				$route .= "&Itemid={$itemid}";
			}
		}
		
		return $route;	
	}

	private static function __findMenuItem( $link ) {	
		$db = Factory::getDbo();
		
		$query = 'SELECT id FROM #__menu WHERE link=' . $db->quote( $link ) . ' AND published=1 LIMIT 1';
		$db->setQuery( $query );

		if ( $itemid = $db->loadResult() ) {
			return $itemid;
		}
		
		return 0;
	}

}
