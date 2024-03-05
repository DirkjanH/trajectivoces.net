<?php
/**
 * @version		$Id: cache.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Joomla! Page Cache Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.arktypography
 */
 
class plgSystemARKtypography extends JPlugin
{

	public  $cache = null;
	protected $db,$app;
	
	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param	array	$config  An array that holds the plugin configuration
	 * @since	1.0
	 */

     
	 public function onAfterInitialise() {
		
		// Work only on front-end
		if ($this->app->isClient('administrator')){  return; }
	
        $hash = JApplicationHelper::getHash('administrator');
        $cokkie_name = md5($hash);
        $sessionId = $this->app->input->cookie->get($cokkie_name);

        $query = $this->db->getQuery(true)
			->select('count(1)')
			->from($this->db->quoteName('#__session'))
			->where($this->db->quoteName('session_id') . ' = '. $this->db->quote($sessionId))
            ->where('client_id = 1')
            ->where('guest = 0');
         $this->db->setQuery($query);
         $adminCount = $this->db->loadResult();


		$arkeditor = $this->app->input->cookie->get('arkeditor_typography',false);
		
		if (JFactory::getUser()->get('guest') && $adminCount && $arkeditor) {
			$config = JFactory::getConfig();
			$config->set('offline', 0);
		}
		
	}


    public function onExtensionAfterSave($context, $table, $isNew  )
	{
	    
       if (!$this->app->isClient('administrator')){  return; }
       
              
       if($context != 'com_config.component')
            return;
       
       if($table->type != 'component' || $table->element != 'com_arkeditor')
          return;
          
       $input = $this->app->input;
       $data   = $input->get('arkform', array(), 'array');
  

       //filter data

       $filter = array();

       foreach($data as $k => $v)
       {
           if(strpos($k,'enable_stylesheet') !== false)
                $filter[$k] = $v;
       }
     	
       if(empty($filter))
            return;

       $params = $table->params;
       $jparams = new  JRegistry($params);
       $bind = $jparams->toArray(); 
       $bind = array_merge($bind,$filter);
       $table->save(array('params'=>$bind));
		
	}
	 
	 
	 
	 function onAfterRoute()
     {
		if ($this->app->isClient('administrator')) {
			return;
		}
		
		if(!file_exists(JPATH_PLUGINS.'/editors/arkeditor')) {
			return;
		}

		$doc = JFactory::getDocument();
		
		if($doc->getType() != 'html') {  //If not correct document type  exit
			return;
		}
       
        $query = $this->db->getQuery(true)
		->select('params')
		->from($this->db->quoteName('#__extensions'))
		->where($this->db->quoteName('element') . ' = '. $this->db->quote('com_arkeditor'))
        ->where($this->db->quoteName('type') . ' = '. $this->db->quote('component'));
        $this->db->setQuery($query);
        $params = $this->db->loadResult();
        $params = new JRegistry($params);
		
		$enable_stylesheet = $params->get('enable_stylesheet_arktypographycontent', 1);

        if(empty($enable_stylesheet))
            return;
       
              						
		$data = $doc->getHeadData();
		$stylesheet = array();
		$url = JURI::base(true).'/index.php?option=com_ajax&plugin=arktypography&format=json';

        if(version_compare( JVERSION, '3.7', '<' ))
        {  
		    $stylesheet[$url]['media'] = null;
		    $stylesheet[$url]['attribs'] = array();
        } 
        
        $stylesheet[$url]['mime'] = 'text/css';
		$data['styleSheets'] = $stylesheet + $data['styleSheets'];
        
		$doc->setHeadData($data);
	}
}