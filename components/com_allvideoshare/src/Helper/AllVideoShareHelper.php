<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Helper;

defined( '_JEXEC' ) or die;

use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Helper\ModuleHelper;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use \Joomla\CMS\Object\CMSObject;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Registry\Registry;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

/**
 * Class AllVideoShareHelper.
 *
 * @since  4.1.0
 */
class AllVideoShareHelper {	

	public static function isVideo( $fileTemp ) {
		$allowed = false;
		$allowed_mime = array( 'video/*' );
		$illegal_mime = array( 'application/x-shockwave-flash', 'application/msword', 'application/excel', 'application/pdf', 'application/powerpoint', 'application/x-zip', 'text/plain', 'text/css', 'text/html', 'text/php', 'text/x-php', 'application/php', 'application/x-php', 'application/x-httpd-php', 'application/x-httpd-php-source' );	
		
		if ( function_exists( 'finfo_open' ) ) {			
			$finfo = finfo_open( FILEINFO_MIME );
			$type = finfo_file( $finfo, $fileTemp );				
			finfo_close( $finfo );			
		} elseif ( function_exists( 'mime_content_type' ) ) {			
			$type = mime_content_type( $fileTemp );
		}
		
		if ( strlen( $type ) && ! in_array( $type, $illegal_mime ) ) {		
			list( $m1, $m2 )= explode( '/', $type );
			
			foreach ( $allowed_mime as $k => $v ) {
				list ( $v1, $v2 ) = explode( '/', $v );
				if ( ( $v1 == '*' && $v2 == '*' ) || ( $v1 == $m1 && ( $v2 == $m2 || $v2 == '*' ) ) ) {
					$allowed = true;
					break;
				}
			}
			
			if ( $allowed == false ) return false;			
		}			
	
		if ( function_exists( 'file_get_contents' ) ) {
			$xss_check = file_get_contents( $fileTemp, false, null, 0, 256 );
			$html_tags = array( 'abbr', 'acronym', 'address', 'applet', 'area', 'audioscope', 'base', 'basefont', 'bdo', 'bgsound', 'big', 'blackface', 'blink', 'blockquote', 'body', 'bq', 'br', 'button', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'comment', 'custom', 'dd', 'del', 'dfn', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'fn', 'font', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'hr', 'html', 'iframe', 'ilayer', 'img', 'input', 'ins', 'isindex', 'keygen', 'kbd', 'label', 'layer', 'legend', 'li', 'limittext', 'link', 'listing', 'map', 'marquee', 'menu', 'meta', 'multicol', 'nobr', 'noembed', 'noframes', 'noscript', 'nosmartquotes', 'object', 'ol', 'optgroup', 'option', 'param', 'plaintext', 'pre', 'rt', 'ruby', 's', 'samp', 'script', 'select', 'server', 'shadow', 'sidebar', 'small', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'title', 'tr', 'tt', 'ul', 'var', 'wbr', 'xml', 'xmp', '!DOCTYPE', '!--' );
			
			foreach ( $html_tags as $tag ) {
				if ( stristr( $xss_check, '<' . $tag . ' ' ) || stristr( $xss_check, '<' . $tag . '>' ) || stristr( $xss_check, '<?php' ) ) {
					return false;
				}
			}
		}

		return true;
	}

	public static function isWebVTT( $fileTemp ) {
		$allowed = false;
		$allowed_mime = array( 'text/vtt', 'text/plain' );
		$illegal_mime = array( 'application/x-shockwave-flash', 'application/msword', 'application/excel', 'application/pdf', 'application/powerpoint', 'application/x-zip', 'text/css', 'text/html', 'text/php', 'text/x-php', 'application/php', 'application/x-php', 'application/x-httpd-php', 'application/x-httpd-php-source' );	
		$type = '';

		if ( function_exists( 'finfo_open' ) ) {			
			$finfo = finfo_open( FILEINFO_MIME_TYPE );
			$type = finfo_file( $finfo, $fileTemp );				
			finfo_close( $finfo );			
		} elseif ( function_exists( 'mime_content_type' ) ) {			
			$type = mime_content_type( $fileTemp );
		}
		
		if ( strlen( $type ) && ! in_array( $type, $illegal_mime ) ) {		
			list( $m1, $m2 )= explode( '/', $type );
			
			foreach ( $allowed_mime as $k => $v ) {
				list ( $v1, $v2 ) = explode( '/', $v );
				if ( ( $v1 == '*' && $v2 == '*' ) || ( $v1 == $m1 && ( $v2 == $m2 || $v2 == '*' ) ) ) {
					$allowed = true;
					break;
				}
			}			
		}			

		return $allowed;
	}

