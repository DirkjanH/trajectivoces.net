<?php
/*------------------------------------------------------------------------
# Copyright (C) 2016-2017 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark  Editor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEditor.Image2
 */
class PlgArkEditorImage2 extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) {
	    
      return "editor.config.image2_alignClasses = ['pull-left','pull-center', 'pull-right'];";
	}
		
    public function onInstanceLoaded(&$params) {
	    
      return 
      "    
      editor.getSelectedWidget = function()
      {
         var  widget = null;
         widget = editor.widgets.focused
         return widget;      
      }  

      editor.widgets.on('instanceCreated', function (evt) {
	      // Event `action` occurs on `image` widget.
          var widget = evt.data;
          if(widget.name == 'image')
          {
            if(widget.element.hasClass('pull-center'))
            { 
                 widget.once('ready', function(evt)
                 {   
                     this.element.removeClass('pull-center');
                     this.setData('align','center');
                     if(this.element.is('p'))
                        this.element.addClass('pull-center');
                });
            }
          }
     });";
	}
}