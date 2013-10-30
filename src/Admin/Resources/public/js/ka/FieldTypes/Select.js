ka.FieldTypes.Select = new Class({

    Extends: ka.FieldAbstract,

    Statics: {
        asModel: true,
        options: {
            __info__: {
                type: 'label',
                label: 'Use static items, a store or a object.'
            },
            items: {
                label: t('static items'),
                desc: t('Use JSON notation. Array(key==label) or Object(key => label). Example: {"item1": "[[Item 1]]"} or ["Foo", "Bar", "Three"].')
            },
            store: {
                label: t('Store path'),
                desc: t('&lt;extKey&gt;/&lt;EntryPath&gt;, Example: publication/stores/news.')
            },
            multi: {
                label: t('Multiple selection'),
                desc: t('This field returns then an array.'),
                'default': false,
                type: 'checkbox'
            },
            combobox: {
                label: t('Combobox'),
                'default': false,
                desc: t('if you want to allow the user to enter a own value.'),
                type: 'checkbox'
            },
            object: {
                label: t('Objecy key'),
                combobox: true,
                type: 'objectKey',
                desc: t('The key of the object')
            }
        }
    },

    options: {
        inputWidth: 'auto',
        style: '',
        combobox: false,
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

    toElement: function() {
        return this.select.toElement();
    },

    getObject: function () {
        return this.select;
    },

    setValue: function (pValue, pInternal) {
        this.select.setValue(pValue, pInternal);
    },

    getValue: function () {
        return this.select.getValue();
    }
});