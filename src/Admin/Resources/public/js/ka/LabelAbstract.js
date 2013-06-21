ka.LabelTypes = ka.LabelTypes || {};

ka.LabelAbstract = new Class({

    definition: {},

    initialize: function(definition){
        this.definition = definition;
    },

    getDefinition: function(){
        return this.definition;
    },

    setDefinition: function(definition){
        this.definition = definition;
    }

});