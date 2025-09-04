<?php
/*------------------------------------------------------------------------
# mod_socialfacebookwidget - Social Facebook Widget
# ------------------------------------------------------------------------
# @author - Social Facebook Widgets
# copyright - All rights reserved by Social FB Widgets
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://socialfbwidgets.com/
# Technical Support:  alex@socialfbwidgets.com
-------------------------------------------------------------------------*/
// no direct access

defined('_JEXEC') or die;
  $document = JFactory::getDocument();
  $name=$params->get('facebook_name');
  $url=$params->get('facebook_url');
  $tab=$params->get('tabs');
  $width=$params->get('facebook_width');
  $height=$params->get('facebook_height');
  $facepile=$params->get('face');
  $cover=$params->get('hide_cover');
  $header=$params->get('small_header');
  $button=$params->get('hide_button');
  $adapt=$params->get('adapt_container_width');
?>
<div class="mod_facebook_page <?php echo $moduleclass_sfx;?>">
   <div class="fb-page"
        data-href="<?php echo $url;?>"
        data-tabs="<?php echo $tab;?>"
        data-width="<?php echo $width;?>"
        data-height="<?php echo $height;?>"
        data-small-header="<?php echo $header;?>"
        data-adapt-container-width="<?php echo $adapt;?>"
        data-hide-cover="<?php echo $cover;?>"
        data-hide-cta="<?php echo $button;?>"
        data-show-facepile="<?php echo $facepile;?>">
        <div class="fb-xfbml-parse-ignore">
          <blockquote cite="<?php echo $url;?>">
            <a href="<?php echo $url;?>"><?php echo $name;?></a>
          </blockquote>
        </div>
      </div>
</div>
 <div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=262562957268319";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
