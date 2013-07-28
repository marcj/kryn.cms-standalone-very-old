ka.FieldTypes.Lang = new Class({

    Extends: ka.FieldTypes.Object,

    Statics: {
        asModel: true
    },

    initialize: function (pFieldInstance, pOptions) {
        pOptions.withoutObjectWrapper = 1;
        pOptions.objects = ['Core\\Language'];

        this.parent(pFieldInstance, pOptions);
    }

});