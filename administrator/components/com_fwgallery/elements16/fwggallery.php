<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/
 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield');
class JFormFieldFWGGallery extends JFormField {
	var	$type = 'fwggallery';

	function getInput() {
		$list = array(
			JHTML :: _('select.option', '0', JText :: _('All'), 'id', 'treename')
		);
		$db = & JFactory::getDBO();
		$db->setQuery('SELECT id, name AS treename FROM #__fwg_projects WHERE published = 1 AND parent = 0 ORDER BY ordering');
		$list = array_merge($list, (array)$db->loadObjectList());
/*		$db->setQuery('SELECT id, name, parent FROM #__fwg_projects WHERE published=1 ORDER BY parent, ordering');
        if ($rows = $db->loadObjectList()) {
            $children = array();
            foreach ($rows as $v) {
                $pt = $v->parent;
                $buff = @$children[$pt] ? $children[$pt] : array();
                array_push($buff, $v);
                $children[$pt] = $buff;
            }
	        $levellimit = 10;
			JHTML::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_fwgallery/helpers');
            $list = array_merge($list, JHTML::_('fwGalleryCategory.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ), 0, 0));
        }*/
		return JHTML::_('select.genericlist', $list, $this->name.'[]', 'class="inputbox" multiple="multiple" size="10"', 'id', 'treename', $this->value);
	}
}
?>