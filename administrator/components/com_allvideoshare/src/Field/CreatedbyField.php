<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Field;

\defined( 'JPATH_BASE' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Form\FormField;

/**
 * Class CreatedbyField.
 *
 * @since  4.1.0
 */
class CreatedbyField extends FormField {

	/**
	 * The form field type.
	 *
	 * @var    tring
	 * @since  4.1.0
	 */
	protected $type = 'createdby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		// Initialize variables.
		$html = array();

		// Load user
		$user_id = $this->value;

		if ( $user_id )	{
			$user = Factory::getUser( $user_id );
		} else {
			$user   = Factory::getUser();
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $user->id . '" />';
		}

		if ( ! $this->hidden ) {
			$html[] = "<div>" . $user->name . " (" . $user->username . ")</div>";
		}

		return implode( $html );
	}
	
}
