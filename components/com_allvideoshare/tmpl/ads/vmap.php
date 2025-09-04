<?php
/**
 * @version    4.2.0
 * @package    Com_AllVideoShare
 * @author     Vinoth Kumar <admin@mrvinoth.com>
 * @copyright  Copyright (c) 2012 - 2023 Vinoth Kumar. All Rights Reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined( '_JEXEC' ) or die; 

use Joomla\CMS\Uri\Uri;

echo '<?xml version="1.0" encoding="utf-8"?>' . "\n"; 
?>
<vmap:VMAP xmlns:vmap="http://www.iab.net/videosuite/vmap" version="1.0">
	<?php if ( $hasPreroll > 0 ) : ?>
		<vmap:AdBreak timeOffset="start" breakType="linear" breakId="preroll">
			<vmap:AdSource id="preroll-ad" allowMultipleAds="false" followRedirects="true">
				<vmap:AdTagURI templateType="vast3">
					<![CDATA[<?php echo URI::root(); ?>index.php?option=com_allvideoshare&view=ads&type=vast&id=<?php echo $prerollId; ?>&format=xml&lang=<?php echo $locales[4]; ?>]]>
				</vmap:AdTagURI>
			</vmap:AdSource>
		</vmap:AdBreak>
	<?php endif; ?>

	<?php if ( $hasPostroll > 0 ) : ?>
		<vmap:AdBreak timeOffset="end" breakType="linear" breakId="postroll">
			<vmap:AdSource id="postroll-ad" allowMultipleAds="false" followRedirects="true">
				<vmap:AdTagURI templateType="vast3">
					<![CDATA[<?php echo URI::root(); ?>index.php?option=com_allvideoshare&view=ads&type=vast&id=<?php echo $postrollId; ?>&format=xml&lang=<?php echo $locales[4]; ?>]]>
				</vmap:AdTagURI>
			</vmap:AdSource>
		</vmap:AdBreak>
	<?php endif; ?>
</vmap:VMAP>