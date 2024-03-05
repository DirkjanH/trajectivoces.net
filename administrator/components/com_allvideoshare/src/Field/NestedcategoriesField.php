<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace MrVinoth\Component\AllVideoShare\Administrator\Field;

\defined('JPATH_BASE') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Form\Field\ListField;
use \Joomla\CMS\HTML\HTMLHelper;

/**
 * Class NestedcategoriesField.
 *
 * @since  4.1.0
 */
class NestedcategoriesField extends ListField {
	
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.0
	 */
	protected $type = 'nestedcategories';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   4.1.0
	 */
	protected function getOptions()	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select( 'a.*' )
			->from( '#__allvideoshare_categories AS a' )
			->where( 'a.state = 1' )
			->order( 'a.name ASC' );

		// Get the options
		$db->setQuery( $query );

		try	{
			$items = $db->loadObjectList();
		} catch ( \RuntimeException $e ) {
			throw new \Exception( $e->getMessage(), 500 );
		}

		$children = array();

		if ( $items ) {
			foreach ( $items as $v ) {
				$v->title = $v->name;
				$v->parent_id = $v->parent;
				$pt = $v->parent;
				$list = @$children[ $pt ] ? $children[ $pt ] : array();
				array_push( $list, $v );
				$children[ $pt ] = $list;
			}
		}

		$list = HTMLHelper::_( 'menu.treerecurse', 0, '', array(), $children, 9999, 0, 0 );

		// Pad the option text with spaces using depth level as a multiplier
		$options = array();

		$key_field = ( isset( $this->element['key_field'] ) && $this->element['key_field'] == 'slug' ) ? 'slug' : 'id';

		foreach ( $list as $item ) {
			$option = new \stdClass;
			$option->value = ( $key_field == 'slug' ) ? $item->slug : $item->id;
			$option->text  = str_ireplace( '&#160;', '-', $item->treename );

			$options[] = $option;
		}

		// Merge any additional options in the XML definition
		$options = array_merge( parent::getOptions(), $options );

		return $options;
	}

	/**
	 * Method to get the field input for a nestedcategories field.
	 *
	 * @return  string  The field input.
	 *
	 * @since   4.1.0
	 */
	protected function getInput() {
		$data = $this->getLayoutData();

		if ( strpos( $this->value, ' ' ) !== false ) {
			$this->value = explode( ' ', trim( $this->value ) );
		}

		$data['value']   = $this->value;
		$data['options'] = $this->getOptions();
		
		return $this->getRenderer( $this->layout )->render( $data );
	}

}
