<?php
/**
 * @package     com_arkeditor
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'list' );

/**
 * This Field Renders a Normal Grouped List but Adds Additional Functionality.
 *
 * Extra:
 * 1. Makes up for the Fact that you Can't Select Multiple Options to be Selected by Default.
 * 2. Adds the Functionality to Select all by Passing an Asterisk (*) as the Default.
 * 3. Allow Custom/Manually Typed Options.
 * 4. Support Data Attributes.listswitcher
 */

class JFormFieldListswitcher extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'listswitcher';
	
	protected $display= '';
	
	protected $renderValue = '';

	protected $hideLabel = false;
	
	protected $target;
	
	protected $layout = 'joomla.form.field.listswitcher';
	
	
	public function __set($name, $value)
	{
		
        switch ($name)
		{
			case 'hideLabel':
				$this->hideLabel = filter_var( $value , FILTER_VALIDATE_BOOLEAN);
				break;
			case 'display':
				$this->hideLabel = (string) $value;
				break;
			case 'target':
				$this->target = (string) $value;
				break;		
			default:
				parent::__set($name, $value);
		}

	} 
	


	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 */
	 
	 public function setup(SimpleXMLElement $element, $value, $group = null)
	{ 
		$result = parent::setup($element, $value, $group);
		
		if ($result == true)
        {
			
            $this->hideLabel = filter_var( $element['hideLabel'], FILTER_VALIDATE_BOOLEAN);
		    $this->display =  (string) $element['display'];
		    $this->target =  (string) $element['target'];
        }
		
		return $result;
	} 
	 

    /**
	 * Allow to override renderer include paths in child fields
	 *
	 * @return  array
	 *
	 * @since   3.5
	 */
     
	protected function getLayoutPaths()
    {
       return array(JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/layouts/com_arkeditor',
            JPATH_ADMINISTRATOR . '/components/com_arkeditor/layouts',
            JPATH_SITE.'/layouts'
       );
    }
    
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$this->multiple = '';
		
		$this->renderValue = parent::getinput();
		
		return rtrim($this->getRenderer($this->layout)->render($this->getLayoutData()), PHP_EOL);
	}//end function
	
	
	protected function getLayoutData()
	{
		// Label preprocess
		
		$label = '';


		if(!$this->hideLabel)
		{
			$label = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
			$label = $this->translateLabel ? JText::_($label) : $label;
		}	


		// Description preprocess
		$description = !empty($this->description) ? $this->description : null;
		$description = !empty($description) && $this->translateDescription ? JText::_($description) : $description;

		$alt = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
		
		$formControl = $this->formControl ? $this->formControl : '';
		
		if($this->group)
		{
			if ($formControl)
			{
				$formControl .= '_' . str_replace('.', '_', $this->group);
			}
			else
			{
				$formControl .= str_replace('.', '_', $this->group);
			}
			
		}	

		return array(
			'autocomplete' => $this->autocomplete,
			'autofocus'    => $this->autofocus,
			'class'        => $this->class,
			'description'  => $description,
			'disabled'     => $this->disabled,
			'field'        => $this,
			'group'        => $this->group,
			'hidden'       => $this->hidden,
			'hint'         => $this->translateHint ? JText::alt($this->hint, $alt) : $this->hint,
			'id'           => $this->id,
			'label'        => $label,
			'labelclass'   => $this->labelclass,
			'multiple'     => $this->multiple,
			'name'         => $this->name,
			'onchange'     => $this->onchange,
			'onclick'      => $this->onclick,
			'pattern'      => $this->pattern,
			'readonly'     => $this->readonly,
			'repeat'       => $this->repeat,
			'required'     => (bool) $this->required,
			'size'         => $this->size,
			'spellcheck'   => $this->spellcheck,
			'validate'     => $this->validate,
			'value'        => $this->value,
			'renderValue'  => $this->renderValue,
			'display'	   => $this->display,
			'target'	   => $this->target,
            'formControl'   => $formControl ? $formControl.'_' : ''
		);
	}
	
}//end class