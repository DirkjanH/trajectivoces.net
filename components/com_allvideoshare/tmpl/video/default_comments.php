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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
?>

<div class="avs-comments mb-4">
	<?php 
	if ( $this->params->get( 'comments_type' ) == 'facebook' ) { 
		?>	
		<p class="lead"><?php echo Text::_( 'COM_ALLVIDEOSHARE_TITLE_ADD_YOUR_COMMENTS' ); ?></p>

		<div id="fb-root"></div>

		<div class="fb-comments"
			data-href="<?php echo Uri::root() . 'index.php?option=com_allvideoshare&view=video&slg=' . $this->video->slug; ?>"
			data-numposts="<?php echo $this->params->get( 'comments_posts', 5 ); ?>"
			data-width="100%"
			data-colorscheme="<?php echo $this->params->get( 'comments_color', 'light' ); ?>">
		</div>
		<?php 
	} elseif ( $this->params->get( 'comments_type' ) == 'komento' ) {
		$komento_file = JPATH_ROOT . '/components/com_komento/bootstrap.php';

		if ( file_exists( $komento_file ) && ComponentHelper::getComponent( 'com_komento', true )->enabled ) {
			require_once( $komento_file );

			$item = new stdClass;
			$item->id = $this->video->id;
			$item->catid = $this->video->catid;
			$item->text = $this->video->description;
			$item->introtext = $this->video->description;

			echo KT::commentify( 'com_allvideoshare', $item, array( 'params' => '' ) );	
		}
	} elseif ( $this->params->get( 'comments_type' ) == 'jlex' ) {
		$jlex_file = JPATH_ROOT . '/components/com_jlexcomment/load.php';

		if ( file_exists( $jlex_file ) && ComponentHelper::getComponent( 'com_jlexcomment', true )->enabled ) {
			require_once( $jlex_file );
			echo JLexCommentLoader::init( 'allvideoshare', $this->video->id , $this->video->title );
		}
	} 
	?>
</div>