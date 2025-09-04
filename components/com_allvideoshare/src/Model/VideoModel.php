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
use \Joomla\CMS\Object\CMSObject;
use \Joomla\CMS\Table\Table;
use \Joomla\Utilities\ArrayHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

/**
 * Class VideoModel.
 *
 * @since  4.1.0
 */
class VideoModel extends ListModel {

	public $__item = null;	

	/**
	 * Constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @see    JController
	 * @since  4.2.0
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
	 * @since   4.2.0
	 * @throws  Exception
	 */
	protected function populateState( $ordering = null, $direction = null )	{
		$app = Factory::getApplication();
		
		// Load the parameters
		$params = $app->getParams();
		$this->setState( 'params', $params );

		// List state information
		$orderby = $params->get( 'related_orderby', '' );
		if ( empty( $orderby ) ) {
			$orderby = $params->get( 'orderby', 'ordering' );
		}

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

		$no_of_rows = (int) $params->get( 'related_rows', 0 );
		if ( empty( $no_of_rows ) ) {
			$no_of_rows = (int) $params->get( 'no_of_rows', $params->get( 'rows', 3 ) );
		}		
		
		$no_of_cols = (int) $params->get( 'related_cols', 0 );
		if ( empty( $no_of_cols ) ) {
			$no_of_cols = (int) $params->get( 'no_of_cols', $params->get( 'cols', 3 ) );
		}

		$limit = $no_of_rows * $no_of_cols;

		$this->setState( 'list.limit', $limit );

		$limitstart = $app->input->get( 'limitstart', 0, 'uint' );
		$this->setState( 'list.start', $limitstart );

		// Filter state information
		$alias = $app->input->getString( 'slg' );
		$id = AllVideoShareQuery::getVideoIdByAlias( $alias );
		$this->setState( 'filter.videoid', $id );

		$catid = 0;
		$this->__item = $this->getVideo( $id );
		if ( $this->__item ) {
			$catid = $this->__item->catid;
		}
		$this->setState( 'filter.catid', $catid );

		$featured = $app->input->get( 'featured', 0, 'uint' );
		$this->setState( 'filter.featured', $featured );

		$this->setState( 'filter.published', 1 );

		if ( ! $params->get( 'show_noauth', 1 ) ) {
			$user = Factory::getUser();
			$this->setState( 'filter.access', $user->getAuthorisedViewLevels() );
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  DatabaseQuery
	 *
	 * @since   4.2.0
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

		// Filter by category
		$filter_catid = $this->state->get( 'filter.catid' );
		$query->where( '( catid = ' . (int) $filter_catid . '  OR  catids LIKE ' . $db->quote( '% ' . (int) $filter_catid . ' %' ) . ' )' );

		// Filtering featured
		$filter_featured = $this->state->get( 'filter.featured' );
		if ( $filter_featured > 0 ) {
			$query->where( 'a.featured = 1' );
		}		

		// Exclude current video
		$filter_videoid = $this->state->get( 'filter.videoid' );
		$query->where( 'a.id != ' . (int) $filter_videoid );

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
		return $items;
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
	public function getVideo( $id = null ) {
		if ( $this->__item === null ) {
			if ( empty( $id ) ) {
				$id = $this->getState( 'filter.videoid' );
			}

			if ( empty( $id ) ) {
				return null;
			}

			$db = Factory::getDbo();
			$user = Factory::getUser();							

			$query = $db->getQuery( true );

			$query->select( '*' );
			$query->from( $db->quoteName( '#__allvideoshare_videos' ) );
			$query->where( 'id = ' . (int) $id );
			$query->where( '(user = ' . $db->quote( $user->username ) . ' OR  state = 1)' );

        	$db->setQuery( $query );
			$this->__item = $db->loadObject();
		}	
		
		if ( ! empty( $this->__item ) ) {
			if ( $this->state->params->get( 'category_name' ) ) {
				if ( isset( $this->__item->catid ) && ! empty( $this->__item->catid ) ) {
					if ( $category = AllVideoShareQuery::getCategory( $this->__item->catid ) ) {
						$this->__item->category = $category;
					}
				}
		
				if ( $this->state->params->get( 'multi_categories' ) ) {
					if ( isset( $this->__item->catids ) && ! empty( $this->__item->catids ) ) {
						if ( ! is_array( $this->__item->catids ) ) {
							$this->__item->catids = explode( ' ', trim( $this->__item->catids ) );	
						}	
		
						$this->__item->categories = array();

						foreach ( $this->__item->catids as $catid ) {
							if ( isset( $this->__item->catid ) && $this->__item->catid == $catid ) {
								continue;
							}

							if ( $category = AllVideoShareQuery::getCategory( $catid ) ) {
								$this->__item->categories[] = $category;
							}
						}
					}
				}
			}
		}

		return $this->__item;
	}
	
}
