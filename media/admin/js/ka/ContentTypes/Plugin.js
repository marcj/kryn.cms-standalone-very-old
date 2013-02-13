ka.ContentTypes || (ka.ContentTypes = {});

ka.ContentTypes.Plugin = new Class({

    Binds: ['apply'],
    Extends: ka.ContentAbstract,

    icon : '&#xe271;',


    options: {

    },

    createLayout: function(){

        this.main = new Element('div', {
            'class': 'ka-normalize ka-content-plugin'
        }).inject(this.contentInstance);

        this.icon = new Element('div', {
            'class': 'ka-content-inner-icon icon-cube-2'
        }).inject(this.main);

        this.inner = new Element('div', {
            'class': 'ka-content-inner'
        }).inject(this.main);

        this.main.addEvent('click', this.openDialog.bind(this));

    },

    openDialog: function(){

        this.dialog = new ka.Dialog(document.body, {
            title: t('Edit plugin'),
            minWidth: '50%',
            minHeight: '50%'
        });

        this.cancelBtn = this.dialog.addButton('Cancel').addEvent('click', this.dialog.close);
        this.saveBtn   = this.dialog.addButton('Apply').setButtonStyle('blue').addEvent('click', this.apply);

        this.dialogPluginChoser = new ka.Field({
            type: 'plugin'
        }, this.dialog);

        this.dialogPluginChoser.setValue(this.value);

        this.dialogPluginChoser.addEvent('change', function(){
            this.dialog.fixBottom();
            this.dialog.center();
        }.bind(this));

        this.dialog.fixBottom();
        this.dialog.center();

    },

    apply: function(){


        this.dialog.close();
        return;

        this.value = this.dialogPluginChoser.getValue();

        this.renderValue();

        this.contentInstance.fireChange();

    },

    renderValue: function(){

        this.inner.empty();

        var module  = this.value.substr(0, this.value.indexOf('::'));
        var plugin  = this.value.substr(module.length+2, this.value.substr(module.length+2).indexOf('::'));
        var options = this.value.substr(module.length+plugin.length+4);

        if (JSON.validate(options)){
            options = JSON.decode(options);
        }

        if (ka.settings.configs[module] && ka.settings.configs[module].plugins && ka.settings.configs[module].plugins[plugin]){
            var pluginConfig = ka.settings.configs[module].plugins[plugin];

            new Element('div', {
                'class': 'ka-content-inner-title',
                text: ka.settings.configs[module].title
            }).inject(this.inner);

            new Element('div', {
                'class': 'ka-content-inner-subtitle',
                text: pluginConfig.label
            }).inject(this.inner);

            /*
            var optionsText = [];
            var text = '';
            var value = '';

            Array.each(pluginConfig[1], function(property, key){

                text  = property.label;
                value = options[key];


                optionsText.push()

            });


            new Element('div', {
                'class': 'ka-content-plugin-options',
                text: optionsText.join(', ')
            }).inject(this.inner);
            */

        } else {
            this.inner.set('text', tf('Plugin or extension not found: %s/%s', module, plugin));
        }

    },

    getEditorConfig: function(){

    },

    setValue: function(pValue){
        this.value = pValue;

        this.renderValue();
    },

    getValue: function(){
        return this.value;
    }

});

