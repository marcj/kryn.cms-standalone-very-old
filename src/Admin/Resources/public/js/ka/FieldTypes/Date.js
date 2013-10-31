ka.FieldTypes.Date = new Class({

    Extends: ka.FieldAbstract,

    Statics: {
        label: 'Date',
        isModel: true,
        options: {
            format: {
                type: 'text',
                label: 'Date format',
                help: 'admin/field-date-format'
            }
        }
    },

    createLayout: function () {
        this.wrapper = new Element('div', {
            'class': 'ka-input-wrapper',
            style: this.options.style,
            styles: {
                'width': this.options.inputWidth == '100%' ? null : this.options.inputWidth,
                'height': this.options.inputHeight
            }
        }).inject(this.fieldInstance.fieldPanel);

        this.input = new Element('input', {
            'class': 'ka-Input-text ka-Input-date',
            type: 'text',
            style: 'width: 100%'
        }).inject(this.wrapper);

        this.datePicker = new ka.DatePicker(this.input, this.options);

        if (this.options.inputWidth) {
            this.input.setStyle('width', this.options.inputWidth);
        }

        if (this.win) {
            this.win.addEvent('resize', this.datePicker.updatePos.bind(this.datePicker));
            this.win.addEvent('move', this.datePicker.updatePos.bind(this.datePicker));
        }

        this.datePicker.addEvent('change', function () {
            this.fireChange();
        }.bind(this));

        if (this.options['default']) {
            var time = new Date(this.field['default'] == 'now' ? null : this.field['default']).getTime();
            this.setValue(time, true);
        }
    },

    setValue: function (pValue) {
        this.datePicker.setTime((pVal != 0) ? pVal : false);
    },

    getValue: function () {
        return this.datePicker.getTime();
    }
});