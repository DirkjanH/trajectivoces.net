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

use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use \Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHtml;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoSharePlayer;

/**
 * Class VideoController.
 *
 * @since  4.1.0
 */
class VideoController extends BaseController {    

    public function cookie() {
        $app = Factory::getApplication();

        // Set the cookie
        $time = time() + 604800; // 1 week
        $app->input->cookie->set( 'com_allvideoshare_gdpr', 1, $time, $app->get( 'cookie_path', '/' ), $app->get( 'cookie_domain' ), $app->isSSLConnection() );

        ob_start();
		echo 'success';
		echo ob_get_clean();
        exit;
    }

    public function views() {
        $id = Factory::getApplication()->input->getInt( 'id' );
        AllVideoSharePlayer::updateViews( $id );

        ob_start();
		echo 'success';
		echo ob_get_clean();
        exit;
    }

    public function ratings() {	
		$app     = Factory::getApplication();		
		$db      = Factory::getDbo();
		$user    = Factory::getUser();
		$session = Factory::getSession();

		$params    = ComponentHelper::getParams( 'com_allvideoshare' );			
		$videoId   = $app->input->getInt( 'id' );	
		$rating    = $app->input->getFloat( 'rating' );
		$userId    = $user->get( 'id' );
		$sessionId = $session->getId();
		
        $query = 'SELECT COUNT(id) FROM #__allvideoshare_ratings WHERE videoid=' . $videoId;
		if ( $params->get( 'guest_ratings' ) ) {
			$query .= ' AND sessionid=' . $db->quote( $sessionId );
		} else {
			$query .= ' AND userid=' . (int) $userId;
		}

        $db->setQuery( $query );
        $count = $db->loadResult();
		
		if ( $count ) {
			$query  = 'UPDATE #__allvideoshare_ratings SET ratings=' . $rating . ' WHERE videoid=' . $videoId;
			if ( $params->get( 'guest_ratings' ) ) {
				$query .= ' AND sessionid=' . $db->quote( $sessionId );
			} else {
				$query .= ' AND userid=' . (int) $userId;
			}

			$db->setQuery( $query );
			$db->execute();			
		} else {
			$row = new \stdClass();
   			$row->id = NULL;
			$row->videoid = $videoId;
			$row->ratings = $rating;
			$row->sessionid = $sessionId;
			$row->userid = $userId;		

   			$db->insertObject( '#__allvideoshare_ratings', $row );
		}	
		
		$query = 'SELECT SUM(ratings) as total_ratings, COUNT(id) as total_users FROM #__allvideoshare_ratings WHERE videoid=' . $videoId;
		$db->setQuery( $query );
		$item = $db->loadObject();

		$ratings = ( $item->total_ratings / ( $item->total_users * 5 ) ) * 100;
					
		$query = 'UPDATE #__allvideoshare_videos SET ratings=' . $ratings . ' WHERE id=' . $videoId;
		$db->setQuery( $query );
		$db->execute();	

		ob_start();
		echo AllVideoShareHtml::RatingsWidget( $videoId, $params );
		echo ob_get_clean();
		exit();
	}

	public function likes() {	
		$app     = Factory::getApplication();		
		$db      = Factory::getDbo();
		$user    = Factory::getUser();
		$session = Factory::getSession();

		$params    = ComponentHelper::getParams( 'com_allvideoshare' );
		$videoId   = $app->input->getInt( 'id' );	
		$like      = $app->input->getInt( 'like' );
		$dislike   = $app->input->getInt( 'dislike' );			
		$userId    = $user->get( 'id' );
		$sessionId = $session->getId();

		$query  = 'SELECT COUNT(id) FROM #__allvideoshare_likes WHERE videoid=' . $videoId;
		if ( $params->get( 'guest_likes' ) ) {
			$query .= ' AND sessionid=' . $db->quote( $sessionId );
		} else {
			$query .= ' AND userid=' . $db->quote( $userId );
		}

		$db->setQuery( $query );			
		$count = $db->loadResult();				
		
		if ( $count ) {
			$query = 'UPDATE #__allvideoshare_likes SET likes=' . $like . ', dislikes=' . $dislike . ' WHERE videoid=' . $videoId;
			if ( $params->get( 'guest_likes' ) ) {
				$query .= ' AND sessionid=' . $db->quote( $sessionId );
			} else {
				$query .= ' AND userid=' . $db->quote( $userId );
			}

			$db->setQuery( $query );
			$db->execute();
		} else {	
			$row = new \stdClass();	
			$row->id = NULL;
			$row->videoid = $videoId;
			$row->likes = $like;
			$row->dislikes = $dislike;	
			$row->sessionid = $sessionId;
			$row->userid = $userId;		
			
			$result = $db->insertObject( '#__allvideoshare_likes', $row );												
		}

		$query = 'SELECT SUM(likes) as total_likes, SUM(dislikes) as total_dislikes FROM #__allvideoshare_likes WHERE videoid=' . $videoId;
		$db->setQuery( $query );
		$item = $db->loadObject();
					
		$query = 'UPDATE #__allvideoshare_videos SET likes=' . (int) $item->total_likes . ', dislikes=' . (int) $item->total_dislikes . ' WHERE id=' . $videoId;
		$db->setQuery( $query );
		$db->execute();	
		
		ob_start();
		echo AllVideoShareHtml::LikesWidget( $videoId, $params );
		echo ob_get_clean();
		exit();					
	}

}
