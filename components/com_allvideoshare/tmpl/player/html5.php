<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

$app = Factory::getApplication();

// Player Settings
$settings = array();

$settings['plyr'] = array(
    'volume'     => (int) $this->params->get( 'volume' ) / 100,
    'resetOnEnd' => true
);

if ( $this->params->get( 'hotkeys', 1 ) ) {
    $settings['plyr']['keyboard'] = array(
        'focused' => true,
        'global'  => false
    );
}

$controls = array();

if ( $this->params->get( 'playlarge', 1 ) ) {
    $controls[] = 'play-large';
}

if ( $this->params->get( 'controlbar' ) ) {
    if ( $this->params->get( 'rewind', 1 ) ) {
        $controls[] = 'rewind';
    }   

    if ( $this->params->get( 'play', 1 ) ) {
        $controls[] = 'play';
    }    

    if ( $this->params->get( 'fastforward', 1 ) ) {
        $controls[] = 'fast-forward';
    }  

    if ( $this->params->get( 'currenttime' ) ) {
        $controls[] = 'current-time';
    }

    if ( $this->params->get( 'progress', 1 ) ) {
        $controls[] = 'progress';
    }

    if ( $this->params->get( 'duration' ) ) {
        $controls[] = 'duration';
    }    

    if ( $this->params->get( 'volumectrl', 1 ) ) {
        $controls[] = 'mute';
        $controls[] = 'volume';
    }

    if ( $this->hasCaptions() ) {
        $controls[] = 'captions';
    }

    if ( $this->hasSettingsMenu() ) {
        $controls[] = 'settings';
    }

    if ( $this->params->get( 'pip' ) ) {
        $controls[] = 'pip';
    }

    $controls[] = 'airplay';

    if ( $this->hasDownload() ) {
        $controls[] = 'download';
    }

    if ( $this->params->get( 'fullscreen' ) ) {
        $controls[] = 'fullscreen';
    }
}

$settings['plyr']['controls'] = $controls;

if ( $this->hasSettingsMenu() ) {
    $settings['plyr']['settings'] = array();
}

if ( $this->hasQualitySwitcher() ) {
    $settings['plyr']['settings'][] = 'quality';
}

if ( $this->hasCaptions() ) {
    $settings['plyr']['settings'][] = 'captions';
}

if ( $this->params->get( 'speed', 1 ) ) {
    $settings['plyr']['settings'][] = 'speed';
}

if ( $this->hasAds() ) {
    $settings['plyr']['ads'] = array(
        'enabled' => true,
        'tagUrl'  => $this->params->get( 'adtagurl' )
    );   
}

$settings['custom'] = array(
    'siteURL'      => URI::root(),    
    'uid'          => $app->input->getInt( 'uid', 0 ),
    'videoId'      => $this->item->id,
    'autoAdvance'  => $app->input->getInt( 'autoadvance', 0 ),    
    'licenseKey'   => $this->params->get( 'licensekey' ),
    'hideLOGO'     => $this->params->get( 'displaylogo', 1 ) ? false : true,
    'logoImage'    => $this->params->get( 'logo' ),
    'logoPosition' => $this->params->get( 'logoposition' ),
    'logoOpacity'  => (int) $this->params->get( 'logoalpha' ) / 100,
    'logoClickURL' => empty( $this->params->get( 'licensekey' ) ) ? 'https://allvideoshare.mrvinoth.com' : $this->params->get( 'logotarget' )
);

if ( $this->item->type == 'hls' ) {
    $settings['custom']['hls'] = $this->item->hls;

    $settings['plyr']['captions'] = array(
        'active'   => false,
        'language' => 'auto',
        'update'   => true
    ); 
}

if ( $this->item->type == 'dash' ) {
    $settings['custom']['dash'] = $this->item->dash;

    $settings['plyr']['captions'] = array(
        'active'   => false,
        'language' => 'auto',
        'update'   => true
    ); 
}

