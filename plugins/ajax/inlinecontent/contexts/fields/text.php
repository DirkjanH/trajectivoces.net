<?php
/*------------------------------------------------------------------------
# Copyright (C) 2017-2018 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');

class ARKContextsFieldsText extends ARKContextsBase
{
	
	protected $field_id;
	
	protected $fieldsModel;
	
	public function __construct($id)
	{
		
		list($id, $field_id) = explode('_',$id,2);
		$this->id = $id;
		$this->field_id = $field_id;
		
		$this->fieldModel = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));
		$this->table = $this->fieldModel->getTable();
		$this->table->load($field_id);
        $this->table->fieldparams = new JRegistry($this->table->fieldparams);
   
    }	
	
	
	public function get()
	{
		
		if( $this->id == null)
			return array( 'title'=>'','data'=>'');	
		
		$text = $this->fieldModel->getFieldValue($this->field_id, $this->id);
	
		$title = reset($text);
	
		return array( 'title'=>$text,'data'=>'');	
	}
	
	public function triggerContentPlugins($rawText) {}
	
	
	public function save($data,$type = 'body')
	{
		
		if($this->id == null)
			return array( 'title'=>'','data'=>'');	


        $user = JFactory::getUser();
       
        $parts = explode('.', $this->table->context, 2);

        $asset = $parts[0] . '.field.' . (int) $this->field_id;

        if (!($user->authorise('core.edit.value', $asset))) 
		{
			throw new Exception("Not Authorized");
	    }	

		
		$text = '';
		

		$text = $data['title']; 
        $text =  $this->_filterText($text); 
		
		$message = 'Could not save custom field!';
		
    

		if(!$this->fieldModel->setFieldValue($this->field_id, $this->id, $text))
        	return array( 'title'=>'','data'=>$text,'message'=>$message);	
		
        $message = '';
	
		//Save version
	    
		$typeAlias = 'com_fields.fields';
		$this->table->default_value = $text;
		$contenthistoryHelper = new JHelperContenthistory($typeAlias);			
		$contenthistoryHelper->store($this->table);
        
		
		return array( 'title'=>$text,'data'=>'','message'=>$message);	
	}
	
	public function version($versionId,$type)
	{
		
		$historyTable = JTable::getInstance('Contenthistory');
		$historyTable->load($versionId);
		$rowArray = JArrayHelper::fromObject(json_decode($historyTable->version_data));
			
		$item = $this->table;
		$item->bind($rowArray);	
		$text = $item->default_value;
	
		return array( 'data'=>$text);
		
	}

    
    protected function _filterText($value)
    {
        

        $type = $this->table->type;

         $plugin = JPluginHelper::getPlugin('fields',  $type);

         if(is_string($plugin->params))
            $plugin->params = new JRegistry($plugin->params);
         $params = clone $plugin->params;

         $params->merge($this->table->fieldParams);
                
         $safeHtmlFilter = &JFilterInput::getInstance(null, null, 1, 1);
         $noHtmlFilter = &JFilterInput::getInstance();

         $filter = $params->get('filter','');

         $filter =  strtoupper($filter);

         $return = null;

         switch( strtoupper($filter))
         {
             case 'UNSET':
				break;

			// No Filter.
			case 'RAW':
				$return = $value;
				break;
            case 'SAFEHTML':
				$return = JFilterInput::getInstance(null, null, 1, 1)->clean($value, 'html');
				break;
            
           	case 'URL':
				if (empty($value))
				{
					return false;
				}

				// This cleans some of the more dangerous characters but leaves special characters that are valid.
				$value = JFilterInput::getInstance()->clean($text, 'html');
				$value = trim($text);

				// <>" are never valid in a uri see http://www.ietf.org/rfc/rfc1738.txt.
				$value = str_replace(array('<', '>', '"'), '', $value);

				// Check for a protocol
				$protocol = parse_url($value, PHP_URL_SCHEME);



				// If there is no protocol and the relative option is not specified,
				// we assume that it is an external URL and prepend http://.
                $relative = $params->get('relative', false);

				if (( $type == 'url' && !$protocol &&  !$relative)
					|| (!$element['type'] == 'url' && !$protocol))
				{
					$protocol = 'http';

					// If it looks like an internal link, then add the root.
					if (substr($value, 0, 9) == 'index.php')
					{
						$value = JUri::root() . $value;
					}

					// Otherwise we treat it as an external link.
					else
					{
						// Put the url back together.
						$value = $protocol . '://' . $value;
					}
				}

				// If relative URLS are allowed we assume that URLs without protocols are internal.
				elseif (!$protocol && $relative)
				{
					$host = JUri::getInstance('SERVER')->gethost();

					// If it starts with the host string, just prepend the protocol.
					if (substr($value, 0) == $host)
					{
						$value = 'http://' . $value;
					}

					// Otherwise if it doesn't start with "/" prepend the prefix of the current site.
					elseif (substr($value, 0, 1) != '/')
					{
						$value = JUri::root(true) . '/' . $value;
					}
				}

				$value = JStringPunycode::urlToPunycode($value);
				$return = $value;
				break; 
            case 'TEL':
				$value = trim($value);

				// Does it match the NANP pattern?
				if (preg_match('/^(?:\+?1[-. ]?)?\(?([2-9][0-8][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$/', $value) == 1)
				{
					$number = (string) preg_replace('/[^\d]/', '', $value);

					if (substr($number, 0, 1) == 1)
					{
						$number = substr($number, 1);
					}

					if (substr($number, 0, 2) == '+1')
					{
						$number = substr($number, 2);
					}

					$result = '1.' . $number;
				}

				// If not, does it match ITU-T?
				elseif (preg_match('/^\+(?:[0-9] ?){6,14}[0-9]$/', $value) == 1)
				{
					$countrycode = substr($value, 0, strpos($value, ' '));
					$countrycode = (string) preg_replace('/[^\d]/', '', $countrycode);
					$number = strstr($value, ' ');
					$number = (string) preg_replace('/[^\d]/', '', $number);
					$result = $countrycode . '.' . $number;
				}

				// If not, does it match EPP?
				elseif (preg_match('/^\+[0-9]{1,3}\.[0-9]{4,14}(?:x.+)?$/', $value) == 1)
				{
					if (strstr($value, 'x'))
					{
						$xpos = strpos($value, 'x');
						$value = substr($value, 0, $xpos);
					}

					$result = str_replace('+', '', $value);
				}

				// Maybe it is already ccc.nnnnnnn?
				elseif (preg_match('/[0-9]{1,3}\.[0-9]{4,14}$/', $value) == 1)
				{
					$result = $value;
				}

				// If not, can we make it a string of digits?
				else
				{
					$value = (string) preg_replace('/[^\d]/', '', $value);

					if ($value != null && strlen($value) <= 15)
					{
						$length = strlen($value);

						// If it is fewer than 13 digits assume it is a local number
						if ($length <= 12)
						{
							$result = '.' . $value;
						}
						else
						{
							// If it has 13 or more digits let's make a country code.
							$cclen = $length - 12;
							$result = substr($value, 0, $cclen) . '.' . substr($value, $cclen);
						}
					}

					// If not let's not save anything.
					else
					{
						$result = '';
					}
				}

				$return = $result;

				break;
                default:
				// Check for a callback filter.
				if (strpos($filter, '::') !== false && is_callable(explode('::', $filter)))
				{
					$return = call_user_func(explode('::', $filter), $value);
				}

				// Filter using a callback function if specified.
				elseif (function_exists($filter))
				{
					$return = call_user_func($filter, $value);
				}

				// Check for empty value and return empty string if no value is required,
				// otherwise filter using JFilterInput. All HTML code is filtered by default.
				else
				{
					$required = (int) $this->table->required;

					if (($value === '' || $value === null) && ! $required)
					{
						$return = '';
					}
					else
					{
						$return = JFilterInput::getInstance()->clean($value, $filter);
					}
				}
				break;
         }

         return $return;

    }
	
}	