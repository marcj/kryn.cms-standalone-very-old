ka.propertyTable = new Class({
    Implements: Options,

    options: {
        withDepends: false

    },

    container: false,

    initialize: function(pContainer, pWin, pOptions){

        this.setOptions(pOptions);

        this.container = pContainer;
        this.win = pWin;


        var table = new Element('table', {
            'class': 'ka-Table-head ka-Table-body',
            style: 'position: relative; top: 0px;',
            cellpadding: 0, cellspacing: 0
        }).inject(this.container);
        this.tbody = new Element('tbody').inject(table);

        var tr = new Element('tr').inject(this.tbody);
        new Element('th', {
            text: t('Key'),
            style: 'width: 150px;'
        }).inject(tr);

        new Element('th', {
            text: t('Definition')
        }).inject(tr);

        new ka.Button(t('Add property'))
        .addEvent('click', function(){
            this.add();
        }.bind(this))
        .inject(this.container);
    },

    getValue: function(){

        var value = {};

        this.tbody.getChildren('.ka-propertyTable-item').each(function(item){

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
                        //check if json string
                        if (pval.substr(0,1) == '"' && pval.substr(pval.length-1,1) == '"')
                            newItem = JSON.decode(pval);

                        //check if json array
                        if (pval.substr(0,1) == '[' && pval.substr(pval.length-1,1) == ']')
                            newItem = JSON.decode(pval);

                        //check if json object
                        if (pval.substr(0,1) == '{' && pval.substr(pval.length-1,1) == '}')
                            newItem = JSON.decode(pval);

                    } catch(e){}

                    if (newItem)
                        property[pkey] = newItem;

                }

            }.bind(this));

            var originDefinition = item.retrieve('definition');

            if (originDefinition && originDefinition.depends)
                property.depends = originDefinition.depends;

            value[key.value] = property;
        }.bind(this));

        return value;
    },

    setValue: function(pValue){

        if (typeOf(pValue) == 'object'){
            Object.each(pValue, function(property,key){

                this.add(key, property);

            }.bind(this));

        }
    },

    add: function(pKey, pDefinition){

        var tr = new Element('tr', {
            'class': 'ka-propertyTable-item'
        }).inject(this.tbody);

        tr.store('definition', pDefinition || {});

        var count = this.tbody.getChildren('tr').length-1;

        var td = new Element('td', {valign: 'top', style: 'border-bottom: 1px solid silver'}).inject(tr);

        var iKey = new Element('input', {
            value: pKey?pKey:'property_'+count,
            'class': 'text ka-propertyTable-item-key'
        })
        .addEvent('keyup', function(e){
            this.value = this.value.replace(' ', '_');
            this.value = this.value.replace(/[^a-zA-Z0-9_-]/, '-');
            this.value = this.value.replace(/--+/, '-');
        })
        .inject(td);

        var div = new Element('div').inject(td);


        new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            title: t('Delete property'),
            style: 'cursor: pointer;'
        })
        .addEvent('click', function(){
            tr.destroy();
        })
        .inject(div);


        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_up.png',
            title: t('Move up'),
            style: 'cursor: pointer;'
        })
        .addEvent('click', function(){
            if (tr.getPrevious() && tr.getPrevious().getFirst('th'))
                return false;
            tr.inject(tr.getPrevious(), 'before');
        })
        .inject(div);


        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_down.png',
            title: t('Move down'),
            style: 'cursor: pointer;'
        })
            .addEvent('click', function(){
            if (!tr.getNext())
                return false;
            tr.inject(tr.getNext(), 'after');
        })
            .inject(div);

        var definition = new Element('td', {style: 'border-bottom: 1px solid silver;'}).inject(tr);



        this.kaFields = {
            label: {
                label: t('Label'),
                type: 'text'
            },
            desc: {
                label: t('Description (Optional)'),
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
                        label: t('Table primary key')
                    },
                    table_label: {
                        needValue: function(n){if(n!='')return true;else return false;},
                        againstField: ['table', 'sql'],
                        label: t('Table label key')
                    },
                    items: {
                        needValue: 'select',
                        label: t('static items'),
                        desc: t('Use JSON notation. Array(key==label) or Object(key => label). Example: {item1: \'Item 1\'}.')
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

            'length': {
                needValue: ['text', 'password', 'number'],
                againstField: 'type',
                type: 'text',
                label: t('Max value length. (Optional)')
            },

            //all
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
        };

        if (this.options.withDepends){

            this.kaFields['needValue'] = {
                label: tc('kaPropertyTable', 'Need value'),
                desc: t("Use JSON notation. String, Array or 'javascript:function(v){return v==1}';")
            }

            this.kaFields['againstField'] = {
                label: tc('kaPropertyTable', 'Against field (Optional)'),
                desc: t("Use JSON notation. String or Array")
            }

        }

        var kaParse = new ka.parse(definition, this.kaFields, {allTableItems:true, tableitem_title_width: 300}, {win:this.win});

        tr.store('kaParse', kaParse);

        if (pDefinition && typeOf(pDefinition) == 'object'){
            //do some migration stuff and setValue

            if(pDefinition.type == 'select' && pDefinition.table_id){
                pDefinition.table_key = pDefinition.table_id+'';
                delete pDefinition.table_id;
            }
            if(pDefinition.type == 'select' && pDefinition.tableItems){
                pDefinition.items = Object.clone(pDefinition.tableItems);
                delete pDefinition.tableItems;
            }


            Object.each(pDefinition, function(value, valueKey){

                if (!kaParse.fields[valueKey]) return;

                var type = kaParse.fields[valueKey].field.type;
                if((type == 'text' || !type) && typeOf(value) != 'string')
                    pDefinition[valueKey] = JSON.encode(value);

            }.bind(this));


            kaParse.setValue(pDefinition);
        }


        new ka.Button(t('Depends'))
            .addEvent('click', this.openDepends.bind(this, tr))
            .inject(definition);

        var dependDiv = new Element('div', {style: 'padding: 5px; color: gray;'}).inject(definition);
        tr.store('dependDiv', dependDiv);

        this.renderDependInfo(tr);

    },


    renderDependInfo: function(pTr){

        var definition = pTr.retrieve('definition');
        var dependDiv = pTr.retrieve('dependDiv');

        dependDiv.empty();

        if (definition.depends && Object.getLength(definition.depends) > 0){
            Object.each(definition.depends, function(item, key){

                new Element('div', {
                    text: '» '+key+' '+(item.label?'('+item.label+')':'')
                }).inject(dependDiv);

            });
        } else {
            dependDiv.set('text', t('No depended properties exist.'));
        }

    },

    openDepends: function(pTr){

        this.dialog = this.win.newDialog('', true);

        this.dialog.setStyles({
            height: '60%',
            width: '80%'
        });
        this.dialog.center();

        var keyField = pTr.getElement('.ka-propertyTable-item-key');

        new Element('h3', {
            'class': 'kryn-headline',
            style: 'margin-bottom: 5px; font-weight: bold;',
            text: tc('kaPropertyTable','Depends for property \'%s\'').replace('%s', keyField.value)
        }).inject(this.dialog.content)

        var dependsPropertyTable = new ka.propertyTable(this.dialog.content, this.win, {withDepends: true});

        var definition = pTr.retrieve('definition');
        pTr.store('dependPropertyTable', dependsPropertyTable);

        if (definition.depends){
            dependsPropertyTable.setValue(definition.depends);
        }

        new ka.Button(t('Cancel')).addEvent('click', this.cancelDepends.bind(this)).inject(this.dialog.bottom);

        new ka.Button(t('Apply')).addEvent('click', function(){
            definition.depends = dependsPropertyTable.getValue();
            pTr.store('definition', definition);
            this.renderDependInfo(pTr);
            this.cancelDepends();
        }.bind(this)).inject(this.dialog.bottom);

    },

    cancelDepends: function(){

        this.dialog.close();
        delete this.dialog;

    }

})