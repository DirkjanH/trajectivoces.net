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
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use \Joomla\CMS\Session\Session;
use \Joomla\CMS\Uri\Uri;

/**
 * Class AllVideoSharePlayer
 *
 * @since  4.1.0
 */
class AllVideoSharePlayer {	

	/**
	 * Get the player HTML
	 *
	 * @param   array  $params  The player configuration parameters
	 *
	 * @return  string
	 * 
	 * @since   4.1.0
	 */
	public static function load( $config ) {
		$app  = Factory::getApplication();
		$lang = Factory::getLanguage();

		// Global component params
		$params = ComponentHelper::getParams( 'com_allvideoshare' );

		// Load component language file
		$lang->load( 'com_allvideoshare', JPATH_SITE );

		// Import CSS
		$wa = $app->getDocument()->getWebAssetManager();

		if ( ! $wa->assetExists( 'style', 'com_allvideoshare.site' ) ) {
			$wr = $wa->getRegistry();
			$wr->addRegistryFile( 'media/com_allvideoshare/joomla.asset.json' );
		}

		$wa->useStyle( 'com_allvideoshare.site' );

		// Get the player URL
		$src = self::getURL( $config );

		// Build player HTML
		$width = ! empty( $config['width'] ) ? $config['width'] : $params->get( 'player_width', 0 );
		$ratio = ! empty( $config['ratio'] ) ? $config['ratio'] : $params->get( 'player_ratio', 56.25 );

		$html = sprintf( 
			'<div class="avs-player-container" style="max-width: %s;"><div class="avs-player" style="padding-bottom: %s;"><iframe width="560" height="315" src="%s" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div></div>', 
			( ! empty( $width ) ? (int) $width . 'px' : '100%' ), 
			(float) $ratio . '%', 
			$src
		);

		// Return
		return $html;
	}

	/**
	 * Get the player URL
	 *
	 * @param   array  $params  The player configuration parameters
	 *
	 * @return  string
	 * 
	 * @since   4.1.0
	 */
	public static function getURL( $config = array() ) {
		// Build player URL
		$query = array(
			'option' => 'com_allvideoshare',
			'view'   => 'player',	
			'id'     => 0,		
			'format' => 'raw'			
		);

		if ( isset( $config['playerid'] ) ) {
			$query['pid'] = (int) $config['playerid'];
		}	

		if ( isset( $config['videoid'] ) ) {
			$query['id'] = (int) $config['videoid'];
		} 

		if ( isset( $config['id'] ) ) {
			$query['id'] = (int) $config['id'];
		} 		
		
		if ( empty( $query['id'] ) ) {
			if ( isset( $config['src'] ) ) {
				$query['src'] = base64_encode( $config['src'] );
			} 

			if ( isset( $config['image'] ) ) {
				$query['image'] = base64_encode( $config['image'] );
			} 
		}
		
		$properties = array( 'uid', 'autoplay', 'autoadvance', 'loop', 'volume', 'muted', 'controlbar', 'playlarge', 'rewind', 'play', 'fastforward', 'progress', 'currenttime', 'duration', 'volumectrl', 'captions', 'quality', 'speed', 'pip', 'download', 'fullscreen', 'embed', 'share', 'adsource', 'preroll', 'postroll', 'adtagurl', 'Itemid' );

		foreach ( $properties as $property ) {
			if ( ! isset( $config[ $property ] ) ) continue;

			switch ( $property ) {
				case 'adtagurl':
					$query['adtagurl'] = base64_encode( $config['adtagurl'] );
					break;
				case 'adsource':
					$query['adsource'] = in_array( $config['adsource'], array( 'preroll', 'postroll' ) ) ? $config['adsource'] : 'both';
					break;
				default: 
					$query[ $property ] = (int) $config[ $property ];
			}				
		}

		$src = Uri::root() . 'index.php?' . http_build_query( $query );

		// Return
		return $src;
	}

	/**
	 * Update views count
	 *
	 * @param   int  $id  The video id.
	 * 
	 * @since   4.1.0
	 */
	public static function updateViews( $id ) {		
		$session = Factory::getSession();
		
		$stored = array();		
		if ( $session->get( 'com_allvideoshare_views' ) ) {
			$stored = $session->get( 'com_allvideoshare_views' );
		}
		
		if ( ! in_array( $id, $stored ) ) {
		    $stored[] = $id;				
	 
			$db = Factory::getDbo();

		 	$query = 'UPDATE #__allvideoshare_videos SET views=views+1 WHERE id=' . (int) $id;
    	 	$db->setQuery( $query );
		 	$db->execute();
		 
		 	$session->set( 'com_allvideoshare_views', $stored );
		}		
	}
	
}
