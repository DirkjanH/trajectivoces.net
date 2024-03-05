<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

define('ARKEDITOR_COMPONENT_VIEW', JUri::root() . 'administrator/components/com_arkeditor/views/cpanel');

//load style sheet

if($this->showSideBar)
{
    JFactory::getDocument()->addStyleSheet( ARKEDITOR_COMPONENT_VIEW . '/css/cpanel.css' );
}
// Define Modules that need assistance
$needsborder = array( 'mod_arkquickicon' );
?>
<?php if($this->showSideBar): ?>
<div class="row-fluid">
<?php if(!empty( $this->sidebar)): ?>
	<div id="sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="main-container" class="span10">
<?php else : ?>
	<div id="main-container" class="span12">
<?php endif;?>
		<div class="row-fluid">
			<div class="span6 pos-left">
				<?php
					foreach ($this->left as $i => $module)
					{
						$params	= new JRegistry( $module->params );
						$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
						$title 	= ( $module->title ) ? '<div class="module-title nav-header">' . $icon . $module->title . '</div>' : '';
						$class 	= ( in_array( $module->module, $needsborder ) ) ? 'row-striped' : '';
						echo '<div class="well well-small ' . $params->get( 'moduleclass_sfx' ) . '">';
						echo $title;
						echo '<div class="' . $class . '">';
						echo ARKModuleHelper::renderModule( $module );
						echo '</div>';
						echo '</div>';
					}
				?>
			</div>
			<div class="span6 pos-right">
				<?php
					foreach ($this->right as $i => $module)
					{
						$params	= new JRegistry( $module->params );
						$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
						$title 	= ( $module->title ) ? '<div class="module-title nav-header">' . $icon . $module->title . '</div>' : '';
						$class 	= ( in_array( $module->module, $needsborder ) ) ? 'row-striped' : '';
						echo '<div class="well well-small ' . $params->get( 'moduleclass_sfx' ) . '">';
						echo $title;
						echo '<div class="' . $class . '">';
						echo ARKModuleHelper::renderModule( $module );
						echo '</div>';
						echo '</div>';
					}
				?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12 pos-bottom">
				<?php
					foreach ($this->bottom as $i => $module)
					{
						$params	= new JRegistry( $module->params );
						$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
						$title 	= ( $module->title ) ? '<div class="module-title nav-header">' . $icon . $module->title . '</div>' : '';
						$class 	= ( in_array( $module->module, $needsborder ) ) ? 'row-striped' : '';
						echo '<div class="well well-small ' . $params->get( 'moduleclass_sfx' ) . '">';
						echo $title;
						echo '<div class="' . $class . '">';
						echo ARKModuleHelper::renderModule( $module );
						echo '</div>';
						echo '</div>';
					}
				?>
			</div>
		</div>
	</div>
</div>
<?php else : ?>
<style>
/*TODO: move to CSS file */
:root {
	--ark-dash-dark: var(--atum-text-dark);
	--ark-dash-light: rgba(0, 0, 0, 0.03);
}
	
#main-container {padding:0;}
#arkstats a 				{ color : #333333; }
#arkstats a:hover 			{ text-decoration : underline; }

#arkstats .badge
{
	display					: inline; /* Make stats module same height as info mod */
}

#arkstats a.badge-link:hover,
#arkstats a.badge-link:focus,
#arkstats a
{
	text-decoration			: none;
}

#arkstats a .badge
{
	border					: 1px solid transparent;
}

#arkstats a:hover .badge,
#arkstats a:focus .badge
{
	color 					: #666666;
	background-color		: #FFFFFF;
	border					: 1px solid #BBBBBB;
	box-shadow 				: 0px 0px 3px #BBBBBB inset;
}

#arkstats a .badge, #arkstats span.badge {
	background-color: #999;
}

#arkstats a .badge-info, #arkstats span.badge-info {
	background-color: #31708f;
}

#arkstats a .badge-inverse, #arkstats span.badge-inverse {
	background-color: #333;
}

#arkstats a .badge-success, #arkstats span.badge-success {
	background-color: #3c763d;
}

