/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */
(function(){CKEDITOR.plugins.add("uiheader",{init:function(n){var t=n.config.headerButtons_states||!1,i=" ckh_disabled",r=" ckh_disabled";n.on("uiSpace",function(u){var e,o,v,f;if(u.data.space==n.config.toolbarLocation||u.data.space=="image"){if(t){e=!0;for(o in t)if(t[o]=="cke_show"){e=!1;break}if(e)return}this.elementMode!=CKEDITOR.ELEMENT_MODE_INLINE&&u.removeListener();var s=t&&t.show_source||"cke_show",h=t&&t.show_design||"cke_show",l=t&&t.show_new||"cke_show",a=t&&t.show_close||"cke_show",c=t&&t.show_blocks||"cke_show";n.elementMode==CKEDITOR.ELEMENT_MODE_INLINE&&(v=n.element.getAttribute("data-type"),s="cke_hide",h="cke_hide",c="cke_hide",i="",r="");f=CKEDITOR.env.ie?'onclick="return false;" onmouseup':"onclick";u.data.html='<div id="cke_top_outer"><div id="inner"><div class="cke_icons"  onmousedown="return false;"><a class="ckh_left '+(t&&t.show_versions||"cke_show")+'" id="ckh_versions" '+f+'="CKEDITOR.instances.'+n.name+'.execCommand(\'versions\');"><span class="ckh_icon ckh_icon_versions"><\/span><span class="label">Versions<\/span><\/a><a class="ckh_left '+l+r+'" id="ckh_new"><span class="ckh_icon ckh_icon_new"><\/span><span class="label">New<\/span><\/a><a class="ckh_left '+(t&&t.show_save||"cke_show")+'" id="ckh_save" '+f+'="CKEDITOR.instances.'+n.name+'.execCommand(\'savecontent\');return false"><span class="ckh_icon ckh_icon_save"><\/span><span class="label">Save<\/span><\/a><a class="ckh_left '+(t&&t.show_undo||"cke_show")+' ckh_disabled" id="ckh_undo"  '+f+'="CKEDITOR.instances.'+n.name+".focus(); CKEDITOR.instances."+n.name+'.execCommand(\'undo\');return false;"><span class="ckh_icon ckh_icon_undo"><\/span><span class="label">Undo<\/span><\/a><a class="ckh_left '+(t&&t.show_redo||"cke_show")+' ckh_disabled" id="ckh_redo" '+f+'="CKEDITOR.instances.'+n.name+".focus();CKEDITOR.instances."+n.name+'.execCommand(\'redo\');return false;"><span class="ckh_icon ckh_icon_redo"><\/span><span class="label">Redo<\/span><\/a><a class="ckh_right '+a+i+'" id="ckh_close" href="javascript:void(0)" onclick=""><span class="ckh_icon ckh_icon_close"><\/span><span class="label">Close<\/span><\/a><a class="ckh_right '+(t&&t.show_find||"cke_show")+'" id="ckh_find" href="javascript:void(0)" '+f+'="CKEDITOR.instances.'+n.name+'.execCommand(\'find\'); return false;"><span class="ckh_icon ckh_icon_find"><\/span><span class="label">Find<\/span><\/a><div class="clearfix"><\/div><\/div><div class="cke_buttons" onmousedown="return false;"><button class="'+s+' ckh_tab" id="cke_source">Source<\/button><button class="'+h+' ckh_tab cke_active" id="cke_design">Design<\/button><button class="'+c+' ckh_tab" id="cke_blocks">Elements<\/button><\/div><\/div><\/div>'+u.data.html}});n.on("uiReady",function(){var n=CKEDITOR.document.findOne("#cke_"+this.name+" .cke_icons"),t;n&&this.focusManager.add(n,1);n&&n.on("touchend",function(n){return n.cancel(),!1});t=CKEDITOR.document.findOne("#cke_"+this.name+" .cke_buttons");t&&this.focusManager.add(t,1)});n.on("instanceReady",function(){var t=CKEDITOR.document.findOne("#cke_"+n.name+" #ckh_save"),o;if(t){t.on("click",function(){n.execCommand("savecontent")});t.on("touchstart",function(){n.execCommand("savecontent")})}if(t=CKEDITOR.document.findOne("#cke_"+n.name+" #ckh_versions"),t)t.on("touchstart",function(){var t=function(n){return n.data.preventDefault(!0),n.cancel(),!1};n.editable().removeListener("touchend",t);n.editable().attachListener(n.document,"touchend",t,null,null,-100);n.execCommand("versions")});if(t=CKEDITOR.document.findOne("#cke_"+n.name+" #ckh_undo"),t)t.on("touchstart",function(){n.execCommand("undo")});if(t=CKEDITOR.document.findOne("#cke_"+n.name+" #ckh_redo"),t)t.on("touchstart",function(){n.execCommand("redo")});if(!r&&(t=CKEDITOR.document.findOne("#cke_"+n.name+" #ckh_new"),t)){t.on("click",function(){var i=n.editable().getCustomData("newArticleURL"),t;i&&(t=document.createElement("form"),t.action=i,t.method="post",document.body.appendChild(t),t.submit())});t.on("touchstart",function(){var i=n.editable().getCustomData("newArticleURL"),t;i&&(t=document.createElement("form"),t.action=i,t.method="post",document.body.appendChild(t),t.submit())})}if(!i&&(t=CKEDITOR.document.findOne("#cke_"+n.name+" #ckh_close"),t)){t.on("click",function(){n.focusManager.blur();n.editable().$.blur()});t.on("touchstart",function(){n.focusManager.blur();n.editable().$.blur()})}var u=CKEDITOR.document.findOne("#cke_"+n.name+" #cke_source"),f=CKEDITOR.document.findOne("#cke_"+n.name+" #cke_design"),e=CKEDITOR.document.findOne("#cke_"+n.name+" #cke_blocks");if(u)u.on("click",function(t){t.data.preventDefault();u.hasClass("cke_active")||(n.getCommand("showblocks").state==CKEDITOR.TRISTATE_ON&&n.execCommand("showblocks"),n.mode=="wysiwyg"&&n.execCommand("source"),u.addClass("cke_active"),f.removeClass("cke_active"),e.removeClass("cke_active"))});if(f)f.on("click",function(t){t.data.preventDefault();f.hasClass("cke_active")||(n.mode=="source"&&n.execCommand("source"),n.getCommand("showblocks").state==CKEDITOR.TRISTATE_ON&&n.execCommand("showblocks"),u.removeClass("cke_active"),f.addClass("cke_active"),e.removeClass("cke_active"))});if(e)e.on("click",function(t){t.data.preventDefault();e.hasClass("cke_active")||(n.mode=="source"&&n.execCommand("source"),n.mode=="wysiwyg"?n.execCommand("showblocks"):window.setTimeout(function(){n.execCommand("showblocks")},200),u.removeClass("cke_active"),f.removeClass("cke_active"),e.addClass("cke_active"));n.focus()});o=n.undoManager.onChange;this.undoManager.onChange=function(){var r=CKEDITOR.document,t=r.findOne("#cke_"+n.name+" #ckh_undo"),i=r.findOne("#cke_"+n.name+" #ckh_redo");t&&(this.undoable()?t.removeClass("ckh_disabled"):t.addClass("ckh_disabled"));i&&(this.redoable()?i.removeClass("ckh_disabled"):i.addClass("ckh_disabled"));o.apply()}})}})})()