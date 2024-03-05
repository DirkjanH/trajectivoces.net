<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/**
 * AllVideoShare Listhelper.
 *
 * @since  4.1.0
 */
abstract class JHtmlListhelper {

	static function toggle( $value, $view, $field, $i ) {
		$states = array(
			0 => array( 'icon-unpublish', Text::_( 'Toggle' ), '' ),
			1 => array( 'icon-publish', Text::_( 'Toggle' ), '' )
		);

		$state  = ArrayHelper::getValue( $states, (int) $value, $states[0] );
		$text   = '<span aria-hidden="true" class="' . $state[0] . '"></span>';
		$html   = '<a href="javascript:void(0);" class="tbody-icon ' . $state[2] . '"';
		$html  .= 'onclick="return Joomla.toggleField( \'cb' . $i . '\',\'' . $view . '.toggle\',\'' . $field . '\' )" title="' . Text::_( $state[1] ) . '">' . $text . '</a>';

		return $html;
	}

}
