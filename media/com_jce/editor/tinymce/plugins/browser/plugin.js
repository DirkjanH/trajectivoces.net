/* jce - 2.9.54 | 2023-11-12 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2023 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
tinymce.create("tinymce.plugins.Browser", {
    init: function(ed, url) {
        var self = this;
        (self.ed = ed).addCommand("mceFileBrowser", function(ui, args, win) {
            self.open(args, win);
        });
    },
    open: function(args, win) {
        args = args || {};
        var ed = this.ed;
        return ed.windowManager.open({
            file: ed.getParam("site_url") + "index.php?option=com_jce&task=plugin.display&plugin=browser" + (args.caller ? "." + args.caller : "") + (args.filter ? "&filter=" + args.filter : ""),
            close_previous: "no",
            size: "mce-modal-landscape-full"
        }, args), !1;
    }
}), tinymce.PluginManager.add("browser", tinymce.plugins.Browser);