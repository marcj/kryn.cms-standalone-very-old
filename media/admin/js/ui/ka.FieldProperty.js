ka.FieldProperty = new Class({

    Implements: [Events, Options],

    Binds: ['fireChange'],

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
                imageGroup: t('Imagegroup'),
                checkboxgroup: t('Checkboxgroup'),

                page: t('Page'),
                file: t('File'),
                folder: t('Folder'),
                object: t('Object'),

                select: t('Select'),
                textlist: t('Textlist'),
                lang: t('Language select'),

                predefined: t('Predefined'),

                array: t('Ka.Field Array'),
                properties: t('Properties (multi array class)'),
                fieldtable: t('Ka.Field table'),
                
                fieldCondition: t('Field Condition'),
                objectCondition: t('Object Condition'),

                textarea: t('Textarea'),
                wysiwyg: t('Wysiwyg'),
                layoutElement: t('Layout element'),
                codemirror: t('CodeMirror (sourcecode editor)'),

                date: t('Date'),
                datetime: t('Datetime'),

                files: t('File select from folder'),
                //filelist: t('File list (Attachments)'),

                tab: t('Tab'),
                headline: t('Headline'),
                info: t('Info'),
                label: t('Label'),
                html: t('Html'),
                childrenSwitcher: t('Children switcher'),

                custom: t('Custom')
                //,
                //windowlist: t('Framework windowList')
            },
            'depends': {

                //datetime, date
                format: {
                    type: 'text',
                    label: t('Date format (Optional)'),
                    help: 'admin/field-date-format',
                    needValue: ['date', 'datetime'],
                    againstField: 'type',
                    inputWidth: 150
                },

                //array
                withOrder: {
                    type: 'checkbox',
                    label: t('With order possibility'),
                    needValue: 'array',
                    againstField: 'type'
                },

                columns: {
                    label: t('Columns'),
                    needValue: 'array',
                    againstField: 'type',

                    type: 'fieldTable',
                    options: {
                        asFrameworkColumn: true,
                        withoutChildren: true,
                        tableitem_title_width: 200,
                        addLabel: t('Add column')
                    }
                },

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
                __or__: {
                    needValue: 'select',
                    type: 'label',
                    label: t('- or -')
                },
                'sql': {
                    needValue: 'select',
                    label: t('SQL'),
                    desc: t('Please only select in your SQL the table_key and table_label from below.'),
                    type: 'text'
                },
                table_key: {
                    needValue: 'select',
                    label: t('Table primary column')
                },
                table_label: {
                    needValue: 'select',
                    label: t('Table label column')
                },
                items: {
                    needValue: ['select', 'checkboxgroup', 'imagegroup'],
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

                //object
                'object': {
                    needValue: ['object', 'predefined', 'fieldcondition', 'objectcondition'],
                    label: t('Objecy key'),
                    desc: t('The key of the object')
                },
                'field': {
                    needValue: ['predefined', 'fieldcondition'],
                    label: t('Field key'),
                    desc: t('The key of the field')
                },
                'objectLabel': {
                    needValue: 'object',
                    label: t('Object label field (Optional)'),
                    desc: t('The key of the field which should be used as label')
                },

                'objectRelation': {
                    label: t('Relation'),
                    needValue: 'object',
                    type: 'select',
                    items: {
                        '1ToN': 'One to Many',
                        'nToM': 'Many to Many'
                    }
                },

                'objectRelationTable': {
                    needValue: 'nToM',
                    againstField: 'objectRelation',
                    label :t('Relation table name (Optional)'),
                    desc: t('The columns of this table are based on the primary keys of left and right table. Propel ORM generates a new model based on this value.')
                },
                
                'objectRelationPhpName': {
                    needValue: 'nToM',
                    againstField: 'objectRelation',
                    label: t('Relation table php name (Optional)'),
                    desc: t('Default is the camelCased table name.')
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
                    desc: t('&lt;extKey&gt;/&lt;EntryPath&gt;, Example: publication/stores/news.')
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
            input_width: 100,
            desc: t('Use a px value or a % value. Example: 25%, 50, 35px')
        },
        primaryKey: {
            needValue: ['text', 'password', 'number', 'checkbox', 'select', 'date', 'object', 'datetime', 'file', 'folder', 'page'],
            againstField: 'type',
            label: t('Primary key'),
            'default': false,
            type: 'checkbox'
        },
        autoIncrement: {
            label: 'Auto increment?',
            desc: t('If no value is assigned the value will be increased by each insertion.'),
            type: 'checkbox',
            'default': false,
            needValue: 'number',
            againstField: 'type'
        },
        __optional__: {
            label: t('Optional'),
            type: 'childrenSwitcher',
            depends: {
                desc: {
                    label: t('Description (Optional)'),
                    type: 'text'
                },
                required: {
                    label: t('Required field? (Optional)'),
                    type: 'checkbox',
                    'default': false
                },
                inputWidth: {
                    label: t('Input element width (Optional)'),
                    needValue: ['text', 'number', 'password', 'object', 'file', 'folder', 'page', 'domain', 'datetime', 'date'],
                    againstField: 'type',
                    type: 'text'
                },
                inputHeight: {
                    label: t('Input element height (Optional)'),
                    needValue: ['textarea', 'codemirror'],
                    againstField: 'type',
                    type: 'text'
                },
                maxlength: {
                    label: t('Max length (Optional)'),
                    needValue: ['text', 'number', 'password'],
                    againstField: 'type',
                    type: 'text'
                },
                target: {
                    label: t('Inject to target (Optional)'),
                    desc: t('If your tab has a own layout.'),
                    type: 'text'
                },
                'needValue': {
                    label: tc('kaFieldTable', 'Visibility condition (Optional)'),
                    desc: t("Shows this field only, if the field defined below or the parent field has the defined value. String, JSON notation for arrays and objects, /regex/ or 'javascript:(value=='foo'||value.substr(0,4)=='lala')'")
                },
                againstField: {
                    label: tc('kaFieldTable', 'Visibility condition target field (Optional)'),
                    desc: t("Define the key of another field if the condition should not against the parent. Use JSON notation for arrays and objects. String or Array")
                },
                'default': {
                    againstField: 'type',
                    type: 'text',
                    label: t('Default value. Use JSON notation for arrays and objects. (Optional)')
                },
                'requiredRegexp': {
                    needValue: ['text','password', 'number', 'checkbox', 'select', 'date', 'datetime', 'file', 'folder'],
                    againstField: 'type',
                    type: 'text',
                    label: t('Required value as regular expression. (Optional)'),
                    desc: t('Example of an email-check: /^[^@]+@[^@]{3,}\.[^\.@0-9]{2,}$/')
                },
                tableItem: {
                    label: t('Acts as a table item (Optional)'),
                    desc: t('Injects instead of a DIV a TR element.'),
                    type: 'checkbox',
                    'default': false
                },
                noWrapper: {
                    label: t('No wrapper. Removes all around the field itself.'),
                    desc: t('Injects only the pure UI of the defined type.'),
                    type: 'checkbox',
                    'default': false
                }
            }
        }
    },

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

        keyModifier: '',

        noActAsTableField: false, //Remove the field 'Acts as a table item'
        asFrameworkFieldDefinition: false, //means for usage in ka.Parse (and therefore in adminWindowEdit/Add), delete some relation stuff
        arrayKey: false //allows key like foo[bar], foo[barsen], foo[bar][sen]
    },

    childDiv: false,
    main: false,

    children: [], //instances of ka.FieldProperty

    initialize: function(pKey, pDefinition, pContainer, pOptions, pWin){

        this.setOptions(pOptions);
        this.win = pWin;
        this.kaFields = Object.clone(this.kaFields);

        if (!this.options.withTableDefinition){
            delete this.kaFields.primaryKey;
            delete this.kaFields.autoIncrement;
        } else {
            delete this.kaFields.type.items.label;
            delete this.kaFields.type.items.html;
            delete this.kaFields.type.items.info;
            delete this.kaFields.type.items.headline;
            delete this.kaFields.type.items.tab;
            delete this.kaFields.type.items.predefined;
        }

        if (this.options.noActAsTableField){
            delete this.kaFields.__optional__.depends.tableitem;
        }

        if (this.options.asFrameworkFieldDefinition){

            delete this.kaFields.type.depends.object_label;
            delete this.kaFields.type.depends.object_label_map;
            delete this.kaFields.type.depends.object_relation;
            delete this.kaFields.type.depends.object_relation_table;
            delete this.kaFields.type.depends.object_relation_table_left;
            delete this.kaFields.type.depends.object_relation_table_right;

        } else {
            //if not frameworkField
            delete this.kaFields.__optional__.depends.target;
            if (this.kaFields.__optional__.depends.tableitem)
                delete this.kaFields.__optional__.depends.tableitem;

        }


        if (this.options.asFrameworkSearch){
            delete this.kaFields.__optional__.depends.empty;
            delete this.kaFields.__optional__.depends.target;
            delete this.kaFields.__optional__.depends.needValue;
            delete this.kaFields.__optional__.depends.againstField;
            delete this.kaFields.__optional__.depends.required_regexp;

            if(this.kaFields.__optional__.depends.tableitem)
                delete this.kaFields.__optional__.depends.tableitem;

            delete this.kaFields.type.items.window_list;
            delete this.kaFields.type.items.childrenSwitcher;
            delete this.kaFields.type.items.layoutelement;
            delete this.kaFields.type.items.wysiwyg;
            delete this.kaFields.type.items.array;
            delete this.kaFields.type.items.tab;

        }

        if (!this.options.asFrameworkColumn){
            delete this.kaFields.width;
        } else {
            delete this.kaFields.__optional__;
            this.kaFields.type.label = t('Display type');
            this.kaFields.type.items = {
                text: t('Text'),
                number: t('Number'),
                bool: t('Boolean'),
                lang: t('Language select'),
                datetime: t('Datetime'),
                imagemap: t('Imagemap'),
                predefined: t('Predefined')
            };

            this.kaFields.type.depends.imageMap = {
                label: t('Map'),
                desc: t('To use Regex surround the value with /.'),
                type: 'array',
                needValue: 'imagemap',
                columns: [
                    {label: t('Value'), width: '50%'},
                    {label: t('Image path')}
                ],
                fields: {
                    value: {
                        type: 'text'
                    },
                    imagePath: {
                        type: 'file'
                    }
                }

            }
        };


        if (typeOf(this.options.fieldTypes) == 'array'){
            Object.each(this.kaFields.type.items, function(def, key){
                if (!this.options.fieldTypes.contains(key))
                    delete this.kaFields.type.items[key];
            }.bind(this));
        }

        if (typeOf(this.options.fieldTypesBlacklist) == 'array'){
            Array.each(this.options.fieldTypesBlacklist, function(key){
                delete this.kaFields.type.items[key];
            }.bind(this));
        }

        if (this.kaFields.type.items.object){
            this.kaFields.type.depends.object.type = 'select';
            this.kaFields.type.depends.object.items = {};

            Object.each(ka.settings.configs, function(config,extensionKey){
                if (config.objects){
                    Object.each(config.objects, function(object,object_key){
                        if ((this.options.asFrameworkFieldDefinition && object.selectable) || !this.options.asFrameworkFieldDefinition)
                            this.kaFields.type.depends.object.items[object_key] = object.title+" ("+object_key+")";
                    }.bind(this));
                }
            }.bind(this));
        }

        var self = this;

        this.main = new Element('div', {
            'class': 'ka-fieldTable-item',
            style: 'border-bottom: 1px solid silver; position: relative;'
        }).inject(pContainer);

        this.main.store('ka.FieldProperty', this);

        var header = new Element('div', {
            'class': 'ka-fieldTable-item-header'
        }).inject(this.main);

        this.main.store('definition', pDefinition || {});

        var count = pContainer.getElements('.ka-fieldTable-item').length-1;

        this.iKey = new ka.Field({
            type: 'text',
            modifier: this.options.keyModifier,
            noWrapper: true,
            width: '180px',
            style: 'display: inline-block;'
        }, header);

        this.iKey.setValue(pKey?pKey:'property_'+count);


        if (this.options.withActionsImages){

            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 5px;",
                title: _('Remove'),
                html: '&#xe26b;'
            })
            .addEvent('click', function(){
                this.win._confirm(t('Really delete?'), function(ok){
                    if(ok){
                        this.fireEvent('delete');
                        this.removeEvents('change');
                        this.main.destroy();
                        delete this;
                    }
                }.bind(this));
            }.bind(this))
            .inject(header);

            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
                title: t('Move up'),
                html: '&#xe2ca;'
            })
            .addEvent('click', function(){
                if (!this.main.getPrevious('.ka-fieldTable-item'))
                    return false;
                this.main.inject(this.main.getPrevious('.ka-fieldTable-item'), 'before');
            }.bind(this))
            .inject(header);


            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
                title: t('Move down'),
                html: '&#xe2cc;'
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
            style: 'width: 220px;'
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

        this.kaParse = new ka.Parse(fieldContainer, this.kaFields, {
            allTableItems:this.options.allTableItems,
            tableitem_title_width: this.options.tableitem_title_width,
            allSmall:this.options.allSmall
        }, {win:this.win});

        this.kaParse.addEvent('change', this.fireChange);

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
                this.children.include(new ka.FieldProperty('child_key_'+(this.childDiv.getChildren().length+1), {}, this.childDiv, this.options));
            }.bind(this))
            .inject(btnDiv);
        }

        if (pDefinition && typeOf(pDefinition) == 'object'){
            //do some migration stuff and setValue

            this.setValue(pKey, pDefinition);
        }
    },

    fireChange: function(){
        this.fireEvent('change');
    },

    getValue: function(){

        var key = this.iKey.getValue();
        if (!key) return;

        var kaParse = this.kaParse;
        var property = kaParse.getValue();

        Object.each(property, function(pval, pkey){
            if(pval === ''){
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

        if (!this.options.withoutChildren){
            property.depends = {};

            this.children.each(function(child){
                var fieldProperty = child.retrieve('ka.FieldProperty');
                var value = fieldProperty.getValue();

                property.depends[value.key] = value.definition;
            });

            if (Object.getLength(property.depends) == 0)
                delete property.depends;
        }

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



        this.iKey.setValue(pKey);

        this.kaParse.setValue(pDefinition);

        delete this.children;

        this.children = [];
        this.childDiv.empty();

        if (!this.options.withoutChildren){
            if (pDefinition.depends){
                Object.each(pDefinition.depends, function(definition, key){

                    this.children.include(new ka.FieldProperty(key, {}, this.childDiv, this.options));

                }.bind(this));

            }
        }
    }

});