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
 * @subpackage  ArkEvents.Format
 */

class PlgArKEventsFormat extends JPlugin
{
	
	protected $db;
	
	
	public function onInstanceCreated(&$params)
	{
		$query = $this->db->getQuery(true);
		
		$query->select('params')
					->from('#__extensions')
					->where('folder = '. $this->db->quote('arkeditor'))
					->where('element = '.$this->db->quote('format'));
		$this->db->setQuery( $query );
		
		$result = $this->db->loadResult();
		
		if($result)
		{	
			$pconfig = new JRegistry($result);
			$tags = $pconfig->get('format_tags',array(
            
            
            ));
            if(!empty($tags))
            {
			    $texts = $pconfig->get('format_tags_text',array(
                
                
                
                ));
			    $configText = "";
			
			    for($i = 0; $i < count($tags); $i++)
			    {
				    $tag = strtolower($tags[$i]);
				    $configText.="this.config.format_".$tag."={element:'".$tag."'};".chr(13);
			    }
			    $configText .="this.config.format_tags_texts = '".implode(";",$texts)."'";
			
			    return "editor.on( 'configLoaded', function() 
				    {
					    //Define format tags
					    ".$configText."			
				    });";
            }
        }
		return '';	
	}
}