if ( $this->item->id > 0 ) {
    // Embed
    if ( $this->params->get( 'embed' ) ) {
        $settings['custom']['embed'] = array(
            'labels'  => array(
                'title'  => Text::_( 'COM_ALLVIDEOSHARE_PLAYER_EMBED_TITLE' ),
                'copy'   => Text::_( 'COM_ALLVIDEOSHARE_PLAYER_EMBED_BUTTON_LABEL_COPY' ),
                'copied' => Text::_( 'COM_ALLVIDEOSHARE_PLAYER_EMBED_BUTTON_LABEL_COPIED' )
            ),
            'code'    => htmlspecialchars( '<div style="position:relative;padding-bottom:' . $this->params->get( 'player_ratio', 56.25 ) . '%;height:0;overflow:hidden;"><iframe style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow:hidden" frameborder="0" type="text/html" src="' . URI::root() . 'index.php?option=com_allvideoshare&view=player&id=' . $this->item->id . '&format=raw" width="100%" height="100%" allowfullscreen allow="autoplay"></iframe></div>' )
        );
    }
        
    // Share
    if ( $this->params->get( 'share' ) ) {
        $settings['custom']['share'] = array(
            'labels'    => array(
                'title' => Text::_( 'COM_ALLVIDEOSHARE_PLAYER_SHARE_TITLE' )
            ),
            'facebook'  => array(
                'icon' => URI::root() . 'media/com_allvideoshare/images/facebook.png',
                'url'  => 'https://www.facebook.com/sharer.php?u='. urlencode( $this->getURL() )
            ),
            'twitter'   => array(
                'icon' => URI::root() . 'media/com_allvideoshare/images/twitter.png',
                'url'  => 'https://twitter.com/share?url='. urlencode( $this->getURL() ) .'&text='. urlencode( $this->item->title )
            ),
            'linkedin'  => array(
                'icon' => URI::root() . 'media/com_allvideoshare/images/linkedin.png',
                'url'  => 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode( $this->getURL() ) . '&title=' . urlencode( $this->item->title )
            ),
            'pinterest' => array(
                'icon' => URI::root() . 'media/com_allvideoshare/images/pinterest.png',
                'url'  => 'https://pinterest.com/pin/create/bookmarklet/?media='. urlencode( $this->item->thumb ) .'&url='. urlencode( $this->getURL() ) . '&is_video=0&description='. rawurlencode( $this->item->title )
            )
        );
    }
}

// Video Attributes
$attributes = array(
    'style'        => 'width: 100%; height: 100%;',    
    'playsinline'  => '',
    'controls'     => '', 
    'controlsList' => 'nodownload'
);

$settings['custom']['autoplay'] = 0;
if ( $this->params->get( 'autoplay' ) == 1 ) {
    $attributes['autoplay'] = '';
    $settings['custom']['autoplay'] = 1;
}

$settings['custom']['loop'] = 0;
if ( $this->params->get( 'loop' ) == 1 ) {
    $attributes['loop'] = '';
    $settings['custom']['loop'] = 1;
}

$settings['custom']['muted'] = 0;
if ( $this->params->get( 'muted' ) == 1 ) {
    $attributes['muted'] = '';
    $settings['custom']['muted'] = 1;
}

if ( ! empty( $this->item->thumb ) ) {
    $attributes['data-poster'] = $this->item->thumb;
}

$_attributes = array();

foreach ( $attributes as $key => $value ) {
    if ( '' === $value ) {
        $_attributes[] = $key;
    } else {
        $_attributes[] = sprintf( '%s="%s"', $key, $value );
    }
}

