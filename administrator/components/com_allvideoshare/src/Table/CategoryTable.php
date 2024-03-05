<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Table;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Access\Access;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Filesystem\File;
use \Joomla\CMS\Filesystem\Folder;
use \Joomla\CMS\Filter\OutputFilter;
use \Joomla\CMS\Helper\ContentHelper;
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table as Table;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Database\DatabaseDriver;
use \Joomla\Registry\Registry;
use \Joomla\Utilities\ArrayHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Class CategoryTable.
 *
 * @since 4.1.0
 */
class CategoryTable extends Table {	

	var $uploadPath = 'media/com_allvideoshare/categories';

	/**
	 * Constructor
	 *
	 * @param  JDatabase  &$db  A database connector object
	 * 
	 * @since  4.1.0
	 */
	public function __construct( DatabaseDriver $db ) {
		$this->typeAlias = 'com_allvideoshare.category';
		parent::__construct( '#__allvideoshare_categories', 'id', $db );
		$this->setColumnAlias( 'published', 'state' );		
	}

	/**
	 * Get the type alias for the history table
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.1.0
	 */
	public function getTypeAlias() {
		return $this->typeAlias;
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     Table:bind
	 * @since   4.1.0
	 * @throws  \InvalidArgumentException
	 */
	public function bind( $array, $ignore = '' ) {
		$date = Factory::getDate();
		$task = Factory::getApplication()->input->get( 'task' );		

		if ( $array['id'] == 0 || empty( $array['created_by'] ) ) {
			$array['created_by'] = Factory::getUser()->id;
		}

		if ( $array['id'] == 0 || empty( $array['modified_by'] ) ) {
			$array['modified_by'] = Factory::getUser()->id;
		}

		if ( $task == 'apply' || $task == 'save' ) {
			$array['modified_by'] = Factory::getUser()->id;
		}

		// Support for alias field: slug
		if ( empty( $array['slug'] ) ) {
			if ( empty( $array['name'] ) ) {
				$array['slug'] = OutputFilter::stringURLSafe( date( 'Y-m-d H:i:s' ) );
			} else {
				if ( Factory::getConfig()->get( 'unicodeslugs' ) == 1 )	{
					$array['slug'] = OutputFilter::stringURLUnicodeSlug( trim( $array['name'] ) );
				} else {
					$array['slug'] = OutputFilter::stringURLSafe( trim( $array['name'] ) );
				}
			}
		}

		// Support for parent field
		if ( empty( $array['parent'] ) ) {
			$array['parent'] = 0;
		}

		// Fallback to the OLD versions	
		$array['type'] = '';

		return parent::bind( $array, $ignore );
	}

	/**
	 * Overloaded check function
	 *
	 * @return  bool
	 * 
	 * @since   4.1.0
	 */
	public function check()	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if ( property_exists( $this, 'ordering' ) && $this->id == 0 ) {
			$this->ordering = self::getNextOrder();
		}
		
		// Check if slug is unique
		if ( ! $this->isUnique( 'slug' ) ) {
			$count = 0;
			$currentAlias = $this->slug;
			while ( ! $this->isUnique( 'slug' ) ) {
				$this->slug = $currentAlias . '-' . $count++;
			}
		}
		
		// Support file field: thumb
		$app = Factory::getApplication();
		$files = $app->input->files->get( 'jform', array(), 'raw' );
		$array = $app->input->get( 'jform', array(), 'ARRAY' );

		if ( $files['thumb']['size'] > 0 ) {
			// Deleting existing file
			$oldFile = AllVideoShareHelper::getFile( $this->id, $this->_tbl, 'thumb' );

			if ( file_exists( $oldFile ) && ! is_dir( $oldFile ) ) {
				unlink( $oldFile );
			}

			$this->thumb = "";
			$singleFile = $files['thumb'];			

			// Check if the server found any error
			$fileError = $singleFile['error'];
			$message = '';

			if ( $fileError > 0 && $fileError != 4 ) {
				switch ( $fileError ) {
					case 1:
						$message = Text::_( 'COM_ALLVIDEOSHARE_FILE_UPLOAD_ERROR_1' );
						break;
					case 2:
						$message = Text::_( 'COM_ALLVIDEOSHARE_FILE_UPLOAD_ERROR_2' );
						break;
					case 3:
						$message = Text::_( 'COM_ALLVIDEOSHARE_FILE_UPLOAD_ERROR_3' );
						break;
				}

				if ( $message != '' ) {
					$app->enqueueMessage( $message, 'warning' );
					return false;
				}
			} elseif ( $fileError == 4 ) {
				if ( isset( $array['thumb'] ) )	{
					$this->thumb = $array['thumb'];
				}
			} else {
				// Check for filetype
				$okMIMETypes = 'image/jpeg,image/png,image/gif';
				$validMIMEArray = explode( ',', $okMIMETypes );
				$fileMime = $singleFile['type'];
				$fileTemp = $singleFile['tmp_name'];

				if ( ! in_array( $fileMime, $validMIMEArray ) )	{
					$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_FILE_UPLOAD_ERROR_FILETYPE' ), 'warning' );
					return false;
				}

				if ( getimagesize( $fileTemp ) === FALSE ) {
					$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_FILE_UPLOAD_ERROR_FILETYPE' ), 'warning' );
					return false;
				}

				// Replace any special characters in the filename
				$filename = File::stripExt( $singleFile['name'] );
				$extension = File::getExt( $singleFile['name'] );
				$filename = preg_replace( "/[^A-Za-z0-9]/i", "-", $filename );
				$filename = $filename . '.' . $extension;				

				$date = HTMLHelper::_( 'date', 'now', 'Y-m', false );
				$filePath = JPATH_ROOT . '/' . $this->uploadPath . '/' . $date . '/' . $filename;		
				
				if ( ! Folder::exists( JPATH_ROOT . '/' . $this->uploadPath . '/' . $date . '/' ) ) {
					Folder::create( JPATH_ROOT . '/' . $this->uploadPath . '/' . $date . '/' );
				}

				if ( ! File::exists( $filePath ) ) {
					if ( ! File::upload( $fileTemp, $filePath ) )	{
						$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_FILE_UPLOAD_ERROR_MOVING_FILE' ), 'warning' );
						return false;
					}
				}

				$this->thumb = Uri::root() . $this->uploadPath . '/' . $date . '/' . $filename;
			}
		} else {
			if ( isset( $array['thumb'] ) )	{
				$this->thumb = $array['thumb'];
			}
		}

		return parent::check();
	}

