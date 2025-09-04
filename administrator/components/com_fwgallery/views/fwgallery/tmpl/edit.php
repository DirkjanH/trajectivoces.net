<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined('_JEXEC') or die('Restricted access');

JHTML :: _('behavior.framework', true);
JHTML :: _('behavior.formvalidation');
JHTML :: script('administrator/components/com_fwgallery/assets/js/moorainbow.1.2b2.js');

JToolBarHelper::title(JText::_('FWG_EDIT_GALLERY'), 'fwgallery-galleries.png');
JToolBarHelper::apply();
JToolBarHelper::save();
JToolBarHelper::cancel('projects', JText :: _($this->obj->id?'Close':'Cancel'));

$editor = JFactory::getEditor();
$color = JFHelper :: getGalleryColor($this->obj->id);
?>
<form action="index.php?option=com_fwgallery&view=fwgallery" method="post" name="adminForm" id="adminForm" class="form-validate">
    <fieldset class="adminform">
        <legend><?php echo JText::_('FWG_DETAILS'); ?></legend>
        <table class="admintable">
            <tr>
                <td class="key">
                    <?php echo JText::_('FWG_GALLERY_NAME'); ?><span class="required">*</span> :
                </td>
                <td>
                    <input class="inputbox required" type="text" name="name" size="50" maxlength="100" value="<?php echo $this->escape($this->obj->name);?>" />
                </td>
            </tr>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_PARENT_GALLERY'); ?>:
	            </td>
	            <td>
	                <?php echo JHTML :: _('fwGalleryCategory.parent', $this->obj); ?>
	            </td>
	        </tr>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_DATE'); ?>:
	            </td>
	            <td>
	                <?php echo JHTML::_('calendar', substr($this->obj->created?$this->obj->created:date('Y-m-d'), 0, 10), 'created', 'created', '%Y-%m-%d', array('class'=>'inputbox', 'size'=>'25',  'maxlength'=>'19')); ?>
	            </td>
	        </tr>
            <tr>
                <td class="key">
                    <?php echo JText::_('FWG_USER'); ?>:
                </td>
                <td>
                    <?php echo JHTML::_('select.genericlist', (array)$this->clients, 'user_id', '', 'id', 'name', $this->obj->user_id?$this->obj->user_id:$this->user->id); ?>
                </td>
            </tr>
	        <tr>
	            <td class="key">
	                <?php echo JText::_('FWG_COLOR'); ?>:
	            </td>
	            <td id="color-row"<?php if ($color) { ?> style="background-color:#<?php echo $color; ?>"<?php } ?>>
					<img id="myRainbow" src="<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/rainbow.png" alt="[r]" width="16" height="16" />
					<input id="color" name="color" type="text" size="13" value="<?php echo $color; ?>" />
					<button type="button" id="myRainbowButton"><?php echo JText :: _('FWG_SELECT'); ?></button>
					<button type="button" id="myRainbowClearButton"><?php echo JText :: _('FWG_CLEAR'); ?></button>
	            </td>
	        </tr>
            <tr>
                <td class="key">
                    <?php echo JText::_('FWG_VIEW_ACCESS'); ?>:
                </td>
                <td>
                    <?php echo JHTML::_('select.genericlist', (array)$this->groups, 'gid', 'size="10"', 'id', 'name', $this->obj->gid?$this->obj->gid:($this->groups?$this->groups[0]->id:29)); ?>
                </td>
            </tr>
<?php
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper :: importPlugin('fwgallery');
		$dispatcher->trigger('getAdminGalleryForm', array($this->obj));
?>
            <tr>
                <td class="key">
                    <?php echo JText::_('FWG_PUBLISHED'); ?>:
                </td>
                <td>
					<fieldset class="radio btn-group">
	                	<?php echo JHTML :: _('select.booleanlist', 'published', '', $this->obj->published); ?>
					</fieldset>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <?php echo JText::_('FWG_DESCRIPTION'); ?>:
                </td>
                <td>
                    <?php echo $editor->display('descr',  $this->obj->descr, '100%', '350', '75', '20', false); ?>
                </td>
            </tr>
        </table>
    </fieldset>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->obj->id; ?>" />
</form>
<span class="required">*</span> - <?php echo JText :: _('FWG_REQUIRED_FIELDS'); ?>
<script type="text/javascript">
var fwgRainbow;
window.addEvent('domready', function() {
	$('myRainbowButton').addEvent('click', function(ev) {
		$('myRainbow').fireEvent('click', ev);
	});
	$('myRainbowClearButton').addEvent('click', function(ev) {
		$('color').value = '';
		$('color-row').setStyle('background-color', '');
	});
	$('color').addEvent('keyup', function() {
		if (this.value.match(/^#[0-9a-fA-F]{3,6}$/)) {
			$('color-row').setStyle('background-color', this.value);
			fwgRainbow.manualSet(this.value, 'hex');
		} else {
			$('color-row').setStyle('background-color', '');
		}
	});
	fwgRainbow = new MooRainbow('myRainbow', {
		wheel: true,
		imgPath: '<?php echo JURI :: root(true); ?>/administrator/components/com_fwgallery/assets/images/',
		onChange: function(color) {
			$('color').value = color.hex;
			$('color-row').setStyle('background-color', color.hex);
		},
		onComplete: function(color) {
			$('color').value = color.hex;
			$('color-row').setStyle('background-color', color.hex);
		}
	});
<?php
if ($color) {
?>
	fwgRainbow.manualSet('<?php echo $color; ?>', 'hex');
<?php
}
?>
});
function submitbutton(task) {
	var form = $('adminForm');
	if ((task == 'apply' || task == 'save') && !document.formvalidator.isValid(form)) {
		alert('<?php echo JText :: _('FWG_NOT_ALL_REQUIRED_FIELDS_ARE_FILLED', true); ?>');
	} else {
		form.task.value = task;
		form.submit();
	}
}
</script>