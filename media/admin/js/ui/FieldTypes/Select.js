ka.FieldTypes.Select = new Class({
    
    Extends: ka.FieldAbstract,

    options: {

        items: false, //array or object
        store: false, //string
        object: false, //for object chooser
        customValue: false //boolean

    },

    createLayout: function(){

        this.select = new ka.Select(this.fieldInstance.fieldPanel, this.options);
        this.select.addEvent('change', this.fieldInstance.fireChange);

    },

    setValue: function(pValue){
        this.select.setValue(pValue);
    },

    getValue: function(){
        return this.select.getValue();
    }
});