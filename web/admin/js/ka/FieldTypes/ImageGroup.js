ka.FieldTypes.ImageGroup = new Class({
    
    Extends: ka.FieldAbstract,

    createLayout: function(){

        this.main = new Element('div', {
            style: 'padding: 5px;',
            'class': 'ka-field-imageGroup'
        }).inject(this.fieldInstance.fieldPanel);

        this.imageGroup = new ka.ImageGroup(this.input);

        this.imageGroupImages = {};

        Object.each(this.field.items, function (image, value) {
            this.imageGroupImages[ value ] = this.imageGroup.addButton(image.label, image.src);
        }.bind(this));

        this.imageGroup.addEvent('change', this.fieldInstance.fireChange);
    },

    setValue: function(pValue){
        Object.each(this.imageGroupImages, function (button, tvalue) {
            button.removeClass('ka-buttonGroup-item');
            if (pValue == tvalue) {
                button.addClass('ka-buttonGroup-item');
            }
        });
    },

    getValue: function(){
        var value = null;
        Object.each(this.imageGroupImages, function (button, tvalue) {
            if (button.hasClass('ka-buttonGroup-item')) {
                value = tvalue;
            }
        });

        return value;
    }
});