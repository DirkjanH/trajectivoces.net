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
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
class PlgArKEventsExtraWidgets extends JPlugin
{

	public $app;


	public function onInstanceCreated(&$params)
	{
		$plugins = JPluginHelper::getPlugin('arkwidget-editor');
		
		$names = array();
		
		$base = str_replace('/administrator','',JURI::base(true));
		
		$includes = array();
		
		foreach($plugins as $plugin)
		{
			$names[] = $plugin->name;
			$includes[] = "CKEDITOR.plugins.addExternal('".$plugin->name."','".$base."/plugins/arkwidget-editor/".$plugin->name."/".$plugin->name."/plugin.js');";
		}	
		
		if($names)
			return "
					editor.on( 'configLoaded', function() {

					
					//load in extra plugins
					if(	this.config.extraPlugins)
						this.config.extraPlugins += ',".implode(',',$names)."';
					else
						this.config.extraPlugins = '".implode(',',$names)."';
							
					".implode(chr(13),$includes)."	
			
					});
				";
		return  null;	
	}
}
