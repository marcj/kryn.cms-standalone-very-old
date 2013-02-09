ka.ContentTypes || (ka.ContentTypes = {});

ka.ContentTypes.Plugin = new Class({

    Extends: ka.ContentAbstract,

    icon : '&#xe271;',


    options: {

    },


    createLayout: function(){

        this.main = new Element('div', {
            'class': 'ka-content-plugin'
        }).inject(this.contentInstance);

        this.inner = new Element('div', {
            'class': 'ka-content-plugin-inner'
        }).inject(this.main);

        this.main.addEvent('click', this.openDialog.bind(this));

    },

    openDialog: function(){


        this.dialog = new ka.Dialog(document.body, {
            title: t('Edit plugin'),
            minWidth: '50%',
            minHeight: '50%'
        });


        this.dialog.setContent('<div style="height: 200px; margin: 50px; border: 1px solid black;">asd</div>');

        this.dialog.center();
        this.dialog.addButton('Cancel').addEvent('click', this.dialog.close);
        this.dialog.addButton('Apply').setButtonStyle('blue');

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
                'class': 'ka-content-plugin-title',
                text: ka.settings.configs[module].title
            }).inject(this.inner);

            new Element('div', {
                'class': 'ka-content-plugin-subtitle',
                text: pluginConfig[0]
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

