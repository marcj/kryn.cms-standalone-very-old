ka.FieldTypes.Label = new Class({

    Extends: ka.FieldAbstract,

    container: null,

    Statics: {
        options: {
            label: {
                label: 'Label',
                type: 'text'
            }
        }
    },

    createLayout: function () {
        //remove main if ka.Field is a table item
        if (this.fieldInstance.main.get('tag') == 'td') {
            this.fieldInstance.main.destroy();

            this.fieldInstance.title.set('colspan', 2);
            this.fieldInstance.title.set('width');
        }

        this.setValue(this.options.label);
    },

    setValue: function (pValue) {

        if (typeOf(pValue) == 'null') {
            return;
        }

        this.fieldInstance.titleText.set('text', pValue);
    },

    getValue: function () {
        return this.fieldInstance.titleText.get('text');
    }
});