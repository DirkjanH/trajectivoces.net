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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

$app = Factory::getApplication();

$canDo      = AllVideoShareHelper::canDo();
$listOrder  = $this->state->get( 'list.ordering' );
$listDirn   = $this->state->get( 'list.direction' );

$function   = $app->input->getCmd( 'function', 'jselectvideos' );
$onclick    = $this->escape( $function );

$valueField = $app->input->getCmd( 'vfield', 'id' );

// Import CSS & JS
HTMLHelper::_( 'bootstrap.tooltip' );

$wa = $app->getDocument()->getWebAssetManager();
$wa->useStyle( 'com_allvideoshare.admin' )
    ->useScript( 'com_allvideoshare.modal' );
?>

<div class="container-popup">
	<form action="<?php echo Route::_( 'index.php?option=com_allvideoshare&view=videos&layout=modal&tmpl=component&function=' . $function . '&vfield=' . $valueField . '&' . Session::getFormToken() . '=1' ); ?>" method="post" name="adminForm" id="adminForm" class="form-inline">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render( 'joomla.searchtools.default', array( 'view' => $this ) ); ?>
					<div class="clearfix"></div>

					<?php if ( empty( $this->items ) ) : ?>
						<div class="alert alert-warning">
							<?php echo Text::_( 'JGLOBAL_NO_MATCHING_RESULTS' ); ?>
						</div>
					<?php else : ?>
						<table class="table table-striped" id="videoList">
							<thead>
								<tr>
									<th>
										<?php echo HTMLHelper::_( 'searchtools.sort',  'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder ); ?>
									</th>
									<th class="d-none d-md-table-cell">
										<?php echo HTMLHelper::_( 'searchtools.sort',  'COM_ALLVIDEOSHARE_VIDEOS_CATEGORIES', 'a.catid', $listDirn, $listOrder ); ?>
									</th>						
									<th class="d-none d-md-table-cell">
										<?php echo HTMLHelper::_( 'searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder ); ?>
									</th>		
									<th class="text-center d-none d-md-table-cell">
										<?php echo HTMLHelper::_( 'searchtools.sort',  'COM_ALLVIDEOSHARE_VIDEOS_FEATURED', 'a.featured', $listDirn, $listOrder ); ?>
									</th>
									<th style="width:5%" class="nowrap text-center">
										<?php echo HTMLHelper::_( 'searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder ); ?>
									</th>
									<th class="text-center d-none d-md-table-cell">
										<?php echo HTMLHelper::_( 'searchtools.sort',  'COM_ALLVIDEOSHARE_VIDEOS_VIEWS', 'a.views', $listDirn, $listOrder ); ?>
									</th>
									<th class="text-center d-none d-md-table-cell">
										<?php echo HTMLHelper::_( 'searchtools.sort',  'JGLOBAL_FIELD_ID_LABEL', 'a.id', $listDirn, $listOrder ); ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $this->items as $i => $item ) : ?>
									<tr class="row<?php echo $i % 2; ?>">
										<td>
											<a class="all-video-share-select-link" href="javascript:void(0)" data-function="<?php echo $this->escape( $onclick ); ?>" data-value="<?php echo ( $valueField == 'slug' ) ? $this->escape( $item->slug ) : (int) $item->id; ?>" data-title="<?php echo $this->escape( $item->title ); ?>">
												<?php echo $this->escape( $item->title ); ?>
											</a>

											<div class="small break-word">
												<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_USER' ); ?>: 
												<?php echo $this->escape( $item->user ); ?>
											</div>

											<?php if ( $canDo ) : ?>
												<div class="small break-word">
													<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_RATINGS' ); ?>: 
													<?php echo ! empty( $item->ratings ) ? number_format( $item->ratings / 20, 1 ) : (float) $item->ratings; ?>
												</div>

												<div class="small break-word">
													<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_LIKES' ); ?>: 
													<?php echo (int) $item->likes; ?>/<?php echo (int) $item->dislikes; ?>
												</div>
											<?php endif; ?>
										</td>
										<td class="small d-none d-md-table-cell">
											<?php 					
											if ( $canDo && $this->params->get( 'multi_categories' ) && ! empty( $item->categories ) ) {
												$categories = array();

												foreach ( $item->categories as $category ) {
													$categories[] = $this->escape( $category->name );
												}

												printf(
													'<div class="mb-2">%s</div><span>%s:</span> %s',
													$this->escape( $item->category ),
													Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_ADDITIONAL' ),
													implode( ', ', $categories )
												);
											} else {
												echo $this->escape( $item->category );
											}
											?>
										</td>						
										<td class="small d-none d-md-table-cell">
											<?php echo $this->escape( $item->access ); ?>
										</td>			
										<td class="d-none d-md-table-cell text-center">
											<span class="tbody-icon">
												<?php if ( $item->featured ) : ?>
													<span class="icon-publish" aria-hidden="true"></span>
												<?php else : ?>
													<span class="icon-unpublish" aria-hidden="true"></span>
												<?php endif; ?>
											</span>
										</td>
										<td class="text-center">
											<span class="tbody-icon">
												<?php if ( $item->state ) : ?>
													<span class="icon-publish" aria-hidden="true"></span>
												<?php else : ?>
													<span class="icon-unpublish" aria-hidden="true"></span>
												<?php endif; ?>
											</span>
										</td>
										<td class="text-center btns d-none d-md-table-cell itemnumber">
											<a href="javascript:void(0);" class="btn btn-secondary small"><?php echo (int) $item->views; ?></a>
										</td>
										<td class="text-center d-none d-md-table-cell">
											<?php echo (int) $item->id; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

						<?php echo $this->pagination->getListFooter(); ?>
					<?php endif; ?>

					<input type="hidden" name="task" value="" />
					<input type="hidden" name="boxchecked" value="0" />
					<input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>" />
					<input type="hidden" name="forcedLanguage" value="<?php echo $app->input->get( 'forcedLanguage', '', 'CMD' ); ?>" />
					<?php echo HTMLHelper::_( 'form.token' ); ?>
				</div>
			</div>
		</div>
	</form>
</div>