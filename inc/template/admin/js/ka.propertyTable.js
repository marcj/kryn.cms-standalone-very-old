ka.propertyTable = new Class({

    container: false,

    initialize: function(pContainer, pWin){
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

    add: function(pKey, pDefinition){


        var tr = new Element('tr', {
            'class': 'ka-propertyTable-item'
        }).inject(this.tbody);

        new Element('input', {
            value: pKey?pKey:'',
            'class': 'text'
        }).inject( new Element('td', {valign: 'top', style: 'border-bottom: 1px solid silver'}).inject(tr) )

        var definition = new Element('td', {style: 'border-bottom: 1px solid silver'}).inject(tr);

        var kaFields = {
            label: {
                label: t('Label'),
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
                    folder: t('Folder'),
                    filelist: t('File list'),
                    layoutelement: t('Layout element'),
                    headline: t('Headline'),
                    info: t('Info'),
                    label: t('Label'),
                    html: t('Html'),
                    imagegroup: t('Imagegroup'),
                    array: t('Array'),
                    custom: t('Custom'),
                    window_list: t('Framework windowList')
                },
                'depends': {
                    'table': {
                        needValue: 'select',
                        label: t('Table name'),
                        desc: t('Start with / to use a table which is not defined in kryn or is in a different database.'),
                        type: 'text'
                    }
                }
            }
        }

        new ka.parse(definition, kaFields, {allTableItems:true, tableitem_title_width: 200}, {win:this.win});
    }


})