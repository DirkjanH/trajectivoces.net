<?php
/*------------------------------------------------------------------------
# Copyright (C) 2017-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark  Editor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEditor.JMenu
 */
class PlgArkEditorJmenu extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) {
        return "
				editor.on( 'configLoaded', function()
				{
					editor.config.jhash = '".JSession::getFormToken()."';
				});";		
	}
		
	public function onInstanceLoaded(&$params) {}
}
