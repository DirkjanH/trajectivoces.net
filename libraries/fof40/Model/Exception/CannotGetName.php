<?php
/**
 * @package   FOF
 * @copyright Copyright (c)2010-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace  FOF40\Model\Exception;

defined('_JEXEC') || die;

use Exception;
use Joomla\CMS\Language\Text;

/**
 * Exception thrown when we can't get a Controller's name
 */
class CannotGetName extends \RuntimeException
{
	public function __construct( $message = "", $code = 500, Exception $previous = null )
	{
		if (empty($message))
		{
			$message = Text::_('LIB_FOF40_MODEL_ERR_GET_NAME');
		}

		parent::__construct( $message, $code, $previous );
	}

}