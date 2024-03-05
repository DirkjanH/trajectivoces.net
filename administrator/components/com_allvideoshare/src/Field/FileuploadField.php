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

use \Joomla\CMS\Form\Field\FileField;
use \Joomla\CMS\Language\Text;

/**
 * Class FileuploadField.
 *
 * @since  4.1.0
 */
class FileuploadField extends FileField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'fileupload';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		$classes = array( 'form-control' );
		if ( ! empty( $this->class ) ) {
			$classes[] = $this->class;
		}

		$html = '<div class="avs-form-field-type mb-2">
			<div class="form-check form-check-inline avs-form-field-type-url">
				<input class="form-check-input" type="radio" name="' . $this->id . '_field_type" id="' . $this->id . '_field_type_url" value="url" checked="checked" />
				<label class="form-check-label" for="' . $this->id . '_field_type_url">' . Text::_( 'COM_ALLVIDEOSHARE_FORM_LBL_DIRECT_URL' ) . '</label>
			</div>
			<div class="form-check form-check-inline avs-form-field-type-upload">
				<input class="form-check-input" type="radio" name="' . $this->id . '_field_type" id="' . $this->id . '_field_type_upload" value="upload" />
				<label class="form-check-label" for="' . $this->id . '_field_type_upload">' . Text::_( 'COM_ALLVIDEOSHARE_FORM_LBL_UPLOAD_FILE' ) . '</label>
			</div>
		</div>
		<div class="avs-form-field-url">
			<input type="text" name="' . $this->name . '" id="' . $this->id . '_url" class="' . implode( ' ', $classes ) . '" placeholder="' . Text::_( 'COM_ALLVIDEOSHARE_FORM_DESC_DIRECT_URL' ) . '" value="' . $this->value . '" />
		</div>
		<div class="avs-form-field-upload" style="display: none">
			' . $this->getRenderer( $this->layout )->render( $this->getLayoutData() ) . '
		</div>';

		return $html;
	}

}
