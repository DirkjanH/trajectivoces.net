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
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

$app  = Factory::getApplication();
$user = Factory::getUser();

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

<div id="avs-category" class="avs category mb-4">
	<?php if ( empty( $this->category ) ) : ?>
		<div class="alert alert-info">
			<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_NOT_FOUND' ); ?>
		</div>
	<?php elseif ( ! empty( $this->category->access ) && ! in_array( $this->category->access, $user->getAuthorisedViewLevels() ) ) : ?>
		<div class="alert alert-error">
			<?php echo Text::_( 'COM_ALLVIDEOSHARE_NO_PERMISSION_VIEW' ); ?>
		</div>
	<?php else : ?>
		<?php if ( $this->params->get( 'show_page_heading' ) ) : ?>
			<div class="page-header">
				<h2 itemprop="headline">
					<?php if ( $this->escape( $this->params->get( 'page_heading' ) ) ) : ?>
						<?php echo $this->escape( $this->params->get( 'page_heading' ) ); ?>
					<?php else : ?>
						<?php echo $this->escape($this->params->get( 'page_title' ) ); ?>
					<?php endif; ?>

					<?php if ( $this->params->get( 'videos_count' ) ) : ?>
						(<?php echo AllVideoShareQuery::getVideosCount( $this->category->id ); ?>)
					<?php endif; ?>

					<?php if ( $this->params->get( 'show_feed' ) ) : ?>
						<a href="<?php echo Route::_( 'index.php?option=com_allvideoshare&view=category&slg=' . $this->category->slug . '&format=feed' ); ?>" target="_blank" class="avs-feed-btn">
						<img src="<?php echo $this->params->get( 'feed_icon', Uri::root() . 'media/com_allvideoshare/images/rss.png' ); ?>" />
						</a>
					<?php endif; ?>
				</h2>
			</div>
		<?php endif; ?>

		<?php 
		if ( empty( $this->category->description ) && empty( $this->items ) && empty( $this->subCategories ) ) { ?>
			<div class="alert alert-info">
				<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_NOT_FOUND' ); ?>
			</div>
			<?php 
		} else {
			// Description
			if ( ! empty( $this->category->description ) ) {
				echo '<div class="avs-description mb-4">' . $this->category->description . '</div>';
			}

			// Videos
			if ( ! empty( $this->items ) ) {
				echo $this->loadTemplate( 'videos' );
			}

			// Subcategories
			if ( ! empty( $this->subCategories ) ) {
				echo $this->loadTemplate( 'categories' );
			}
		}
		?>
	<?php endif; ?>
</div>
