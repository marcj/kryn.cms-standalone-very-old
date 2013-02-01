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
        }).inject(document.hiddenElement);

        if (this.options['class'])
            this.main.addClass(this.options['class']);

        this.editor = CKEDITOR.inline(this.main);

        this.editor.on('instanceReady', this.editorReady.bind(this));

    },

    editorReady: function(){
        this.main.inject(this.fieldInstance.fieldPanel);
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