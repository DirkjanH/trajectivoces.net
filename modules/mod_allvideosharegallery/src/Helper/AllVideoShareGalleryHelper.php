<?php
/**
 * @version     4.2.0
 * @package     Com_AllVideoShare
 * @subpackage  Mod_AllVideoShareGallery
 * @author      Vinoth Kumar <admin@mrvinoth.com>
 * @copyright   Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Module\AllVideoShareGallery\Site\Helper;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Language\Language;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

/**
 * Helper for mod_allvideosharegallery
 *
 * @since  4.1.0
 */
Class AllVideoShareGalleryHelper {

	/**
	 * Retrieve categories
	 *
	 * @param   Joomla\Registry\Registry  &$params  Module parameters
	 *
	 * @return  array  The categories list.
	 * 
	 * @since   4.1.0
	 */
	public static function getCategories( &$params ) {	
		$db = Factory::getDbo();

		$parent = AllVideoShareQuery::getCategoryIdByAlias( $params->get( 'category' ) );
		$query = 'SELECT * FROM #__allvideoshare_categories WHERE state=1 AND parent=' . (int) $parent;

		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$accessLevels = $user->getAuthorisedViewLevels();
			
			$query .= ' AND access IN (' . implode( ',', $accessLevels ) . ')';
		}
		
		$orderby = $params->get( 'orderby', 'title_asc' );
		
		switch ( $orderby ) {			
			case 'latest':
			case 'latest_by_date':
				$query .= ' ORDER BY id DESC';
				break;
			case 'title_asc':
				$query .= ' ORDER BY name ASC';
				break;
			case 'title_desc':
				$query .= ' ORDER BY name DESC';
				break;
			case 'random':
				$query .= ' ORDER BY RAND()';
				break;
			default:
				$query .= ' ORDER BY ordering ASC';
		}
		
		$limit = (int) $params->get( 'rows', 3 ) * (int) $params->get( 'cols', 3 );
		
		$db->setQuery( $query, 0, $limit );
       	$items = $db->loadObjectList();

        return $items;		
    }

	/**
	 * Retrieve categories count
	 *
	 * @param   Joomla\Registry\Registry  &$params  Module parameters
	 *
	 * @return  int  The categories count.
	 * 
	 * @since   4.1.0
	 */
	public static function getTotalCategories( &$params ) {	
		$db = Factory::getDbo();

		$parent = AllVideoShareQuery::getCategoryIdByAlias( $params->get( 'category' ) );
		$query = 'SELECT COUNT(id) FROM #__allvideoshare_categories WHERE state=1 AND parent=' . (int) $parent;
		
		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$accessLevels = $user->getAuthorisedViewLevels();
			
			$query .= ' AND access IN (' . implode( ',', $accessLevels ) . ')';
		}
		
		$db->setQuery( $query );
       	$count = $db->loadResult();
			
        return $count;		
    }

	/**
	 * Retrieve videos
	 *
	 * @param   Joomla\Registry\Registry  &$params  Module parameters
	 *
	 * @return  array  The videos list.
	 * 
	 * @since   4.1.0
	 */
	public static function getVideos( &$params ) {	
		$db = Factory::getDbo();

		$query = 'SELECT * FROM #__allvideoshare_videos WHERE state=1';
		
		$catid = AllVideoShareQuery::getCategoryIdByAlias( $params->get( 'category' ) );
		if ( $catid ) {
			$query .= ' AND ( catid=' . $catid .' OR catids LIKE ' . $db->Quote( '% ' . $catid . ' %' ) . ' )';
		}
		
		$alias = AllVideoShareHelper::getAlias();
		if ( ! empty( $alias ) ) {
			$query .= ' AND slug != ' . $db->Quote( $alias );
		}
		
		if ( 'featured' == $params->get( 'orderby' ) || 1 == $params->get( 'featured', 0 ) ) {
			$query .= ' AND featured=1';
		}

		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$accessLevels = $user->getAuthorisedViewLevels();
			
			$query .= ' AND access IN (' . implode( ',', $accessLevels ) . ')';
		}
		
		$orderby = $params->get( 'orderby', 'latest_by_date' );

		switch ( $orderby ) {			
			case 'latest':
				$query .= ' ORDER BY id DESC';
				break;
			case 'latest_by_date':
				$query .= ' ORDER BY created_date DESC';
				break;
			case 'title_asc':
				$query .= ' ORDER BY title ASC';
				break;
			case 'title_desc':
				$query .= ' ORDER BY title DESC';
				break;
			case 'popular':
				$query .= ' ORDER BY views DESC';
				break;
			case 'popular_by_ratings':
				$query .= ' ORDER BY ratings DESC';
				break;
			case 'popular_by_likes':
				$query .= ' ORDER BY likes DESC';
				break;
			case 'random':
				$query .= ' ORDER BY RAND()';
				break;
			default:
				$query .= ' ORDER BY ordering ASC';
				break;
		}			
		
		$limit = (int) $params->get( 'rows', 3 ) * (int) $params->get( 'cols', 3 );
		
		$db->setQuery( $query, 0, $limit );
       	$items = $db->loadObjectList();

		// Bind categories
		if ( $params->get( 'category_name' ) ) {
			foreach ( $items as $item ) {
				if ( isset( $item->catid ) && ! empty( $item->catid ) ) {
					if ( $category = AllVideoShareQuery::getCategory( $item->catid ) ) {
						$item->category = $category;
					}
				}
		
				if ( $params->get( 'multi_categories' ) ) {
					if ( isset( $item->catids ) && ! empty( $item->catids ) ) {
						$item->catids = explode( ' ', trim( $item->catids ) );		
						$item->categories = array();
			
						foreach ( $item->catids as $catid ) {
							if ( isset( $item->catid ) && $item->catid == $catid ) {
								continue;
							}
							
							if ( $category = AllVideoShareQuery::getCategory( $catid ) ) {
								$item->categories[] = $category;
							}
						}
					}
				}
			}
		}

        return $items;		
    }

	/**
	 * Retrieve videos count
	 *
	 * @param   Joomla\Registry\Registry  &$params  Module parameters
	 *
	 * @return  array  The videos count.
	 * 
	 * @since   4.1.0
	 */
	public static function getTotalVideos( &$params ) {	
		$db = Factory::getDbo();

		$query = 'SELECT COUNT(id) FROM #__allvideoshare_videos WHERE state=1';
		
		$catid = AllVideoShareQuery::getCategoryIdByAlias( $params->get( 'category' ) );
		if ( $catid ) {
			$query .= ' AND ( catid=' . $catid .' OR catids LIKE ' . $db->Quote( '% ' . $catid . ' %' ) . ' )';
		}
		
		$alias = AllVideoShareHelper::getAlias();
		if ( ! empty( $alias ) ) {
			$query .= ' AND slug != ' . $db->Quote( $alias );
		}
		
		if ( 'featured' == $params->get( 'orderby' ) || 1 == $params->get( 'featured', 0 ) ) {
			$query .= ' AND featured=1';
		}

		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$accessLevels = $user->getAuthorisedViewLevels();
			
			$query .= ' AND access IN (' . implode( ',', $accessLevels ) . ')';
		}
		
		$db->setQuery( $query );
       	$count = $db->loadResult();
			
        return $count;		
    }
	
}
