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

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;
use MrVinoth\Module\AllVideoShareGallery\Site\Helper\AllVideoShareGalleryHelper;

// Vars
$params = AllVideoShareHelper::resolveParams( $params );
$items  = AllVideoShareGalleryHelper::getCategories( $params );

$image_ratio  = (float) $params->get( 'image_ratio', 56.25 );
$column_class = AllVideoShareHelper::getCSSClassNames( $params );

$custom_route = $params->get( 'link' );
if ( ! empty( $custom_route ) ) {
	$custom_route  = str_replace( 'slg=', '', $custom_route );
	$custom_route .= ! strpos( $custom_route, '?' ) ? '?slg=' : '&slg=';
}

$show_more = 0;
if ( $params->get( 'more' ) ) {
	$total = AllVideoShareGalleryHelper::getTotalCategories( $params );

	if ( $total > count( $items ) ) {
		$show_more = 1;
	}
}

// Import CSS
$wa = $app->getDocument()->getWebAssetManager();

if ( ! $wa->assetExists( 'style', 'com_allvideoshare.site' ) ) {
	$wr = $wa->getRegistry();
	$wr->addRegistryFile( 'media/com_allvideoshare/joomla.asset.json' );
}

if ( $params->get( 'load_bootstrap' ) ) {
	$wa->useStyle( 'com_allvideoshare.bootstrap' );
}

$wa->useStyle( 'com_allvideoshare.site' );

if ( $css = $params->get( 'custom_css' ) ) {
    $wa->addInlineStyle( $css );
}
?>

<div class="avs categories mod_allvideosharegallery">
	<?php if ( empty( $items ) ) : ?>
		<p class="text-muted">
			<?php echo Text::_( 'MOD_ALLVIDEOSHAREGALLERY_NO_CATEGORIES_FOUND' ); ?>
		</p>
	<?php else : ?>
		<div class="avs-grid">
			<div class="avs-row">
				<?php foreach ( $items as $i => $item ) : 
					if ( ! empty( $custom_route ) ) {
						$route = $custom_route . $item->slug;
					} else {
						$route = AllVideoShareRoute::getCategoryRoute( $item );
					}
					
					$item_link = Route::_( $route );
					?>
					<div class="<?php echo $column_class; ?>">
						<div class="avs-card mb-3 p-2">
							<a href="<?php echo $item_link; ?>" class="avs-responsive-item" style="padding-bottom: <?php echo $image_ratio; ?>%">
								<div class="avs-image" style="background-image: url( '<?php echo AllVideoShareHelper::getImage( $item ); ?>' );">&nbsp;</div>
							</a>

							<div class="avs-card-body mt-2">
								<div class="avs-title">
									<a href="<?php echo $item_link; ?>" class="card-link">
										<?php
										echo AllVideoShareHelper::Truncate( $item->name, $params->get( 'title_length', 0 ) ); 

										if ( $params->get( 'videos_count' ) ) {
											$count = AllVideoShareQuery::getVideosCount( $item->id );
											echo ' (' . $count . ')';
										}
										?>
									</a>
								</div>

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
					$route = AllVideoShareRoute::getCategoriesRoute( $params->get( 'category' ) );
					
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
