ka.FieldTypes.Plugin = new Class({
    Extends: ka.FieldAbstract,

    Statics: {
        asModel: true
    },

    createLayout: function () {
        this.main = new Element('div').inject(this.fieldInstance.fieldPanel);
        this.renderValue();
    },

    renderValue: function () {
        this.value = this.value || {};

        this.main.getChildren().destroy();

        var fields = {
            bundle: {
                label: t('Bundle'),
                type: 'select',
                items: {}
            }
        };

        Object.each(ka.settings.configs, function (config, key) {
            if (!config.plugins) {
                return;
            }
            if (!Object.getLength(config.plugins)) {
                return;
            }

            fields.bundle.items[key] = config.name || key;

            var plugin = {
                label: t('Plugin'),
                type: 'select',
                needValue: key,
                againstField: 'bundle',
                items: {}
            };

            Object.each(config.plugins, function (def, pluginKey) {
                if (typeOf(def) == 'array') {
                    def = this.normalizePlugin(def);
                }

                plugin.items[pluginKey] = def.label;
            }.bind(this));

            fields['plugin[' + key + ']'] = plugin;
        }.bind(this));

        this.fieldForm = new ka.FieldForm(this.main, fields);

        this.pluginPropertyContainer = new Element('div', {
            'class': 'ka-field-plugin-options'
        }).inject(this.main);

        var i = 0;
        this.fieldForm.addEvent('change', function () {
            this.pluginPropertyContainer.getChildren().destroy();
            var bundle = this.fieldForm.getValue('bundle');
            var plugin = this.fieldForm.getValue('plugin[' + bundle + ']');

            if (!ka.settings.configs[bundle]) {
                delete this.pluginPropertyForm;
                return;
            }

            var def = this.normalizePlugin(ka.settings.configs[bundle].plugins[plugin]);

            if (def && def.options) {
                this.pluginPropertyForm = new ka.FieldForm(this.pluginPropertyContainer, def.options);
                this.pluginPropertyForm.setValue(this.value.options);
                this.pluginPropertyForm.addEvent('change', function() {
                    this.fieldInstance.fireChange();
                }.bind(this));
            } else {
                delete this.pluginPropertyForm;
            }

            this.fieldInstance.fireChange();
        }.bind(this));

        if (this.value && this.value.bundle) {
            var value = {};
            value.bundle = this.value.bundle;
            value.plugin = {};
            value.plugin[value.bundle] = this.value.plugin;
            this.fieldForm.setValue(value);
        }

        this.fieldForm.fireEvent('change');
    },

    normalizePlugin: function (pPlugin) {

        if (typeOf(pPlugin) != 'array') {
            return pPlugin;
        }
        var plugin = {};

        plugin.label = pPlugin[0];
        plugin.options = pPlugin[1];

        return plugin;
    },

    setValue: function (pValue) {
        pValue = this.normalizeValue(pValue);

        this.value = pValue;
        this.renderValue();
    },

    /**
     * since old kryn version stores the value as string
     * we need to convert it to the new object def.
     * @param {String|Object} pValue
     * @return {Object}
     */
    normalizeValue: function (pValue) {

        if (typeOf(pValue) == 'object') {
            return pValue;
        }

        if (typeOf(pValue) == 'string' && JSON.validate(pValue)) {
            return JSON.decode(pValue);
        }

        if (typeOf(pValue) != 'string') {
            return {};
        }

        var bundle = pValue.substr(0, pValue.indexOf('::'));
        var plugin = pValue.substr(bundle.length + 2, pValue.substr(bundle.length + 2).indexOf('::'));
        var options = pValue.substr(bundle.length + plugin.length + 4);

        options = JSON.validate(options) ? JSON.decode(options) : {};

        return {
            bundle: bundle,
            plugin: plugin,
            options: options
        };
    },

    getValue: function () {

        var plugin = {};
        plugin.bundle = this.fieldForm.getValue('bundle');

        plugin.plugin = this.fieldForm.getValue('plugin[' + plugin.bundle + ']')

        if (this.pluginPropertyForm) {
            plugin.options = this.pluginPropertyForm.getValue();
        } else {
            plugin.options = {};
        }

        return JSON.encode(plugin);
    }

});