<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the JoomlaCK editor will only be allowed under the following conditions: http://joomlackeditor.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined('_JEXEC') or die;

/**
 *Ark inline content  System Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEvrnts.autostylesheetfilter
 */
class PlgArKEventsAutoCSSFilter extends JPlugin
{

	public $app;


	public function onInstanceCreated(&$params)
	{
		
		$filterlists = $params->get('exclude_selectors',array('{"exclude_selector":[]}'));
		$defaultfilters = $params->get('default_exclude_selectors',
			array(	
				'body',
				'.cke_',
				'__',
				'.sbox-',
				'input',
				'textarea',
				'button',
				'select',
				'form',
				'fieldset',
				'.modal-backdrop',
				'div.modal',
				'.chzn',
				'.uk-slideshow-',
				'.uk-position',
				'.fa-',
				'.fa.',
				'#jcemediabox-',
				'.jcemediabox-',
				'.pweb-',
				'.uk-accordion-',
				'div.video_',
				'div.audio_',
				'a',
				'input',
				'h1.',
				'h2.',
				'h3.',
				'h4.',
				'h5.',
				'h6.',
				'div.cke_',
				'ul.cke_',
				'.ns2-',
				'p.ns2-',
				'div.ns2-',
				'span.ns2',
				'.tm-',
				'.nss',
				'p.sp-',
				'.img-',
				'.col-',
				'.pull-',
				'.size-',
				'.input-',
				'.btn-',
				'.tab-',
				'.tabs-',
				'.items-',
				'.list-',
				'.pill-',
				'.modal-',
				'.carousel-',
				'.accordion-',
				'.progress-',
				'.bar-',
				'.popover-',
				'.control-',
				'.controls-',
				'.sidebar-',
				'.blog-',
				'.uneditable-',
				'.hidden-',
				'.pre-',
				'.google-',
				'.help-',
				'.gm-',
				'.g-',
				'#g-',
				'.item_',
				'.site_',
				'.header_',
				'.content_',
				'.post_',
				'.grid_',
				'.logo',
				'.bt',
				'.pull',
				'.nav',
				'.search',
				'.footer',
				'.offset',
				'.table',
				'.row',
				'.radio',
				'.checkbox',
				'.tag',
				'.form',
				'.width',
				'.height',
				'.container',
				'.system',
				'.dropdown',
				'.breadcrumb',
				'.module',
				'.pagination',
				'.fade',
				'.next',
				'.prev',
				'.active',
				'.clearfix',
				'.span',
				'td.span',
				'th.span',
				'i.fa-grav',
				'i.fa-gantry',
				'#form',
				'#map',
				'.sbox-',
				'.wk-',
				'.media-',
				'.hide',
				'.categor',
				'.#sbox-',
				'.wk-',
				'.media-',
				'img.pull-',
				'img.left',
				'img.right',
				'img.center',
				'.body-',
				'.uk-animation',
				'.uk-article',
				'.uk-grid',
				'.uk-width',
				'.uk-li',
				'.uk-ac',
				'.uk-ta',
				'.uk-th',
				'.uk-to',
				'.uk-b',
				'.uk-c',
				'.uk-d',
				'.uk-f',
				'.uk-m',
				'.uk-n',
				'.uk-o',
				'.uk-p',
				'.uk-r',
				'.uk-s',
				'.uk-v',
				'html.',
				'div.pull-',
				'p.pull-',
				'p.ark_',
				'.h1',
				'.h2',
				'.h3',
				'.h4',
				'.h5',
				'.h6',
				'.bg-',
				'.has-',
				'.jsn',
				'#jsn',
				'.visible-',
				'label.',
				'.page-',
				'.sr-',
				'.pb_',
				'.carousel',
				'.uk-thumbnail',
				'img.modal',
				'.ark-',
				'#ark-',
				'.ui-',
				'.mv-',
				'.mb-',
				'.mt-',
				'.mr-',
				'.ml-',
				'.pv-',
				'.ph-',
				'.pa-',
				'.pt-',
				'.pb-',
				'.pl-',
				'.pr-',
				'.fs-',
				'.fd-',
				'.eb-',
				'p.eb-',
				'div.eb-',
				'.vjs-',
				'.markitup-',
				'.wysiwyg-',
				'.textboxlist-',
				'.colorpicker-',
				'.mce-',
				'figcaption.',
				)
		);
		
		if(empty($filterlists) && empty($defaultFilters))
			return;
		
		$defaults = array();
		foreach($defaultfilters as $defaultlist)
			$defaults[] = '^'.str_replace(array('\\','.'),array('','\.'), $defaultlist);    
		
		$selectors = array();
		foreach($filterlists as $filterlist)
		{	
			$list = json_decode($filterlist,true);
			
			if(empty($list))
              continue;  
			
			for($i = 0; $i < count($list['exclude_selector']); $i++)
			{	
				$selector = $list['exclude_selector'][$i];
				if(!$selector) continue;
				$selectors[] = '^'.str_replace(array('\\','.','-'),array('','\.','\-'), $selector);
			}
		}			
			
			
		if(empty($selectors) && empty($defaults) )
			return;
	
		$selectors = array_merge($defaults,$selectors);
		
		
		return "
					editor.on( 'configLoaded', function() {
						this.config.stylesheetParser_skipSelectors = /(". implode('|',$selectors) . ")/;
					});
				";
	}
}
