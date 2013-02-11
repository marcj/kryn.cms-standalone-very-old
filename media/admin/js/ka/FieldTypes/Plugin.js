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

            var def = this.normalizePlugin(ka.settings.configs[module].plugins[plugin]);

            this.pluginPropertyForm = new ka.FieldForm(this.pluginPropertyContainer, def.options, {
                allTableItems: true
            });

            this.fieldInstance.fireChange();

        }.bind(this));

        this.fieldForm.fireEvent('change');
    },

    normalizePlugin: function(pPlugin){

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
        //todo
    }

});