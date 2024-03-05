<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Helper;

\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Component\ComponentHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use \Joomla\CMS\Uri\Uri;

/**
 * Class AllVideoShareHtml.
 *
 * @since  4.1.0
 */
class AllVideoShareHtml {	

	public static function RatingsWidget( $item, $params ) {	
		$db = Factory::getDbo();	 

		if ( is_numeric( $item ) ) {
			$videoId = $item;

			$query = 'SELECT ratings FROM #__allvideoshare_videos WHERE id=' . (int) $videoId;
			$db->setQuery( $query );
			$ratings = $db->loadResult();
		} else {
			$videoId = $item->id;
			$ratings = $item->ratings;
		}

    	$query = 'SELECT COUNT(id) FROM #__allvideoshare_ratings WHERE videoid=' . (int) $videoId;
        $db->setQuery( $query );
		$total = $db->loadResult();

		$html  = '<div class="avs-ratings">';
		$html .= '<span class="avs-ratings-stars">';
		$html .= '<span class="avs-ratings-current" style="width:' . (float) $ratings . '%;"></span>';
		
		$j = 0.5;
		for ( $i = 0; $i < 10; $i++ ) {
			$j += 0.5;

			$html .= '<span class="avs-ratings-star">';
			$html .= '<a href="javascript: void(0);" class="avs-ratings-star-' . ( $j * 10 ) . '" title="' . Text::sprintf( 'COM_ALLVIDEOSHARE_RATINGS_TITLE', $j, 5 ) . '" data-id="' . (int) $videoId . '" data-value="' . $j . '">1</a>';
			$html .= '</span>';
		}
		
		$html .= '</span>';
		$html .= '<span class="avs-ratings-info">' . Text::sprintf( 'COM_ALLVIDEOSHARE_RATINGS_INFO', ( $ratings * 5 ) / 100, $total ) . '</span>';
		$html .= '</div>';
		
		return $html;		
	}

	public static function LikesWidget( $item, $params ) {			
		$db      = Factory::getDBO();
		$user    = Factory::getUser();
		$session = Factory::getSession();	
				
		$videoId   = is_numeric( $item ) ? $item : $item->id;
		$userId    = $user->get( 'id' );
		$sessionId = $session->getId();	

		// Has Liked/Disliked?
		$query = 'SELECT likes,dislikes FROM #__allvideoshare_likes WHERE videoid=' . (int) $videoId;
		if ( $params->get( 'guest_likes' ) ) {
			$query .= ' AND sessionid=' . $db->quote( $sessionId );
		} else {
			$query .= ' AND userid=' . (int) $userId;
		}
		$db->setQuery( $query );		
		$status = $db->loadObject();

		// Total number of likes
		$query = 'SELECT COUNT(id) FROM #__allvideoshare_likes WHERE videoid=' . (int) $videoId . ' AND likes=1';
		$db->setQuery( $query );		
		$likes_count = $db->loadResult();

		// Total number of dislikes
		$query = 'SELECT COUNT(id) FROM #__allvideoshare_likes WHERE videoid=' . (int) $videoId . ' AND dislikes=1';
		$db->setQuery( $query );		
		$dislikes_count = $db->loadResult();	
	
		// Build HTML output
		$html  = '<div class="avs-likes-dislikes">';        
        $html .= '<span class="avs-dislike-btn" data-id="' . (int) $videoId . '" data-like="0" data-dislike="1">';
        $html .= '<span class="avs-dislike-icon' . ( $status && $status->dislikes > 0 ? ' active' : '' ) . '"></span>';
        $html .= '<span class="avs-like-dislike-separator"></span>';
        $html .= '<span class="avs-dislike-count">' . (int) $dislikes_count . '</span>';          
      	$html .= '</span>';
		$html .= '<span class="avs-like-btn" data-id="' . (int) $videoId . '" data-like="1" data-dislike="0">';
        $html .= '<span class="avs-like-icon' . ( $status && $status->likes > 0 ? ' active' : '' ) . '"></span>';
        $html .= '<span class="avs-like-dislike-separator"></span>';
        $html .= '<span class="avs-like-count">' . (int) $likes_count . '</span>';              
        $html .= '</span>';
        $html .= '</div>';
		
		return $html;				
	}

}
