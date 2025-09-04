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
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

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
		$app = Factory::getApplication();
		
		$params = $app->getParams();

		// List state information
		$orderby = $params->get( 'orderby', 'latest_by_date' );

		switch ( $orderby ) {
			case 'latest':
				$orderCol  = 'id';
				$orderDirn = 'DESC';
				break;	
			case 'latest_by_date':
				$orderCol  = 'created_date';
				$orderDirn = 'DESC';
				break;	
			case 'title_asc':
				$orderCol  = 'title';
				$orderDirn = 'ASC';	
				break;
			case 'title_desc':
				$orderCol  = 'title';
				$orderDirn = 'DESC';	
				break;			
			case 'popular':
				$orderCol  = 'views';
				$orderDirn = 'DESC';
				break;
			case 'popular_by_ratings':
				$orderCol  = 'ratings';
				$orderDirn = 'DESC';
				break;
			case 'popular_by_likes':
				$orderCol  = 'likes';
				$orderDirn = 'DESC';
				break;
			case 'random':
				$orderCol  = 'RAND';
				$orderDirn = '';
				break;
			default:
				$orderCol  = 'ordering';
				$orderDirn = 'ASC';
				break;	
		}

		$this->setState( 'list.ordering', $orderCol );
		$this->setState( 'list.direction', $orderDirn );

		$format = $app->input->getWord( 'format' );

		if ( $format === 'feed' ) {
			$limit = (int) $params->get( 'feed_limit', $app->get( 'feed_limit', 20  ) );
		} else {
			$no_of_rows = (int) $params->get( 'no_of_rows', $params->get( 'rows', 3 ) );
			$no_of_cols = (int) $params->get( 'no_of_cols', $params->get( 'cols', 3 ) );		
			$limit = $no_of_rows * $no_of_cols;
		}

		$this->setState( 'list.limit', $limit );

		$limitstart = $app->input->get( 'limitstart', 0, 'uint' );
		$this->setState( 'list.start', $limitstart );

		// Filter state information
		$featured = $app->input->get( 'featured', 0, 'uint' );
		$this->setState( 'filter.featured', $featured );

		$this->setState( 'filter.published', 1 );

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

		$query->from( $db->quoteName( '#__allvideoshare_videos', 'a' ) );
			
		// Filter by published state
		$query->where( 'a.state = 1' );

		// Filtering featured
		$filter_featured = $this->state->get( 'filter.featured' );

		if ( $filter_featured > 0 ) {
			$query->where( 'a.featured = 1' );
		}		
		
		// Filter by access levels
		$accessLevels = $this->state->get( 'filter.access', array() );
		if ( ! empty( $accessLevels ) ) {
			$query->where( 'a.access IN (' . implode( ',', $accessLevels ) . ')' );
		}
		
		// Add the list ordering clause
		$orderCol  = $this->state->get( 'list.ordering', 'a.created_date' );
		$orderDirn = $this->state->get( 'list.direction', 'DESC' );

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

		foreach ( $items as $item ) {
			if ( isset( $item->catid ) && ! empty( $item->catid ) ) {
				if ( $category = AllVideoShareQuery::getCategory( $item->catid ) ) {
					$item->category = $category;
				}
			}
	
			if ( $this->state->params->get( 'category_name' ) && $this->state->params->get( 'multi_categories' ) ) {
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
		}
		
		return $items;
	}

}
