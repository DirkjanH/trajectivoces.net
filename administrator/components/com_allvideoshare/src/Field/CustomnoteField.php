<?php
/**
 * @version    4.2.0
 * @package    Com_Allvideoshare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Field;

\defined( 'JPATH_BASE' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Form\FormField;
use \Joomla\CMS\Language\Text;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Class CustomnoteField.
 *
 * @since  4.1.0
 */
class CustomnoteField extends FormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'customnote';

	/**
	 * Hide the label when rendering the form field.
	 *
	 * @var    boolean
	 * @since  4.1.0
	 */
	protected $hiddenLabel = true;

	/**
	 * Hide the description when rendering the form field.
	 *
	 * @var    boolean
	 * @since  4.1.0
	 */
	protected $hiddenDescription = true;

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		if ( ! AllVideoShareHelper::canDo() ) {
			$extension = isset( $this->element['extension'] ) ? $this->element['extension'] : 'com_allvideoshare';
			return '<div class="alert alert-warning" style="margin: 0;">' . Text::_( strtoupper( $extension ) . '_PREMIUM_LBL_PREMIUM_ONLY' ) . '</div>';
		}
		
		return '';
	}	

}