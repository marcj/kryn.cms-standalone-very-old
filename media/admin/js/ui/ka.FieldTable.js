ka.FieldTable = new Class({

    Implements: [Options, Events],

    Binds: ['fireChange'],

    options: {
        addLabel: t('Add property'),
        withTableDefinition: false, //shows the 'Is primary key?' and 'Auto increment' fields
        asFrameworkColumn: false, //for column definition, with width field. without the optional stuff and limited range of types
        asFrameworkSearch: false, //Remove some option fields, like 'visibility condition', 'can be empty', etc
        withoutChildren: false, //deactivate children?
        tableitem_title_width: 330,
        allTableItems: true,
        allSmall: false,
        withActionsImages: true,

        fieldTypes: false, //if as array defined, we only have types which are in this list
        fieldTypesBlacklist: false, //if as array defined, we only have types which are not in this list

        noActAsTableField: false, //Remove the field 'Acts as a table item'
        asFrameworkFieldDefinition: false, //means for usage in ka.Parse (and therefore in adminWindowEdit/Add), delete some relation stuff
        arrayKey: false //allows key like foo[bar], foo[barsen], foo[bar][sen]
    },

    container: false,

    initialize: function(pContainer, pWin, pOptions){

        this.setOptions(pOptions);

        this.container = pContainer;
        this.win = pWin;

        this.header = new Element('div', {
            style: 'background-color: #d1d1d1; padding: 2px; height: 27px; position: relative; border-bottom: 1px solid silver;'
        }).inject(this.container);

        this.showDefinitions = new ka.Checkbox(this.header);
        document.id(this.showDefinitions).setStyles({
            position: 'absolute',
            left: 5, top: 3
        });

        new Element('div', {
            text: tc('kaFieldTable', 'Show definitions'),
            style: 'position: absolute; left: 72px; top: 9px; color: white; font-weight: bold; font-size: 11px;'
        }).inject(this.header);

        new Element('div', {style: 'clear: both'}).inject(this.header);

        this.showDefinitions.addEvent('change', function(){
            if (this.showDefinitions.getValue()){
                this.itemContainer.removeClass('ka-fieldTable-hide-definition')
            } else {
                this.itemContainer.addClass('ka-fieldTable-hide-definition')
            }
        }.bind(this));

        this.itemContainer = new Element('div', {
            style: 'background-color: #d6d6d6;',
            'class': 'ka-fieldTable-hide-definition'
        }).inject(this.container);

        this.footer = new Element('div', {
            style: 'background-color: #d1d1d1; padding: 2px; height: 24px;'
        }).inject(this.container);

        new ka.Button(this.options.addLabel)
        .addEvent('click', function(){
            this.add(null,null, this.itemContainer);
        }.bind(this))
        .inject(this.footer);
    },

    fireChange: function(){
        this.fireEvent('change');
    },

    getValue: function(){

        var result = {};

        this.itemContainer.getChildren('.ka-fieldTable-item').each(function(item){

            var fieldProperty = item.retrieve('ka.FieldProperty');
            var value = fieldProperty.getValue();

            result[value.key] = value.definition;

        }.bind(this));

        return result;
    },

    setValue: function(pValue){

        if (typeOf(pValue) == 'object'){
            Object.each(pValue, function(property,key){
                this.add(key, property, this.itemContainer, this.options)
            }.bind(this));

        }
    },

    add: function(pKey, pDefinition, pContainer){

        var fieldProperty = new ka.FieldProperty(pKey, pDefinition, pContainer, this.options, this.win);
        fieldProperty.addEvent('change', this.fireChange);

        return fieldProperty;

    }

})