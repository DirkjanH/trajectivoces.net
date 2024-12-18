<?php

/**
 * @package JCE MediaBox
 * @copyright Copyright (C) 2006 - 2023 Ryan Demmer. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL 3, see LICENCE
 * 
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 * Light Theme inspired by Slimbox by Christophe Beyls
 * @ http://www.digitalia.be
 *
 * Shadow Theme inspired by ShadowBox
 * @ http://mjijackson.com/shadowbox/
 *
 * Squeeze theme inspired by Squeezebox by Harald Kirschner
 * @ http://digitarald.de/project/squeezebox/
 *
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
 * JCE MediaBox Plugin
 *
 * @package         JCE MediaBox
 * @subpackage    System
 */
class plgSystemJCEMediabox extends CMSPlugin
{
    protected $version = '2.1.7';
    
    /**
     * Create a list of translated labels for popup window
     * @return Key : Value labels string
     */
    protected function getLabels()
    {
        $this->loadLanguage('plg_system_jcemediabox', JPATH_ADMINISTRATOR);
        $this->loadLanguage('plg_system_jcemediabox', __DIR__);

        $words = array('close', 'next', 'previous', 'cancel', 'numbers', 'numbers_count', 'download');

        $v = array();

        foreach ($words as $word) {
            $v[$word] = htmlspecialchars(Text::_('PLG_SYSTEM_JCEMEDIABOX_LABEL_' . strtoupper($word)));
        }

        return $v;
    }

    private function getAssetPath($relative)
    {
        $hash = '?' . md5($this->version);

        return Uri::base(true) . '/media/plg_system_jcemediabox/' . $relative . $hash;
    }

    /**
     * OnAfterRoute function
     * @return Boolean true
     */
    public function onAfterDispatch()
    {
        $app = Factory::getApplication();

        // only in "site"
        if ($app->getClientId() !== 0) {
            return;
        }

        $document = Factory::getDocument();
        $docType = $document->getType();

        // only in html pages
        if ($docType != 'html') {
            return;
        }

        $db = Factory::getDBO();

        // Causes issue in Safari??
        $pop = $app->input->getInt('pop');
        $print = $app->input->getInt('print');
        $task = $app->input->getCmd('task');
        $tmpl = $app->input->getWord('tmpl');

        // don't load mediabox on certain pages
        if ($pop || $task == 'new' || $task == 'edit') {
            return;
        }

        // load in print
        if ($tmpl == 'component' && !$print) {
            return;
        }

        $params = $this->params;

        $components = $params->get('components');

        if (!empty($components)) {
            if (is_string($components)) {
                $components = explode(',', $components);
            }

            $option = $app->input->get('option', '');

            foreach ($components as $component) {
                if ($option === 'com_' . $component || $option === $component) {
                    return;
                }
            }
        }

        // get active menu
        $menus = $app->getMenu();
        $menu = $menus->getActive();

        // get menu items from parameter
        $menuitems = (array) $params->get('menu');

        // is there a menu assignment?
        if (!empty($menuitems) && !empty($menuitems[0])) {
            if ($menu && !in_array($menu->id, (array) $menuitems)) {
                return;
            }
        }

        // get excluded menu items from parameter
        $menuitems_exclude = (array) $params->get('menu_exclude');

        // is there a menu exclusion?
        if (!empty($menuitems_exclude) && !empty($menuitems_exclude[0])) {
            if ($menu && in_array($menu->id, (array) $menuitems_exclude)) {
                return;
            }
        }

        $theme = $params->get('theme', 'standard');

        if ($params->get('dynamic_themes', 0)) {
            $theme = $app->input->getWord('theme', $theme);
        }

        $config = array(
            'base' => Uri::base(true) . '/',
            'theme' => $theme,
            //'mediafallback' => (int) $params->get('mediafallback', 0),
            //'mediaselector' => $params->get('mediaselector', 'audio,video'),
            'width' => $params->get('width', ''),
            'height' => $params->get('height', ''),
            'lightbox' => (int) $params->get('lightbox', 0),
            'shadowbox' => (int) $params->get('shadowbox', 0),
            'icons' => (int) $params->get('icons', 1),
            'overlay' => (int) $params->get('overlay', 1),
            'overlay_opacity' => (float) $params->get('overlayopacity'),
            'overlay_color' => $params->get('overlaycolor', ''),
            'transition_speed' => (int) $params->get('transition_speed', $params->get('scalespeed', 300)),
            'close' => (int) $params->get('close', 2),
            'scrolling' => (string) $params->get('scrolling', 'fixed'),
            'labels' => $this->getLabels(),
            'swipe' => (bool) $params->get('swipe', 1)
        );

        if ($this->params->get('jquery', 1)) {
            // Include jQuery
            HTMLHelper::_('jquery.framework');
        }

        $document->addScript($this->getAssetPath('js/jcemediabox.min.js'));
        $document->addStyleSheet($this->getAssetPath('css/jcemediabox.min.css'));

        $document->addScriptDeclaration('jQuery(document).ready(function(){WfMediabox.init(' . json_encode($config) . ');});');
    }
}
