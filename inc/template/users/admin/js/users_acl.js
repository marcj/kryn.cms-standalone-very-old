var users_users_acl = new Class({

    initialize: function(pWindow){
        this.win = pWindow;

        this._createLayout();
    },

    _createLayout: function(){

        new ka.objectTree(this.win.content, 'pages', {}, {win: this.win});



    }

});
