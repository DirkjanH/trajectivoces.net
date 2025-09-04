<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

JHTML :: _('behavior.framework', true);
JHTML :: script('components/com_fwgallery/assets/js/cerabox.min.js');

?>
<script type="text/javascript">
document.addEvent('domready', function() {
	document.getElements('a.fwg-lightbox').cerabox({
		titleFormat: 'Image {number} / {total} {title}'
	});
	document.getElements('.fwg-star-rating-not-logged').each(function(el) {
		el.removeEvents('click');
		el.addEvent('click', function() {
			alert('<?php echo JText :: _('FWG_VOTING_IS_AVAILABLE_FOR_REGISTERED_USERS_ONLY__PLEASE_REGISTER_', true); ?>');
		});
	});
	document.getElements('.fwg-star-rating').each(function(rating) {
		rating.getElements('.fwgallery-stars').each(function(star) {
			star.removeEvents('click');
			star.addEvent('click', function() {
				var ids = this.getProperty('rel').match(/^(\d+)_(\d+)$/);
				if (ids.length == 3) {
					new Request({
						url: '<?php echo JRoute :: _('index.php?format=raw&option=com_fwgallery&view=gallery&task=vote', false); ?>',
						onSuccess: function(html) {
							var el = document.getElement('#rating'+ids[1]);
							if (el) el.innerHTML = html;
						}
					}).send('id='+ids[1]+'&value='+ids[2]);
				}
			});
		});
	});
});
</script>