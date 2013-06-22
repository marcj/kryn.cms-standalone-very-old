ka.FieldTypes.Select = new Class({

    Extends: ka.FieldAbstract,

    options: {
        inputWidth: 'auto',
        style: '',

        items: false, //array or object
        store: false, //string
        object: false //for object chooser

    },

    createLayout: function () {

        if (typeOf(this.options.inputWidth) == 'number' || (typeOf(this.options.inputWidth) == 'string' &&
            this.options.inputWidth.replace('px', '') &&
            this.options.inputWidth.search(/[^0-9]/) === -1)) {
            this.options.inputWidth -= 2;
        }

        this.select = new ka.Select(this.fieldInstance.fieldPanel, this.options);

        if (this.options.inputWidth && 'auto' !== this.options.inputWidth) {
            document.id(this.select).setStyle('width', this.options.inputWidth);
        }

        this.select.addEvent('change', this.fieldInstance.fireChange);

    },

    getObject: function () {
        return this.select;
    },

    setValue: function (pValue) {
        this.select.setValue(pValue);
    },

    getValue: function () {
        return this.select.getValue();
    }
});