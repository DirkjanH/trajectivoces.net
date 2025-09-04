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
use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Session\Session;

/**
 * Class VideoField.
 *
 * @since  4.2.0
 */
class VideoField extends FormField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.2.0
	 */
	protected $type = 'video';
	
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.2.0
	 */
	protected function getInput() {
		$allowClear  = ( (string) $this->element['clear'] != 'false' );
		$allowSelect = ( (string) $this->element['select'] != 'false' );

		// Create the modal id
		$modalId = 'video_' . $this->id;

		// Add the modal field script to the document head
		HTMLHelper::_(
			'script',
			'system/fields/modal-fields.min.js',
			[ 'version' => 'auto', 'relative' => true ]
		);

		// Script to proxy the select modal function to the modal-fields.js file
		if ( $allowSelect ) {
			static $scriptSelect = null;

			if ( is_null( $scriptSelect ) ) {
				$scriptSelect = [];
			}

			if ( ! isset( $scriptSelect[ $this->id ] ) ) {
				Factory::getDocument()->addScriptDeclaration("
					function jselectvideo_"	. $this->id . "( value, title, object ) { 
						window.processModalSelect( 'Video', '" . $this->id . "', value, title, '', object ); 
					}
				");

				$scriptSelect[ $this->id ] = true;
			}
		}

		// Setup variables for display
		$extension = isset( $this->element['extension'] ) ? $this->element['extension'] : 'com_allvideoshare';
		$keyField  = isset( $this->element['key_field'] ) ? $this->element['key_field'] : 'id';
		
		$modalTitle = Text::_( strtoupper( $extension ) . '_MODAL_HEADER_SELECT_VIDEO' );

		$linkVideos = 'index.php?option=com_allvideoshare&amp;view=videos&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';
		$urlSelect  = $linkVideos . '&amp;function=jselectvideo_' . $this->id . '&amp;vfield=' . $keyField;		

		if ( $this->value ) {
			$db = Factory::getDbo();

			$query = $db->getQuery( true )
				->select( $db->quoteName( 'title' ) )
				->from( $db->quoteName( '#__allvideoshare_videos' ) );
			
			if ( $keyField == 'slug' ) {
				$query->where( $db->quoteName( 'slug' ) . ' = ' .  $db->quote( $this->value ) );
			} else {
				$query->where( $db->quoteName( 'id' ) . ' = ' . (int) $this->value );
			}

			$db->setQuery( $query );

			try {
				$title = $db->loadResult();
			} catch ( \RuntimeException $e ) {
				Factory::getApplication()->enqueueMessage( $e->getMessage(), 'error' );
			}
		}

		$title = empty( $title ) ? '- ' . $modalTitle . ' -' : htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' );

		// The current video display field
		$html = '';

		if ( $allowSelect || $allowClear ) {
			$html .= '<span class="input-group">';
		}

		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" readonly size="35">';

		// Clear video button
		if ( $allowClear ) {
			$html .= '<button'
				. ' class="btn btn-secondary' . ( $this->value ? '' : ' hidden' ) . '"'
				. ' style="border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem;"'
				. ' id="' . $this->id . '_clear"'
				. ' type="button"'
				. ' onclick="window.processModalParent( \'' . $this->id . '\' ); return false;">'
				. Text::_( 'JCLEAR' )
				. '</button>';
		}

		// Select video button
		if ( $allowSelect ) {
			$html .= '<button'
				. ' class="btn btn-primary hasTooltip' . ( $this->value ? ' hidden' : '' ) . '"'
				. ' id="' . $this->id . '_select"'
				. ' data-bs-toggle="modal"'
				. ' type="button"'
				. ' data-bs-target="#modalselect' . $modalId . '"'
				. ' title="' . HTMLHelper::tooltipText( strtoupper( $extension ) . '_MODAL_HEADER_SELECT_VIDEO' ) . '">'
				. Text::_( 'JSELECT' )
				. '</button>';
		}		

		if ( $allowSelect || $allowClear ) {
			$html .= '</span>';
		}

		// Select video modal
		if ( $allowSelect ) {
			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'modalselect' . $modalId,
				[
					'title'       => $modalTitle,
					'url'         => $urlSelect,
					'height'      => '400px',
					'width'       => '800px',
					'bodyHeight'  => 70,
					'modalWidth'  => 80,
					'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_( 'JLIB_HTML_BEHAVIOR_CLOSE' ) . '</button>',
				]
			);
		}

		// Note: class='required' for client side validation
		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="'
			. $this->id . '_id"'
			. $class . ' data-required="' . (int) $this->required
			. '" name="' . $this->name
			. '" data-text="'
			. htmlspecialchars( '- ' . Text::_( strtoupper( $extension ) . '_MODAL_HEADER_SELECT_VIDEO', true ) . ' -', ENT_COMPAT, 'UTF-8' )
			. '" value="' . $this->value . '">';

		return $html;
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   4.2.0
	 */
	protected function getLabel() {
		return str_replace( $this->id, $this->id . '_name', parent::getLabel() );
	}

}
