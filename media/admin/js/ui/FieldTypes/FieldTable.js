ka.FieldTypes.FieldTable = new Class({
    
    Extends: ka.FieldAbstract,

    createLayout: function(){

        this.fieldTable = new ka.FieldTable(this.fieldInstance.fieldPanel, this.win, this.options);

        this.fieldTable.addEvent('change', this.fieldInstance.fireChange);

    },

    setValue: function(pValue){
        this.fieldTable.setValue(pValue);
    },

    getValue: function(){
        return this.fieldTable.getValue();
    }
});