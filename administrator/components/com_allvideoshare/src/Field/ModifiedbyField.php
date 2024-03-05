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

use \Joomla\CMS\Form\FormField;
use \Joomla\CMS\Factory;

/**
 * Class ModifiedbyField.
 *
 * @since  4.1.0
 */
class ModifiedbyField extends FormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'modifiedby';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		// Initialize variables
		$user = Factory::getUser();

		$html   = array();		
		$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $user->id . '" />';
		if ( ! $this->hidden ) {
			$html[] = "<div>" . $user->name . " (" . $user->username . ")</div>";
		}

		return implode( $html );
	}

}
