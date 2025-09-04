<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2016 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

class ARKModelEditPlugin extends JModelForm
{
	protected $_cache;

	public function getItem( $pk = null )
	{
		
		$app		= JFactory::getApplication();
        $cid 		= $app->input->get( 'cid', array(), 'array' );
		$id 		=  is_null($pk) ? current( $cid ) : $pk;
		
		if(!isset($this->_cache[$id]))
		{
		
			$user		= JFactory::getUser();
			$item 		= ARKHelper::getTable('plugin');

			// load the row from the db table
			$item->load( $id );
			
			// Hide CK's plugin
			if( !$item || in_array( $item->name, ARKHelper::getHiddenPlugins() ) )
			{
				$app->redirect( 'index.php?option=com_arkeditor&view=list', 'Could Not Load Plugin.', 'error' );
				return false;		
			}

			// fail if checked out not by 'me'
			if ($item->isCheckedOut( $user->get('id') ))
			{
				$msg = JText::sprintf( 'COM_ARKEDITOR_MSG_BEING_EDITED', JText::_( 'The plugin' ), ($item->title ?: $item->name) );
				$app->redirect( JRoute::_( 'index.php?option=com_arkeditor&view=list', false ), $msg, 'error' );
				return false;
			}

			// TOOLBARS
			$toolbars = $this->getToolbarList();
			$item->selections = $this->getSelectedToolbarList();

			if( !$item->selections )
			{
				$item->toolbars = 'none';
			}
			elseif( count( $item->selections ) == count( $toolbars ) )
			{
				$item->toolbars = 'all';
			}
			else
			{
				$item->toolbars = 'select';
			}

			// GROUPS
			$groups 		= $this->getUserGroupList();
			$allowedGroups 	= array();
			
			// re-order groups to match acl col
			foreach( $groups as $group )
			{
				$allowedGroups[] = $group->value;
			}

			if( !is_null( $item->acl ))
			{
				$allowedGroups = json_decode($item->acl);
			}

			if($item->acl == '[]')
			{
				$item->group = 'special';
			} 
			elseif(count($allowedGroups) == count($groups)) 
			{
				$item->group = 'all';
			} 
			else 
			{
				$item->group = 'select';
			}

			$item->groups	= $allowedGroups;
			$xmlPath = '';

			if($item->iscore) //AW get path for core plugins XML file
			{
				$path		= JPATH_COMPONENT.'/editor/plugins';
				$xmlPath 	= $path .'/'. $item->name .'.xml';
			}
			else
			{
				$path		= JPATH_PLUGINS.'/arkeditor/'.$item->name;
				$xmlPath 	= $path .'/'. $item->name .'.xml';
			}

			$item->xmlPath = 	$xmlPath;

			if($id)
			{
				$item->checkout( $user->get('id') );
	   
				if(JFile::exists($xmlPath ))
				{
					$data =  simplexml_load_file( $xmlPath );
					$item->description = (string) $data->description;
				}
				else
				{
					$item->xmlPath = '';
					$item->description = '';
				}
			} else {
				$item->type 		= 'plugin';
				$item->published 	= 1;
				$item->description 	= 'From XML install file';
				$item->icon 		= '';
				$item->params		= '';
			}

			$this->_cache[$pk] = $item;
		}

		return $this->_cache[$pk];
	}

	function getForm( $data = array(), $loadData = true )
	{
		  
        $form = $this->loadForm('com_arkeditor.editplugin', 'editplugin', array('control' => 'jform', 'load_data' => $loadData));
		
        return ( empty( $form ) ) ? false : $form;
	}


	function getPluginForm( $data = array(), $loadData = true )
	{
		
         $form = $this->loadForm('com_arkeditor.editplugin', 'editplugin', array('control' => 'jform', 'load_data' => $loadData));
		 
		$item = $this->getItem();
		
		if($item->xmlPath)
        {
			
			$content =  file_get_contents($item->xmlPath);
			
			$content = preg_replace( array('/\<params group="options">/i','/\<params>/i','/\<params(.*)\<\/params\>/is'), array('<params name="advanced">','<params name="basic">','<config><fields name="params"><fieldset$1</fieldset></fields></config>'), $content );
			$content = str_replace( array( '<install', '</install', '<params', '</params', '<param', '</param' ), array( '<form', '</form', '<fieldset','</fieldset', '<field', '</field' ), $content );
			
						
			// Get the plugin form.
			if (!$form->load($content, false, '//config'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}	
		 
		
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	function getSelectedToolbarList()
	{
		return JModelLegacy::getInstance( 'list', 'ARKModel' )->getSelectedToolbarList();
	}

	function getToolbarList()
	{
		$rows = array();
		arkimport('helper');
		$toolbars = ARKHelper::getEditorToolbars();

		if(!empty($toolbars))
		{
			foreach($toolbars as $toolbar)
			{
				$row = new stdclass;
				$row->text = ucfirst($toolbar); 
				$row->value = $toolbar;
				$rows[] = $row;
			}
		}
		return $rows;
	}
	

	function getUserGroupList()
	{
		return JModelLegacy::getInstance( 'list', 'ARKModel' )->getUserGroupList();
	}
}