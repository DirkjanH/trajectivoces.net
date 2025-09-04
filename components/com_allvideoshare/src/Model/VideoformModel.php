<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Model;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\FormModel;
use \Joomla\CMS\Object\CMSObject;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Table\Table;
use \Joomla\Utilities\ArrayHelper;

/**
 * Class VideoformModel.
 *
 * @since  4.1.0
 */
class VideoformModel extends FormModel {

	private $item = null;	

	/**
	 * Method to get the table
	 *
	 * @param   string $type   Name of the Table class
	 * @param   string $prefix Optional prefix for the table class name
	 * @param   array  $config Optional configuration array for Table object
	 *
	 * @return  Table|boolean Table if found, boolean false on failure
	 * 
	 * @since   4.1.0
	 */
	public function getTable( $type = 'Video', $prefix = 'Administrator', $config = array() ) {
		return parent::getTable( $type, $prefix, $config );
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	protected function populateState() {
		$app = Factory::getApplication();

		// Load state from the request userState on edit or from the passed variable on default
		if ( $app->input->get( 'layout' ) == 'edit' ) {
			$id = $app->getUserState( 'com_allvideoshare.edit.video.id' );
		} else {
			$id = $app->input->get( 'id' );
			$app->setUserState( 'com_allvideoshare.edit.video.id', $id );
		}

		$this->setState( 'video.id', $id );
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return  Object|boolean  Object on success, false on failure.
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function getItem( $id = null ) {
		if ( $this->item === null )	{
			$app = Factory::getApplication();

			$this->item = false;

			if ( empty( $id ) )	{
				$id = $this->getState( 'video.id' );
			}

			// Get a level row instance
			$table = $this->getTable();

			if ( $table !== false && $table->load( $id ) && ! empty( $table->id ) )	{
				$user = Factory::getUser();
				$id   = $table->id;			

				$canEdit = $user->authorise( 'core.edit', 'com_allvideoshare' ) || $user->authorise( 'core.create', 'com_allvideoshare' );

				if ( ! $canEdit && $user->authorise( 'core.edit.own', 'com_allvideoshare' ) ) {
					$canEdit = $user->id == $table->created_by;
				}

				if ( ! $canEdit ) {
					$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_NO_PERMISSION_EDIT' ), 'warning' );
					$app->redirect( Route::_( 'index.php?option=com_allvideoshare&view=videoform&layout=edit&id=' . $id, false ) );

					return false;
				}

				// Convert the Table to a clean CMSObject
				$properties = $table->getProperties( 1 );
				$this->item = ArrayHelper::toObject( $properties, CMSObject::class );				
			}
		}

		return $this->item;
	}		

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form     A Form object on success, false on failure
	 *
	 * @since   4.1.0
	 */
	public function getForm( $data = array(), $loadData = true ) {
		// Get the form
		$form = $this->loadForm(
			'com_allvideoshare.video', 
			'videoform', 
			array(
				'control'   => 'jform',
				'load_data' => $loadData
			)
		);

		if ( empty( $form ) ) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 * 
	 * @since   4.1.0
	 */
	protected function loadFormData() {
		$data = Factory::getApplication()->getUserState( 'com_allvideoshare.edit.video.data', array() );

		if ( empty( $data ) ) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data The form data
	 *
	 * @return  bool
	 *
	 * @throws  Exception
	 * @since   4.1.0
	 */
	public function save( $data ) {
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		$id    = ( ! empty( $data['id'] ) ) ? $data['id'] : (int) $this->getState( 'video.id' );
		$state = ( ! empty( $data['state'] ) ) ? 1 : 0;		
		
		if ( $id ) {
			// Check the user can edit this item
			$authorised = $user->authorise( 'core.edit', 'com_allvideoshare' ) || $authorised = $user->authorise( 'core.edit.own', 'com_allvideoshare' );
		} else {
			// Check the user can create new items in this section
			$authorised = $user->authorise( 'core.create', 'com_allvideoshare' );
		}

		if ( $authorised !== true ) {
			$menu = $app->getMenu();
			$item = $menu->getActive();
			$url  = ( empty( $item->link ) ? 'index.php?option=com_allvideoshare&view=videoform' : $item->link );

			$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_NO_PERMISSION_ADD' ), 'warning' );
			$app->redirect( Route::_( $url, false ) );

			return false;
		}

		$table = $this->getTable();		

		if ( $table->save( $data ) === true ) {
			return $table->id;
		} else {
			return false;
		}		
	}

	/**
	 * Publish or Unpublish the video
	 *
	 * @param   int $id    Item id
	 * @param   int $state Publish state
	 *
	 * @return  boolean
	 * 
	 * @since   4.1.0
	 */
	public function publish( $id, $state ) {
		$table = $this->getTable();
				
		$table->load( $id );
		$table->state = $state;

		return $table->store();				
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   4.1.0
	 */
	public function checkin( $id = null ) {
		// Get the id
		$id = ( ! empty( $id ) ) ? $id : (int) $this->getState( 'video.id' );
		
		if ( $id ) {
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in
			if ( method_exists( $table, 'checkin' ) ) {
				if ( ! $table->checkin( $id ) )	{
					return false;
				}
			}
		}

		return true;		
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   4.1.0
	 */
	public function checkout( $id = null ) {
		// Get the user id
		$id = ( ! empty( $id ) ) ? $id : (int) $this->getState( 'video.id' );
		
		if ( $id ) {
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object
			$user = Factory::getUser();

			// Attempt to check the row out
			if ( method_exists( $table, 'checkout' ) ) {
				if ( ! $table->checkout( $user->get( 'id' ), $id ) ) {
					return false;
				}
			}
		}

		return true;		
	}

	/**
	 * Method to delete data
	 *
	 * @param   int  $pk  Item primary key
	 *
	 * @return  int  The id of the deleted item
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function delete( $pk ) {
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		$menu = $app->getMenu();
		$item = $menu->getActive();
		$url  = ( empty( $item->link ) ? 'index.php?option=com_allvideoshare&view=uservideos' : $item->link );
		
		if ( empty( $pk ) )	{
			$pk = (int) $this->getState( 'video.id' );
		}

		if ( $pk == 0 || $this->getItem( $pk ) == null ) {
			$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_ITEM_DOESNT_EXIST' ), 'warning' );
			$app->redirect( Route::_( $url, false ) );

			return false;
		}

		if ( $user->authorise( 'core.delete', 'com_allvideoshare' ) !== true ) {
			$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_NO_PERMISSION_DELETE' ), 'warning' );
			$app->redirect( Route::_( $url, false ) );

			return false;
		}

		$table = $this->getTable();

		if ( $table->delete( $pk ) !== true ) {
			$app->enqueueMessage( Text::sprintf( 'COM_ALLVIDEOSHARE_VIDEO_DELETE_FAILED', $pk ), 'warning' );
			$app->redirect( Route::_( $url, false ) );

			return false;
		}

		return $pk;		
	}

	/**
	 * Check if data can be saved
	 *
	 * @return  bool
	 * 
	 * @since   4.1.0
	 */
	public function getCanSave() {
		$table = $this->getTable();
		return $table !== false;
	}
	
}
