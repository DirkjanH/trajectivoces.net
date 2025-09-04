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
 * Class RatingTable.
 *
 * @since  4.1.0
 */
class RatingTable extends Table {
	
	/**
	 * Constructor
	 *
	 * @param  JDatabase  &$db  A database connector object
	 * 
	 * @since  4.1.0
	 */
	public function __construct( DatabaseDriver $db ) {
		$this->typeAlias = 'com_allvideoshare.rating';
		parent::__construct( '#__allvideoshare_ratings', 'id', $db );
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

			$query = 'SELECT SUM(ratings) as total_ratings, COUNT(id) as total_users FROM #__allvideoshare_ratings WHERE videoid=' . (int) $this->videoid;
			$db->setQuery( $query );
			$item = $db->loadObject();

			$ratings = 0.0;
			if ( ! empty( $item->total_ratings ) ) {
				$ratings = ( $item->total_ratings / ( $item->total_users * 5 ) ) * 100;
			}
						
			$query = 'UPDATE #__allvideoshare_videos SET ratings=' . (float) $ratings . ' WHERE id=' . (int) $this->videoid;
			$db->setQuery( $query );
			$db->execute();	
		}

		return $result;
	}
	
}
