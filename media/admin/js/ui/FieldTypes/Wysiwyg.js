ka.FieldTypes.Wysiwyg = new Class({
    
    Extends: ka.FieldAbstract,

    value: '',

    createLayout: function(){
        //todo, implement WYSIWYG editor
    },

    setValue: function(pValue){
        this.value = pValue;
    },

    getValue: function(){
        return this.value;
    }
});