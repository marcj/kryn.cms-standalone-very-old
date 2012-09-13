ka.FieldTypes.Datetime = new Class({
    
    Extends: ka.FieldAbstract,

    createLayout: function(){

        this.input = new Element('input', {
            'class': 'text ka-field-dateTime',
            type: 'text',
            style: 'width: 100%'
        }).inject(this.fieldInstance.fieldPanel);

        this.options.time = true;
        this.datePicker = new ka.DatePicker(this.input, this.options);

        if (this.options.inputWidth)
            this.input.setStyle('width', this.options.inputWidth);

        if (this.win) {
            this.win.addEvent('resize', this.datePicker.updatePos.bind(this.datePicker));
            this.win.addEvent('move', this.datePicker.updatePos.bind(this.datePicker));
        }

        this.datePicker.addEvent('change', function () {
            this.fireChange();
        }.bind(this));


        if (this.options['default']) {
            var time = new Date(this.field['default']=='now'?null:this.field['default']).getTime();
            this.setValue(time, true);
        }
    },

    setValue: function(pValue){
        this.datePicker.setTime((pVal != 0) ? pVal : false);
    },

    getValue: function(){
        return this.datePicker.getTime();
    }
});