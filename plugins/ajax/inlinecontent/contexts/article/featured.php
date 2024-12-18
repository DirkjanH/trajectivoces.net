<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

 /**
 *Ark inline content Featured Context Class
 *
 * @package     Inlinecontent.Contexts
 * @subpackage  Inlinecontent.contexts.ContentFeatured
 */
 
 
 
class ARKContextsArticleFeatured extends ARKContextsArticleArticle
{
			
	public function triggerContentPlugins($rawText)
	{
		
		$item = new stdclass;
					
		$text = '';
		
		if (isset($rawText))
		{
			$pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
			$tagPos = preg_match($pattern, $rawText);
			
			if ($tagPos == 0)
			{
				
				$text = $rawText;
			}
			else
			{
				list ($text, $rawText) = preg_split($pattern, $rawText, 2);
			}
		}

		
		$item->text = $text;
		$item->id = $this->id;
		$item->access = $this->table->access;
		$item->introtext = $text;
		$params = JComponentHelper::getParams('com_content');
		$params->merge( new JRegistry($this->table->attribs));
		$params->set('inline',false);
		//$dispatcher	= JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$app = JFactory::getApplication();
		$app->input->set('option','com_content');
		if($params->get('show_readmore',0) && !empty($this->table->fulltext))
		{	
			if(!isset($item->readmore))
				$item->readmore = 1;
		}
		JFactory::getLanguage()->load('com_content',JPATH_SITE);	
		JFactory::getApplication()->triggerEvent('onContentPrepare', array ('com_content.featured', &$item, &$params, 0));
		$item->introtext = $item->text;
		JFactory::getApplication()->triggerEvent('onContentBeforeDisplay', array ('com_content.featured', &$item, &$params, 0));
		
		$app->input->set('option','com_ajax');
			
		return array( 'data'=>$item->introtext);
	}
	
	public function save($data,$type = 'body')
	{
		if($this->id == null)
			return array( 'title'=>'','data'=>'');	

		if($type == 'title')
		{
			$data['title'] = strip_tags($data['title']); 
			$data['title'] = html_entity_decode($data['title']);
		}
		
        if(isset($data['articletext'])){
			$data['articletext'] = base64_decode($data['articletext']);	

        }
		
        $createdBy = $this->table->created_by;

        $user = JFactory::getUser();	

        $asset = 'com_content.article.'.$this->id;

        //can user edit item if not then bail
		if (!($user->authorise('core.edit', $asset) || ($user->authorise('core.edit.own', $asset) && $user->id == $createdBy)) )
		{
			throw new Exception("Not Authorized");
		}	
        	
        //Get and set current tags data to stop them from being wiped out
		$this->table->tagsHelper = new JHelperTags;
		$this->table->tagsHelper->tags = (array) explode( ',', $this->table->tagsHelper->getTagIds($this->id, 'com_content.article') );
		
		$this->table->save($data);
		
		//We need to process data as we are sending it back to the client
		
		$params = JComponentHelper::getParams('com_content');
		$params->merge( new JRegistry($this->table->attribs));
				
		$item = new stdclass;
		$item->id = $this->id;
		$item->access = $this->table->access;
		$item->introtext = $this->table->introtext;
		$item->text = $item->introtext;
		$item->params = $params;
		if(isset($this->table->readmore))
			$item->readmore = $this->table->readmore;
		
		//let's detect if any plugin tags are being used 
		//if so let's inform the system to warn the user
		$message = $this->detectPluginTags($item->text);
		
		$plugins = JPluginHelper::importPlugin('content');
		//$dispatcher = JEventDispatcher::getInstance();
		$item->params->set('inline',false); //set this so inline plugin does not pick this up
		//set option as com_content
		$app = JFactory::getApplication();
		$app->input->set('option','com_content');
		
		if($item->params->get('show_readmore',0))
		{	
			if(!isset($item->readmore) && !empty($this->table->fulltext))
				$item->readmore = 1;
		}
				
		JFactory::getLanguage()->load('com_content',JPATH_SITE);		
		JFactory::getApplication()->triggerEvent('onContentPrepare', array ('com_content.featured', &$item, &$item->params, 0));
		$item->introtext = $item->text;
		JFactory::getApplication()->triggerEvent('onContentBeforeDisplay', array ('com_content.featured', &$item, &$item->params, 0));
		$app->input->set('option','com_ajax');
				
		return array( 'title'=>$item->title,'data'=>$item->introtext,'message'=>$message);	
	}
	
}
