ka.FieldTypes.Input = ka.FieldTypes.Text = new Class({
    
    Extends: ka.FieldAbstract,

    Binds: ['replace', 'checkChange'],

    options: {
        maxLength: 255,
        inputWidth: '100%',

        /**
         * Can be an array like
        *   ['regex', 'modifier' 'replacement']
         * to replace the content of the input after 'keyup' and 'change'.
         *
         * @type {Array}
         */
        replace: null,

        modifiers: {
            'trim': function(v){ return v.replace(/^\s+|\s+$/g, ""); },
            'lower': function(v){ return v.toLowerCase(); },
            'phpfunction': function(v){ return v.replace(/[^a-zA-Z0-9_]/g, ''); },
            'underscore': function(v){ return v.replace(/([^a-z])/g, function($1){return "_"+$1.toLowerCase().replace(/[^a-z]/, '');}); },
            'camelcase': function(v){ return v.replace(/([^a-zA-Z0-9][a-z])/g, function($1){return $1.toUpperCase().replace(/[^a-zA-Z0-9]/,'');}); },
            'dash': function(v){ return v.replace(/([^a-zA-Z0-9])/g, function($1){return "-"+$1.toLowerCase().replace(/[^a-z]/, '');}); }
        }
    },

    /**
     * @internal
     * @type {String}
     */
    oldValue: null,

    createLayout: function(){

        this.wrapper = new Element('div', {
            styles: {
                'width': this.options.inputWidth,
                'margin': '2px'
            }
        }).inject(this.fieldInstance.fieldPanel);

        this.input = new Element('input', {
            'class': 'ka-Input',
            styles: {
                'width': '100%',
                'height': this.options.inputHeight
            },
            maxLength: this.options.maxLength
        }).inject(this.wrapper);

        this.input.addEvent('change', this.checkChange);
        this.input.addEvent('keyup', this.checkChange);

    },

    checkChange: function(){

        if (this.lastCheckChangeTimeout) clearTimeout(this.lastCheckChangeTimeout);
        this.lastCheckChangeTimeout = this._checkChange.delay(100, this);

    },

    _checkChange: function(){

        var range = this.input.getSelectedRange();

        if (typeOf(this.options.modifier) == 'string'){
            var modifiers = this.options.modifier.split('|');

            Array.each(modifiers, function(modifier){
                this.input.value = this.options.modifiers[modifier](this.input.value);
            }.bind(this));

        } else if (typeOf(this.options.modifier) == 'function'){
            this.input.value = this.options.modifier(this.input.value);
        }

        if (this.options.replace){
            this.replace();
        }

        if (document.activeElement == this.input)
            this.input.selectRange(range.start, range.end);

        if (this.oldValue !== this.input.value){
            this.fieldInstance.fireChange();
            this.oldValue = this.input.value;
        }
    },

    replace: function(){

        var regEx = new RegExp(this.options.replace[0], this.options.replace[1]);
        var oldValue = this.input.value;
        this.input.value = oldValue.replace(regEx, this.options.replace[2]);

    },

    setDisabled: function(pDisabled){
        this.input.disabled = pDisabled;
    },

    setValue: function(pValue){
        if (typeOf(pValue) == 'null') pValue = '';
        this.oldValue = pValue;
        this.input.value = pValue;
        this._checkChange();
    },

    getValue: function(){
        return this.input.value;
    }

});