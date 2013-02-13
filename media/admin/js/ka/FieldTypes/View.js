ka.FieldTypes.View = new Class({
    
    Extends: ka.FieldTypes.Select,

    options: {

        inputWidth: '100%',
        module: '',
        directory: ''

    },

    initialize: function(pFieldInstance, pOptions){

        pOptions.object = 'Core\\View';
        pOptions.objectBranch = pOptions.directory;

        this.parent(pFieldInstance, pOptions);
    }
});