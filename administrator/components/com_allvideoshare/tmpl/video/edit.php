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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

HTMLHelper::_( 'draggablelist.draggable' );

$wa = $this->document->getWebAssetManager();

$wa->useStyle( 'com_allvideoshare.admin' )
	->useScript( 'keepalive' )
	->useScript( 'form.validate' )
    ->useScript( 'com_allvideoshare.admin' );

$inlineScript = "
	if ( typeof( avs ) === 'undefined' ) {
		var avs = {};
	};

	avs.i18n_upload_file = '" . Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_UPLOAD_FILE' ) . "';
";

$wa->addInlineScript( $inlineScript, [ 'position' => 'before' ], [], [ 'com_allvideoshare.admin' ] );
?>

<form action="<?php echo Route::_( 'index.php?option=com_allvideoshare&layout=edit&id=' . (int) $this->item->id ); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="video-form" class="form-validate form-horizontal">
	<div class="row title-alias form-vertical mb-3">
		<div class="col-12 col-md-6">
			<?php echo $this->form->renderField( 'title' ); ?>
		</div>
		<div class="col-12 col-md-6">
			<?php echo $this->form->renderField( 'slug' ); ?>
    	</div>
	</div>

	<?php echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', array( 'active' => 'general' ) ); ?>

	<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'general', Text::_( 'COM_ALLVIDEOSHARE_TAB_GENERAL', true ) ); ?>
		<div class="row">
			<div class="col-lg-8">
				<div>
					<fieldset class="options-form">
						<legend><?php echo Text::_( 'COM_ALLVIDEOSHARE_FIELDSET_GENERAL' ); ?></legend>
						<?php echo $this->form->renderField( 'type' ); ?>
						<?php echo $this->form->renderField( 'video' ); ?>
						<?php echo $this->form->renderField( 'hd' ); ?>
						<?php echo $this->form->renderField( 'youtube' ); ?>
						<?php echo $this->form->renderField( 'vimeo' ); ?>
						<?php echo $this->form->renderField( 'hls' ); ?>
						<?php echo $this->form->renderField( 'thirdparty' ); ?>
						<?php echo $this->form->renderField( 'thumb' ); ?>
						<?php echo $this->form->renderField( 'description' ); ?>						
					</fieldset>
				</div>
			</div>

			<div class="col-lg-4">
				<fieldset class="form-vertical">
					<legend class="visually-hidden"><?php echo Text::_( 'COM_ALLVIDEOSHARE_FIELDSET_GENERAL' ); ?></legend>
					<?php echo $this->form->renderField( 'catid' ); ?>
					<?php if ( $this->canDo && $this->params->get( 'multi_categories' ) ) echo $this->form->renderField( 'catids' ); ?>					
					<?php echo $this->form->renderField( 'access' ); ?>
					<?php echo $this->form->renderField( 'featured' ); ?>
					<?php echo $this->form->renderField( 'views' ); ?>
					<?php echo $this->form->renderField( 'state' ); ?>					
					<?php echo $this->form->renderField( 'user' ); ?>
					<?php echo $this->form->renderField( 'created_date' ); ?>
				</fieldset>
			</div>
		</div>
	<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>

	<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'captions', Text::_( 'COM_ALLVIDEOSHARE_TAB_CAPTIONS', true ) ); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="options-form">
					<legend><?php echo Text::_( 'COM_ALLVIDEOSHARE_FIELDSET_CAPTIONS' ); ?></legend>

					<div class="alert alert-success mt-0 small">
						<?php echo Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_HEADER_NOTE' ); ?>
					</div>

					<input type="hidden" name="jform[captions]" />
					
					<table class="table" id="captionList">
						<thead>
							<tr>
								<th width="1%" class="text-center d-none d-md-table-cell">
									<span class="icon-menu-2" aria-hidden="true"></span>
								</th>												
								<th>
									<?php echo Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_TABLE_HEADING_FILE' ); ?>
								</th>			
								<th>
									<?php echo Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_TABLE_HEADING_LABEL' ); ?>
								</th>
								<th>
									<?php echo Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_TABLE_HEADING_SRCLANG' ); ?>
								</th>	
								<th class="d-none d-md-table-cell">
									&nbsp;
								</th>
							</tr>
						</thead>
						<tbody id="avs-captions" class="js-draggable">
							<?php if ( ! empty( $this->item->captions ) && $captions = json_decode( $this->item->captions ) ) : ?>
								<?php foreach ( $captions as $caption ) : ?>
									<tr class="avs-caption">	
										<td class="text-center d-none d-md-table-cell">
											<span class="sortable-handler">
												<span class="icon-ellipsis-v" aria-hidden="true"></span>
											</span>
										</td>
										<td>										
											<div class="avs-caption-file">
												<?php 
												if ( ! empty( $caption->src ) ) {
													echo basename( $caption->src ); 
												} else {
													echo '<a href="javascript:void(0)" class="avs-caption-upload btn btn-success btn-sm">' . Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_UPLOAD_FILE' ) . '</a>';
												}
												?>
											</div>
											<input type="hidden" name="jform[captions][src][]" value="<?php echo $caption->src; ?>" />
											<input type="file" name="jform[captions][file][]" class="hidden" />
										</td>							
										<td>
											<input type="text" name="jform[captions][label][]" value="<?php echo $caption->label; ?>" placeholder="English" class="form-control form-control-sm" />
										</td>
										<td>
											<input type="text" name="jform[captions][srclang][]" value="<?php echo $caption->srclang; ?>" placeholder="en" class="form-control form-control-sm" />
										</td>																
										<td class="text-center">
											<span class="tbody-icon">
												<a href="javascript: void(0);" class="avs-caption-remove">
													<span class="icon-delete" aria-hidden="true"></span>
												</a>
											</span>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>

					<div id="avs-captions-empty-note" class="text-center text-muted<?php if ( ! empty( $this->item->captions ) ) { echo ' hidden'; } ?>">
						<?php echo Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_EMPTY_NOTE' ); ?>
					</div>

					<div class="clearfix">
						<button type="button" id="avs-captions-add-new" class="btn btn-secondary float-end" onclick="return false;">
							<span class="icon-save-new" aria-hidden="true"></span> 
							<?php echo Text::_( 'COM_ALLVIDEOSHARE_CAPTIONS_BUTTON_ADD' ); ?>
						</button>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>

	<?php echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'advanced', Text::_( 'COM_ALLVIDEOSHARE_TAB_ADVANCED', true ) ); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="options-form">
					<legend><?php echo Text::_( 'COM_ALLVIDEOSHARE_FIELDSET_ADVANCED' ); ?></legend>
					<?php echo $this->form->renderField( 'tags' ); ?>
					<?php echo $this->form->renderField( 'metadescription' ); ?>
				</fieldset>
			</div>
		</div>
	<?php echo HTMLHelper::_( 'uitab.endTab' ); ?>

	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField( 'created_by' ); ?>
	<?php echo $this->form->renderField( 'modified_by' ); ?>
	<?php echo $this->form->renderField( 'updated_date' ); ?>
	
	<?php echo HTMLHelper::_( 'uitab.endTabSet' ); ?>

	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>
