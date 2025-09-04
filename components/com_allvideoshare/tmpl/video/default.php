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
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHtml;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoSharePlayer;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

$app = Factory::getApplication();

// Import CSS
$wa = $app->getDocument()->getWebAssetManager();

if ( $this->params->get( 'load_bootstrap' ) ) {
	$wa->useStyle( 'com_allvideoshare.bootstrap' );
}

if ( $this->params->get( 'popup' ) ) {
	$wa->useStyle( 'com_allvideoshare.popup' )
		->useScript( 'com_allvideoshare.popup' );
}

$wa->useStyle( 'com_allvideoshare.site' );

if ( $css = $this->params->get( 'custom_css' ) ) {
    $wa->addInlineStyle( $css );
}

$wa->useScript( 'com_allvideoshare.site' );

$inlineScript = "
	if ( typeof( avs ) === 'undefined' ) {
		var avs = {};
	};

	avs.baseurl = '" . URI::root() . "';
	avs.userid = " . Factory::getUser()->get( 'id' ) . ";	
	avs.guest_ratings = " . $this->params->get( 'guest_ratings', 0 ) . ";
	avs.guest_likes = " . $this->params->get( 'guest_likes', 0 ) . ";
	avs.message_login_required = '" . Text::_( 'COM_ALLVIDEOSHARE_ALERT_MESSAGE_LOGIN_REQUIRED' ) . "';
";

$wa->addInlineScript( $inlineScript, [ 'position' => 'before' ], [], [ 'com_allvideoshare.site' ] );
?>

<div id="avs-video" class="avs video mb-4">
	<?php 
	// Search Form
	if ( $this->hasAccess && $this->params->get( 'search' ) ) :
		$route = AllVideoShareRoute::getSearchRoute(); 
		?>
		<form class="avs-search-form mb-4" action="<?php echo Route::_( $route ); ?>" method="GET" role="search">
			<?php if ( ! AllVideoShareHelper::isSEF() ) : ?>
				<input type="hidden" name="option" value="com_allvideoshare" />
				<input type="hidden" name="view" value="search" />
				<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt( 'Itemid' ); ?>" />
			<?php endif; ?>

			<div class="input-group">
				<input type="text" name="q" class="form-control" placeholder="<?php echo Text::_( 'COM_ALLVIDEOSHARE_FILTER_SEARCH_VIDEOS' ); ?>..." />
				<button class="btn btn-primary" type="submit">
					<span class="icon-search icon-white" aria-hidden="true"></span>
				</button>
			</div>
		</form>
	<?php endif; ?>

	<?php
	// Video Player
	$args = array( 
		'width'  => $this->params->get( 'player_width' ),
		'ratio'  => $this->params->get( 'player_ratio' ),
		'id'     => $this->video->id,
		'Itemid' => $app->input->getInt( 'Itemid' )
	);

	echo '<div class="avs-player-wrapper mb-4">' . AllVideoSharePlayer::load( $args ) . '</div>';
	?>

	<div class="avs-video-info mb-4">
		<?php 
		// Video Title
		if ( $this->params->get( 'show_page_heading' ) ) : ?>
			<h2 class="avs-title mt-0 mb-2" itemprop="headline">
				<?php if ( $this->escape( $this->params->get( 'page_heading' ) ) ) : ?>
					<?php echo $this->escape( $this->params->get( 'page_heading' ) ); ?>
				<?php else : ?>
					<?php echo $this->escape($this->params->get( 'page_title' ) ); ?>
				<?php endif; ?>
			</h2>
		<?php endif; ?>

		<?php
		$meta = array();

		// Author Name
		if ( $this->params->get( 'author_name' ) ) {
			$meta[] = sprintf(
				'<span class="avs-meta-author"><span class="icon-user icon-fw"></span> %s</span>',
				Factory::getUser( $this->video->created_by )->username
			);
		}
				
		// Date Added
		if ( $this->params->get( 'date_added' ) ) {
			$jdate = new Date( $this->video->created_date );
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
				Text::sprintf( 'COM_ALLVIDEOSHARE_N_VIEWS_COUNT', $this->video->views )
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
		if ( $this->hasAccess && $this->params->get( 'category_name' ) ) {
			$categories = array();

			if ( isset( $this->video->category ) ) {
				$route = AllVideoShareRoute::getCategoryRoute( $this->video->category );
				$item_link = Route::_( $route );

				$categories[] = sprintf(
					'<a href="%s">%s</a>',
					$item_link,
					$this->escape( $this->video->category->name )
				);
			}
			
			if ( $this->params->get( 'multi_categories' ) && ! empty( $this->video->categories ) ) {
				foreach ( $this->video->categories as $category ) {
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
		// Ratings & Likes
		if ( $this->hasAccess && ( $this->params->get( 'ratings' ) || $this->params->get( 'likes' ) ) ) : ?>
			<?php if ( $this->params->get( 'ratings' ) ) : ?>
				<div id="avs-ratings-widget" class="mt-1">
					<?php echo AllVideoShareHtml::RatingsWidget( $this->video, $this->params ); ?>
				</div>
			<?php endif; ?>	
			
			<?php if ( $this->params->get( 'likes' ) ) : ?>
				<div id="avs-likes-dislikes-widget" class="mt-1">
					<?php echo AllVideoShareHtml::LikesWidget( $this->video, $this->params ); ?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php
		// Video Description	
		if ( $this->params->get( 'description' ) && ! empty( $this->video->description ) ) {
			echo '<div class="avs-description mt-3">' . $this->video->description . '</div>';
		}
		?>
	</div>	
    
	<?php	
	// Comments
	if ( $this->hasAccess && $this->params->get( 'comments_type' ) ) { 
		echo $this->loadTemplate( 'comments' );
	}
    ?>

	<?php	
	// Related Videos
	if ( $this->hasAccess && $this->params->get( 'related_videos' ) ) {
		echo $this->loadTemplate( 'related' );
	}
	?>
</div>