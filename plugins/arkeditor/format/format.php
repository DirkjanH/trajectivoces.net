<?php
/*------------------------------------------------------------------------
# Copyright (C) 2016-2017 WebxSolution Ltd. All Rights Reserved.
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
 * @subpackage  ArkEditor.Format
 */
class PlgArkEditorFormat extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) { 
		return	"editor.once( 'configLoaded', function() 
				{
					if(!CKEDITOR.loadedExtFormat && CKEDITOR.plugins.registered.format) // already loaded by core CKEDITOR JS file
					{
						delete CKEDITOR.plugins.registered.format;
						CKEDITOR.loadedExtFormat = true; 
					}	
				});
								
				editor.once( 'langLoaded', function(ev) 
				{
					if(CKEDITOR.lang[this.langCode] && CKEDITOR.lang[this.langCode].format) //if language exists for format plugions
						delete CKEDITOR.lang[this.langCode].format;
				});
				";
	}
		
	public function onInstanceLoaded(&$params) {}
}