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

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Uri\Uri;

/**
 * Class AllVideoShareYouTubeApi.
 *
 * @since  4.1.2
 */
class AllVideoShareYouTubeApi {

	/**
     * @var  string  The YouTube API Key.
	 * 
	 * @since  4.1.2
     */
	protected $apiKey;

	/**
     * @var  array  The YouTube API Urls.
	 * 
	 * @since  4.1.2
     */
    protected $apiUrls = array(       
		'playlistItems.list' => 'https://www.googleapis.com/youtube/v3/playlistItems',
		'channels.list'      => 'https://www.googleapis.com/youtube/v3/channels',
		'search.list'        => 'https://www.googleapis.com/youtube/v3/search',
		'videos.list'        => 'https://www.googleapis.com/youtube/v3/videos'
	);

	/**
	 * Query videos.
	 * 
     * @param   array  $params  Array of query params.
	 * 
     * @return  mixed
	 * 
	 * @since   4.1.2
     */
    public function query( $params = array() ) {
		// Get YouTube API Key
		if ( empty( $params['apiKey'] ) ) {
			return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_API_KEY' ) );
		}

		$this->apiKey = $params['apiKey'];

		// Process output		
		$response = array();

		if ( isset( $params['src'] ) ) {
			$params['src'] = trim( $params['src'] );
		}

		switch ( $params['type'] ) {
			case 'playlist':
				if ( empty( $params['src'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_PLAYLIST' ) );
				}
				
				$response = $this->requestApiPlaylistItems( $params );
				break;

			case 'channel':
				if ( empty( $params['src'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_CHANNEL' ) );
				}

				$params['id'] = $this->getChannelId( $params );

				if ( empty( $params['id'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_CHANNEL' ) );
				}

				// Get playlistId from the channel
				$playlistId = $this->getPlaylistId( $params );

				if ( empty( $playlistId ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_NO_VIDEOS_FOUND' ) );
				}

				// Get videos using the playlistId
				$params['src'] = $playlistId;
				$response = $this->requestApiPlaylistItems( $params );
				break;

			case 'username':
				if ( empty( $params['src'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_USERNAME' ) );
				}

				// Get playlistId from the channel 
				$params['forUsername'] = $this->parseYouTubeIdFromUrl( $params['src'], 'username' );
				$playlistId = $this->getPlaylistId( $params );

				if ( empty( $playlistId ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_NO_VIDEOS_FOUND' ) );
				}

				// Get videos using the playlistId
				$params['src'] = $playlistId;
				$response = $this->requestApiPlaylistItems( $params );
				break;

			case 'search':
				if ( empty( $params['src'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_SEARCH' ) );
				}
				
				$response = $this->requestApiSearch( $params );						
				break;

			case 'videos':			
				if ( empty( $params['src'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_VIDEOS' ) );
				}

				$response = $this->requestApiVideos( $params );
				break;

			case 'livestream':
				if ( empty( $params['src'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_CHANNEL' ) );
				}

				$response = new \stdClass();
				$response->id = $this->getChannelId( $params );

				if ( empty( $response->id ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_CHANNEL' ) );
				}
				break;

			default: // video
				if ( empty( $params['src'] ) ) {
					return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_INVALID_SOURCE_VIDEO' ) );
				}
				
				$response = $this->requestApiVideo( $params );
				break;
		}

		return $response;
	}

	/**
	 * Grab the playlist, channel or video Id using the YouTube Url given.
	 * 
     * @param   string  $url   YouTube Url.
	 * @param   string  $type  Type of the Url (playlist|channel|video).
	 * 
     * @return  mixed
	 * 
	 * @since   4.1.2
     */
    private function parseYouTubeIdFromUrl( $url, $type = 'video' ) {
		$url = trim( $url );
		$id  = $url;

		switch ( $type ) {
			case 'playlist':
				if ( preg_match( '/list=(.*)&?\/?/', $url, $matches ) ) {
					$id = $matches[1];
				}
				break;

			case 'channel':
				if ( filter_var( $id, FILTER_VALIDATE_URL ) ) {
					$id = '';
				}

				$url = parse_url( rtrim( $url, '/' ) );

				if ( isset( $url['path'] ) && preg_match( '/^\/channel\/(([^\/])+?)$/', $url['path'], $matches ) ) {
					$id = $matches[1];
				}
				break;

			case 'username':
				$url = parse_url( rtrim( $url, '/' ) );

				if ( isset( $url['path'] ) && preg_match( '/^\/user\/(([^\/])+?)$/', $url['path'], $matches ) ) {
					$id = $matches[1];
				}
				break;
			
			default: // video
				$url = parse_url( $url );
			
				if ( array_key_exists( 'host', $url ) ) {				
					if ( 0 === strcasecmp( $url['host'], 'youtu.be' ) ) {
						$id = substr( $url['path'], 1 );
					} elseif ( 0 === strcasecmp( $url['host'], 'www.youtube.com' ) ) {
						if ( isset( $url['query'] ) ) {
							parse_str( $url['query'], $url['query'] );

							if ( isset( $url['query']['v'] ) ) {
								$id = $url['query']['v'];
							}
						}
							
						if ( empty( $id ) ) {
							$url['path'] = explode( '/', substr( $url['path'], 1 ) );

							if ( in_array( $url['path'][0], array( 'e', 'embed', 'v' ) ) ) {
								$id = $url['path'][1];
							}
						}
					}
				}
		}

		return $id;
	}

	/**
	 * Get the channel ID.
	 * 
     * @param   array   $params  Array of query params.
	 * 
     * @return  string
	 * 
	 * @since   4.2.0
     */
    private function getChannelId( $params = array() ) {
		// Parse channel ID from URL: https://www.youtube.com/channel/XXXXXXXXXX
		$id = $this->parseYouTubeIdFromUrl( $params['src'], 'channel' );

		if ( empty( $id ) ) {
			// Get channel ID from a Video URL: https://www.youtube.com/watch?v=XXXXXXXXXX		
			$videoId = $this->parseYouTubeIdFromUrl( $params['src'], 'video' );

			// Request from cache
			$channelIds = $this->getOption( 'youtube_channel_ids', array() );
			if ( ! is_array( $channelIds ) ) {
				$channelIds = (array) $channelIds;
			}

			if ( isset( $channelIds[ $videoId ] ) && ! empty( $channelIds[ $videoId ] ) ) {
				return $channelIds[ $videoId ];
			}

			// Request from API
			$apiUrl = $this->getApiUrl( 'videos.list' );

			$params['id'] = $videoId;
			
			$apiParams = $this->safeMergeParams(
				array(
					'id'    => '',
					'part'  => 'id,snippet,contentDetails,status',
					'cache' => 0
				), 
				$params
			);

			$apiResponse = $this->requestApi( $apiUrl, $apiParams, 'channelId' );
			if ( isset( $apiResponse->error ) ) {
				return $id;	
			}

			$videos = $this->parseVideos( $apiResponse );
			if ( isset( $videos->error ) ) {
				return $id;
			}

			// Process output
			if ( $id = $videos[0]->channel_id ) {
				// Store in cache
				$channelIds[ $videoId ] = $id;
				$this->updateOption( 'youtube_channel_ids', $channelIds );
			}
		}

		return $id;		
	}

	/**
	 * Get playlistId using channels API.
	 * 
     * @param   array  $params  Array of query params.
	 * 
     * @return  mixed
	 * 
	 * @since   4.2.0
     */
	private function getPlaylistId( $params = array() ) {
		// Request from cache
		$playlistIds = $this->getOption( 'youtube_playlist_ids', array() );
		if ( ! is_array( $playlistIds ) ) {
			$playlistIds = (array) $playlistIds;
		}

		$key = '';

		if ( isset( $params['forUsername'] ) && ! empty( $params['forUsername'] ) ) {
			$key = $params['forUsername'];
		}

		if ( isset( $params['id'] ) && ! empty( $params['id'] ) ) {
			unset( $params['forUsername'] );
			$key = $params['id'];
		}

		if ( isset( $playlistIds[ $key ] ) && ! empty( $playlistIds[ $key ] ) ) {
			return $playlistIds[ $key ];
		}

		// Request from API	
		$apiUrl = $this->getApiUrl( 'channels.list' );

		$apiParams = $this->safeMergeParams(
			array(
				'id'          => '',
				'forUsername' => '',
				'part'        => 'contentDetails',
				'cache'       => 0
			),
			$params
		);

		$apiResponse = $this->requestApi( $apiUrl, $apiParams, 'playlistId' );
		if ( isset( $apiResponse->error ) ) {
			return $apiResponse;
		}

		if ( ! isset( $apiResponse->items ) ) {
			return false;
		}

		$items = $apiResponse->items;
		if ( ! is_array( $items ) || count( $items ) == 0 ) {
			return false;
		}

		// Process output
		if ( $id = $items[0]->contentDetails->relatedPlaylists->uploads ) {
			// Store in cache
			$playlistIds[ $key ] = $id;
			$this->updateOption( 'youtube_playlist_ids', $playlistIds );

			// Return
			return $id;
		}

		return $response;
	}

	/**
	 * Get videos using playlistItems API.
	 * 
     * @param   array  $params  Array of query params.
	 * 
     * @return  stdClass
	 * 
	 * @since   4.1.2
     */
    private function requestApiPlaylistItems( $params = array() ) {
		$apiUrl = $this->getApiUrl( 'playlistItems.list' );
		
		$params['playlistId'] = $this->parseYouTubeIdFromUrl( $params['src'], 'playlist' );

    	$apiParams = $this->safeMergeParams(
			array(
				'playlistId' => '',
				'part'       => 'id,snippet,contentDetails,status',
				'maxResults' => 50,
				'pageToken'  => '',
				'cache'      => 0
			),
			$params
		);
		
		$apiResponse = $this->requestApi( $apiUrl, $apiParams );
		if ( isset( $apiResponse->error ) ) {
			return $apiResponse;
		}

		$videos = $this->parseVideos( $apiResponse );
		if ( isset( $videos->error ) ) {
			return $videos;
		}

		// Process output
		$response = new \stdClass();	
		$response->pageInfo = $this->parsePageInfo( $apiResponse );
		$response->videos = $videos;

		return $response;		
	}	

	/**
	 * Get videos using search API.

     * @param   array  $params  Array of query params.
	 * 
     * @return  stdClass
	 * 
	 * @since   4.1.2
     */
    private function requestApiSearch( $params = array() ) {
		$apiUrl = $this->getApiUrl( 'search.list' );				

		$params['q'] = $params['src'];	

		if ( ! empty( $params['q'] ) ) {
			$params['q'] = str_replace( '|', '%7C', $params['q'] );
		}

		$params['type'] = 'video'; // Overrides user defined type value 'search'

		$apiParams = $this->safeMergeParams(
			array(
				'q'               => '',
				'channelId'       => '',
				'type'            => 'video',
				'videoEmbeddable' => true,
				'part'            => 'id,snippet',
				'order'           => 'date',
				'maxResults'      => 50,
				'pageToken'       => '',
				'cache'           => 0
			),
			$params
		);
		
		$apiResponse = $this->requestApi( $apiUrl, $apiParams );
		if ( isset( $apiResponse->error ) ) {
			return $apiResponse;
		}

		$videos = $this->parseVideos( $apiResponse );
		if ( isset( $videos->error ) ) {
			return $videos;
		}

		// Process output
		$response = new \stdClass();
		$response->pageInfo = $this->parsePageInfo( $apiResponse );
		$response->videos = $videos;

		return $response;	
	}		

	/**
	 * Get details of the given video Urls.
	 * 
     * @param   array  $params  Array of query params.
	 * 
     * @return  stdClass
	 * 
	 * @since   4.1.2
     */
  	private function requestApiVideos( $params = array() ) {
		$apiUrl = $this->getApiUrl( 'videos.list' );	

		$urls = str_replace( "\n", ',', $params['src'] );
		$urls = str_replace( ' ', ',', $urls );
		$urls = explode( ',', $urls );
		$urls = array_filter( $urls );

		$ids = array();
		foreach ( $urls as $url ) {
			$ids[] = $this->parseYouTubeIdFromUrl( $url, 'video' );
		}
		$totalVideos = count( $ids );
		$totalPages  = ceil( $totalVideos / $params['maxResults'] );

		$currentPage = isset( $params['pageToken'] ) ? (int) $params['pageToken'] : 1;
		$currentPage = max( $currentPage, 1 );
		$currentPage = min( $currentPage, $totalPages );

		$offset = ( $currentPage - 1 ) * $params['maxResults'];
		if ( $offset < 0 ) {
			$offset = 0;
		}

		$currentIds   = array_slice( $ids, $offset, $params['maxResults'] );
		$params['id'] = implode( ',', $currentIds );

		$apiParams = $this->safeMergeParams(
			array(
            	'id'    => '',
				'part'  => 'id,snippet,contentDetails,status',
				'cache' => 0
			), 
			$params
		);

		$apiResponse = $this->requestApi( $apiUrl, $apiParams );
		if ( isset( $apiResponse->error ) ) {
			return $apiResponse;
		}

		$videos = $this->parseVideos( $apiResponse );
		if ( isset( $videos->error ) ) {
			return $videos;
		}

		// Process output
		$response = new \stdClass();
		$response->videos = $videos;

		$response->pageInfo = new \stdClass();
		$response->pageInfo->totalVideos = $totalVideos;

		if ( $currentPage > 1 ) {
			$response->pageInfo->prevPageToken = $currentPage - 1;
		}

		if ( $currentPage < $totalPages ) {
			$response->pageInfo->nextPageToken = $currentPage + 1;
		}

		return $response;		
	}

	/**
	 * Get details of the given video Id.
	 * 
     * @param   array  $params   Array of query params.
	 * 
     * @return  stdClass
	 * 
	 * @since   4.1.2
     */
    private function requestApiVideo( $params = array() ) {
		$apiUrl = $this->getApiUrl( 'videos.list' );
		
		$params['id'] = $this->parseYouTubeIdFromUrl( $params['src'], 'video' );
		
		$apiParams = $this->safeMergeParams(
			array(
            	'id'    => '',
				'part'  => 'id,snippet,contentDetails,status',
				'cache' => 0
			), 
			$params
		);

		$apiResponse = $this->requestApi( $apiUrl, $apiParams );
		if ( isset( $apiResponse->error ) ) {
			return $apiResponse;
		}

		$videos = $this->parseVideos( $apiResponse );
		if ( isset( $videos->error ) ) {
			return $videos;
		}

		// Process output
		$response = new \stdClass();
		$response->videos = $videos;

		return $response;		
	}
	
	/**
     * Get API Url by request.
	 *
     * @param   array  $name
	 * 
     * @return  string
	 * 
	 * @since   4.1.2
     */
  	private function getApiUrl( $name ) {
    	return $this->apiUrls[ $name ];
	}	

	/**
     * Request data from the API server.
     *
     * @param   string  $url      YouTube API Url.
     * @param   array   $params   Array of query params.
	 * @param   string  $context  "channelId", "playlistId", or "videos"
	 * 
     * @return  mixed  
	 * 
	 * @since   4.1.2
     */
  	private function requestApi( $url, $params, $context = 'videos' ) {
		$db = Factory::getDbo();
		$date = Factory::getDate();

		$params['key'] = $this->apiKey;

		$q = '';
		if ( isset( $params['q'] ) ) {
			$q = $params['q'];
			unset( $params['q'] );
		}		

		// Clear expired data from cache
		$query = 'DELETE FROM #__allvideoshare_cache WHERE expiry_date <= ' . $db->quote( $date->toSql() );
		$db->setQuery( $query );
		$db->execute();

		// Request data from cache
		$cacheUrl = $url . ( strpos( $url, '?' ) === false ? '?' : '' ) . http_build_query( $params );
		if ( ! empty( $q ) ) {
			$cacheUrl .= '&q=' . $q; 
		}

		$cacheName  = 'youtube_' . md5( $cacheUrl );		

		$query = 'SELECT * FROM #__allvideoshare_cache WHERE name=' . $db->quote( $cacheName );
		$db->setQuery( $query );
        $response = $db->loadObject();

		if ( ! empty( $response ) ) {
			$data = json_decode( $response->value );
			return $data;
		}		

		// Request data from API server
		$data = array();
		$response = NULL;

		$cacheDuration = 0;		
		if ( isset( $params['cache'] ) ) {
			$cacheDuration = (int) $params['cache'];
			unset( $params['cache'] );
		}
		
		$apiUrl = $url . ( strpos( $url, '?' ) === false ? '?' : '' ) . http_build_query( $params );
		if ( ! empty( $q ) ) {
			$apiUrl .= '&q=' . $q; 
		}

		if ( function_exists( 'curl_init' ) ) { 
			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_REFERER, Uri::root() );
			curl_setopt( $ch, CURLOPT_URL, $apiUrl );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $ch, CURLOPT_VERBOSE, 0 );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			$response = curl_exec( $ch );

			curl_close( $ch );		
		} elseif ( function_exists( 'file_get_contents' ) ) {
			$response = file_get_contents( $apiUrl );
		}

		if ( $response ) {
			$data = json_decode( $response );

			if ( ! $data ) {
				return $this->getError( json_last_error_msg() );	
			}

			// Store data in cache
			if ( 'videos' == $context && $cacheDuration > 0 ) {	
				if ( isset( $data->items ) && is_array( $data->items ) && count( $data->items ) > 0 ) {	
					$row = new \stdClass();
					
					$row->id = NULL;
					$row->name = $cacheName;
					$row->value = json_encode( $data );
					$row->expiry_date = date( 'Y-m-d H:i:s', strtotime( sprintf( '+%d seconds', $cacheDuration ) ) );	

					$db->insertObject( '#__allvideoshare_cache', $row );
				}
			}
		}		

		// Finally return the data
		return $data;
	}

	/**
     * Parse videos from the YouTube API response object.
     *
     * @param   object  $data  YouTube API response object.
	 * 
     * @return  mixed
	 * 
	 * @since   4.1.2
     */
    private function parseVideos( $data ) {
		if ( ! isset( $data->items ) ) {
			return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_NO_VIDEOS_FOUND' ) );
		}

		$items = $data->items;	
		if ( ! is_array( $items ) || 0 == count( $items ) ) {
			return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_NO_VIDEOS_FOUND' ) );
		}

		$videos = array();

		foreach ( $items as $item ) {
			$video = new \stdClass();

			// Video Id
			$video->id = '';	

			if ( isset( $item->snippet->resourceId ) && isset( $item->snippet->resourceId->videoId ) ) {
				$video->id = $item->snippet->resourceId->videoId;
			} elseif ( isset( $item->contentDetails ) && isset( $item->contentDetails->videoId ) ) {
				$video->id = $item->contentDetails->videoId;
			} elseif ( isset( $item->id ) && isset( $item->id->videoId ) ) {
				$video->id = $item->id->videoId;
			} elseif ( isset( $item->id ) ) {
				$video->id = $item->id;
			}
			
			// Video channel ID	
			$video->channel_id = '';
			
			if ( isset( $item->snippet->channelId ) ) {
				$video->channel_id = $item->snippet->channelId;
			}

			// Video title
			$video->title = $item->snippet->title;

			// Video description
			$video->description = $item->snippet->description;

			// Video thumbnails
			if ( isset( $item->snippet->thumbnails ) ) {
				$video->thumbnails = $item->snippet->thumbnails;
			}		

			// Video publish date
			$video->published_at = $item->snippet->publishedAt;

			// Push resulting object to the main array
			$status = 'private';
			
			if ( isset( $item->status ) && ( 'public' == $item->status->privacyStatus || 'unlisted' == $item->status->privacyStatus ) ) {
				$status = 'public';				
			}

			if ( isset( $item->snippet->status ) && ( 'public' == $item->snippet->status->privacyStatus || 'unlisted' == $item->snippet->status->privacyStatus ) ) {
				$status = 'public';				
			}

			if ( 'youtube#searchResult' == $item->kind ) {
				$status = 'public';				
			}

			if ( 'public' == $status ) {
				$videos[] = $video;
			}
		}

		if ( 0 == count( $videos ) ) {
			return $this->getError( Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_NO_VIDEOS_FOUND' ) );
		}

		return $videos;		
	}	

	/**
     * Parse page info from the YouTube API response object.
     *
     * @param   object  $data  YouTube API response object.
	 * 
     * @return  object
	 * 
	 * @since   4.1.2
     */
    private function parsePageInfo( $data ) {
		$pageInfo = new \stdClass();

		// Total count of videos found
		if ( isset( $data->pageInfo ) && isset( $data->pageInfo->totalResults ) ) {
			$pageInfo->totalVideos = $data->pageInfo->totalResults;
		}		

		// Token for the previous page
		if ( isset( $data->prevPageToken ) ) {
			$pageInfo->prevPageToken = $data->prevPageToken;
		}
		
		// Token for the next page
		if ( isset( $data->nextPageToken ) ) {
			$pageInfo->nextPageToken = $data->nextPageToken;
		}

		return $pageInfo;
	}

	/**
	 * Combine user params with known params and fill in defaults when needed.
	 *
	 * @param   array  $pairs   Entire list of supported params and their defaults.
	 * @param   array  $params  User defined params.
	 * 
	 * @return  array  $out  Combined and filtered params array.
	 * 
	 * @since   4.1.2
	*/
	private function safeMergeParams( $pairs, $params ) {
		$params = (array) $params;
		$out = array();
		
		foreach ( $pairs as $name => $default ) {
			if ( array_key_exists( $name, $params ) ) {
				$out[ $name ] = $params[ $name ];
			} else {
				$out[ $name ] = $default;
			}

			if ( empty( $out[ $name ] ) ) {
				unset( $out[ $name ] );
			}
		}
		
		return $out;
	}

	/**
	 * Retrieves an option value based on an option name.
	 * 
	 * @param   string  $option         Name of the option to retrieve.
	 * @param   mixed   $default_value  Default value to return if the option does not exist.
	 * 
	 * @return  mixed   Value of the option.
	 * 
	 * @since   4.2.0
	 */
	private function getOption( $option, $default_value = false ) {	
		$db = Factory::getDbo();

		$query = 'SELECT * FROM #__allvideoshare_options WHERE name=' . $db->quote( $option );
		$db->setQuery( $query );
        $response = $db->loadObject();

		if ( ! empty( $response ) ) {
			$value = unserialize( $response->value );
			return $value;
		}	

		return $default_value;
	}

	/**
	 * Updates the value of an option that was already added.
	 * 
	 * @param  string  $option  Name of the option to update.
	 * @param  mixed   $value   Option value.
	 * 
	 * @since  4.2.0
	 */
	private function updateOption( $option, $value ) {	
		$db = Factory::getDbo();

		$query = 'SELECT COUNT(id) FROM #__allvideoshare_options WHERE name=' . $db->quote( $option );
		$db->setQuery( $query );
        $count = $db->loadResult();

		if ( $count ) {
			$query = 'UPDATE #__allvideoshare_options SET value=' . $db->quote( serialize( $value ) ) . ' WHERE name=' . $db->quote( $option );
			$db->setQuery( $query );
			$db->execute();	
		} else {
			$row = new \stdClass();
					
			$row->id = NULL;
			$row->name = $option;
			$row->value = serialize( $value );

			$db->insertObject( '#__allvideoshare_options', $row );
		}
	}

	/**
	 * Build error object.
	 *
	 * @param   string  $message  Error message.
	 * 
	 * @return  object  Error object.
	 * 
	 * @since   4.1.2
	*/
	private function getError( $message ) {
		$obj = new \stdClass();
		$obj->error = 1;
		$obj->error_message = $message;

		return $obj;
	}
	
}
