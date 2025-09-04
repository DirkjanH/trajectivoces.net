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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\Registry\Registry;
use \Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeApi;

/**
 * Class AllVideoShareYouTubeHelper.
 *
 * @since  4.1.2
 */
Class AllVideoShareYouTubeHelper {	

	/**
	 * Get YouTube image URL.
	 * 
	 * @param   stdClass  $video   YouTube video object.
	 * @param   stdClass  $params  The component/module params.
	 * 
	 * @return  string  YouTube image URL.
	 * 
	 * @since   4.1.2
	 */
	public static function getImageUrl( $video, $params ) {
		$src = Uri::root() . 'media/com_allvideoshare/images/placeholder.jpg';

        if ( isset( $video->thumbnails->default ) ) {
            $src = $video->thumbnails->default->url;
        }

        // 4:3 ( default - 120x90, high - 480x360, standard - 640x480 )
        if ( $params->get( 'image_ratio' ) == 75 ) {
            if ( isset( $video->thumbnails->high ) ) {
                $src = $video->thumbnails->high->url;
            }
        }

        // 16:9 ( medium - 320x180, maxres - 1280x720 )
        if ( $params->get( 'image_ratio' ) == 56.25 ) {
            if ( isset( $video->thumbnails->medium ) ) {
                $src = $video->thumbnails->medium->url;
            }
        }	

		return $src;
	}

	/**
	 * Get YouTube player URL.
	 * 
	 * @param   stdClass  $item    YouTube source object.
	 * @param   stdClass  $params  The component/module params.
	 * 
	 * @return  string  YouTube video embed URL.
	 * 
	 * @since   4.1.2
	 */
	public static function getPlayerUrl( $item, $params ) {
		$query = array();

		if ( $params->get( 'type' ) == 'livestream' ) {
			$url = "https://www.youtube-nocookie.com/embed/live_stream";
			$query['channel'] = $item->id;
		} else {
			$url = "https://www.youtube-nocookie.com/embed/{$item->id}";
		}

		if ( $params->get( 'autoplay' ) ) { // autoplay
			$query['autoplay'] = 1;
		}

		if ( $params->get( 'loop' ) ) { // loop (controlled by youtube.js)
			// $query['loop'] = 1;
		}

		if ( ! $params->get( 'controls' ) ) { // controls
			$query['controls'] = 0;
		}   

		if ( $params->get( 'modestbranding' ) ) { // modestbranding
			$query['modestbranding'] = 1;
		}

		if ( $params->get( 'cc_load_policy' ) ) { // cc_load_policy
			$query['cc_load_policy'] = 1;
		}

		if ( ! $params->get( 'iv_load_policy' ) ) { // iv_load_policy
			$query['iv_load_policy'] = 3;
		}

		if ( $params->get( 'hl' ) ) { // hl
			$query['hl'] = $params->get( 'hl' );
		}

		if ( $params->get( 'cc_lang_pref' ) ) { // cc_lang_pref
			$query['cc_lang_pref'] = $params->get( 'cc_lang_pref' );
		} 

		$query['rel'] = 0; // rel
		$query['playsinline'] = 1; // playsinline
		$query['enablejsapi'] = 1; // enablejsapi
		
		return $url . '?' . http_build_query( $query );
	}	

	/**
	 * Get video description to show on top of the player.
	 *
	 * @param   stdClass  $video       YouTube video object.
	 * @param   int       $wordsCount  Number of words to show by default.
	 * 
	 * @return  string  Video description.
	 * 
	 * @since   4.1.2
	 */
	public static function getVideoDescription( $video, $wordsCount = 30 ) {
		$description = $video->description;
		$wordsArray  = explode( ' ', strip_tags( $description ) );
		
		if ( count( $wordsArray ) > $wordsCount ) {
			$wordsArray[ $wordsCount ] = '<span class="avs-description-dots">...</span></span><span class="avs-description-more" style="display: none;">' . $wordsArray[ $wordsCount ];

			$description  = '<span class="avs-description-less">' . implode( ' ', $wordsArray ) . '</span>';
			$description .= '<div class="mt-2"><a href="javascript:void(0);" class="avs-description-toggle-btn">[+] ' . Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_BUTTON_LBL_SHOW_MORE' ) . '</a></div>';
		}

		return nl2br( $description );	
	}

	/**
	 * Get unique ID.
	 * 
	 * @return  string  Unique ID.
	 * 
	 * @since   4.1.2
	 */
	public static function getUniqid() {
		global $avsUniqid;

		if ( ! $avsUniqid ) {
			$avsUniqid = 0;
		}

		return uniqid() . ++$avsUniqid;
	}	

	public static function resolveParams( $params ) {		
		$global = ComponentHelper::getParams( 'com_allvideoshare' );

		$defaults = array(
			'youtube_api_key'    => $global->get( 'youtube_api_key' ),
			'type'               => 'playlist',
			'playlist'           => '',
			'channel'            => '',
			'username'           => '',
			'search'             => '',
			'order'              => 'relevance',
			'limit'              => '',
			'video'              => '',
			'videos'             => '',
			'fallback_message'   => '',
			'cache'              => 0,
			'layout'             => 'classic',
			'columns'            => 3,
			'per_page'           => 12,
			'image_ratio'        => 56.25,
			'title'              => 1,
			'title_length'       => 0,
			'excerpt'            => 1,
			'excerpt_length'     => 75,
			'pagination'         => 1,
			'pagination_type'    => 'more',
			'arrows'             => 1,
			'arrow_size'         => 24,
			'arrow_bg_color'     => '#0088cc',
			'arrow_icon_color'   => '#ffffff',
			'arrow_radius'       => 12,
			'arrow_top_offset'   => 30,
			'arrow_left_offset'  => -25,
			'arrow_right_offset' => -25,
			'dots'               => 1,
			'dot_size'           => 24,
			'dot_color'          => '#0088cc',
			'playlist_position'  => 'right',
			'playlist_color'     => 'dark',
			'playlist_width'     => 250,
			'playlist_height'    => 250,
			'player_ratio'       => 56.25,
			'player_title'       => 1,
			'player_description' => 1,
			'autoplay'           => 0,
			'autoadvance'        => 0,
			'loop'               => 0,
			'controls'           => 1,
			'modestbranding'     => 1,
			'cc_load_policy'     => 0,
			'iv_load_policy'     => 0,
			'hl'                 => '',
			'cc_lang_pref'       => ''
		);
		
		// Merge with global params
		if ( is_array( $params ) ) {
			$params = array_merge( $defaults, $params );
			$params = new Registry( $params );
		} else {
			foreach ( $defaults as $key => $value ) {
				$params->def( $key, $value );
			}				
		}

		$sourceType = $params->get( 'type' );
		if ( $sourceType == 'livestream' ) {
			$params->set( 'livestream', $params->get( 'channel' ) );
		}

		$layout = $params->get( 'layout', 'classic' );
		if ( $layout == 'inline' || $layout == 'popup' ) {
			$params->set( 'autoplay', 1 );
		}

		$columns = (int) $params->get( 'columns' );
		if ( $columns < 3 ) {
			$params->set( 'columns', 3 );
		} elseif ( $columns > 12 ) {
			$params->set( 'columns', 12 );
		}

		$limit = (int) $params->get( 'limit' );
		if ( empty( $limit ) || $limit > 500 ) {
			$params->set( 'limit', 500 );
		}

		$perPage = (int) $params->get( 'per_page' );
		if ( empty( $perPage ) || $perPage > 50 ) {
			$params->set( 'per_page', 50 );
		}	

		return $params;				 
	}

	public static function renderGallery( $params ) {
		$app      = Factory::getApplication();
		$document = Factory::getDocument();
		$language = Factory::getLanguage();		

		// Load component language file		
		$language->load( 'com_allvideoshare', JPATH_SITE );

		// Vars
		$params = self::resolveParams( $params );
		$uid    = self::getUniqid();
		$layout = $params->get( 'layout', 'classic' );

		// Import CSS & JS
		$wa = $app->getDocument()->getWebAssetManager();

		if ( ! $wa->assetExists( 'style', 'com_allvideoshare.youtube' ) ) {
			$wr = $wa->getRegistry();
			$wr->addRegistryFile( 'media/com_allvideoshare/joomla.asset.json' );
		}

		if ( $params->get( 'load_bootstrap' ) ) {
			$wa->useStyle( 'com_allvideoshare.bootstrap' );
		}

		if ( $layout == 'popup' ) {
			$wa->useStyle( 'com_allvideoshare.popup' );
			$wa->useScript( 'com_allvideoshare.popup' );
		}

		if ( $layout == 'slider' ) {
			$wa->useStyle( 'com_allvideoshare.slider' );
			$wa->useScript( 'com_allvideoshare.slider' );
		}

		$wa->useStyle( 'com_allvideoshare.site' );
		$wa->useScript( 'com_allvideoshare.youtube' );

		$inlineStyle = $params->get( 'custom_css' );

		if ( $layout == 'playlist' ) {
			if ( $params->get( 'playlist_position' ) == 'right' ) {
				$inlineStyle .= "
					#avs-youtube-layout-playlist-" . $uid . " .avs-playlist-player {
						width: calc(100% - " . (int) $params->get( 'playlist_width', 250 ) . "px);
					}

					#avs-youtube-layout-playlist-" . $uid . " .avs-playlist-videos {
						width: " . (int) $params->get( 'playlist_width', 250 ) . "px;
					}

					@media only screen and (max-width: 768px) {
						#avs-youtube-layout-playlist-" . $uid . " .avs-playlist-player,
						#avs-youtube-layout-playlist-" . $uid . " .avs-playlist-videos {
							width: 100%;
						}

						#avs-youtube-layout-playlist-" . $uid . " .avs-playlist-videos {
							max-height: " . (int) $params->get( 'playlist_height', 250 ) . "px;
							overflow-y: scroll;
						}
					}
				";
			} else {
				$inlineStyle .= "
					#avs-youtube-layout-playlist-" . $uid . " .avs-playlist-videos {
						max-height: " . (int) $params->get( 'playlist_height', 250 ) . "px;
						overflow-y: scroll;
					}
				";
			}
		}

		if ( ! empty( $inlineStyle ) ) {
			$wa->addInlineStyle( $inlineStyle );
		}

		$inlineScript = "
			if ( typeof( avs ) === 'undefined' ) {
				var avs = {};
			};

			avs.youtube = {};
			avs.youtube.baseurl = '" . URI::root() . "';
			avs.youtube.i18n = {};
			avs.youtube.i18n.active = '" . Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_LBL_ACTIVE' ) . "';
			avs.youtube.i18n.show_more = '" . Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_BUTTON_LBL_SHOW_MORE' ) . "';
			avs.youtube.i18n.show_less = '" . Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_BUTTON_LBL_SHOW_LESS' ) . "';
		";

		$wa->addInlineScript( $inlineScript, [ 'position' => 'before' ], [], [ 'com_allvideoshare.youtube' ] );

		// Query YouTube API
		$sourceType = $params->get( 'type' );

		$apiParams = array(
			'apiKey'     => $params->get( 'youtube_api_key' ),
			'type'       => $sourceType,
			'src'        => $params->get( $sourceType ),
			'order'      => $params->get( 'order' ), // Applicable only when type=search
			'maxResults' => (int) $params->get( 'per_page' ),
			'cache'      => (int) $params->get( 'cache' )
		);

		$youtubeApi = new AllVideoShareYouTubeApi();
		$response = $youtubeApi->query( $apiParams );

		// Output
		$html = '';

		if ( isset( $response->error ) ) {
			$html  = '<div class="avs">';
			$html .= '<div class="alert alert-danger mb-4">';
			if ( isset( $response->error_message ) ) {
				$html .= $response->error_message;
			} elseif ( isset( $response->error->message ) ) {
				$html .= $response->error->message;
			}
			$html .= '</div>';
			$html .= '</div>';
		} else {
			$displayData = array(
				'info'   => $response,
				'uid'    => $uid,
				'params' => $params
			);

			// Layout
			if ( $sourceType == 'video' ) {
				$layout = 'single';
			} elseif ( $sourceType == 'livestream' ) {
				$layout = 'livestream';
			}

			// Output
			ob_start();
			echo LayoutHelper::render( 'youtube.' . $layout, $displayData, JPATH_SITE . '/components/com_allvideoshare/layouts' );
			$html = ob_get_clean();
		}

		// Return
		return $html;
	}

}