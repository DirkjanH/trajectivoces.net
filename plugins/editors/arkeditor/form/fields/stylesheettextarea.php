<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('textarea');

/**
 * Form Field class for the Joomla Platform.
 * Supports a multi line area for entry of plain text
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 *
 */
class JFormFieldStylesheetTextarea extends JFormFieldTextarea 
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Stylesheettextarea';
	
	/**
	 * The height of the textarea.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $height;

	/**
	 * The width of the textarea.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $width;
	
	protected $buttonClass;
	
	

	
	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'height':
			case 'width':
			case 'buttonClass':
				$this->$name = (string) $value;
				break;
			default:
				parent::__set($name, $value);
		}
	}

	

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$this->height      = $this->element['height'] ? (string) $this->element['height'] : '340px';
			$this->width       = $this->element['width'] ? (string) $this->element['width'] : '100%';
			$this->buttonClass	= $this->element['buttonClass'] ? ' '. (string) $this->element['buttonClass'] : '';
            $this->allowDisable = false;
            if($this->element['allow_disable'])
            {
                $value = (string)  $this->element['allow_disable']; 
				$this->allowDisable = ($value === 'true' || $value === '1');
            }

		}

		return $result;
	}	
		
		
		
	/**
	 * Method to get the textarea field input markup.
	 * Use the rows and columns attributes to specify the dimensions of the area.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	 
	protected function getInput()
	{
		// Initialize some field attributes.
		static $identifier; 
		
		if(!isset($identifier))
		{
			if (version_compare(JVERSION, '4.0', 'lt' ) )
            {
                JHTML::_('behavior.modal');		
            }
			$identifier = 0;
		}
		$identifier++; 
        
		// Initialize JavaScript field attributes.
        if($this->allowDisable)
        {
            $params = JComponentHelper::getParams('com_arkeditor');
            $enabled = $params->get('enable_stylesheet_'.$this->element["name"],true);
        }

		$button	= 
        '<style>
            .controls #enable_stylesheet'.$this->element["name"].'.btn-group.btn-group-yesno > .btn {
                padding: 4px 12px;
            }
        </style>
		
		<br clear="left"/><br /><input type="button" class="btn'.$this->buttonClass.'" id="modal'.$identifier.'" href="index.php?option=com_arkeditor&view=typography&tmpl=component&e_id='.$this->id.'" rel="{handler: \'iframe\' , size: {x:640, y:480}}" value="Expand View"/>'.
        (!$this->allowDisable ? '' : '
        <fieldset id="enable_stylesheet'.$this->element["name"].'" class="btn-group btn-group-yesno radio">
			<input id="enable_stylesheet_'.$this->element["name"].'0" name="arkform[enable_stylesheet_'.$this->element["name"].']" value="1" '.($enabled ? 'checked="checked"' : '').' type="radio">
			<label class="btn" for="enable_stylesheet_'.$this->element["name"].'0"> Enable </label>
			<input id="enable_stylesheet_'.$this->element["name"].'1" name="arkform[enable_stylesheet_'.$this->element["name"].']" value="0" '.($enabled ? '' : 'checked="checked"').' type="radio">
			<label class="btn hasPopover" for="enable_stylesheet_'.$this->element["name"].'1" data-original-title="<strong>Warning</strong>" data-content="It is strongly recommended that you keep this option enabled. Disabling this option may lead to the lose of some features provided by the editor and may also cause some undesired effects in the display of some media objects"> Disable </label>		
		</fieldset>');
        
        $doc = JFactory::getDocument();
        $doc->addSCriptDeclaration('
        jQuery(function($)
        {
           
                $input'.$identifier.' = $("fieldset#enable_stylesheet'.$this->element["name"].' input[type=radio]:checked");
                
                if($input'.$identifier.'.is(":checked") && $input'.$identifier.'.val() == "0")
                {
                    $("#'.$this->id.'").attr("readonly", true);
                    $( "input#modal'.$identifier.'").attr("disabled",true);
                }
                else
                {
                    $("#'.$this->id.'").attr("readonly", false);
                    $( "input#modal'.$identifier.'").attr("disabled",false);
                } 

                $("fieldset#enable_stylesheet'.$this->element["name"].' input[type=radio]").on("change", function(){
  
                if(this.checked && this.value == "0")
                {
                    $("#'.$this->id.'").attr("readonly", true);
                    $( "input#modal'.$identifier.'").attr("disabled",true);
                }
                else
                {
                    $("#'.$this->id.'").attr("readonly", false);
                    $( "input#modal'.$identifier.'").attr("disabled",false);
                }
            });
        });
    
        jQuery(function($)
        {
            var $dialog = $("#modal'.$identifier.'");
            $dialog.on("click", function()
            {
               if (typeof (SqueezeBox) == "object")
               {
                    SqueezeBox.fromElement($dialog.get(0),	{ parse: \'rel\'}); 
                }
                else
                {
                  ARK.squeezeBox.fromElement($dialog.get(0), { parse: \'rel\'});
                }

            });
        });
       ');
		
		if($this->value)
			$this->value = base64_decode($this->value);
		

        $textarea = parent::getInput();
		
		
		return 	str_replace('<textarea ','<textarea style="overflow:auto; min-width:50%;height:'.$this->height.';width:'.$this->width.';" ',$textarea) . $button;
	}
}	