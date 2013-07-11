var admin_system = new Class({
    initialize: function(pWindow) {
        this.win = pWindow;
        this._createLayout();
    },

    _createLayout: function() {
        this.addSection('admin');
        Object.each(ka.settings.configs, function(config, key) {
            if ('admin' !== key) {
                this.addSection(key);
            }
        }, this);
    },

    addSection: function(bundleName) {
        var config = ka.settings.configs[bundleName];
        var container, subContainer;

        if (config.entryPoints) {
            var systemEntryPoints = this.collectSystemEntryPoints(config.entryPoints);
            if (0 < Object.getLength(systemEntryPoints)) {

                container = new Element('div', {
                    'class': 'ka-system-cat'
                }).inject(this.win.getContentContainer());

                new Element('h2', {
                    'class': 'light',
                    text: config.label || config.name
                }).inject(container);

                Object.each(systemEntryPoints, function(entryPoint) {
                    if (!entryPoint.type) {
                        subContainer = new Element('div', {
                            'class': 'ka-system-cat'
                        }).inject(this.win.getContentContainer());

                        new Element('h2', {
                            'class': 'light',
                            text: entryPoint.label
                        }).inject(subContainer);

                        Object.each(entryPoint.children, function(subEntryPoint) {
                            this.addLink(bundleName, subEntryPoint, subContainer);
                        }, this);

                    } else {
                        this.addLink(bundleName, entryPoint, container);
                    }
                }, this);
            }
        }
    },

    addLink: function(bundleName, entryPoint, container) {
        var item = new Element('a', {
            'class': 'ka-system-settings-link',
            text: entryPoint.label
        })
            .addEvent('click', function() {
                ka.wm.open(bundleName + '/' + entryPoint.fullPath);
            })
            .inject(container);

        if (entryPoint.icon) {
            var span = new Element('span', {
                'class': entryPoint.icon.indexOf('#') === 0 ?  entryPoint.icon.substr(1) : null
            }).inject(item, 'top');

            if (-1 === entryPoint.icon.indexOf('#')) {
                new Element('img', {
                    src: ka.mediaPath(entryPoint.icon)
                }).inject(span);
            }
        }
    },

    collectSystemEntryPoints: function(entryPoints) {
        var result = {};

        Object.each(entryPoints, function(entryPoint, key) {
            if (!entryPoint.link) return;

            if (entryPoint.system) {
                result[key] = entryPoint;
            }
            if (entryPoint.children) {
                result = Object.merge(result, this.collectSystemEntryPoints(entryPoint.children));
            }
        }, this);

        return result
    },


    shit: function() {

        logger(ka.settings);


        this.win.content.set('html',
            '<h1>Kryn.cms</h1><br/>' +
                'Version: {ka.settings.configs.core.version}<br/>' +
                '<br/>' +
                '<a href="{_path}LICENSE">LICENSE</a><br/>' +
                '<br/>' +
                '<a href="http://forum.kryn.org" target="_blank">forum.kryn.org</a><br />' +
                '<a href="mailto:support@kryn.org">support@kryn.org</a><br />' +
                '<a href="http://docu.kryn.org" target="_blank">docu.kryn.org</a><br/>' +
                '<br/>' +
                '<div>&copy; <a target="_blank" href="http://www.kryn.org">www.kryn.org</a>. All Rights Reserved.' +
                '<br/>' +
                '<br/>');

        mowla.render(this.win.content);
    }
});