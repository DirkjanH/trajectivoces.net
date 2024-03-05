<?php
/*------------------------------------------------------------------------
# Copyright (C) 2021 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Ark Inline content editing Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.ArkContent
 */
class PlgContentArkFormFieldsJ4Compat extends CMSPlugin
{
	protected $app;
	
	protected $db;
	
	private const fieldTypes = ['radio','list','listmultiple','GroupsList','componentlist','adercomponentList'];
	
	public function onContentPrepareForm(Form $form, $data)
	{
		
		if(version_compare(JVERSION, '4.0', 'lt' ) )
        {
			return;
		}
		
		
		
		if ($this->app->isClient('site'))
		{
			return;
		}
		
		$context = $form->getName();
		
		// Check we are manipulating a valid form.
		if (!in_array($context, ['com_config.component', 'com_plugins.plugin', 'com_arkeditor.plugin']))
		{
			return true;
		}
		
		$isTruefunc =  function($val) {
			$boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
			return ( $boolval===null ? false : $boolval );
		};
   
		
		
		if($context == 'com_config.component')
		{
            
			if(!($component = $this->app->input->get('component','')))
				return;
			
			if($component != 'com_arkeditor')
			   return;
		}
		
		

		if($context == 'com_plugins.plugin')
		{
            
			$extension_id = $this->app->input->getInt('extension_id',0);
			//Get element
			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('element'))
				->from($this->db->quoteName('#__extensions'))
				->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
				->where($this->db->quoteName('folder') . ' = '. $this->db->quote('editors'))
				->where($this->db->quoteName('extension_id') . ' = :id')
				->bind(':id', $extensionid);
			$this->db->setQuery($query);
			$element = $this->db->loadResult();
			
			if(!$element)
				return;
		}
         
        //Update XML form fields for Joomla 4
		
		$xml = $form->getXml();
	
		$names = $xml->xpath('descendant-or-self::field[@name]/@name');
		$fieldnames = array_unique(array_map('strval', $names ? $names : array()));   //clean and convert to string array
		   
		foreach($fieldnames as $fieldname)
		{
			$type = $form->getFieldAttribute($fieldname,'type');
			
			if(!in_array($type,static::fieldTypes)) //Is the field type one we are looking for?
				continue;
			
			$multiple = $form->getFieldAttribute($fieldname, 'multiple', false);
			
			if($type == 'radio')
			{
				if(!($class = $form->getFieldAttribute($fieldname, 'class', false)))
					continue; //Nothing to do
				
				    //Reset classnames
					$class = str_replace(['btn-group btn-group-yesno','btn-group'],'',$class);
					$form->setFieldAttribute($fieldname, 'class', $class);
					
					//set form field layout
					$layout = 'joomla.form.field.radio.switcher';
					$form->setFieldAttribute($fieldname, 'layout', $layout);
			}
			else
			{
			  
			  if(!$isTruefunc($multiple))
				  continue;
			  //set form field layout
			  $layout = 'joomla.form.field.list-fancy-select';
			  $form->setFieldAttribute($fieldname, 'layout', $layout);
			}
		
		}	

		return true;
    }
}