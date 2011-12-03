ka.pluginChooser = new Class({
    Implements: Events,
    initialize: function (pTypes, pTarget) {
        this.modules = [];
        this.target = pTarget;

        var w = pTarget.getWindow();


        var opts = [];
        if ($type(pTypes) == 'string')
            opts = pTypes.split('::');

        this.choosen = {};
        this.choosen.module = opts[0];

        if (!ka.settings.configs[this.choosen.module])
            this.choosen.module = '-';

        this.choosen.plugin = opts[1];
        if (this.choosen.module != '-' && !ka.settings.configs[this.choosen.module]['plugins'][this.choosen.plugin])
            this.choosen.plugin = '-';

        try {
            this.choosen.options = JSON.decode(opts[2]);
        } catch (e) {
        }
        ;

        this.renderLayout();

        this.selectModules.setValue(this.choosen.module);
        this.moduleChanged();

        this.selectPlugin.setValue(this.choosen.plugin);
        this.loadProperties();

        this.setValue(this.choosen.options);

    },

    renderLayout: function () {

        this.main = new Element('div', {
            style: ' ',
            'class': 'ka-pluginchooser-main'
        }).inject(this.target);

        this.head = new Element('div', {
            'style': 'position: absolute; left: 0px; top: 0px; right: 0px; height: 50px; padding: 10px;'
        }).inject(this.main);

        this.optionsPane = new Element('div', {
            'class': 'ka-pluginchooser-options'
        }).inject(this.main);

        this.bottom = new Element('div', {
            'class': 'ka-pluginchooser-bottom'
        }).inject(this.main);

        new ka.Button(_('Cancel')).addEvent('click', function () {
            this.fireEvent('cancel');
        }.bind(this)).inject(this.bottom);

        new ka.Button(_('OK')).addEvent('click', function () {
            this.fireEvent('ok');
        }.bind(this)).inject(this.bottom);


        //module
        var table = new Element('table', {
            width: '100%'
        }).inject(this.head);
        var tbody = new Element('tbody').inject(table);

        var tr = new Element('tr').inject(tbody);
        var td = new Element('td', {
            html: _('Extension:'),
            width: 100,
            style: 'font-size: 12px; font-weight: bold; padding: 0px 4px;'
        }).inject(tr);
        var td = new Element('td', {
            width: 200
        }).inject(tr);

        this.selectModules = new ka.Select();

        this.selectModules.add('-', _('-- Please choose --'));

        Object.each(ka.settings.configs, function (config, ext) {
            if (config.plugins) {
                var title = config.title['en'];
                if (config.title[ window._session.lang ]) {
                    title = config.title[ window._session.lang ];
                }
                this.selectModules.add(ext, title);
            }
        }.bind(this));

        this.selectModules.addEvent('change', this.moduleChanged.bind(this));
        this.selectModules.inject(td);

        this.pluginDescription = new Element('td', {
            rowspan: 2,
            html: _('Please choose a extension and a plugin.')
        }).inject(tr);

        var tr = new Element('tr').inject(tbody);
        var td = new Element('td', {
            html: _('Plugin:'),
            style: 'font-size: 12px; font-weight: bold; padding: 0px 4px;'
        }).inject(tr);

        var td = new Element('td', {
            width: 200
        }).inject(tr);

        this.selectPlugin = new ka.Select();
        this.selectPlugin.inject(td);
        this.selectPlugin.addEvent('change', this.loadProperties.bind(this));

    },

    moduleChanged: function () {

        var mod = this.selectModules.getValue();
        this.pluginDescription.set('html', _('Please choose a extension and a plugin.'));

        this.selectPlugin.empty();
        this.optionsPane.empty();

        if (!ka.settings.configs[mod]) {
            return;
        }

        if (mod != '-')
            this.choosen.module = mod;

        this.selectPlugin.add('-', _('-- Please choose --'));

        Object.each(ka.settings.configs[mod].plugins, function (plugin, pluginId) {

            var properties = this.selectPlugin.add(pluginId, plugin[0]);

        }.bind(this));

        if (this.choosen.plugin != '-') {
            this.selectPlugin.setValue(this.choosen.plugin);
            this.loadProperties();
        }

    },

    loadProperties: function () {

        var mod = this.selectModules.getValue();
        var plugin = this.selectPlugin.getValue();

        this.optionsPane.empty();

        if (plugin != '-')
            this.choosen.plugin = plugin;

        this.propertyTable = new Element('table').inject(this.optionsPane);
        this.propertyTBody = new Element('tbody').inject(this.propertyTable);

        if (this.lastGetRequest) this.lastGetRequest.cancel();

        this.pluginDescription.set('html', _('Please choose a extension and a plugin.'));

        this.lastGetRequest = new Request.JSON({url: _path + 'admin/backend/plugins/get/', onComplete: function (res) {

            if (res) {
                this.fieldObj = new ka.parse(this.propertyTBody, res[1], {allTableItems: true});
                this.setValue(this.choosen.options);

                if (res[2]) {
                    this.pluginDescription.set('text', res[2]);
                }
            } else {
                new Element('tr', {
                    html: '<td>There was an error while fetching the plugin definition.</td>'
                }).inject(this.propertyTBody)
            }

        }.bind(this)}).post({ module: mod, plugin: plugin });
    },

    setValue: function (pValues) {
        if (this.fieldObj)
            this.fieldObj.setValue(pValues);
    },

    getValue: function () {
        var res = this.choosen.module + '::' + this.choosen.plugin + '::';
        if (this.fieldObj)
            res += JSON.encode(this.fieldObj.getValue());
        return res;
    },

    inject: function (pTarget) {
        this.main.inject(pTarget);
        return this;
    }
});