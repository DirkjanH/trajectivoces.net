<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Controller;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeHelper;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareYouTubeApi;

/**
 * Class DisplayController.
 *
 * @since  4.1.0
 */
class DisplayController extends BaseController {

	/**
	 * Constructor.
	 *
	 * @param  array                $config   An optional associative array of configuration settings.
	 * Recognized key values include 'name', 'default_task', 'model_path', and
	 * 'view_path' (this list is not meant to be comprehensive).
	 * @param  MVCFactoryInterface  $factory  The factory.
	 * @param  CMSApplication       $app      The JApplication for the dispatcher
	 * @param  Input                $input    Input
	 *
	 * @since  4.1.0
	 */
	public function __construct( $config = array(), MVCFactoryInterface $factory = null, $app = null, $input = null ) {
		parent::__construct( $config, $factory, $app, $input );
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  \Joomla\CMS\MVC\Controller\BaseController  This object to support chaining.
	 *
	 * @since   4.1.0
	 */
	public function display( $cachable = false, $urlparams = false ) {
		$view = $this->input->getCmd( 'view', 'videos' );					
		
		switch ( $view ) {
			case 'category':
				$alias = $this->input->getString( 'slg', '' );
				if ( empty( $alias ) ) {
					$view = 'categories';
				}
				break;
			case 'video':
				$alias = $this->input->getString( 'slg', '' );
				if ( empty( $alias ) ) {
					$view = 'videos';
				}

				// Redirect our old embed URL to the latest
				$tmpl = $this->input->getCmd( 'tmpl', '' );
				$id = $this->input->getInt( 'id' );
				if ( $tmpl == 'component' && $id > 0 ) {
					$this->setRedirect( Uri::root() . 'index.php?option=com_allvideoshare&view=player&vid=' . $id . '&format=raw' );
					$this->redirect();	
				}
				break;
			case 'uservideos':
			case 'user': // Fallback to the old versions
				$view = 'uservideos';
			case 'videoform':			
				$user = Factory::getUser();

				if ( $user->guest ) {
					$this->setMessage( Text::_( 'COM_ALLVIDEOSHARE_USER_LOGIN_REQUIRED' ), 'info' );
					$this->setRedirect( Route::_( 'index.php?option=com_users&view=login&return=' . base64_encode( Uri::getInstance()->toString() ) ) );

					$this->redirect();
				}

				$layout = $this->input->getCmd( 'layout', '' );
				if ( $layout == 'add' ) {
					$view = 'videoform';
				}
				break;
		}
		
		$this->input->set( 'view', $view );		

		parent::display( $cachable, $urlparams );
		return $this;
	}

	/**
	 * Load more YouTube videos.
	 *
	 * @since 4.1.2
	 */
	public function youtubeAjax() {
		$app      = Factory::getApplication();
		$language = Factory::getLanguage();

		// Load component language file		
		$language->load( 'com_allvideoshare', JPATH_SITE );

		// Vars
		$json = array( 
			'success' => false,
			'message' => '',
			'data'    => array(
				'html' => ''
			) 
		);

		$params = AllVideoShareYouTubeHelper::resolveParams( $app->input->post->getArray() );

		// Query YouTube API
		$apiParams = array(
			'apiKey'     => $params->get( 'youtube_api_key' ),
			'type'       => $params->get( 'type' ),
			'src'        => $params->get( 'src' ),
			'order'      => $params->get( 'order' ), // Applicable only when type=search
			'maxResults' => (int) $params->get( 'per_page' ),
			'cache'      => (int) $params->get( 'cache' ),
			'pageToken'  => $params->get( 'pageToken' )
		);		

		$youtubeApi = new AllVideoShareYouTubeApi();
		$response = $youtubeApi->query( $apiParams );

		// Output
		if ( ! isset( $response->error ) ) {
			$displayData = array(
				'info'   => $response,
				'params' => $params
			);

			// Pagination
			if ( isset( $response->pageInfo ) ) {				
				$json['data']['next_page_token'] = isset( $response->pageInfo->nextPageToken ) ? $response->pageInfo->nextPageToken : '';
				$json['data']['prev_page_token'] = isset( $response->pageInfo->prevPageToken ) ? $response->pageInfo->prevPageToken : '';
			}

			// Output
			ob_start();
			echo LayoutHelper::render( 'youtube.' . $params->get( 'layout' ), $displayData, JPATH_SITE . '/components/com_allvideoshare/layouts' );
			$json['data']['html'] = ob_get_clean();

			$json['success'] = true;
		} else {
			$json['message'] = $response->error_message;		
		}
		
		echo json_encode( $json );	
		exit();
	}
	
}
