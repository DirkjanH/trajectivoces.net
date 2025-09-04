<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$wa = $this->document->getWebAssetManager();
$wa->useScript( 'com_allvideoshare.shortcode' );

$this->eName = Factory::getApplication()->input->getCmd( 'e_name', '' );
$this->eName = preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $this->eName );

$form = Form::getInstance( 'com_allvideoshare.youtube', JPATH_ROOT . '/administrator/components/com_allvideoshare/forms/youtube.xml' );
$form->bind( array() );
?>
<div class="container-popup">
	<form id="youtube-form">
		<?php echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', array( 'active' => 'source' ) ); ?>

		<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'source', Text::_( 'COM_ALLVIDEOSHARE_TAB_SOURCE', true ) ); ?>
        	<div class="row">
				<div class="col-lg-9">
					<?php echo $form->renderField( 'type' );  ?>
                    <?php echo $form->renderField( 'playlist' );  ?>
                    <?php echo $form->renderField( 'channel' );  ?>
                    <?php echo $form->renderField( 'username' );  ?>
                    <?php echo $form->renderField( 'search' );  ?>			
                    <?php echo $form->renderField( 'video' );  ?>
                    <?php echo $form->renderField( 'videos' );  ?>
                    <?php echo $form->renderField( 'order' );  ?>
                    <?php echo $form->renderField( 'limit' );  ?>
                    <?php echo $form->renderField( 'cache' );  ?>
        		</div>
        	</div>
		<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>

		<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'gallery', Text::_( 'COM_ALLVIDEOSHARE_TAB_GALLERY', true ) ); ?>
        		<div class="row">
					<div class="col-lg-9">
						<?php echo $form->renderField( 'layout' );  ?>
                        <?php echo $form->renderField( 'columns' );  ?>
                        <?php echo $form->renderField( 'per_page' );  ?>
                        <?php echo $form->renderField( 'image_ratio' );  ?>
                        <?php echo $form->renderField( 'title' );  ?>
                        <?php echo $form->renderField( 'title_length' );  ?>
                        <?php echo $form->renderField( 'excerpt' );  ?>
                        <?php echo $form->renderField( 'excerpt_length' );  ?>
                        <?php echo $form->renderField( 'pagination' );  ?>
                        <?php echo $form->renderField( 'pagination_type' );  ?>
                        <?php echo $form->renderField( 'arrows' );  ?>
                        <?php echo $form->renderField( 'arrow_size' );  ?>
                        <?php echo $form->renderField( 'arrow_bg_color' );  ?>
                        <?php echo $form->renderField( 'arrow_icon_color' );  ?>
                        <?php echo $form->renderField( 'arrow_radius' );  ?>
                        <?php echo $form->renderField( 'arrow_top_offset' );  ?>
                        <?php echo $form->renderField( 'arrow_left_offset' );  ?>
                        <?php echo $form->renderField( 'arrow_right_offset' );  ?>
                        <?php echo $form->renderField( 'dots' );  ?>
                        <?php echo $form->renderField( 'dot_size' );  ?>
                        <?php echo $form->renderField( 'dot_color' );  ?>
                        <?php echo $form->renderField( 'playlist_position' );  ?>
                        <?php echo $form->renderField( 'playlist_color' );  ?>
                        <?php echo $form->renderField( 'playlist_width' );  ?>
                        <?php echo $form->renderField( 'playlist_height' );  ?>
        		</div>
        	</div>
		<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>

		<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'player', Text::_( 'COM_ALLVIDEOSHARE_TAB_PLAYER', true ) ); ?>
        	<div class="row">
				<div class="col-lg-9">
					<?php echo $form->renderField( 'player_ratio' );  ?>
                    <?php echo $form->renderField( 'player_title' );  ?>
                    <?php echo $form->renderField( 'player_description' );  ?>		
                    <?php echo $form->renderField( 'autoplay' );  ?>
                    <?php echo $form->renderField( 'autoadvance' );  ?>
                    <?php echo $form->renderField( 'loop' );  ?>
                    <?php echo $form->renderField( 'controls' );  ?>
                    <?php echo $form->renderField( 'modestbranding' );  ?>
                    <?php echo $form->renderField( 'cc_load_policy' );  ?>
                    <?php echo $form->renderField( 'iv_load_policy' );  ?>
                    <?php echo $form->renderField( 'hl' );  ?>
                    <?php echo $form->renderField( 'cc_lang_pref' );  ?>
        		</div>
       		</div>
		<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>
        
        <?php echo HTMLHelper::_( 'uitab.endTabSet' ); ?>
	</form>
</div>