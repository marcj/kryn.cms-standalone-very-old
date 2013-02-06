ka.ContentTypes || (ka.ContentTypes = {});

ka.ContentTypes.Plugin = new Class({

    Extends: ka.ContentAbstract,

    icon : '&#xe271;',


    options: {

    },


    createLayout: function(){


    },

    getEditorConfig: function(){

    },

    setValue: function(pValue){
        this.value = pValue;
    },

    getValue: function(){
        return this.value;
    }

});

