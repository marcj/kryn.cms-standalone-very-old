ka.fieldProperty = new Class({

    Implements: [Events, Options],

    kaFields: {
        label: {
            label: t('Label'),
            desc: t('Surround the value with [[ and ]] to make it multilingual.'),
            type: 'text'
        },
        'type': {
            label: t('Type'),
            type: 'select',
            items: {
                text: t('Text'),
                password: t('Password'),
                number: t('Number'),
                checkbox: t('Checkbox'),
                page: t('Page'),
                file: t('File'),
                folder: t('Folder'),
                select: t('Select'),
                tab: t('Tab'),
                lang: t('Language select'),
                textlist: t('Textlist'),
                textarea: t('Textarea'),
                array: t('Array'),
                wysiwyg: t('Wysiwyg'),
                date: t('Date'),
                datetime: t('Datetime'),
                files: t('File list from folder'),
                filelist: t('File list (Attachments)'),
                layoutelement: t('Layout element'),
                headline: t('Headline'),
                info: t('Info'),
                label: t('Label'),
                html: t('Html'),
                imagegroup: t('Imagegroup'),
                custom: t('Custom'),
                childrenswitcher: t('Children switcher'),
                window_list: t('Framework windowList')
            },
            'depends': {

                //select
                '__info__': {
                    needValue: 'select',
                    type: 'label',
                    label: t('Use a store, a table, SQL or static items.')
                },
                'table': {
                    needValue: 'select',
                    label: t('Table name'),
                    desc: t('Start with / to use a table which is not defined in kryn or is in a different database.'),
                    type: 'text'
                },
                'sql': {
                    needValue: 'select',
                    label: t('SQL'),
                    desc: t('Please only select in your SQL the table_key and table_label.'),
                    type: 'text'
                },
                table_key: {
                    needValue: function(n){if(n!='')return true;else return false;},
                    againstField: ['table', 'sql'],
                    label: t('Table primary column')
                },
                table_label: {
                    needValue: function(n){if(n!='')return true;else return false;},
                    againstField: ['table', 'sql'],
                    label: t('Table label column')
                },
                items: {
                    needValue: 'select',
                    label: t('static items'),
                    desc: t('Use JSON notation. Array(key==label) or Object(key => label). Example: {"item1": "[[Item 1]]"} or ["Foo", "Bar", "Three"].')
                },

                //select, file and folder
                'multi': {
                    needValue: ['select', 'file', 'folder'],
                    label: t('Multiple selection'),
                    desc: t('This field returns then an array.'),
                    type: 'checkbox'
                },

                //tab
                tabFullPage: {
                    label: t('Full page'),
                    type: 'checkbox',
                    needValue: 'tab'
                },

                //textlist
                'doubles': {
                    needValue: 'textlist',
                    label: t('Allow double entries'),
                    type: 'checkbox'
                },

                //select,textlist
                'store': {
                    needValue: ['select', 'textlist'],
                    label: t('Store path'),
                    desc: t('<extKey>/<EntryPath>, Example: publication/stores/news.')
                },

                //files
                'withoutExtension': {
                    needValue: 'files',
                    type: 'checkbox',
                    label: t('File names without extensions'),
                    'default': 1
                },
                directory: {
                    needValue: 'files',
                    label: t('List files from this folder'),
                    desc: t('Relative from Kryn.cms installation folder.')
                }

            }
        },
        width: {
            label: t('Width'),
            desc: t('Use a px value or a % value. Example: 25%, 50, 35px')
        },
        primaryKey: {
            label: t('Primary key'),
            type: 'checkbox'
        },
        autoIncrement: {
            label: 'Auto increment?',
            desc: t('If no value is assigned the value will be increased by each insertion.'),
            type: 'checkbox',
            needValue: 'number',
            againstField: 'type'
        },
        __optional__: {
            label: t('Optional'),
            type: 'childrenswitcher',
            depends: {
                desc: {
                    label: t('Description (Optional)'),
                    type: 'text'
                },
                empty: {
                    label: t('Can be empty? (Optional)'),
                    type: 'checkbox',
                    'default': 1
                },
                'needValue': {
                    label: tc('kaFieldTable', 'Visibility condition (Optional)'),
                    desc: t("Shows this field only, if the field defined below or the parent field has the defined value. String, Array(use JSON notation), /regex/ or 'javascript:(value=='foo'||value.substr(0,4)=='lala')'")
                },
                againstField: {
                    label: tc('kaFieldTable', 'Visibility condition field (Optional)'),
                    desc: t("Define the key of another field if the condition should not against the parent. Use JSON notation. String or Array")
                },
                'length': {
                    needValue: ['text', 'password', 'number'],
                    againstField: 'type',
                    type: 'text',
                    label: t('Max value length. (Optional)')
                },
                'default': {
                    needValue: ['text','password', 'number', 'checkbox', 'select', 'date', 'datetime', 'file', 'folder'],
                    againstField: 'type',
                    type: 'text',
                    label: t('Default value. Use JSON notation. (Optional)')
                },
                'required_regexp': {
                    needValue: ['text','password', 'number', 'checkbox', 'select', 'date', 'datetime', 'file', 'folder'],
                    againstField: 'type',
                    type: 'text',
                    label: t('Required value as regular expression. (Optional)'),
                    desc: t('Example of an email-check: /^[^@]+@[^@]{3,}\.[^\.@0-9]{2,}$/')
                }
            }
        }
    },

    options: {
        addLabel: t('Add property'),
        withTableDefinition: false, //shows the 'Is primary key?' and 'Auto increment' fields
        withWidthField: false, //for column definition.
        withoutChildren: false, //deactivate children?
        tableitem_title_width: 330,
        allTableItems: true,
        withActionsImages: true,
        arrayKey: false //allows key like foo[bar], foo[barsen], foo[bar][sen]
    },


    childDiv: false,
    main: false,

    children: [], //instances of ka.fieldProperty

    initialize: function(pKey, pDefinition, pContainer, pOptions){

        this.setOptions(pOptions);

        if (!this.options.withTableDefinition){
            delete this.kaFields.primaryKey;
            delete this.kaFields.autoIncrement;
        }

        if (!this.options.withWidthField)
            delete this.kaFields.width;

        var self = this;

        this.main = new Element('div', {
            'class': 'ka-fieldTable-item',
            style: 'border-bottom: 1px solid silver; position: relative;'
        }).inject(pContainer);

        this.main.store('ka.fieldProperty', this);

        var header = new Element('div', {
            'class': 'ka-fieldTable-item-header'
        }).inject(this.main);

        this.main.store('definition', pDefinition || {});

        var count = pContainer.getElements('.ka-fieldTable-item').length-1;

        this.iKey = new Element('input', {
            value: pKey?pKey:'property_'+count,
            style: 'width: 155px',
            'class': 'text ka-fieldTable-item-key'
        })
        .addEvent('keyup', function(e){

            if (e.key.length > 1) return;
            var range = this.getSelectedRange();

            this.value = this.value.replace(' ', '_');
            if (self.options.arrayKey)
                this.value = this.value.replace(/[^a-zA-Z0-9_\-\[\]]/, '-');
            else
                this.value = this.value.replace(/[^a-zA-Z0-9_\-]/, '-');
            this.value = this.value.replace(/--+/, '-');

            this.selectRange(range.start, range.end);
        })
        .inject(header);

        if (this.options.withActionsImages){

            new Element('img', {
                src: _path+'inc/template/admin/images/icons/delete.png',
                title: t('Delete property'),
                style: 'cursor: pointer; position: relative; top: 3px;'
            })
            .addEvent('click', function(){
                this.win._confirm(t('Really delete?'), function(ok){
                    if(ok)
                        this.fireEvent('delete');
                }.bind(this));
            }.bind(this))
            .inject(header);

            new Element('img', {
                src: _path+'inc/template/admin/images/icons/arrow_up.png',
                title: t('Move up'),
                style: 'cursor: pointer; position: relative; top: 3px;'
            })
            .addEvent('click', function(){
                if (!this.main.getPrevious('.ka-fieldTable-item'))
                    return false;
                this.main.inject(this.main.getPrevious('.ka-fieldTable-item'), 'before');
            }.bind(this))
            .inject(header);


            new Element('img', {
                src: _path+'inc/template/admin/images/icons/arrow_down.png',
                title: t('Move down'),
                style: 'cursor: pointer; position: relative; top: 3px;'
            })
            .addEvent('click', function(){
                if (!this.main.getNext())
                    return false;
                this.main.inject(this.main.getNext(), 'after');
            }.bind(this))
            .inject(header);

        }

        var showDefinition = new Element('div', {
            'class': 'ka-fieldTable-showDefinition',
            style: 'width: 150px;'
        }).inject(header);

        var ch = new ka.Checkbox(showDefinition);
        ch.addEvent('change', function(){
            if(ch.getValue()){
                this.main.addClass('ka-fieldTable-showDefinition-show-sub');
            } else {
                this.main.removeClass('ka-fieldTable-showDefinition-show-sub');
            }
        }.bind(this));

        new Element('div',{
            style: 'position: absolute; left: 70px; top: 6px; color: gray;',
            text: t('Show definition')
        }).inject(showDefinition);

        if (!this.options.withTableDefinition) {
            var headerInfo = new Element('div', {
                text: t('Surround the key above with __ and __ (double underscore) to define a field which acts only as a user interface item and does not appear in the result.'),
                style: 'color: gray',
                'class': 'ka-fieldTable-key-info'
            }).inject(header);
        }

        var main = new Element('div',{'class': 'ka-fieldTable-definition',style: 'background-color: #e5e5e5'}).inject(this.main);

        var fieldContainer;

        if (this.options.allTableItems){
            var table = new Element('table', {
                width: '100%'
            }).inject( main );

           fieldContainer = new Element('tbody').inject(table);
        } else {
            fieldContainer = main;
        }

        this.kaParse = new ka.parse(fieldContainer, this.kaFields, {allTableItems:this.options.allTableItems, tableitem_title_width: this.options.tableitem_title_width}, {win:this.win});

        this.main.store('kaParse', this.kaParse);


        this.childDiv = new Element('div', {
            'class': 'ka-fieldTable-children'
        }).inject(this.main);

        this.main.store('dependDiv', this.childDiv);

        if (!this.options.withoutChildren){
            var btnDiv = Element('div', {
                style: 'padding-top: 3px; padding-left: 10px;'
            }).inject(this.main)

            new ka.Button(t('Add child'))
            .addEvent('click', function(){
                this.children.include(new ka.fieldProperty('child_key_'+(this.childDiv.getChildren().length+1), {}, this.childDiv, this.options));
            }.bind(this))
            .inject(btnDiv);
        }

        if (pDefinition && typeOf(pDefinition) == 'object'){
            //do some migration stuff and setValue

            this.setValue(pKey, pDefinition);
        }
    },

    getValue: function(){

        var key = this.iKey.value;
        if (!key) return;

        var kaParse = this.kaParse;
        var property = kaParse.getValue();

        Object.each(property, function(pval, pkey){
            if(pval == ''){
                delete property[pkey];
                return;
            }

            /*
             * Convert JSON notation to javascript objects
             */

            if (!kaParse.fields[pkey]) return;

            var type = kaParse.fields[pkey].field.type;
            if(type == 'text' || !type) {

                if(typeOf(pval) != 'string') return;

                var newItem = false;

                try {

                    //check if json array
                    if (pval.substr(0,1) == '[' && pval.substr(pval.length-1) == ']'&&
                        pval.substr(0,2) != '[[' && pval.substr(pval.length-2) != ']]')
                        newItem = JSON.decode(pval);

                    //check if json object
                    if (pval.substr(0,1) == '{' && pval.substr(pval.length-1,1) == '}')
                        newItem = JSON.decode(pval);

                } catch(e){}

                if (newItem)
                    property[pkey] = newItem;

            }

        }.bind(this));

        property.depends = {};

        this.children.each(function(child){
            var fieldProperty = child.retrieve('ka.fieldProperty');
            var value = fieldProperty.getValue();

            property.depends[value.key] = value.definition;
        });

        if (Object.getLength(property.depends) == 0)
            delete property.depends;

        return {
            key: key,
            definition: property
        };
    },


    setValue: function(pKey, pDefinition){

        if(pDefinition.type == 'select' && pDefinition.table_id){
            pDefinition.table_key = pDefinition.table_id+'';
            delete pDefinition.table_id;
        }

        if(pDefinition.type == 'select' && pDefinition.tableItems){
            if (typeOf(pDefinition.tableItems) == 'object')
                pDefinition.items = Object.clone(pDefinition.tableItems);

            if (typeOf(pDefinition.tableItems) == 'array')
                pDefinition.items = Array.clone(pDefinition.tableItems);

            delete pDefinition.tableItems;
        }

        if (typeOf(pDefinition.items) == 'array'){
            var first = pDefinition.items[0];
            if (typeOf(first) == 'object'){
                var newItems = {};
                Array.each(pDefinition.items, function(item){
                    newItems[ item[pDefinition.table_key] ] = item[pDefinition.table_label];
                });
                pDefinition.items = newItems;
            }
        }

        Object.each(pDefinition, function(value, valueKey){

            if (!this.kaParse.fields[valueKey]) return;

            var type = this.kaParse.fields[valueKey].field.type;
            if((type == 'text' || !type) && typeOf(value) != 'string')
                pDefinition[valueKey] = JSON.encode(value);

        }.bind(this));



        this.iKey.value = pKey;

        this.kaParse.setValue(pDefinition);

        delete this.children;

        this.children = [];
        this.childDiv.empty();

        if (pDefinition.depends){
            Object.each(pDefinition.depends, function(definition, key){

                this.children.include(new ka.fieldProperty(key, {}, this.childDiv, this.options));

            }.bind(this));

        }
    }

});