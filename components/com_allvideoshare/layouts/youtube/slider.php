<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
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

$language = Factory::getLanguage();
$columns  = (int) $params->get( 'columns' );

$jsonArray = array(
    'uid'                => $uid,
    'is_rtl'             => $language->get( 'rtl' ),
    'autoplay'           => (int) $params->get( 'autoplay' ),
    'loop'               => (int) $params->get( 'loop' ),
    'controls'           => (int) $params->get( 'controls' ),
    'autoadvance'        => (int) $params->get( 'autoadvance' ),
    'player_title'       => (int) $params->get( 'player_title' ),
    'player_description' => (int) $params->get( 'player_description' ),
    'arrow_size'         => (int) $params->get( 'arrow_size', 24 ) . 'px',
    'arrow_bg_color'     => $params->get( 'arrow_bg_color', '#08c' ),
    'arrow_icon_size'    => ( (int) $params->get( 'arrow_size', 24 ) - 5 ) . 'px',
    'arrow_icon_color'   => $params->get( 'arrow_icon_color', '#fff' ),		
    'arrow_radius'       => (int) $params->get( 'arrow_radius', 12 ) . 'px',
    'arrow_top_offset'   => (int) $params->get( 'arrow_top_offset', 50 ) . '%',
    'arrow_left_offset'  => (int) $params->get( 'arrow_left_offset', -25 ) . 'px',
    'arrow_right_offset' => (int) $params->get( 'arrow_right_offset', -25 ) . 'px',
    'dot_size'           => (int) $params->get( 'dot_size', 24 ) . 'px',
    'dot_color'          => $params->get( 'dot_color', '#08c' )
);

$slickOptions = array(
    'infinite'       => false,
    'slidesToShow'   => $columns,
	'slidesToScroll' => 1,
    'arrows'         => ! empty( $params->get( 'arrows' ) ) ? true : false,
    'dots'           => ! empty( $params->get( 'dots' ) ) ? true : false,
    'responsive'     => array()
);

if ( $columns > 3 ) {
    $slickOptions['responsive'][] = array(
        'breakpoint' => 768,
        'settings'   => array(
            'slidesToShow' => 3
        )
    );
}

if ( $columns > 2 ) {
    $slickOptions['responsive'][] = array(
        'breakpoint' => 480,
        'settings'   => array(
            'slidesToShow' => 2
        )
    );
}
?>

<div id="avs-youtube-layout-slider-<?php echo $uid; ?>" class="avs avs-youtube avs-youtube-layout-slider" data-params='<?php echo json_encode( $jsonArray ); ?>'>
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
    <div class="avs-videos mb-4" data-slick='<?php echo json_encode( $slickOptions ); ?>'>
        <?php foreach ( $videos as $index => $video ) : 
            $classNames = array();
            $classNames[] = 'avs-video';
            $classNames[] = 'avs-video-' . $video->id;
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
</div>