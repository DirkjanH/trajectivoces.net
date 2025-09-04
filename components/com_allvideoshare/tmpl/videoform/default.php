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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

$app = Factory::getApplication();

// Import CSS & JS
$wa = $app->getDocument()->getWebAssetManager();

if ( $this->params->get( 'load_bootstrap' ) ) {
	$wa->useStyle( 'com_allvideoshare.bootstrap' );
}

$wa->useStyle( 'com_allvideoshare.site' );

if ( $css = $this->params->get( 'custom_css' ) ) {
    $wa->addInlineStyle( $css );
}

$wa->useScript( 'keepalive' )
	->useScript( 'form.validate' )
	->useScript( 'com_allvideoshare.form' );

$canEdit  = AllVideoShareHelper::canUserEdit( $this->item );
$canState = Factory::getUser()->authorise( 'core.edit.state','com_allvideoshare' );
?>

<div id="avs-videoform" class="avs videoform mb-4">
	<?php if ( ! $canEdit ) : ?>
		<?php $app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_NO_PERMISSION_EDIT' ), 'error' ); ?>
	<?php else : ?>
		<?php if ( $this->params->get( 'show_page_heading' ) ) : ?>	
			<div class="page-header">
				<?php if ( ! empty( $this->item->id ) ) : ?>
					<h2 itemprop="headline"><?php echo Text::_( 'COM_ALLVIDEOSHARE_EDIT_VIDEO_TITLE' ); ?></h2>
				<?php else: ?>
					<h2 itemprop="headline"><?php echo Text::_( 'COM_ALLVIDEOSHARE_ADD_VIDEO_TITLE' ); ?></h2>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<form id="form-video" action="<?php echo Route::_( 'index.php?option=com_allvideoshare&task=videoform.save' ); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="jform[id]" value="<?php echo isset( $this->item->id ) ? (int) $this->item->id : ''; ?>" />
			<input type="hidden" name="jform[captions]" value='<?php echo isset( $this->item->captions ) ? $this->item->captions : ''; ?>' />
			<input type="hidden" name="jform[access]" value="<?php echo isset( $this->item->access ) ? (int) $this->item->access : 1; ?>" />
			<input type="hidden" name="jform[featured]" value="<?php echo isset( $this->item->featured ) ? (int) $this->item->featured : 0; ?>" />
			<input type="hidden" name="jform[views]" value="<?php echo isset( $this->item->views ) ? (int) $this->item->views : 0; ?>" />
			<input type="hidden" name="jform[ratings]" value="<?php echo isset( $this->item->ratings ) ? (float) $this->item->ratings : 0; ?>" />
			<input type="hidden" name="jform[likes]" value="<?php echo isset( $this->item->likes ) ? (float) $this->item->likes : 0; ?>" />
			<input type="hidden" name="jform[dislikes]" value="<?php echo isset( $this->item->dislikes ) ? (float) $this->item->dislikes : 0; ?>" />
			<input type="hidden" name="jform[ordering]" value="<?php echo isset( $this->item->ordering ) ? (int) $this->item->ordering : ''; ?>" />
			<input type="hidden" name="jform[checked_out]" value="<?php echo isset( $this->item->checked_out ) ? $this->item->checked_out : ''; ?>" />
			<input type="hidden" name="jform[checked_out_time]" value="<?php echo isset( $this->item->checked_out_time ) ? $this->item->checked_out_time : ''; ?>" />
			<input type="hidden" name="jform[created_date]" value="<?php echo isset( $this->item->created_date ) ? $this->item->created_date : ''; ?>" />

			<?php echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', array( 'active' => 'video', 'breakpoint' => '0' ) ); ?>
				
			<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'video', Text::_( 'COM_ALLVIDEOSHARE_TAB_VIDEO', true ) ); ?>
				<div class="avs-control-group avs-control-group-title">
					<?php echo $this->form->renderField( 'title' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-catid">
					<?php echo $this->form->renderField( 'catid' ); ?>
				</div>

				<?php if ( $this->canDo && $this->params->get( 'multi_categories' ) ) : ?>
					<div class="avs-control-group avs-control-group-catids">
						<?php echo $this->form->renderField( 'catids' ); ?>
					</div>
				<?php endif; ?>

				<div class="avs-control-group avs-control-group-type">
					<?php
					$options = array(
						'general' => Text::_( 'COM_ALLVIDEOSHARE_FORM_OPTION_TYPE_GENERAL' )
					);

					if ( $this->params->get( 'type_youtube') ) {
						$options['youtube'] = Text::_( 'COM_ALLVIDEOSHARE_FORM_OPTION_TYPE_YOUTUBE' );
					}

					if ( $this->params->get( 'type_vimeo') ) {
						$options['vimeo'] = Text::_( 'COM_ALLVIDEOSHARE_FORM_OPTION_TYPE_VIMEO' );
					}

					if ( $this->params->get( 'type_hls') ) {
						$options['hls'] = Text::_( 'COM_ALLVIDEOSHARE_FORM_OPTION_TYPE_HLS' );
					}
					?>
					<div class="control-group"<?php if ( count( $options ) == 1 ) { echo ' style="display: none;"'; } ?>>
						<div class="control-label">
							<label id="jform_type-lbl" for="jform_type"><?php echo Text::_( 'COM_ALLVIDEOSHARE_FORM_LBL_VIDEO_TYPE' ); ?></label>
						</div>
						<div class="controls">
							<select id="jform_type" name="jform[type]" class="form-select">
								<?php
								foreach ( $options as $value => $text ) {								
									$selected = isset( $this->item->type ) ? $this->item->type : 'general';

									printf(
										'<option value="%s"%s>%s</option>',
										$value,
										( $value == $selected ? ' selected="selected"' : '' ),
										$text
									);
								}
								?>
							</select>
						</div>
					</div>
				</div>

				<div class="avs-control-group avs-control-group-video">
					<?php echo $this->form->renderField( 'video' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-hd" style="display: none;">
					<?php echo $this->form->renderField( 'hd' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-youtube"<?php if ( ! $this->params->get( 'type_youtube' ) ) { echo ' style="display: none;"'; } ?>>
					<?php echo $this->form->renderField( 'youtube' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-vimeo"<?php if ( ! $this->params->get( 'type_vimeo' ) ) { echo ' style="display: none;"'; } ?>>
					<?php echo $this->form->renderField( 'vimeo' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-hls"<?php if ( ! $this->params->get( 'type_hls' ) ) { echo ' style="display: none;"'; } ?>>
					<?php echo $this->form->renderField( 'hls' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-thumb">
					<?php echo $this->form->renderField( 'thumb' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-description">
					<?php echo $this->form->renderField( 'description' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-state">
					<div class="control-group">
						<?php if ( ! $canState ) : ?>
							<div class="control-label"><?php echo $this->form->getLabel( 'state' ); ?></div>
							<div class="controls">
								<?php
								$state = isset( $this->item->state ) ? $this->item->state : 0;

								switch ( $state ) {
									case 1:
										echo Text::_( 'JPUBLISHED' );
										break;
									case 0:
										echo Text::_( 'JUNPUBLISHED' );
										break;
								}
								?>
							</div>
							<input type="hidden" name="jform[state]" value="<?php echo $state; ?>" />
						<?php else : ?>
							<div class="control-label"><?php echo $this->form->getLabel( 'state' ); ?></div>
							<div class="controls"><?php echo $this->form->getInput( 'state' ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>

			<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'Advanced', Text::_( 'COM_ALLVIDEOSHARE_TAB_ADVANCED', true ) ); ?>
				<div class="avs-control-group avs-control-group-tags">
					<?php echo $this->form->renderField( 'tags' ); ?>
				</div>

				<div class="avs-control-group avs-control-group-metadescription">
					<?php echo $this->form->renderField( 'metadescription' ); ?>
				</div>
			<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>

			<div class="control-group">
				<div class="controls">
					<?php if ( $this->canSave ) : ?>
						<button type="submit" class="validate btn btn-primary">
							<span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_( 'JSUBMIT' ); ?>
						</button>
					<?php endif; ?>
					<a class="btn btn-danger" href="<?php echo Route::_( 'index.php?option=com_allvideoshare&task=videoform.cancel' ); ?>" title="<?php echo Text::_( 'JCANCEL' ); ?>">
					   <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_( 'JCANCEL' ); ?>
					</a>
				</div>
			</div>

			<input type="hidden" name="option" value="com_allvideoshare" />
			<input type="hidden" name="task" value="videoform.save" />
			<?php echo HTMLHelper::_( 'form.token' ); ?>
		</form>
	<?php endif; ?>
</div>
