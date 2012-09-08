ka.FieldTypes.ChildrenSwitcher = new Class({
    
    Extends: ka.FieldAbstract,

    createLayout: function(){

        this.fieldInstance.title.empty();

        this.toggler = new Element('a', {
            text: this.options.label,
            'class': 'icon-arrow-19',
            style: 'display: block; padding: 2px; cursor: pointer; position: relative; left: -5px;'
        }).inject(this.fieldInstance.title);

        this.fieldInstance.handleChildsMySelf = true;

        this.toggler.addEvent('click', this.fieldInstance.fireChange);
        this.toggler.addEvent('click', function(){
            this.setValue(!this.getValue());
        }.bind(this));

        this.value = this.options.value || this.options['default'];

        this.fieldInstance.addEvent('check-depends', function(){
            this.setValue(this.value);
        }.bind(this));

        /*function(){
            this._setValue( this.value==0?1:0)
        }.bind(this));

        //with check-depends we have this.childContainer
        this.addEvent('check-depends', function(){
            this._setValue(this.value);
        }.bind(this));

        this.checkbox.addEvent('change', this.fieldInstance.fireChange);
         */
    },

    setValue: function(pValue){
        this.value = pValue || 0;

        if (!this.fieldInstance.childContainer) return;

        if (!this.value){
            this.fieldInstance.childContainer.setStyle('display', 'none');
            this.toggler.set('class', 'icon-arrow-19');
        } else {
            this.fieldInstance.childContainer.setStyle('display', 'block');
            this.toggler.set('class', 'icon-arrow-17');
        }
    },

    getValue: function(){
        return this.value;
    }
});