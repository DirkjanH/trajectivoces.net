<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/
defined('_JEXEC') or die('Restricted access');

$link = (!$this->params->get('display_galleries_lightbox') or ($this->params->get('display_galleries_lightbox') and $this->row->_subfolders))?
	JRoute::_('index.php?option=com_fwgallery&view=gallery&id='.$this->row->id.':'.JFilterOutput :: stringURLSafe($this->row->name).'&Itemid='.JFHelper :: getItemid('gallery', $this->row->id, JRequest :: getInt('Itemid')).'#fwgallerytop')
	:
	('javascript:" rel="'.$this->row->id);

$styles = array();
if ($this->gallery_width) $styles[] = 'width:'.$this->gallery_width.'px;';
if ($this->counter == $this->row_limit) $styles[] = 'margin-right: 0;';

?>
        <div class="fwgs-item"<?php if ($styles) { ?> style="<?php echo implode('', $styles); ?>"<?php } ?>>
<?php
$styles = array();
if ($this->gallery_image_height) $styles[] = 'height:'.$this->gallery_image_height.'px;';
?>
            <div class="fwgs-image"<?php if ($styles) { ?> style="<?php echo implode('', $styles); ?>"<?php } ?>><a href="<?php echo $link; ?>"><img src="<?php echo JURI :: base(false).JFHelper :: getFileFilename($this->row, 'th'); ?>" alt="<?php echo JFHelper :: escape($this->row->name); ?>" <?php echo JFHelper :: getCenteringStyle($this->row, 'th', 0, $this->gallery_image_height); ?>/></a></div>
<?php
if ($this->params->get('display_name_gallery')) {
?>
            <div class="fwgs-name" style="background-color:#<?php echo JFHelper :: getGalleryColor($this->row->id); ?>">
                <a href="<?php echo $link; ?>"><?php echo $this->row->name; ?></a>
            </div>
<?php
}
if ($this->params->get('display_date_gallery') and $date = JFHelper::encodeDate($this->row->created)) {
?>
            <div class="fwgs-date">
                <?php echo $date; ?>
            </div>
<?php
}
if ($this->params->get('display_owner_gallery')) {
?>
            <div class="fwgs-author">
                <?php echo JText::_('FWG_BY')." ".$this->row->_user_name; ?>
            </div>
<?php
}
?>
        </div>
