<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JHTMLfwGalleryCategory {
    static function parent(&$row, $field_name = 'parent', $attr='') {
        $db = JFactory :: getDBO();

        // If a not a new item, lets set the menu item id
        $where = array();
        if ($row and !empty($row->id)) $where[] = 'id != '.(int) $row->id;

        // In case the parent was null
        if (!isset ($row->$field_name)) {
            $row->$field_name = 0;
        }
        // get a list of the menu items
        // excluding the current menu item and its child elements
        $db->setQuery('SELECT * FROM #__fwg_projects AS p '.($where?('WHERE '.implode(' AND ', $where)):'').' ORDER BY parent, name');
        $children = array ();
        if ($mitems = $db->loadObjectList()) {
            // first pass - collect children
            foreach ($mitems as $v) {
                $pt = $v->parent;
                $list = @ $children[$pt] ? $children[$pt] : array ();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        // second pass - get an indent list of the items
        $list = JHTML :: _('fwGalleryCategory.treerecurse', 0, '', array (), $children, 9999, 0, 0);
        // assemble menu items to the array
        $mitems = array ();
        $mitems[] = JHTML :: _('select.option', '0', JText :: _('FWG_TOP'));
        foreach ($list as $item) {
            $mitems[] = JHTML :: _('select.option', $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
        }

        return JHTML :: _('fwGalleryCategory.genericlist', $mitems, $field_name, 'class="inputbox" '.$attr, 'value', 'text', $row->$field_name);
    }

	static function options( $arr, $key = 'value', $text = 'text', $selected = null, $translate = false )
	{
		$html = '';

		foreach ($arr as $i => $option)
		{
			$element =& $arr[$i]; // since current doesn't return a reference, need to do this

			$isArray = is_array( $element );
			$extra	 = '';
			if ($isArray)
			{
				$k 		= $element[$key];
				$t	 	= $element[$text];
				$id 	= ( isset( $element['id'] ) ? $element['id'] : null );
				if(isset($element['disable']) && $element['disable']) {
					$extra .= ' disabled="disabled"';
				}
			}
			else
			{
				$k 		= $element->$key;
				$t	 	= $element->$text;
				$id 	= ( isset( $element->id ) ? $element->id : null );
				if(isset( $element->disable ) && $element->disable) {
					$extra .= ' disabled="disabled"';
				}
			}

			// This is real dirty, open to suggestions,
			// barring doing a propper object to handle it
			if ($k === '<OPTGROUP>') {
				$html .= '<optgroup label="' . $t . '">';
			} else if ($k === '</OPTGROUP>') {
				$html .= '</optgroup>';
			}
			else
			{
				//if no string after hypen - take hypen out
				$splitText = explode( ' - ', $t, 2 );
				$t = $splitText[0];
				if(isset($splitText[1])){ $t .= ' - '. $splitText[1]; }

				//$extra = '';
				//$extra .= $id ? ' id="' . $arr[$i]->id . '"' : '';
				if (is_array( $selected ))
				{
					foreach ($selected as $val)
					{
						$k2 = is_object( $val ) ? $val->$key : $val;
						if ($k == $k2)
						{
							$extra .= ' selected="selected"';
							break;
						}
					}
				} else {
					$extra .= ( (string)$k == (string)$selected  ? ' selected="selected"' : '' );
				}

				//if flag translate text
				if ($translate) {
					$t = JText::_( $t );
				}

				// ensure ampersands are encoded
				$k = JFilterOutput::ampReplace($k);
				$t = JFilterOutput::ampReplace($t);

				$html .= '<option value="'. $k .'" '. $extra .'>' . $t . '</option>';
			}
		}

		return $html;
	}

	static function genericlist( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false )
	{
		if ( is_array( $arr ) ) {
			reset( $arr );
		}

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		 }

		$id = $name;

		if ( $idtag ) {
			$id = $idtag;
		}

		$id		= str_replace('[','',$id);
		$id		= str_replace(']','',$id);

		$html	= '<select name="'. $name .'" id="'. $id .'" '. $attribs .'>';
		$html	.= JHTMLfwGalleryCategory::Options( $arr, $key, $text, $selected, $translate );
		$html	.= '</select>';

		return $html;
	}

    static function getCategories($field_name, $product_categories = array(), $attr='SIZE="10"', $multiple=true, $first_option_name = '', $select_first=false, $has_images = false) {
        $db = JFactory :: getDBO();
        $where = array();
        if ($has_images) {
        	$user = JFactory :: getUser();
        	$where[] = 'EXISTS(SELECT * FROM #__fwg_files AS f WHERE f.project_id = p.id AND f.user_id = '.$user->id.')';
        }
        $db->setQuery('SELECT id, name, parent FROM #__fwg_projects AS p '.($where?('WHERE '.implode(' AND ', $where)):'').' ORDER BY parent, name');
        $children = array ();
        if ($mitems = $db->loadObjectList()) {
            // first pass - collect children
            foreach ($mitems as $v) {
                $pt = $v->parent;
                $list = @ $children[$pt] ? $children[$pt] : array ();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        // second pass - get an indent list of the items
        $list = JHTML :: _('fwGalleryCategory.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        // assemble menu items to the array
        $text = '<select name="'.$field_name.'" '.($multiple?'multiple ':'').'class="inputbox" '.$attr.'>';
        if ($first_option_name) $text .= '<option value="">'.$first_option_name.'</option>';

		$selected = !$select_first;
		if (!$selected and $product_categories) {
	        foreach ($list as $item) {
		        if (is_array($product_categories)) {
					foreach ($product_categories as $pc) {
						if ($pc->id == $item->id) {
							$selected = true;
							break 2;
						}
					}
		        } elseif ($product_categories == $item->id) {
					$selected = true;
					break;
		        }
	        }
		}

        foreach ($list as $item) {
	        $text .= '<option value="'.$item->id.'"';
	        if (!$selected) {
	        	$text .= ' selected';
	        	$selected = true;
	        }
	        if (is_array($product_categories)) {
				foreach ($product_categories as $pc) {
					if ($pc->id == $item->id) {
						$text .= ' selected';
						break;
					}
				}
	        } elseif ($product_categories == $item->id) {
				$text .= ' selected';
	        }
			$text .= '>&nbsp;&nbsp;&nbsp;'.$item->treename.'</option>';
        }
        $text .= '</select>';

        return $text;
    }
	static function treerecurse( $id, $indent, $list, &$children, $maxlevel=9999, $level=0, $type=1 )
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;

				if ( $type ) {
					$pre 	= '<sup>|_</sup>&nbsp;';
					$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				} else {
					$pre 	= '- ';
					$spacer = '&nbsp;&nbsp;';
				}

				if ( $v->parent == 0 ) {
					$txt 	= $v->name;
				} else {
					$txt 	= $pre . $v->name;
				}
				$pt = $v->parent;
				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count( @$children[$id] );
				$list = JHTMLfwGalleryCategory::treerecurse( $id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type );
			}
		}
		return $list;
	}
}
?>