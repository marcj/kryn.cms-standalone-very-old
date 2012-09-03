ka.FieldTypes.File = new Class({
   
    Extends: ka.FieldTypes.Object,

    initialize: function(pFieldInstance, pOptions){

        pOptions.withoutObjectWrapper = 1;
        pOptions.chooserOptions = {
            returnPath: 1,
            onlyLocal: 1
        };
        pOptions.objects = ['file'];

        this.parent(pFieldInstance, pOptions);
    }

});