	/**
     * Overrides Table::store to set modified data and user id.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   4.2.0
     */
    public function store( $updateNulls = true ) {
		return parent::store( true );
	}
	
    /**
     * Delete a record by id
     *
     * @param   mixed  $pk  Primary key value to delete. Optional
     *
     * @return  bool
	 * 
	 * @since   4.1.0
     */
    public function delete( $pk = null ) {
        $this->load( $pk );
        $result = parent::delete( $pk );
        
		if ( $result ) {
			// Is uploaded through our component interface?
			$isUploaded = strpos( $this->thumb, $this->uploadPath );

			if ( $isUploaded !== false ) {
				// Remove protocols
				$parts = explode( $this->uploadPath, $this->thumb );
				$file = JPATH_ROOT . '/' . $this->uploadPath . $parts[1];

				// Delete if the file exists
				if ( File::exists( $file ) ) {
					File::delete( $file );
				}

				// Delete the parent directory if empty
				$directory = pathinfo( $file, PATHINFO_DIRNAME );
				if ( Folder::exists( $directory ) ) {
					$files = array_diff( scandir( $directory ), array( '.', '..' ) );
					if ( empty( $files ) ) {
						Folder::delete( $directory );
					}
				}
			}
		}

        return $result;
    }

	/**
	 * Check if a field is unique
	 *
	 * @param   string  $field  Name of the field
	 *
	 * @return  bool  True if unique
	 * 
	 * @since   4.1.0
	 */
	private function isUnique( $field )	{
		$db = Factory::getDbo();
		$query = $db->getQuery( true );

		$query
			->select( $db->quoteName( $field ) )
			->from( $db->quoteName( $this->_tbl ) )
			->where( $db->quoteName( $field ) . ' = ' . $db->quote( $this->$field ) )
			->where( $db->quoteName( 'id' ) . ' <> ' . (int) $this->{$this->_tbl_key} );

		$db->setQuery( $query );
		$db->execute();

		return ( $db->getNumRows() == 0 ) ? true : false;
	}

}
