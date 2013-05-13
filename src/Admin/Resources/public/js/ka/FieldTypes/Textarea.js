ka.FieldTypes.Textarea = new Class({

    Extends: ka.FieldTypes.Input,

    options: {
        inputWidth: '100%',
        inputHeight: '50px'
    },

    createLayout: function () {

        this.wrapper = new Element('div', {
            style: this.options.style,
            'class': 'ka-input-wrapper',
            styles: {
                'width': this.options.inputWidth == '100%' ? null : this.options.inputWidth
            }
        }).inject(this.fieldInstance.fieldPanel);

        this.input = new Element('textarea', {
            'class': 'ka-Input-text',
            styles: {
                'width': '100%',
                'height': this.options.inputHeight
            }
        }).inject(this.wrapper);

        this.input.addEvent('change', this.checkChange);
        this.input.addEvent('keyup', this.checkChange);
    }
});