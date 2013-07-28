ka.FieldTypes.File = new Class({

    Extends: ka.FieldTypes.Object,

    Statics: {
        asModel: true,
        options: {
            returnPath: {
                label: 'Return the path',
                desc: 'Instead of the object id',
                type: 'checkbox',
                'default': false
            },
            onlyLocal: {
                label: 'Only local files',
                type: 'checkbox',
                'default': false
            }
        }
    },

    options: {
        returnPath: false,
        onlyLocal: false
    },

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