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
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  System.inlineContent
 */
class PlgArKEventsPasteFromWord extends JPlugin
{

	public $app;
	
	public function onInstanceCreated(&$params)
	{
			return "
					editor.on( 'configLoaded', function() {
						this.config.pasteFromWordCleanupFile = this.config.baseHref + 'plugins/editors/arkeditor/ckeditor/plugins/pastefromword/filter/'+ (this.config.pasteFromWordCleanupFile || 'default') + '.js'
					});";
	}

}
