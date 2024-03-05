<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2016 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;



$app = Factory::getApplication();

$app->getLanguage()->load('', JPATH_ADMINISTRATOR);
$app->getLanguage()->load('plg_editors-xtd_image', JPATH_ADMINISTRATOR);

Text::script('JFIELD_MEDIA_LAZY_LABEL');
Text::script('JFIELD_MEDIA_ALT_LABEL');
Text::script('JFIELD_MEDIA_ALT_CHECK_LABEL');
Text::script('JFIELD_MEDIA_ALT_CHECK_DESC_LABEL');
Text::script('JFIELD_MEDIA_CLASS_LABEL');
Text::script('JFIELD_MEDIA_FIGURE_CLASS_LABEL');
Text::script('JFIELD_MEDIA_FIGURE_CAPTION_LABEL');
Text::script('JFIELD_MEDIA_LAZY_LABEL');
Text::script('JFIELD_MEDIA_SUMMARY_LABEL');



$asset = $app->input->get('asset','com_content');

$name = $asset = $app->input->get('e_name');

$doc  = $app->getDocument();

$doc->getWebAssetManager()
		->registerScript('webcomponent.document-select', Uri::root().'administrator/components/com_arkeditor/views/files/js/document-select.js', [], ['type' => 'module'])
		->useScript('webcomponent.document-select');


$options = [
	'height'     => '400px',
	'width'      => '800px',
	'confirmCallback' => 'Joomla.getFile(Joomla.selectedMediaFile, \'' . $name . '\', window.parent.document.querySelector(\'ark-field-mediamore\') );Joomla.Modal.getCurrent().close();',
	'confirmText' => Text::_('Insert Link')
];

$confirm = '<button type="button" class="btn btn-success" onclick="' . $options['confirmCallback'] . '">'
		. $options['confirmText'] . ' </button>';

$link = 'index.php?option=com_media&view=media&tmpl=component&e_name=' . $name . '&asset=' . $asset . '&author=&path=ark-files:/';

?>
<style>
    iframe
    {
        width: 100%;
		height:  100%;
    }
	.modal-body 
	{
	  height: 70vh;	
	}	
</style>
<div class="modal-body" id="files">
    <iframe src="<?php echo $link;?>" height="<?php echo $options['height'];?>" width="<?php echo $options['width'];?>"></iframe>
</div>
<div class="modal-footer">
    <?php echo $confirm;?><button type="button" class="btn btn-secondary" onclick="Joomla.Modal.getCurrent().close();"><?php echo Text::_("JLIB_HTML_BEHAVIOR_CLOSE"); ?></button>
</div>
<script>
document.addEventListener('DOMContentLoaded', event => {
   Object.assign(Joomla,window.parent.Joomla);
    if(element = parent.document.querySelector('joomla-field-mediamore'))
	 {
		element.remove();
	 }
	   
	frames[0].frameElement.contentWindow.addEventListener('load', function()
	{
	  if(this.document.querySelector('button.media-toolbar-list-view span.icon-list'))
		this.document.querySelector('button.media-toolbar-list-view').click();
	});	
});
</script>