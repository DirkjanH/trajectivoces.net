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
use \Joomla\CMS\Uri\Uri;
use \MrVinoth\Component\AllVideoShare\Site\Helper\AllVideoShareHelper;

/**
 * Class ClearcacheField.
 *
 * @since  4.1.2
 */
class ClearcacheField extends FormField {

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.1.2
	 */
	protected $type = 'clearcache';

	/**
	 * Hide the description when rendering the form field.
	 *
	 * @var    boolean
	 * @since  4.1.2
	 */
	protected $hiddenDescription = true;

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   4.1.2
	 */
	protected function getInput() {	
		$app = Factory::getApplication();

		$wa = $app->getDocument()->getWebAssetManager();

		if ( ! $wa->assetExists( 'script', 'com_allvideoshare.admin' ) ) {
			$wr = $wa->getRegistry();
			$wr->addRegistryFile( 'media/com_allvideoshare/joomla.asset.json' );
		}

		$wa->useScript( 'com_allvideoshare.admin' );

		return sprintf(
			'<button type="button" id="avs-clear-cache" class="btn btn-success" data-baseurl="%1$s" data-label="%2$s">%2$s</button>',
			Uri::root(),
			Text::_( 'COM_ALLVIDEOSHARE_FORM_LBL_CLEAR_API_CACHE' )
		);
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   4.1.2
	 */
	public function getLabel() {
		return '&nbsp;';
	}	

}