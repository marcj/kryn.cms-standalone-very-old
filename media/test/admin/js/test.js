var test_test = new Class({


    initialize: function(pWin){

        this.win = pWin;

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

        var items = [];
        for (var i =0; i<100;i++)
            items.push('Mowla '+i);

        new ka.Select(this.win.content, {
            items: items
        });


        new Element('input').inject(this.win.content);
        
        new ka.Textlist(this.win.content, {
            items: [
                'Hosa', 'Mowla', 'Gazzo'
            ]
        });

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
