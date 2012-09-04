ka.FieldTypes.Label = new Class({
    
    Extends: ka.FieldAbstract,

    container: null,

    getContainer: function(){
        if (this.container) return this.container;

        this.container = this.fieldInstance.fieldPanel;

        if (this.fieldInstance.main.get('colspan') == 2){
            this.container = this.fieldInstance.main;
        } else if (this.fieldInstance.main.get('tag') == 'td') {
            this.fieldInstance.main.destroy();
            this.fieldInstance.title.set('colspan', 2);
            this.fieldInstance.title.set('width');
            this.container = this.fieldInstance.title;
        }

        return this.container;
    },

    createLayout: function(){
        this.setValue(this.options.label);
    },

    setValue: function(pValue){

        if (typeOf(pValue) == 'null') return;

        this.getContainer().set('text', pValue);
        if (this.options.desc){
            new Element('div', {
                'class': 'desc',
                html: this.options.desc
            }).inject(this.getContainer());
        }
    },

    getValue: function(){
        return this.getContainer().get('text');
    }
});