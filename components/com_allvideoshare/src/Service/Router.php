<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Service;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Application\SiteApplication;
use \Joomla\CMS\Categories\Categories;
use \Joomla\CMS\Categories\CategoryFactoryInterface;
use \Joomla\CMS\Categories\CategoryInterface;
use \Joomla\CMS\Component\Router\RouterViewConfiguration;
use \Joomla\CMS\Component\Router\RouterView;
use \Joomla\CMS\Component\Router\Rules\StandardRules;
use \Joomla\CMS\Component\Router\Rules\MenuRules;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Menu\AbstractMenu;
use \Joomla\Database\DatabaseInterface;
use \MrVinoth\Component\AllVideoShare\Site\Service\AllVideoShareNomenuRules as NomenuRules;

/**
 * Class AllVideoShareRouter.
 *
 * @since  4.1.0
 */
class Router extends RouterView {

	private $noIDs;

	/**
	 * The category factory
	 *
	 * @var    CategoryFactoryInterface
	 *
	 * @since  4.1.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var    array
	 *
	 * @since  4.1.0
	 */
	private $categoryCache = [];

	public function __construct( SiteApplication $app, AbstractMenu $menu, CategoryFactoryInterface $categoryFactory, DatabaseInterface $db ) {
		$params = Factory::getApplication()->getParams( 'com_allvideoshare' );
		$this->noIDs = (bool) $params->get( 'sef_ids' );
		$this->categoryFactory = $categoryFactory;
		
		$categories = new RouterViewConfiguration( 'categories' );
		$this->registerView( $categories );
			
		$category = new RouterViewConfiguration( 'category' );
		$category->setKey( 'id' )->setParent( $categories );
		$this->registerView( $category );

		$videos = new RouterViewConfiguration( 'videos' );
		$this->registerView( $videos );

		$video = new RouterViewConfiguration( 'video' );
		$video->setKey( 'id' )->setParent( $videos );
		$this->registerView( $video );

		$user = new RouterViewConfiguration( 'user' );
		$this->registerView( $user );

		$uservideos = new RouterViewConfiguration( 'uservideos' );
		$this->registerView( $uservideos );

		$videoform = new RouterViewConfiguration( 'videoform' );
		$videoform->setKey( 'id' );
		$this->registerView( $videoform );

		$youtube = new RouterViewConfiguration( 'youtube' );
		$this->registerView( $youtube );		

		parent::__construct( $app, $menu );

		$this->attachRule( new MenuRules( $this ) );
		$this->attachRule( new StandardRules( $this ) );
		$this->attachRule( new NomenuRules( $this ) );
	}
	
	/**
	 * Method to get the segment(s) for an video
	 *
	 * @param   string  $id     ID of the video to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 * 
	 * @since   4.1.0
	 */
	public function getVideoSegment( $id, $query ) {
		if ( ! strpos( $id, ':' ) ) {
			$db = Factory::getDbo();
			$dbquery = $db->getQuery( true );

			$dbquery->select( $dbquery->qn( 'slug' ) )
				->from( $dbquery->qn( '#__allvideoshare_videos' ) )
				->where( 'id = ' . $dbquery->q( $id ) );

			$db->setQuery( $dbquery );

			$id .= ':' . $db->loadResult();
		}

		if ( $this->noIDs ) {
			list( $void, $segment ) = explode( ':', $id, 2 );
			return array( $void => $segment );
		}

		return array( (int) $id => $id );
	}

	/**
	 * Method to get the segment(s) for an videoform
	 *
	 * @param   string  $id     ID of the videoform to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 * 
	 * @since   4.1.0
	 */
	public function getVideoformSegment( $id, $query ) {
		return $this->getVideoSegment( $id, $query );
	}

	/**
	 * Method to get the segment(s) for an category
	 *
	 * @param   string  $id     ID of the category to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 * 
	 * @since   4.1.0
	 */
	public function getCategorySegment( $id, $query ) {
		if ( ! strpos( $id, ':' ) ) {
			$db = Factory::getDbo();
			$dbquery = $db->getQuery( true );

			$dbquery->select( $dbquery->qn( 'slug' ) )
				->from( $dbquery->qn( '#__allvideoshare_categories' ) )
				->where( 'id = ' . $dbquery->q( $id ) );

			$db->setQuery( $dbquery );

			$id .= ':' . $db->loadResult();
		}

		if ( $this->noIDs )	{
			list( $void, $segment ) = explode( ':', $id, 2 );
			return array( $void => $segment );
		}

		return array( (int) $id => $id );
	}
	
	/**
	 * Method to get the segment(s) for an video
	 *
	 * @param   string  $segment  Segment of the video to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 * 
	 * @since   4.1.0
	 */
	public function getVideoId( $segment, $query ) {
		if ( $this->noIDs )	{
			$db = Factory::getDbo();
			$dbquery = $db->getQuery( true );

			$dbquery->select( $dbquery->qn( 'id' ) )
				->from( $dbquery->qn( '#__allvideoshare_videos' ) )
				->where( 'slug = ' . $dbquery->q( $segment ) );

			$db->setQuery( $dbquery );

			return (int) $db->loadResult();
		}

		return (int) $segment;
	}

	/**
	 * Method to get the segment(s) for an videoform
	 *
	 * @param   string  $segment  Segment of the videoform to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 * 
	 * @since   4.1.0
	 */
	public function getVideoformId( $segment, $query ) {
		return $this->getVideoId( $segment, $query );
	}

	/**
	 * Method to get the segment(s) for an category
	 *
	 * @param   string  $segment  Segment of the category to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 * 
	 * @since   4.1.0
	 */
	public function getCategoryId( $segment, $query ) {
		if ( $this->noIDs )	{
			$db = Factory::getDbo();
			$dbquery = $db->getQuery( true );

			$dbquery->select( $dbquery->qn( 'id' ) )
				->from( $dbquery->qn( '#__allvideoshare_categories' ) )
				->where( 'slug = ' . $dbquery->q( $segment ) );

			$db->setQuery( $dbquery );

			return (int) $db->loadResult();
		}

		return (int) $segment;
	}

	/**
	 * Method to get categories from cache
	 *
	 * @param   array  $options   The options for retrieving categories
	 *
	 * @return  CategoryInterface  The object containing categories
	 *
	 * @since   4.1.0
	 */
	private function getCategories( array $options = [] ): CategoryInterface {
		$key = serialize( $options );

		if ( ! isset( $this->categoryCache[ $key ] ) ) {
			$this->categoryCache[ $key ] = $this->categoryFactory->createCategory( $options );
		}

		return $this->categoryCache[ $key ];
	}

}
