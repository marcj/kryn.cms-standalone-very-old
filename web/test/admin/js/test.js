var test_test = new Class({


    initialize: function(pWin){

        this.win = pWin;

        new ka.Select(this.win.content, {
            object: 'Core\\Domain'
        });

        new ka.Select(this.win.content, {
            object: 'Core\\Language'
        });


        new ka.FieldForm(this.win.content,{
            'defaultIcon': {
                label: t('Default icon'),
                type: 'file',
                combobox: true
            }
        });


        new ka.Field({
            "label": "Sort direction",
            "items": {
                "desc": "[[Descending]]",
                "asc": "[[Ascending]]"
            },
            "type": "select"
        }, this.win.content);


        new ka.Field({
            label: t('EntryPoint'),
            type: 'object',
            combobox: true,
            object: 'Core\\EntryPoint'
        }, this.win.content);

        new ka.Field({
            label: t('Array'),
            type: 'array',
            columns: [null, '30%'],
            startWith: 1,
            fields: {
                title: {
                    label: 'Title',
                    type: 'text',
                    required: true
                },
                'visible': {
                    'label': 'Visible',
                    'type': 'checkbox',
                    'width': '100'
                }
            }
        }, this.win.content);


        var tree;


        new ka.Button('Deselect')
            .addEvent('click', function(){
                tree.getFieldObject().deselect();
            })
            .inject(this.win.content);

        new ka.Button('Reload 8')
            .addEvent('click', function(){
                tree.getFieldObject().reloadBranch({id: 8});
            })
            .inject(this.win.content);

        new ka.Button('Reload parent 8')
            .addEvent('click', function(){
                tree.getFieldObject().reloadParentBranch({id: 8});
            })
            .inject(this.win.content);

        new ka.Button('Reload 22')
            .addEvent('click', function(){
                tree.getFieldObject().reloadBranch({id: 22});
            })
            .inject(this.win.content);

        new ka.Button('Reload parent 22')
            .addEvent('click', function(){
                tree.getFieldObject().reloadParentBranch({id: 22});
            })
            .inject(this.win.content);

        new ka.Button('Reload Domain')
            .addEvent('click', function(){
                tree.getFieldObject().reloadBranch({id: 1}, 'Core\\Domain');
            })
            .inject(this.win.content);

        tree = new ka.Field({
            label: t('Nodes'),
            type: 'tree',
            objectKey: 'Core\\Node'
        }, this.win.content);


        return;

        new ka.Select(this.win.content, {
            items: [
                'Hosa', 'Mowla', 'Gazzo'
            ]
        });

        var s2 = new ka.Select(this.win.content, {
            object: 'group'
        });

        s2.setValue(5);

        var s2 = new ka.Select(this.win.content, {
            object: 'group',
            labelTemplate: '[{id}] {name}'
        });

        s2.setValue(5);

        var div = new Element('div', {
            style: 'padding-top: 15px;'
        }).inject(this.win.content);

        var field = {
            bla: {
                label: t('Icon path mapping'),
                type: 'array',
                asHash: true,
                columns: [
                    {label: t('Value'), width: '30%'},
                    {label: t('Icon path')}
                ],
                fields: {
                    value: {
                        type: 'text'
                    },
                    path: {
                        type: 'file',
                        combobox: true
                    }
                }
            }
        };

        var fieldObj = new ka.FieldForm(div, field);

        fieldObj.setValue({
            bla: {peter: "10"}
        });

        var items = [];
        for (var i =0; i<100;i++)
            items.push('Mowla '+i);

        new ka.Select(this.win.content, {
            items: items
        });

        new Element('input').inject(this.win.content);
        
        new ka.TextboxList(this.win.content, {
            items: [
                'Hosa', 'Mowla', 'Gazzo'
            ]
        });

        var field = new ka.Select(this.win.content, {
            object: 'domain'
        });

        var ch = new Element('div', {
            style: 'padding-top: 15px;'
        }).inject(this.win.content);

        field.addEvent('change', function(){
            ch.empty();
            logger( field.getValue());
            this.lastObjectTree = new ka.Field({
                type: 'tree',
                object: 'node',
                scope: field.getValue()
            }, ch);
        });

        //field.fireEvent('change');

        var div = new Element('div', {
            style: 'padding-top: 15px;'
        }).inject(this.win.content);

//        this.lastObjectTree = new ka.Field({
//            type: 'tree',
//            object: 'file'
//        }, div);

        var div = new Element('div', {
            style: 'padding-top: 15px;'
        }).inject(this.win.content);

        new ka.Field({
            type: 'fieldTable',
            fieldWidth: '100%',
            asFrameworkColumn: true
        }, div);

    }

});
