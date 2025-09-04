<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

require_once( JPATH_ROOT . '/components/com_komento/bootstrap.php' );

class KomentoComAllVideoShare extends KomentoExtension {

	public $_item;

	public $_map = array(
		'id'         => 'id',
		'title'      => 'title',
		'hits'       => 'views',
		'created_by' => 'user',
		'catid'      => 'catid',
		'permalink'  => 'permalink_field'
	);

	public function __construct( $component ) {
		$lang = Factory::getLanguage();
		$lang->load( 'com_allvideoshare' );

		parent::__construct( $component );
	}

	public function load( $cid ) {
		static $instances = array();

		if ( ! isset( $instances[ $cid ] ) ) {
			$app = Factory::getApplication();	
			$db  = Factory::getDbo();
			
			$cid = (int) $cid;
			
			$query = 'SELECT * FROM #__allvideoshare_videos WHERE id=' . $cid;			
			$db->setQuery( $query );
			$this->_item = $db->loadObject();	

			// Generate link for this video
			$this->_item->permalink_field = AllVideoShareRoute::getVideoRoute( $this->_item );

			// Call the prepareLink function and leave the rest to us
			$this->_item->permalink_field = $this->prepareLink( $this->_item->permalink_field );

			$instances[ $cid ] = $this->_item;
		}

		$this->_item = $instances[ $cid ];

		return $this;
	}

	public function getComponentName() {
		return Text::_( 'COM_ALLVIDEOSHARE' );
	}

	public function getContentIds( $categories = '' ) {
		$db    = Factory::getDbo();
		$query = '';

		if ( empty( $categories ) )	{
			$query = 'SELECT id FROM #__allvideoshare_videos ORDER BY id';
		} else {
			if ( is_array( $categories ) ) {
				$categories = implode( ',', $categories );
			}

			$query = 'SELECT id FROM #__allvideoshare_videos WHERE catid IN (' . $categories . ') ORDER BY id';
		}

		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	public function getCategories() {
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select( 'a.*' )
			->from( '#__allvideoshare_categories AS a' )
			->where( 'a.state = 1' )
			->order( 'a.name ASC' );

		// Get the options
		$db->setQuery( $query );

		try	{
			$items = $db->loadObjectList();
		} catch ( \RuntimeException $e ) {
			// throw new \Exception( $e->getMessage(), 500 );
		}
		
		$children = array();

		if ( $items ) {
			foreach ( $items as $v ) {
				$v->title = $v->name;
				$v->parent_id = $v->parent;
				$pt = $v->parent;
				$list = @$children[ $pt ] ? $children[ $pt ] : array();
				array_push( $list, $v );
				$children[ $pt ] = $list;
			}
		}

		$categories = HTMLHelper::_( 'menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

		// Optional. populate tree listing
		foreach ( $categories as &$row ) {
			$row->treename = str_ireplace( '&#160;', '.&#160;&#160;&#160;', $row->treename );			
			$row->treename = str_replace( '-', '|_', $row->treename );	
		}

		return $categories;
	}

	// Determine if is listing view
	public function isListingView()	{
		$views = array( 'videos', 'categories' );
		return in_array( $this->input->get( 'view' ), $views );
	}

	// Determine if is entry view
	public function isEntryView() {
		return $this->input->get('view') == 'video';
	}

	public function onExecute( &$article, $html, $view, $options = array() ) {
		$article->text .= $html;
		return $html;
	}

}
