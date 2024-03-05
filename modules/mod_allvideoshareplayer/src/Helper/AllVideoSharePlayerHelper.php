<?php
/**
 * @version     4.2.0
 * @package     Com_AllVideoShare
 * @subpackage  Mod_AllVideoSharePlayer
 * @author      Vinoth Kumar <admin@mrvinoth.com>
 * @copyright   Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Module\AllVideoSharePlayer\Site\Helper;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Language;
use \Joomla\CMS\Language\Text;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Helper for mod_allvideoshareplayer
 *
 * @since  4.1.0
 */
Class AllVideoSharePlayerHelper {

	/**
	 * Retrieve video data
	 *
	 * @param   Joomla\Registry\Registry  &$params  Module parameters
	 *
	 * @return  object  The video item
	 * 
	 * @since   4.1.0
	 */
	public static function getItem( &$params ) {	
		$app = Factory::getApplication();
		$db  = Factory::getDbo();	

		// Vars
		$id = (int) $params->get( 'videoid' );
		$alias = AllVideoShareHelper::getAlias();

		$keyfield = 'none';	

		if ( ! empty( $id ) ) {
			$keyfield = 'id';	
		}

		if ( $params->get( 'autodetect' ) ) {
			$autodetect = 1;
		
			if ( empty( $alias ) ) {
				$autodetect = 0;
			}

			if ( $app->input->getCmd( 'option' ) == 'com_allvideoshare' && $app->input->getCmd( 'view' ) == 'category' ) { // Is a category view?
				$autodetect = 0;
			}

			if ( $autodetect ) {
				$keyfield = 'alias';
			}
		}
		
		// Query
		$query = 'SELECT * FROM #__allvideoshare_videos WHERE state=1';

		if ( $keyfield == 'id' ) {
			$query .= ' AND id=' . (int) $id;
		} elseif ( $keyfield == 'alias' ) {
			$query .= ' AND slg=' . $db->quote( $alias );
		} else {
			$catid = (int) $params->get( 'catid' );
			if ( ! empty( $catid ) ) {
				$query .= ' AND ( catid=' . $catid .' OR catids LIKE ' . $db->Quote( '% ' . $catid . ' %' ) . ' )';
			}

			$featured = (int) $params->get( 'featured', 0 );
			if ( 1 == $featured ) {
				$query .= ' AND featured=1';
			}

			$orderby = $params->get( 'orderby', 'latest' );
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
			}	

			$query .= ' LIMIT 1';			
		}

		$db->setQuery( $query );
		$item = $db->loadObject();		
		
		return $item;		
	}
	
}
