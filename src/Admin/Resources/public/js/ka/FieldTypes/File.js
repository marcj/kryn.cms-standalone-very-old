ka.FieldTypes.File = new Class({

    Extends: ka.FieldTypes.Object,

    initialize: function (pFieldInstance, pOptions) {

        pOptions.withoutObjectWrapper = 1;
        pOptions.combobox = true;
        pOptions.browserOptions = {
            returnPath: 1,
            onlyLocal: 1
        };
        pOptions.objects = ['Core\\File'];

        this.parent(pFieldInstance, pOptions);
    }

});