/* jce - 2.9.54 | 2023-11-12 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2023 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function() {
    var each = tinymce.each, colors = "000000,993300,333300,003300,003366,000080,333399,333333,800000,FF6600,808000,008000,008080,0000FF,666699,808080,FF0000,FF9900,99CC00,339966,33CCCC,3366FF,800080,999999,FF00FF,FFCC00,FFFF00,00FF00,00FFFF,00CCFF,993366,FFFFFF,FF99CC,FFCC99,FFFF99,CCFFCC,CCFFFF,99CCFF,CC99FF";
    tinymce.create("tinymce.plugins.FontColorPlugin", {
        init: function(ed, url) {
            (this.editor = ed).onNodeChange.add(function(ed, cm, n, collapsed, o) {
                var c, fc, bc;
                function updateColor(controlId, color) {
                    (c = cm.get(controlId)) && (color = color || c.settings.default_color) !== c.value && c.displayColor(color);
                }
                each(o.parents, function(n) {
                    if (n.style && (n.style.color && (updateColor("forecolor", n.style.color), 
                    fc = !0), n.style.backgroundColor && (updateColor("backcolor", n.style.backgroundColor), 
                    bc = !0), fc) && bc) return !1;
                });
            });
        },
        createControl: function(n) {
            return "forecolor" === n ? this._createForeColorMenu() : "backcolor" === n ? this._createBackColorMenu() : void 0;
        },
        _createForeColorMenu: function() {
            var c, ed = this.editor, s = ed.settings, o = {
                more_colors_func: function() {
                    ed.execCommand("mceColorPicker", !1, {
                        color: c.value,
                        func: function(co) {
                            c.setColor(co);
                        }
                    });
                }
            }, v = s.fontcolor_foreground_colors || "";
            return v && (o.colors = v.replace("$default", colors)), o.default_color = s.fontcolor_foreground_color || "#000000", 
            o.title = "advanced.forecolor_desc", o.onselect = o.onclick = function(v) {
                if (!v) return ed.formatter.remove("forecolor");
                ed.formatter.apply("forecolor", {
                    value: v
                }), ed.undoManager.add(), ed.nodeChanged();
            }, o.scope = this, c = ed.controlManager.createColorSplitButton("forecolor", o);
        },
        _createBackColorMenu: function() {
            var c, ed = this.editor, s = ed.settings, o = {
                more_colors_func: function() {
                    ed.execCommand("mceColorPicker", !1, {
                        color: c.value,
                        func: function(co) {
                            c.setColor(co);
                        }
                    });
                }
            }, v = s.fontcolor_background_colors || "";
            return v && (o.colors = v.replace("$default", colors)), o.default_color = s.fontcolor_background_color || "#FFFF00", 
            o.title = "advanced.backcolor_desc", o.onselect = o.onclick = function(v) {
                if (!v) return ed.formatter.remove("hilitecolor");
                ed.formatter.apply("hilitecolor", {
                    value: v
                }), ed.undoManager.add(), ed.nodeChanged();
            }, o.scope = this, c = ed.controlManager.createColorSplitButton("backcolor", o);
        }
    }), tinymce.PluginManager.add("fontcolor", tinymce.plugins.FontColorPlugin);
}();