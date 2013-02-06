ka.Content = new Class({

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

    },

    toElement: function(){
        return this.contentContainer || this.main;
    },

    loadTemplate: function(pValue){

        this.lastRq = new Request.JSON({url: _path+'admin/content-template', noCache: true,
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

        this.contentObject.setValue(pValue.content);

    }

});