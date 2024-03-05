<?php
/**
 * FW Gallery 2.3.2
 * @copyright (C) 2013 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );


$id = JRequest :: getString('id');

JHTML :: _('behavior.framework', true);
JHTML :: script('components/com_fwgallery/assets/js/cerabox.min.js');

?>
<div id="fwgallery" class="fw-gallery">
	<a name="fwgallerytop"></a>
<?php
if (!$this->params->get('hide_iphone_app_promo') and JFHelper :: detectIphone()) {
?>
	<div class="fwg-iphone-promo"><img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/iPhoneAppStore_transp_mini.png" /></div>
<?php
}
?>
	<div class="fwg-title"><?php echo $this->obj->name; ?></div>
	<form action="<?php echo JRoute :: _('index.php?option=com_fwgallery&view=tag&Itemid='.JRequest :: getInt('Itemid').($id?('&id='.$id):'')); ?>" method="post">
<?php
if ($this->list) {
	$this->row_limit = $this->params->get('images_a_row', 3);
	$this->image_width = $this->params->get('image_box_width');
	$this->image_height = $this->params->get('im_mid_h');
	if (!$this->image_width) $this->image_width = $this->params->get('im_mid_w');
?>
	    <div class="fwgs-header">
<?php
if ($this->params->get('display_gallery_sorting')) {
?>
			<div class="fwgs-header-ordering">
				<?php echo JText :: _('FWG_ORDER_BY'); ?>: <?php echo JHTML :: _('select.genericlist', array(
					JHTML :: _('select.option', 'name', JText :: _('FWG_ALPHABETICALLY'), 'id', 'name'),
					JHTML :: _('select.option', 'new', JText :: _('FWG_NEWEST_FIRST'), 'id', 'name'),
					JHTML :: _('select.option', 'old', JText :: _('FWG_OLDEST_FIRST'), 'id', 'name'),
					JHTML :: _('select.option', 'order', JText :: _('FWG_ORDERING'), 'id', 'name'),
					JHTML :: _('select.option', 'voting', JText :: _('FWG_VOTING'), 'id', 'name')
				), 'order', 'onchange="this.form.submit();"', 'id', 'name', $this->order); ?>
			</div>
<?php
}
if ($this->params->get('display_gallery_limit_selector')) {
?>
	        <div class="fwgs-header-pagination">
	        	<?php echo JText::_('FWG_DISPLAY_LIMIT'); ?>: <?php echo $this->pagination->getLimitBox(); ?>
	        </div>
<?php
}
?>
	        <div class="clr"></div>
	    </div>
		<div class="fwg-images">
<?php
	$this->new_days = $this->params->get('new_days');
	$this->counter = 1;
    foreach ($this->list as $row) {
    	$this->row = $row;
		$styles = array();
		if ($this->image_width) $styles[] = 'width:'.$this->image_width.'px;';
		if ($this->counter == $this->row_limit) $styles[] = 'margin-right: 0;';
?>
		<div class="fwg-item"<?php if ($styles) { ?> style="<?php echo implode('', $styles); ?>"<?php } ?>>
<?php
			include(dirname(__FILE__).'/../../gallery/tmpl/default_item.php');
?>
		</div>
<?php
        if ($this->counter >= $this->row_limit) {
?>
        	<div class="clr"></div>
<?php
        	$this->counter = 1;
        } else $this->counter++;
    }
	if ($this->counter > 1) {
?>
	<div class="clr"></div>
<?php
	}
?>
    	</div>
        <div class="fwgs-footer-pagination pagination">
        	<?php echo $this->pagination->getPagesLinks(); ?>
        </div>
<?php
	if ($this->params->get('display_galleries_lightbox')) {
?>
<script type="text/javascript">
window.addEvent('domready', function() {
	document.getElements('.fwgs-image a').each(function(el) {
		el.addEvent('click', function() {
			var id = document.getElement(this).getProperty('rel');
			if (id) {
				if (document.getElement('#fwg-preview-gallery')) document.getElement('#fwg-preview-gallery').dispose();
				if (document.getElement('#fwg-lightbox')) document.getElement('#fwg-lightbox').dispose();
				if (document.getElement('#fwg-lightboxOverallView')) document.getElement('#fwg-lightboxOverallView').dispose();
				if (document.getElement('#fwg-lightboxIndicator')) document.getElement('#fwg-lightboxIndicator').dispose();

				new Request.JSON({
					url: '<?php echo JRoute :: _('index.php?option=com_fwgallery&view=tag', false); ?>',
					data: {
						id: id,
						format: 'json'
					},
					onSuccess: function(data) {
						var div = new Element('div', {
							'id': 'fwg-preview-gallery',
							'style': 'display: none'
						});
						div.inject(document.getElement('body'));
						for (var i in data.images) {
							var link = new Element('a', {
								'class':'fwg-lightbox',
								'href': data.images[i].link,
								'rel':'fwg-lightbox-gallery-link'
							});
							link.inject(div);
							var image = new Element('img', {
								alt: data.images[i].descr
							});
							image.inject(link);
						}
						var cerabox = new CeraBox(div.getElements('a.fwg-lightbox'), {
							titleFormat: 'Image {number} / {total} {title}'
						});
						div.getElement('a.fwg-lightbox').fireEvent('click');
					}
				}).send();
			}
		});
	});
});
</script>
<?php
	}
	if (!defined('FWGALLERY_LIGHTBOX_LOADED')) {
		define('FWGALLERY_LIGHTBOX_LOADED', true);
		include(dirname(__FILE__).'/../../gallery/tmpl/default_scripts.php');
	}
} else {
    echo JText::_('FWG_NO_IMAGES_IN_THIS_TAG');
}
?>
	</form>
</div>
<?php
if (!$this->params->get('hide_fw_copyright')) {
?>
<div id="fwcopy" style="display:block;visibility:visible;text-align:center;font-size:10px;padding:20px 0;">
	<?php echo JText::_("FWG_POWERED_BY"); ?> <a href="http://fastw3b.net/fwgallery.html" target="_blank"><?php echo JText::_("FWG_FW_GALLERY"); ?></a>
</div>
<?php
}
?>
