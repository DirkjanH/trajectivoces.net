<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2016 WebxSolution Ltd. All Rights Reserved.
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
 * @subpackage  System.arkeditoruser
 */
class PlgSystemArKModal extends JPlugin
{
	public function onBeforeRender()
	{
		if( JFactory::getApplication()->isClient('Site') || version_compare(JVERSION, '4.0', 'ge' ) )
		{
			Joomla\CMS\HTML\HTMLHelper::_('jquery.framework');

            $base = str_replace('/administrator','', JURI::base());
            JHtml::stylesheet($base . 'media/editors/arkeditor/css/squeezebox.css');
			JHtml::script($base . 'media/editors/arkeditor/js/jquery.easing.min.js');
			JHtml::script($base . 'media/editors/arkeditor/js/squeezebox.js');

			// Support Image Modals
			JFactory::getDocument()->addScriptDeclaration(
			
				"(function()
				{
					if(typeof jQuery == 'undefined')
						return;
					
					jQuery(function($)
					{
						if($.fn.squeezeBox)
						{
							$( 'a.modal' ).squeezeBox({ parse: 'rel' });
				
							$( 'img.modal' ).each( function( i, el )
							{
								$(el).squeezeBox({
									handler: 'image',
									url: $( el ).attr( 'src' )
								});
							})
						}
						else if(typeof(SqueezeBox) !== 'undefined')
						{
							$( 'img.modal' ).each( function( i, el )
							{
								SqueezeBox.assign( el, 
								{
									handler: 'image',
									url: $( el ).attr( 'src' )
								});
							});
						}
						
						function jModalClose() 
						{
							if(typeof(SqueezeBox) == 'object')
								SqueezeBox.close();
							else
								ARK.squeezeBox.close();
						}
					
					});
				})();"
			);

             if(version_compare(JVERSION, '4.0', 'ge' ))
             {
                 

				 
				 JFactory::getDocument()->addScriptDeclaration("

                 (() =>
				 {
					if(typeof jQuery == 'undefined')
						return;
                     jQuery(() =>
				     {
                          
						function resizeModel(event)
						{
							if(this.options.handler == 'iframe')
							{
								this.options.size = {x: Math.floor(document.documentElement.clientWidth*0.8), y: Math.floor(document.documentElement.clientHeight*0.7)};
								this.win.css({height: this.options.size.y,width: this.options.size.x});
								this.asset[0].height =  this.options.size.y;
								this.asset[0].width =  this.options.size.x;
								
							}
						};

						const resizeModalFn = resizeModel.bind(ARK.squeezeBox);

						ARK.squeezeBox.presets.onOpen = function()
						{
							if(Joomla.Modal)
								Joomla.Modal.setCurrent(this);
							
							this.win.addClass('shadow');
							
							if(this.options.handler == 'iframe')
							{		
								this.options.size = {x: Math.floor(document.documentElement.clientWidth*0.8), y: Math.floor(document.documentElement.clientHeight*0.7)};
								this.asset[0].height =  this.options.size.y;
								this.asset[0].width =  this.options.size.x;
								window.addEventListener('resize', resizeModalFn);
							} 
						};

						ARK.squeezeBox.presets.onClose = function()
						{
							if(Joomla.Modal)
								Joomla.Modal.setCurrent('');
							if(this.options.handler == 'iframe')
								window.removeEventListener('resize', resizeModalFn);
						};
                     }); 
                    })();"
                 );
             }
		}
	}
}