ka.LabelTypes.Select = new Class({
    Extends: ka.LabelAbstract,

    render: function(values) {
        var value = values[this.fieldId + '_' + this.definition.tableLabel] || values[this.fieldId + '__label'] || values[this.fieldId];
        return ka.htmlEntities('string' === typeOf(value) ? string : JSON.encode(value));
    }
});