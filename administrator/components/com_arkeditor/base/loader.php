<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

// -PC- J3.0 fix
jimport( 'joomla.filesystem.folder' );

class ARKLoader
{
	 /**
	 * Loads a class from specified directories.
	 *
	 * @param string $name	The class name to look for ( dot notation ).
	 * @param string $base	Search this directory for the class.
	 * @param string $key	String used as a prefix to denote the full path of the file ( dot notation ).
	 * @return void
	 * @since 1.5
	 */
	public static function import($filePath)
	{
		static $paths;

		if (!isset($paths))
		{
			$paths = array();
		}

		$keyPath 	= $filePath;
		$base 		= JPATH_COMPONENT_ADMINISTRATOR;
		$parts 		= explode( '.', $filePath );
		$classname 	= array_pop( $parts );

		if(!isset($paths[$keyPath]))
		{
			if(in_array('event',$parts))
			{
				
				if(in_array('observable',$parts))
				{
					$classname = 'ARK'.  ucfirst($classname) .'Observable';
				}
				else
				{
					$classname	= 'ARK'. ucfirst($classname) . 'ControllerListener';
				}
			}
			elseif(in_array('controllers',$parts))
			{
				$classname = ucfirst($classname) .'Controller';
			}
			else
			{
				$classname = 'ARK'.  ucfirst($classname);
			}

			$path		= str_replace( '.', DS, $filePath );
			$classes	= ARKLoader::register($classname, $base.'/'.$path.'.php');
			$rs			= isset($classes[strtolower($classname)]);
			$paths[$keyPath] = $rs;
		}

		return $paths[$keyPath];
	}

	/**
	 * Add a class to autoload
	 *
	 * @param	string $classname	The class name
	 * @param	string $file		Full path to the file that holds the class
	 * @return	array|boolean  		Array of classes
	 * @since 	1.5
	 */
	public static function &register($class = null, $file = null)
	{
		static $classes;
	
		if(!isset($classes)) {
			$classes = array();
		}

		if($class && is_file($file))
		{
			// Force to lower case.
			$class = strtolower($class);
			$classes[$class] = $file;

			// In php4 we load the class immediately.
			if((version_compare( phpversion(), '5.0' ) < 0)) {
				ARKLoader::load($class);
			}
		}

		return $classes;
	}

	/**
	 * Load the file for a class
	 *
	 * @access  public
	 * @param   string  $class  The class that will be loaded
	 * @return  boolean True on success
	 * @since   1.5
	 */
	public static function load( $class )
	{
		$class = strtolower($class); //force to lower case

		if (class_exists($class)) {
			return;
		}

		$classes = ARKLoader::register();
		if(array_key_exists( strtolower($class), $classes)) {
			include($classes[$class]);
			return true;
		}
		return false;
	}
}

/**
 * Intelligent file importer
 *
 * @access public
 * @param string $path A dot syntax path
 * @since 1.5
 */
function arkimport( $path )
{
	return ARKLoader::import($path);
}

function ARKRegisterAllEventlisetners()
{
	$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.'/event');

	foreach($files as $file)
	{
		arkimport('event.'. str_replace('.php','',$file));
	}
}

if(function_exists('spl_autoload_register'))
{
	spl_autoload_register(array('ARKLoader','load'));
}