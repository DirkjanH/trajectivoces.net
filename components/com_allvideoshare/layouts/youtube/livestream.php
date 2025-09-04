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
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeHelper;

$params = $displayData['params'];
$uid    = isset( $displayData['uid'] ) ? $displayData['uid'] : 0;

$jsonArray = array(
    'uid' => $uid
);
?>

<div id="avs-youtube-layout-livestream-<?php echo $uid; ?>" class="avs avs-youtube avs-youtube-layout-livestream" data-params='<?php echo json_encode( $jsonArray ); ?>'>
    <!-- Player -->
    <div class="avs-youtube-player mb-4">
        <div class="avs-player" style="padding-bottom: <?php echo (float) $params->get( 'player_ratio', 56.25 ); ?>%;">
            <iframe id="avs-player-<?php echo $uid; ?>" style="display: none;" width="100%" height="100%" src="<?php echo AllVideoShareYouTubeHelper::getPlayerUrl( $displayData['info'], $params ); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    
    <!-- Fallback Message -->
    <div class="avs-youtube-fallback-message alert alert-info mb-4" style="display: none;">
        <?php echo Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_LIVESTREAM_FALLBACK_MESSAGE' ); ?>
    </div>
</div>