<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoSharePlayer;

/**
 * Plugin to enable loading videos into content (e.g. articles)
 * This uses the {avsplayer} syntax
 *
 * @since  4.1.0
 */
class plgContentAllVideoSharePlayer extends CMSPlugin {

	/**
	 * Plugin that loads videos within content
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	public function onContentPrepare( $context, &$article, &$params, $page = 0 ) {
		// Don't run this plugin when the content is being indexed
		if ( $context === 'com_finder.indexer' ) {
			return;
		}

		// Simple performance check to determine whether bot should process further
		if ( strpos( $article->text, 'avsplayer' ) === false ) {
			return;
		}

		// Expression to search for
		$regex = '/{avsplayer\s*.*?}/i';

		// Find all instances of plugin and put in $matches
		preg_match_all( $regex, $article->text, $matches );

		// Number of plugins
		$count = count( $matches[0] );

		$this->_process( $article, $matches[0], $count, $regex );
	}

	private function _process( $article, $matches, $count, $regex ) {	
		foreach ( $matches as $match ) {
			$query = str_replace( '{avsplayer', '', $match );
			$query = str_replace( '}', '', $query );
			$query = str_replace( '"', '', $query );
			$query = str_replace( "'", '', $query );
			$query = strip_tags( $query );
			$query = trim( $query );
			$query = explode( ' ', $query );

			$params = array();
			foreach ( $query as $string ) {
				list( $key, $value ) = explode( '=', $string, 2 );
				$params[ $key ] = $value;
			}
			
			$player = $this->_load( $params );
			$article->text = str_replace( $match, $player, $article->text );
		}

		// Removes the left tags
	   	$article->text = preg_replace( $regex, '', $article->text );	   
   }

   private function _load( $params ) {	
		$app = Factory::getApplication();	

		$params = array_merge(
			array(
				'id'         => 0,
				'autodetect' => 0
			),
			$params
		);

		// Fallback to old shortcode parameters
		if ( isset( $params['videoid'] ) ) {
			$params['id'] = $params['videoid'];
		}

		// Get the video Id from URL
		$alias = $app->input->getString( 'slg', '' );
		
		if ( empty( $alias ) ) {
			$params['autodetect'] = 0;
		}

		if ( $app->input->getCmd( 'option' ) == 'com_allvideoshare' && $app->input->getCmd( 'view' ) == 'category' ) { // Is a category view?
			$params['autodetect'] = 0;
		}

		if ( ! empty( $params['autodetect'] ) ) {
			$db = Factory::getDbo();

			$query = 'SELECT id FROM #__allvideoshare_videos WHERE state=1 AND slug=' . $db->Quote( $alias );
			$db->setQuery( $query );
			$result = $db->loadResult();

			if ( ! empty( $result ) ) {
				$params['id'] = $result;
			}
		}		
		
		return AllVideoSharePlayer::load( $params );	
	}

}