	public static function getFile( $pk, $table, $field ) {
		$db = Factory::getDbo();
		$query = $db->getQuery( true );

		$query
			->select( $field )
			->from( $table )
			->where( 'id = ' . (int) $pk );

		$db->setQuery( $query );

		return $db->loadResult();
	}

	public static function getVideoImageFromEmbedCode( $embedcode ) {
		$image = '';

		$document = new \DOMDocument();
		@$document->loadHTML( $embedcode );	

		$iframes = $document->getElementsByTagName( 'iframe' ); 
		if ( $iframes->length > 0 ) {
			if ( $iframes->item(0)->hasAttribute( 'src' ) ) {
				$src = $iframes->item(0)->getAttribute( 'src' );

				// YouTube
				if ( false !== strpos( $src, 'youtube.com' ) || false !== strpos( $src, 'youtu.be' ) ) {
					$image = self::getYouTubeVideoImage( $src );
				}
				
				// Vimeo
				elseif ( false !== strpos( $src, 'vimeo.com' ) ) {
					$image = self::getVimeoVideoImage( $src );
				}
			}
		}

		return $image;
	}

	public static function getYouTubeVideoImage( $url ) {
		if ( empty( $url ) ) {
			return '';
		}

		$image   = '';
		$videoId = self::getYouTubeVideoId( $url );

		if ( ! empty( $videoId ) ) {
			$image = 'https://img.youtube.com/vi/' . $videoId . '/0.jpg';
		}

		return $image;
	}

	public static function getYouTubeVideoId( $url ) {  
		if ( empty( $url ) ) {
			return '';
		}

		$videoId = false; 	
    	$url = parse_url( $url );		
		
    	if ( strcasecmp( $url['host'], 'youtu.be' ) === 0 ) {
        	$videoId = substr( $url['path'], 1 );
    	} elseif (  strcasecmp( $url['host'], 'www.youtube.com' ) === 0 ) {
        	if ( isset( $url['query'] ) ) {
           		parse_str( $url['query'], $url['query'] );
            	if  ( isset( $url['query']['v'] ) ) {
               		$videoId = $url['query']['v'];
            	}
        	}
			
        	if ( $videoId == false ) {
            	$url['path'] = explode( '/', substr( $url['path'], 1 ) );
            	if ( in_array( $url['path'][0], array( 'e', 'embed', 'v' ) ) ) {
                	$videoId = $url['path'][1];
            	}
        	}
    	}
		
    	return $videoId;
	}

