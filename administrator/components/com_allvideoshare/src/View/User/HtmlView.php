<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\View\User;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Toolbar\ToolbarHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * View class for a single Video.
 *
 * @since  4.1.0
 */
class HtmlView extends BaseHtmlView {

	protected $state;

	protected $item;

	protected $form;

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
		$this->state = $this->get( 'State' );
		$this->item  = $this->get( 'Item' );
		$this->form  = $this->get( 'Form' );
		$this->canDo = AllVideoShareHelper::canDo();
		$this->params = ComponentHelper::getParams( 'com_allvideoshare' );	

		// Check for errors
		if ( count( $errors = $this->get( 'Errors' ) ) ) {
			throw new \Exception( implode( "\n", $errors ) );
		}

		$this->addToolbar();
		parent::display( $tpl );
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	protected function addToolbar()	{
		Factory::getApplication()->input->set( 'hidemainmenu', true );

		$user  = Factory::getUser();
		$isNew = ( $this->item->id == 0 );

		if ( isset( $this->item->checked_out ) ) {
			$checkedOut = ! ( $this->item->checked_out == 0 || $this->item->checked_out == $user->get( 'id' ) );
		} else {
			$checkedOut = false;
		}

		$canDo = AllVideoShareHelper::getActions();

		$componentName = Text::_( 'COM_ALLVIDEOSHARE' );
		$title = $isNew ? Text::sprintf( 'COM_ALLVIDEOSHARE_VIDEO_ADD_TITLE', $componentName ) : Text::sprintf( 'COM_ALLVIDEOSHARE_VIDEO_EDIT_TITLE', $componentName );
		ToolbarHelper::title( $title , 'jcc fa-film' );

		// If not checked out, can save the item
		if ( ! $checkedOut && ( $canDo->get( 'core.edit' ) || ( $canDo->get( 'core.create' ) ) ) ) {
			ToolbarHelper::apply( 'user.apply', 'JTOOLBAR_APPLY' );
			ToolbarHelper::save( 'user.save', 'JTOOLBAR_SAVE' );
		}

		if ( empty( $this->item->id ) ) {
			ToolbarHelper::cancel( 'user.cancel', 'JTOOLBAR_CANCEL' );
		} else {
			ToolbarHelper::cancel( 'user.cancel', 'JTOOLBAR_CLOSE' );
		}
	}

}
