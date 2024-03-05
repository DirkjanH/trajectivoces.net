<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\View\Videos;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Form\Form;
use \Joomla\CMS\HTML\Helpers\Sidebar;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Toolbar\Toolbar;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \Joomla\Component\Content\Administrator\Extension\ContentComponent;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * View class for a list of videos.
 *
 * @since  4.1.0
 */
class HtmlView extends BaseHtmlView {

	protected $state;

	protected $items;

	protected $pagination;	

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function display( $tpl = null ) {
		$app = Factory::getApplication();

		$this->state = $this->get( 'State' );
		$this->items = $this->get( 'Items' );
		$this->pagination = $this->get( 'Pagination' );

		if ( empty( $this->items ) && $this->get( 'IsEmptyState' ) ) {
			$this->setLayout( 'emptystate' );
		}
		
		$this->params = ComponentHelper::getParams( 'com_allvideoshare' );
		$this->filterForm = $this->get( 'FilterForm' );
		$this->activeFilters = $this->get( 'ActiveFilters' );		

		// Check for errors
		if ( count( $errors = $this->get( 'Errors' ) ) ) {
			throw new \Exception( implode( "\n", $errors ) );
		}

		if ( $count = $this->get( 'UnApprovedCount' ) ) {
			if ( $count == 1 ) {
				$app->enqueueMessage(
					Text::sprintf(
						'COM_ALLVIDEOSHARE_N_ITEMS_PENDING_APPROVAL_1', 
						$count
					),
					'info'
				);
			} else {
				$app->enqueueMessage(
					Text::sprintf(
						'COM_ALLVIDEOSHARE_N_ITEMS_PENDING_APPROVAL', 
						$count
					),
					'info'
				);
			}
		}

		$this->addToolbar();

		$this->sidebar = Sidebar::render();
		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	protected function addToolbar() {
		$state = $this->get( 'State' );
		$canDo = AllVideoShareHelper::getActions();

		ToolbarHelper::title( Text::_( 'COM_ALLVIDEOSHARE_TITLE_VIDEOS' ), 'jcc fa-film' );

		$toolbar = Toolbar::getInstance( 'toolbar' );

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Video';

		if ( file_exists( $formPath ) )	{
			if ( $canDo->get( 'core.create' ) )	{
				$toolbar->addNew( 'video.add' );
			}
		}

		if ( $canDo->get( 'core.edit.state' ) || count( $this->transitions ) ) {
			$dropdown = $toolbar->dropdownButton( 'status-group' )
				->text( 'JTOOLBAR_CHANGE_STATUS' )
				->toggleSplit( false )
				->icon( 'fas fa-ellipsis-h' )
				->buttonClass( 'btn btn-action' )
				->listCheck( true );

			$childBar = $dropdown->getChildToolbar();

			if ( isset( $this->items[0]->state ) ) {
				$childBar->publish( 'videos.publish' )->listCheck( true );
				$childBar->unpublish( 'videos.unpublish' )->listCheck( true );
				$childBar->archive( 'videos.archive' )->listCheck( true );
			} elseif ( isset( $this->items[0] ) ) {
				// If this component does not use state then show a direct delete button as we can not trash
				$toolbar->delete( 'videos.delete' )
					->text( 'JTOOLBAR_EMPTY_TRASH' )
					->message( 'JGLOBAL_CONFIRM_DELETE' )
					->listCheck( true );
			}

			if ( isset( $this->items[0]->checked_out ) ) {
				$childBar->checkin( 'videos.checkin' )->listCheck( true );
			}

			if ( isset( $this->items[0]->state ) ) {
				$childBar->trash( 'videos.trash' )->listCheck( true );
			}
		}		

		// Show trash and delete for components that uses the state field
		if ( isset( $this->items[0]->state ) ) {
			if ( $this->state->get( 'filter.state' ) == ContentComponent::CONDITION_TRASHED && $canDo->get( 'core.delete' ) ) {
				$toolbar->delete( 'videos.delete' )
					->text( 'JTOOLBAR_EMPTY_TRASH' )
					->message( 'JGLOBAL_CONFIRM_DELETE' )
					->listCheck( true );
			}
		}

		if ( $canDo->get( 'core.admin' ) ) {
			$toolbar->preferences( 'com_allvideoshare' );
		}

		// Set sidebar action
		Sidebar::setAction( 'index.php?option=com_allvideoshare&view=videos' );
	}
	
	/**
	 * Method to order fields 
	 *
	 * @return  void 
	 * 
	 * @since   4.1.0
	 */
	protected function getSortFields() {
		return array(
			'a.`id`' => Text::_( 'JGRID_HEADING_ID' ),
			'a.`ordering`' => Text::_( 'JGRID_HEADING_ORDERING' ),
			'a.`title`' => Text::_( 'JGLOBAL_TITLE' ),
			'a.`views`' => Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_VIEWS' ),
			'a.`featured`' => Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_FEATURED' ),
			'a.`state`' => Text::_( 'JSTATUS' ),
			'a.`user`' => Text::_( 'COM_ALLVIDEOSHARE_VIDEOS_USER' ),
		);
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return  bool
	 * 
	 * @since   4.1.0
	 */
	public function getState( $state ) {
		return isset( $this->state->{$state} ) ? $this->state->{$state} : false;
	}

}
