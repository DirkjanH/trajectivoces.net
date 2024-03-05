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
class PlgInlineArkeditor extends JPlugin
{

	protected $app;
	protected $db;
	
	public $id = 0;
	public $context = '';
	public $type = 'body';
	public $itemtype = '';
	public $tag = 'div';
    public $style ='';
	public $class ='';
	public $firstfocus = 'false';
	public $versiontype = 'auto';
	public $autoparagraph = 'auto';
	
	
	
	public $defaults = array
	(
		'id' => 0,	
		'context' => '',
		'type' => 'body',
		'itemtype' => '',
		'tag' => 'div',
		'style' =>'',
		'class' =>'',
		'firstfocus' => 'false',
		'versiontype' => 'auto',
		'autoparagrap' => 'auto'
	);
	
	
	private function _setDefaults()
	{
		foreach($this->defaults  as $key => $value)
		{
			$this->$key = $value;
		}
	}
	
	
	public function editable(&$text, $config)
	{
		if($this->app->isClient('administrator'))
		{	
			return;
		}	
    
		$user = JFactory::getUser();	
			
        //if user is guest lets bail
		if($user->get('guest'))
		{
			return;
		}
		
			
		$cParams = JComponentHelper::getParams('com_arkeditor');
		if(empty($cParams) ||!$cParams->get('enable_inline',true))
		{
			return;
		}	
		
		if(!JPluginHelper::isEnabled('editors','arkeditor'))
            return;	
		
        if(!JPluginHelper::isEnabled('system','inlinecontent'))
            return;	

        if(!$this->app->input->get('ark_inine_enabled',true))
            return;	
		
		//reset defaults
		$this->_setDefaults();
		
		
		if($config instanceof JRegistry)
				$config = $config->toArray();
		
		if(!is_array($config))
			return;
		
		//let's get settings
		if(isset($config['id']))		
			$this->id = $config['id'];
		
		if(isset($config['context']))		
			$this->context = $config['context'];
		
		if(isset($config['type']))		
			$this->type = $config['type'];
		
		if(isset($config['itemtype']))		
			$this->itemtype = $config['itemtype'];
		
		if(isset($config['tag']))		
			$this->tag = $config['tag'];

        if(isset($config['style']))		
			$this->style = $config['style'];
		
		 if(isset($config['class']))		
			$this->class = $config['class'];
		
		if(isset($config['firstfocus']))		
			$this->firstfocus = $config['firstfocus'];
		
		if(isset($config['versiontype']))		
			$this->versiontype = $config['versiontype'];
		
		if(isset($config['autoparagraph']))		
			$this->autoparagraph = $config['autoparagraph'];
		
		$this->text = $text;
		
		$text = JLayoutHelper::render('joomla.arkeditor.editable', $this);
	}	


    public function onEditable(&$text, $config)
	{
        return $this->editable($text, $config);
	}	

}
