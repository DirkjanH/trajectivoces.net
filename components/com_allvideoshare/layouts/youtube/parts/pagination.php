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

$params   = $displayData['params'];
$pageInfo = isset( $displayData['info']->pageInfo ) ? $displayData['info']->pageInfo : array();

if ( empty( $params->get( 'pagination' ) ) || empty( $pageInfo ) ) {
    return false;
}

$sourceType = $params->get( 'type' );

$jsonArray = array(
    'type'               => $sourceType,
    'src'                => $params->get( $sourceType ),
    'order'              => $params->get( 'order' ), // Applicable only when type=search
    'per_page'           => (int) $params->get( 'per_page' ),
    'cache'              => (int) $params->get( 'cache' ),
    'layout'             => $params->get( 'layout' ),
    'columns'            => (int) $params->get( 'columns' ),
    'image_ratio'        => (float) $params->get( 'image_ratio' ),
    'title'              => (int) $params->get( 'title' ),
    'title_length'       => (int) $params->get( 'title_length' ),
    'excerpt'            => (int) $params->get( 'excerpt' ),
    'excerpt_length'     => (int) $params->get( 'excerpt_length' ),
    'pagination_type'    => $params->get( 'pagination_type' ),					
    'player_description' => (int) $params->get( 'player_description' ),
    'autoplay'           => (int) $params->get( 'autoplay' ),	
    'loop'               => (int) $params->get( 'loop' ),
    'controls'           => (int) $params->get( 'controls' ),
    'modestbranding'     => (int) $params->get( 'modestbranding' ),
    'cc_load_policy'     => (int) $params->get( 'cc_load_policy' ),
    'iv_load_policy'     => (int) $params->get( 'iv_load_policy' ),
    'hl'                 => $params->get( 'hl' ),
    'cc_lang_pref'       => $params->get( 'cc_lang_pref' ),							
    'total_pages'        => 1,		
    'paged'              => (int) $params->get( 'paged', 1 ),	
    'next_page_token'    => isset( $pageInfo->nextPageToken ) ? $pageInfo->nextPageToken : '',
    'prev_page_token'    => isset( $pageInfo->prevPageToken ) ? $pageInfo->prevPageToken : ''
);

// Find total number of pages
$totalVideos = isset( $pageInfo->totalVideos ) ? (int) $pageInfo->totalVideos : 0;

if ( $totalVideos > 0 ) {
    if ( $sourceType == 'search' ) {
        $limit = min( (int) $params->get( 'limit' ), $totalVideos );
        $jsonArray['total_pages'] = ceil( $limit / $jsonArray['per_page'] );
    } else {
        $jsonArray['total_pages'] = ceil( $totalVideos / $jsonArray['per_page'] );
    }
}		

// Process output
if ( $jsonArray['total_pages'] < 2 ) {
    return false; 
}

if ( $jsonArray['pagination_type'] == 'pager' ) : ?>
    <nav class="avs-youtube-pagination avs-youtube-pagination-type-pager mb-4" data-params='<?php echo json_encode( $jsonArray ); ?>'>
        <div class="d-flex justify-content-center">
            <ul class="pagination">
                <li class="avs-page-item-previous page-item disabled">
                    <a href="#" class="avs-youtube-pagination-link avs-youtube-pagination-link-previous page-link" data-type="previous"><?php echo Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_BUTTON_LBL_PREVIOUS' ); ?></a>
                </li>
                <li class="page-item disabled">
                    <a href="#" class="page-link">
                        <span class="avs-page-info-current">1</span>
                        <?php echo Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_TEXT_OF' ); ?>
                        <span class="avs-page-info-total"><?php echo (int) $jsonArray['total_pages']; ?></span>
                    </a>
                </li>
                <li class="avs-page-item-next page-item">
                    <a href="#" class="avs-youtube-pagination-link avs-youtube-pagination-link-next page-link" data-type="next"><?php echo Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_BUTTON_LBL_NEXT' ); ?></a>
                </li>
            </ul>

            <div class="spinner-border" role="status"></div>
        </div>
    </nav>
<?php else : ?>
    <div class="avs-youtube-pagination avs-youtube-pagination-type-load_more mb-4" data-params='<?php echo json_encode( $jsonArray ); ?>'>
        <div class="d-flex justify-content-center">
            <button type="button" class="avs-youtube-pagination-link avs-youtube-pagination-link-next btn btn-primary" data-type="more"><?php echo Text::_( 'COM_ALLVIDEOSHARE_YOUTUBE_BUTTON_LBL_LOAD_MORE' ); ?></button>
            <div class="spinner-border" role="status"></div>
        </div>
    </div>
<?php endif;