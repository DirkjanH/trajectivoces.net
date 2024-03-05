<?php
/*------------------------------------------------------------------------
# Copyright (C) 2012-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.event.plugin');
jimport('joomla.filesystem.file');

/**
 * ckeditor Lite for Joomla! WYSIWYG Editor Plugin
 *
 * @author WebxSolution Ltd <andrew@webxsolution.com>
 * @package Editors
 * @since 1.5
 */
require_once(JPATH_PLUGINS.'/system/inlinecontent/inlinemode.php');
 
class plgEditorArkEditor extends JPlugin {

	/**
	 * Method to handle the onInitEditor event.
	 *  - Initializes the arkeditor WYSIWYG Editor
	 *
	 * @access public
	 * @return string JavaScript Initialization string
	 * @since 1.5
	 */
	
    protected static $plugins = null;
	
    protected static $loaded = false;
	
	protected static $loadFunc = false;

	public $app;
	
	public $inlineMode = ArkInlineMode::NOTSET;  
	
	
		 
	
    function __construct(& $subject, $config) 
	{
		if(isset($config['inlineMode']))
            $this->inlineMode = $config['inlineMode'];
        parent::__construct($subject, $config);
	}
    
    
    public function onInit()
	{
		
        $document = JFactory::getDocument();
        $document->addCustomTag('<script src="'. JURI::root().'plugins/editors/arkeditor/ckeditor/ckeditor.js"></script>');
		
		$document->addCustomTag('<script src="'. JURI::root().'media/editors/arkeditor/js/arkeditor.min.js"></script>');
	
                                
		//$document->addScript(JURI::root().'plugins/editors/arkeditor/ckeditor/ckeditor.js');
		
		$document->addCustomTag("<script>
        (function()
        {
		    var jfunctions = {};
		    CKEDITOR.tools.extend(CKEDITOR.tools,
		    {
			    getData : function(IdOrName)
			    {
				     return CKEDITOR.instances[IdOrName] && CKEDITOR.instances[IdOrName].getData() || CKEDITOR.oEditor && CKEDITOR.oEditor.getData();	
			    },
			    setData : function(IdOrName,ohtml)
			    {
				     CKEDITOR.instances[IdOrName] && CKEDITOR.instances[IdOrName].setData(ohtml) || CKEDITOR.oEditor && CKEDITOR.oEditor.setData(ohtml);
			    },
			    addHashFunction : function( fn, ref)
			    {
				    jfunctions[ref] =  function()
				    {
					    fn.apply( window, arguments );
				    };
			    },
			    callHashFunction : function( ref )
			    {
				    var fn = jfunctions[ ref ];
				    return fn && fn.apply( window, Array.prototype.slice.call( arguments, 1 ) );
			    }
		    })
        })();</script>");
				
		
				
		$temp = JComponentHelper::getParams('com_arkeditor');
		
		$clone = clone $temp;//Clone this as merge function wipes out some values
				  
		$temp->merge($this->params,true); //wipes out 
		$params = $temp;
		
		//Restore wiped out values
		$params->set('exclude_stylesheets',$clone->get('exclude_stylesheets'));
		$params->set('exclude_selectors',$clone->get('exclude_selectors'));

       
		if($this->inlineMode == ArkInlineMode::INLINE)
        {
			$params->set('toolbar','inline');
			$params->set('toolbar_ft','inline');
		}
		else
		{
			if($params->get('enable_preloader',true) && $this->inlineMode != ArkInlineMode::INLINE) //Do not load when inline editing
			{
				$document->addStyleSheet( JURI::root(). 'layouts/joomla/arkeditor/css/preloader.css');
			}	
		}	

		//Fire ARK Events		
		JPluginHelper::importPlugin( 'arkevents' );	
		//$dispatcher = JEventDispatcher::getInstance();
		
		$result = $this->app->triggerEvent('onBeforeLoadToolbar',array( &$params));
				
		$instanceCreatedResult = $this->app->triggerEvent('onInstanceCreated',array( &$params));
		$instanceReadyResult = $this->app->triggerEvent('onInstanceReady', array( &$params));
		
		        		
		static::importPlugin( 'arkeditor' );	
		static::importPlugin( 'arkwidget-editor' );	
		
		$instanceBeforeCreatedResult = $this->app->triggerEvent('onInstanceBeforeCreated',array( &$params));
        $instanceBeforeLoadedResult = $this->app->triggerEvent('onBeforeInstanceLoaded',array( &$params));
		$instanceLoadedResult = $this->app->triggerEvent('onInstanceLoaded',array( &$params));
				
		
		//backward compatibility with JCK
		$document->addCustomTag(
		"<script>if( !window.addDomReadyEvent)
				window.addDomReadyEvent = {};
				var editor_implementOnInstanceReady = editor_onDoubleClick = function(){}
				window.addDomReadyEvent.add = CKEDITOR.domReady;
		</script>");

		
        //Fire General Instance Created Events				
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceCreated',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceBeforeCreatedResult) ? implode(chr(13), $instanceBeforeCreatedResult) : '') ."	
		});
        </script>");
        
        
        //Fire General Instance Created Events				
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceCreated',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceCreatedResult) ? implode(chr(13), $instanceCreatedResult) : '') ."	
		});
        </script>");
				
		//Fire plugin specific Instance Created Event
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceCreated',function(evt)
		{
			var editor = evt.editor;
			 " .  (!empty($instanceBeforeLoadedResult) ? implode(chr(13), $instanceBeforeLoadedResult) : '') ."	
		});</script>");
		
		//Fire plugin specific Instance Loaded Event
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceLoaded',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceLoadedResult) ? implode(chr(13), $instanceLoadedResult) : '') ."	
		});</script>");
		
		//Fire General Instance ready Events
		$document->addCustomTag(
		"<script>CKEDITOR.on('instanceReady',function(evt)
		{
			 var editor = evt.editor;
			 " .  (!empty($instanceReadyResult) ? implode(chr(13), $instanceReadyResult) : '') ."	
		});</script>");


        $this->params = $params;
		
	}


	public function onGetContent( $editor ) {
		return " Joomla.editors.instances['$editor'].getValue(); ";
	}
	
	public function onSetContent($editor, $html) {
		return " Joomla.editors.instances['$editor'].setValue('$html'); ";
	}

	/**
	 * ckeditor Lite WYSIWYG Editor - copy editor content to form field
	 *
	 * @param string 	The name of the editor
	 */
	
	function onSave( $editor ) { /* We do not need to test for anything */	}

	
	public function onGetInsertMethod($name)
	{
		$document = JFactory::getDocument();
		$document->addCustomTag(
		"<script>	
		function ARKEditorUpdateSelectedImageOrLink(instanceName,text)
		{
            
            
            var editor = CKEDITOR.instances[instanceName];
			
			if(!editor.hasBookMarks)
			{	
				editor.hasBookMarks = function() { return this._bookmarks};
			}
			
			if(!editor.resetBookMarks)
			{	
				editor.resetBookMarks = function() { this._bookmarks = null;};
			}
						
			if(CKEDITOR.env.ie)
			{
				var bookmarks = null;
				
				if( (bookmarks = editor.hasBookMarks()))
				{
					var sel = editor.getSelection();
					sel && sel.selectBookmarks( bookmarks );
					editor.resetBookMarks();
				}
			
			}
			
			if(text.match(/^<a[^>]+?href/i))
			{

                if((widget = editor.getSelectedWidget()))
		        {
			        if(widget.name == 'image')
                    {
                        var newElement =  CKEDITOR.dom.element.createFromHtml(text),
				            element = widget.parts.image;
                  				
			            var href = newElement.getAttribute('href');
				        newElement.setAttribute('href',href); 

			            var attr = element.getAttributes();
			            delete attr.src;
			            element.removeAttributes(attr);
			            var cleanHtml = element.getOuterHtml();

			            newElement.setHtml(cleanHtml);
			            editor.widgets.del(widget);
			            editor.insertHtml(newElement.getOuterHtml())
			            return true;
                    }
		        }
                else if ( ( element = CKEDITOR.plugins.link.getSelectedLink2( editor ) ) && element.hasAttribute( 'href' ) )
				{
						var newElement =  CKEDITOR.dom.element.createFromHtml(text);
						newElement.copyAttributes(element);
						element.data('cke-saved-href',element.getAttribute('href'));
                        //element.setHtml(newElement.getHtml());   
						editor.getSelection().selectElement(element); //content changes so reselect element
  					    return true;
				}
                else
                {


				   CKEDITOR.dom.selection.prototype.getSelectedHtml = function() 
					{
						let cache = this._.cache,
							html = '';
						if ( cache.selectedHtml !== undefined )
							return cache.selectedHtml;

						let nativeSel = this.document.getWindow().$.getSelection();
						
						if(nativeSel && nativeSel.rangeCount)
						{
							if (nativeSel.rangeCount) 
							{
								let container = document.createElement('div');
								for (let i = 0, len = nativeSel.rangeCount; i < len; ++i) {
									container.appendChild(nativeSel.getRangeAt(i).cloneContents());
								}
								html = container.innerHTML;
								container = null;
							}
							return ( cache.selectedHtml = html );
						}
						
						return html;
					}
					
					
					var selection = editor.getSelection(),
						selectedHtml = '';
					
					if(selectedHtml = editor.getSelection().getSelectedHtml())
					{	
                        var newElement =  CKEDITOR.dom.element.createFromHtml(text);
						newElement.setHtml(selectedHtml);
						editor.insertElement(newElement);	
						editor.getSelection().selectElement(newElement);
						return true;
					}
                }
		
			}
			else if (text.match(/^<img/i))
			{
				var widget = editor.getSelectedWidget();
                if(widget && widget.name == 'image')
				{
		               
                    if(widget.data.hasCaption)
                    {
						widget.setData('hasCaption',false);
                        widget = editor.widgets.focused;
                        widget.element.data('caption',false);
                    }
					
					var element = widget.element,
						newElement =  CKEDITOR.dom.element.createFromHtml(text);

					if(element.is('a'))
					{	
						newElement.copyAttributes(widget.parts.image);
					}			
					else
						newElement.copyAttributes(element,{class:1});

                    //set classes
                     var classes  = widget.getClasses()
                     for( var className in classes)
                         widget.removeClass(className);

                    var newClasses = newElement.getAttribute('class');
                    newClasses = newClasses && newClasses.split(' ') || [];

                    //remove any align classes

                   
                    for ( var i = 0; i < newClasses.length; i++)
                        widget.addClass(newClasses[i]);
                       
                    //update wrapper with alignment class

                     var align = 'none';
         
                    if( newElement.hasClass('pull-left'))
                          align = 'left';
                    
                    if( newElement.hasClass('pull-center'))
                    {
                          align = 'center';
                          widget.parts.image.removeClass('pull-center');
                    }

                    if( newElement.hasClass('pull-right'))
                         align = 'right';
                    
                    widget.setData('align',align);

                    if( align == 'center')
                    {
                      if(widget.element.is('p'))
                        widget.element.addClass('pull-center'); 
                    } 
                    
                    widget.setData('src', newElement.getAttribute('src'));

					if(CKEDITOR.plugins.image && CKEDITOR.plugins.image.resize)
					{
					    var image = widget.parts.image;
						var src = widget.data.src.replace(/\?i=[0-9]+?$/i, '');
						widget.setData('src',src);
						CKEDITOR.plugins.image.resize(widget.parts.image,editor);
					}
				    return true;
				}
			}
			else if(text.match(/^<figure/i))
			{
				var widget = editor.getSelectedWidget(); 
                if(widget && widget.name == 'image')
				{
					
					if(!widget.data.hasCaption)
                    {
						widget.setData('hasCaption',true);
                        widget = editor.widgets.focused;
	                }

    
					var figElement = widget.element,
						newFigElement =  CKEDITOR.dom.element.createFromHtml(text);
                    
					
					var image = figElement.findOne('img'),
						caption = figElement.findOne('figcaption'),
						newImage = newFigElement.findOne('img'),
						newCaption = newFigElement.findOne('figcaption');
						
					if(!newImage || !newCaption)
						return false;
					
                    if(caption)
                    {
						caption.setHtml(newCaption.getHtml());
                        if((capClass = newCaption.getAttribute('class')))
                            caption.addClass(capClass);
                    }

                    
				    newFigElement.copyAttributes(figElement, {class:1});	

                    //set classes
                     var classes  = widget.getClasses()
                     for( var className in classes)
                         widget.removeClass(className);

                    var newClasses = newFigElement.getAttribute('class');
                    newClasses = newClasses && newClasses.split(' ') || [];
                    
                    for ( var i = 0; i < newClasses.length; i++)
                        widget.addClass(newClasses[i]);
                       
                    //update wrapper with alignment class

                     var align = 'none';
         
                    if( newFigElement.hasClass('pull-left'))
                          align = 'left';
                    
                    if( newFigElement.hasClass('pull-center'))
                    {
                          align = 'center';
                          //remove center class from figure element
                          figElement.removeClass('pull-center');
                    }
                    if( newFigElement.hasClass('pull-right'))
                         align = 'right';
                    
                    widget.setData('align',align);

                    newImage.copyAttributes(image);
					widget.setData('src',newImage.getAttribute('src'));
					if(CKEDITOR.plugins.image && CKEDITOR.plugins.image.resize)
					{
						var src = widget.data.src.replace(/\?i=[0-9]+?$/i, '');
						widget.setData('src',src);
						CKEDITOR.plugins.image.resize(image,editor);
					}
  				}
                else
                {
                    var newFigElement =  CKEDITOR.dom.element.createFromHtml(text);
                    newFigElement.addClass(editor.config.image2_captionedClass);
                    var html = newFigElement.getOuterHtml()
                    editor.insertHtml(html);
                }
                return true;
			}
			
			return false;
		}

		function IeCursorFix() {} //Do Nothing
        </script>");
		
		return true;
	 
	}
	
	 /**
	 * Display the editor area.
	 *
	 * @param   string   $name     The name of the editor area.
	 * @param   string   $content  The content of the field.
	 * @param   string   $width    The width of the editor area.
	 * @param   string   $height   The height of the editor area.
	 * @param   int      $col      The number of columns for the editor area.
	 * @param   int      $row      The number of rows for the editor area.
	 * @param   boolean  $buttons  True and the editor buttons will be displayed.
	 * @param   string   $id       An optional ID for the textarea. If not supplied the name is used.
	 * @param   string   $asset    The object asset
	 * @param   object   $author   The author.
	 *
	 * @return  string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null)
	{

		
		
		if (empty($id))
		{
			$id = $name;
		}
		
		$id = $this->_cleanString($id);
		
		$params = $this->params;	
		
		if($this->inlineMode == ArkInlineMode::NONE)
			return;
		
		if($this->inlineMode == ArkInlineMode::INLINE)
        {
          
			$config                     			=   new stdclass;
            $toolbars								=	$this->_decode($params->get('toolbars'));	
					
			$config->toolbar						= 	($params->get('toolbar','') == 'mobile' ?  $toolbars['mobile'] :  $toolbars['inline']);
			$config->toolbar_inline 				= 	$toolbars['inline'];
			$config->toolbar_mobile					= 	$toolbars['mobile'];	
			$config->toolbar_image 					= 	$toolbars['image'];		
			$config->toolbar_title					= 	$toolbars['title'];	
			$config->toolbarName 					= 	'inline';
			
		    $config->skin							= 	$params->def( 'skin', 'officemetro' );
			$lang_mode								= 	$params->def( 'lang_mode', 1 );
			$config->language						= 	$params->def( 'lang_code', 'en' );
			$config->entermode 						= 	$params->def( 'entermode', 1 );
			$config->shiftentermode 				= 	$params->def( 'shiftentermode', 2 );
			$config->imagepath						=   $params->def( 'imagePath','images');
			$config->bgcolor						= 	$params->def( 'bgcolor','#ffffff');
    		$config->ftcolor						= 	$params->def( 'ftcolor','');
            $config->ftfamily						= 	$params->def( 'ftfamily','');
            $config->ftsize			   				= 	$params->def( 'ftsize','');
			$config->textalign						= 	$params->def( 'textalign','');
			$config->entities						= 	$params->def( 'entities',0);
  			$formatsource							= 	$params->def( 'formatsource',1);
			$config->baseHref 						=   JURI::root();
			$config->base                           =   JURI::base();
			$config->dialog_backgroundCoverColor	=   $params->def( 'dialog_backgroundCoverColor','black'); 
			$config->dialog_backgroundCoverOpacity	=   $params->def( 'dialog_backgroundCoverOpacity','0.5'); 
			$config->autoDisableInline				=   (int) $params->def( 'auto_disable_inline',1);
			$config->enableUserWarnings				=	(int) $params->def( 'enable_user_warnings',1);
			
			
			JFactory::getDocument()->addScriptOptions('plg_editor_arkeditor', ["inline"=>true], false);
			

			//lets get language direction
			$language	= JFactory::getLanguage();
	
			if ($language->isRTL()) {
				$config->direction = 'rtl';
			} else {
				 $config->direction = 'ltr';
			}
			
			$config->defaultLanguage = "en"; 
			switch ($lang_mode)
			{
			 case 0:
			    $config->direction = $params->get( 'direction','ltr');
				break;
			 case 1:
				// Joomla Default
				//Access Joomla's global configuation and get the language setting from there
				if (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . strtolower($language->getTag()) . ".js"))
				{
					$config->language = strtolower($language->getTag());
				}
				elseif (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . substr($language->getTag(), 0, strpos($language->getTag(), '-')) . ".js"))
				{
					$config->language = strtolower(substr($language->getTag(), 0, strpos($language->getTag(), '-')));
				} 
				break;
			 case 2:
				$config->language = ""; // Browser default
				$config->direction = "";
				break; 
			}
			
		
			//let's get style format
						 
			if(!$formatsource)
			{
				$config->formatsource = "
					var format = [];
					format['indent'] = false;
					format['breakBeforeOpen'] = false; 
					format['breakAfterOpen'] =  false;
					format['breakBeforeClose'] = false;
					format['breakAfterClose'] = false;
					var dtd = CKEDITOR.dtd;
					for ( var e in CKEDITOR.tools.extend( {}, dtd.\$nonBodyContent, dtd.\$block, dtd.\$listItem, dtd.\$tableContent ) ) {
							editor.dataProcessor.writer.setRules( e, format); 
					} 
			
					editor.dataProcessor.writer.setRules( 'pre',
					{
						indent: false
					}); 
				";
			}	
			else
			{
				$config->formatsource = "
					editor.dataProcessor.writer.setRules( 'pre',
					{
						indent : false,
						breakAfterOpen : false,	
						breakBeforeClose: false
					}); 
				";
			}
			
			
			$inline = JLayoutHelper::render('joomla.arkeditor.inline', $config);

			$userDetails = new stdclass;
			$user = JFactory::getUser();
			$userDetails->name = $user->name;
			$userDetails->username = $user->username;
			$userDetails->email = $user->email;

			$inline.= JLayoutHelper::render('joomla.arkeditor.sidebar', $userDetails);
			
			return $inline;
		}
		
		
						
		/* Load the CK Parameters */
	
	
		$skin							= 	$params->def( 'skin', 'officemetro' );
		$height							= 	$params->def( 'height', $height);
		$width							= 	$params->def( 'width',  $width );
		$lang_mode						= 	$params->def( 'lang_mode', 1 );
		$lang							= 	$params->def( 'lang_code', 'en' );
		$entermode 						= 	$params->def( 'entermode', 1 );
		$shiftentermode 				= 	$params->def( 'shiftentermode', 2 );
		$imagepath						=   $params->def( 'imagePath','images');
		$bgcolor						= 	$params->def( 'bgcolor','#ffffff');
		$ftcolor						= 	$params->def( 'ftcolor','');
        $ftfamily						= 	$params->def( 'ftfamily','');
        $ftsize			    			= 	$params->def( 'ftsize','');
		$textalign						= 	$params->def( 'textalign','');
		$entities						= 	$params->def( 'entities',0);
		$formatsource					= 	$params->def( 'formatsource',1);
		$toolbars						=	$this->_decode($params->get('toolbars'));
		$dialog_backgroundCoverColor	=   $params->def( 'dialog_backgroundCoverColor','black'); 
		$dialog_backgroundCoverOpacity	=   $params->def( 'dialog_backgroundCoverOpacity','0.5');
		$enable_preloader				=	$params->get('enable_preloader',true); 
        $stylelistsource                =   $params->get('stylelistsource', 1);


			
		if(empty($height))
		{
			$height =  480;
		}
			
		
		if (is_numeric($width))
		{
			$width .= 'px';
		}

		if (is_numeric($height))
		{
			$height .= 'px';
		}
	
		//Diaplay textarea	
		$textarea = new stdClass;
		$textarea->name    = $name;
		$textarea->id      = $id;
		$textarea->cols    = $col;
		$textarea->rows    = $row;
		$textarea->width   = $width;
		$textarea->height  = $height;
		$textarea->content = $content;
		

		
		if(static::$loaded)
		{
	
			
			$retunScript = JLayoutHelper::render('joomla.arkeditor.textarea', $textarea) . ($enable_preloader ?  JLayoutHelper::render('joomla.arkeditor.preloader', $textarea) : '').'<script>CKEDITOR.domReady(function(event){ 
			
			if(!CKEDITOR.textareas)
				CKEDITOR.textareas = [];

			CKEDITOR.textareas[CKEDITOR.textareas.length] = { "id":"'.$textarea->id.'", "width":"'.$textarea->width.'", "height":"'.$textarea->height.'" };';
			
			
			if(!static::$loadFunc)
			{
				$retunScript .='
			
				CKEDITOR.replacetextAreas = function(config) 
				{
					function process(textarea,config)
					{
						var editor = CKEDITOR.replace(textarea.id,config);
						var xtdbuttons = CKEDITOR.document.getById("editor-xtd-buttons");
						if(xtdbuttons)
						{  
							editor.on("loaded", function(evt)
							{
								buttonsHtml = xtdbuttons.getOuterHtml().replace(/'.static::$loaded.'/g,this.name);
								var buttonsElement = CKEDITOR.dom.element.createFromHtml(buttonsHtml); 
								this.container.getParent().append(buttonsElement);
													
								var elements = buttonsElement.getElementsByTag("a");
															
								for(i= 0; i < elements.count();i++)
								{
									//override mootools model click event
									if(elements.getItem(i).hasClass("modal-button"))
									{
										(function()
										{
											var el = $(elements.getItem(i).$);
											el.addEvent("click", function(e) 
											{
												e.stop();
												SqueezeBox.fromElement(el,	{
																				parse: "rel"
																			});
											});
										})();
									}		
								}				
							
							});
						}
					}
						
					var textareas = this.textareas;
					setTimeout(function step() {
					 process(textareas.shift(),config);
					 if(textareas.length > 0) {
						 setTimeout(step,25)
					 }
					},25);
				}	
				CKEDITOR.once("instanceReady",function(evt)
				{
			
					var config =  CKEDITOR.instances["'.static::$loaded.'"].config;
					CKEDITOR.replacetextAreas(config);
				})';
				static::$loadFunc = true;
			}
			
		
			return $retunScript .'			
			});</script>';
			

		}
		else
			static::$loaded = $id;	
		
		$plugin = JPluginHelper::getPlugin('editors','arkeditor');	
		$plugin->inlineMode = ArkInlineMode::REPLACE;
			
		$toolbar_name_bk	=	$params->def( 'toolbar', 'back' );
		$toolbar_name_ft 	=	$params->def( 'toolbar_ft', 'front' );
		
		$toolbar = $toolbars[$toolbar_name_bk];
		
		if($this->app->isClient('site'))
		{
			$toolbar = $toolbars[$toolbar_name_ft];
		
		}
			

			
		//lets get language direction
		$language	= JFactory::getLanguage();

		if ($language->isRTL()) {
			$direction = 'rtl';
		} else {
			 $direction = 'ltr';
		}
		
		 $defaultLanguage = 'en';
		switch ($lang_mode)
		{
		 case 0:
			 $direction = $params->get( 'direction','ltr');
			 break;
		 case 1:
			// Joomla Default
			//Access Joomla's global configuation and get the language setting from there
			if (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . strtolower($language->getTag()) . ".js"))
			{
				$lang = strtolower($language->getTag());
			}
			elseif (file_exists(JPATH_PLUGINS . "/editors/arkeditor/ckeditor/lang/" . substr($language->getTag(), 0, strpos($language->getTag(), '-')) . ".js"))
			{
				$lang = strtolower(substr($language->getTag(), 0, strpos($language->getTag(), '-')));
			} 
			break;
		 case 2:
			$lang = ""; // Browser default
			$direction = "";
			break; 
		}
		
		//let's get style format
					 
		if(!$formatsource)
		{
			$ormatsource = "
				var format = [];
				format['indent'] = false;
				format['breakBeforeOpen'] = false; 
				format['breakAfterOpen'] =  false;
				format['breakBeforeClose'] = false;
				format['breakAfterClose'] = false;
				var dtd = CKEDITOR.dtd;
				for ( var e in CKEDITOR.tools.extend( {}, dtd.\$nonBodyContent, dtd.\$block, dtd.\$listItem, dtd.\$tableContent ) ) {
						editor.dataProcessor.writer.setRules( e, format); 
				} 
		
				editor.dataProcessor.writer.setRules( 'pre',
				{
					indent: false
				}); 
			";
		}	
		else
		{
			$formatsource = "
				editor.dataProcessor.writer.setRules( 'pre',
				{
					indent : false,
					breakAfterOpen : false,	
					breakBeforeClose: false
				}); 
			";
		}
		
		$document = JFactory::getDocument();
		
				
		$document->addCustomTag("<script>
			CKEDITOR.domReady(function(event)
			{
				//addCustom CSS
				CKEDITOR.addCss( 'body { background: ". $bgcolor . " none;". ($textalign ? " text-align: ".$textalign.";" :"")."}' );
				".( $ftcolor ? "CKEDITOR.addCss( 'body { color: ". $ftcolor."; }' )" : "")."
				".( $ftfamily ? "CKEDITOR.addCss( 'body { font-family: ".$ftfamily."; }' )" : "")."
				".( $ftsize ? "CKEDITOR.addCss( 'body { font-size: ". $ftsize."; }' )" : "")."
			});
		</script>");
		
		$stylesheets = $this->_getTemplateCSS();
		$customStylesheetPath = "";

       
		if($stylelistsource == '3')
		{
			$url = $params->get('arkcustomtypographyfile', '');
            $customStylesheetPath = '';
            if($url)
            {
                $customStylesheetPath = preg_replace('/^(.*?)(\/|\\\\)templates(.*?)$/','templates$3',$url);
                $customStylesheetPath = preg_replace('/\\\\/','/',$customStylesheetPath);
            }
		}	
		
		
		$document->addCustomTag("
            <script>
			CKEDITOR.on( 'instanceCreated', function( evt ) {
			
				evt.editor.on( 'configLoaded', function() {
					
					this.config.stylesheetParser_validSelectors = /^(\w|\-)*?(\.|#)(\w|\-)+$/; 
					
					var styleSheets = ".$stylesheets."
					this.config.contentsCss = [];
					
					for(var i = 0; i < styleSheets.length; i++)
					{
						this.config.contentsCss[i] = styleSheets[i].href;
					}
					this.config.contentsCss.push('".JURI::root()."index.php?option=com_ajax&plugin=arktypography&format=json&task=editor') 
					". (!$customStylesheetPath ? "this.config.contentsCss.push('".JURI::root()."index.php?option=com_ajax&plugin=arktypography&format=json&task=custom')	":"")."  
					".($customStylesheetPath ? "this.config.contentsCss.push('".JURI::root().$customStylesheetPath."')" : "")."
					
				});	
			})
		</script>");
		
		$document->addCustomTag("<script>
			CKEDITOR.domReady(function(event)
			{
				CKEDITOR.tools.callHashFunction('".$id."','".$id."');
			});
		</script>");
		
		$xtdbuttons = $this->_displayButtons($id, $buttons, $asset, $author);
	
		$document->addCustomTag("<script>
		
		CKEDITOR.JoomlaXTDbuttons = '". preg_replace("/\s+|\n+|\r/"," ",str_replace("'","\'",$xtdbuttons))."';
				
		</script>"); 
		
		$joomlaOptions = array(
		   "baseHref"=>JURI::root(),
		   "base"=>JURI::base(),     
		    "imagePath"=>$imagepath, 	
		    "skin"=>$skin, 
		    "toolbar"=>$toolbar,
			"toolbar_inline"=>$toolbars['inline'],
			"toolbar_image"=>$toolbars['image'],
			"contentsLangDirection"=>$direction,
			"language"=>$lang,
			"defaultLanguage"=>$defaultLanguage,
			"enterMode"=>$entermode,
			"shiftEnterMode"=>$shiftentermode,
			"entities"=>$entities,
			"dialog_backgroundCoverColor"=>$dialog_backgroundCoverColor,
			"dialog_backgroundCoverOpacity"=>$dialog_backgroundCoverOpacity,
			"extraAllowedContent"=>"hr[class,id]",
			"csrfToken"=>JSession::getFormToken()
		);
		
		if($width )
			$joomlaOptions['width'] = $width;
		
		if($height )
			$joomlaOptions['height'] = $height;
		
		$document->addScriptOptions('plg_editor_arkeditor', $joomlaOptions, false);
		
		$document->addCustomTag("<script>	
			CKEDITOR.tools.addHashFunction(function(div)
			{
				//create editor instance
				var joomlaOptions = Joomla.optionsStorage && Joomla.optionsStorage['plg_editor_arkeditor'] || Joomla.getOptions('plg_editor_arkeditor', {});
				var oEditor = CKEDITOR.replace(div,joomlaOptions);
			},'" . $id . "');</script>"); 
	
		
		
		
		$editor = JLayoutHelper::render('joomla.arkeditor.textarea', $textarea) . ($enable_preloader ?  JLayoutHelper::render('joomla.arkeditor.preloader', $textarea) : '').
		$xtdbuttons;
  
		return $editor;
	}	
	
	private function _displayButtons($name, $buttons, $asset, $author)
	{
		
		
        $return = '';
        
        $args = array(
			'name'  => $name,
			'event' => 'onGetInsertMethod'
		);
		

        if(method_exists($this, 'update'))
		{	
			$results = (array) $this->update($args);
			
			foreach ($results as $result)
			{
				if (is_string($result) && trim($result))
				{
					$return .= $result;
				}
			}
		}
		else
			$this->onGetInsertMethod($name);

		
		
		if (is_array($buttons) || (is_bool($buttons) && $buttons))
		{	

            
            if (method_exists($this, 'getDispatcher')) {

                		
			    $buttonsEvent = new Joomla\Event\Event(
			        'getButtons',
			        [
				        'editor'  => $name,
				        'buttons' => $buttons,
			        ]
		        );

                $buttons = $this->getDispatcher()->dispatch('getButtons', $buttonsEvent);
                $xtdbuttons = $buttons['result'];
                $return .= JLayoutHelper::render('joomla.editors.buttons',  $xtdbuttons);
            } 
			else {
                $buttons = $this->_subject->getButtons($name, $buttons, $asset, $author);
                $return .= JLayoutHelper::render('joomla.arkeditor.buttons', $buttons);
			}

		}
		return $return;
	
	}
	
	private function _getTemplateCSS()
	{
		//load up CSS sylesheets
		$stylesheetsString = 'CKEDITOR.document.$.styleSheets';
		
		if($this->app->isClient('administrator'))
		{
			$params = JComponentHelper::getParams('com_arkeditor');
			$stylesheets = array();
			
			//Check to see if the DomDocument class has been installed. As not all hosting companies install it.
			if( !class_exists( 'DOMDocument' ) )
			{
				if( $params->get( 'enable_debug', false ) )
				{
					JFactory::getApplication()->enqueueMessage( 'Missing required class DomDocument. Please ask your server administrator to install PHP\'s XML library.', 'error');
				}//end if
				
				return '';
			}//end if
			
			$dom = new DOMDocument();
			$dom->strictErrorChecking = false;
			$dom->recover = true;
			
            $session = JFactory::getSession();
			$server = $this->app->input->server;
			$authToken = $server->get('HTTP_AUTHORIZATION', $server->get('REDIRECT_HTTP_AUTHORIZATION','','string'),'string');
					
			$headers = array
				( 
					'Accept-Encoding'=>'gzip;q=1.0, *;q=0', 
					'User-Agent'=>'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36', 
					'Referer'=>'https://www.google.com',
					'Cookie'=>'arkeditor_typography=1;'.$session->getName().'='.$session->getId()
				);
				
			if($authToken)
			{	
				$headers['Authorization'] = $authToken;
				
				if($params->get( 'useArkProxy' ))
				{	
					$table = JTable::getInstance( 'extension' );
					if( $table->load( array( 'element'=>'com_arkeditor' ) ) )
					{
						$params = new JRegistry( $table->params );
						$params->set( 'useArkProxy', false );
						$table->save( array( 'params' => $params->toArray() ) );
					}//end if
					unset( $table );
				}
			}
			
			
			if( version_compare(JVERSION, '3.4.0', 'ge') && $params->get( 'useArkProxy' ) )
			{

				$http = JHttpFactory::getHttp();
				try
				{
					$response	= $http->get( 'https://www.arkextensions.com/index.php?option=com_ajax&plugin=arkproxy&format=json&url=' . base64_encode( JURI::root() ), [], 5 );
				}
				catch( Exception $e )
				{
					
					$response = new stdclass;
					$response->body = '';
				}
				
				@$dom->loadHTML($response->body);
			}
			elseif(version_compare(JVERSION, '3.4.0', 'ge'))
			{
				$http = JHttpFactory::getHttp();
                          
				$response = null;	
				try
				{
					
					$response = $http->get(JURI::root(), $headers, 5 );
					
					if(!empty($response) && empty($response->body) ||  !in_array($response->code, array(200,403)))  
					{
						$http = null;
						$http = JHttpFactory::getHttp(new JRegistry,'socket');
					    $response = $http->get(JURI::root(), $headers, 5 );
						
						//Check to see if the HTTP status code successful. Else try the ark proxy server.
						if( !in_array($response->code, array(200,403)) )
						{
							throw new Exception( 'Unable to connect via local connection.', $response->code );
						}//end if
					}//end if
					
					//Decompress the data
					if( isset($response->headers['Content-Encoding']) && $response->headers['Content-Encoding'] == 'gzip' )
					{
						if (!function_exists('gzdecode')) 
						{
							function gzdecode($data)
							{
								return gzinflate(substr($data,10,-8));
							}
						}
						//check that headers are not faking it!
						$is_really_gzip = 0 === JString::strpos($response->body , "\x1f" . "\x8b" . "\x08");
						
						if($is_really_gzip)
							$response->body = gzdecode( $response->body );
					}//end if
					
				}
				catch(Exception $ex)
				{
					
					try
					{
						$http = JHttpFactory::getHttp();
                        //The system has failed to acces the root index file. So try via the Ark Proxy. It is succeeds then force the editor
						//to use the proxy each time.
						$response	= $http->get( 'https://www.arkextensions.com/index.php?option=com_ajax&plugin=arkproxy&format=json&url=' . base64_encode( JURI::root() ), [], 5 );
						//Check to see if the HTTP status code is success. Else fail.
						if( $response->code !== 200 )
						{
							//Bail out as this isn't working.
							if( $params->get( 'enable_debug', false ) )
							{
								JFactory::getApplication()->enqueueMessage( 'Failed to read the website source code and thus parse the CSS. Originating error code: '.$ex->getCode().', Proxy error code: '.$response->code, 'error');
							}//end if
							return '';
						}//end if
						$table = JTable::getInstance( 'extension' );
						if( $table->load( array( 'element'=>'com_arkeditor' ) ) )
						{
							$params = new JRegistry( $table->params );
							$params->set( 'useArkProxy', true );
							$table->save( array( 'params' => $params->toArray() ) );
						}//end if
						unset( $table );
					}
					catch( Exception $e )
					{
						
						$response = new stdclass;
						$response->body = '';
					}

				}
	

				@$dom->loadHTML($response->body);
			}
			else
			{	
				$options=array(
					"ssl"=>array(
						"verify_peer"=>false,
						"verify_peer_name"=>false
					)
				);  
							
				@$dom->loadHTML(file_get_contents(JURI::root(),false,stream_context_create($options)));	
			}
			$links = $dom->getElementsByTagName('link');
			
			foreach($links as $link)
			{
			   // if $link_tag rel == stylesheet
			   // get href value
			   if($link->hasAttribute('rel') && $link->getAttribute('rel') == 'stylesheet')
			   {
					$href = $link->getAttribute('href');
					if( !preg_match('/(^https?|^\/\/)/',$href))
					{
						$uri = JURI::getInstance();
						$base = $uri->toString(array('scheme', 'host', 'port'));
						$href = $base.$href;
					}	
					$stylesheets[] = array('href'=> $href);
			   }
			}
			
			$stylesheetsString = json_encode($stylesheets);
		}

		return $stylesheetsString;
	}
	
	private function _decode($decode)
	{
		return json_decode(base64_decode($decode),true);
	}
	
	private function _cleanString($str)
  	{
	// remove any whitespace, and ensure all characters are alphanumeric
     $str = preg_replace(array('/\s+/','/\[/','/[^A-Za-z0-9_\-]/'), array('-','_',''), $str);
     // trim
     $str = trim($str);
     return $str;
    }

    protected static function importPlugin($type, $plugin = null, $autocreate = true, $dispatcher = null)
	{
		static $loaded = array();

        
 	    // Check for the default args, if so we can optimise cheaply
		$defaults = false;

		if (is_null($plugin) && $autocreate == true && is_null($dispatcher))
		{
			$defaults = true;
		}
        
		$loadedPlugin = null;
		
		if (version_compare(JVERSION, '4.0', 'lt' ) )
        {
			if (!isset($loaded[$type]) || !$defaults)
			{
				$results = null;

				// Load the plugins from the database.
				$plugins = static::load();

				// Get the specified plugin(s).
				for ($i = 0, $t = count($plugins); $i < $t; $i++)
				{
					if ($plugins[$i]->type == $type && ($plugin === null || $plugins[$i]->name == $plugin))
					{
						static::import($plugins[$i], $autocreate, $dispatcher);
						$results = true;
					}
				}

				// Bail out early if we're not using default args
				if (!$defaults)
				{
					return $results;
				}

				$loaded[$type] = $results;
				
				$loadedPlugin = $loaded[$type];
			}
		}
		else
		{
			// Ensure we have a dispatcher now so we can correctly track the loaded plugins
			$dispatcher = $dispatcher ?: JFactory::getApplication()->getDispatcher();

			// Get the dispatcher's hash to allow plugins to be registered to unique dispatchers
			$dispatcherHash = spl_object_hash($dispatcher);

			if (!isset($loaded[$dispatcherHash]))
			{
				$loaded[$dispatcherHash] = array();
			}

			if (!$defaults || !isset($loaded[$dispatcherHash][$type]))
			{
				$results = null;

				// Load the plugins from the database.
				$plugins = static::load();

				// Get the specified plugin(s).
				for ($i = 0, $t = \count($plugins); $i < $t; $i++)
				{
					if ($plugins[$i]->type === $type && ($plugin === null || $plugins[$i]->name === $plugin))
					{
						static::import($plugins[$i], $autocreate, $dispatcher);
						$results = true;
					}
				}

				// Bail out early if we're not using default args
				if (!$defaults)
				{
					return $results;
				}

				$loaded[$dispatcherHash][$type] = $results;
				
				$loadedPlugin = $loaded[$dispatcherHash][$type];
			}
			
		}	
		return $loadedPlugin;
	}

    protected static function import($plugin, $autocreate = true, $dispatcher = null)
	{
		static $collection = array();

  
		$plugin->type = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->type);
		$plugin->name = preg_replace('/[^A-Z0-9_\.-]/i', '', $plugin->name);

        $path = JPATH_PLUGINS . '/' . $plugin->type . '/' . $plugin->name . '/' . $plugin->name . '.php';
		
		if(version_compare(JVERSION, '4.0', 'lt' ) )
        {
			if (!isset($collection[$path]))
			{
				if (file_exists($path))
				{
					if (!isset($collection[$path]))
					{
						require_once $path;
					}

					$collection[$path] = true;

					if ($autocreate)
					{
						// Makes sure we have an event dispatcher
						if (!is_object($dispatcher))
						{
							$dispatcher = JEventDispatcher::getInstance();
						}
						$type = str_replace('-','',$plugin->type);
						$className = 'Plg' . $type . $plugin->name;

						if (class_exists($className))
						{
							// Load the plugin from the database.
							if (!isset($plugin->params))
							{
								// Seems like this could just go bye bye completely
								$plugin = static::getPlugin($plugin->type, $plugin->name);
							}

							// Instantiate and register the plugin.
							new $className($dispatcher, (array) ($plugin));
						}
					}
				}
				else
				{
					$collection[$path] = false;
				}
			}
		}
		else
		{
			// Get the dispatcher's hash to allow paths to be tracked against unique dispatchers
			$hash = spl_object_hash($dispatcher) . $plugin->type . $plugin->name;

			if (\array_key_exists($hash, $collection))
			{
				return;
			}

			$collection[$hash] = true;

			$plugin = JFactory::getApplication()->bootPlugin($plugin->name, $plugin->type);

			if ($dispatcher && $plugin instanceof Joomla\Event\DispatcherAwareInterface)
			{
				$plugin->setDispatcher($dispatcher);
			}

			if (!$autocreate)
			{
				return;
			}

			$plugin->registerListeners();
		}	
	}

    public static function getPlugin($type, $plugin = null)
	{
		$result = array();
		$plugins = static::load();

		// Find the correct plugin(s) to return.
		if (!$plugin)
		{
			foreach ($plugins as $p)
			{
				// Is this the right plugin?
				if ($p->type == $type)
				{
					$result[] = $p;
				}
			}
		}
		else
		{
			foreach ($plugins as $p)
			{
				// Is this plugin in the right group?
				if ($p->type == $type && $p->name == $plugin)
				{
					$result = $p;
					break;
				}
			}
		}

		return $result;
	}


    protected static function load()
	{
		
        if (static::$plugins !== null)
		{
			return static::$plugins;
		}

		$user = JFactory::getUser();
		$cache = JFactory::getCache('com_arkeditor', '');

		$levels = implode(',', $user->getAuthorisedViewLevels());

        $types = array('arkeditor', 'arkwidget-editor');

       

		if (!(static::$plugins = $cache->get($levels)))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('folder AS type, element AS name, params')
				->from('#__extensions')
				->where('enabled = 1')
				->where('type =' . $db->quote('plugin'))
				->where('state IN (0,1)')
				->where('access IN (' . $levels . ')')
                ->where("folder IN ('" . implode("','",$types) . "')")
				->order('ordering');

			static::$plugins = $db->setQuery($query)->loadObjectList();

			$cache->store(static::$plugins, $levels);
		}

		return static::$plugins;
	}
}