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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

HTMLHelper::_( 'formbehavior.chosen', 'select' );

$app  = Factory::getApplication();
$user = Factory::getUser();

$userId     = $user->get( 'id' );
$listOrder  = $this->state->get( 'list.ordering' );
$listDirn   = $this->state->get( 'list.direction' );
$canCreate  = $user->authorise( 'core.create', 'com_allvideoshare' ) && file_exists( JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'videoform.xml' );
$canEdit    = ( $user->authorise( 'core.edit', 'com_allvideoshare' ) || $user->authorise( 'core.edit.own', 'com_allvideoshare' ) ) && file_exists( JPATH_COMPONENT .  DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'videoform.xml' );
$canState   = $user->authorise( 'core.edit.state', 'com_allvideoshare' );
$canDelete  = $user->authorise( 'core.delete', 'com_allvideoshare' );
$canDo      = AllVideoShareHelper::canDo();

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

<form action="<?php echo htmlspecialchars( Uri::getInstance()->toString() ); ?>" method="post" name="adminForm" class="avs uservideos mb-4" id="adminForm">
	<?php if ( $this->params->get( 'show_page_heading' ) ) : ?>	
		<div class="page-header">
			<h2 itemprop="headline">
				<?php if ( $this->escape( $this->params->get( 'page_heading' ) ) ) : ?>
					<?php echo $this->escape( $this->params->get( 'page_heading' ) ); ?>
				<?php else : ?>
					<?php echo $this->escape($this->params->get( 'page_title' ) ); ?>
				<?php endif; ?>
			</h2>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $this->filterForm ) ) { echo LayoutHelper::render( 'joomla.searchtools.default', array( 'view' => $this ) ); } ?>
	
	<div class="table-responsive">
		<table class="table table-striped" id="userVideoList">
			<thead>
				<tr>				
					<th class="text-center d-none d-md-table-cell">
						#
					</th>
					<th>
						<?php echo HTMLHelper::_( 'grid.sort',  'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder ); ?>
					</th>
					<th class="d-none d-md-table-cell">
						<?php echo HTMLHelper::_( 'grid.sort',  'COM_ALLVIDEOSHARE_VIDEOS_CATEGORIES', 'a.catid', $listDirn, $listOrder ); ?>
					</th>
					<th class="text-center d-none d-md-table-cell">
						<?php echo HTMLHelper::_( 'grid.sort',  'COM_ALLVIDEOSHARE_VIDEOS_VIEWS', 'a.views', $listDirn, $listOrder ); ?>
					</th>
					<th class="text-center d-none d-md-table-cell">
						<?php echo HTMLHelper::_( 'grid.sort',  'COM_ALLVIDEOSHARE_VIDEOS_FEATURED', 'a.featured', $listDirn, $listOrder ); ?>
					</th>
					<th class="text-center d-none d-md-table-cell">
						<?php echo HTMLHelper::_( 'grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder ); ?>
					</th>
					<th class="text-center d-none d-md-table-cell">
						<?php echo HTMLHelper::_( 'grid.sort',  'JGLOBAL_FIELD_ID_LABEL', 'a.id', $listDirn, $listOrder ); ?>
					</th>
					<?php if ( $canEdit || $canDelete ) : ?>
						<th class="text-center" style="min-width: 120px;">
							<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_ACTIONS' ); ?>
						</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $this->items as $i => $item ) : 
					$route = AllVideoShareRoute::getVideoRoute( $item );
					$item_link = Route::_( $route );
					?>
					<tr class="row<?php echo $i % 2; ?>">						
						<td class="text-center d-none d-md-table-cell">
							<?php echo (int) $this->pagination->limitstart + $i + 1; ?>
						</td>
						<td>
							<a href="<?php echo $item_link; ?>">
								<?php echo $this->escape( $item->title ); ?>
							</a>
						</td>
						<td class="d-none d-md-table-cell">
							<?php
							$categories = array();

							$route = AllVideoShareRoute::getCategoryRoute( $item->category );
							$item_link = Route::_( $route );

							$categories[] = sprintf(
								'<a href="%s">%s</a>',
								$item_link,
								$this->escape( $item->category->name )
							);
							
							if ( $canDo && $this->params->get( 'multi_categories' ) && ! empty( $item->categories ) ) {
								foreach ( $item->categories as $category ) {
									$route = AllVideoShareRoute::getCategoryRoute( $category );
									$item_link = Route::_( $route );

									$categories[] = sprintf(
										'<a href="%s">%s</a>',
										$item_link,
										$this->escape( $category->name )
									);
								}
							}

							echo implode( ', ', $categories );
							?>
						</td>
						<td class="text-center d-none d-md-table-cell">
							<?php echo (int) $item->views; ?>
						</td>
						<td class="text-center d-none d-md-table-cell">
							<?php if ( ! empty( $item->featured ) ) : ?>
								<?php echo Text::_( 'JYES' ); ?>
							<?php else : ?>
								<?php echo Text::_( 'JNO' ); ?>
							<?php endif; ?>
						</td>
						<td class="text-center d-none d-md-table-cell">
							<?php $class = ( $canState ) ? 'active' : 'disabled'; ?>

							<a class="btn btn-sm <?php echo $class; ?>" href="<?php echo ( $canState ) ? JRoute::_( 'index.php?option=com_allvideoshare&task=videoform.publish&id=' . $item->id . '&state=' . ( ( $item->state + 1 ) % 2 ), false, 2 ) : '#'; ?>">
								<?php if ( $item->state == 1 ): ?>
									<i class="icon-publish"></i>
								<?php else: ?>
									<i class="icon-unpublish"></i>
								<?php endif; ?>
							</a>
						</td>
						<td class="text-center d-none d-md-table-cell">
							<?php echo $item->id; ?>
						</td>
						<?php if ( $canEdit || $canDelete ) : ?>
							<td class="text-center">
								<?php if ( $canEdit ) : ?>
									<a href="<?php echo Route::_( 'index.php?option=com_allvideoshare&task=videoform.edit&id=' . $item->id, false, 2 ); ?>" class="btn btn-sm btn-primary" type="button"><i class="icon-edit" ></i></a>
								<?php endif; ?>

								<?php if ( $canDelete ) : ?>
									<a href="<?php echo Route::_( 'index.php?option=com_allvideoshare&task=videoform.remove&id=' . $item->id, false, 2 ); ?>" class="btn btn-sm btn-danger delete-button" type="button"><i class="icon-trash" ></i></a>
								<?php endif; ?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( empty( $this->items ) ) : ?>
			<p class="text-center text-muted">
				<?php echo Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_NOT_FOUND' ); ?>
			</p>
		<?php endif; ?>
	</div>

	<?php echo $this->pagination->getListFooter(); ?>
	
	<?php if ( $canCreate ) : ?>
		<a href="<?php echo Route::_( 'index.php?option=com_allvideoshare&task=videoform.edit&id=0', false, 0 ); ?>" class="btn btn-success btn-small"><i class="icon-plus"></i>
			<?php echo Text::_( 'COM_ALLVIDEOSHARE_ADD_NEW_VIDEO' ); ?>
		</a>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>

<?php
if ( $canDelete ) {
	$wa->addInlineScript("
		jQuery(document).ready(function() {
			jQuery( '.delete-button' ).click( deleteItem );
		});

		function deleteItem() {
			if ( ! confirm( \"" . Text::_( 'COM_ALLVIDEOSHARE_DELETE_MESSAGE' ) . "\" ) ) {
				return false;
			}
		}
	", [], [], ["jquery"]);
}
?>