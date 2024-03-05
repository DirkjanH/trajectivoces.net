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
use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * AdController class.
 *
 * @since  4.1.0
 */
class AdController extends BaseController {
	
    public function impression() {
		$db = Factory::getDbo();

		$query = 'UPDATE #__allvideoshare_adverts SET impressions=impressions+1 WHERE id=' . Factory::getApplication()->input->getInt( 'id' );
    	$db->setQuery( $query );
		$db->execute();
    }

    public function click() {	
		$db = Factory::getDbo();	
			
		$query = 'UPDATE #__allvideoshare_adverts SET clicks=clicks+1 WHERE id=' . Factory::getApplication()->input->getInt( 'id' );
    	$db->setQuery( $query );
		$db->execute();		
	}
	
}
