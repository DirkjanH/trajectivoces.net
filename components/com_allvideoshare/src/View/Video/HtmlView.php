<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\View\Video;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

/**
 * Frontpage View class.
 *
 * @since  4.1.0
 */
class HtmlView extends BaseHtmlView {

	protected $state;

	protected $params;

	protected $video;

	protected $items;

	protected $pagination;

	protected $hasAccess;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	public function display( $tpl = null ) {
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		$this->state  = $this->get( 'State' );
		$this->video  = $this->get( 'Video' );
		$this->canDo  = AllVideoShareHelper::canDo();
		$this->params = AllVideoShareHelper::resolveParams( $app->getParams( 'com_allvideoshare' ) );	

		// Check for errors
		$errors = $this->get( 'Errors' );
		
		if ( count( $errors ) > 0 ) {
			for ( $i = 0, $n = count( $errors ); $i < $n; $i++ ) {
				if ( $errors[ $i ] instanceof \Exception ) {
					$app->enqueueMessage( $errors[ $i ]->getMessage(), 'error' );
				} else {
					$app->enqueueMessage( $errors[ $i ], 'error' );
				}
			}

			return false;
		}

		if ( empty( $this->video ) ) {
			$app->enqueueMessage( Text::_( 'COM_ALLVIDEOSHARE_VIDEO_NOT_FOUND' ), 'error' );
			$app->setHeader( 'status', 404, true );

			return false;
        } 

		$this->hasAccess = 1;
		if ( ! empty( $this->video->access ) && ! in_array( $this->video->access, $user->getAuthorisedViewLevels() ) && $this->video->user != $user->username ) {
			$this->hasAccess = 0;
        }  

		if ( $this->params->get( 'related_videos' ) ) {
			$this->items = $this->get( 'Items' );
			$this->pagination = $this->get( 'Pagination' );
		}

		$this->params->set( 'show_page_heading', $this->params->get( 'title' ) );
		
		$this->_prepareDocument();

		parent::display( $tpl );
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 * @throws  Exception
	 */
	protected function _prepareDocument() {
		$app = Factory::getApplication();

		$title = null;
		$meta_description = null;
		$meta_keywords = null;

		$isMenuItem = 0;

		// Because the application sets a default page title,
		// We need to get it from the menu item itself
		$menus = $app->getMenu();	
		$menu = $menus->getActive();			

		if ( $menu ) {
			$meta_description = $this->params->get( 'menu-meta_description', $this->video->metadescription );
			$meta_keywords = $this->params->get( 'menu-meta_keywords', $this->video->tags );

			if ( $menu->link == 'index.php?option=com_allvideoshare&view=video&slg=' . $this->video->slug ) {
				$isMenuItem = 1;

				$title = $this->params->get( 'page_title', $menu->title );
				$this->params->def( 'page_heading', $title );
			} else {
				$title = $this->video->title;
				$this->params->set( 'page_heading', $title );

				if ( ! empty( $this->video->metadescription ) ) {
					$meta_description = $this->video->metadescription;
				}

				if ( ! empty( $this->video->tags ) ) {
					$meta_keywords = $this->video->tags;
				}
			}
		} else {
			$title = $this->params->get( 'page_title', $this->video->title );
			$this->params->def( 'page_heading', $title );

			$meta_description = $this->video->metadescription;
			$meta_keywords = $this->video->tags;
		}

		if ( empty( $title ) ) {
			$title = $app->get( 'sitename' );
		} elseif ( $app->get( 'sitename_pagetitles', 0 ) == 1 )	{
			$title = Text::sprintf( 'JPAGETITLE', $app->get( 'sitename' ), $title );
		} elseif ( $app->get( 'sitename_pagetitles', 0 ) == 2 )	{
			$title = Text::sprintf( 'JPAGETITLE', $title, $app->get( 'sitename' ) );
		}

		$this->document->setTitle( $title );

		if ( ! empty( $meta_description ) ) {
			$this->document->setDescription( $meta_description );
		}

		if ( ! empty( $meta_keywords ) ) {
			$this->document->setMetadata( 'keywords', $meta_keywords );
		}

		if ( $this->params->get( 'robots' ) ) {
			$this->document->setMetadata( 'robots', $this->params->get( 'robots' ) );
		}

		// Add Custom Tags
		if ( $this->params->get( 'comments_type' ) == 'facebook' ) {
			$language = str_replace( '-', '_', Factory::getLanguage()->getTag() );
			$this->document->addCustomTag( '<script async defer crossorigin="anonymous" src="https://connect.facebook.net/' . $language . '/sdk.js#xfbml=1&version=v12.0" nonce="' . $app->get( 'csp_nonce' ) . '"></script>' );
		}

		if ( $fbAppId = $this->params->get( 'fbappid' ) ) {
			$this->document->addCustomTag( '<meta property="fb:app_id" content="' . $fbAppId . '">' );
		}

		$this->document->addCustomTag( '<meta property="og:site_name" content="' . $app->get( 'sitename' ) . '" />' );

		$pageURL = Route::_( 'index.php?option=com_allvideoshare&view=video&slg=' . $this->video->slug, true, 0, true );
		$this->document->addCustomTag( '<meta property="og:url" content="' . $pageURL . '" />' );

		$this->document->addCustomTag( '<meta property="og:type" content="video" />' );
		$this->document->addCustomTag( '<meta property="og:title" content="' . $this->video->title . '" />' );

		$description = $meta_description;
		if ( empty( $description ) && ! empty( $this->video->description ) ) {
			$description = AllVideoShareHelper::Truncate( $this->video->description );
			$description = str_replace( '...', '', $description );
		}

		if ( ! empty( $description ) ) {
			$this->document->addCustomTag( '<meta property="og:description" content="' . $description . '" />' );
		}

		$image = AllVideoShareHelper::getImage( $this->video );
		if ( ! empty( $image ) ) {
			$this->document->addCustomTag( '<meta property="og:image" content="' . $image . '" />' );
		}

		$videoURL = Uri::root() . 'index.php?option=com_allvideoshare&view=player&id=' . $this->video->id . '&format=raw';
		$this->document->addCustomTag( '<meta property="og:video:url" content="' . $videoURL . '" />' );

		if ( stripos( $pageURL, 'https://' ) === 0 ) {
			$this->document->addCustomTag( '<meta property="og:video:secure_url" content="' . $videoURL . '" />' );
		}

		$this->document->addCustomTag( '<meta property="og:video:type" content="text/html">' );
		$this->document->addCustomTag( '<meta property="og:video:width" content="1280">' );
		$this->document->addCustomTag( '<meta property="og:video:height" content="720">' );

		$this->document->addCustomTag( '<meta name="twitter:card" content="summary">' );
		$this->document->addCustomTag( '<meta name="twitter:title" content="' . $this->video->title . '">' );

		if ( ! empty( $description ) ) {
			$this->document->addCustomTag( '<meta property="twitter:description" content="' . $description . '" />' );
		}
		
		if ( ! empty( $image ) ) {
			$this->document->addCustomTag( '<meta property="twitter:image" content="' . $image . '" />' );
		}

		// Add Breadcrumbs
		if ( ! $isMenuItem ) {
			$pathway = $app->getPathway();
				
			if ( isset( $this->video->category ) && ! empty( $this->video->category ) && ! in_array( $this->video->category->name, $pathway->getPathwayNames() ) ) {
				$pathway->addItem( $this->video->category->name, 'index.php?option=com_allvideoshare&view=category&slg=' . $this->video->category->slug );
			}

			if ( ! in_array( $this->video->title, $pathway->getPathwayNames() ) ) {
				$pathway->addItem( $this->video->title );    
			}
		}
	}

}
