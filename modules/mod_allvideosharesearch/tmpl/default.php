<?php
/**
 * @version     4.2.0
 * @package     Com_AllVideoShare
 * @subpackage  Mod_AllVideoShareSearch
 * @author      Vinoth Kumar <admin@mrvinoth.com>
 * @copyright   Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die; 

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

// Vars
$params = AllVideoShareHelper::resolveParams( $params );

$itemId = $params->get( 'itemid', 0 );
$route  = AllVideoShareRoute::getSearchRoute( $itemId );

$search  = '';
$context = 'com_allvideoshare.search';
if ( $app->input->get( 'option' ) == 'com_allvideoshare' && $app->input->get( 'view' ) == 'search' ) {
	$search = $app->getUserStateFromRequest( $context, 'q', '', 'string' );
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

<form class="avs search mod_allvideosharesearch" action="<?php echo Route::_( $route ); ?>" method="GET" role="search">
	<?php if ( ! AllVideoShareHelper::isSEF() ) : ?>
		<input type="hidden" name="option" value="com_allvideoshare" />
		<input type="hidden" name="view" value="search" />
		<?php if ( ! empty( $itemId ) ) : ?>
			<input type="hidden" name="Itemid" value="<?php echo (int) $itemId; ?>" />
		<?php endif; ?>
	<?php endif; ?>

	<div class="input-group">
		<input type="text" name="q" class="form-control" placeholder="<?php echo Text::_( 'MOD_ALLVIDEOSHARESEARCH_FILTER_SEARCH_VIDEOS' ); ?>..." value="<?php echo htmlspecialchars( $search, ENT_COMPAT, 'UTF-8' ); ?>" />
		<button class="btn btn-primary" type="submit">
			<span class="icon-search icon-white" aria-hidden="true"></span>
		</button>
	</div>
</form>