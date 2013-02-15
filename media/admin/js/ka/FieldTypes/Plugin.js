ka.FieldTypes.Plugin = new Class({

    Extends: ka.FieldAbstract,

    createLayout: function(){

        this.main = new Element('div').inject(this.fieldInstance.fieldPanel);

        this.renderValue();

    },

    renderValue: function(pValue){
        pValue = pValue || {};

        this.main.getElements('*').destroy();

        var fields = {
            module: {
                label: t('Extension'),
                type: 'select',
                inputWidth: 240,
                items: {}
            }
        };


        Object.each(ka.settings.configs, function(config, key){

            if (!config.plugins) return;
            if (!Object.getLength(config.plugins)) return;

            fields.module.items[key] = config.title;

            var plugin = {
                label: t('Plugin'),
                type: 'select',
                needValue: key,
                againstField: 'module',
                inputWidth: 240,
                items: {}
            };

            Object.each(config.plugins, function(def, pluginKey){

                if (typeOf(def) == 'array')
                    def = this.normalizePlugin(def);

                plugin.items[pluginKey] = def.label;

            }.bind(this));


            fields['plugin['+key+']'] = plugin;

        }.bind(this));

        this.fieldForm = new ka.FieldForm(this.main, fields, {
            allTableItems: true
        });

        this.pluginPropertyContainer = new Element('div', {
            'class': 'ka-field-plugin-options'
        }).inject(this.main);

        this.fieldForm.addEvent('change', function(){

            this.pluginPropertyContainer.getChildren().destroy();
            var module = this.fieldForm.getValue('module');
            var plugin = this.fieldForm.getValue('plugin['+module+']');

            if (!ka.settings.configs[module]){
                delete this.pluginPropertyForm;
                return;
            }

            var def = this.normalizePlugin(ka.settings.configs[module].plugins[plugin]);

            if (def && def.options){
                this.pluginPropertyForm = new ka.FieldForm(this.pluginPropertyContainer, def.options, {
                    allTableItems: true
                });
            } else {
                delete this.pluginPropertyForm;
            }

            this.fieldInstance.fireChange();

        }.bind(this));

        if (pValue && pValue.module){
            var value = {};
            value.module = pValue.module;
            value.plugin = {};
            value.plugin[value.module] = pValue.plugin;
            this.fieldForm.setValue(value);
        }

        this.fieldForm.fireEvent('change');
    },

    normalizePlugin: function(pPlugin){

        if (typeOf(pPlugin) != 'array') return pPlugin;
        var plugin = {};

        plugin.label   = pPlugin[0];
        plugin.options = pPlugin[1];

        return plugin;
    },

    setValue: function(pValue){
        if (typeOf(pValue) == 'string'){
            pValue = this.normalizeValue(pValue);
        }

        this.renderValue(pValue);
    },

    /**
     * since old kryn version stores the value as string
     * we need to convert it to the new object def.
     * @param {String} pValue
     * @return {Object}
     */
    normalizeValue: function(pValue){

        var module  = pValue.substr(0, pValue.indexOf('::'));
        var plugin  = pValue.substr(module.length+2, pValue.substr(module.length+2).indexOf('::'));
        var options = pValue.substr(module.length+plugin.length+4);

        options = JSON.validate(options) ? JSON.decode(options) : {};

        return {
            module: module,
            plugin: plugin,
            options: options
        };
    },

    getValue: function(){

        var plugin = '';
        var module = this.fieldForm.getValue('module');

        plugin += module;
        plugin += '::'+this.fieldForm.getValue('plugin['+module+']')

        plugin += '::';
        if (this.pluginPropertyForm){
            //plugin += JSON.encode(this.pluginPropertyForm.getValue());
        } else {
            plugin += '{}';
        }

        return plugin;
    }

});