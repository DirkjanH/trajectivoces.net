<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

// -PC- J3.0 fix
if( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

class com_arkeditorInstallerScript
{
	protected $positions 	= array( 'ark_cpanel', 'ark_icon', 'ark_footer' );
	protected $modules 		= array( 'mod_arkcpanel', 'mod_arkstats', 'mod_arkpro', 'mod_arkvote','mod_arktip', 'mod_arkupdate' );
	
	
	function preflight( $type, $parent ) 
	{
		$jversion = new JVersion();
		// Installing component manifest file version
		$this->release = (string) $parent->getManifest()->version;

		// If fresh install, lang file can't be loaded yet so use the tmp dir one.
		$lang = JFactory::getLanguage();
		$lang->load( 'com_arkeditor' , dirname(__FILE__) );
		$lang->load( 'com_arkeditor.sys' , dirname(__FILE__) );
	}

	function install($parent)
	{
		$version 	= (string) $parent->getManifest()->version;
		$mainframe 	= JFactory::getApplication();
		$db 		= JFactory::getDBO();

		$query = "SELECT count(1) FROM #__modules"
		." WHERE position IN ( '" . implode( "', '", $this->positions ) . "' )";
		$db->setQuery( $query );
		$count = $db->loadResult();
		if($count)  $this->uninstall($parent);

		jimport('joomla.filesystem.folder');

		$src 	= 'components/com_arkeditor/modules/';
		$dest 	= 'modules/';

		foreach( $this->modules as $module )
		{
			if( !JFolder::copy( $src . $module, $dest . $module, JPATH_ADMINISTRATOR, true ) ){
				$mainframe->enqueueMessage( JText::sprintf( 'COM_ARKEDITOR_CUSTOM_INSTALL', $module . ' module!' ) );
			}
		}

		/*===========================================================> */
		/*==============================================> LEFT MODULES */
		/*===========================================================> */
		$row 			= JTable::getInstance('module');
		$row->module 	= '';
		$row->position 	= 'ark_icon';
		$row->published = 1;
		$row->showtitle = 1;
		$row->access 	= 1;
		$row->client_id = 1;
		$row->params 	= '{}';
		$row->content 	= '';
		$row->language 	= '*';

		/*$row->id 		= 0;
		$row->title 	= 'Dashboard';
		$row->module 	= 'mod_arkquickicon';
		$row->ordering = $row->getNextOrder( "position='ark_icon'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Control Panel icon Module data!') );
		}*/

		$row->id 		= 0;
		$row->title 	= 'Statistics';
		$row->module 	= 'mod_arkstats';
		$row->params 	= '{"moduleclass_sfx":"box-arkstats","icon":"bars"}';
		$row->ordering 	= $row->getNextOrder( "position='ark_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Statistical Module data!') );
		}

		$row->id 		= 0;
		$row->title 	= 'Spread The Love';
		$row->module 	= 'mod_arkvote';
		$row->params 	= '{"moduleclass_sfx":"box-arkvote","icon":"heart-2"}';
		$row->ordering = $row->getNextOrder( "position='ark_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Vote Module data!') );
		}

		$row->id 		= 0;
		$row->title 	= 'Update Available';
		$row->module 	= 'mod_arkupdate';
		$row->params 	= '{"moduleclass_sfx":"box-arkupdate","icon":"loop"}';
		$row->ordering = $row->getNextOrder( "position='ark_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Update Module data!') );
		}

		/*===========================================================> */
		/*=============================================> RIGHT MODULES */
		/*===========================================================> */		
		$row  = JTable::getInstance('module');
		$row->module 	= '';
		$row->position 	= 'ark_cpanel';
		$row->published = 1;
		$row->showtitle = 1;
		$row->access 	= 1;
		$row->client_id = 1;
		$row->params 	= '{}';
		$row->language 	= '*';

		$row->id 		= 0;
		$row->title 	= 'Ark Editor v' . $version;
		$row->module 	= 'mod_arkcpanel';
		$row->params 	= '{"moduleclass_sfx":"box-arkinfo","icon":"info-circle"}';
		$row->content 	= '';
		$row->ordering = $row->getNextOrder( "position='ark_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Ark Editor custom Module data!') );
		}
		
		
		$row->id 		= 0;
		$row->title 	= 'Tip of the Day';
		$row->module 	= 'mod_arktip';
		$row->content 	= '';
		$row->params 	= '{"moduleclass_sfx":"box-arktip","icon":"info-circle"}';
		$row->ordering = $row->getNextOrder( "position='ark_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Tip of the day data!') );
		}

		$row->id 		= 0;
		$row->title 	= 'GO PRO!';
		$row->module 	= 'mod_arkpro';
		$row->content 	= '';
		$row->params 	= '{"moduleclass_sfx":"box-arkpro","icon":"lightning"}';
		$row->ordering = $row->getNextOrder( "position='ark_cpanel'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Pro Module data!') );
		}

		/*===========================================================> */
		/*============================================> FOOTER MODULES */
		/*===========================================================> */
		$row 			= JTable::getInstance('module');
		$row->position 	= 'ark_footer';
		$row->published = 1;
		$row->showtitle = 1;
		$row->access 	= 1;
		$row->client_id = 1;
		$row->params 	= '{}';
		$row->content 	= '';
		$row->language 	= '*';

		$row->id 		= 0;
		$row->title 	= 'Dashboard';
		$row->module 	= 'mod_arkquickicon';
		$row->ordering = $row->getNextOrder( "position='ark_footer'" );
		if (!$row->store()) {
			$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL','Control Panel icon Module data!') );
		}

		jimport('joomla.filesystem.file');

	
		//Check System requirements for the editor 
		define('ARKEDITOR_BASE',JPATH_CONFIGURATION . '/plugins/editors/arkeditor/ckeditor');

		if(!JFolder::exists(ARKEDITOR_BASE))
		{
			$mainframe->enqueueMessage( JText::_('COM_ARKEDITOR_CUSTOM_INSTALL_SYSTEM_DETECTED_EDITOR_NOT_INSTALLED') );
			return;
		}

		$perms  = fileperms(JPATH_CONFIGURATION.'/index.php');
		$perms = (decoct($perms & 0777));

		$default_fperms = '0644';
		$default_dperms = '0755'; 

		if($perms == 777 || $perms == 666)
		{
			$default_fperms = '0666';
			$default_dperms = '0777'; 
		}

		$fperms = ARKEDITOR_BASE.'/config.js';

		if(!stristr(PHP_OS,'WIN') && JPath::canChmod(ARKEDITOR_BASE)  && $perms != decoct(fileperms($fperms) & 0777))
		{

			$path = ARKEDITOR_BASE.'/plugins';

			if(!JPath::setPermissions($path,$default_fperms,$default_dperms))
			{
				$mainframe->enqueueMessage( JText::_('COM_ARKEDITOR_CUSTOM_INSTALL_SYSTEM_DETECTED_INCORRECT_FILE_PERMISSONS_FOR_EDITOR') );
			}
		}

		//for upgrade
		$query = 'SELECT p.name FROM #__ark_editor_plugins p WHERE p.iscore = 0';
		$db->setQuery( $query );
		$results = $db->loadObjectList();

		if(!empty($results))
		{
			for($i = 0; $i < count($results);$i++)
			{
				if(JFolder::exists(JPATH_PLUGINS.'/editors/arkeditor/plugins/'.$results[$i]->name) && 
					!JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_arkeditor/editor/plugins/'.$results[$i]->name)
				)
				{
					$src 	= JPATH_PLUGINS.'/editors/'.'/arkeditor/plugins/'.$results[$i]->name;
					$dest 	= JPATH_ADMINISTRATOR.'/components/com_arkeditor/editor/plugins/'.$results[$i]->name;

					if( !JFolder::copy( $src, $dest) )
					{
						$mainframe->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_INSTALL_UNABLE_TO_MOVE_SPRINTF','base plugin .'.$results[$i]->name.' to ARK backup folder!') );
					}
				}
			}//end for loop
		}
		
		//fix remove component install file from the editor's folder
		$file = JPATH_ADMINISTRATOR.'/components/com_arkeditor/editor/com_arkeditor.xml';
		if(JFile::exists($file))
			JFile::delete($file);
	}
	
	function postflight( $type, $parent ) 
	{
		
		if($type != 'install')
		 return;
		
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true);
		$query->select('params')
			->from('#__extensions')	
			->where('type = '.$db->quote('component'))
			->where('element = '.$db->quote('com_arkeditor'));
			
		$db->setQuery($query);
		$params = $db->loadResult();
		
		if($params === false)
			throw new Exception('Failed to retrieve parameters from Ark Editor Component');

		if(!$params)
			$params = '{}';
			
		$params = new JRegistry($params);	
			
		//Store JCK css typography
		
		if(!$params->get('arktypographycontent',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_style.css');
			if($cssContent)
				$params->set('arktypographycontent',base64_encode($cssContent));
		}
		
		
		if(!$params->get('arkcustomtypographycontent',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_custom.css');
			if($cssContent)
				$params->set('arkcustomtypographycontent',base64_encode($cssContent));
		}
		
		
		if(!$params->get('bootstrap2',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_bootstrap2.css');
			if($cssContent)
				$params->set('bootstrap2',base64_encode($cssContent));
		}
		
		
		if(!$params->get('bootstrap3',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_bootstrap3.css');
			if($cssContent)
				$params->set('bootstrap3',base64_encode($cssContent));
		}
		
		
		if(!$params->get('bootstrap3gantry5',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_bootstrap3gantry5.css');
			if($cssContent)
				$params->set('bootstrap3gantry5',base64_encode($cssContent));
		}
		
		
		if(!$params->get('uikit2',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_uikit2.css');
			if($cssContent)
				$params->set('uikit2',base64_encode($cssContent));
		}
		
		if(!$params->get('uikit3',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_uikit3.css');
			if($cssContent)
				$params->set('uikit3',base64_encode($cssContent));
		}
		
		if(!$params->get('arkuikit',false))
		{
			$cssContent = file_get_contents(JPATH_PLUGINS.'/system/arktypography/install_arkuikit.css');
			if($cssContent)
				$params->set('arkuikit',base64_encode($cssContent));
		}
		
			
		$query->clear()
		->update('#__extensions')
		->set('params = '.$db->quote($params->toString()))
		->where('type = '.$db->quote('component'))
		->where('element = '.$db->quote('com_arkeditor'));
		
		$db->setQuery($query);
		if(!$db->execute())
			throw new Exception('Failed to update parameters for Ark Editor Component');
	}
	
	
    function update($parent) 
    {
			
		$this->install($parent);
		
		$db = JFactory::getDBO();
	
		$dbserver = 'mysql';
	
		switch (strtolower($db->name)) 
		{
			case 'mysql':
			case 'mysqli':
			case 'pdomysql':	
				$dbserver = 'mysql';
				break;
			case 'sqlsrv':
			case 'sqlite':
			case 'sqlazure':	
				$dbserver = 'sqlsrv';
				break;	
			case 'postgresql':
				$dbserver = 'postgres';
				break;
		}
		
		if(method_exists($parent, 'extension_root')) {
			$sqlfile = $parent->getPath('extension_root').'/sql/'.$dbserver.'.sql';
		} else {
			$sqlfile = $parent->getParent()->getPath('extension_root').'/sql/'.$dbserver.'.sql';
		}
		
		
		
		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);
		
		if ($buffer !== false) {
			jimport('joomla.installer.helper');
			$queries = JDatabaseDriver::splitSql($buffer);
	
			if (count($queries) != 0) {
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query[0] != '#') {
						$db->setQuery($query);
						if (!$db->execute()) {
							if( !class_exists( 'ARKHelper' ) ) require_once( JPATH_COMPONENT_ADMINISTRATOR.'/helper.php' );

							ARKHelper::error( JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));
							return false;
						}
					}
				}
			}
		}
		
    }
	
	function uninstall($parent)
	{
		$app 	= JFactory::getApplication();
		$db 	= JFactory::getDBO();
		$path 	= JPATH_ADMINISTRATOR.'/modules/';
		jimport('joomla.filesystem.folder');

		foreach( $this->modules as $module )
		{
			if( JFolder::exists( $path . $module ) && !JFolder::delete( $path . $module ) ){
				$app->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_UNINSTALL', $module . ' module!') );
			}
		}

		$sql = $db->getQuery( true );
		$sql->select("id")
		->from("#__modules")
		->where("position IN ( '" . implode( "', '", $this->positions ) . "' )");
		$db->setQuery( $sql );
		$ids = $db->loadColumn();
		
		if($ids)
		{
			
			for($i = 0; $i < count($ids); $i++)
				$aids[$i] = 'com_modules.module.' . $ids[$i];
						
			$sql->clear()
				->delete( '#__assets' )
				->where( "title IN ( '" . implode( ", ", $aids ) ."' )");
			if( !$db->setQuery( $sql )->execute() )
			{
				$app->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_UNINSTALL','Ark Editor\'s modules assets data!') );
			}
						
			$sql->clear()
				->delete( '#__modules' )
				->where( "id IN ( " . implode( ", ", $ids ) ." )");
			if( !$db->setQuery( $sql )->execute() )
			{
				$app->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_UNINSTALL','Ark Editor\'s modules data!') );
			}
		}
		
		// For some reason we need to remove the row from the asset table?!
		$sql = $db->getQuery( true );
		$sql->delete( '#__assets' )
			->where( 'name = '.$db->quote('com_arkeditor') )
			->where( 'title = '.$db->quote('com_arkeditor') );
		if( !$db->setQuery( $sql )->execute() )
		{
			$app->enqueueMessage( JText::sprintf('COM_ARKEDITOR_CUSTOM_UNINSTALL','Unable to remove ARK asset record!') );
		}
	}
}