$attributes = implode( ' ', $_attributes );
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <link rel="stylesheet" href="<?php echo URI::root(); ?>media/com_allvideoshare/player/plyr.css?v=3.7.8" />
	<style type="text/css">
        * {
            font-family: Verdana, sans-serif;
        }
        
        html, 
        body {
            width: 100% !important;
            height: 100% !important;
            margin:0 !important; 
            padding:0 !important; 
            overflow: hidden;
        }

        .plyr {
            width: 100%;
            height: 100%;
        }

        .plyr__video-wrapper {
            position: absolute;
        }

        .plyr__ads .plyr__control--overlaid {
            z-index: 999;
        }

        .plyr__cues {
            visibility: hidden;
        }
            
        .plyr__share,
        .plyr__embed {
            width: 100%;
            height: 100%;
            pointer-events: none;
            opacity: 0;
            -webkit-transition: opacity .5s;
               -moz-transition: opacity .5s;
                -ms-transition: opacity .5s;
                 -o-transition: opacity .5s;
                    transition: opacity .5s;
        }

        .plyr__share.fadein,
        .plyr__embed.fadein {
            pointer-events: auto;
            opacity: 1;
        }

        #plyr__embed-button,
        #plyr__share-button {
			position: absolute;
            width: 35px;
			height: 35px;			
			right: 15px;            
			background-color: rgba( 0, 0, 0, 0.5 );	
            background-position: center;		
			background-repeat: no-repeat; 
            border-radius: 2px;	
            cursor:pointer;			
			z-index: 1;	
		} 
        
        #plyr__embed-button:hover,
        #plyr__share-button:hover {        
			background-color: #00B3FF;	
		}      

        #plyr__embed-button {
            top: 15px;
            background-image: url( '<?php echo URI::root(); ?>media/com_allvideoshare/images/embed.png' );
        }

        #plyr__share-button {
            background-image: url( '<?php echo URI::root(); ?>media/com_allvideoshare/images/share.png' );
        }

        #plyr__embed-close-button,
        #plyr__share-close-button {
            position: absolute;
            width: 35px;
			height: 35px;
            top: 15px;
			right: 15px;
			background-image: url( '<?php echo URI::root(); ?>media/com_allvideoshare/images/close.png' );
			background-position: center;			
			background-repeat: no-repeat;			
			cursor: pointer;	
            z-index: 9;		
		}

        #plyr__embed-box,
        #plyr__share-box {
            position: absolute;		
            width: 100%;
			height: 100%;
            top: 0;
			left: 0;
			background-color: #000;
			overflow: hidden;
			z-index: 999;		
		}

        #plyr__embed-box-inner,
        #plyr__share-box-inner {            
			display: -webkit-box;
			display: -moz-box;
			display: -ms-flexbox;
			display: -webkit-flex;
			display: flex;
            width: 100%;
			height: 100%;
			align-items: center;
			justify-content: center;
			text-align:center;	
		}

        #plyr__embed-content,
		#plyr__embed-title,
		#plyr__embed-code,
        #plyr__share-content,
        #plyr__share-title {
			width: 100%;
		}

        #plyr__embed-content,
        #plyr__share-content {
			margin: 10px;
            max-width: 640px;
		}        

        #plyr__embed-title,
        #plyr__share-title {
            color: #EEE;
            font-size: 12px;
            text-transform: uppercase;
        }

		#plyr__embed-code {
            margin: 10px 0;
            padding: 3px;
            border: 1px solid #FFF;            
            color: #666;
            line-height: 1.5;
            resize: none;
		}

        #plyr__embed-code:focus {
            outline: 0;
        }

        #plyr__embed-copy-button {
			display: block;
            width: 75px;
            margin: 0 auto;
			padding: 7px 0;
			background: #00B3FF;
			border-radius: 2px;			
			color: #FFF;
			font-size: 11px;
			font-weight: bold;
			text-align: center;
            text-transform: uppercase;
            opacity: 0.9;
			cursor: pointer;
		}
		
		#plyr__embed-copy-button:hover {
            opacity: 1;
		}

        #plyr__share-icons {
            margin: 10px 0;
        }

        #plyr__share-icons a {
            display: inline-block;
            padding: 5px;
            -webkit-transition: -webkit-transform .5s ease-in-out;
            transition: transform .5s ease-in-out;
        } 

        #plyr__share-icons a:hover {
		    -webkit-transform: rotate( 360deg );
		    transform: rotate( 360deg );
		} 
		
		.contextmenu {
            position: absolute;
            top: 0;
            left: 0;
            margin: 0;
            padding: 0;
            background: rgba( 0, 0, 0, 0.5 );
			border-radius: 2px;
            z-index: 9999999999; /* make sure it shows on fullscreen */
        }
        
        .contextmenu-item {
            margin: 0;
            padding: 8px 12px;
            font-size: 12px;
            color: #FFF;		
            white-space: nowrap;
            cursor: pointer;
        }
    </style>    

    <?php
    if ( ! empty( $this->params->get( 'custom_css' ) ) ) {
        printf( '<style type="text/css">%s</style>', $this->params->get( 'custom_css' ) );
    }
    ?>
