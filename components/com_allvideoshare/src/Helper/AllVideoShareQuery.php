<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Helper;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use \Joomla\CMS\Uri\Uri;

/**
 * Class AllVideoShareQuery.
 *
 * @since  4.1.0
 */
class AllVideoShareQuery {	

	public static function getCategoryIdByAlias( $alias = '' ) {
		if ( empty( $alias ) ) {
			return 0;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery( true );

		$query
			->select( 'id' )
			->from( $db->quoteName( '#__allvideoshare_categories' ) )
			->where( $db->quoteName( 'slug' ) . ' = ' .  $db->quote( $alias ) );

        $db->setQuery( $query );
        $result = $db->loadResult();
		
		return ! empty( $result ) ? $result : 0;		
	}

	public static function getCategory( $key_value, $key_field = 'id', $state = 1 ) {
		if ( empty( $key_value ) ) {
			return null;
		}		

		$db = Factory::getDbo();
		$query = $db->getQuery( true );

		$query
			->select( '*' )
			->from( $db->quoteName( '#__allvideoshare_categories' ) );

		if ( $key_field == 'id' ) {
			$query->where( $db->quoteName( 'id' ) . ' = ' . (int) $key_value );
		} elseif ( $key_field == 'alias' ) {
			$query->where( $db->quoteName( 'slug' ) . ' = ' . $db->quote( $key_value ) );
		}		

		if ( $state ) {
			$query->where( $db->quoteName( 'state' ) . ' = 1' );
		}

		$params = ComponentHelper::getParams( 'com_allvideoshare' );
		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$accessLevels = $user->getAuthorisedViewLevels();

			$query->where( 'access IN (' . implode( ',', $accessLevels ) . ')' );			
		}

        $db->setQuery( $query );
        $item = $db->loadObject();

		return $item;
	}

	public static function getCategoryChildren( $parent ) {			
		$db = Factory::getDbo();
		$user = Factory::getUser();	

		$params = ComponentHelper::getParams( 'com_allvideoshare' );		
		$accessLevels = $user->getAuthorisedViewLevels();

		$ids = array( $parent );
		$array = $ids;

		while ( count( $array ) ) {
			$query = sprintf(
				'SELECT id FROM #__allvideoshare_categories WHERE state=1 AND parent IN (%s)',
				implode( ',', $array )
			);
			
			if ( ! $params->get( 'show_noauth', 1 ) ) {
				$query .= ' AND access IN (' . implode( ',', $accessLevels ) . ')';
			}

			$db->setQuery( $query );
			$array = $db->loadColumn();			

			if ( ! empty( $array ) ) {
				$ids = array_merge( $ids, $array );
			}			
		}

		$ids = array_map( 'intval', $ids );
		$ids = array_unique( $ids );

		return $ids;
	}

	public static function getVideoIdByAlias( $alias = '' ) {
		if ( empty( $alias ) ) {
			return 0;
		}
		
		$db = Factory::getDbo();
		$query = $db->getQuery( true );

		$query
			->select( 'id' )
			->from( $db->quoteName( '#__allvideoshare_videos' ) )
			->where( $db->quoteName( 'slug' ) . ' = ' .  $db->quote( $alias ) );

        $db->setQuery( $query );
        $result = $db->loadResult();
		
		return ! empty( $result ) ? $result : 0;		
	}

	public static function getVideo( $key_value, $key_field = 'id', $state = 1 ) {
		if ( empty( $key_value ) ) {
			return null;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery( true );

		$query
			->select( '*' )
			->from( $db->quoteName( '#__allvideoshare_videos' ) );

		if ( $key_field == 'id' ) {
			$query->where( $db->quoteName( 'id' ) . ' = ' . (int) $key_value );
		} elseif ( $key_field == 'alias' ) {
			$query->where( $db->quoteName( 'slug' ) . ' = ' . $db->quote( $key_value ) );
		}		

		if ( $state ) {
			$query->where( $db->quoteName( 'state' ) . ' = 1' );
		}

		$params = ComponentHelper::getParams( 'com_allvideoshare' );
		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$accessLevels = $user->getAuthorisedViewLevels();

			$query->where( 'access IN (' . implode( ',', $accessLevels ) . ')' );			
		}

        $db->setQuery( $query );
        $item = $db->loadObject();

		return $item;
	}		

	public static function getVideosCount( $catid ) {			
		$db = Factory::getDbo();	
		
		$query = 'SELECT COUNT(id) FROM #__allvideoshare_videos WHERE state=1';
		$catids = self::getCategoryChildren( $catid );

		$likes = array();
		foreach ( $catids as $catid ) {
			$likes[] = 'catids LIKE ' . $db->quote( '% ' . $catid . ' %' );
		}
		$query .= ' AND ( catid IN (' . implode( ',', $catids ) . ') OR ' . implode( ' OR ', $likes ) . ' )';

		$params = ComponentHelper::getParams( 'com_allvideoshare' );
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
