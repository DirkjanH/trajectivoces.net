<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die();

class JTableObserverARKPlugin extends JTableObserver
{

	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);
		return $observer;
	}

	public function onAfterLoad(&$result, $row)
	{
		$db =  JFactory::getDBO(); 
		$query = $db->getQuery(true);
		$query->select('enabled')
			->from('#__extensions')
			->where('folder = '.$db->quote('arkeditor'))
			->where('custom_data = '.$this->table->id);
		
		$db->setQuery( $query );
		$state = $db->loadResult();
		
		if($state !== false)
		{
			if($this->table->published != $state)
				$this->table->published = $state;
		}
	}
	
	public function onAfterStore(&$result)
	{
		if ($result)
		{
			$extenson = JTable::getInstance('extension');
			$extenson->load(array('folder'=>'arkeditor','custom_data'=>$this->table->id));
			$extenson->save(array('params'=>$this->table->params));
		}	
	}
	
	public function onTableAfterStore($event)
    {
		$stdEvent = json_decode(json_encode($event));
        $event = unserialize(
            preg_replace('@^O:8:"stdClass":@','O:38:"Joomla\\CMS\\Event\\Table\\AfterStoreEvent":',
            serialize($stdEvent))
		);
	   
	   
	   $table	= $event['subject'];
	   $result = $event['result'];
	   
	   if(!($table instanceof ARKTablePlugin))
		   return;

       
		if ($result)
		{
            $extenson = JTable::getInstance('extension');
			$extenson->load(array('folder'=>'arkeditor','custom_data'=>$table->id));
			$extenson->save(array('params'=>$table->params));
        
        } 

    }

    
	public function onTableAfterLoad($event)
	{
	    $stdEvent = json_decode(json_encode($event));
        $event = unserialize(
            preg_replace('@^O:8:"stdClass":@','O:37:"Joomla\\CMS\\Event\\Table\\AfterLoadEvent":',
            serialize($stdEvent))
		);
	
		$table	= $event['subject'];
		
		if(!($table instanceof ARKTablePlugin))
		   return;
		

        $db =  JFactory::getDBO(); 
		$query = $db->getQuery(true);
		$query->select('enabled')
			->from('#__extensions')
			->where('folder = '.$db->quote('arkeditor'))
			->where('custom_data = '.$table->id);
		
		$db->setQuery( $query );
		$state = $db->loadResult();
		
		if($state !== false)
		{
			if($this->table->published != $state)
				$this->table->published = $state;
		}
	}
	
	
}