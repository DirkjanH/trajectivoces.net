<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Table;

// No direct access
\defined( '_JEXEC' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Table\Table;
use \Joomla\Database\DatabaseDriver;

/**
 * Class LikeTable.
 *
 * @since  4.1.0
 */
class LikeTable extends Table {
	
	/**
	 * Constructor
	 *
	 * @param  JDatabase  &$db  A database connector object
	 * 
	 * @since  4.1.0
	 */
	public function __construct( DatabaseDriver $db ) {
		$this->typeAlias = 'com_allvideoshare.like';
		parent::__construct( '#__allvideoshare_likes', 'id', $db );
	}

	/**
	 * Get the type alias for the history table
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.1.0
	 */
	public function getTypeAlias() {
		return $this->typeAlias;
	}

	/**
     * Overrides Table::store to set modified data and user id.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   4.2.0
     */
    public function store( $updateNulls = true ) {
		return parent::store( true );
	}
	
	/**
     * Delete a record by id
     *
     * @param   mixed  $pk  Primary key value to delete. Optional
     *
     * @return  bool
	 * 
	 * @since   4.1.0
     */
    public function delete( $pk = null ) {
        $this->load( $pk );
        $result = parent::delete( $pk );
        
		if ( $result )	{
			$db = Factory::getDbo();

			$query = 'SELECT SUM(likes) as total_likes, SUM(dislikes) as total_dislikes FROM #__allvideoshare_likes WHERE videoid=' . (int) $this->videoid;
			$db->setQuery( $query );
			$item = $db->loadObject();

			if ( $item ) {
				$likes = $item->total_likes;
				$dislikes = $item->total_dislikes;
			} else {
				$likes = 0;
				$dislikes = 0;
			}
						
			$query = 'UPDATE #__allvideoshare_videos SET likes=' . (int) $likes . ', dislikes=' . (int) $dislikes . ' WHERE id=' . (int) $this->videoid;
			$db->setQuery( $query );
			$db->execute();	
		}

		return $result;
	}
	
}
