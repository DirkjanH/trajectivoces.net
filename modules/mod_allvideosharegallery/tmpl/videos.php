<?php
/**
 * @version     4.2.0
 * @package     Com_AllVideoShare
 * @subpackage  Mod_AllVideoShareGallery
 * @author      Vinoth Kumar <admin@mrvinoth.com>
 * @copyright   Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;
use MrVinoth\Module\AllVideoShareGallery\Site\Helper\AllVideoShareGalleryHelper;

// Vars
$params = AllVideoShareHelper::resolveParams( $params );
$items  = AllVideoShareGalleryHelper::getVideos( $params );

$player_ratio = $params->get( 'player_ratio', 56.25 );
$image_ratio  = $params->get( 'image_ratio', 56.25 );
$column_class = AllVideoShareHelper::getCSSClassNames( $params );
$popup_class  = AllVideoShareHelper::getCSSClassNames( $params, 'popup' );

$custom_route = $params->get( 'link' );
if ( ! empty( $custom_route ) ) {
	$custom_route  = str_replace( 'slg=', '', $custom_route );
	$custom_route .= ! strpos( $custom_route, '?' ) ? '?slg=' : '&slg=';
}

$show_more = 0;
if ( $params->get( 'more' ) ) {
	$total = AllVideoShareGalleryHelper::getTotalVideos( $params );

	if ( $total > count( $items ) ) {
		$show_more = 1;
	}
}

// Import CSS & JS
$wa = $app->getDocument()->getWebAssetManager();

if ( ! $wa->assetExists( 'style', 'com_allvideoshare.site' ) ) {
	$wr = $wa->getRegistry();
	$wr->addRegistryFile( 'media/com_allvideoshare/joomla.asset.json' );
}

if ( $params->get( 'load_bootstrap' ) ) {
	$wa->useStyle( 'com_allvideoshare.bootstrap' );
}

if ( $params->get( 'popup' ) ) {
	$wa->useStyle( 'com_allvideoshare.popup' )
		->useScript( 'com_allvideoshare.popup' )
		->useScript( 'com_allvideoshare.site' );
}

$wa->useStyle( 'com_allvideoshare.site' );

if ( $css = $params->get( 'custom_css' ) ) {
    $wa->addInlineStyle( $css );
}
?>

<div class="avs videos mod_allvideosharegallery">
	<?php if ( empty( $items ) ) : ?>
		<p class="text-muted">
			<?php echo Text::_( 'MOD_ALLVIDEOSHAREGALLERY_NO_VIDEOS_FOUND' ); ?>
		</p>
	<?php else : ?>
		<div class="avs-grid mb-4<?php echo $popup_class; ?>" data-player_ratio="<?php echo (float) $player_ratio; ?>">
			<div class="avs-row">
				<?php foreach ( $items as $i => $item ) : 
					if ( ! empty( $custom_route ) ) {
						$route = $custom_route . $item->slug;
					} else {
						$route = AllVideoShareRoute::getVideoRoute( $item );
					}
					
					$item_link = Route::_( $route );

					if ( $params->get( 'popup' ) ) {
						$item_link = 'javascript:void(0)';
					}			

					$iframe_src = URI::root() . 'index.php?option=com_allvideoshare&view=player&id=' . $item->id . "&format=raw&autoplay=1";
					?>
					<div class="avs-grid-item avs-video-<?php echo (int) $item->id; ?> <?php echo $column_class; ?>" data-mfp-src="<?php echo $iframe_src; ?>">
						<div class="avs-card mb-3 p-2">
							<a href="<?php echo $item_link; ?>" class="avs-responsive-item" style="padding-bottom: <?php echo (float) $image_ratio; ?>%">
								<div class="avs-image" style="background-image: url( '<?php echo AllVideoShareHelper::getImage( $item ); ?>' );">&nbsp;</div>
								
								<svg class="avs-svg-icon avs-svg-icon-play" width="32" height="32" viewBox="0 0 32 32">
									<path d="M16 0c-8.837 0-16 7.163-16 16s7.163 16 16 16 16-7.163 16-16-7.163-16-16-16zM16 29c-7.18 0-13-5.82-13-13s5.82-13 13-13 13 5.82 13 13-5.82 13-13 13zM12 9l12 7-12 7z"></path>
								</svg>
							</a>

							<div class="avs-card-body mt-2">
								<div class="avs-title">
									<a href="<?php echo $item_link; ?>" class="card-link">
										<?php echo AllVideoShareHelper::Truncate( $item->title, $params->get( 'title_length', 0 ) ); ?>
									</a>
								</div>								

								<?php
                                $meta = array();
                                    
                                // Author Name
                                if ( $params->get( 'author_name' ) ) {
                                    $meta[] = sprintf(
                                        '<span class="avs-meta-author"><span class="icon-user icon-fw"></span> %s</span>',
                                        Factory::getUser( $item->created_by )->username
                                    );
                                }

                                // Date Added
                                if ( $params->get( 'date_added' ) ) {
                                    $jdate = new Date( $item->created_date );
                                    $prettyDate = $jdate->format( Text::_( 'DATE_FORMAT_LC3' ) );

                                    $meta[] = sprintf(
                                        '<span class="avs-meta-date"><span class="icon-calendar icon-fw"></span> %s</span>',
                                        $prettyDate
                                    );
                                }

                                // Views Count
                                if ( $params->get( 'views' ) ) {
                                    $meta[] = sprintf(
                                        '<span class="avs-meta-views"><span class="icon-eye icon-fw"></span> %s</span>',
                                        Text::sprintf( 'MOD_ALLVIDEOSHAREGALLERY_N_VIEWS_COUNT', $item->views )
                                    );
                                }	
                                
                                if ( count( $meta ) ) {
                                    printf( 
                                        '<div class="avs-meta text-muted small mt-1">%s</div>',
                                        implode( ' / ', $meta )
                                    );
                                }
                                ?>	

                                <?php 
                                // Categories
                                if ( $params->get( 'category_name' ) ) {
                                    $categories = array();

                                    if ( isset( $item->category ) && ! empty( $item->category ) ) {
                                        $route = AllVideoShareRoute::getCategoryRoute( $item->category );
                                        $item_link = Route::_( $route );

                                        $categories[] = sprintf(
                                            '<a href="%s">%s</a>',
                                            $item_link,
                                            $item->category->name
                                        ); 
                                    }

                                    if ( $params->get( 'multi_categories' ) && ! empty( $item->categories ) ) {
                                        foreach ( $item->categories as $category ) {
                                            $route = AllVideoShareRoute::getCategoryRoute( $category );
                                            $item_link = Route::_( $route );
                        
                                            $categories[] = sprintf(
                                                '<a href="%s">%s</a>',
                                                $item_link,
                                                $category->name
                                            );
                                        }
                                    }

                                    if ( ! empty( $categories ) ) {
                                        printf(
                                            '<div class="avs-categories text-muted small mt-1"><span class="icon-folder-open icon-fw"></span> %s</div>',
                                            implode( ', ', $categories )
                                        );
                                    }
                                }
                                ?>

								<?php 
								// Ratings
								if ( $params->get( 'ratings' ) ) : ?>
									<div class="avs-ratings-small mt-1">
										<span class="avs-ratings-stars">
											<span class="avs-ratings-current" style="width: <?php echo (float) $item->ratings; ?>%;"></span>
										</span>
									</div>
								<?php endif; ?> 

								<?php 
								// Short Description
								if ( $params->get( 'excerpt' ) && ! empty( $item->description ) ) : ?>
									<div class="avs-excerpt small mt-2">
										<?php echo AllVideoShareHelper::Truncate( $item->description, $params->get( 'excerpt_length' ) ); ?>
									</div>
								<?php endif; ?>
							</div>					
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php 
			// More Button
			if ( $show_more ) {
				$more_button_url = $params->get( 'more_button_url' );

				if ( empty( $more_button_url ) ) {
					$route = AllVideoShareRoute::getVideosRoute( $params->get( 'category' ) );
					
					if ( ! empty( $route ) ) {
						$more_button_url = Route::_( $route );
					}
				}		
				
				if ( ! empty( $more_button_url ) ) {
					printf(
						'<a class="btn btn-primary" href="%s">%s</a>',
						$more_button_url,
						$params->get( 'more_button_text', Text::_( 'MOD_ALLVIDEOSHAREGALLERY_MORE_BUTTON_TEXT' ) )
					);
				}
			}		 
			?>
		</div>		
	<?php endif; ?>
</div>
