<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeHelper;

$params = $displayData['params'];
$uid    = isset( $displayData['uid'] ) ? $displayData['uid'] : 0;
$videos = isset( $displayData['info']->videos ) ? $displayData['info']->videos : array();

if ( empty( $videos ) ) {
    return false;
}

$featured = $videos[0]; // Featured Video

$jsonArray = array(
    'uid'                => $uid,    
    'autoplay'           => (int) $params->get( 'autoplay' ),
    'loop'               => (int) $params->get( 'loop' ),
    'controls'           => (int) $params->get( 'controls' ),
    'autoadvance'        => (int) $params->get( 'autoadvance' ),
    'player_title'       => (int) $params->get( 'player_title' ),
    'player_description' => (int) $params->get( 'player_description' )
);
?>

<div id="avs-youtube-layout-playlist-<?php echo $uid; ?>" class="avs avs-youtube avs-youtube-layout-playlist" data-params='<?php echo json_encode( $jsonArray ); ?>'> 
    <div class="avs-playlist <?php echo $params->get( 'playlist_position', 'right' ); ?> <?php echo $params->get( 'playlist_color', 'dark' ); ?>">
        <!-- Player -->
        <div class="avs-playlist-player avs-youtube-player">
            <div class="avs-player" style="padding-bottom: <?php echo (float) $params->get( 'player_ratio', 56.25 ); ?>%;">
                <iframe id="avs-player-<?php echo $uid; ?>" width="100%" height="100%" src="<?php echo AllVideoShareYouTubeHelper::getPlayerUrl( $featured, $params ); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>

        <!-- Playlist -->
        <div class="avs-playlist-videos">
            <div class="avs-videos">
                <?php foreach ( $videos as $index => $video ) :
                    $classNames = array();
                    $classNames[] = 'avs-video';
                    $classNames[] = 'avs-video-' . $video->id;

                    if ( $index == 0 ) {
                        $classNames[] = 'avs-active';
                    }
                    ?>
                    <div class="<?php echo implode( ' ', $classNames ); ?>" data-id="<?php echo $video->id; ?>" data-title="<?php echo $video->title; ?>">
                        <div class="d-flex p-2">
                            <div class="flex-shrink-0">
                                <div class="avs-responsive-item" style="padding-bottom: <?php echo (float) $params->get( 'image_ratio', 56.25 ); ?>%">
                                    <img class="avs-image" src="<?php echo AllVideoShareYouTubeHelper::getImageUrl( $video, $params ); ?>" />
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <?php if ( $params->get( 'title' ) ) : ?> 
                                    <div class="avs-title"><?php echo AllVideoShareHelper::Truncate( $video->title, $params->get( 'title_length' ) ); ?></div>
                                <?php endif; ?> 

                                <?php if ( $params->get( 'excerpt' ) && ! empty( $video->description ) ) : ?>
                                    <div class="avs-excerpt small text-muted mt-2"><?php echo AllVideoShareHelper::Truncate( $video->description, $params->get( 'excerpt_length' ) ); ?></div>
                                <?php endif; ?>

                                <?php if ( $params->get( 'player_description' ) ) : ?>  
                                    <div class="avs-description" style="display: none;"><?php if ( ! empty( $video->description ) ) echo AllVideoShareYouTubeHelper::getVideoDescription( $video ); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>	

    <div class="avs-player-caption">
        <?php if ( $params->get( 'player_title' ) ) : ?>    
            <h2 class="avs-title avs-player-title mt-3" itemprop="headline"><?php echo $featured->title; ?></h2>  
        <?php endif; ?>

        <?php if ( $params->get( 'player_description' ) ) : ?>  
            <div class="avs-description avs-player-description mt-3"><?php if ( ! empty( $featured->description ) ) echo AllVideoShareYouTubeHelper::getVideoDescription( $featured ); ?></div>
        <?php endif; ?>
    </div>
</div>