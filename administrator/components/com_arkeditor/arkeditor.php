<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the ARK Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

// Require specific controller
// Controller

// -PC- J3.0 fix
if( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );

//load base classes
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/base/loader.php');

//defines CKEDITOR library includes path
define('CKEDITOR_LIBRARY',JPATH_PLUGINS.'/editors/arkeditor/ckeditor'); 

define('ARKEDITOR_COMPONENT', JURI::root() . 'administrator/components/com_arkeditor');

//load  default style sheets
$document = JFactory::getDocument();
$document->addStyleSheet( ARKEDITOR_COMPONENT . '/css/header.css');

arkimport('base.controller');
arkimport('base.listener');
//now load in JBrowser class as it is now needed
jimport('joomla.environment.browser');

//register all event listeners
ARKRegisterAllEventlisetners();
$app = JFactory::getApplication();

//Map table observers

if (version_compare(JVERSION, '4.0', '<' ) == 1)
{
    require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/observer/plugin.php';
    JObserverMapper::addObserverClassToClass('JTableObserverARKPlugin', 'ARKTablePlugin');
}
else
{
    if(!class_exists('JTableObserver'))
    {
        abstract class JTableObserver {};
    }
    
    if(!class_exists('JEvent'))
    {
        abstract class JEvent {
            
             function __construct( &$subject ) {}

        };
    }

    require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/observer/plugin.php';

    $dispatcher = JFactory::getApplication()->getDispatcher();
    $jtableObserverARKPlugins = new JTableObserverARKPlugin;
    $dispatcher->addListener('onTableAfterStore', [$jtableObserverARKPlugins, 'onTableAfterStore']);
    
}

$controllername = '';

$task = $app->input->get('task','' );

if(strpos($task,'.'))
	list($controllername,$task) = explode('.',$task);

if($controllername)
  $app->input->set('controller',$controllername);
 
//Make sure we load in system language file for component

$lang = JFactory::getLanguage();
$component = 'com_arkeditor.sys';
$lang->load($component, JPATH_ADMINISTRATOR);
  
require_once('helper.php');
//$view = $app->input->get('view','cpanel' );

if(!is_dir(CKEDITOR_LIBRARY))
{
	$app->enqueueMessage( JText::_( 'COM_ARKEDITOR_MSG_NOEDITOR_DETECT' ), 'warning' );
    $app->input->set( 'task','' );
    $app->input->set( 'view','cpanel' );
}
elseif( !JPluginHelper::isEnabled( 'editors', 'arkeditor' ) )
{
	$app->enqueueMessage( JText::_( 'COM_ARKEDITOR_MSG_EDITOR_DISABLED' ), 'warning' );
    if($app->input->get( 'task','' ) != 'cpanel.editor')
    {
        $app->input->set( 'task','' );
        $app->input->set( 'view','cpanel' );
    }
}//end if

// main helper class
arkimport('helper');
// global include classes
arkimport('parameter.parameter');
arkimport('html.html');

$controller =  JControllerLegacy::getInstance('ARK');
	
$controller->execute($app->input->get( 'task' ));
$controller->redirect();