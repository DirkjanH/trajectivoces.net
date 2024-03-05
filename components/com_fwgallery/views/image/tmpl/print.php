<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');
?>
<div id="fwgallery">
	<a name="fwgallerytop"></a>
<?php
if ($this->params->get('display_name_image')) {
?>
	<div class="fwgi-name"><?php echo $this->row->name; ?></div>
<?php
}
?>
    <div class="fwgi-header">
        <div class="fwgi-header-total">
<?php
if ($this->params->get('display_date_image') and $date = JFHelper::encodeDate($this->row->created)) {
?>
	            <div class="fwgi-stats-date"><?php echo $date; ?></div>
<?php
}
if ($this->params->get('display_image_views')) {
?>
			<div class="fwgi-stats-views">
				<?php echo JText :: _('FWG_VIEWS') ?>: <?php echo $this->row->hits; ?>
			</div>
<?php
}
if ($this->params->get('use_voting')) {
?>
			<div class="fwgi-stats-vote" id="rating<?php echo $this->row->id; ?>">
				<?php include(dirname(__FILE__).'/../../gallery/tmpl/default_vote.php'); ?>
			</div>
<?php
}
?>
			<div class="fwgi-stats-print">
				<a href="javascript:window.print();"><img src="<?php echo JURI :: root(true); ?>/components/com_fwgallery/assets/images/print.png" /></a>
			</div>
			<div class="clr"></div>
        </div>
        <div class="clr"></div>
    </div>

	<div class="fwgi-image">
		<div class="fwgi-image-picture fwgi-image-<?php echo $this->row->_plugin_name; ?>">
<?php
if ($this->plugin_output) {
	echo $this->plugin_output;
} else {
?>
	        <img src="<?php echo JURI::root(true).'/'.JFHelper::getFileFilename($this->row); ?>" alt="<?php echo JFHelper :: escape($this->row->name); ?>" />
<?php
}
if ($new_days = (int)$this->params->get('new_days')) {
	$date_diff = floor((mktime() - strtotime($this->row->created))/86400);
	if ($date_diff <= $new_days) {
?>
			    <div class="fwgi-image-new"></div>
<?php
	}
}
?>
		</div>
<?php
	if ($this->row->copyright and $this->params->get('display_user_copyright')) {
?>
					<div class="fwgi-image-copyright"><?php echo $this->row->copyright; ?></div>
<?php
	}
	if ($this->row->descr) {
?>
					<?php echo JText::_('FWG_DESCRIPTION').': '.nl2br($this->row->descr); ?>
<?php
	}
	if ($this->comments) {
?>
					<div class="fwgi-image-comments"><?php echo $this->comments; ?></div>
<?php
	}
?>
	</div>

</div>
<?php
$params = JComponentHelper :: getParams('com_fwgallery');
if (!$params->get('hide_fw_copyright')) {
?>
<div id="fwcopy" style="display:block;visibility:visible;text-align:center;font-size:10px;padding:20px 0;">
	<?php echo JText::_("FWG_POWERED_BY"); ?> <a href="http://fastw3b.net/fwgallery.html" target="_blank"><?php echo JText::_("FWG_FW_GALLERY"); ?></a>
</div>
<?php
}
?>
