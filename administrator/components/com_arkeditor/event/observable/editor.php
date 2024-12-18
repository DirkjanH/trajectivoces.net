<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

jimport('joomla.event.dispatcher');
jimport('joomla.base.observable'); //AW added for Joomla 2.5


if(!class_exists('JObservable'))
{
    abstract class JObservable {}
}

class ARKEditorObservable extends JObservable
{
	/**
	 * Plugin  Manager controller  event handler name
	 *
	 * @var	object
	 */
	var $eventHandlerName = null;
	
	/**
	 * Plugin  Manager controller  event handler object
	 *
	 * @var	object
	 */
	var $_eventHandler = null;

	/**
	 * constructor
	 *
	 * @access	protected
	 * @param	string	The event handler
	 */
	function __construct($eventHandlerName)
	{
		$eventListenerClassName = 'ARK' . JString::ucfirst($eventHandlerName) . 'ControllerListener';

		if(class_exists($eventListenerClassName))
		{
			$this->_eventHandler = new $eventListenerClassName($this);
			
		}
		else
		{
			ARKHelper::error('No Event listener ' . $eventListenerClassName .' class found.'); 

		}
	}

	/**
	 * Update Editor
	 *
	 * @param	string event
	 *@param  array arguments for function call of event handller
	 */
	function update($event, $args )
	{
	
		$args['event'] = $event;

		$this->_eventHandler->update($args);
	}

	/**
	 * Reload Toolbar
	 * 
	 * Used to restore toolbar if editor is re-installed
	 */	
	function getEventHandler()
	{
		return $this->_eventHandler;
	}//end function getEventHandler
}