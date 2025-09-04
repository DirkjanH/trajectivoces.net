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

class ARKContextsFieldsMedia extends ARKContextsFieldsEditor
{
	
	public function get()
	{
		if( $this->id == null)
			return array( 'title'=>'','data'=>'');	
		
		$value = $this->fieldModel->getFieldValue($this->field_id, $this->id);
		$this->table->value = $value;
        JFactory::getApplication()->input->set('ark_inine_enabled',false);
		$render = FieldsHelper::render($this->table->context,'field.render',array('field' => $this->table));
	
		return array( 'title'=>'','data'=>$render);	
	}

    public function triggerContentPlugins($rawText)
	{	
        $dom = new DOMDocument();
		$dom->strictErrorChecking = false;
		$dom->recover = true;
		$dom->loadHTML('<div>'.$rawText.'</div>');

        $images = $dom->getElementsByTagName('img');
		$image = $images->item(0);
		$src = $image->getAttribute('src'); 
		$text = str_replace(JURI::base(),'',$src);	
        
        
        $text = $this->_prepareCustomField($text);

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
            $this->table->default_value = $text;	
		}

	    $message = 'Could not save custom field!';
		
		
        $dom = new DOMDocument();
		$dom->strictErrorChecking = false;
		$dom->recover = true;
		$dom->loadHTML('<div>'.$text.'</div>');

        $images = $dom->getElementsByTagName('img');
		$image = $images->item(0);
		$src = $image->getAttribute('src');
		$text = str_replace(JURI::base(),'',$src);	
        
        if(!$this->fieldModel->setFieldValue($this->field_id, $this->field_id, $text))
			return array( 'title'=>'','data'=>$text,'message'=>$message);
        
        $message = '';

        
        //We need to process data as we are sending it back to the client
        $render = $this->_prepareCustomField($text);

		//Save version
	
        $typeAlias = 'com_fields.fields';
		$this->table->default_value = $render;
		$contenthistoryHelper = new JHelperContenthistory($typeAlias);			
		$contenthistoryHelper->store($this->table);

		$message = $this->detectPluginTags($render);

		return array( 'title'=>'','data'=>$render,'message'=>$message);	
	}
	
	public function version($versionId, $type)
	{
		
		$historyTable = JTable::getInstance('Contenthistory');
		$historyTable->load($versionId);
		$rowArray = JArrayHelper::fromObject(json_decode($historyTable->version_data));
			
		$item = $this->table;
		$item->bind($rowArray);	
		$render = $item->default_value;
	
		return array( 'data'=>$render);
		
	}

}		