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
 * Class AdvertisementsModel.
 *
 * @since  4.1.0
 */
class AdvertisementsModel extends ListModel {

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
				'type', 'a.type',
				'video', 'a.video',
				'link', 'a.link',
				'impressions', 'a.impressions',
				'clicks', 'a.clicks',
				'state', 'a.state',
				'ordering', 'a.ordering',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by'
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
		// Compile the store id.
		$id .= ':' . $this->getState( 'filter.search' );
		$id .= ':' . $this->getState( 'filter.state ');
		$id .= ':' . $this->getState( 'filter.type ');
		
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
		$query->from( '`#__allvideoshare_adverts` AS a' );
		
		// Join over the users for the checked out user
		$query->select( "uc.name AS uEditor" );
		$query->join( "LEFT", "#__users AS uc ON uc.id=a.checked_out" );

		// Join over the user field 'created_by'
		$query->select( '`created_by`.name AS `created_by`' );
		$query->join( 'LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`' );

		// Join over the user field 'modified_by'
		$query->select( '`modified_by`.name AS `modified_by`' );
		$query->join( 'LEFT', '#__users AS `modified_by` ON `modified_by`.id = a.`modified_by`' );		

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
				$query->where( '( a.title LIKE ' . $search . ' )' );
			}
		}		

		// Filtering type
		$filter_type = $this->state->get( 'filter.type' );

		if ( ! empty( $filter_type ) ) {
			$query->where( 'a.type = ' . $db->Quote( $db->escape( $filter_type ) ) );
		}

		// Add the list ordering clause
		$orderCol  = $this->state->get( 'list.ordering', 'a.id' );
		$orderDirn = $this->state->get( 'list.direction', 'DESC' );

		if ( $orderCol && $orderDirn ) {
			$query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return  mixed  Array of data items on success, false on failure.
	 * 
	 * @since  4.1.0
	 */
	public function getItems() {
		$items = parent::getItems();
		
		foreach ( $items as $oneItem ) {
			$oneItem->type = ! empty( $oneItem->type ) ? Text::_( 'COM_ALLVIDEOSHARE_FORM_OPTION_ADVERTISEMENT_TYPE_' . strtoupper( $oneItem->type ) ) : '';
		}

		return $items;
	}
	
}
