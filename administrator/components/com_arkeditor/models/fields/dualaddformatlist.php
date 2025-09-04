<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die;
defined('JPATH_BASE') or die;

require_once "dualaddselectlist.php";

class JFormFieldDualAddFormatList extends JFormFieldDualAddSelectList
{
	protected $type = 'DualAddFormatList';
	
	public static $ElementAddSelectListJSWritten = FALSE;
	
	protected function getInput()
	{
		if (!static::$ElementAddSelectListJSWritten) 
		{
			$jsFile = dirname(__FILE__) . "/addselectlist.js";
			$jsUrl = str_replace(JPATH_ROOT, JURI::root(true), $jsFile);
			$jsUrl = str_replace(DIRECTORY_SEPARATOR, "/", $jsUrl);

			$document = JFactory::getDocument();
			$document->addScript( $jsUrl );

			static::$ElementAddSelectListJSWritten = TRUE;
		}

		// Initialize variables.
		$list = array();
		$attr = '';

		$name = $this->name;
        $id = $this->id;

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : ' size="12"';
		$attr .= ' multiple="multiple"';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : ' onchange="updateSelectList(this,document.getElementById(\''.$id.'\'));updateOption(this,document.adminForm.'.$id.'_inputtext);updateOption(document.getElementById(\''.$id.'\'),document.adminForm.'.$id.'_inputvalue);"' ;

		$attr .= ' style="width:150px"';

		// Get the field options.
		$values = (array) $this->getOptions();
		$elementName = (string) $this->element['name'];
		$listName = str_replace($elementName,$elementName.'_text',$this->name);
		$lists[] = JHtml::_('select.genericlist', $this->_texts, $listName.'[]', trim($attr.$onchange), 'value', 'text', '', $this->id.'_text');

		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : ' onchange="updateSelectList(this,document.getElementById(\''.$id.'_text\'));updateOption(this,document.adminForm.'.$id.'_inputvalue);updateOption(document.getElementById(\''.$id.'_text\'),document.adminForm.'.$id.'_inputtext);"' ;

		$lists[] = JHtml::_('select.genericlist', $values, $this->name.'[]', trim($attr.$onchange), 'value', 'text', '', $this->id);

		return 
		'<table border="0" cellpadding="10" cellspacing="0" width="250">
		<tr>
			<td>Default Text<br/>
				<input type="'.$id.'_inputtext" id="input" name="'.$id.'_inputtext" style="min-width:150px;"/>
			</td>
			<td>Element Name<br/>
				<input type="'.$id.'_inputvalue" id="input" name="'.$id.'_inputvalue" style="min-width:150px;"/>
			</td>
			<td valign="bottom">
				<input type="button" class="btn" value="Add" onclick="addToList(document.getElementById(\''.$id.'_text\'),document.adminForm.'.$id.'_inputtext.value,document.adminForm.'.$id.'_inputtext.value);addToList(document.getElementById(\''.$id.'\'),document.adminForm.'.$id.'_inputvalue.value,document.adminForm.'.$id.'_inputvalue.value);" />
			</td>
		</tr>	
		<tr>
			<td>
				'.$lists[0].'		
			</td>
			<td>
				'.$lists[1].'		
			</td>
			<td valign="top">
				<input type="button" class="btn" value="Modify" onclick="modifyList(document.getElementById(\''.$id.'_text\'),document.adminForm.'.$id.'_inputtext.value,document.adminForm.'.$id.'_inputtext.value);modifyList(document.getElementById(\''.$id.'\'),document.adminForm.'.$id.'_inputvalue.value,document.adminForm.'.$id.'_inputvalue.value);" />
				<br /><br />
				<input type="button" class="btn" value="Up" onclick="moveUpList(document.getElementById(\''.$id.'_text\')),moveUpList(document.getElementById(\''.$id.'\'))" />
				<br /><br />
				<input type="button" class="btn" value="Down" onclick="moveDownList(document.getElementById(\''.$id.'_text\'));moveDownList(document.getElementById(\''.$id.'\'))" />
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input type="button" class="btn" value="Delete" onclick="removeFromList(document.getElementById(\''.$id.'_text\'));removeFromList(document.getElementById(\''.$id.'\'));" />
			</td>
		</tr>
		</table>
        <script language="javascript">
 			// Unload bootstrap select list
			if( jQuery )
			{
				jQuery(document).ready(function()
				{
					jQuery( "#'.$id.'_chzn" ).remove();
					jQuery( "#'.$id.'" ).css( "display", "block" );
					jQuery( "#'.$id.'" ).css( "width", "215px" );
					jQuery( "#'.$id.'_text_chzn" ).remove();
					jQuery( "#'.$id.'_text" ).css( "display", "block" );
					jQuery( "#'.$id.'_text" ).css( "width", "215px" );
				});
			}

           document.adminForm.addEvent("submit", function()
            {
                var oSelect = document.id("'.$id.'");
               oOptions = oSelect.getElements("option");
        
                if(oOptions)
                {
                    $$(oOptions).each(function(elem)
                    {
                          elem.setAttribute( "selected", "selected" );
                          elem.selected = true;
                     });
                }
				
			   var oSelect = document.id("'.$id.'_text");
               oOptions = oSelect.getElements("option");
        
                if(oOptions)
                {
                    $$(oOptions).each(function(elem)
                    {
                          elem.setAttribute( "selected", "selected" );
                          elem.selected = true;
                     });
                }
				
                return true;
           });
        </script>';
	}
}