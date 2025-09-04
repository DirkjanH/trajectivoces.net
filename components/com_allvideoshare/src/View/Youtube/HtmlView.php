<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\View\YouTube;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeHelper;

/**
 * Frontpage View class.
 *
 * @since  4.1.2
 */
class HtmlView extends BaseHtmlView {

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  void
	 *
	 * @since   4.1.2
	 * @throws  Exception
	 */
	public function display( $tpl = null ) {
		$app = Factory::getApplication();		

		$this->params = $app->getParams( 'com_allvideoshare' );

		echo AllVideoShareYouTubeHelper::renderGallery( $this->params );
		
		// Prepare Document
		$this->_prepareDocument();
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   4.1.2
	 * @throws  Exception
	 */
	protected function _prepareDocument() {
		$app = Factory::getApplication();
	
		$title = $this->params->get( 'page_title', '' );

		if ( empty( $title ) ) {
			$title = $app->get( 'sitename' );
		} elseif ( $app->get( 'sitename_pagetitles', 0 ) == 1 )	{
			$title = Text::sprintf( 'JPAGETITLE', $app->get( 'sitename' ), $title );
		} elseif ( $app->get( 'sitename_pagetitles', 0 ) == 2 )	{
			$title = Text::sprintf( 'JPAGETITLE', $title, $app->get( 'sitename' ) );
		}

		$this->document->setTitle( $title );

		if ( $this->params->get( 'menu-meta_description' ) ) {
			$this->document->setDescription( $this->params->get( 'menu-meta_description' ) );
		}

		if ( $this->params->get( 'menu-meta_keywords' ) ) {
			$this->document->setMetadata( 'keywords', $this->params->get( 'menu-meta_keywords' ) );
		}

		if ( $this->params->get( 'robots' ) ) {
			$this->document->setMetadata( 'robots', $this->params->get( 'robots' ) );
		}
	}

}