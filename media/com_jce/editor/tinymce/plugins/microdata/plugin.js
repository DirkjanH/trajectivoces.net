/* jce - 2.9.54 | 2023-11-12 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2023 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function() {
    var each = tinymce.each;
    tinymce.create("tinymce.plugins.MicrodataPlugin", {
        init: function(ed, url) {
            (this.editor = ed).addCommand("mceMicrodata", function() {
                ed.windowManager.open({
                    file: ed.getParam("site_url") + "index.php?option=com_jce&task=plugin.display&plugin=microdata",
                    size: "mce-modal-square-medium"
                }, {
                    plugin_url: url
                });
            }), ed.addButton("microdata", {
                title: "microdata.desc",
                cmd: "mceMicrodata"
            }), ed.onNodeChange.add(function(ed, cm, n, co) {
                cm.setDisabled("microdata", co), cm.setActive("microdata", co && function(n) {
                    return n.getAttribute("itemprop") || n.getAttribute("data-microdata-itemprop");
                }(n));
            }), ed.onInit.add(function(ed) {
                ed.settings.compress.css || ed.dom.loadCSS(url + "/css/content.css");
            }), ed.onPreInit.add(function() {
                var attribs = [ "itemscope", "itemtype", "itemid", "itemprop", "itemref", "content" ].concat([ "about", "rel", "rev", "resource", "property", "datatype", "typeof" ]);
                ed.schema.addValidElements("meta[itemprop|content|id|class|name|http-equiv|charset]"), 
                ed.schema.addValidElements("link[href|itemprop|id|class|rel|media|hreflang|type|sizes]"), 
                each(ed.schema.elements, function(v, k) {
                    /\w+/.test(k) && v.attributes && (each(attribs, function(name) {
                        var val = "itemscope" == name ? {
                            defaultValue: "itemscope"
                        } : {};
                        v.attributes[name] = val;
                    }), v.attributesOrder.concat(v.attributesOrder, attribs), k = ed.schema.children[k]) && k.span && (k.meta = {}, 
                    k.link = {});
                }), ed.serializer.addAttributeFilter("data-microdata-itemscope,data-microdata-itemtype,data-microdata-itemid,data-microdata-itemprop,data-microdata-itemref,data-microdata-content", function(nodes, name) {
                    for (var node, k, v, i = nodes.length; i--; ) node = nodes[i], 
                    k = name.replace("data-microdata-", ""), v = node.attr(name), 
                    "itemtype" === k && (node.attr("itemscope", "itemscope"), -1 === v.indexOf("://")) && (v = "https://schema.org/" + v), 
                    node.attr(k, v), node.attr(name, null);
                }), ed.parser.addAttributeFilter("itemscope,itemtype,itemid,itemprop,itemref,content", function(nodes, name) {
                    for (var node, v, i = nodes.length; i--; ) v = (node = nodes[i]).attr(name), 
                    "content" === name && "meta" === node.name || ("itemscope" === name && (v = "itemscope"), 
                    "itemtype" === name && -1 === v.indexOf("://") && (v = "https://schema.org/" + v), 
                    node.attr("data-microdata-" + name, v), node.attr(name, null));
                }), ed.formatter.register({
                    microdata: {
                        inline: "span",
                        onformat: function(elm, fmt, vars) {
                            each(vars, function(value, key) {
                                ed.dom.setAttrib(elm, key, value);
                            });
                        }
                    },
                    "microdata-remove": {
                        selector: "span",
                        attributes: [ "data-microdata-itemprop" ],
                        remove: "emtpy"
                    }
                });
            });
        }
    }), tinymce.PluginManager.add("microdata", tinymce.plugins.MicrodataPlugin);
}();