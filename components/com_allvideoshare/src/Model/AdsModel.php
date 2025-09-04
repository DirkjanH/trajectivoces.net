<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Site\Model;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use \Joomla\CMS\Uri\Uri;
use \Joomla\Database\ParameterType;
use \Joomla\Utilities\ArrayHelper;

/**
 * Class AdsModel.
 *
 * @since  4.1.0
 */
class AdsModel extends BaseDatabaseModel {

    public function getPrerollId() {	
        $db = Factory::getDbo();
				 
        $query = 'SELECT id FROM #__allvideoshare_adverts WHERE state=1 AND (type=' . $db->Quote( 'preroll' ) . ' OR type=' . $db->Quote( 'both' ) . ') ORDER BY RAND() LIMIT 1';
        $db->setQuery( $query );
        $id = $db->loadResult();
		
		return $id;  
    }

    public function getPostrollId() {	
        $db = Factory::getDbo();
				 
        $query = 'SELECT id FROM #__allvideoshare_adverts WHERE state=1 AND (type=' . $db->Quote( 'postroll' ) . ' OR type=' . $db->Quote( 'both' ) . ') ORDER BY RAND() LIMIT 1';
        $db->setQuery( $query );
        $id = $db->loadResult();
		
		return $id;  
    }

    public function getAd() {	
        $db = Factory::getDbo();

        $query = 'SELECT * FROM #__allvideoshare_adverts WHERE state=1 AND id=' . Factory::getApplication()->input->getInt( 'id' );
        $db->setQuery( $query );
        $item = $db->loadObject();

        if ( ! empty( $item ) && strpos( $item->video, 'media/com_allvideoshare' ) !== false ) {
            $parts = explode( 'media/', $item->video );
            $item->video = URI::root() . 'media/' . $parts[1];
        }
    
        return $item;   
    }

}
