ka.FieldTypes.Wysiwyg = new Class({
    
    Extends: ka.FieldAbstract,

    value: '',

    options: {

        'class': ''

    },

    createLayout: function(){

        this.main = new Element('div', {
            contentEditable: true,
            'class': 'selectable ka-Field-wysiwyg'
        }).inject(this.fieldInstance.fieldPanel);

        if (this.options['class'])
            this.main.addClass(this.options['class']);

        CKEDITOR.inline(this.main);
    },

    toElement: function(){
        return this.main;
    },

    setValue: function(pValue){
        this.value = pValue;
    },

    getValue: function(){
        return this.value;
    }
});