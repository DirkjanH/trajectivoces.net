<?php
/**
 * @version     4.2.0
 * @package     Com_AllVideoShare
 * @subpackage  Mod_AllVideoSharePlayer
 * @author      Vinoth Kumar <admin@mrvinoth.com>
 * @copyright   Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die; 

use Joomla\CMS\Language\Text;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoSharePlayer;
use MrVinoth\Module\AllVideoSharePlayer\Site\Helper\AllVideoSharePlayerHelper;

$item = AllVideoSharePlayerHelper::getItem( $params );
?>

<div class="avs player mod_allvideoshareplayer">
	<?php if ( empty( $item ) ) : ?>
		<p class="text-muted">
			<?php echo Text::_( 'MOD_ALLVIDEOSHAREPLAYER_VIDEO_NOT_FOUND' ); ?>
		</p>
	<?php else : ?>
		<?php if ( $params->get( 'title' ) ) : ?>
			<h3><?php echo $item->title; ?></h3>
		<?php endif; ?>

		<?php 
		$args = array( 
			'width' => $params->get( 'player_width' ),
			'ratio' => $params->get( 'player_ratio' ),
			'id'    => $item->id
		);
		
		$options = array_keys( $params->toArray() );
		
		foreach ( $options as $option ) {
			$value = $params->get( $option, '' );
			if ( $value != '' ) {
				$args[ $option ] = $value;
			}
		}
		
		echo AllVideoSharePlayer::load( $args ); 
		?>

		<?php if ( $params->get( 'description' ) ) : ?>
			<p style="margin-top: 15px;"><?php echo $item->description; ?></p>
		<?php endif; ?>
	<?php endif; ?>
</div>
