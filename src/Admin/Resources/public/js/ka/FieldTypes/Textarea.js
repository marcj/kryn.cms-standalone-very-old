ka.FieldTypes.Textarea = new Class({

    Extends: ka.FieldTypes.Text,

    Statics: {
        asModel: true
    },

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

        if ('auto' === this.options.inputHeight) {
            this.input.style.overflowY = 'hidden';
            this.input.addEvent('change', this.updateHeight.bind(this));
            this.input.addEvent('keydown', this.updateHeight.bind(this));
            this.input.addEvent('keyup', this.updateHeight.bind(this));
            this.updateHeight();
        }

        this.input.addEvent('change', this.checkChange);
        this.input.addEvent('keyup', this.checkChange);
    },

    updateHeight: function() {
        var scrollHeight = this.input.getScrollSize().y;
        var height = this.input.getSize().y;

        height = scrollHeight > height ? scrollHeight+5 : height;
        this.input.style.height = Math.max(height, 50) + 'px';
    },

    setValue: function(value, internal) {
        this.parent(value, internal);
        this.updateHeight();
    }
});