ka.FieldTypes.Files = new Class({
   
    Extends: ka.FieldTypes.Select,

    initialize: function(pFieldInstance, pOptions){

        pOptions.object = 'Core\\File';
        pOptions.objectBranch = pOptions.directory;
        pOptions.objectLabel = 'name';
        pOptions.labelTemplate = '{name}';

        this.parent(pFieldInstance, pOptions);
    }

});