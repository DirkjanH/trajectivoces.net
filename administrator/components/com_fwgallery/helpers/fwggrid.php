<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JHTMLfwgGrid {
    static function selected(&$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='') {
        $img    = $row->selected ? $imgY : $imgX;
        $task   = $row->selected ? 'unselect' : 'select';
        $alt    = JText::_($row->selected ?'selected':'Unselected');
        $action = JText::_($row->selected ?'Unselect Item':'Select item');

        $href = '
        <a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
        <img src="'.JURI :: root(true).'/administrator/components/com_fwgallery/assets/images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
        ;

        return $href;
    }
	static function published( &$row, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix='' )
	{
		$img 	= $row->published ? $imgY : $imgX;
		$task 	= $row->published ? 'unpublish' : 'publish';
		$alt 	= $row->published ? JText::_( 'Published' ) : JText::_( 'Unpublished' );
		$action = $row->published ? JText::_( 'Unpublish Item' ) : JText::_( 'Publish item' );

		$href = '
		<a href="javascript:void(0);" onclick="return listItemTask(\'cb'. $i .'\',\''. $prefix.$task .'\')" title="'. $action .'">
		<img src="'.JURI :: root(true).'/administrator/components/com_fwgallery/assets/images/'. $img .'" border="0" alt="'. $alt .'" /></a>'
		;

		return $href;
	}
}
?>