</head>
<body>
    <?php
    switch ( $this->item->type ) {
        case 'youtube':
            $source = sprintf(
                'https://www.youtube.com/embed/%s?iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1',
                AllVideoShareHelper::getYouTubeVideoId( $this->item->youtube )
            );

            if ( ! empty( $settings['custom']['autoplay'] ) ) {
				$source .= '&amp;autoplay=1';
			}
			
			if ( ! empty( $settings['custom']['loop'] ) ) {
				$source .= '&amp;loop=1';
			}

            if ( ! empty( $settings['custom']['muted'] ) ) {
				$source .= '&amp;muted=1';
			}

            printf(
                '<div id="player" class="plyr__video-embed" %s><iframe width="560" height="315" src="%s" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>',
                $attributes,
                $source
            );
            break;
        case 'vimeo':
            $videoId = '';

            // Get image using the standard OEmbed API
            if ( function_exists( 'file_get_contents' ) ) {
                $oembed = json_decode( file_get_contents( "https://vimeo.com/api/oembed.json?url={$this->item->vimeo}" ) );
                if ( $oembed ) {
                    if ( isset( $oembed->video_id ) ) {
                        $videoId = $oembed->video_id;
                    }
                }
            }

            // Fallback to our old method to get the Vimeo ID
            if ( empty( $videoId ) ) {			
                $videoId = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $this->item->vimeo, '/' ) );
            }
            
            $source = sprintf(
                'https://player.vimeo.com/video/%s?byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media',
                $videoId
            );

            if ( ! empty( $settings['custom']['autoplay'] ) ) {
				$source .= '&amp;autoplay=1';
			}
			
			if ( ! empty( $settings['custom']['loop'] ) ) {
				$source .= '&amp;loop=1';
			}

            if ( ! empty( $settings['custom']['muted'] ) ) {
				$source .= '&amp;muted=1';
			}

            printf(
                '<div id="player" class="plyr__video-embed" %s><iframe width="560" height="315" src="%s" frameborder="0" scrolling="no" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>',
                $attributes,
                $source
            );
            break;
        case 'hls':
        case 'dash':
            printf(
                '<video id="player" %s></video>',
                $attributes
            );
            break;
        default:
            $sources = array();

            // SD
            $parsed = $this->getFileInfo( $this->item->video, 'SD' );

            $sources[] = sprintf(
                '<source type="video/%s" src="%s" size="%d" />',
                $parsed['ext'],
                $this->item->video,
                $parsed['quality']                
            );
            
            // HD
            if ( $this->hasQualitySwitcher() ) {
                $parsed = $this->getFileInfo( $this->item->hd, 'HD' );
                
                $sources[] = sprintf(
                    '<source type="video/%s" src="%s" size="%d" />',
                    $parsed['ext'],
                    $this->item->hd,
                    $parsed['quality']                
                );
            }

            if ( ! empty( $sources ) ) {
                echo '<video id="player" ' . $attributes . '>';
                echo implode( '', $sources );

                if ( $this->hasCaptions() ) {
                    foreach ( $this->item->captions as $caption ) {
                        printf(
                            '<track kind="captions" src="%s" label="%s" srclang="%s" />',
                            $caption['src'],
                            $caption['label'],
                            $caption['srclang']
                        );
                    }
                }

                echo '</video>';
            }
    }        
    ?>

    <div id="contextmenu" class="contextmenu" style="display: none;">
        <div class="contextmenu-item">
            <?php
            if ( $this->canDo ) {
                echo $app->get( 'sitename' );                               
            } else {
                echo 'A Joomla Video Gallery'; 
            }
            ?>
        </div>
    </div>

    <script src="<?php echo URI::root(); ?>media/com_allvideoshare/player/plyr.js?v=3.7.8" type="text/javascript"></script>
	<script src="<?php echo URI::root(); ?>media/com_allvideoshare/player/plyr.polyfilled.js?v=3.7.8" type="text/javascript"></script>
    
    <?php if ( $this->item->type == 'hls' ) : ?>
        <script src="<?php echo URI::root(); ?>media/com_allvideoshare/player/hls.min.js?v=1.4.3" type="text/javascript"></script>
    <?php endif; ?>

    <?php if ( $this->item->type == 'dash' ) : ?>
        <script src="<?php echo URI::root(); ?>media/com_allvideoshare/player/dash.all.min.js" type="text/javascript"></script>
    <?php endif; ?>

    <script type="text/javascript">
		(function() {
			'use strict';
            
            // Vars
            var settings = <?php echo json_encode( $settings['custom'] ); ?>;
            var lastState = '';

            /**
			 * Helper Functions
			 */  

            function updateViewsCount() {
                var xmlhttp;

                if ( window.XMLHttpRequest ) {
                    xmlhttp = new XMLHttpRequest();
                } else {
                    xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
                };
                
                xmlhttp.onreadystatechange = function() {				
                    if ( 4 == xmlhttp.readyState && 200 == xmlhttp.status ) {					
                        if ( xmlhttp.responseText ) {
                            // Do nothing
                        }						
                    }					
                };	

                xmlhttp.open( 'GET', '<?php echo URI::root(); ?>index.php?option=com_allvideoshare&task=video.views&id=' + settings.videoId, true );
                xmlhttp.send();							
            }

            function copyEmbedCode() {
                document.getElementById( 'plyr__embed-code' ).select();
                document.execCommand( 'copy' );  

                document.getElementById( 'plyr__embed-copy-button' ).textContent = settings.embed.labels.copied;

                setTimeout(function() {
                    document.getElementById( 'plyr__embed-copy-button' ).textContent = settings.embed.labels.copy;
                }, 2000 );	 
            }

            function fadeIn( $elem ) {
                $elem.className += ' fadein';
            }

            function fadeOut( $elem ) {
                $elem.className = $elem.className.replace( ' fadein', '' );
            }

			/**
			 * Initialize the player
			 */  
            var video = document.getElementById( 'player' ); 
            const player = new Plyr( video, <?php echo json_encode( $settings['plyr'] ); ?> );

            // Ads
            player.on( 'ready', () => {
                if ( player.hasOwnProperty( 'ads' ) ) {
                    var adsLoaded = false;

                    player.ads.on( 'loaded', () => {
                        if ( adsLoaded ) {
                            return;
                        }

                        adsLoaded = true;                        

                        var adsManager = player.ads.manager;

                        var adPlayBtn = document.createElement( 'button' );
                        adPlayBtn.type = 'button';
                        adPlayBtn.className = 'plyr__control plyr__control--overlaid';
                        adPlayBtn.style.display = 'none';
                        adPlayBtn.innerHTML = '<svg aria-hidden="true" focusable="false"><use xlink:href="#plyr-play"></use></svg>';
                        
                        document.getElementsByClassName( 'plyr__ads' )[0].appendChild( adPlayBtn );                        

                        adsManager.addEventListener( google.ima.AdEvent.Type.PAUSED, function() {
                            adPlayBtn.style.display = '';
                        });

                        adPlayBtn.addEventListener( 'click', function() {
                            adPlayBtn.style.display = 'none';
                            adsManager.resume();
                        });
                    });
                }
            });

            var viewsCountUpdated = false;
            player.on( 'playing', function() {
                if ( ! viewsCountUpdated ) {
                    viewsCountUpdated = true;

                    if ( settings.videoId > 0 ) {
                        updateViewsCount();
                    }
                }
			});

            // HLS
            if ( settings.hls ) {
                const hls = new Hls();
                hls.loadSource( settings.hls );
                hls.attachMedia( video );
                window.hls = hls;
                
                // Handle changing captions
                player.on( 'languagechange', () => {
                    setTimeout( () => hls.subtitleTrack = player.currentTrack, 50 );
                });
            }

            // MPEG-DASH
            if ( settings.dash ) {
                const dash = dashjs.MediaPlayer().create();
                dash.initialize( video, settings.dash, true );
                window.dash = dash;
            }

             // AutoAdvance
			if ( settings.autoAdvance ) {
				player.on( 'ended', function() {
					parent.postMessage(
						{ 				
							message: 'ON_ALLVIDEOSHARE_ENDED',			
							id: settings.uid,
                            loop: settings.loop
						},
						'*'
					); 
			   });
			}

            // Embed
            if ( settings.embed ) {	
                var embedLayer = document.createElement( 'div' );
                embedLayer.className = 'plyr__embed';
                embedLayer.innerHTML = '<div id="plyr__embed-button"></div><div id="plyr__embed-box" style="display: none;"><div id="plyr__embed-box-inner"><div id="plyr__embed-close-button"></div><div id="plyr__embed-content"><div id="plyr__embed-title">' + settings.embed.labels.title + '</div><input type="text" id="plyr__embed-code" value="' + settings.embed.code + '" readonly /><div id="plyr__embed-copy-button">' + settings.embed.labels.copy + '</div></div></div></div>';

                document.getElementsByClassName( 'plyr' )[0].appendChild( embedLayer );

                // Show or Hide
                var embedLocked = false;

                player.on( 'controlsshown', () => {
                    if ( ! embedLocked ) {
                        fadeIn( embedLayer );
                    }                            
                });
                
                player.on( 'controlshidden', () => {
                    if ( ! embedLocked ) {
                        fadeOut( embedLayer );
                    }
                });

                // Open
                document.getElementById( 'plyr__embed-button' ).addEventListener( 'click',  function() {
                    embedLocked = true;

                    if ( player.playing ) {
                        lastState = 'playing';
                        player.pause();
                    } else {
                        lastState = '';
                    }                    

                    document.getElementById( 'plyr__embed-button' ).style.display = 'none';						
                    document.getElementById( 'plyr__embed-box' ).style.display    = '';	
                });

                // Close
                document.getElementById( 'plyr__embed-close-button' ).addEventListener( 'click',  function() {
                    embedLocked = false;

                    if ( lastState == 'playing' ) {
                        player.play();
                    }

                    document.getElementById( 'plyr__embed-box' ).style.display    = 'none';
                    document.getElementById( 'plyr__embed-button' ).style.display = '';                            	
                });

                // Copy               
                document.getElementById( 'plyr__embed-code' ).addEventListener( 'focus', copyEmbedCode );
                document.getElementById( 'plyr__embed-copy-button' ).addEventListener( 'click', copyEmbedCode );
            }

            // Share
            if ( settings.share ) {
                var shareLayer = document.createElement( 'div' );
                shareLayer.className = 'plyr__share';
                shareLayer.innerHTML = '<div id="plyr__share-button" style="top: ' + ( settings.embed ? '55px' : '15px' ) + ';"></div><div id="plyr__share-box" style="display: none;"><div id="plyr__share-box-inner"><div id="plyr__share-close-button"></div><div id="plyr__share-content"><div id="plyr__share-title">' + settings.share.labels.title + '</div><div id="plyr__share-icons"><a href="' + settings.share.facebook.url + '" target="_blank" class="share-facebook"><span><img src="' + settings.share.facebook.icon + '" /></span></a><a href="' + settings.share.twitter.url + '" target="_blank" class="share-twitter"><span><img src="' + settings.share.twitter.icon + '" /></span></a><a href="' + settings.share.linkedin.url + '" target="_blank" class="share-linkedin"><span><img src="' + settings.share.linkedin.icon + '" /></span></a><a href="' + settings.share.pinterest.url + '" target="_blank" class="share-pinterest"><span><img src="' + settings.share.pinterest.icon + '" /></span></a></div></div></div></div>';

                document.getElementsByClassName( 'plyr' )[0].appendChild( shareLayer );

                // Show or Hide
                var shareLocked = false;

                player.on( 'controlsshown',  () => {
                    if ( ! shareLocked ) {
                        fadeIn( shareLayer );
                    }                            
                });
                
                player.on( 'controlshidden',  () => {
                    if ( ! shareLocked ) {
                        fadeOut( shareLayer );
                    }
                });

                // Open
                document.getElementById( 'plyr__share-button' ).addEventListener( 'click',  function() {
                    shareLocked = true;

                    if ( player.playing ) {
                        lastState = 'playing';
                        player.pause();
                    } else {
                        lastState = '';
                    }                    

                    document.getElementById( 'plyr__share-button' ).style.display = 'none';						
                    document.getElementById( 'plyr__share-box' ).style.display    = '';	
                });

                // Close
                document.getElementById( 'plyr__share-close-button' ).addEventListener( 'click',  function() {
                    shareLocked = false;

                    if ( lastState == 'playing' ) {
                        player.play();
                    }
                    
                    document.getElementById( 'plyr__share-box' ).style.display    = 'none';
                    document.getElementById( 'plyr__share-button' ).style.display = '';                            	
                });
            }

            // Custom ContextMenu
            var contextmenu = document.getElementById( 'contextmenu' );
            var timeout_handler = '';
            
            document.addEventListener( 'contextmenu', function( e ) {                    
                if ( 3 === e.keyCode || 3 === e.which ) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var width = contextmenu.offsetWidth,
                        height = contextmenu.offsetHeight,
                        x = e.pageX,
                        y = e.pageY,
                        doc = document.documentElement,
                        scrollLeft = ( window.pageXOffset || doc.scrollLeft ) - ( doc.clientLeft || 0 ),
                        scrollTop = ( window.pageYOffset || doc.scrollTop ) - ( doc.clientTop || 0 ),
                        left = x + width > window.innerWidth + scrollLeft ? x - width : x,
                        top = y + height > window.innerHeight + scrollTop ? y - height : y;
            
                    contextmenu.style.display = '';
                    contextmenu.style.left = left + 'px';
                    contextmenu.style.top = top + 'px';
                    
                    clearTimeout( timeout_handler );
                    timeout_handler = setTimeout(function() {
                        contextmenu.style.display = 'none';
                    }, 1500 );				
                }                                                     
            });
            
            if ( '' != settings.logoClickURL ) {
                contextmenu.addEventListener( 'click', function() {
                    top.window.location.href = settings.logoClickURL;
                });
            }
            
            document.addEventListener( 'click', function() {
                contextmenu.style.display = 'none';								 
            });

            // Dispatch an event
			var evt = document.createEvent( 'CustomEvent' );
			evt.initCustomEvent( 'player.init', false, false, { player: player, settings: settings } );
			window.dispatchEvent( evt );           
		})();
    </script>
</body>
</html>