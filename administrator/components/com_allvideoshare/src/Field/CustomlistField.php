<?php
/**
 * @version    4.2.0
 * @package    Com_Allvideoshare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Field;

\defined( 'JPATH_PLATFORM' ) or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Form\Field\ListField;
use \Joomla\CMS\Language\Text;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Class CustomlistField.
 *
 * @since  4.1.0
 */
class CustomlistField extends ListField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'customlist';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		if ( ! AllVideoShareHelper::canDo() ) {
			$extension = isset( $this->element['extension'] ) ? $this->element['extension'] : 'com_allvideoshare';
			return '<label class="control-label text-danger">' . Text::_( strtoupper( $extension ) . '_PREMIUM_LBL_PREMIUM_ONLY' ) . '</label>';
		}

		$data = $this->getLayoutData();

		$data['options'] = (array) $this->getOptions();

		return $this->getRenderer( $this->layout )->render( $data );
	}

}
