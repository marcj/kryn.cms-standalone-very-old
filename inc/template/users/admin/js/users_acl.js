var users_users_acl = new Class({

    groupDivs: {},
    userDivs: {},

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
        .addEvent('keyup', function(){
            if (this.timeout) clearTimeout(this.timeout);
            this.timeout = this.loadList.delay(100, this);
        }.bind(this))
        .addEvent('mousedown', function(e){
            e.stopPropagation();
        })
        .inject(this.win.titleGroups);

        this.qImage = new Element('img', {
            src: _path+'inc/template/admin/images/icon-search-loupe.png',
            style: 'position: absolute; left: 11px; top: 8px;'
        }).inject(this.win.titleGroups)


        this.tabs = new ka.tabPane(this.right, true, this.win);

        this.entryPointTab = this.tabs.addPane(t('Entry points'), '');
        this.tabs.addPane(t('Objects'), '');
        this.tabs.addPane(t('Windows'), '');

        this.tabs.hide();

        this.loadEntryPoints();
        this.loadObjects();
        this.loadWindows();

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

    loadObjects: function(){




    },

    loadWindows: function(){




    },

    loadEntryPoints: function(){

        this.entryPointList = new Element('div', {
            'class': 'users-acl-entrypoint-list'
        })
        .inject(this.entryPointTab.pane);

        this.addEntryPointTree(ka.settings.configs['admin'], 'admin');

        Object.each(ka.settings.configs, function(ext, extCode){
            if( extCode != 'admin' && ext.admin ){
                this.addEntryPointTree( ext, extCode );
            }
        }.bind(this));
    },

    getEntryPointTitle: function(pNode){

        switch(pNode.type){

            case 'iframe':
            case 'custom':
                return ('Window %s').replace('%s', pNode.type);

            case 'list':
            case 'edit':
            case 'add':
            case 'combine':
                return ('Framework window %s').replace('%s', pNode.type);

            case 'function':
                return t('Background function call');

            case 'store':
                return t('Type store');

            default:
                return t('Default entry point');
        }

    },

    getEntryPointIcon: function(pNode){

        /*

         '': t('Default'),
         store: t('Store'),
         'function': t('Background function'),
         custom: t('[Window] Custom'),
         iframe: t('[Window] iFrame'),
         list: t('[Window] Framework list'),
         edit: t('[Window] Framework edit'),
         add: t('[Window] Framework add'),
         combine: t('[Window] Framework Combine')

         */
        switch(pNode.type){

            case 'list':
                return 'admin/images/icons/application_view_list.png';

            case 'edit':
                return 'admin/images/icons/application_form_edit.png';

            case 'add':
                return 'admin/images/icons/application_form_add.png';

            case 'combine':
                return 'admin/images/icons/application_side_list.png';

            case 'function':
                return 'admin/images/icons/script_code.png';

            case 'iframe':
            case 'custom':
                return 'admin/images/icons/application.png';

            case 'store':
                return 'admin/images/icons/database.png';

            default:
                return 'admin/images/icons/folder.png'
        }

    },

    addEntryPointTree: function(pExtensionConfig, pExtensionKey){

        var title = ka.getExtensionTitle(pExtensionKey);

        var target = new Element('div', {
            style: 'padding-top: 5px; margin-top: 5px; border-top: 1px dashed silver;'
        }).inject( this.entryPointList );

        var a = new Element('a', { href: 'javascript:;', text: title, title: '#'+pExtensionKey, style: 'font-weight: bold;'}).inject( target );

        var childContainer = new Element('div', {'class': 'users-acl-tree-childcontainer', style: 'padding-left: 25px;'}).inject( a, 'after' );

        if(pExtensionKey == 'admin')
            this.extContainer = childContainer;

        var path = pExtensionKey+'/';

        a.store('path', path);
        this.loadEntryPointChildren(pExtensionConfig.admin, path, childContainer);

    },

    loadEntryPointChildren: function(pAdmin, pCode, pChildContainer){

        Object.each(pAdmin, function(item, index){

            if(item.acl == false) return;

            var element = new Element('a', {
                href: 'javascript:;',
                text: t(item.title),
                title: this.getEntryPointTitle(item)+', '+pCode+index
            }).inject(pChildContainer);

            new Element('img', {
                src: _path+this.getEntryPointIcon(item)
            }).inject(element, 'top');

            var code = pCode+index+'/';
            element.store('code', code);
            var childContainer = new Element('div', {'class': 'users-acl-tree-childcontainer', style: 'padding-left: 25px;'}).inject( pChildContainer );

            this.loadEntryPointChildren(item.childs, code, childContainer);

        }.bind(this));
    },

    loadList: function(){

        var q = this.query.value;

        this.left.empty();

        new Element('div', {
            'class': 'ka-list-combine-itemloader',
            text: t('Loading ...')
        }).inject(this.left);

        var req = {};
        if (q)
            req.q = q;

        if (this.lastRq)
            this.lastRq.cancel();

        this.lastRq = new Request.JSON({url: _path+'admin/users/users/acl/search', noCache: 1,
            onComplete: this.renderList.bind(this)
        }).get(req);


    },

    renderList: function(pItems){

        if (pItems && typeOf(pItems) == 'object'){

            this.left.empty();

            if (typeOf(pItems.users) == 'array' && pItems.users.length > 0){
                new Element('div', {
                    'class': 'ka-list-combine-splititem',
                    text: t('Users')
                }).inject(this.left);

                Array.each(pItems.users, function(item){

                    var div = new Element('div', {
                        'class': 'ka-list-combine-item'
                    })
                    .addEvent('click', this.loadRules.bind(this, ['user', item]))
                    .inject(this.left);

                    this.userDivs[item.rsn] = div;

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
            }

            if (typeOf(pItems.groups) == 'array' && pItems.groups.length > 0){

                new Element('div', {
                    'class': 'ka-list-combine-splititem',
                    text: t('Groups')
                }).inject(this.left);

                Array.each(pItems.groups, function(item){

                    var div = new Element('div', {
                        'class': 'ka-list-combine-item'
                    })
                    .addEvent('click', this.loadRules.bind(this, ['group', item]))
                    .inject(this.left);


                    this.groupDivs[item.rsn] = div;

                    new Element('h2', {
                        text: item.name
                    }).inject(div);


                }.bind(this));

            }

        }

    },

    loadRules: function(pType, pItem){

        var div = pType=='group'? this.groupDivs[pItem.rsn]:this.userDivs[pItem.rsn];
        if (!div) return;

        this.left.getElements('.ka-list-combine-item').removeClass('active');

        div.addClass('active');

        var title;
        if (pType == 'user')
            title = t('User %s').replace('%s', pItem['username']);
        else
            title = t('Group %s').replace('%s', pItem['name']);

        this.win.setTitle(title);


        this.tabs.show();

    }


});
