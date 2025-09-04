<?php
/**
 * Akeeba Engine
 *
 * @package   akeebaengine
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Engine\Dump\Native;

defined('AKEEBAENGINE') || die();

use Akeeba\Engine\Dump\Base;
use Akeeba\Engine\Factory;

/**
 * Dump class for the "None" database driver (ie no database used by the application)
 */
class None extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

	/** @inheritDoc */
	protected function getTablesToBackup(): void
	{
	}

	/** @inheritDoc */
	protected function stepDatabaseDump(): void
	{
		Factory::getLog()->info("Reminder: database definitions using the 'None' driver result in no data being backed up.");

		$this->setState(self::STATE_FINISHED);
	}

	/** @inheritDoc */
	protected function getDatabaseNameFromConnection(): string
	{
		return '';
	}

	/** @inheritDoc */
	protected function getAllTables(): array
	{
		return [];
	}

	protected function _run()
	{
		Factory::getLog()->info("Reminder: database definitions using the 'None' driver result in no data being backed up.");

		$this->setState(self::STATE_POSTRUN);
	}

	protected function _finalize()
	{
		Factory::getLog()->info("Reminder: database definitions using the 'None' driver result in no data being backed up.");

		$this->setState(self::STATE_FINISHED);
	}
}
