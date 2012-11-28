var test_test = new Class({


    initialize: function(pWin){

        this.win = pWin;

        new ka.Select(this.win.content, {
            object: 'language'
        });

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
            chooserBrowserTreeIconMapping: {
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

        var fieldObj = new ka.Parse(div, field);

        fieldObj.setValue({
            chooserBrowserTreeIconMapping: {peter: "10"}
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
