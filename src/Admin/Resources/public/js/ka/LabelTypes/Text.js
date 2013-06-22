ka.LabelTypes.Text = new Class({
    Extends: ka.LabelAbstract,

    render: function(values) {
        var value = values[this.fieldId] || '';

        var clazz = this.originField.type.charAt(0).toUpperCase() + this.originField.type.slice(1);
        if ('Text' !== clazz && ka.LabelTypes[clazz]) {
            var obj = new ka.LabelTypes[clazz](this.originField, this.definition, this.fieldId, this.objectKey);
            value = obj.render(values);
        }

        return ka.htmlEntities('string' === typeOf(value) ? value : JSON.encode(value));
    }
});