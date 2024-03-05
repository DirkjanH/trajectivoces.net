<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Model;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;

/**
 * Class VideosModel.
 *
 * @since  4.1.0
 */
class VideosModel extends ListModel {

	/**
	* Constructor.
	*
	* @param  array  $config  An optional associative array of configuration settings.
	*
	* @see    JController
	* @since  4.1.0
	*/
	public function __construct( $config = array() ) {
		if ( empty( $config['filter_fields'] ) ) {
			$config['filter_fields'] = array(
				'id', 'a.id',				
				'title', 'a.title',
				'slug', 'a.slug',
				'catid', 'a.catid',
				'catids', 'a.catids',
				'type', 'a.type',
				'video', 'a.video',
				'hd', 'a.hd',
				'youtube', 'a.youtube',
				'vimeo', 'a.vimeo',
				'hls', 'a.hls',
				'thirdparty', 'a.thirdparty',
				'thumb', 'a.thumb',
				'description', 'a.description',
				'access', 'a.access',
				'featured', 'a.featured',
				'views', 'a.views',
				'ratings', 'a.ratings',
				'likes', 'a.likes',
				'dislikes', 'a.dislikes',			
				'user', 'a.user',
				'tags', 'a.tags',
				'metadescription', 'a.metadescription',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'created_date', 'a.created_date',
				'updated_date', 'a.updated_date'
			);
		}

		parent::__construct( $config );
	}	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	protected function populateState( $ordering = null, $direction = null )	{
		// List state information
		parent::populateState( 'a.id', 'DESC' );

		$context = $this->getUserStateFromRequest( $this->context . '.filter.search', 'filter_search' );
		$this->setState( 'filter.search', $context );

		// Split context into component and optional section
		$parts = FieldsHelper::extract( $context );

		if ( $parts ) {
			$this->setState( 'filter.component', $parts[0] );
			$this->setState( 'filter.section', $parts[1] );
		}
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string A store id.
	 *
	 * @since   4.1.0
	 */
	protected function getStoreId( $id = '' ) {
		// Compile the store id
		$id .= ':' . $this->getState( 'filter.search' );
		$id .= ':' . $this->getState( 'filter.state' );
		$id .= ':' . $this->getState( 'filter.catid' );
		$id .= ':' . $this->getState( 'filter.featured' );
		$id .= ':' . $this->getState( 'filter.user' );
		
		return parent::getStoreId( $id );		
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   4.1.0
	 */
	protected function getListQuery() {
		// Create a new query object
		$db    = $this->getDbo();
		$query = $db->getQuery( true );

		// Select the required fields from the table
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from( '`#__allvideoshare_videos` AS a' );
		
		// Join over the categories for the category name
		$query->select( "c.name AS category" );
		$query->join( "LEFT", "#__allvideoshare_categories AS c ON c.id=a.catid" );

		// Join over the users for the checked out user
		$query->select( "uc.name AS uEditor" );
		$query->join( "LEFT", "#__users AS uc ON uc.id=a.checked_out" );

		// Join over the user field 'created_by'
		$query->select( '`created_by`.name AS `created_by`' );
		$query->join( 'LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`' );

		// Join over the user field 'modified_by'
		$query->select( '`modified_by`.name AS `modified_by`' );
		$query->join( 'LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`' );

		// Join over the access level field 'access'
		$query->select( '`access`.title AS `access`' );
		$query->join( 'LEFT', '#__viewlevels AS access ON `access`.id = a.`access`' );		

		// Filter by published state
		$published = $this->getState( 'filter.state' );

		if ( is_numeric( $published ) )	{
			$query->where( 'a.state = ' . (int) $published );
		} elseif ( empty( $published ) ) {
			$query->where( '(a.state IN (0, 1))' );
		}

		// Filter by search in title
		$search = $this->getState( 'filter.search' );

		if ( ! empty( $search ) ) {
			if ( stripos( $search, 'id:' ) === 0 ) {
				$query->where( 'a.id = ' . (int) substr( $search, 3 ) );
			} else {
				$search = $db->Quote( '%' . $db->escape( $search, true ) . '%' );
				$query->where( '( a.title LIKE ' . $search . '  OR  a.description LIKE ' . $search . '  OR  a.tags LIKE ' . $search . '  OR  a.metadescription LIKE ' . $search . ' )' );
			}
		}		

		// Filtering catid
		$filter_catid = $this->state->get( 'filter.catid' );

		if ( is_numeric( $filter_catid ) ) {
			$query->where( '( a.catid = ' . (int) $filter_catid . '  OR  a.catids LIKE ' . $db->Quote( '% ' . (int) $filter_catid . ' %' ) . ' )' );
		}

		// Filtering featured
		$filter_featured = $this->state->get( 'filter.featured' );

		if ( is_numeric( $filter_featured ) ) {
			$query->where( 'a.featured = ' . (int) $filter_featured );
		}

		// Filtering user
		$filter_user = $this->state->get( 'filter.user' );

		if ( ! empty( $filter_user ) ) {
			$query->where( 'a.user = ' . $db->Quote( $db->escape( $filter_user ) ) );
		}

		// Add the list ordering clause
		$orderCol  = $this->state->get( 'list.ordering', 'a.id' );
		$orderDirn = $this->state->get( 'list.direction', 'DESC' );

		if ( $orderCol && $orderDirn ) {
			if ( $orderCol === 'a.ordering' || $orderCol === 'a.catid' ) {
				$ordering = [
					$db->quoteName( 'c.name' ) . ' ' . $db->escape( $orderDirn ),
					$db->quoteName( 'a.ordering' ) . ' ' . $db->escape( $orderDirn ),
				];
			} else {
				$ordering = $db->escape( $orderCol ) . ' ' . $db->escape( $orderDirn );
			}

			$query->order( $ordering );
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return  mixed  Array of data items on success, false on failure.
	 * 
	 * @since   4.1.0
	 */
	public function getItems() {
		$db = Factory::getDbo();

		$items = parent::getItems();
		
		foreach ( $items as $oneItem ) {
			if ( isset( $oneItem->catids ) )	{
				$values     = explode( ' ', trim( $oneItem->catids ) );
				$categories = array();

				foreach ( $values as $value ) {
					if ( ! empty( $value ) ) {						
						$query = 'SELECT id,name FROM #__allvideoshare_categories WHERE id=' . (int) $value;
						$db->setQuery( $query );
						$result = $db->loadObject();

						if ( $result )	{
							$categories[] = $result;
						}
					}
				}

				$oneItem->categories = $categories;
			}

			$oneItem->userid = $oneItem->user;
			if ( isset( $oneItem->user ) && ! empty( $oneItem->user ) ) {
				$query = 'SELECT name FROM #__users WHERE username=' . $db->Quote( $oneItem->user );
				$db->setQuery( $query );
				$result = $db->loadResult();

				$oneItem->user = ! empty( $result ) ? $result : $oneItem->user;
			}
		}

		return $items;
	}

	public function getUnApprovedCount() {
		$db = Factory::getDbo();
		$user = Factory::getUser();

		$query = 'SELECT COUNT(id) FROM #__allvideoshare_videos WHERE state = 0 AND user !=' . $db->Quote( $user->username );
		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}

}
