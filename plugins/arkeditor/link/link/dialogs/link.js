"use strict";
/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */
(function(){function n(){var n=this.getDialog(),t=n._.editor,i=t.config.linkPhoneRegExp,u=t.config.linkPhoneMsg,f=t.lang.link,r=CKEDITOR.dialog.validate.notEmpty(f.noTel).apply(this);return!n.getContentElement("info","linkType")||n.getValueOf("info","linkType")!="tel"?!0:r!==!0?r:i?CKEDITOR.dialog.validate.regex(i,u).call(this):void 0}CKEDITOR.dialog.add("link",function(t){function y(n,t){var i=n.createRange();return i.setStartBefore(t),i.setEndAfter(t),i}function p(n,t){var o=h.getLinkAttributes(n,t),s=n.getSelection().getRanges(),l=new CKEDITOR.style({element:"a",attributes:o.set}),a=[],i,r,e,u,f;for(l.type=CKEDITOR.STYLE_INLINE,u=0;u<s.length;u++){for(i=s[u],i.collapsed?(r=new CKEDITOR.dom.text(t.linkText||(t.type=="email"?t.email.address:o.set["data-cke-saved-href"]),n.document),i.insertNode(r),i.selectNodeContents(r)):c!==t.linkText&&(r=new CKEDITOR.dom.text(t.linkText,n.document),i.shrink(CKEDITOR.SHRINK_TEXT),n.editable().extractHtmlFromRange(i),i.insertNode(r)),e=i._find("a"),f=0;f<e.length;f++)e[f].remove(!0);l.applyToRange(i,n);a.push(i)}n.getSelection().selectRanges(a)}function w(n,t,i){for(var e=h.getLinkAttributes(n,i),s=[],l,a,v,r,o,u,f=0;f<t.length;f++)r=t[f],o=r.data("cke-saved-href"),l=i.linkText&&c!=i.linkText,v=o==c,a=i.type=="email"&&o=="mailto:"+c,r.setAttributes(e.set),r.removeAttributes(e.removed),l?u=i.linkText:(v||a)&&(u=i.type=="email"?i.email.address:e.set["data-cke-saved-href"]),u&&r.setText(u),s.push(y(n,r));n.getSelection().selectRanges(s)}var h=CKEDITOR.plugins.link,c,l=function(){var r=this.getDialog(),i=r.getContentElement("target","popupFeatures"),n=r.getContentElement("target","linkTargetName"),u=this.getValue();if(i&&n){i=i.getElement();i.hide();n.setValue("");switch(u){case"frame":n.setLabel(t.lang.link.targetFrameName);n.getElement().show();break;case"popup":i.show();n.setLabel(t.lang.link.targetPopupName);n.getElement().show();break;default:n.setValue(u);n.getElement().hide()}}},b=function(){var n=this.getDialog(),u=["urlOptions","anchorOptions","emailOptions","telOptions"],f=this.getValue(),e=n.definition.getContents("upload"),o=e&&e.hidden,r,i;for(f=="url"?(t.config.linkShowTargetTab&&n.showPage("target"),o||n.showPage("upload")):(n.hidePage("target"),o||n.hidePage("upload")),r=0;r<u.length;r++)(i=n.getContentElement("info",u[r]),i)&&(i=i.getElement().getParent().getParent(),u[r]==f+"Options"?i.show():i.hide());n.layout()},a=function(n,t){t[n]&&this.setValue(t[n][this.id]||"")},f=function(n){return a.call(this,"target",n)},e=function(n){return a.call(this,"advanced",n)},v=function(n,t){t[n]||(t[n]={});t[n][this.id]=this.getValue()||""},o=function(n){return v.call(this,"target",n)},s=function(n){return v.call(this,"advanced",n)},r=t.lang.common,i=t.lang.link,u;return{title:i.title,minWidth:(CKEDITOR.skinName||t.config.skin)=="moono-lisa"?450:350,minHeight:240,getModel:function(n){var t=h.getSelectedLink(n,!0);return t[0]||null},contents:[{id:"info",label:i.info,title:i.info,elements:[{type:"text",id:"linkDisplayText",label:i.displayText,setup:function(){this.enable();this.setValue(t.getSelection().getSelectedText());c=this.getValue()},commit:function(n){n.linkText=this.isEnabled()?this.getValue():""}},{id:"linkType",type:"select",label:i.type,"default":"url",items:[[i.toUrl,"url"],[i.toAnchor,"anchor"],[i.toEmail,"email"],[i.toPhone,"tel"]],onChange:b,setup:function(n){this.setValue(n.type||"url")},commit:function(n){n.type=this.getValue()}},{type:"vbox",id:"urlOptions",children:[{type:"hbox",widths:["25%","75%"],children:[{id:"protocol",type:"select",label:r.protocol,items:[["http://‎","http://"],["https://‎","https://"],["ftp://‎","ftp://"],["news://‎","news://"],[i.other,""]],"default":t.config.linkDefaultProtocol,setup:function(n){n.url&&this.setValue(n.url.protocol||"")},commit:function(n){n.url||(n.url={});n.url.protocol=this.getValue()}},{type:"text",id:"url",label:r.url,required:!0,onLoad:function(){this.allowOnChange=!0},onKeyUp:function(){this.allowOnChange=!1;var i=this.getDialog().getContentElement("info","protocol"),n=this.getValue(),t=/^(http|https|ftp|news):\/\/(?=.)/i.exec(n);t?(this.setValue(n.substr(t[0].length)),i.setValue(t[0].toLowerCase())):/^((javascript:)|[#\/\.\?])/i.test(n)&&i.setValue("");this.allowOnChange=!0},onChange:function(){this.allowOnChange&&this.onKeyUp()},validate:function(){var n=this.getDialog(),u;return n.getContentElement("info","linkType")&&n.getValueOf("info","linkType")!="url"?!0:!t.config.linkJavaScriptLinksAllowed&&/javascript\:/.test(this.getValue())?(alert(r.invalidValue),!1):this.getDialog().fakeObj?!0:(u=CKEDITOR.dialog.validate.notEmpty(i.noUrl),u.apply(this))},setup:function(n){this.allowOnChange=!1;n.url&&this.setValue(n.url.url);this.allowOnChange=!0},commit:function(n){this.onChange();n.url||(n.url={});n.url.url=this.getValue();this.allowOnChange=!1}}],setup:function(){this.getDialog().getContentElement("info","linkType")||this.getElement().show()}},{type:"button",id:"browse",hidden:"true",filebrowser:"info:url",label:r.browseServer}]},{type:"vbox",id:"anchorOptions",width:260,align:"center",padding:0,children:[{type:"fieldset",id:"selectAnchorText",label:i.selectAnchor,setup:function(){u=h.getEditorAnchors(t);this.getElement()[u&&u.length?"show":"hide"]()},children:[{type:"hbox",id:"selectAnchor",children:[{type:"select",id:"anchorName","default":"",label:i.anchorName,style:"width: 100%;",items:[[""]],setup:function(n){var t,i;if(this.clear(),this.add(""),u)for(t=0;t<u.length;t++)u[t].name&&this.add(u[t].name);n.anchor&&this.setValue(n.anchor.name);i=this.getDialog().getContentElement("info","linkType");i&&i.getValue()=="email"&&this.focus()},commit:function(n){n.anchor||(n.anchor={});n.anchor.name=this.getValue()}},{type:"select",id:"anchorId","default":"",label:i.anchorId,style:"width: 100%;",items:[[""]],setup:function(n){if(this.clear(),this.add(""),u)for(var t=0;t<u.length;t++)u[t].id&&this.add(u[t].id);n.anchor&&this.setValue(n.anchor.id)},commit:function(n){n.anchor||(n.anchor={});n.anchor.id=this.getValue()}}],setup:function(){this.getElement()[u&&u.length?"show":"hide"]()}}]},{type:"html",id:"noAnchors",style:"text-align: center;",html:'<div role="note" tabIndex="-1">'+CKEDITOR.tools.htmlEncode(i.noAnchors)+"<\/div>",focus:!0,setup:function(){this.getElement()[u&&u.length?"hide":"show"]()}}],setup:function(){this.getDialog().getContentElement("info","linkType")||this.getElement().hide()}},{type:"vbox",id:"emailOptions",padding:1,children:[{type:"text",id:"emailAddress",label:i.emailAddress,required:!0,validate:function(){var n=this.getDialog(),t;return!n.getContentElement("info","linkType")||n.getValueOf("info","linkType")!="email"?!0:(t=CKEDITOR.dialog.validate.notEmpty(i.noEmail),t.apply(this))},setup:function(n){n.email&&this.setValue(n.email.address);var t=this.getDialog().getContentElement("info","linkType");t&&t.getValue()=="email"&&this.select()},commit:function(n){n.email||(n.email={});n.email.address=this.getValue()}},{type:"text",id:"emailSubject",label:i.emailSubject,setup:function(n){n.email&&this.setValue(n.email.subject)},commit:function(n){n.email||(n.email={});n.email.subject=this.getValue()}},{type:"textarea",id:"emailBody",label:i.emailBody,rows:3,"default":"",setup:function(n){n.email&&this.setValue(n.email.body)},commit:function(n){n.email||(n.email={});n.email.body=this.getValue()}}],setup:function(){this.getDialog().getContentElement("info","linkType")||this.getElement().hide()}},{type:"vbox",id:"telOptions",padding:1,children:[{type:"tel",id:"telNumber",label:i.phoneNumber,required:!0,validate:n,setup:function(n){n.tel&&this.setValue(n.tel);var t=this.getDialog().getContentElement("info","linkType");t&&t.getValue()=="tel"&&this.select()},commit:function(n){n.tel=this.getValue()}}],setup:function(){this.getDialog().getContentElement("info","linkType")||this.getElement().hide()}}]},{id:"target",requiredContent:"a[target]",label:i.target,title:i.target,elements:[{type:"hbox",widths:["50%","50%"],children:[{type:"select",id:"linkTargetType",label:r.target,"default":"notSet",style:"width : 100%;",items:[[r.notSet,"notSet"],[i.targetFrame,"frame"],[i.targetPopup,"popup"],[r.targetNew,"_blank"],[r.targetTop,"_top"],[r.targetSelf,"_self"],[r.targetParent,"_parent"]],onChange:l,setup:function(n){n.target&&this.setValue(n.target.type||"notSet");l.call(this)},commit:function(n){n.target||(n.target={});n.target.type=this.getValue()}},{type:"text",id:"linkTargetName",label:i.targetFrameName,"default":"",setup:function(n){n.target&&this.setValue(n.target.name)},commit:function(n){n.target||(n.target={});n.target.name=this.getValue().replace(/([^\x00-\x7F]|\s)/gi,"")}}]},{type:"vbox",width:"100%",align:"center",padding:2,id:"popupFeatures",children:[{type:"fieldset",label:i.popupFeatures,children:[{type:"hbox",children:[{type:"checkbox",id:"resizable",label:i.popupResizable,setup:f,commit:o},{type:"checkbox",id:"status",label:i.popupStatusBar,setup:f,commit:o}]},{type:"hbox",children:[{type:"checkbox",id:"location",label:i.popupLocationBar,setup:f,commit:o},{type:"checkbox",id:"toolbar",label:i.popupToolbar,setup:f,commit:o}]},{type:"hbox",children:[{type:"checkbox",id:"menubar",label:i.popupMenuBar,setup:f,commit:o},{type:"checkbox",id:"fullscreen",label:i.popupFullScreen,setup:f,commit:o}]},{type:"hbox",children:[{type:"checkbox",id:"scrollbars",label:i.popupScrollBars,setup:f,commit:o},{type:"checkbox",id:"dependent",label:i.popupDependent,setup:f,commit:o}]},{type:"hbox",children:[{type:"text",widths:["50%","50%"],labelLayout:"horizontal",label:r.width,id:"width",setup:f,commit:o},{type:"text",labelLayout:"horizontal",widths:["50%","50%"],label:i.popupLeft,id:"left",setup:f,commit:o}]},{type:"hbox",children:[{type:"text",labelLayout:"horizontal",widths:["50%","50%"],label:r.height,id:"height",setup:f,commit:o},{type:"text",labelLayout:"horizontal",label:i.popupTop,widths:["50%","50%"],id:"top",setup:f,commit:o}]}]}]}]},{id:"upload",label:i.upload,title:i.upload,hidden:!0,filebrowser:"uploadButton",elements:[{type:"file",id:"upload",label:r.upload,style:"height:40px",size:29},{type:"fileButton",id:"uploadButton",label:r.uploadSubmit,filebrowser:"info:url","for":["upload","upload"]}]},{id:"advanced",label:i.advanced,title:i.advanced,elements:[{type:"vbox",padding:1,children:[{type:"hbox",widths:["45%","35%","20%"],children:[{type:"text",id:"advId",requiredContent:"a[id]",label:i.id,setup:e,commit:s},{type:"select",id:"advLangDir",requiredContent:"a[dir]",label:i.langDir,"default":"",style:"width:110px",items:[[r.notSet,""],[i.langDirLTR,"ltr"],[i.langDirRTL,"rtl"]],setup:e,commit:s},{type:"text",id:"advAccessKey",requiredContent:"a[accesskey]",width:"80px",label:i.acccessKey,maxLength:1,setup:e,commit:s}]},{type:"hbox",widths:["45%","35%","20%"],children:[{type:"text",label:i.name,id:"advName",requiredContent:"a[name]",setup:e,commit:s},{type:"text",label:i.langCode,id:"advLangCode",requiredContent:"a[lang]",width:"110px","default":"",setup:e,commit:s},{type:"text",label:i.tabIndex,id:"advTabIndex",requiredContent:"a[tabindex]",width:"80px",maxLength:5,setup:e,commit:s}]}]},{type:"vbox",padding:1,children:[{type:"hbox",widths:["45%","55%"],children:[{type:"text",label:i.advisoryTitle,requiredContent:"a[title]","default":"",id:"advTitle",setup:e,commit:s},{type:"text",label:i.advisoryContentType,requiredContent:"a[type]","default":"",id:"advContentType",setup:e,commit:s}]},{type:"hbox",widths:["45%","55%"],children:[{type:"text",label:i.cssClasses,requiredContent:"a(cke-xyz)","default":"",id:"advCSSClasses",setup:e,commit:s},{type:"text",label:i.charset,requiredContent:"a[charset]","default":"",id:"advCharset",setup:e,commit:s}]},{type:"hbox",widths:["45%","55%"],children:[{type:"text",label:i.rel,requiredContent:"a[rel]","default":"",id:"advRel",setup:e,commit:s},{type:"text",label:i.styles,requiredContent:"a{cke-xyz}","default":"",id:"advStyles",validate:CKEDITOR.dialog.validate.inlineStyle(t.lang.common.invalidInlineStyle),setup:e,commit:s}]},{type:"hbox",widths:["45%","55%"],children:[{type:"checkbox",id:"download",requiredContent:"a[download]",label:i.download,setup:function(n){n.download!==undefined&&this.setValue("checked","checked")},commit:function(n){this.getValue()&&(n.download=this.getValue())}}]}]}]}],onShow:function(){var t=this.getParentEditor(),i=t.getSelection(),u=this.getContentElement("info","linkDisplayText").getElement().getParent().getParent(),r=h.getSelectedLink(t,!0),n=r[0]||null,f;n&&n.hasAttribute("href")&&(i.getSelectedElement()||i.isInTable()||i.selectElement(n));f=h.parseLinkAttributes(t,n);r.length<=1&&h.showDisplayTextForElement(n,t)?u.show():u.hide();this._.selectedElements=r;this.setupContent(f)},onOk:function(){var n={};this.commitContent(n);this._.selectedElements.length?(w(t,this._.selectedElements,n),delete this._.selectedElements):p(t,n)},onLoad:function(){t.config.linkShowAdvancedTab||this.hidePage("advanced");t.config.linkShowTargetTab||this.hidePage("target")},onFocus:function(){var n=this.getContentElement("info","linkType"),t;n&&n.getValue()=="url"&&(t=this.getContentElement("info","url"),t.select())}}})})()