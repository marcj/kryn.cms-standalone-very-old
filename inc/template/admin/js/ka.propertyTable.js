ka.propertyTable = new Class({
    Implements: Options,

    options: {
        withDependFields: false,
        addLabel: t('Add property'),
        mode: 'kaField', //kaField = ka.field definition, object = object field definition
        withTableDefinition: false, //shows the 'Is primary key?' and 'Auto increment' fields
        arrayKey: false //key like foo[bar], foo[barsen], foo[bar][sen]
    },

    container: false,

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
                    label: tc('kaPropertyTable', 'Visibility condition (Optional)'),
                    desc: t("Shows this field only, if the field defined below or the parent field has the defined value. String, Array(use JSON notation), /regex/ or 'javascript:(value=='foo'||value.substr(0,4)=='lala')'")
                },
                againstField: {
                    label: tc('kaPropertyTable', 'Visibility condition field (Optional)'),
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

    initialize: function(pContainer, pWin, pOptions){

        this.setOptions(pOptions);

        this.container = pContainer;
        this.win = pWin;

        if (!this.options.withTableDefinition){
            delete this.kaFields.primaryKey;
            delete this.kaFields.autoIncrement;
        }

        this.header = new Element('div', {
            style: 'background-color: #d1d1d1; padding: 2px; height: 27px; position: relative; border-bottom: 1px solid silver;'
        }).inject(this.container);

        this.showDefinitions = new ka.Checkbox(this.header);
        document.id(this.showDefinitions).setStyles({
            position: 'absolute',
            left: 5, top: 3
        });

        new Element('div', {
            text: tc('kaPropertyTable', 'Show definitions'),
            style: 'position: absolute; left: 72px; top: 9px; color: white; font-weight: bold; font-size: 11px;'
        }).inject(this.header);

        new Element('div', {style: 'clear: both'}).inject(this.header);

        this.showDefinitions.addEvent('change', function(){
            if (this.showDefinitions.getValue()){
                this.itemContainer.removeClass('ka-propertyTable-hide-definition')
            } else {
                this.itemContainer.addClass('ka-propertyTable-hide-definition')
            }
        }.bind(this));

        this.itemContainer = new Element('div', {
            style: 'background-color: #d6d6d6;',
            'class': 'ka-propertyTable-hide-definition'
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

    getValue: function(){

        return this._getValue(this.itemContainer);

    },

    _getValue: function(pContainer){
        var result = {};

        pContainer.getChildren('.ka-propertyTable-item').each(function(item){
            var key = item.getElement('.ka-propertyTable-item-key');
            if (!key) return;

            var property = item.retrieve('kaParse').getValue();
            var kaParse = item.retrieve('kaParse');

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

            property.depends = this._getValue(item.retrieve('dependDiv'));

            if (Object.getLength(property.depends) == 0)
                delete property.depends;

            result[key.value] = property;

        }.bind(this));

        return result;
    },

    setValue: function(pValue){

        if (typeOf(pValue) == 'object'){
            Object.each(pValue, function(property,key){
                this._setValue(key, property, this.itemContainer)
            }.bind(this));

        }
    },

    _setValue: function(pKey, pProperty, pContainer){
        var div = this.add(pKey, pProperty, pContainer);

        if (pProperty.depends){
            Object.each(pProperty.depends, function(property,key){
                this._setValue(key, property, div.retrieve('dependDiv'));
            }.bind(this));

        }
    },

    add: function(pKey, pDefinition, pContainer){
        var self = this;

        var div = new Element('div', {
            'class': 'ka-propertyTable-item',
            style: 'border-bottom: 1px solid silver; position: relative;'
        }).inject(pContainer);

        var header = new Element('div', {
            'class': 'ka-propertyTable-item-header'
        }).inject(div);

        div.store('definition', pDefinition || {});

        var count = this.itemContainer.getElements('.ka-propertyTable-item').length-1;

        var iKey = new Element('input', {
            value: pKey?pKey:'property_'+count,
            'class': 'text ka-propertyTable-item-key'
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

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            title: t('Delete property'),
            style: 'cursor: pointer; position: relative; top: 3px;'
        })
        .addEvent('click', function(){
            this.win._confirm(t('Really delete?'), function(ok){
                if(ok)
                    div.destroy();
            }.bind(this));
        }.bind(this))
        .inject(header);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_up.png',
            title: t('Move up'),
            style: 'cursor: pointer; position: relative; top: 3px;'
        })
        .addEvent('click', function(){
            if (!div.getPrevious('.ka-propertyTable-item'))
                return false;
            div.inject(div.getPrevious('.ka-propertyTable-item'), 'before');
        })
        .inject(header);


        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_down.png',
            title: t('Move down'),
            style: 'cursor: pointer; position: relative; top: 3px;'
        })
        .addEvent('click', function(){
            if (!div.getNext())
                return false;
            div.inject(div.getNext(), 'after');
        })
        .inject(header);

        var showDefinition = new Element('div', {
            'class': 'ka-propertyTable-showDefinition',
            style: 'width: 150px;'
        }).inject(header);

        var ch = new ka.Checkbox(showDefinition);
        ch.addEvent('change', function(){
            if(ch.getValue()){
                div.addClass('ka-propertyTable-showDefinition-show-sub');
            } else {
                div.removeClass('ka-propertyTable-showDefinition-show-sub');
            }
        });

        new Element('div',{
            style: 'position: absolute; left: 70px; top: 6px; color: gray;',
            text: t('Show definition')
        }).inject(showDefinition);

        if (!this.options.withTableDefinition) {
            var headerInfo = new Element('div', {
                text: t('Surround the key above with __ and __ (double underscore) to define a field which acts only as a user interface item and does not appear in the result.'),
                style: 'color: gray',
                'class': 'ka-propertyTable-key-info'
            }).inject(header);
        }

        var main = new Element('div',{'class': 'ka-propertyTable-definition',style: 'background-color: #e5e5e5'}).inject(div);

        var table = new Element('table', {
            width: '100%'
        }).inject( main );

        var fieldContainer = new Element('tbody').inject(table);

        var kaParse = new ka.parse(fieldContainer, this.kaFields, {allTableItems:true, tableitem_title_width: 350}, {win:this.win});

        div.store('kaParse', kaParse);

        if (pDefinition && typeOf(pDefinition) == 'object'){
            //do some migration stuff and setValue

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

                if (!kaParse.fields[valueKey]) return;

                var type = kaParse.fields[valueKey].field.type;
                if((type == 'text' || !type) && typeOf(value) != 'string')
                    pDefinition[valueKey] = JSON.encode(value);

            }.bind(this));

            kaParse.setValue(pDefinition);
        }


        var dependDiv = new Element('div', {
            'class': 'ka-propertyTable-children'
        }).inject(div);

        div.store('dependDiv', dependDiv);

        var btnDiv = Element('div', {
            style: 'padding-top: 3px; padding-left: 10px;'
        }).inject(div)

        new ka.Button(t('Add child'))
        .addEvent('click', function(){
            this.add('child_key_'+(dependDiv.getChildren().length+1), {}, dependDiv);
        }.bind(this))
        .inject(btnDiv);

        //this.renderDependInfo(div);

        return div;
    }

    /*

    renderDependInfo: function(pPropertyDiv){

        var definition = pPropertyDiv.retrieve('definition');
        var dependDiv = pPropertyDiv.retrieve('dependDiv');

        dependDiv.empty();

        if (definition.depends && Object.getLength(definition.depends) > 0){
            Object.each(definition.depends, function(item, key){

                new Element('div', {
                    text: '» '+key+' '+(item.label?'('+item.label+')':'')
                }).inject(dependDiv);

            });
        } else {
            dependDiv.set('text', t('No children there.'));
        }

    },

    openDepends: function(pPropertyDiv){

        this.dialog = this.win.newDialog('', true);

        this.dialog.setStyles({
            height: '60%',
            width: '80%'
        });
        this.dialog.center();

        var keyField = pPropertyDiv.getElement('.ka-propertyTable-item-key');

        new Element('h3', {
            'class': 'kryn-headline',
            style: 'margin-bottom: 5px; font-weight: bold;',
            text: tc('kaPropertyTable','Children of property \'%s\'').replace('%s', keyField.value)
        }).inject(this.dialog.content)

        var dependsPropertyTable = new ka.propertyTable(this.dialog.content, this.win, this.options);

        var definition = pPropertyDiv.retrieve('definition');
        pPropertyDiv.store('dependPropertyTable', dependsPropertyTable);

        if (definition.depends){
            dependsPropertyTable.setValue(definition.depends);
        }

        new ka.Button(t('Cancel')).addEvent('click', this.cancelDepends.bind(this)).inject(this.dialog.bottom);

        new ka.Button(t('Apply')).addEvent('click', function(){
            definition.depends = dependsPropertyTable.getValue();
            pPropertyDiv.store('definition', definition);
            this.renderDependInfo(pPropertyDiv);
            this.cancelDepends();
        }.bind(this)).inject(this.dialog.bottom);

    },

    cancelDepends: function(){

        this.dialog.close();
        delete this.dialog;

    }
    */

})