<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use MrVinoth\Component\AllVideoShare\Administrator\Extension\AllVideoShareComponent;

/**
 * The AllVideoShare service provider.
 *
 * @since  4.1.0
 */
return new class implements ServiceProviderInterface {

	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.1.0
	 */
	public function register( Container $container ) {
		$container->registerServiceProvider( new CategoryFactory( '\\MrVinoth\\Component\\AllVideoShare' ) );
		$container->registerServiceProvider( new MVCFactory( '\\MrVinoth\\Component\\AllVideoShare' ) );
		$container->registerServiceProvider( new ComponentDispatcherFactory( '\\MrVinoth\\Component\\AllVideoShare' ) );
		$container->registerServiceProvider( new RouterFactory( '\\MrVinoth\\Component\\AllVideoShare' ) );

		$container->set(
			ComponentInterface::class,
			function( Container $container ) {
				$component = new AllVideoShareComponent( $container->get( ComponentDispatcherFactoryInterface::class ) );

				$component->setRegistry( $container->get( Registry::class ) );
				$component->setMVCFactory( $container->get( MVCFactoryInterface::class ) );
				$component->setCategoryFactory( $container->get( CategoryFactoryInterface::class ) );
				$component->setRouterFactory( $container->get( RouterFactoryInterface::class ) );

				return $component;
			}
		);
	}
};
