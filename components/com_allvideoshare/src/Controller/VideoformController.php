<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Controller;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Controller\FormController;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Utilities\ArrayHelper;

/**
 * Class VideoformController.
 *
 * @since  4.1.0
 */
class VideoformController extends FormController {

	/**
	 * Method to check out an item for editing and redirect to the edit form.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function edit( $key = NULL, $urlVar = NULL )	{
		$app = Factory::getApplication();

		// Get the previous edit id (if any) and the current edit id
		$previousId = (int) $app->getUserState( 'com_allvideoshare.edit.video.id' );
		$editId     = $app->input->getInt( 'id', 0 );

		// Set the video id for the user to edit in the session
		$app->setUserState( 'com_allvideoshare.edit.video.id', $editId );

		// Get the model
		$model = $this->getModel( 'Videoform', 'Site' );

		// Check out the item
		if ( $editId ) {
			$model->checkout( $editId );
		}

		// Check in the previous user
		if ( $previousId ) {
			$model->checkin( $previousId );
		}

		// Redirect to the edit screen
		$this->setRedirect( Route::_( 'index.php?option=com_allvideoshare&view=videoform&layout=edit', false ) );
	}

	/**
	 * Method to save data.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   4.1.0
	 */
	public function save( $key = NULL, $urlVar = NULL )	{
		// Check for request forgeries
		$this->checkToken();

		// Initialise variables
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		$model = $this->getModel( 'Videoform', 'Site' );

		// Get the user data
		$data = Factory::getApplication()->input->get( 'jform', array(), 'array' );

		// Validate the posted data
		$form = $model->getForm();

		if ( ! $form ) {
			throw new \Exception( $model->getError(), 500 );
		}

		// Validate the posted data
		$data = $model->validate( $form, $data );

		// Check for errors
		if ( $data === false ) {
			// Get the validation messages
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user
			for ( $i = 0, $n = count( $errors ); $i < $n && $i < 3; $i++ ) {
				if ( $errors[ $i ] instanceof \Exception ) {
					$app->enqueueMessage( $errors[ $i ]->getMessage(), 'warning' );
				} else {
					$app->enqueueMessage( $errors[ $i ], 'warning' );
				}
			}

			$input = $app->input;
			$jform = $input->get( 'jform', array(), 'ARRAY' );

			// Save the data in the session
			$app->setUserState( 'com_allvideoshare.edit.video.data', $jform );

			// Redirect back to the edit screen
			$id = (int) $app->getUserState( 'com_allvideoshare.edit.video.id' );
			$this->setRedirect( Route::_( 'index.php?option=com_allvideoshare&view=videoform&layout=edit&id=' . $id, false ) );

			$this->redirect();
		}

		// Attempt to save the data
		$return = $model->save( $data );

		// Check for errors
		if ( $return === false ) {
			// Save the data in the session
			$app->setUserState( 'com_allvideoshare.edit.video.data', $data );

			// Redirect back to the edit screen
			$id = (int) $app->getUserState( 'com_allvideoshare.edit.video.id' );
			$this->setMessage( Text::sprintf( 'COM_ALLVIDEOSHARE_VIDEO_SAVE_FAILED', $model->getError() ), 'warning' );
			$this->setRedirect( Route::_( 'index.php?option=com_allvideoshare&view=videoform&layout=edit&id=' . $id, false ) );

			$this->redirect();
		}

		// Check in the profile
		if ( $return ) {
			$model->checkin( $return );
		}

		// Clear the profile id from the session
		$app->setUserState( 'com_allvideoshare.edit.video.id', null );

		// Redirect to the list screen
		$query = sprintf(
			'SELECT id FROM #__menu WHERE link=%s AND published=1 LIMIT 1',
			$db->quote( "index.php?option=com_allvideoshare&view=uservideos" )
		);

		$db->setQuery( $query );

		if ( $itemid = $db->loadResult() ) {
			$route = "index.php?Itemid=$itemid";
		} else {		
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();

			$route = ( empty( $item->link ) ? 'index.php?option=com_allvideoshare&view=uservideos' : $item->link );
		}

		$this->setMessage( Text::_( 'COM_ALLVIDEOSHARE_VIDEO_SAVED_SUCCESSFULLY' ) );
		$this->setRedirect( Route::_( $route, false ) );

		// Flush the data from the session
		$app->setUserState( 'com_allvideoshare.edit.video.data', null );
	}

