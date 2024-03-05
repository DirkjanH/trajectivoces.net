<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark  Editor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEditor.Treelink
 */
class PlgArkEditorXml extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) {
		
		 return "editor.once( 'configLoaded', function() 
				{
					if(!CKEDITOR.loadedExtXML && CKEDITOR.plugins.registered.xml) // already loaded by core CKEDITOR JS file
					{
						delete CKEDITOR.plugins.registered.xml;
						CKEDITOR.loadedExtXML = true; 
					}	
				
				});
			";    
	}
		
	public function onInstanceLoaded(&$params) {}
}