	public static function getVimeoVideoImage( $url ) {
		if ( empty( $url ) ) {
			return '';
		}

		$image   = '';
		$videoId = ''; 
		$updated = 0;

		// Get image using the standard OEmbed API
		if ( function_exists( 'file_get_contents' ) ) {
			$oembed = json_decode( file_get_contents( "https://vimeo.com/api/oembed.json?url={$url}" ) );

			if ( $oembed ) {
				if ( isset( $oembed->video_id ) ) {
					$videoId = $oembed->video_id;
				}

				if ( isset( $oembed->thumbnail_url ) ) {
					$image   = $oembed->thumbnail_url; 
					$updated = 1;     
				}
			}
		}

		// Fallback to our old method to get the Vimeo ID
		if ( empty( $videoId ) ) {			
			$isVimeo = preg_match( '/vimeo\.com/i', $url );  

			if ( $isVimeo ) {
				$videoId = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $url, '/' ) );
			}
		}

		// Find large thumbnail using the Vimeo API v2
		if ( ! empty( $videoId ) ) {
			if ( function_exists( 'file_get_contents' ) ) {
				$response = unserialize( file_get_contents( "https://vimeo.com/api/v2/video/{$videoId}.php" ) );

				if ( is_array( $response ) && isset( $response[0]['thumbnail_large'] ) ) {
					$image = $response[0]['thumbnail_large'];
				}
			} elseif ( function_exists( 'curl_init' ) ) { 
				$url = "https://vimeo.com/api/v2/video/{$videoId}.json";

				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_URL, $url );
				$response = curl_exec( $ch );
				curl_close( $ch );

				if ( $response ) {
					$json = json_decode( $response );

					if ( is_array( $json ) && isset( $json[0]->thumbnail_large ) ) {
						$image = $json[0]->thumbnail_large;
					}
				}				
			}
		}

		// Get image from private videos
		if ( ! empty( $videoId ) && empty( $image ) ) {
			if ( function_exists( 'curl_init' ) ) {
				$params = ComponentHelper::getParams( 'com_allvideoshare' );

				if ( $token = $params->get( 'vimeo_authorization_token' ) ) {
					$ch = curl_init();
					curl_setopt( $ch, CURLOPT_URL, "https://api.vimeo.com/videos/{$videoId}/pictures" );
					
					$authorization = "Authorization: Bearer " . $token; // Prepare the authorisation token
					curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' , $authorization )); // Inject the token into the header
			
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
					curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
					$response = curl_exec( $ch );
					curl_close( $ch );
			
					if ( $response ) {
						$json = json_decode( $response );
			
						if ( $json && isset( $json->data ) ) {               
							$canBreak = false;
			
							foreach ( $json->data as $item ) {
								foreach ( $item->sizes as $picture ) {
									$image = $picture->link;
									$updated = 1;
			
									if ( $picture->width >= 400 ) {
										$canBreak = true;
										break;
									}
								}
			
								if ( $canBreak ) break;
							}
						}
					}
				}
			}
		}

		if ( $updated ) {
			if ( strpos( $image, '?' ) !== false ) {
				$image .= '&new=1';
			} else {
				$image .= '?new=1';
			}
		}
	
		return $image;
	}

	public static function canDo() {
		$db = Factory::getDbo();
		return in_array( $db->getPrefix() . 'allvideoshare_options', $db->getTableList() );
	}

	public static function getActions() {
		$user   = Factory::getUser();
		$result = new CMSObject;

		$assetName = 'com_allvideoshare';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ( $actions as $action )	{
			$result->set( $action, $user->authorise( $action, $assetName ) );
		}

		return $result;
	}
	
	public static function resolveParams( $params ) {
		// An ugly fallback to old versions
		if ( $columns = $params->get( 'no_of_cols' ) ) {
			$params->set( 'cols', $columns );
		}

		if ( $rows = $params->get( 'no_of_rows' ) ) {
			$params->set( 'rows', $rows );
		}	
		
		if ( $params->get( 'popup' ) == -1 ) {				
			$params->set( 'popup', '' );
		}

		// Merge with global params
		$global = ComponentHelper::getParams( 'com_allvideoshare' );

		$params = new Registry( json_decode( $params ) );
		$temp   = clone $global;

		$temp->merge( $params );
		$params = $temp;
		
		// Premium
		$canDo = self::canDo();

		if ( ! $canDo ) {
			$params->set( 'popup', 0 );
			$params->set( 'ratings', 0 );
			$params->set( 'likes', 0 );			
			$params->set( 'multi_categories', 0 );
		}	

		return $params;				 
	}

	public static function getImage( $item ) {
		$params = ComponentHelper::getParams( 'com_allvideoshare' );

		if ( $item ) {
			// Update old image URLs on the Vimeo videos
			if ( isset( $item->type ) && $item->type == 'vimeo' ) {
				$update = 0;

				if ( empty( $item->thumb ) ) {
					$update = 1;
				} else {
					if ( strpos( $item->thumb, 'vimeocdn.com' ) !== false ) {
						$query = parse_url( $item->thumb, PHP_URL_QUERY );
						parse_str( $query, $parsed );

						if ( ! isset( $parsed['new'] ) ) {
							$update = 1;
						}
					}
				}

				if ( $update ) {
					$image = self::getVimeoVideoImage( $item->vimeo );

					if ( ! empty( $image ) ) {
						$db = Factory::getDbo();

						$query = 'UPDATE #__allvideoshare_videos SET thumb=' . $db->quote( $image ) . ' WHERE id=' . (int) $item->id;
						$db->setQuery( $query );
						$db->execute();

						$item->thumb = $image;
					}
				}
			}

			// Default Image
			if ( empty( $item->thumb ) && $params->get( 'default_image' ) ) {
				$item->thumb = Uri::root() . $params->get( 'default_image' );
			}

			if ( ! empty( $item->thumb ) )	{
				// If an uploaded image file
				if ( strpos( $item->thumb, 'media/com_allvideoshare/' ) !== false || strpos( $item->thumb, 'media/allvideoshare/' ) !== false ) {
					$parsed = explode( 'media', $item->thumb );
					$item->thumb = URI::root() . 'media' . $parsed[1];
				}
				
				return $item->thumb;
			}	
		}

		return Uri::root() . 'media/com_allvideoshare/images/placeholder.jpg';				 
	}

	public static function getAlias() {		
        $alias = str_replace( ':', '-', Factory::getApplication()->input->getString( 'slg', '' ) );		
		return strip_tags( stripslashes( $alias ) );				 
	}

	public static function canUserEdit( $item ) {
		$permission = false;
		$user       = Factory::getUser();

		if ( $user->authorise( 'core.edit', 'com_allvideoshare' ) ) {
			$permission = true;
		} else {
			if ( isset( $item->created_by ) ) {
				if ( $user->authorise( 'core.edit.own', 'com_allvideoshare' ) && $item->created_by == $user->id ) {
					$permission = true;
				}
			} else {
				$permission = true;
			}
		}

		return $permission;
	}

	public static function Truncate( $text, $length = 150 ) {
		if ( empty( $length ) )	{
			return $text;
		}

		$text = strip_tags( $text );
		
    	if ( $length > 0 && strlen( $text ) > $length ) {
        	$tmp = substr( $text, 0, $length );
            $tmp = substr( $tmp, 0, strrpos( $tmp, ' ' ) );

            if ( strlen( $tmp ) >= $length - 3 ) {
            	$tmp = substr( $tmp, 0, strrpos( $tmp, ' ' ) );
            }
 
            $text = $tmp . '...';
        }
 
        return $text;		
	}

	public static function getCSSClassNames( $params, $context = 'grid' ) {
		$class = '';

		// Grid
		if ( $context == 'grid' ) {
			$column_no = (int) $params->get( 'cols', 3 );
			
			$class = 'avs-col avs-col-' . $column_no;
			if ( $column_no > 3 ) $class .= ' avs-col-sm-3';
			if ( $column_no > 2 ) $class .= ' avs-col-xs-2';
		}

		// Grid: Related
		if ( $context == 'grid.related' ) {
			$column_no = (int) $params->get( 'related_cols', 0 );
			if ( empty( $column_no ) ) {
				$column_no = (int) $params->get( 'cols', 3 );
			}
			
			$class = 'avs-col avs-col-' . $column_no;
			if ( $column_no > 3 ) $class .= ' avs-col-sm-3';
			if ( $column_no > 2 ) $class .= ' avs-col-xs-2';
		}

		// Popup
		if ( $context == 'popup' ) {
			if ( $params->get( 'popup' ) ) {
				$class = ' avs-popup';
			}
		}

		if ( $context == 'slick.popup' ) {
			if ( $params->get( 'popup' ) ) {
				$class = ' avs-slick-popup';
			}
		}

		return $class;
	}

	public static function isSEF() {
		$route = Route::_( 'index.php?option=com_allvideoshare' );

		if ( strpos( $route, 'index.php?option=com_allvideoshare' ) !== false ) {
			return false;
		}

		return true;
	}	
	
}