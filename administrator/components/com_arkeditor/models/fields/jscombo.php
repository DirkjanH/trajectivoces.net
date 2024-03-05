<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldJSCombo extends JFormFieldList
{
	protected $type = 'JSCombo';
	
	public static $ElementJSComboJSWritten = FALSE;

	protected function getInput()
	{
		if (!static::$ElementJSComboJSWritten) 
		{
			$file = dirname(__FILE__) . "/jscombo.js";
			$url  = str_replace(JPATH_ROOT, JURI::root(true), $file);
			$url  = str_replace(DIRECTORY_SEPARATOR, "/", $url);
			$doc  = JFactory::getDocument();
			$doc->addScript( $url );
			$doc->addScriptDeclaration( 'jQuery(document).ready(function (){ new JSComboParam(); });' );	// Use jQuery to delay execution of our code till last
			static::$ElementJSComboJSWritten = TRUE;
		}

		$doc->addScriptDeclaration("
			jQuery(document).ready(function ()
			{
				var id = '".$this->id."';
				var text = jQuery('#'+id.replace('Switcher',''));
				
				if(jQuery('#'+id+'_chzn').length)
				{
					jQuery('#'+id).chosen().change(function() {
						text.val(jQuery(this).val());
					});
				}
			});
		");	

		$this->class = $this->class ? 'ark_combo' . chr( 32 ) .(string)$this->class : 'ark_combo';

		return parent::getInput();
	}
}