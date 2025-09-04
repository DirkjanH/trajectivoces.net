<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\View\Player;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\AbstractView;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Component\Content\Site\Helper\RouteHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

/**
 * Frontpage View class
 *
 * @since  4.1.0
 */
class RawView extends AbstractView {

	protected $state;

	protected $item;	

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function display( $tpl = null ) {
		$app = Factory::getApplication();
		$user = Factory::getUser();

		$this->state  = $this->get( 'State' );
		$this->params = $this->get( 'Params' );
		$this->item   = $this->get( 'Item' ); 

		if ( empty( $this->item ) ) {
            return false;
        }	

		if ( ! empty( $this->item->access ) && ! in_array( $this->item->access, $user->getAuthorisedViewLevels() ) ) {
            require JPATH_ROOT . '/components/com_allvideoshare/tmpl/player/access.php';
			return false;
        }  

		if ( $this->params->get( 'show_gdpr_consent' ) && $app->input->cookie->get( 'com_allvideoshare_gdpr', null ) == null ) {
			if ( in_array( $this->item->type, array( 'youtube', 'vimeo', 'thirdparty' ) ) ) {
				require JPATH_ROOT . '/components/com_allvideoshare/tmpl/player/gdpr.php';
				return false;
			}
		}

		if ( $this->item->type == 'thirdparty' ) {
			require JPATH_ROOT . '/components/com_allvideoshare/tmpl/player/iframe.php';
		} else {
			$this->canDo = AllVideoShareHelper::canDo(); 		

			// Fallback to old versions that doesn't have the youtube field
			if ( $this->item->type == 'youtube' ) {
				if ( empty( $this->item->youtube ) && ! empty( $this->item->video ) ) {
					if ( false !== strpos( $this->item->video, 'youtube.com' ) || false !== strpos( $this->item->video, 'youtu.be' ) ) {
						$this->item->youtube = $this->item->video;
					}
				}
			}

			// Fallback to old versions that doesn't have the vimeo field
			if ( $this->item->type == 'vimeo' ) {
				if ( empty( $this->item->vimeo ) && ! empty( $this->item->video ) ) {
					if ( false !== strpos( $this->item->video, 'vimeo.com' ) ) {
						$this->item->vimeo = $this->item->video;
					}
				}
			}

			// HLS / MPEG-DASH
			if ( $this->item->type == 'hls' ) {
				if ( strpos( $this->item->hls, '.mpd' ) !== false ) {
					$this->item->type = 'dash';
					$this->item->dash = $this->item->hls;
				}
			}		

			// Captions
			$tracks = array();

			if ( ! empty( $this->item->captions ) && $captions = json_decode( $this->item->captions ) ) {
				foreach ( $captions as $caption ) {
					if ( empty( $caption->src ) ) {
						continue;
					}

					$tracks[] = array(
						'src'     => $caption->src,
						'label'   => ( ! empty( $caption->label ) ? $caption->label : Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_EMPTY_LABEL' ) ),
						'srclang' => ( ! empty( $caption->srclang ) ? $caption->srclang : 'unknown' )
					);
				}
			}

			$this->item->captions = $tracks;

			require JPATH_ROOT . '/components/com_allvideoshare/tmpl/player/html5.php';
		}		
	}

	public function getTitle() {	
		$app = Factory::getApplication();

		$title = $this->item->title;

		if ( $this->item->id > 0 ) {
			if ( $app->get( 'sitename_pagetitles', 0 ) == 1 ) {
				$title = Text::sprintf( 'JPAGETITLE', $app->get( 'sitename' ), $title );
			} elseif ( $app->get( 'sitename_pagetitles', 0 ) == 2 ) {
				$title = Text::sprintf( 'JPAGETITLE', $title, $app->get( 'sitename' ) );
			}
		}
	
		return $title;
    }

	public function getURL() {
		if ( $this->item->id > 0 ) {
        	return Route::_( 'index.php?option=com_allvideoshare&view=video&slg=' . $this->item->slug, true, 0, true );	
		}

		return Uri::root();
    }

	public function hasSettingsMenu() {
		if ( $this->hasQualitySwitcher() || $this->hasCaptions() || $this->params->get( 'speed', 1 ) ) {
			return true;
		}

		return false;
	}

	public function hasCaptions() {	
		if ( $this->params->get( 'captions', 1 ) ) {
			if ( in_array( $this->item->type, array( 'general', 'url', 'upload' ) ) && ! empty( $this->item->captions ) ) {
				return true;
			}

			if ( in_array( $this->item->type, array( 'hls', 'dash' ) ) ) {
				return true;
			}
		}
		
		return false;		
    }

	public function hasQualitySwitcher() {	
		if ( $this->params->get( 'quality' ) ) {
			if ( in_array( $this->item->type, array( 'general', 'url', 'upload' ) ) && ! empty( $this->item->hd ) ) {
				return true;
			}
		}
		
		return false;		
    }

	public function hasDownload() {	
		if ( $this->params->get( 'download', 1 ) ) {
			if ( in_array( $this->item->type, array( 'general', 'url', 'upload' ) ) ) {
				return true;
			}
		}
		
		return false;		
    }

	public function getFileInfo( $url, $format = 'SD' ) {
		$result = array(
			'ext'     => 'mp4',
			'quality' =>  ''
		);

        $parse_url = parse_url( $url );

		// Parse extension
        $ext = pathinfo( $parse_url['path'], PATHINFO_EXTENSION );
        if ( in_array( $ext, array( 'webm', 'ogv' ) ) ) {
			$result['ext'] = $ext;
		}

		// Parse quality
		$filename = pathinfo( $parse_url['path'], PATHINFO_FILENAME );
		$filename = strtolower( $filename );

		$quality_string = '';
		if ( strpos( $filename, '-' ) !== false ) {
			$parts = explode( '-', $filename );
			$quality_string = end( $parts );
		} elseif ( strpos( $filename, '_' ) !== false ) {
			$parts = explode( '_', $filename );
			$quality_string = end( $parts );
		}

		$result['quality'] = (int) $quality_string;
		if ( $quality_string != $result['quality'] . 'p' ) {
			$result['quality'] = ( $format == 'SD' ) ? 480 : 1080;
		};
        
        return $result;        
    }

	public function hasAds() {
		if ( ! $this->canDo ) {
			$this->params->set( 'adsource', 'custom' );
		}

		if ( $this->params->get( 'adsource' ) == 'vast' ) {
			$adtagurl = $this->params->get( 'adtagurl' );

            if ( $adtagurl != '' ) {
				$excerpt = '';

				if ( ! empty( $this->item->description ) ) {
					$excerpt = $this->item->metadescription;
				}
				
				if ( empty( $excerpt ) && ! empty( $this->item->description ) ) {
					$excerpt = AllVideoShareHelper::Truncate( $this->item->description );
					$excerpt = str_replace( '...', '', $excerpt );
				}

				$placeholders = array(
					'[domain]'        => URI::root(),
					'[video_id]'      => $this->item->id,
					'[video_title]'   => $this->item->title,
					'[video_excerpt]' => $excerpt,
					'[ip_address]'    =>  $this->getIpAddress()
				);

				$adtagurl = strtr( $adtagurl, $placeholders );

				$this->params->set( 'adtagurl', $adtagurl );
                return true;
            }
        } else {
			$hasPreroll  = $this->params->get( 'preroll' );
			$hasPostroll = $this->params->get( 'postroll' );

			if ( $hasPreroll == 1 || $hasPostroll == 1 ) {
				$lang = Factory::getLanguage();
				$locales = $lang->getLocale();

				$adtagurl  = URI::root() . 'index.php?option=com_allvideoshare&view=ads&type=vmap';
				if ( $hasPreroll ) $adtagurl .= '&preroll=1';
				if ( $hasPostroll ) $adtagurl .= '&postroll=1';
				$adtagurl .= '&format=xml&lang=' . $locales[4];

				$this->params->set( 'adtagurl', $adtagurl );
				return true;
			}
        }

        return false;
    }

	public function getIpAddress() {
        // Whether ip is from share internet
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        
        // Whether ip is from proxy
        elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        
        // Whether ip is from remote address
        else {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip_address;        
    }

}
