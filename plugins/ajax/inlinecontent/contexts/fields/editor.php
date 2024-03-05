<?php
/*------------------------------------------------------------------------
# Copyright (C) 2017-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');

class ARKContextsFieldsEditor extends ARKContextsFieldsText
{
	
    public function get()
	{
		if( $this->id == null)
			return array( 'title'=>'','data'=>'');	
		
		$data = $this->fieldModel->getFieldValue($this->field_id, $this->id);
	
		return array( 'title'=>'','data'=>$data);	
	}
	

    public function triggerContentPlugins($rawText)
	{	
        $text = $this->_prepareCustomField($rawText);

		return array( 'data'=>$text);
	}
	
	public function save($data,$type = 'body')
	{
		
		if($this->id == null)
			return array( 'title'=>'','data'=>'');	

        $user = JFactory::getUser();
       
        $parts = explode('.', $this->table->context, 2);

        $asset = $parts[0] . '.field.' . (int) $this->field_id;

        if (!($user->authorise('core.edit.value', $asset))) 
		{
			throw new Exception("Not Authorized");
	    }	
	
		$text = '';
		

		if(isset($data['articletext']))
		{	
			$text = base64_decode($data['articletext']);
            $text =  $this->_filterText($text);	
		}
         
		$message = 'Could not save custom field!';

        		
		if(!$this->fieldModel->setFieldValue($this->field_id, $this->id, $text))
			return array( 'title'=>'','data'=>$text,'message'=>$message);	
		
        $message = '';
	
	
		//Save version
	
		$typeAlias = 'com_fields.fields';
		$this->table->default_value = $text;
		$contenthistoryHelper = new JHelperContenthistory($typeAlias);			
		$contenthistoryHelper->store($this->table);
		
		//We need to process data as we are sending it back to the client
		$text = $this->_prepareCustomField($text);

		$message = $this->detectPluginTags($text);

		return array( 'title'=>'','data'=>$text,'message'=>$message);	
	}
	
	public function version($versionId, $type)
	{
		
		$historyTable = JTable::getInstance('Contenthistory');
		$historyTable->load($versionId);
		$rowArray = JArrayHelper::fromObject(json_decode($historyTable->version_data));
			
		$item = $this->table;
		$item->bind($rowArray);	
		$text = $item->default_value;
	
		return array( 'data'=>$text);
		
	}


    protected function _prepareCustomField($value) 
    {
        
        $item = new stdclass;
        $item->id = $this->id;
        $item->name = '';
        $item->title = '';
        $item->fulltext = '';
        $item->introtext = '';
        $item->description = '';
        $item->params = new JObject;

        $this->table->value = $value;

        $field =  $this->table;

       
        //disable inline editing
        JFactory::getApplication()->input->set('ark_inine_enabled',false);
       
        JPluginHelper::importPlugin('fields');

		//$dispatcher = JEventDispatcher::getInstance();
        
        JFactory::getApplication()->triggerEvent('onCustomFieldsBeforePrepareField', array($context, $item, &$field));

		$value = JFactory::getApplication()->triggerEvent('onCustomFieldsPrepareField', array($context, $item, &$field));

        // Event allow plugins to modfify the output of the prepared field
		JFactory::getApplication()->triggerEvent('onCustomFieldsAfterPrepareField', array($context, $item, $field, &$value));

        if(is_array($value))
        {
            $value = implode($value, ' ');
        }

        return $value;
    }

}		