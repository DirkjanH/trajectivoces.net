<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2015 WebxSolution Ltd. All Rights Reserved.
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
class PlgSystemArkmediaObserver extends JPlugin
{

	public $app;

	public function onAfterInitialise()
	{
		//Inline editing is only enabled for frontend editing	
    	if($this->app->isClient('site'))
			return; 
	
		if(!JComponentHelper::isInstalled('com_arkeditor'))
			return;
		
	    if(!JComponentHelper::isInstalled('com_arkmedia'))
			return;
		
		if (version_compare(JVERSION, '4.0', 'lt' ) )
        {
			//Map table observers
			require_once JPATH_PLUGINS.'/system/arkmediaobserver/observer/extension.php';

			if( version_compare( JVERSION, '3.8.0', 'lt' ) ) 
			{
				JObserverMapper::addObserverClassToClass('JTableObserverARKExtension', 'JTableExtension');
			}
			else
			{
				JObserverMapper::addObserverClassToClass('JTableObserverARKExtension', 'Joomla\\CMS\\Table\\Extension');
			}
		}
	}
	
	public function onTableAfterStore($event)
	{
		
       $stdEvent = json_decode(json_encode($event));
       $event = unserialize(
            preg_replace('@^O:8:"stdClass":@','O:38:"Joomla\\CMS\\Event\\Table\\AfterStoreEvent":',
            serialize($stdEvent))
       );
	   
	   $table	= $event['subject'];
	   $result = $event['result']; 
	   
	   if(!($table instanceof JTableExtension))
		   return;
          
        if ($result)
		{
			
			if($table->element == 'com_arkmedia')
			{
				$params = new JRegistry($table->params);
				
				$locations = (array) $params->get('folder-locations');
				
				$imagePath = $locations['images']; 
				$docPath = $locations['documents']; 
				
			
				$atable = JTable::getInstance('extension');
				$atable->load(array('element'=>'com_arkeditor'));
				
				if(!$atable->extension_id)
					return;
				
				$config =  new JRegistry($atable->params);
				
				$update = false;
				if($imagePath != $config->get('imagePath' ,'images')  || $docPath != $config->get('filePath' ,'files') )
					$update = true;
				
				if($update)
				{
					$config->set('imagePath', $imagePath);
					$config->set('filePath', $docPath);
					$bindData = $config->toArray();
					$atable->save(array('params'=>$bindData));
				}

			}
			elseif($table->element == 'com_arkeditor')
			{
				$params = new JRegistry($table->params);
					
				$imagePath = $params->get('imagePath'); 
				$docPath =  $params->get('filePath');  
				
			
				$mtable = JTable::getInstance('extension');
				$mtable->load(array('element'=>'com_arkmedia'));
				
				if(!$mtable->extension_id)
					return;
				
				$config =  new JRegistry($mtable->params);
				
				$update = false;
				$locations = (array) $config->get('folder-locations',array());
				
				if(!empty($locations))
				{	
					if($imagePath != $locations['images'] || $docPath != $locations['documents'] )
						$update = true;
				}
				else
					$update = true;
				
				if($update)
				{
					$locations['images'] = $imagePath;
					$locations['documents'] = $docPath;
					$config->set('folder-locations', $locations);
					$bindData = $config->toArray();
					$mtable->save(array('params'=>$bindData));
				}
				
			}	

		}	
	}
}

