<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
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
 * @subpackage  ArkEvents.Magicline
 */

class PlgArKEventsElement extends JPlugin
{
	public function onInstanceCreated(&$params)
	{
		//Do Nothing
		return 	"
		
		let proto = CKEDITOR.dom.node.prototype;
		
		CKEDITOR.dom.node = function( domNode ) {
			if ( domNode ) {
				
				let domType = null;
				
				try
				{
					domType = domNode.nodeType;
				}	
				catch(e)
				{
					return new CKEDITOR.dom[ 'domObject' ]( domNode );
				}
				let type =
					domType == CKEDITOR.NODE_DOCUMENT ? 'document' :
					domType == CKEDITOR.NODE_ELEMENT ? 'element' :
					domType == CKEDITOR.NODE_TEXT ? 'text' :
					domType == CKEDITOR.NODE_COMMENT ? 'comment' :
					domType == CKEDITOR.NODE_DOCUMENT_FRAGMENT ? 'documentFragment' :
					'domObject'; // Call the base constructor otherwise.

				return new CKEDITOR.dom[ type ]( domNode );
			}

			return this;
		};

		CKEDITOR.dom.node.prototype = proto;	
		";
	}
}