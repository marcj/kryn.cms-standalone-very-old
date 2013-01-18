ka.FieldTypes.Select = new Class({
    
    Extends: ka.FieldAbstract,

    options: {

        inputWidth: '100%',

        items: false, //array or object
        store: false, //string
        object: false, //for object chooser
        customValue: false //boolean

    },

    createLayout: function(){

        this.wrapper = new Element('div', {
            'class': 'ka-Select-wrapper',
            style: this.options.style,
            styles: {
                'width': this.options.inputWidth=='100%'?null:this.options.inputWidth,
                'height': this.options.inputHeight
            }
        }).inject(this.fieldInstance.fieldPanel);


        this.select = new ka.Select(this.wrapper, this.options);

        this.select.addEvent('change', this.fieldInstance.fireChange);

    },

    setValue: function(pValue){
        this.select.setValue(pValue);
    },

    getValue: function(){
        return this.select.getValue();
    }
});