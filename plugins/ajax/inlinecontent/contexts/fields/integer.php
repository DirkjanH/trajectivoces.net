<?php
/*------------------------------------------------------------------------
# Copyright (C) 2017-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

class ARKContextsFieldsInteger extends ARKContextsFieldsText
{
	
	protected $field_id;
	
	protected $fieldsModel;
	

	public function get()
	{
		
		if( $this->id == null)
			return array( 'title'=>'','data'=>'');	
		
		$text = $this->fieldModel->getFieldValue($this->field_id, $this->id);
	
		$title = (int) reset($text);
	
		return array( 'title'=>$title,'data'=>'');	
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
		

		$text = $data['title']; 
        $text =  $this->_filterText($text); 
		
		$intValue  = (int) $text;

		$first = $this->table->fieldparams->get('first');
		$last = $this->table->fieldparams->get('last');
		
		if($first && $intValue  < $first)
			$intValue = $first;
		elseif($last && $intValue  < $last)
			$intValue = $last;
				
		$message = 'Could not save custom field!';
		
		if(!$this->fieldModel->setFieldValue($this->field_id, $this->id, $intValue))
			return array( 'title'=>'','data'=>$text,'message'=>$message);
        
        $message = '';
	    	
	
		//Save version
	
		$typeAlias = 'com_fields.fields';
		$this->table->default_value = $intValue;
		$contenthistoryHelper = new JHelperContenthistory($typeAlias);			
		$contenthistoryHelper->store($this->table);
		
		return array( 'title'=>$intValue,'data'=>'','message'=>$message);	
	}
	
	public function version($versionId,$type)
	{
		
		$historyTable = JTable::getInstance('Contenthistory');
		$historyTable->load($versionId);
		$rowArray = JArrayHelper::fromObject(json_decode($historyTable->version_data));
			
		$item = $this->table;
		$item->bind($rowArray);	
		$text = $item->default_value;
	
		return array( 'data'=>$text);
		
	}
	
}		