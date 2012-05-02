var users_users_acl = new Class({

    initialize: function(pWindow){
        this.win = pWindow;

        this._createLayout();
    },

    _createLayout: function(){

        var bla = new ka.objectTree(this.win.content, 'node', {rootId: 1}, {win: this.win});


        new ka.Button('Select 7')
        .addEvent('click', function(){
            bla.select(7);
        })
        .inject(this.win.content);

        new ka.Button('Select 11')
        .addEvent('click', function(){
            bla.select(11);
        })
        .inject(this.win.content);

        new ka.Button('Select 14')
        .addEvent('click', function(){
            bla.select(14);
        })
        .inject(this.win.content);
    }

});
