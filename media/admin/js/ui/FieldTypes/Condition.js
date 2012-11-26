ka.FieldTypes.Condition = new Class({
    
    Extends: ka.FieldAbstract,

    options: {
        object: null,
        field: null,
        startWith: 0
    },

    dateConditions: ['= NOW()', '!=  NOW()', '<  NOW()', '>  NOW()', '<=  NOW()', '>=  NOW()'],

    createLayout: function(){

        this.main = new Element('div', {
            'class': 'ka-field-condition-container'
        }).inject(this.fieldInstance.fieldPanel);

        new ka.Button(t('Add condition'))
        .addEvent('click', this.addCondition.bind(this, this.main))
        .inject(this.fieldInstance.fieldPanel);

        new ka.Button(t('Add group'))
        .addEvent('click', this.addGroup.bind(this, this.main))
        .inject(this.fieldInstance.fieldPanel);

        if (this.options.startWith){
            for(var i=0; i<this.options.startWith;i++)
                this.addCondition(this.main);
        }

    },
    
   reRender: function(pTarget){

        pTarget.getChildren().removeClass('ka-field-condition-withoutRel');

        var first = pTarget.getFirst();
        if (first) first.addClass('ka-field-condition-withoutRel');

    },

    addCondition: function(pTarget, pValues, pCondition){

        var div = new Element('div', {
            'class': 'ka-field-condition-item'
        }).inject(pTarget);

        var table = new Element('table', {
            style: 'width: 100%; table-layout: fixed; background-color: transparent;'
        }).inject(div);

        var tbody = new Element('tbody').inject(table);
        var tr = new Element('tr').inject(tbody);

        new Element('td', {
            'class': 'ka-field-condition-leftBracket',
            text: '('
        }).inject(tr);

        var td = new Element('td', {style: 'width: 40px', 'class': 'ka-field-condition-relContainer'}).inject(tr);

        var relSelect = new ka.Select(td);
        document.id(relSelect).setStyle('width', '100%');
        relSelect.add('AND', 'AND');
        relSelect.add('OR', 'OR');

        div.relSelect = relSelect;

        if (pCondition)
            relSelect.setValue(pCondition.toUpperCase());

        var td = new Element('td', {style: 'width: 25%'}).inject(tr);

        if (this.options.fields || this.options.field){
            div.iLeft = new ka.Select(td, {
                customValue: true
            });

            document.id(div.iLeft).setStyle('width', '100%');

            objectDefinition = ka.getObjectDefinition(this.options.object);

            if (this.options.field){

                div.iLeft.add(this.options.field, objectDefinition.fields[this.options.field].label||this.options.field);
                div.iLeft.setEnabled(false);

            } else {
                Object.each(objectDefinition.fields, function(def, key){
                    div.iLeft.add(key, def.label||key);
                }.bind(this));
            }

        } else {
            div.iLeft = new ka.Field({type: 'text'}, td);
        }


        if (pValues)
            div.iLeft.setValue(pValues[0]);

        var td = new Element('td', {style: 'width: 41px; text-align: center'}).inject(tr);
        var select = new ka.Select(td);
        div.iMiddle = select;

        document.id(select).setStyle('width', '100%');

        ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN', 'NOT IN', 'REGEXP',
            '= CURRENT_USER', '!= CURRENT_USER'].each(function(item){
            select.add(item, item);
        });

        if (pValues)
            select.setValue(pValues[1]);

        div.rightTd = new Element('td', {style: 'width: 25%'}).inject(tr);
        div.iRight = new Element('input', {
            'class': 'text',
            style: 'width: 100%',
            value: pValues?pValues[2]:''
        }).inject(div.rightTd);
        div.iRight.getValue = function(){return this.value;};

        if (this.options.fields || this.options.field){
            div.iLeft.addEvent('change', this.updateRightTdField.bind(this, div));
            div.iMiddle.addEvent('change', this.updateRightTdField.bind(this, div));

            this.updateRightTdField(div);
        }

        var actions = new Element('td', {style: 'width: '+parseInt((16*4)+3)+'px'}).inject(tr);

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_up.png'})
        .addEvent('click', function(){
            if (div.getPrevious()){
                div.inject(div.getPrevious(), 'before');
                this.reRender(pTarget);
            }
        }.bind(this))
        .inject(actions);
        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_down.png'})
        .addEvent('click', function(){
            if (div.getNext()){
                div.inject(div.getNext(), 'after');
                this.reRender(pTarget);
            }
        }.bind(this))
        .inject(actions);

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/delete.png'})
        .addEvent('click', function(){
            this.win._confirm(t('Really delete?'), function(a){
                if (!a) return;
                div.destroy();
                this.reRender(pTarget);
            });
        }.bind(this))
        .inject(actions);

        new Element('td', {
            'class': 'ka-field-condition-leftBracket',
            text: ')'
        }).inject(tr);

        this.reRender(pTarget);

    },

    updateRightTdField: function(div){

        var chosenField = div.iLeft.getValue();

        var fieldDefinition = Object.clone(objectDefinition.fields[chosenField]);

        if (div.iRight)
            var backupedValue = div.iRight.getValue();

        delete div.iRight;

        div.rightTd.empty();

        if (fieldDefinition.primaryKey){
            if (['=', '!=', 'IN', 'NOT IN'].contains(div.iMiddle.getValue())){
                    fieldDefinition = {
                        type: 'object',
                        object: this.options.object,
                        withoutObjectWrapper: true
                    };

                if (div.iMiddle.getValue() == 'IN'){
                    fieldDefinition.multi = 1;
                }
            } else {
                fieldDefinition.type = 'text';
            }
        }

        if (div.iMiddle.getValue() == 'IN' || div.iMiddle.getValue() == 'NOT IN'){
            if (fieldDefinition.type == 'select')
                fieldDefinition.type = 'textlist';
            else
                fieldDefinition.multi = 1;
        }

        if (['LIKE', 'REGEXP'].contains(div.iMiddle.getValue())){
            fieldDefinition = {type: 'text'};
        }

        if (fieldDefinition.type == 'object' && fieldDefinition.object == 'user'){
            ['= CURRENT_USER', '!= CURRENT_USER'].each(function(item){
                div.iMiddle.showOption(item);
            });
        } else {
            ['= CURRENT_USER', '!= CURRENT_USER'].each(function(item){
                div.iMiddle.hideOption(item);
            });
        }

        if (fieldDefinition.type == 'date'|| fieldDefinition.type == 'datetime'){
            this.dateConditions.each(function(item){div.iMiddle.add(item);});
        } else {
            this.dateConditions.each(function(item){div.iMiddle.remove(item);});
        }

        fieldDefinition.noWrapper = true;
        fieldDefinition.fieldWidth = '100%';

        if (!this.dateConditions.contains(div.iMiddle.getValue())){

            div.iRight = new ka.Field(
                fieldDefinition, div.rightTd
            );

            div.iRight.code = div.iMiddle.getValue()+'_'+chosenField;

            if (backupedValue)
                div.iRight.setValue(backupedValue);
        }

    },


    addGroup: function(pTarget, pValues, pCondition){

        var div = new Element('div', {
            'class': 'ka-field-condition-group'
        }).inject(pTarget);

        var relContainer = new Element('span', {
            'class': 'ka-field-condition-relContainer',
            style: 'position: absolute; left: -52px;'
        }).inject(div);

        var relSelect = new ka.Select(relContainer);
        document.id(relSelect).setStyle('width', '47px');
        relSelect.add('AND', 'AND');
        relSelect.add('OR', 'OR');
        div.relSelect = relSelect;

        if (pCondition)
            relSelect.setValue(pCondition.toUpperCase());

        var con = new Element('div', {
            'class': 'ka-field-condition-container'
        }).inject(div);
        div.container = con;

        new ka.Button(t('Add condition'))
        .addEvent('click', this.addCondition.bind(this, con))
        .inject(con, 'before');

        new ka.Button(t('Add group'))
        .addEvent('click', this.addGroup.bind(this, con))
        .inject(con, 'before');

        var actions = new Element('span', {style: 'position: relative; top: 3px; width: '+parseInt((16*4)+3)+'px'}).inject(con, 'before');

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_up.png'})
            .addEvent('click', function(){
            if (div.getPrevious()){
                div.inject(div.getPrevious(), 'before');
                this.reRender(pTarget);
            }
        }.bind(this))
        .inject(actions);

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_down.png'})
            .addEvent('click', function(){
            if (div.getNext()){
                div.inject(div.getNext(), 'after');
                this.reRender(pTarget);
            }
        }.bind(this))
        .inject(actions);

        new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/delete.png'})
        .addEvent('click', function(){
            this.win._confirm(t('Really delete?'), function(a){
                if (!a) return;
                div.destroy();
                this.reRender(pTarget);
            });
        }.bind(this))
        .inject(actions);

        this.reRender(pTarget);

        this.renderValues(pValues, con);
    },

    renderValues: function (pValue, pTarget, pLastRel){
        if (typeOf(pValue) == 'array'){

            var lastRel = pLastRel || '';

            Array.each(pValue, function(item){

                if (typeOf(item) == 'array' && typeOf(item[0]) == 'array'){
                    //item is a group
                    this.addGroup(pTarget, item, lastRel);

                } else if(typeOf(item) == 'array'){
                    //item is a condition
                    this.addCondition(pTarget, item, lastRel);

                } else if(typeOf(item) == 'string'){
                    lastRel = item;
                }
            }.bind(this));
        }
    },


    setValue: function(pValue){
        this.main.empty();

        if (typeOf(pValue) == 'string'){
            try {
                pValue = JSON.decode(pValue);
            } catch(e){

            }
        }

        if(typeOf(pValue) == 'array' && typeOf(pValue[0]) == 'string')
            pValue = [pValue];

        if (typeOf(pValue) == 'array'){
            this.renderValues(pValue, this.main);
        }

    },

    extractValues: function(pTarget){

        var result = [];

        pTarget.getChildren().each(function(item){

            if (item.hasClass('ka-field-condition-item')){

                if (!item.hasClass('ka-field-condition-withoutRel'))
                    result.push(item.relSelect.getValue());

                result.push([
                    item.iLeft.getValue(),
                    item.iMiddle.getValue(),
                    item.iRight.getValue()
                ]);
            }

            if (item.hasClass('ka-field-condition-group')){
                if (!item.hasClass('ka-field-condition-withoutRel'))
                    result.push(item.relSelect.getValue());
                result.push(extractValues(item.container));
            }

        });

        return result;
    },

    getValue: function(){
        return this.extractValues(this.main);
    }

});