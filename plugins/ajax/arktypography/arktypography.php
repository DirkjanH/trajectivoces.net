<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2014 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark Typography Ajax Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Ajax.arktypography
 */

error_reporting(E_ERROR & ~E_NOTICE);
 
class PlgAjaxArkTypography extends JPlugin
{

	public $db, $app;
	
    public function onAjaxArktypography()
	{
		$type = $this->app->input->get('task','editor');  
		//Print out CSS
		$this->_CSS($type);
	}	
	
	
	private function _CSS($type)
	{
		$query = $this->db->getQuery(true);

		$query->select('params')
			->from('#__extensions')
			  ->where('type ='	. $this->db->Quote('component'))
			->where('element ='	.$this->db->Quote('com_arkeditor'));

		$this->db->setQuery($query);	
		$results = $this->db->loadResult();

		$params =  @ new JRegistry($results);

		
		if($type == 'editor')
			$contentCSS = $params->get('arktypographycontent','');
		else
			$contentCSS = $params->get('arkcustomtypographycontent','');
		
		if($contentCSS)
			$contentCSS = base64_decode($contentCSS);

    	// Remove comments
		$contentCSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $contentCSS);

		// Remove space after colons
		$contentCSS = str_replace(': ', ':', $contentCSS);

		// Remove whitespace
		$contentCSS = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $contentCSS);

		// Enable GZip encoding.

		if(JFactory::getConfig()->get('gzip',false))
		{
			if(!ini_get('zlib.output_compression') && ini_get('output_handler')!='ob_gzhandler') //if server is configured to do this then leave it the server to do it's stuff
			ob_start("ob_gzhandler");
		}

		// Enable caching
		header('Cache-Control: public'); 

		// Expire in one day
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT'); 

		// Set the correct MIME type, because Apache won't set it for us
		header("Content-type: text/css");

		// Write everything out
		echo $contentCSS;
		exit;
	}	
	
}