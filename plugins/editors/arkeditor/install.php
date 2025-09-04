<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * @package		ARK
 * @subpackage	arkeditor
 * @since		1.0.1
 */

class plgEditorsArkeditorInstallerScript
{
	/**
	 * Post-flight extension installer method.
	 *
	 * This method runs after all other installation code.
	 *
	 * @param	$type
	 * @param	$parent
	 *
	 * @return	void
	 * @since	1.0.3
	 */
	 
	function postflight($type, $parent)
	{
		// Display a move files and folders to parent.
		
			
		jimport('joomla.filesystem.folder');
			
		$srcBase = JPATH_PLUGINS.'/editors/arkeditor/layouts/joomla/'; 
		$dstBase = JPATH_SITE.'/layouts/joomla/';
		
		$folders = JFolder::folders($srcBase);
		
		$manifest = $parent->getParent()->getManifest();	
		$attributes = $manifest->attributes();	
				
		$method = ($attributes->method ? (string)$attributes->method : false); 
		
	
		foreach($folders as $folder)
		{
		
			if($method !='upgrade')
			{
				if(JFolder::exists($dstBase.$folder))
					JFolder::delete($dstBase.$folder);
			}
			
			JFolder::copy($srcBase.$folder,$dstBase.$folder,null, true);
		}
		
		if($type == 'install')
		{
			//update $db
			$db = Jfactory::getDBO();
			
			$toolbars = base64_encode('{"full":[["Maximize","Scayt","SpellChecker","Print"],["Cut","Copy","SelectAll","Paste","PasteText","PasteFromWord"],["Bold","Italic","Underline","Strike","Superscript","Subscript","SpecialChar","Blockquote"],["NumberedList","BulletedList","Outdent","Indent"],["BidiRtl","BidiLtr"],["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],["Unlink","MenuLinks","Email","Anchor"],["Document","Image","Arkmediabutton"],["Styles","Format","Font"],["FontSize","RemoveFormat"],["TextColor","BGColor"],["Table","HorizontalRule","CreateDiv","Iframe","Flash","Smiley","About"],["Fontawesome"]],"back":[["Maximize","Scayt"],["Cut","Copy","SelectAll","Paste","PasteFromWord"],["Bold","Italic","Underline","Strike","Superscript","SpecialChar","Blockquote"],["NumberedList","BulletedList","Outdent","Indent"],["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],["Unlink","MenuLinks","Email","Anchor"],["Document","Image","Arkmediabutton"],["Format","Styles","RemoveFormat"],["Table","HorizontalRule","CreateDiv","Iframe"],["Fontawesome"]],"front":[["Maximize","Source","ShowBlocks","Scayt"],["Undo","Redo","Versions"],["Cut","PasteFromWord"],["Bold","Italic","Underline","Blockquote"],["NumberedList","BulletedList"],["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],["Unlink","MenuLinks","Email","Anchor"],["Document","Image","Arkmediabutton"],["Format","Styles","RemoveFormat"],["Table","CreateDiv","Smiley"],["Fontawesome"]],"inline":[["Sourcedialog","PasteText"],["Bold","NumberedList","BulletedList"],["Format"],["CreateDiv"],["MenuLinks","Readmore"],["Undo","Redo","Versions"],["Image","Arkmediabutton"],["Arkwidgetbutton"],["Close"]],"title":[["Save"],["Cut","Copy","PasteText"],["Undo","Redo"],["Close"]],"image":[["Save"],["Image"],["MenuLinks","Document"],["Versions"],["Close"]],"mobile":[["Bold"],["MenuLinks"],["Image"],["Save"],["Versions"],["Close"]]}');

			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__extensions')
			->where('folder = '.$db->quote('editors'))
			->where('element = '.$db->quote('arkeditor'));
				
			$db->setQuery($query);
			$params = $db->loadResult();	
			
			if($params === false)
				throw new Exception('Failed to retrieve parameters from Editor');

			if(!$params)
				$params = '{}';
			
			$params = new JRegistry($params);
			
			$params->set('toolbars',$toolbars);
				
			$query->clear()
			->update('#__extensions')
			->set('params= '.$db->quote($params->toString()))
			->where('folder = '.$db->quote('editors'))
			->where('element = '.$db->quote('arkeditor'));
				
			$db->setQuery($query);
			if(!$db->execute())
				throw new Exception('Failed to update parameters for Editor');
			
		}
		
		if($type == 'update')
		{
			$db = JFactory::getDBO();
			
			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__extensions')
			->where('folder = '.$db->quote('editors'))
			->where('element = '.$db->quote('arkeditor'));
				
			$db->setQuery($query);
			$params = $db->loadResult();	
			
			if($params === false)
				throw new Exception('Failed to retrieve parameters from Editor');

			$params = new JRegistry($params);
			
			$config = base64_decode($params->get('toolbars'));
			
			$toolbars = json_decode($config,true);
			
						
			if(!isset($toolbars['full']))
			{
				$toolbars['full'] = json_decode('[["Maximize","Scayt","SpellChecker","Print"],["Cut","Copy","SelectAll","Paste","PasteText","PasteFromWord"],["Bold","Italic","Underline","Strike","Superscript","Subscript","SpecialChar","Blockquote"],["NumberedList","BulletedList","Outdent","Indent"],["BidiRtl","BidiLtr"],["JustifyLeft","JustifyCenter","JustifyRight","JustifyBlock"],["Unlink","Link","Email","Anchor"],["Document","Image","Arkmediabutton"],["Styles","Format","Font"],["FontSize","RemoveFormat"],["TextColor","BGColor"],["Table","HorizontalRule","CreateDiv","Iframe","Flash","Smiley","About"],["Fontawesome"]]',true);
				
				$config = json_encode($toolbars);
							
				$params->set('toolbars',base64_encode($config));
				
							
				$query->clear()
				->update('#__extensions')
				->set('params= '.$db->quote($params->toString()))
				->where('folder = '.$db->quote('editors'))
				->where('element = '.$db->quote('arkeditor'));
					
				$db->setQuery($query);
				if(!$db->execute())
					throw new Exception('Failed to update parameters for Editor');
			
			}
		
		}	
	}
	
	function uninstall($parent) 
	{
		jimport('joomla.filesystem.folder');
		
		$app = JFactory::getApplication();
		
		$db = JFactory::getDBO();

		$folder =  JPATH_SITE.'/layouts/joomla/arkeditor';
		
		if(JFolder::exists($folder) && !JFolder::delete($folder)) {
			$app->enqueueMessage( JText::_('Unable to delete Arkeditor Layouts') );
		}
		
	}
		
}