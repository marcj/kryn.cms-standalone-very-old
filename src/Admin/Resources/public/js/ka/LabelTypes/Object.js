ka.LabelTypes['Object'] = new Class({
    Extends: ka.LabelAbstract,
    
    options: {
        relationsAsArray: false
    },

    render: function(values) {

        var label, relation, tempValue;
        if (this.fieldId.indexOf('.') > 0) {
            relation = this.fieldId.split('.')[0];
            label = this.fieldId.split('.')[1];
        } else {
            //find label
            var def = ka.getObjectDefinition(this.objectKey);
            label = def.labelField;
        }

        if (typeOf(values[relation]) == 'object') {
            //to-one relation
            tempValue = {};
            if (this.options.relationsAsArray) {
                tempValue[label] = values[relation][label];
                return ka.htmlEntities(tempValue);
            } else {
                return ka.htmlEntities(values[relation] ? values[relation][label] : '');
            }
        }
        if (typeOf(values[relation]) == 'array') {
            //to-many relation
            //we join by pField['join'] char, default is ', '
            tempValue = [];
            Array.each(values[relation], function (relValue) {
                tempValue.push(relValue[label]);
            });
            var joined = tempValue.join(this.originField['join'] || ', ');
            if (this.options.relationsAsArray) {
                tempValue = {};
                tempValue[label] = joined;
                return ka.htmlEntities(tempValue);
            } else {
                return ka.htmlEntities(joined);
            }
        }
    }
});