ka.FieldTypes.View = new Class({
    
    Extends: ka.FieldTypes.Select,

    options: {

        inputWidth: '100%',
        module: '',
        directory: ''

    },

    initialize: function(pFieldInstance, pOptions){

        pOptions.object = 'Core\\View';
        pOptions.objectBranch = pOptions.directory ? pOptions.directory : true;

        this.parent(pFieldInstance, pOptions);
    },


    getValue: function(){
        var value = this.parent();
        return value.substr(this.options.directory.length);
    }
});