<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\View\Videos;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Document\Feed\FeedItem;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\AbstractView;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Frontpage View class
 *
 * @since  4.2.0
 */
class FeedView extends AbstractView {

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  void
	 *
	 * @since   4.2.0
	 * @throws  Exception
	 */
	public function display( $tpl = null ) {
		$app = Factory::getApplication();

		$siteEmail = $app->get( 'mailfrom' );
		$fromName  = $app->get( 'fromname' );
		$feedEmail = $app->get( 'feed_email', 'none' );

		$this->document->editor = $fromName;

		if ( $feedEmail !== 'none' ) {
			$this->document->editorEmail = $siteEmail;
		}

		$route = 'index.php?option=com_allvideoshare&view=videos&format=feed';
		$this->document->link = Route::_( $route );

		// Get some data from the model
		$items = $this->get( 'Items' );

		foreach ( $items as $item ) {
			// Strip HTML from feed item title
			$title = htmlspecialchars( $item->title, ENT_QUOTES, 'UTF-8' );
			$title = html_entity_decode( $title, ENT_COMPAT, 'UTF-8' );

			// Strip HTML from feed item description text
			$description = $item->metadescription;

			if ( empty( $description ) ) {
				$description = $item->description;
			}

			if ( ! empty( $item->thumb ) ) {
				$description .= '<p><img src="' . AllVideoShareHelper::getImage( $item ) . '" /></p>';
			}

			if ( ! empty( $description ) ) {
				$description = '<div class="feed-description">' . $description . '</div>';
			}

			$author = Factory::getUser( $item->created_by );

			$date = $item->created_date ? date( 'r', strtotime( $item->created_date ) ) : '';

			// Load individual item creator class
			$feeditem = new FeedItem;
			$feeditem->title       = $title;
			$feeditem->link        = '/index.php?option=com_allvideoshare&view=video&slg=' . $item->slug;
			$feeditem->description = $description;
			$feeditem->date        = $date;
			$feeditem->category    = ( isset( $item->category ) && ! empty( $item->category ) ) ? $item->category->name : null;
			$feeditem->author      = $author->name;

			if  ( $feedEmail === 'site' ) {
				$feeditem->authorEmail = $siteEmail;
			}

			if ( $feedEmail === 'author' ) {
				$feeditem->authorEmail = $author->email;
			}

			// Loads item info into RSS array
			$this->document->addItem( $feeditem );
		}
	}

}
