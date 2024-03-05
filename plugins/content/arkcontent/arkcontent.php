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
 * Ark Inline content editing Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.ArkContent
 */
  
use Joomla\Registry\Registry;

JLoader::registerPrefix('ARK', JPATH_PLUGINS . '/content/arkcontent');

jimport('joomla.filesystem.file');
 
class PlgContentArkContent extends JPlugin
{

	protected $app;
	protected $db;
	private $_extraTypes = array();
	private $_path = '';
	private $option;
	private $elements;

    /**
     *  Used by On Drag upload, to reply success if file already exists
     */ 
    public function onContentBeforeSave( $context, $object_file, $isNew )
    {
        if( 
            $context != 'com_media.file' 
            || !$isNew
            || !$this->app->input->get( 'ARKInline', FALSE )
            || !JFile::exists($object_file->filepath)
          )
        {
            return true;
        }

        $returnUrl          = str_replace(JPATH_ROOT, '',  $object_file->filepath);
        $name               = ltrim(str_replace('\\', '/', $returnUrl),'/');
        $output             = array
            (
                'uploaded'  => 1,
                'filename'  =>  $object_file->name,
                'url'       => $name
            );
		echo json_encode($output);
        die;

    }//end function onContentBeforeSave

	public function onContentPrepare($context, $article, &$params, $page = 0)
	{

	    //Inline editing is only enabled for frontend editing	
		if ($this->app->isClient('administrator'))
		{
			return;
		}
		
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
		
		
        $cParams = JComponentHelper::getParams('com_arkeditor');
		if(empty($cParams) ||!$cParams->get('enable_inline',true))
		{
			$this->isEnabled = false;
			return;
		}	

       if(!JPluginHelper::isEnabled('editors','arkeditor'))
            return;	
		
        if(!JPluginHelper::isEnabled('system','inlinecontent'))
            return;	

        if(!$this->app->input->get('ark_inine_enabled',false))
            return;	
		
		$query = $this->db->getQuery(true);
		
		$query->select('element')
			->from('#__ark_editor_inline_views');
		$this->db->setQuery($query);	
		$this->elements = $this->db->loadColumn();
		
		$autoDrawEditableRegionsComponentlist = $cParams->get('component_auto_enable_editable_regions_list',array());

		$this->option = $this->app->input->get('option','');

		if(!empty($autoDrawEditableRegionsComponentlist) && !in_array($this->option,$autoDrawEditableRegionsComponentlist))
			return;
	
		$useArrayParameters = false;
		
		if(is_array($params))
		{	
			$parameters = new Registry($params);
			$useArrayParameters = true;
		}
		else
		{
			if(is_object($params)) 
				$parameters = clone $params;
			else
				$parameters = new Registry;
		}	
		
		if($parameters instanceof JObject || $parameters instanceof stdClass )
		{
			 $registry = new Registry;
             $parameters = $registry->loadObject($parameters);  
		}
        elseif( $parameters instanceof Registry)
        {
            //Do nothing
        }	
        else
        {
  			return; // invalid data type so return.
		}
		
		//parameters should be a JRegistry object by this point
		if(!empty($parameters->toArray()) && $parameters->get('inline') === false )
		{
			return;
		}
		
		$option = $this->app->input->get('arkoption');
		$extension = str_replace('com_','',$option);
			
		$classname = 'ARKExtensions'.ucfirst($extension);
               
			   
		if(!class_exists($classname))
			return;	
			   
        $instance =   new $classname($context, $article, $parameters );

	    $instance->prepare();

        $parameters = $instance->params;
		
		$this->_path = JPATH_PLUGINS . '/content/arkcontent/contexts/'.$extension;
		
		//Now lets check for exra types for this extension for inline editing if we find any lets process them as well
		if(JFolder::exists($this->_path))
		{
			$query = $this->db->getQuery(true);
			$query->select('element')
				->from('#__ark_editor_inline_views')
				->where($this->db->quoteName('parent').' = '. $option);
		
			$this->db->setQuery($query);
			$this->_extraTypes = $this->loadColumn();
			
			if(!empty($this->_extraTypes))
			{
				foreach($this->_extraTypes as $extraType)
				{
					if(!JFolder($this->_path.'/'.$extraType))
						continue;
					
					$classname = 'ARKExtensions'.ucfirst($extension).ucfirst($extraType);
					$instance =   new $classname($context, $article, $parameters );
					$instance->prepare();
					$parameters = $instance->params;
				}
			}	
		}
		
		if($useArrayParameters)
			$params = $parameters->toArray();
		elseif($parameters instanceof JObject )
		{
			$props = $parameters->toArray();
			$params = new JObject($props);
		}
        elseif( $params instanceof Registry)
        {
            $params = $parameters;
        }
		else
		{	
			$params =  $parameters->toObject();
		}	
	}	
	
		
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
	{

		//Inline editing is only enabled for frontend editing	
		if ($this->app->isClient('administrator'))
		{
			return;
		}
		
	
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

        $cParams = JComponentHelper::getParams('com_arkeditor');
		if(empty($cParams) ||!$cParams->get('enable_inline',true))
		{
			$this->isEnabled = false;
			return;
		}	
                
        if(!JPluginHelper::isEnabled('editors','arkeditor'))
            return;	
		
        if(!JPluginHelper::isEnabled('system','inlinecontent'))
            return;	
		
        if(!$this->app->input->get('ark_inine_enabled',false))
            return;	
		
		$autoDrawEditableRegionsComponentlist = $cParams->get('component_auto_enable_editable_regions_list', array());
		
		if(!empty($autoDrawEditableRegionsComponentlist) && !in_array($this->option, $autoDrawEditableRegionsComponentlist))
			return;
							
		$useArrayParameters = false;
		
		if(is_array($params))
		{	
			$parameters = new Registry($params);
			$useArrayParameters = true;
		}
		else
		{
			if(!empty($params)) 
				$parameters = clone $params;
			else
				$parameters = new Registry;
		}	

		
		if($parameters instanceof JObject || $parameters instanceof stdClass )
		{
			 $registry = new Registry;
             $parameters = $registry->loadObject($parameters);  
		}
        elseif( $parameters instanceof Registry)
        {
            //Do nothing
        }	
        else
        {
  			return; // invalid data type so return.
		}
		
		
		if(!empty($parameters->toArray()) && $parameters->get('inline') === false )
		{
			return;
		}
		
		$option = $this->app->input->get('arkoption');
		$extension = str_replace('com_','',$option);
		
	    $classname = 'ARKExtensions'.ucfirst($extension);

       	if(!class_exists($classname))
			return;	

		$instance = new $classname($context, $article, $parameters );

		$instance->display();

        $parameters = $instance->params;
		
		//Now lets check for exra types for this extension for inline editing if we find any lets process them as well
		if(!empty($this->_extraTypes))
		{
			foreach($this->_extraTypes as $extraType)
			{
				if(!JFolder($this->_path.'/'.$extraType))
					continue;
				
				$classname = 'ARKExtensions'.ucfirst($extension).ucfirst($extraType);
				$instance = new $classname($context, $article, $parameters );
				$instance->display();
				$parameters = $instance->params;
			}
		}	
		
		if($useArrayParameters)
			$params = $parameters->toArray();
		elseif($parameters instanceof JObject )
		{
			$props = $parameters->toArray();
			$params = new JObject($props);
		}
        elseif( $params instanceof Registry)
        {
            $params = $parameters;
        }	
		else
		{	
			$params =  $parameters->toObject();
		}	
    }
}