<?php

/*------------------------------------------------------------------------
# Copyright (C) 2021 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

jimport( 'joomla.event.event' );

abstract class ArkListener extends JEvent
{
    private $dispatcher;
    
    function __construct( &$subject )
	{
		parent::__construct( $subject );

        if (version_compare(JVERSION, '4.0', 'ge' ))
        {
           $this->setDispatcher($subject);
		   $this->registerListeners();
        }
    }


    public function registerListeners()
    {
	   

	    $reflectedObject = new \ReflectionObject($this);
	    $methods = $reflectedObject->getMethods(\ReflectionMethod::IS_PUBLIC);

	    /** @var \ReflectionMethod $method */
	    foreach ($methods as $method)
	    {
		    if (substr($method->name, 0, 2) !== 'on')
		    {
			    continue;
		    }
	        // Everything checks out, this is a proper listener.
		    $this->registerListener($method->name);
		}
    }


    private function registerListener(string $methodName)
	{
		$this->getDispatcher()->addListener(
			$methodName,
			function (Joomla\CMS\Event\AbstractEvent $event) use ($methodName)
			{
				// Get the event arguments
				$arguments = $event->getArguments();

				// Extract any old results; they must not be part of the method call.
				$allResults = [];

				if (isset($arguments['result']))
				{
					$allResults = $arguments['result'];

					unset($arguments['result']);
				}

				// Convert to indexed array for unpacking.
				$arguments = array_values($arguments);

				$result = $this->{$methodName}(...$arguments);

				// Ignore null results
				if ($result === null)
				{
					return;
				}

				// Restore the old results and add the new result from our method call
				$allResults[]    = $result;
				$event['result'] = $allResults;
			}
		);
	}
    
    
    /**
	 * Get the event dispatcher.
	 *
	 * @return  DispatcherInterface
	 *
	 * @since   1.2.0
	 * @throws  \UnexpectedValueException May be thrown if the dispatcher has not been set.
	 */
	final public function getDispatcher()
	{
		if ($this->dispatcher)
		{
			return $this->dispatcher;
		}

		throw new UnexpectedValueException('Dispatcher not set in ' . __CLASS__);
	}

	/**
	 * Set the dispatcher to use.
	 *
	 * @param   DispatcherInterface  $dispatcher  The dispatcher to use.
	 *
	 * @return  $this
	 *
	 * @since   1.2.0
	 */
	final public function setDispatcher($dispatcher)
	{
		$this->dispatcher = $dispatcher;

		return $this;
	}

}