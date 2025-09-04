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
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;

/**
 * Class CategoriesModel.
 *
 * @since  4.1.0
 */
class CategoriesModel extends ListModel {

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
				'name', 'a.name',
				'slug', 'a.slug',
				'parent', 'a.parent',
				'thumb', 'a.thumb',
				'description', 'a.description',
				'access', 'a.access',				
				'metakeywords', 'a.metakeywords',
				'metadescription', 'a.metadescription',
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
		$app = Factory::getApplication();		

		$params = $app->getParams();		

		// List state information
		$orderby = $params->get( 'categories_orderby', '' );
		if ( empty( $orderby ) ) {
			$orderby = $params->get( 'orderby', 'title_asc' );
		}

		switch ( $orderby ) {
			case 'latest':
			case 'latest_by_date':
				$orderCol  = 'id';
				$orderDirn = 'DESC';
				break;
			case 'title_asc':
				$orderCol  = 'name';
				$orderDirn = 'ASC';	
				break;
			case 'title_desc':
				$orderCol  = 'name';
				$orderDirn = 'DESC';	
				break;			
			case 'ordering':
				$orderCol  = 'ordering';
				$orderDirn = 'ASC';
				break;	
			default:
				$orderCol  = 'RAND';
				$orderDirn = '';
		}

		$this->setState( 'list.ordering', $orderCol );
		$this->setState( 'list.direction', $orderDirn );

		$no_of_rows = (int) $params->get( 'no_of_rows', $params->get( 'rows', 3 ) );
		$no_of_cols = (int) $params->get( 'no_of_cols', $params->get( 'cols', 3 ) );		
		$limit = $no_of_rows * $no_of_cols;

		$this->setState( 'list.limit', $limit );

		$limitstart = $app->input->get( 'limitstart', 0, 'uint' );
		$this->setState( 'list.start', $limitstart );

		// Filter state information
		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$this->setState( 'filter.access', $user->getAuthorisedViewLevels() );
		}

		// Load the parameters
		$this->setState( 'params', $params );
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

		$query->from( $db->quoteName( '#__allvideoshare_categories', 'a' ) );
		$query->where( 'a.parent = 0' );		
		$query->where( 'a.state = 1' );

		// Filtering access
		$accessLevels = $this->state->get( 'filter.access', array() );
		if ( ! empty( $accessLevels ) ) {
			$query->where( 'a.access IN (' . implode( ',', $accessLevels ) . ')' );
		}
			
		// Add the list ordering clause
		$orderCol  = $this->state->get( 'list.ordering', 'a.ordering' );
		$orderDirn = $this->state->get( 'list.direction', 'ASC' );

		if ( $orderCol ) {
			if ( $orderCol == 'RAND' ) {
				$query->order( 'RAND()' );
			} else {
				if ( $orderDirn ) {
					$query->order( $db->escape( $orderCol . ' ' . $orderDirn ) );
				} else {
					$query->order( $db->escape( $orderCol ) );
				}
			}
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
		return $items;
	}
	
}
