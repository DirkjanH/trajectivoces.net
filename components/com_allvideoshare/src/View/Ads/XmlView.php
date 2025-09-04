<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\View\Ads;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\AbstractView;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Component\Content\Site\Helper\RouteHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareQuery;

/**
 * Frontpage View class
 *
 * @since  4.1.0
 */
class XmlView extends AbstractView {

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

		$this->setHeader();

		$type = $app->input->getCmd( 'type' );		
		if ( empty( $type ) ) {
			$type = $app->input->getCmd( 'task', 'vmap' ); // Fallback to our old versions
		}		

		if ( $type == 'vmap' ) {
			$this->vmap();
		} else {
			$this->vast();
		}
	}

	private function vmap() {
        $app = Factory::getApplication();

		$lang = Factory::getLanguage();	
		$locales = $lang->getLocale();	

        $hasPreroll = $app->input->getInt( 'preroll', 0 );
		$hasPostroll = $app->input->getInt( 'postroll', 0 );				

        if ( $hasPreroll ) {
            $prerollId = $this->get( 'PrerollId' );

            if ( empty( $prerollId ) ) {
                $hasPreroll = 0;
            }
        }		

		if ( $hasPostroll ) {
            $postrollId = $this->get( 'PostrollId' );

            if ( empty( $postrollId ) ) {
                $hasPostroll = 1;
            }
        }		

		include JPATH_ROOT . '/components/com_allvideoshare/tmpl/ads/vmap.php';
 	}

	private function vast() {
		$item = $this->get( 'Ad' );  

        if ( empty( $item ) ) {
            return;
        }

		$app = Factory::getApplication();
		$siteName = $app->get( 'sitename' );

		$pixelImage = Uri::root() . 'media/com_allvideoshare/images/pixel.png';

		include JPATH_ROOT . '/components/com_allvideoshare/tmpl/ads/vast.php';
	}

	public function setHeader() {
        $u = Uri::getInstance( Uri::base() );

		if ( $u->getScheme() ) {
			$origin = $u->getScheme() . '://imasdk.googleapis.com';
        } else {
            $origin = 'https://imasdk.googleapis.com';
        }

        $app = Factory::getApplication();
        $app->setHeader( 'Access-Control-Allow-Origin', $origin );
        $app->setHeader( 'Access-Control-Allow-Credentials', 'true' );
    }

}
