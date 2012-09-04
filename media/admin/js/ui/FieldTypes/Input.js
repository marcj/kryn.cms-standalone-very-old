ka.FieldTypes.Text = ka.FieldTypes.Input = new Class({
    
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
        replace: null
    },

    /**
     * @internal
     * @type {String}
     */
    oldValue: null,

    createLayout: function(){

        this.input = new Element('input', {
            'class': 'ka-Input',
            styles: {
                'width': this.options.inputWidth,
                'height': this.options.inputHeight
            },
            maxLength: this.options.maxLength
        }).inject(this.fieldInstance.fieldPanel);


        if (this.options.replace){
            this.input.addEvent('change', this.replace);
            this.input.addEvent('keyup', this.replace);
        }

        this.input.addEvent('change', this.checkChange);
        this.input.addEvent('keyup', this.checkChange);

    },

    checkChange: function(){
        if (this.oldValue !== this.input.value){
            this.fieldInstance.fireChange();
            this.oldValue = this.input.value;
        }
    },

    replace: function(){

        var regEx = new RegExp(this.options.replace[0], this.options.replace[1]);
        var oldValue = this.input.value;
        this.input.value = oldValue.replace(regEx, this.options.replace[0]);

        this.checkChange();
    },

    setDisabled: function(pDisabled){
        this.input.disabled = pDisabled;
    },

    setValue: function(pValue){
        if (typeOf(pValue) == 'null') pValue = '';
        this.oldValue = pValue;
        this.input.value = pValue;
    },

    getValue: function(){
        return this.input.value;
    }

});