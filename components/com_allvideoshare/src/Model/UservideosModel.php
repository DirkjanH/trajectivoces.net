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
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

/**
 * Class UservideosModel.
 *
 * @since  4.1.0
 */
class UservideosModel extends ListModel {

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
		parent::populateState( 'a.created_date', 'DESC' );

		$app = Factory::getApplication();
		$list = $app->getUserState( $this->context . '.list' );

		$ordering  = $this->getUserStateFromRequest( $this->context . '.filter_order', 'filter_order', 'a.created_date' );
		$direction = strtoupper( $this->getUserStateFromRequest( $this->context . '.filter_order_Dir', 'filter_order_Dir', 'DESC' ) );
		
		if ( ! empty( $ordering ) || ! empty( $direction ) ) {
			$list['fullordering'] = $ordering . ' ' . $direction;
		}

		$app->setUserState( $this->context . '.list', $list );	

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

		$query->from( $db->quoteName( '#__allvideoshare_videos', 'a' ) );
			
		// Join over the users for the checked out user
		$query->select( 'uc.name AS uEditor' );
		$query->join( 'LEFT', '#__users AS uc ON uc.id = a.checked_out' );

		// Join over the created by field 'created_by'
		$query->join( 'LEFT', '#__users AS created_by ON created_by.id = a.created_by' );

		// Join over the created by field 'modified_by'
		$query->join( 'LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by' );		
		
		// Filtering state
		$query->where( '(a.state IN (0, 1))' );

		// Filtering user
		$user = Factory::getUser();
		$query->where( 'a.user = ' . $db->quote( $user->username ) );

		// Filter by search in title
		$search = $this->getState( 'filter.search' );

		if ( ! empty( $search ) ) {
			if ( stripos( $search, 'id:' ) === 0 ) {
				$query->where( 'a.id = ' . (int) substr( $search, 3 ) );
			} else {
				$search = $db->quote( '%' . $db->escape( $search, true ) . '%' );
				$query->where( '( a.title LIKE ' . $search . '  OR  a.description LIKE ' . $search . '  OR  a.tags LIKE ' . $search . '  OR  a.metadescription LIKE ' . $search . ' )' );
			}
		}			
			
		// Add the list ordering clause
		$orderCol  = $this->state->get( 'list.ordering', 'a.created_date' );
		$orderDirn = $this->state->get( 'list.direction', 'DESC' );

		if ( $orderCol && $orderDirn ) {
			$query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );
		}

		return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed  An array of data on success, false on failure.
	 * 
	 * @since   4.1.0
	 */
	public function getItems() {
		$items = parent::getItems();
		
		foreach ( $items as $item )	{
			if ( isset( $item->catid ) && ! empty( $item->catid ) ) {
				if ( $category = AllVideoShareQuery::getCategory( $item->catid ) ) {
					$item->category = $category;
				}
			}

			if ( isset( $item->catids ) && ! empty( $item->catids ) ) {
				$item->catids = explode( ' ', trim( $item->catids ) );
				$item->categories = array();

				foreach ( $item->catids as $catid ) {
					if ( isset( $item->catid ) && $item->catid == $catid ) {
						continue;
					}
					
					if ( $category = AllVideoShareQuery::getCategory( $catid ) ) {
						$item->categories[] = $category;
					}
				}
			}
		}

		return $items;
	}

}
