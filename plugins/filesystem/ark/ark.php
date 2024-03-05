<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  FileSystem.Local
 *
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 
 */

defined('_JEXEC') or die;


use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Media\Administrator\Event\MediaProviderEvent;
use Joomla\Component\Media\Administrator\Provider\ProviderInterface;



	
/**
 * FileSystem Local plugin.
 *
 * The plugin to deal with the local filesystem in Media Manager.
 *
 * @since  4.0.0
 */
class PlgFileSystemArk extends CMSPlugin implements ProviderInterface
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Setup Providers for Local Adapter
	 *
	 * @param   MediaProviderEvent  $event  Event for ProviderManager
	 *
	 * @return   void
	 *
	 * @since    4.0.0
	 */
	public function onSetupProviders(MediaProviderEvent $event)
	{

	   
		list($adapter,$path) = explode(':',JFactory::getApplication()->input->getString('path','local-images:/'));
	   
		if($adapter == 'ark-files')
		{

			$providerManager = $event->getProviderManager();
			
			$unregisterProviders = function() {
				$this->providers = [];
			};
			
			$unregisterProviders->call($providerManager);

			$providerManager->registerProvider($this);

		}
	}

	/**
	 * Returns the ID of the provider
	 *
	 * @return  string
	 *
	 * @since  4.0.0
	 */
	public function getID()
	{
		return $this->_name;
	}

	/**
	 * Returns the display name of the provider
	 *
	 * @return string
	 *
	 * @since  4.0.0
	 */
	public function getDisplayName()
	{
		return Text::_('Ark');
	}

	/**
	 * Returns and array of adapters
	 *
	 * @return  \Joomla\Component\Media\Administrator\Adapter\AdapterInterface[]
	 *
	 * @since  4.0.0
	 */
	public function getAdapters()
	{
		$adapters = [];
		$directories = $this->params->get('directories', '[{"directory": "files"}]');

		// Do a check if default settings are not saved by user
		// If not initialize them manually
		if (is_string($directories))
		{
			$directories = json_decode($directories);
		}

		foreach ($directories as $directoryEntity)
		{
			if ($directoryEntity->directory)
			{
				$directoryPath = JPATH_ROOT . '/' . $directoryEntity->directory;
				$directoryPath = rtrim($directoryPath) . '/';

				$adapter = new \Joomla\Plugin\Filesystem\Local\Adapter\LocalAdapter(
					$directoryPath, $directoryEntity->directory
				);

				$adapters[$adapter->getAdapterName()] = $adapter;
			}
		}

		return $adapters;
	}
}
