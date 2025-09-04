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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

$app = Factory::getApplication();

// Vars
$image_ratio  = (float) $this->params->get( 'image_ratio', 56.25 );
$column_class = AllVideoShareHelper::getCSSClassNames( $this->params );

// Import CSS
$wa = $app->getDocument()->getWebAssetManager();

if ( $this->params->get( 'load_bootstrap' ) ) {
	$wa->useStyle( 'com_allvideoshare.bootstrap' );
}

$wa->useStyle( 'com_allvideoshare.site' );

if ( $css = $this->params->get( 'custom_css' ) ) {
    $wa->addInlineStyle( $css );
}
?>

<div id="avs-categories" class="avs categories mb-4">
	<?php if ( $this->params->get( 'show_page_heading' ) ) : ?>
		<div class="page-header">
			<h2 itemprop="headline">
				<?php if ( $this->escape( $this->params->get( 'page_heading' ) ) ) : ?>
					<?php echo $this->escape( $this->params->get( 'page_heading' ) ); ?>
				<?php else : ?>
					<?php echo $this->escape($this->params->get( 'page_title' ) ); ?>
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
			<?php echo Text::_( 'COM_ALLVIDEOSHARE_CATEGORIES_NOT_FOUND' ); ?>
		</div>
	<?php else : ?>
		<div class="avs-grid">
			<div class="avs-row">
				<?php foreach ( $this->items as $i => $item ) : 
					$route = AllVideoShareRoute::getCategoryRoute( $item );
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
										echo AllVideoShareHelper::Truncate( $item->name, $this->params->get( 'title_length', 0 ) ); 

										if ( $this->params->get( 'videos_count' ) ) {
											$count = AllVideoShareQuery::getVideosCount( $item->id );
											echo ' (' . $count . ')';
										}
										?>
									</a>
								</div>
								
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