	/**
	 * Publish or Unpublish a record
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   4.1.0
	 */
	public function publish() {
		// Initialise variables
		$app = Factory::getApplication();

		// Checking if the user can remove object
		$user = Factory::getUser();

		if ( $user->authorise('core.edit', 'com_allvideoshare') || $user->authorise( 'core.edit.state', 'com_allvideoshare' ) )	{
			$model = $this->getModel( 'Videoform', 'Site' );

			// Get the video data
			$id    = $app->input->getInt( 'id' );
			$state = $app->input->getInt( 'state' );

			// Attempt to save the data
			$return = $model->publish( $id, $state );

			// Check for errors
			if ( $return === false ) {
				$this->setMessage( Text::sprintf( 'COM_ALLVIDEOSHARE_VIDEO_SAVE_FAILED', $model->getError() ), 'warning' );
			} else {
				$this->setMessage( Text::_( 'COM_ALLVIDEOSHARE_VIDEO_SAVED_SUCCESSFULLY' ) );
			}

			// Clear the profile id from the session
			$app->setUserState( 'com_allvideoshare.edit.video.id', null );

			// Flush the data from the session
			$app->setUserState( 'com_allvideoshare.edit.video.data', null );

			// Redirect to the list screen			
			$menu = Factory::getApplication()->getMenu();
			$item = $menu->getActive();

			if ( ! $item ) {
				// If there isn't any menu item active, redirect to list view
				$this->setRedirect( Route::_( 'index.php?option=com_allvideoshare&view=uservideos', false ) );
			} else {
				$this->setRedirect( Route::_( 'index.php?Itemid='. $item->id, false ) );
			}
		} else {
			throw new \Exception( 500 );
		}
	}

	/**
	 * Check in record
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.1.0
	 */
	public function checkin() {
		// Check for request forgeries
		$this->checkToken( 'GET' );
		
		// Checking if the user can remove object
		$user = Factory::getUser();

		if ( $user->authorise( 'core.manage', 'com_allvideoshare' ) ) {
			$id = $this->input->getInt( 'id', 0 );
			
			$model = $this->getModel( 'Videoform', 'Site' );
			$return = $model->checkin( $id );

			if ( $return === false ) {
				// Checkin failed
				$message = Text::sprintf( 'JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError() );
				$this->setRedirect( Route::_( 'index.php?option=com_allvideoshare&view=uservideos', false ), $message, 'error' );
				return false;
			} else {
				// Checkin succeeded
				$message = Text::_( 'COM_ALLVIDEOSHARE_VIDEO_CHECKEDIN_SUCCESSFULLY' );
				$this->setRedirect( Route::_( 'index.php?option=com_allvideoshare&view=uservideos', false ), $message );
				return true;
			}
		} else {
			throw new \Exception( Text::_( 'JERROR_ALERTNOAUTHOR' ), 403 );
		}
	}

	/**
	 * Method to abort current operation
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function cancel( $key = NULL ) {
		$app = Factory::getApplication();

		// Get the current edit id
		$editId = (int) $app->getUserState( 'com_allvideoshare.edit.video.id' );

		// Get the model
		$model = $this->getModel( 'Videoform', 'Site' );

		// Check in the item
		if ( $editId ) {
			$model->checkin( $editId );
		}

		$menu = Factory::getApplication()->getMenu();
		$item = $menu->getActive();
		$url  = ( empty( $item->link ) ? 'index.php?option=com_allvideoshare&view=uservideos' : $item->link );
		$this->setRedirect( Route::_( $url, false ) );
	}

	/**
	 * Method to remove data
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function remove() {
		$app   = Factory::getApplication();
		$model = $this->getModel( 'Videoform', 'Site' );
		$pk    = $app->input->getInt( 'id' );

		// Attempt to save the data
		try	{
			$return = $model->delete( $pk );

			// Check in the profile
			$model->checkin( $return );

			// Clear the profile id from the session
			$app->setUserState( 'com_allvideoshare.edit.video.id', null );

			$menu = $app->getMenu();
			$item = $menu->getActive();
			$url = ( empty( $item->link ) ? 'index.php?option=com_allvideoshare&view=uservideos' : $item->link );

			// Redirect to the list screen
			$this->setMessage( Text::_( 'COM_ALLVIDEOSHARE_VIDEO_DELETED_SUCCESSFULLY' ) );
			$this->setRedirect( Route::_( $url, false ) );

			// Flush the data from the session
			$app->setUserState( 'com_allvideoshare.edit.video.data', null );
		} catch ( \Exception $e ) {
			$errorType = ( $e->getCode() == '404' ) ? 'error' : 'warning';
			$this->setMessage( $e->getMessage(), $errorType );
			$this->setRedirect( 'index.php?option=com_allvideoshare&view=uservideos' );
		}
	}
	
}
