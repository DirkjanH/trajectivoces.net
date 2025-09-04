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
use Joomla\CMS\Layout\LayoutHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeHelper;

$params = $displayData['params'];
$uid    = isset( $displayData['uid'] ) ? $displayData['uid'] : 0;
$videos = isset( $displayData['info']->videos ) ? $displayData['info']->videos : array();

if ( empty( $videos ) ) {
    return false;
}

$featured = $videos[0]; // Featured Video

$columnClass = AllVideoShareHelper::getCSSClassNames( $params );

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

<div id="avs-youtube-layout-classic-<?php echo $uid; ?>" class="avs avs-youtube avs-youtube-layout-classic" data-params='<?php echo json_encode( $jsonArray ); ?>'>
    <!-- Player -->
    <div class="avs-youtube-player mb-4">
        <div class="avs-player" style="padding-bottom: <?php echo (float) $params->get( 'player_ratio', 56.25 ); ?>%;">
            <iframe id="avs-player-<?php echo $uid; ?>" width="100%" height="100%" src="<?php echo AllVideoShareYouTubeHelper::getPlayerUrl( $featured, $params ); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
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

    <!-- Gallery -->
    <div class="avs-videos avs-row mb-4">
        <?php foreach ( $videos as $index => $video ) : 
            $classNames = array();
            $classNames[] = 'avs-video';
            $classNames[] = 'avs-video-' . $video->id;
            $classNames[] = $columnClass;

            if ( $uid != 0 && $index == 0 ) {
                $classNames[] = 'avs-active';
            }
            ?>
            <div class="<?php echo implode( ' ', $classNames ); ?>" data-id="<?php echo $video->id; ?>" data-title="<?php echo $video->title; ?>">
                <div class="mb-3 p-2">
                    <div class="avs-responsive-item" style="padding-bottom: <?php echo (float) $params->get( 'image_ratio', 56.25 ); ?>%">
                        <img class="avs-image" src="<?php echo AllVideoShareYouTubeHelper::getImageUrl( $video, $params ); ?>" />
                        
                        <svg class="avs-svg-icon avs-svg-icon-play" width="32" height="32" viewBox="0 0 32 32">
                            <path d="M16 0c-8.837 0-16 7.163-16 16s7.163 16 16 16 16-7.163 16-16-7.163-16-16-16zM16 29c-7.18 0-13-5.82-13-13s5.82-13 13-13 13 5.82 13 13-5.82 13-13 13zM12 9l12 7-12 7z"></path>
                        </svg>

                        <span class="badge bg-success fw-normal"><?php echo Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_LBL_ACTIVE' ); ?></span>
                    </div>

                    <?php if ( $params->get( 'title' ) ) : ?> 
                        <div class="avs-title mt-2">                     
                            <a href="#"><?php echo AllVideoShareHelper::Truncate( $video->title, $params->get( 'title_length' ) ); ?></a>
                        </div>
                    <?php endif; ?> 

                    <?php if ( $params->get( 'excerpt' ) && ! empty( $video->description ) ) : ?>
                        <div class="avs-excerpt small mt-2"><?php echo AllVideoShareHelper::Truncate( $video->description, $params->get( 'excerpt_length' ) ); ?></div>
                    <?php endif; ?>

                    <?php if ( $params->get( 'player_description' ) ) : ?>  
                        <div class="avs-description" style="display: none;"><?php if ( ! empty( $video->description ) ) echo AllVideoShareYouTubeHelper::getVideoDescription( $video ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php echo LayoutHelper::render( 'youtube.parts.pagination', $displayData, JPATH_SITE . '/components/com_allvideoshare/layouts' ); ?> 
</div>