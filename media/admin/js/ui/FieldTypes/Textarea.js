ka.FieldTypes.Textarea = new Class({
    
    Extends: ka.FieldTypes.Input,

    createLayout: function(){
        this.input = new Element('textarea', {
            'class': 'ka-Input',
            styles: {
                'width': this.options.inputWidth,
                'height': this.options.inputHeight
            }
        }).inject(this.fieldInstance.fieldPanel);
    }
});