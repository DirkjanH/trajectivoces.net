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
 *Ark inline content  Extension Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Extension.ArkManager
 */
 

 
class PlgExtensionArkManager extends JPlugin
{
	
	
	public $app;
	private $packageUninstall = false;
	
	public function onExtensionBeforeUninstall($eid)
	{
		
		if($this->app->input->get('ark.package.uninstall',false))
			return;
	
		
		if( empty($eid) )
			return;
		
		
		$row = JTable::getInstance('extension');
		$row->load($eid);

		$type =  $row->type;

		if( !in_array($type, array('plugin', 'component')))
			return;
        	
		if($type =='plugin')
		{	
			if($row->folder != 'editors')
				return;
		}	
			
          		
		if(in_array($row->element,array('arkeditor', 'com_arkeditor')))
		{	
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('extension_id')
			->from('#__extensions')
			->where('type = '.$db->quote('package'))
			->where('element = '.$db->quote('pkg_arkeditor'));
			
			$id = $db->setQuery( $query )->loadResult();	
		
			$installer = JInstaller::getInstance();
			if($installer->uninstall('package',$id))
			{
				$this->app->enqueueMessage(JText::sprintf('COM_INSTALLER_UNINSTALL_SUCCESS','package'));
				$this->app->redirect('index.php?option=com_installer');
			}	
		}
	}	
}