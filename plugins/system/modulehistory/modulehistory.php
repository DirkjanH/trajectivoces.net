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
class PlgSystemModuleHistory extends JPlugin
{

	public $app;

	public function onAfterInitialise()
	{
		//Inline editing is only enabled for frontend editing	
	
         $user = JFactory::getUser();
                
        //if user is guest lets bail
		if($user->get('guest'))
		{
			return;
		}

		
		if(!JComponentHelper::isInstalled('com_arkeditor'))
		{
			return;
		}
	    
		
		$params = JComponentHelper::getParams('com_arkeditor');
		
		if(!$params->get('enable_modulehistory',true))
		{
			return;
		}	
        		
		if (version_compare(JVERSION, '4.0', 'lt' ))
        {
		
			if( version_compare( JVERSION, '3.8.0', 'lt' )) 
			{
				jimport('legacy.table.module');
				JObserverMapper::addObserverClassToClass('JTableObserverARKExtension', 'JTableModule')	;
			}
			else
			{  		
				JObserverMapper::addObserverClassToClass('JTableObserverContenthistory', 'Joomla\\CMS\\Table\\Module', array('typeAlias' => 'com_modules.custom'));
			}
        }
        $component = JComponentHelper::getComponent('com_modules');
		$component->params->set('save_history',$component->params->get('save_history', 1));
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

        if(!( $table instanceof JTableModule))
          return;

       $typeAlias = $table->getTypeAlias();
       $aliasParts = $aliasParts = explode('.', $typeAlias);

       
	   if (JComponentHelper::getParams($aliasParts[0])->get('save_history', 0))
       {
           $id = $table->getId();
           $helper = new JHelper; 
           $data = $helper->getDataObject($table);
		   $versionNote = '';

		   Joomla\CMS\Versioning::store($typeAlias, $id, $data, '');
       }

    }

}