var admin_system = new Class({
    initialize: function (pWindow) {
        this.win = pWindow;
        this._createLayout();
    },

    _createLayout: function () {

        var tpl = 
        '<h3>Kryn.cms</h3><br/>'+
        'Version: {$ka.settings.configs.core.version}<br/>'+
        'Version: {ka.settings.configs.core.version}<br/>'+
        '<br/>'+
        '<a href="{_path}LICENSE">LICENSE</a><br/>'+
        '<br/>'+
        '<a href="http://forum.kryn.org" target="_blank">forum.kryn.org</a><br />'+
        '<a href="mailto:support@kryn.org">support@kryn.org</a><br />'+
        '<a href="http://docu.kryn.org" target="_blank">docu.kryn.org</a><br/>'+
        '<br/>'+
        '<div>&copy; <a target="_blank" href="http://www.kryn.org">www.kryn.org</a>. All Rights Reserved.'+
        '<br/>'+
        '<br/>'+
        '';

        ka.template(this.win.content, tpl);
        this.win.content.setStyle('text-align', 'center');
    }
});

