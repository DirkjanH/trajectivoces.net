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
 * @subpackage  ArkEditor.ImageManager
 */
class PlgArkEditorImageManager extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params){
	    
         if(version_compare(JVERSION, '4.0', 'ge' ))
         {
        
            return "
				    editor.on( 'configLoaded', function()
				    {
					    editor.config.Joomla4 = 1;
                    })";
         }
        
        return ''; 
	}
	
	public function onInstanceLoaded(&$params) {}
}
