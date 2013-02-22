ka.Content = new Class({

    Binds: ['onOver', 'onOut', 'remove', 'fireChange'],
    Implements: [Options, Events],

    options: {
    },

    slot: null,
    contentObject: null,
    currentType: null,
    currentTemplate: null,

    contentContainer: null,

    initialize: function(pContent, pSlot, pOptions){

        this.slot = pSlot;
        this.setOptions(pOptions);

        this.renderLayout();

        this.setValue(pContent);

    },

    renderLayout: function(){

        this.main = new Element('div', {
            'class': 'ka-content'
        }).inject(this.slot);

        this.main.kaContentInstance = this;

        this.actionBar = new Element('div', {
            'class': 'ka-normalize ka-content-actionBar'
        });

        this.addActionBarItems();

    },

    fireChange: function(){



    },

    addActionBarItems: function(){

        new Element('a', {
            html: '&#xe0c6;',
            href: 'javascript: ;',
            class: 'icon ka-content-actionBar-move',
            title: t('Move content')
        }).inject(this.actionBar);

        new Element('a', {
            html: '&#xe26b;',
            href: 'javascript: ;',
            title: t('Remove content'),
            class: 'icon'
        })
        .addEvent('click', this.remove)
        .inject(this.actionBar);

    },

    remove: function(){
        this.main.destroy();
        this.actionBar.destroy();
    },

    onOver: function(){
        this.actionBar.inject(this.main);
    },

    onOut: function(){
        this.actionBar.dispose();
    },

    toElement: function(){
        return this.contentContainer || this.main;
    },

    loadTemplate: function(pValue){

        this.lastRq = new Request.JSON({url: _path+'admin/content/template', noCache: true,
            onComplete: function(pResponse){

                this.main.empty();
                this.main.set('html', pResponse.data);

                this.contentContainer = this.main.getElement('.ka-content-container');

                this.currentTemplate = pValue.template;
                return this.setValue(pValue);

            }.bind(this)}).get({
                template: pValue.template
            });

    },

    focus: function(){
        if (this.contentObject){
            this.contentObject.focus();
            this.nextFocus = false;
        } else {
            this.nextFocus = true;
        }
    },

    getValue: function(){
        if (this.contentObject){
            this.value.content = this.contentObject.getValue();
        }

        return this.value;
    },

    setValue: function(pValue){

        if (!this.currentType || pValue.type != this.currentType || !this.currentTemplate ||
            this.currentTemplate != pValue.template){

            if (!this.currentTemplate || this.currentTemplate != pValue.template){
                return this.loadTemplate(pValue);
            }

            if (!ka.ContentTypes)
                throw 'No ka.ContentTypes loaded.';

            var clazz = ka.ContentTypes[pValue.type] || ka.ContentTypes[pValue.type.capitalize()];
            if (clazz){
                this.contentObject = new clazz(this);
            } else {
                throw tf('ka.ContentType `%s` not found.', pValue.type);
            }

            if (this.nextFocus){
                this.focus();
            }
            this.currentType = pValue.type;
        }

        this.value = pValue;
        this.contentObject.setValue(pValue.content);

    }

});