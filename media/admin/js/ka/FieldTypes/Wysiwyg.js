ka.FieldTypes.Wysiwyg = new Class({
    
    Extends: ka.FieldAbstract,

    value: '',

    options: {

        'class': ''

    },

    createLayout: function(){

        this.fieldInstance.fieldPanel.set('html', '<div contenteditable="true" class="selectable ka-Field-wysiwyg"></div>');

        this.main = this.fieldInstance.fieldPanel.getElement('div');

        if (this.options['class'])
            this.main.addClass(this.options['class']);

        logger(this.main);
        (function(){
            CKEDITOR.inline(this.main);
        }.bind(this)).delay(500);
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