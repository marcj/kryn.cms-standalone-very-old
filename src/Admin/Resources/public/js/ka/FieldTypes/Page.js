ka.FieldTypes.Page = new Class({

    Extends: ka.FieldTypes.Object,

    Statics: {
        asModel: true
    },

    initialize: function (pFieldInstance, pOptions) {
        pOptions.withoutObjectWrapper = 1;
        pOptions.objects = ['Core\\Node'];

        this.parent(pFieldInstance, pOptions);
    }

});