var users_users_acl = new Class({

    initialize: function(pWindow){
        this.win = pWindow;

        this._createLayout();
    },

    _createLayout: function(){

        this.win.extendHead();

        this.left = new Element('div', {
            'class': 'users-acl-left'
        }).inject(this.win.content);

        this.right = new Element('div', {
            'class': 'users-acl-right'
        }).inject(this.win.content);


        this.query = new Element('input', {
            'class': 'text gradient users-acl-query',
            type: 'text'
        })
        .addEvent('mousedown', function(e){
            e.stopPropagation();
        })
        .inject(this.win.titleGroups);

        this.qImage = new Element('img', {
            src: _path+'inc/template/admin/images/icon-search-loupe.png',
            style: 'position: absolute; left: 20px; top: 8px;'
        }).inject(this.win.titleGroups)


        this.tabs = new ka.tabPane(this.right, true, this.win);

        this.tabs.addPane(t('Backend'), '');
        this.tabs.addPane(t('Objects'), '');
        this.tabs.addPane(t('Windows'), '');

        document.id(this.tabs.buttonGroup).setStyle('position', 'absolute');
        document.id(this.tabs.buttonGroup).setStyle('left', 200);
        document.id(this.tabs.buttonGroup).setStyle('top', 0);


        this.loadList();



        return;
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
    },

    loadList: function(){

        var q = this.query.input;

        this.left.empty();

        new Element('div', {
            'class': 'ka-list-combine-itemloader',
            text: t('Loading ...')
        }).inject(this.left);

        var req = {};
        if (q)
            req.query = q;

        if (this.lastRq)
            this.lastRq.cancel();

        this.lastRq = new Request.JSON({url: _path+'admin/users/users/acl/search', noCache: 1,
            onComplete: this.renderList.bind(this)
        }).get(req);


    },

    renderList: function(pItems){


        if (pItems && typeOf(pItems) == 'object'){

            this.left.empty();

            new Element('div', {
                'class': 'ka-list-combine-splititem',
                text: t('Users')
            }).inject(this.left);

            Array.each(pItems.users, function(item){

                var div = new Element('div', {
                    'class': 'ka-list-combine-item'
                }).inject(this.left);

                var h2 = new Element('h2', {
                    text: item.username
                }).inject(div);

                var subline = new Element('div', {
                    'class': 'subline'
                }).inject(div);

                new Element('span', {
                    text: item.first_name+' '+item.last_name
                }).inject(subline);

                new Element('span', {
                    text: ' ('+item.email+')'
                }).inject(subline);


                var subline = new Element('div', {
                    'class': 'subline',
                    style: 'color: silver',
                    text: item.groups_name
                }).inject(div);


            }.bind(this));


            new Element('div', {
                'class': 'ka-list-combine-splititem',
                text: t('Groups')
            }).inject(this.left);

            Array.each(pItems.groups, function(item){

                var div = new Element('div', {
                    'class': 'ka-list-combine-item'
                }).inject(this.left);

                new Element('h2', {
                    text: item.name
                }).inject(div);


            }.bind(this));

        }



    }

});
