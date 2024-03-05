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

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

$app = Factory::getApplication();

// Vars
$itemid = $app->input->getInt( 'Itemid', 0 );
$player_ratio = (float) $this->params->get( 'player_ratio', 56.25 );
$image_ratio  = (float) $this->params->get( 'image_ratio', 56.25 );
$column_class = AllVideoShareHelper::getCSSClassNames( $this->params );
$popup_class  = AllVideoShareHelper::getCSSClassNames( $this->params, 'popup' );

// Import CSS
$wa = $app->getDocument()->getWebAssetManager();

if ( $this->params->get( 'load_bootstrap' ) ) {
	$wa->useStyle( 'com_allvideoshare.bootstrap' );
}

if ( $this->params->get( 'popup' ) ) {
	$wa->useStyle( 'com_allvideoshare.popup' )
		->useScript( 'com_allvideoshare.popup' )
		->useScript( 'com_allvideoshare.site' );
}

$wa->useStyle( 'com_allvideoshare.site' );

if ( $css = $this->params->get( 'custom_css' ) ) {
    $wa->addInlineStyle( $css );
}
?>

<div id="avs-videos" class="avs videos mb-4">
	<?php if ( $this->params->get( 'show_page_heading' ) ) : ?>
		<div class="page-header">
			<h2 itemprop="headline">
				<?php if ( $this->escape( $this->params->get( 'page_heading' ) ) ) : ?>
					<?php echo $this->escape( $this->params->get( 'page_heading' ) ); ?>
				<?php else : ?>
					<?php echo $this->escape( $this->params->get( 'page_title' ) ); ?>
				<?php endif; ?>

				<?php if ( $this->params->get( 'show_feed' ) ) : ?>
					<a href="<?php echo Route::_( 'index.php?option=com_allvideoshare&view=videos&format=feed' ); ?>" target="_blank" class="avs-feed-btn">
						<img src="<?php echo $this->params->get( 'feed_icon', Uri::root() . 'media/com_allvideoshare/images/rss.png' ); ?>" />
					</a>
				<?php endif; ?>
			</h2>
		</div>
	<?php endif; ?>

	<?php if ( empty( $this->items ) ) : ?>
		<div class="alert alert-info">
			<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_NOT_FOUND' ); ?>
		</div>
	<?php else : ?>
		<div class="avs-grid mb-4<?php echo $popup_class; ?>" data-player_ratio="<?php echo $player_ratio; ?>">
			<div class="avs-row">
				<?php foreach ( $this->items as $i => $item ) : 
					$route = AllVideoShareRoute::getVideoRoute( $item );
					$item_link = Route::_( $route );

					if ( $this->params->get( 'popup' ) ) {
						$item_link = 'javascript:void(0)';
					}			

					$iframe_src = URI::root() . 'index.php?option=com_allvideoshare&view=player&id=' . $item->id . "&format=raw&autoplay=1&Itemid=" . $itemid;
					?>
					<div class="avs-grid-item avs-video-<?php echo (int) $item->id; ?> <?php echo $column_class; ?>" data-mfp-src="<?php echo $iframe_src; ?>">
						<div class="avs-card mb-3 p-2">
							<a href="<?php echo $item_link; ?>" class="avs-responsive-item" style="padding-bottom: <?php echo $image_ratio; ?>%">
								<div class="avs-image" style="background-image: url( '<?php echo AllVideoShareHelper::getImage( $item ); ?>' );">&nbsp;</div>
								
								<svg class="avs-svg-icon avs-svg-icon-play" width="32" height="32" viewBox="0 0 32 32">
									<path d="M16 0c-8.837 0-16 7.163-16 16s7.163 16 16 16 16-7.163 16-16-7.163-16-16-16zM16 29c-7.18 0-13-5.82-13-13s5.82-13 13-13 13 5.82 13 13-5.82 13-13 13zM12 9l12 7-12 7z"></path>
								</svg>
							</a>

							<div class="avs-card-body mt-2">
								<div class="avs-title">
									<a href="<?php echo $item_link; ?>" class="card-link">
										<?php echo AllVideoShareHelper::Truncate( $item->title, $this->params->get( 'title_length', 0 ) ); ?>
									</a>
								</div>							

								<?php
								$meta = array();
									
								// Author Name
								if ( $this->params->get( 'author_name' ) ) {
									$meta[] = sprintf(
										'<span class="avs-meta-author"><span class="icon-user icon-fw"></span> %s</span>',
										Factory::getUser( $item->created_by )->username
									);
								}

								// Date Added
								if ( $this->params->get( 'date_added' ) ) {
									$jdate = new Date( $item->created_date );
									$prettyDate = $jdate->format( Text::_( 'DATE_FORMAT_LC3' ) );

									$meta[] = sprintf(
										'<span class="avs-meta-date"><span class="icon-calendar icon-fw"></span> %s</span>',
										$prettyDate
									);
								}

								// Views Count
								if ( $this->params->get( 'views' ) ) {
									$meta[] = sprintf(
										'<span class="avs-meta-views"><span class="icon-eye icon-fw"></span> %s</span>',
										Text::sprintf( 'COM_ALLVIDEOSHARE_N_VIEWS_COUNT', $item->views )
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
								if ( $this->params->get( 'category_name' ) ) {
									$categories = array();

									if ( isset( $item->category ) && ! empty( $item->category ) ) {
										$route = AllVideoShareRoute::getCategoryRoute( $item->category );
										$item_link = Route::_( $route );

										$categories[] = sprintf(
											'<a href="%s">%s</a>',
											$item_link,
											$this->escape( $item->category->name )
										); 
									}

									if ( $this->params->get( 'multi_categories' ) && ! empty( $item->categories ) ) {
										foreach ( $item->categories as $category ) {
											$route = AllVideoShareRoute::getCategoryRoute( $category );
											$item_link = Route::_( $route );
						
											$categories[] = sprintf(
												'<a href="%s">%s</a>',
												$item_link,
												$this->escape( $category->name )
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
								if ( $this->params->get( 'ratings' ) ) : ?>
									<div class="avs-ratings-small mt-1">
										<span class="avs-ratings-stars">
											<span class="avs-ratings-current" style="width: <?php echo (float) $item->ratings; ?>%;"></span>
										</span>
									</div>
								<?php endif; ?> 

								<?php 
								// Short Description
								if ( $this->params->get( 'excerpt' ) && ! empty( $item->description ) ) : ?>
									<div class="avs-excerpt small mt-2">
										<?php echo AllVideoShareHelper::Truncate( $item->description, $this->params->get( 'excerpt_length' ) ); ?>
									</div>
								<?php endif; ?>
							</div>					
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<?php echo $this->pagination->getListFooter(); ?>
		</div>		
	<?php endif; ?>
</div>
