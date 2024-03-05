<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeHelper;

$params = $displayData['params'];
$uid    = isset( $displayData['uid'] ) ? $displayData['uid'] : 0;
$videos = isset( $displayData['info']->videos ) ? $displayData['info']->videos : array();

if ( empty( $videos ) ) {
    return false;
}

$featured = $videos[0]; // Featured Video

$jsonArray = array(
    'uid'  => $uid,
    'loop' => (int) $params->get( 'loop' )
);
?>

<div id="avs-youtube-layout-single-<?php echo $uid; ?>" class="avs avs-youtube avs-youtube-layout-single" data-params='<?php echo json_encode( $jsonArray ); ?>'>
    <!-- Player -->
    <div class="avs-youtube-player mb-4">
        <div class="avs-player" style="padding-bottom: <?php echo (float) $params->get( 'player_ratio', 56.25 ); ?>%;">
            <iframe id="avs-player-<?php echo (int) $uid; ?>" width="100%" height="100%" src="<?php echo AllVideoShareYouTubeHelper::getPlayerUrl( $featured, $params ); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>

        <div class="avs-player-caption">
            <?php if ( $params->get( 'player_title' ) ) : ?>    
                <h2 class="avs-title mt-3" itemprop="headline"><?php echo $featured->title; ?></h2>  
            <?php endif; ?>

            <?php if ( $params->get( 'player_description' ) ) : ?>  
                <div class="avs-description avs-player-description mt-3"><?php if ( ! empty( $featured->description ) ) echo AllVideoShareYouTubeHelper::getVideoDescription( $featured ); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