#arkstats a .badge-important, #arkstats span.badge-important {
	background-color: #a94442;
}

#ark-dashboard .quick-icons .quickicon a.dark-grey {
	
    color: var(--ark-dash-dark);
    border: 1px solid white;
}

#ark-dashboard .quick-icons .quickicon a.dark-grey:hover, #ark-dashboard .quick-icons .quickicon a.dark-grey:focus, #ark-dashboard .quick-icons .quickicon a.dark-grey:active {
    background: var(--ark-dash-light);
}

#ark-dashboard .quick-icons .quickicon a.dark-grey [class^=icon-]
{
  color: transparent;
  background: linear-gradient(var(--ark-dash-dark), var(--ark-dash-dark));
  -webkit-background-clip: text;
  background-clip: text;
}

#ark-dashboard .quick-icons .quickicon a.dark-grey:hover [class^=icon-], #ark-dashboard .quick-icons .quickicon a.dark-grey:focus [class^=icon-], #ark-dashboard .quick-icons .quickicon a.dark-grey:active [class^=icon-] {
	background: var(--ark-dash-dark);
	-webkit-background-clip: text;
    background-clip: text;
}


</style>
<div id="main-container" class="container-fluid">
	<div class="row">
		<div class="col">
				<?php
					foreach ($this->left as $i => $module)
					{
						$params	= new JRegistry( $module->params );
						$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
						$title 	= ( $module->title ) ? '<div class="module-title card-header">' . $icon . $module->title . '</div>' : '';
						$class 	= '';
						echo '<div class="card mb-3 ' . $params->get( 'moduleclass_sfx' ) . '">';
						echo $title;
						echo '<div class="card-body ' . $class . '">';
						echo ARKModuleHelper::renderModule( $module );
						echo '</div>';
						echo '</div>';
					}
				?>
		</div>
		<div class="col">
			<?php
				foreach ($this->right as $i => $module)
				{
					$params	= new JRegistry( $module->params );
					$icon	= ( $params->get( 'icon', false ) ) ? '<i class="icon-' . $params->get( 'icon' ) . '"></i>' . chr( 32 ) : '';
					$title 	= ( $module->title ) ? '<div class="module-title card-header">' . $icon . $module->title . '</div>' : '';
					$class 	= '';
					echo '<div class="card mb-3 ' . $params->get( 'moduleclass_sfx' ) . '">';
					echo $title;
					echo '<div class="card-body ' . $class . '">';
					echo ARKModuleHelper::renderModule( $module );
					echo '</div>';
					echo '</div>';
				}
			?>
		</div>
	</div>
	<div class="row">
		<div id="ark-dashboard" class="col pos-bottom">
			<div class="card mb-3">
				<div class="card-header module-title">
					<span aria-hidden="true" class="icon-cube"></span>&nbsp;Apps Dashboard
				</div>
				<div class="card-body">
					<nav class="quick-icons" aria-label="Quick Links System">
						<ul class="nav flex-wrap">
							<li class="quickicon quickicon-single col mb-3">
				
								<a href="index.php?option=com_arkeditor&view=list" class="dark-grey">
									<div class="quickicon-icon d-flex align-items-end big">
										<div aria-hidden="true" class="icon-plug"></div>
									</div>
									<div class="quickicon-name d-flex align-items-end">Plugins</div>
								</a>
							</li>
							<li class="quickicon quickicon-single col mb-3">
				
								<a href="index.php?option=com_arkeditor&view=toolbars" class="dark-grey">
									<div class="quickicon-icon d-flex align-items-end big">
										<div aria-hidden="true" class="icon-th"></div>
									</div>
									<div class="quickicon-name d-flex align-items-end">Layout Manager</div>
								</a>
							</li>
							<li class="quickicon quickicon-single col mb-3">
				
								<a href="index.php?option=com_arkeditor&view=cpanel&task=cpanel.editor"  class="dark-grey">
									<div class="quickicon-icon d-flex align-items-end big">
										<div aria-hidden="true" class="icon-edit"></div>
									</div>
									<div class="quickicon-name d-flex align-items-end">Editor</div>
								</a>
							</li>
						</ul>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>