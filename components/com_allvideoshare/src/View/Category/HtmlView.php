<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\View\Category;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareRoute;

/**
 * Frontpage View class.
 *
 * @since  4.1.0
 */
class HtmlView extends BaseHtmlView {

	protected $state;

	protected $params;

	protected $category;

	protected $items;

	protected $pagination;		

	protected $subCategories;

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
		$app = Factory::getApplication();		

		$this->state = $this->get( 'State' );		
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

		$category_alias = $app->input->getString( 'slg' );
		$this->category = AllVideoShareQuery::getCategory( $category_alias, 'alias' );

		if ( ! empty( $this->category ) ) {
			$this->items = $this->get( 'Items' );
			$this->pagination = $this->get( 'Pagination' );
			$this->subCategories = $this->get( 'CategoriesByParent' );	

			$this->_prepareDocument();
		}
		
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
		// we need to get it from the menu item itself
		$menus = $app->getMenu();
		$menu = $menus->getActive();				

		if ( $menu ) {
			$meta_description = $this->params->get( 'menu-meta_description', $this->category->metadescription );
			$meta_keywords = $this->params->get( 'menu-meta_keywords', $this->category->metakeywords );

			if ( $menu->link == 'index.php?option=com_allvideoshare&view=category&slg=' . $this->category->slug ) {
				$isMenuItem = 1;

				$title = $this->params->get( 'page_title', $menu->title );
				$this->params->def( 'page_heading', $title );				
			} else {
				$title = $this->category->name;
				$this->params->set( 'page_heading', $title );

				if ( ! empty( $this->category->metadescription ) ) {
					$meta_description = $this->category->metadescription;
				}

				if ( ! empty( $this->category->metakeywords ) ) {
					$meta_keywords = $this->category->metakeywords;
				}
			}			
		} else {
			$title = $this->params->get( 'page_title', $this->category->name );
			$this->params->def( 'page_heading', $title );

			$meta_description = $this->category->metadescription;
			$meta_keywords = $this->category->metakeywords;
		}

		if ( empty( $title ) ) {
			$title = $app->get( 'sitename' );
		} elseif ($app->get( 'sitename_pagetitles', 0 ) == 1 ) {
			$title = Text::sprintf( 'JPAGETITLE', $app->get( 'sitename' ), $title );
		} elseif ($app->get( 'sitename_pagetitles', 0 ) == 2 ) {
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
		if ( $fbAppId = $this->params->get( 'fbappid' ) ) {
			$this->document->addCustomTag( '<meta property="fb:app_id" content="' . $fbAppId . '">' );
		}

		$this->document->addCustomTag( '<meta property="og:site_name" content="' . $app->get( 'sitename' ) . '" />' );

		$pageURL = Route::_( 'index.php?option=com_allvideoshare&view=category&slg=' . $this->category->slug, true, 0, true );
		$this->document->addCustomTag( '<meta property="og:url" content="' . $pageURL . '" />' );

		$this->document->addCustomTag( '<meta property="og:type" content="article" />' );
		$this->document->addCustomTag( '<meta property="og:title" content="' . $this->category->name . '" />' );

		$description = $meta_description;
		if ( empty( $description ) && ! empty( $this->category->description ) ) {
			$description = AllVideoShareHelper::Truncate( $this->category->description );
			$description = str_replace( '...', '', $description );
		}

		if ( ! empty( $description ) ) {
			$this->document->addCustomTag( '<meta property="og:description" content="' . $description . '" />' );
		}

		$image = AllVideoShareHelper::getImage( $this->category );
		if ( ! empty( $image ) ) {
			$this->document->addCustomTag( '<meta property="og:image" content="' . $image . '" />' );
		}	

		$this->document->addCustomTag( '<meta name="twitter:card" content="summary">' );
		$this->document->addCustomTag( '<meta name="twitter:title" content="' . $this->category->name . '">' );

		if ( ! empty( $description ) ) {
			$this->document->addCustomTag( '<meta property="twitter:description" content="' . $description . '" />' );
		}

		if ( ! empty( $image ) ) {
			$this->document->addCustomTag( '<meta property="twitter:image" content="' . $image . '" />' );
		}
		
		// Add Breadcrumbs
		if ( ! $isMenuItem && ! empty( $this->category ) ) {
			$pathway = $app->getPathway();

			$parent = $this->category->parent;
			$crumbs = array();

			while ( $parent != 0 ) {
				if ( $category = AllVideoShareQuery::getCategory( $parent ) ) {	
					if ( ! in_array( $category->name, $pathway->getPathwayNames() ) ) {
						$crumbs[] = $category;						
					}

					$parent = $category->parent;
				} else {
					$parent = 0;
				}
			}

			if ( ! empty( $crumbs ) ) {
				$crumbs = array_reverse( $crumbs );
				foreach ( $crumbs as $crumb ) {
					$pathway->addItem( $crumb->name, 'index.php?option=com_allvideoshare&view=category&slg=' . $crumb->slug );
				}
			}

			if ( ! in_array( $this->category->name, $pathway->getPathwayNames() ) ) {
				$pathway->addItem( $this->category->name );    
			}
		}
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return  bool
	 * 
	 * @since   4.1.0
	 */
	public function getState( $state ) {
		return isset( $this->state->{$state} ) ? $this->state->{$state} : false;
	}
	
}
