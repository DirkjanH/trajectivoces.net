<?php
/**
 * @package     com_arkeditor
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

extract($displayData);

echo $renderValue;
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
jQuery(function($){
	
	var base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(r){var t,e,o,h,a,n,c,d="",i=0;for(r=this._utf8_encode(r);i<r.length;)t=r.charCodeAt(i++),e=r.charCodeAt(i++),o=r.charCodeAt(i++),h=t>>2,a=(3&t)<<4|e>>4,n=(15&e)<<2|o>>6,c=63&o,isNaN(e)?n=c=64:isNaN(o)&&(c=64),d=d+this._keyStr.charAt(h)+this._keyStr.charAt(a)+this._keyStr.charAt(n)+this._keyStr.charAt(c);return d},decode:function(r){var t,e,o,h,a,n,c,d="",i=0;for(r=r.replace(/[^A-Za-z0-9\+\/\=]/g,"");i<r.length;)h=this._keyStr.indexOf(r.charAt(i++)),a=this._keyStr.indexOf(r.charAt(i++)),n=this._keyStr.indexOf(r.charAt(i++)),c=this._keyStr.indexOf(r.charAt(i++)),t=h<<2|a>>4,e=(15&a)<<4|n>>2,o=(3&n)<<6|c,d+=String.fromCharCode(t),64!=n&&(d+=String.fromCharCode(e)),64!=c&&(d+=String.fromCharCode(o));return d=this._utf8_decode(d)},_utf8_encode:function(r){r=r.replace(/\r\n/g,"\n");for(var t="",e=0;e<r.length;e++){var o=r.charCodeAt(e);128>o?t+=String.fromCharCode(o):o>127&&2048>o?(t+=String.fromCharCode(o>>6|192),t+=String.fromCharCode(63&o|128)):(t+=String.fromCharCode(o>>12|224),t+=String.fromCharCode(o>>6&63|128),t+=String.fromCharCode(63&o|128))}return t},_utf8_decode:function(r){for(var t="",e=0,o=c1=c2=0;e<r.length;)o=r.charCodeAt(e),128>o?(t+=String.fromCharCode(o),e++):o>191&&224>o?(c2=r.charCodeAt(e+1),t+=String.fromCharCode((31&o)<<6|63&c2),e+=2):(c2=r.charCodeAt(e+1),c3=r.charCodeAt(e+2),t+=String.fromCharCode((15&o)<<12|(63&c2)<<6|63&c3),e+=3);return t}};
	
	
	
	var formControl = "'.$formControl.'";
	
	var	target = $("#"+formControl+"'.$target.'");
	
	var fieldsStr = "'.$display.'",
		fields = JSON.parse(fieldsStr.replace(/\'/g,\'"\')),
		fieldid = "'.$id.'";
	
	var oldIndex = "'.$value.'";
	
	$("select#"+fieldid).on("change", function(){
		
		var oldField =  $("input#"+formControl+fields[oldIndex]);
		if(!oldField.length)
			return;
		var enc = base64.encode(target.val());
		
		oldField.val(enc);
		
		var index = this.value,
			field =  $("input#"+formControl+fields[index]);
		var dec = base64.decode(field.val());	

		target.val(dec);
		oldIndex = index;	
	});
	
	$(this.adminForm).submit(function() {
		
		var oldField =  $("input#"+formControl+fields[oldIndex]);
		if(!oldField.length)
			return true;
		var enc = base64.encode(target.val());	
		oldField.val(enc);
		return true;
	});
});
'); 
