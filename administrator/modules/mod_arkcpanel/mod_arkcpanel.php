<?php
/*------------------------------------------------------------------------
# Copyright (C) 2018-2019 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

if (!defined( '_ARK_CPANEL_MODULE' ))
{
	/** ensure that functions are declared only once */
	define( '_ARK_CPANEL_MODULE', 1 );
	
	$path = JPATH_ADMINISTRATOR.'/components/com_arkeditor/com_arkeditor.xml';
	$xml = simplexml_load_file($path);
	$version = (string)  $xml->version; 

	require JModuleHelper::getLayoutPath('mod_arkcpanel', $params->get('layout', 'default'));

}