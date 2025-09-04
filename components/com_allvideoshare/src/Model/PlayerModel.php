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
use \Joomla\CMS\MVC\Model\ItemModel;
use \Joomla\CMS\Object\CMSObject;
use \Joomla\CMS\Table\Table;
use \Joomla\Utilities\ArrayHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Class PlayerModel.
 *
 * @since  4.1.0
 */
class PlayerModel extends ItemModel {

	public $__item;	

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

		$id = $app->input->getInt( 'id' );
		if ( empty( $id ) ) {
			$id = $app->input->getInt( 'vid' );
		}

		$this->setState( 'video.id', $id );
	}

	/**
	 * Get player params
	 * 
	 * @return  object  The player params
	 * 
	 * @since   4.1.0
	 */
	public function getParams() {
		$app = Factory::getApplication();

		$params = AllVideoShareHelper::resolveParams( $app->getParams( 'com_allvideoshare' ) );

		// Overwrite params from player id
		$pid = $app->input->getInt( 'pid', 0 );

		if ( $pid > 0 ) {
			$db = Factory::getDbo();

			$table_name = str_replace( '#__', $db->getPrefix(), '#__allvideoshare_players' );
			
			if ( in_array( $table_name, $db->getTableList() ) ) {
				$query = $db->getQuery( true );

				$query
					->select( '*' )
					->from( $db->quoteName( '#__allvideoshare_players' ) )
					->where( $db->quoteName( 'state' ) . ' = 1' )
					->where( $db->quoteName( 'id' ) . ' = ' . (int) $pid );

				$db->setQuery( $query );
				$player = $db->loadObject();

				if ( ! empty( $player ) ) {
					$params->set( 'autoplay', $player->autostart );
					$params->set( 'loop', $player->loop );
					$params->set( 'volume', $player->volumelevel );
					$params->set( 'controlbar', $player->controlbar );
					$params->set( 'duration', $player->durationdock );
					$params->set( 'currenttime', $player->timerdock );
					$params->set( 'fullscreen', $player->fullscreendock );
					$params->set( 'quality', $player->hddock );
					$params->set( 'embed', $player->embeddock );
					$params->set( 'share', $player->sharedock );
					$params->set( 'adsource', $player->ad_engine );
					$params->set( 'preroll', $player->preroll );
					$params->set( 'postroll', $player->postroll );
					$params->set( 'adtagurl', $player->vast_url );
				}
			}
		} 

		// Overwrite from query params
		$properties = array( 'autoplay', 'loop', 'volume', 'muted', 'controlbar', 'playlarge', 'rewind', 'play', 'fastforward', 'progress', 'currenttime', 'duration', 'volumectrl', 'captions', 'quality', 'speed', 'pip', 'download', 'fullscreen', 'quality', 'embed', 'share', 'adsource', 'preroll', 'postroll', 'adtagurl' );

		foreach ( $properties as $property ) {
			switch ( $property ) {
				case 'adtagurl':
					$value = $app->input->get( $property, '', 'BASE64' );
					if ( ! empty( $value ) ) {
						$params->set( $property, base64_decode( $value ) );
					}
					break;
				case 'adsource':
					$value = $app->input->get( $property, '' );
					if ( ! empty( $value ) ) {
						$params->set( $property, $value );
					}
					break;
				default: 
					$value = $app->input->getInt( $property, -1 );
					if ( $value > -1 ) {
						$params->set( $property, $value );
					}
			}
		}

		return $params;
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function getItem( $id = null ) {
		if ( $this->__item === null ) {
			$app = Factory::getApplication();
			$db = Factory::getDbo();

			$this->__item = false;

			if ( empty( $id ) ) {
				$id = $this->getState( 'video.id' );
			}

			if ( $id > 0 ) {
				// Get a level row instance
				$table = $this->getTable();

				// Attempt to load the row
				if ( $table && $table->load( $id ) ) {
					// Convert the Table to a clean CMSObject
					$properties  = $table->getProperties( 1 );
					$this->__item = ArrayHelper::toObject( $properties, CMSObject::class );				
				}
			} else {
				$src = $app->input->get( 'src', '', 'BASE64' );	
				
				if ( ! empty( $src ) ) {
					$properties = array( 
						'id'    => 0,
						'title' => $app->get( 'sitename' )
					);

					$src = base64_decode( $src );
					
					if ( false !== strpos( $src, 'youtube.com' ) || false !== strpos( $src, 'youtu.be' ) ) {
						$properties['type'] = 'youtube';
						$properties['youtube'] = $src;
						$properties['thumb'] = AllVideoShareHelper::getYouTubeVideoImage( $src );
					} elseif ( false !== strpos( $src, 'vimeo.com' ) ) {
						$properties['type'] = 'vimeo';
						$properties['vimeo'] = $src;
						$properties['thumb'] = AllVideoShareHelper::getVimeoVideoImage( $src );
					} elseif ( strpos( $src, '.m3u8' ) !== false ) {
						$properties['type'] = 'hls';
						$properties['hls'] = $src;
					} elseif ( strpos( $src, '.mpd' ) !== false ) {
						$properties['type'] = 'dash';
						$properties['dash'] = $src;
					} else {
						$properties['type'] = 'general';
						$properties['video'] = $src;
					}

					$this->__item = ArrayHelper::toObject( $properties, CMSObject::class );
				}
			}

			if ( ! empty( $this->__item ) ) {
				$image = $app->input->get( 'image', '', 'BASE64' );	
				if ( ! empty( $image ) ) {
					$this->__item->thumb = base64_decode( $image );
				}
			}	

			if ( empty( $this->__item ) ) {
				return false;
			}
		}		

		return $this->__item;
	}

	/**
	 * Get an instance of Table class
	 *
	 * @param   string  $type    Name of the Table class to get an instance of.
	 * @param   string  $prefix  Prefix for the table class name. Optional.
	 * @param   array   $config  Array of configuration values for the Table object. Optional.
	 *
	 * @return  Table|bool  Table if success, false on failure.
	 * 
	 * @since   4.1.0
	 */
	public function getTable( $type = 'Video', $prefix = 'Administrator', $config = array() ) {
		return parent::getTable( $type, $prefix, $config );
	}
	
}
