var admin_system_module_edit = new Class({

    initialize: function (pWin) {
        this.win = pWin;
        this.mod = this.win.params.name;
        this.win.setTitle(this.mod + ' - ' + this.win.getTitle());
        this._createLayout();
    },

    _createLayout: function () {

        this.topNavi = this.win.addTabGroup();
        this.buttons = {};
        this.buttons['general'] = this.topNavi.addButton(t('General'), '', this.viewType.bind(this, 'general'));
        this.buttons['extras'] = this.topNavi.addButton(t('Extras'), '', this.viewType.bind(this, 'extras'));
        this.buttons['links'] = this.topNavi.addButton(t('Admin entry points'), '', this.viewType.bind(this, 'links'));

        this.buttons['objects'] = this.topNavi.addButton(t('Objects'), '', this.viewType.bind(this, 'objects'));
        this.buttons['db'] = this.topNavi.addButton(t('Model'), '', this.viewType.bind(this, 'db'));

        this.buttons['windows'] = this.topNavi.addButton(t('Backend Views'), '', this.viewType.bind(this, 'windows'));
        this.buttons['plugins'] = this.topNavi.addButton(t('Frontend Views'), '', this.viewType.bind(this, 'plugins'));

        this.buttons['docu'] = this.topNavi.addButton(t('Docu'), '', this.viewType.bind(this, 'docu'));
        this.buttons['help'] = this.topNavi.addButton(t('Help'), '', this.viewType.bind(this, 'help'));
        this.buttons['layouts'] = this.topNavi.addButton(t('Themes'), '', this.viewType.bind(this, 'layouts'));
        this.buttons['language'] = this.topNavi.addButton(t('Language'), '', this.viewType.bind(this, 'language'));

        this.panes = {};
        Object.each(this.buttons, function (button, id) {
            this.panes[id] = new Element('div', {
                'class': 'admin-system-modules-edit-pane'
            }).inject(this.win.content);
        }.bind(this));

        this.loader = new ka.Loader().inject(this.win.content);
        this.loader.hide();

        this.viewType('general');
    },


    /*
     *  Plugins
     * 
     *
     */

    loadPlugins: function () {

        if (this.lr) this.lr.cancel();
        this.panes['plugins'].empty();

        this.pluginsPane = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['plugins']);

        this.pluginTBody = new Element('table', {
            'class': 'ka-Table-head ka-Table-body',
            style: 'position: relative; top: 0px; background-color: #eee; width: 100%',
            cellpadding: 0, cellspacing: 0
        }).inject(this.pluginsPane);

        var tr = new Element('tr').inject(this.pluginTBody);
        new Element('th', {
            text: t('PHP Method name'),
            style: 'width: 260px;'
        }).inject(tr);

        new Element('th', {
            text: t('Plugin title'),
            style: 'width: 260px;'
        }).inject(tr);

        new Element('th', {
            text: t('Properties')
        }).inject(tr);

        new Element('th', {
            text: t('Actions'),
            style: 'width: 80px;'
        }).inject(tr);

        var buttonBar = new ka.ButtonBar(this.panes['plugins']);
        buttonBar.addButton(t('Add plugin'), this.addPlugin.bind(this));
        buttonBar.addButton(t('Save'), this.savePlugins.bind(this));

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/plugins', noCache: 1, onComplete: function (res) {

            if (res) {
                Object.each(res.data, function (item, key) {
                    this.addPlugin(item, key)
                }.bind(this));
            }
            this.loader.hide();

        }.bind(this)}).get({name: this.mod});
    },

    savePlugins: function () {

        var req = {plugins: {}};

        this.pluginsPane.getElements('.plugin').each(function(pluginDiv){

            var inputs = pluginDiv.getElements('input');
            var fieldTable = pluginDiv.retrieve('fieldTable');

            var plugin = [
                inputs[1].value,
                fieldTable.getValue()
            ]

            req.plugins[inputs[0].value] = plugin;
        });

        if (this.lr) this.lr.cancel();
        this.loader.show();

        req.plugins = JSON.encode(req.plugins);
        req.name = this.mod;
        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/plugins', noCache: 1, onComplete: function (res) {
            this.loader.hide();
            ka.loadSettings();
        }.bind(this)}).post(req);

    },

    addPlugin: function (pPlugin, pKey) {

        var tr = new Element('tr', {
            'class': 'plugin'
        }).inject(this.pluginTBody);

        var leftTd = new Element('td').inject(tr);
        var rightTd = new Element('td').inject(tr);
        var right2Td = new Element('td').inject(tr);
        var actionTd = new Element('td').inject(tr);


        var tr2 = new Element('tr').inject(this.pluginTBody);
        var bottomTd = new Element('td', {style: 'border-bottom: 1px solid silver', colspan: 4}).inject(tr2);

        new Element('input', {'class': 'text', style: 'width: 250px;', value:pKey?pKey:''}).inject(leftTd);

        new Element('input', {'class': 'text', style: 'width: 250px;', value:pPlugin?pPlugin[0]:''}).inject(rightTd);


        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Delete property'),
            html: '&#xe26b;'
        })
        .addEvent('click', function(){
            this.win._confirm(t('Really delete'), function(ok){
                if (!ok) return;
                tr.destroy();
                tr2.destroy();
            });
        }.bind(this))
        .inject(actionTd);

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: t('Move up'),
            html: '&#xe2ca;'
        })
        .addEvent('click', function(){
            var previous = tr.getPrevious();
            if (previous.getElement('th')) return;

            tr.inject(previous.getPrevious(), 'before');
            tr2.inject(tr,'after');
        })
        .inject(actionTd);


        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: t('Move down'),
            html: '&#xe2cc;'
        })
        .addEvent('click', function(){
            if (!tr2.getNext())
                return false;
            tr2.inject(tr2.getNext().getNext(), 'after');
            tr.inject(tr2, 'before');
        })
        .inject(actionTd);


        var a = new Element('a', {
            text: t('Properties'),
            style: 'display: block; padding: 2px; cursor: pointer'
        }).inject(right2Td);

        new Element('img', {
            src: _path+ PATH_MEDIA + '/admin/images/icons/tree_plus.png',
            style: 'margin-left: 2px; margin-right: 3px;'
        }).inject(a, 'top');

        var propertyPanel = new Element('div', {
            style: 'display: none; margin: 15px; margin-top: 5px; border: 1px solid silver; background-color: #e7e7e7;',
            'class': 'ka-extmanager-plugins-properties-panel'
        }).inject(bottomTd);

        a.addEvent('click', function(){
            if (propertyPanel.getStyle('display') == 'block'){
                propertyPanel.setStyle('display', 'none');
                this.getElement('img').set('src', _path+ PATH_MEDIA + '/admin/images/icons/tree_plus.png');
            } else {
                propertyPanel.setStyle('display', 'block');
                this.getElement('img').set('src', _path+ PATH_MEDIA + '/admin/images/icons/tree_minus.png');
            }

        });

        var fieldTable = new ka.FieldTable(propertyPanel, this.win, {
            arrayKey: true
        });

        tr.store('fieldTable', fieldTable);

        if (pPlugin)
            fieldTable.setValue(pPlugin[1]);

    },


    /*
     * 
     * Documentation
     *
     */


    saveDocu: function () {
        if (this.lr) this.lr.cancel();
        this.loader.show();
        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/docu', noCache: 1, onComplete: function (res) {
            this.loader.hide();
        }.bind(this)}).post({text: this.text.getValue(), /*lang: this.languageSelect.value, */name: this.mod});
    },

    loadDocu: function () {

        if (this.lr) this.lr.cancel();
        this.panes['docu'].empty();
        var p = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['docu']);

        var buttonBar = new ka.ButtonBar(this.panes['docu']);
        buttonBar.addButton(t('Save'), this.saveDocu.bind(this));

        this.text = new ka.Field({
            label: t('Documentation'), type: 'wysiwyg'}, p, {win: this.win});
        this.text.setValue(t('Loading ...'));

        this.text.input.setStyle('height', '100%');
        this.text.input.setStyle('width', '100%');

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/docu', noCache: 1, onComplete: function (res) {
            this.text.setValue(res);
        }.bind(this)}).get({/*lang: this.languageSelect.value, */name: this.mod});

        this.loader.hide();
    },

    saveWindows: function () {

    },

    loadWindows: function () {
        if (this.lr) this.lr.cancel();
        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/windows', noCache: 1,
        onComplete: function (pResult) {
            this.loader.hide();
            this._renderWindows(pResult.data);
        }.bind(this)}).get({name: this.mod});
    },


    _renderWindows: function (pForms) {

        this.panes['windows'].empty();

        var p = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['windows']);
        this.windowsPaneItems = p;

        this.windowsTBody = new Element('table', {
            'class': 'ka-Table-head ka-Table-body',
            style: 'position: relative; top: 0px; background-color: #eee',
            cellpadding: 0, cellspacing: 0
        }).inject(this.windowsPaneItems);

        var tr = new Element('tr').inject(this.windowsTBody);
        new Element('th', {
            text: t('Class name'),
            style: 'width: 260px;'
        }).inject(tr);

        new Element('th', {
            text: t('Actions'),
            style: 'width: 80px;'
        }).inject(tr);

        pForms.each(function (form) {
            this.addWindow(form);
        }.bind(this));


        var buttonBar = new ka.ButtonBar(this.panes['windows']);
        buttonBar.addButton(t('Add window'), function(){
            this.createWindow('');
        }.bind(this));
    },

    createWindow: function(pName){
        //prompt
        //request, check exists
        //create file

        var dialog = this.win.newDialog('<b>'+t('New tab')+'</b>');
        dialog.setStyle('width', 400);

        var d = new Element('div', {
            style: 'padding: 5px 0px;'
        }).inject(dialog.content);

        var table = new Element('table').inject(d);
        var tbody = table;

        var tr = new Element('tr').inject(tbody);

        new Element('td', {text: t('Window class name:')}).inject(tr);
        var td = new Element('td').inject(tr);
        var name = new Element('input', {'class': 'text'})
        .addEvent('change', function(e){
            this.value = this.value.replace(/\W/, '_');
        }).addEvent('keyup', function(e){
            this.fireEvent('change');
        })
        .inject(td);

        var tr = new Element('tr').inject(tbody);
        new Element('td', {text: t('Class:')}).inject(tr);
        var td = new Element('td').inject(tr);
        var typeClass = new ka.Field({
            type: 'select', items: {
                adminWindowAdd: 'adminWindowAdd',
                adminWindowEdit: 'adminWindowEdit',
                adminWindowList: 'adminWindowList',
                adminWindowCombine: 'adminWindowCombine'
            },
            noWrapper: 1,
            input_width: 150
        }, td)


        this.newWindowDialogCancelBtn = new ka.Button(t('Cancel'))
            .addEvent('click', function(){
            dialog.close();
        })
        .inject(dialog.bottom);

        this.newWindowDialogApplyBtn = new ka.Button(t('Apply'))
        .addEvent('click', function(){

            if (name.value == ''){

                this.win._alert(t('Class name is empty'));
                return;
            }

            this.newWindowDialogCancelBtn.deactivate();
            this.newWindowDialogApplyBtn.deactivate();
            this.newWindowDialogApplyBtn.startTip(t('Please wait ...'));

            new Request.JSON({url: _path+'admin/system/module/editor/new-window', noCache: 1, onComplete: function(res){

                this.newWindowDialogApplyBtn.stopTip();
                dialog.close();

                if (res.error == 'file_exists'){
                    this.win._alert(t('Class already exist'), function(){
                        this.createWindow(name.value);
                    }.bind(this));
                    return;
                } else {
                    this.addWindow(name.value);
                }

            }.bind(this)}).get({className: name.value, name: this.mod, 'class': typeClass.getValue()});

        }.bind(this))
        .inject(dialog.bottom);

        dialog.center();

    },

    addWindow: function (pClassName) {

        var className = this.windowsTBody.getLast().hasClass('two')?'one':'two';

        var tr = new Element('tr',{
            'class': className
        }).inject(this.windowsTBody);

        var td = new Element('td', {
            text: pClassName
        }).inject(tr);

        var td = new Element('td').inject(tr);


        new ka.Button(t('Edit window'))
        .addEvent('click', function(){

            ka.wm.open('admin/system/module/editWindow', {module: this.mod, className: pClassName});
        }.bind(this))
        .inject(td);

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Remove'),
            html: '&#xe26b;'
        }).addEvent('click', function () {
            //delete
        }.bind(this)).inject(td);

    },

    /**
     *
     *
     * Database form
     *
     *
     */
    loadDb: function () {
        if (this.lr) this.lr.cancel();
        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/model', noCache: 1, onComplete: function (res) {
            this.loader.hide();
            this._renderDb(res.data);
        }.bind(this)}).get({name: this.mod});
    },

    saveDb: function () {

        var req = {};
        req.name = this.mod;
        req.model = this.dbEditor.getValue();

        this.loader.show();

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/model', noCache: 1, onComplete: function () {
            this.loader.hide();
            ka.loadSettings();
        }.bind(this)}).post(req);
    },

    _renderDb: function (pModel) {
        this.panes['db'].empty();

        this.dbEditorPane = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['db']);

        this.dbEditor = new ka.Field({
            type: 'codemirror',
            noWrapper: 1,
            input_height: '100%'
        }, this.dbEditorPane);

        this.dbEditor.setValue(pModel);

        var buttonBar = new ka.ButtonBar(this.panes['db']);

        var info = new Element('div', {
            style: 'position: absolute; left: 5px; top: 7px; color: gray;',
            text: 'module/'+this.mod+'/model.xml, '
        }).inject(document.id(buttonBar));

        new Element('a', {
            text: t('XML Schema'),
            target: '_blank',
            href: 'http://www.propelorm.org/reference/schema.html'
        }).inject(info);

        new Element('span', {text: ', '}).inject(info);

        new Element('a', {
            text: t('Propel Basics'),
            target: '_blank',
            href: 'http://www.propelorm.org/documentation/02-buildtime.html'
        }).inject(info);

        buttonBar.addButton(t('Save'), this.saveDb.bind(this));
        buttonBar.addButton(t('ORM update'), function () {
            ka.wm.open('admin/system/development/orm', {doUpdate: 1});
        }.bind(this));

    },


    /*
     *  Help
     */

    loadHelp: function () {
        if (this.lr) this.lr.cancel();

        this.lr = new Request.JSON({url: _path + 'admin/system/module/getHelp', noCache: 1, onComplete: function (res) {
            this.loader.hide();
            this._renderHelp(res);

        }.bind(this)}).post({name: this.mod/*, lang: this.languageSelect.value*/});
    },

    _renderHelp: function (pHelp) {
        this.panes['help'].empty();

        this.helpPane = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['help']);

        Object.each(pHelp, function (item, index) {
            this.addHelpItem(item);
        }.bind(this));

        var buttonBar = new ka.ButtonBar(this.panes['help']);
        buttonBar.addButton(t('Add help'), this.addHelpItem.bind(this));
        buttonBar.addButton(t('Save'), this.saveHelp.bind(this));

    },

    saveHelp: function () {
        var req = {};
        var items = [];
        this.helpPane.getElements('div.ka-admin-system-module-help').each(function (div) {

            var item = {};
            item.title = div.getElements('input')[0].value;
            item.tags = div.getElements('input')[1].value;
            item.id = div.getElements('input')[2].value;
            item.faq = (div.getElements('input')[3].checked) ? 1 : 0;
            item.help = div.getElement('textarea').value;
            items.include(item);

        }.bind(this));

//        req.lang = this.languageSelect.value;
        req.name = this.mod;
        req.help = JSON.encode(items);
        this.loader.show();

        this.lr = new Request.JSON({url: _path + 'admin/system/module/saveHelp', noCache: 1, onComplete: function () {
            this.loader.hide();
        }.bind(this)}).post(req);
    },

    addHelpItem: function (pItem) {
        if (!pItem) pItem = {};
        var main = new Element('div', {
            'class': 'ka-admin-system-module-help',
            style: 'padding: 5px; border-bottom: 1px solid #ddd; margin: 5px;'
        }).inject(this.helpPane);

        new Element('span', {html: t('Title'), style: 'padding-right: 3px;'}).inject(main);
        new Element('input', {
            'class': 'text',
            style: 'width: 200px;',
            value: pItem.title
        }).inject(main);

        new Element('span', {html: t('Tags'), style: 'padding: 0px 3px;'}).inject(main);
        new Element('input', {
            'class': 'text',
            value: pItem.tags
        }).inject(main);

        new Element('span', {html: t('ID'), style: 'padding: 0px 3px;'}).inject(main);
        new Element('input', {
            'class': 'text',
            value: pItem.id
        }).inject(main);

        new Element('span', {html: t('FAQ?'), style: 'padding: 0px 3px;'}).inject(main);
        new Element('input', {
            type: 'checkbox',
            value: 1,
            checked: (pItem.faq == 1) ? true : false
        }).inject(main);

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Remove'),
            html: '&#xe26b;'
        }).addEvent('click', function () {
            main.destroy();
        }.bind(this)).inject(main);

        new Element('textarea', {
            value: pItem.help,
            style: 'width: 100%; height: 100px;'
        }).inject(main);

    },


    loadLinks: function () {
        if (this.lr) this.lr.cancel();

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/config', noCache: 1, onComplete: function (res) {
            this.loader.hide();
            this._renderLinks(res.data);
        }.bind(this)}).get({name: this.mod});
    },

    _renderLinks: function (pConfig) {
        this.panes['links'].empty();

        var p = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px; padding: 5px;',
            text: t('Show definitions')
        }).inject(this.panes['links']);
        this.layoutPaneItems = p;

        var ch = new ka.Checkbox(p);
        ch.addEvent('change', function(){
            if (!ch.getValue())
                p.addClass('admin-system-modules-hide-layoutSettings');
            else
                p.removeClass('admin-system-modules-hide-layoutSettings');
        });
        ch.setValue(0);
        p.addClass('admin-system-modules-hide-layoutSettings');

        if (pConfig.admin) {
            Object.each(pConfig.admin, function (link, key) {
                this._linksAddNewLevel(key, link, p);
            }.bind(this));
        }

        var buttonBar = new ka.ButtonBar(this.panes['links']);

        buttonBar.addButton(t('Add link'), function () {
            var count = p.getChildren().length;
            this._linksAddNewLevel('first_lvl_id_'+(count+1), {}, p);
        }.bind(this));

        buttonBar.addButton(t('Save'), this.saveLinks.bind(this));

    },

    saveLinks: function () {

        var admin = {};

        this.layoutPaneItems.getChildren('.ka-extension-manager-links-item').each(function(item){
            var input = item.getElement('input');
            admin[input.value ] = this._getLayoutSetting(item);
        }.bind(this));

        var req = {};
        req.name = this.mod;
        req.entryPoints = JSON.encode(admin);
        this.loader.show();

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/entryPoints', noCache: 1, onComplete: function () {
            this.loader.hide();
            ka.loadSettings();
            ka.loadMenu();
        }.bind(this)}).post(req);

    },

    _getLayoutSetting: function (pLayoutItem) {
        var res = {};

        var kaParser = pLayoutItem.retrieve('kaparser');
        res = kaParser.getValue();

        Object.each(res, function(v,k){
            if (v === '')
                delete res[k];
        })

        res['childs'] = {};

        pLayoutItem.getElement('.layoutChilds').getChildren('.ka-extension-manager-links-item').each(function(item){
            var input = item.getElement('input');
            res['childs'][input.value ] = this._getLayoutSetting(item);
        }.bind(this));

        return res;
    },

    _createLayoutLinkSettings: function (pSub, pLink) {

        var table = new Element('table', {width: '100%'}).inject(pSub)
        var tbody = table;

        var kaFields = {
            title: {
                label: t('Title'),
                desc: t('Surround the value with [[ and ]] to make it multilingual.')
            },
            type: {
                label: t('Type'),
                type: 'select',
                items: {
                    '': t('Default'),
                    store: t('Store'),
                    'function': t('Background function'),
                    custom: t('[Window] Custom'),
                    iframe: t('[Window] iFrame'),
                    list: t('[Window] Framework list'),
                    edit: t('[Window] Framework edit'),
                    add: t('[Window] Framework add'),
                    combine: t('[Window] Framework Combine')
                },
                depends: {
                    __info_store__: {
                        needValue: 'store',
                        label: t('Use a own class or a table name'),
                        type: 'label'
                    },
                    __info_form__: {
                        needValue: ['list', 'edit', 'add', 'combine'],
                        label: t('Use a own class or select a form'),
                        type: 'label'
                    },
                    'class': {
                        label: t('PHP Class'),
                        desc: t('Scheme: module/&lt;extKey&gt;/&lt;class&gt;.class.php'),
                        needValue: ['list', 'edit', 'add', 'combine', 'store']
                    },
                    functionType: {
                        needValue: 'function',
                        type: 'select',
                        label: t('Function type'),
                        items: {
                            global: t('Call global defined function'),
                            code: t('Execture code')
                        },
                        depends: {
                            functionName: {
                                type: 'text',
                                label: t('Function name'),
                                needValue: 'global'
                            },
                            functionCode: {
                                type: 'codemirror',
                                needValue: 'code',
                                codemirrorOptions: {
                                    mode: 'javascript'
                                },
                                label: t('Javascript code')
                            }
                        }
                    },
                    __or__: {
                        label: t('or'),
                        type: 'label',
                        needValue: 'store'
                    },
                    table: {
                        label: t('Table'),
                        needValue: 'store',
                        depends: {
                            table_key: {
                                label: t('Table primary column'),
                                needValue: function(n){if(n!='')return true;else return false;}
                            },
                            table_label: {
                                label: t('Table label column'),
                                needValue: function(n){if(n!='')return true;else return false;}

                            }
                        }
                    },
                    '__info_js_name__': {
                        type: 'label',
                        needValue: 'custom',
                        label: t('File name and class information'),
                        help: 'admin/extension-custom-javascript',
                        desc: t('Javascript file: media/&lt;extKey&gt;/admin/js/&lt;pathWithUnderscore&gt;.js and class name: &lt;extKey&gt;_&lt;pathWithUnderscore&gt;.')
                    }
                }
            },
            isLink: {
                label: t('Is link in administration menu bar?'),
                desc: t('Only in the first and second level.'),
                type: 'checkbox',
                depends: {
                    icon: {
                        needValue: 1,
                        label: t('Icon (Optional)'),
                        desc: t('Relative to media/'),
                        type: 'text'
                    }
                }
            },
            __optional__: {
                label: t('Optional'),
                type: 'childrenSwitcher',
                needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                againstField: 'type',
                depends: {
                    multi: {
                        label: t('Allow multiple instances?'),
                        needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                        againstField: 'type',
                        type: 'checkbox'
                    },
                    minWidth: {
                        label: t('Min width'),
                        needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                        againstField: 'type',
                        type: 'number'
                    },
                    minHeight: {
                        label: t('Min height'),
                        needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                        againstField: 'type',
                        type: 'number'
                    },
                    defaultWidth: {
                        label: t('Default width'),
                        needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                        againstField: 'type',
                        type: 'number'
                    },
                    defaultHeight: {
                        label: t('Default height'),
                        needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                        againstField: 'type',
                        type: 'number'
                    },

                    fixedWidth: {
                        label: t('Fixed width'),
                        needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                        againstField: 'type',
                        type: 'number'
                    },
                    fixedHeight: {
                        label: t('Fixed height'),
                        needValue: ['custom', 'iframe', 'list', 'edit', 'add', 'combine'],
                        againstField: 'type',
                        type: 'number'
                    }
                }
            }
        }

        var kaParser = new ka.Parse(tbody, kaFields, {allTableItems:1}, {win: this.win});
        pSub.getParent().store('kaparser', kaParser);
        kaParser.setValue(pLink);
    },

    _linksAddNewLevel: function (pKey, pLink, pParent) {

        var lvl1 = new Element('div', {
            style: 'border: 1px solid #ccc; padding-left: 0px; padding-bottom: 5px; background-color: #e8e8e8; margin: 5px 0px; position: relative;',
            'class': 'layoutItem ka-extension-manager-links-item'
        }).inject(pParent);

        var header = new Element('div',{
            'style': 'border-bottom: 1px solid silver; background-color: #e1e1e1; padding: 2px;'
        }).inject(lvl1);

        new Element('input', {
            value: pKey,
            'class': 'text',
            style: 'margin-left: 4px;'
        }).inject(header);

        var subDelBtn = new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Remove entry point'),
            html: '&#xe26b;'
        }).addEvent('click', function () {
            this.win._confirm(t('Delete?'), function (res) {
                if (!res)return;
                lvl1.destroy();
            });
        }.bind(this)).inject(header);

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: t('Move up'),
            html: '&#xe2ca;'
        }).addEvent('click', function () {
            if (lvl1.getPrevious()) {
                lvl1.inject(lvl1.getPrevious(), 'before');
            }
        }.bind(this)).inject(header);

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: t('Move down'),
            html: '&#xe2cc;'
        }).addEvent('click', function () {
            if (lvl1.getNext()) {
                lvl1.inject(lvl1.getNext(), 'after');
            }
        }.bind(this)).inject(header);

        var showDefinition = new Element('div', {
            'class': 'admin-system-modules-edit-pane-showDefinition',
            style: 'width: 250px;'
        }).inject(header);

        var ch = new ka.Checkbox(showDefinition);
        ch.addEvent('change', function(){
            if(ch.getValue()){
                lvl1.addClass('admin-system-modules-show-layoutSettings');
            } else {
                lvl1.removeClass('admin-system-modules-show-layoutSettings');
            }
        });

        new Element('div',{
            style: 'position: absolute; left: 70px; top: 6px; color: gray;',
            text: t('Show definition')
        }).inject(showDefinition);

        var sub = new Element('div', {
            style: 'padding: 2px; padding-left: 25px',
            'class': 'layoutSettings'
        }).inject(lvl1);

        var childs = new Element('div', {
            style: 'padding: 2px; padding-left: 25px;',
            'class': 'layoutChilds'
        }).inject(lvl1);

        this._createLayoutLinkSettings(sub, pLink);

        /*
        var subAddBtn = new Element('img', {
            'src': _path + PATH_MEDIA + '/admin/images/icons/add.png',
            title: t('Add Link'),
            style: 'cursor: pointer; position: relative; top: 3px; left: 2px;'
        }).addEvent('click', function () {
            this._linksAddNewLevel('mykey', {}, childs);
        }.bind(this)).inject(sub);
        */

        document.id(new ka.Button('Add children'))
        .addEvent('click', function(){
            var count = childs.getChildren().length+1;
            this._linksAddNewLevel('path_key_'+count, {}, childs);
        }.bind(this))
        .setStyle('margin-left', 15)
        .inject(lvl1)

        if (pLink.childs) {
            Object.each(pLink.childs, function (item, key) {
                this._linksAddNewLevel(key, item, childs);
            }.bind(this));
        }

    },

    _loadGeneral: function (pConfig) {
        this.panes['general'].empty();

        var p = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['general']);

        this.generellFields = {};

        var fields = {
            title: {
                label: t('Title'),
                type: 'text'
            },
            desc: {
                label: t('Description'),
                type: 'textarea'
            },
            tags: {
                label: t('Tags'),
                type: 'text'
            },
            screenshots: {
                label: t('Screenshots'),
                type: 'text',
                desc: t('Screenshots in %s').replace('%s', 'media/'+this.mod + '/_screenshots/'),
                disabled: true
            },
            owner: {
                label: t('Owner'),
                type: 'text'
            },
            version: {
                label: t('Version'),
                type: 'text'
            },
            depends: {
                label: t('Depends'),
                desc: t('Comma seperated list of extension. Example kryn=>0.5.073,admin>0.4.'),
                help: 'extensions-dependency', 
                type: 'text'
            },
            community: {
                label: t('Community'),
                type: 'checkbox',
                desc: t('Is this extension available under kryn.org/extensions.')
            },
            category: {
                label: t('Category'),
                desc: t('What kind of extension is this?'), 
                type: 'select',
                items: {
                    1: 'Information/Editorial office',
                    2: 'Multimedia',
                    3: 'SEO',
                    4: 'Widget',
                    5: 'Statistic',
                    6: 'Community',
                    7: 'Interface',
                    8: 'System',
                    9: 'Advertisement',
                    10: 'Security',
                    11: 'ECommerce',
                    12: 'Download / Documents',
                    13: 'Theme / Layouts',
                    14: 'Language package',
                    15: 'Data acquisition',
                    16: 'Collaboration'
                },
            },
            writableFiles: {
                label: t('Writable files'),
                desc: t('Specify these files which are not automaticly overwritten during an update (if a modification exist). One file per line. Use * as wildcard. Read docs for more information'),
                type: 'textarea'
            }

        }

        /*
        var title = ( pConfig.title ) ? pConfig.title : '';
        this.generellFields['title'] = new ka.Field({
            label: t('Title'), value: title
        }).inject(p);

        var desc = pConfig.desc ? pConfig.desc : '';
        this.generellFields['desc'] = new ka.Field({
            label: t('Description'), value: desc, type: 'textarea'
        }).inject(p);

        var tags = ( pConfig.tags ) ? pConfig.tags : '';
        this.generellFields['tags'] = new ka.Field({
            label: t('Tags'), value: tags, desc: t('Comma separated values')
        }).inject(p);

        var screenshotsCount = 'No Screenshots found';
        if (pConfig.screenshots) {
            screenshotsCount = pConfig.screenshots.length;
        }

        new ka.Field({
            label: t('Screenshots'), value: screenshotsCount, desc: t('Screenshots in %s').replace('%s', 'media/'+this.mod + '/_screenshots/'),
            disabled: true
        }).inject(p);

        var owner = ka.settings.system.communityEmail;
        if (pConfig.owner == "" || !pConfig.owner) {
            owner = t('No owner - local version');
        }

        var owner = new ka.Field({
            label: t('Owner'), value: owner, disabled: true
        }).inject(p);

        var _this = this;
        if (ka.settings.system.communityId > 0 && !pConfig.owner > 0) {
            new ka.Button(t('Set to my extension: ' + ka.settings.system.communityEmail))
            .setStyle('position', 'relative').setStyle('left', '25px').addEvent('click', function () {
                this.setToMyExtension = ka.settings.system.communityId;
                owner.setValue(ka.settings.system.communityEmail);
            }.bind(this)).inject(p);
        }

        this.generellFields['version'] = new ka.Field({
            label: t('Version'), value: pConfig.version
        }).inject(p);

        this.generellFields['depends'] = new ka.Field({
            label: t('Dependency'), desc: t('Comma seperated list of extension. Example kryn=>0.5.073,admin>0.4.'), help: 'extensions-dependency', value: pConfig.depends
        }).inject(p);

        this.generellFields['community'] = new ka.Field({
            label: t('Visible in community'), desc: t('Is this extension searchable and accessible for others?'), value: pConfig.community, type: 'checkbox'
        }).inject(p);

        this.generellFields['category'] = new ka.Field({
            label: t('Category'), desc: t('What kind of extension is this?'), value: pConfig.category, type: 'select',
            tableItems: [
                {v: t('Information/Editorial office'), i: 1},
                {v: t('Multimedia'), i: 2},
                {v: t('SEO'), i: 3},
                {v: t('Widget'), i: 4},
                {v: t('Statistic'), i: 5},
                {v: t('Community'), i: 6},
                {v: t('Interface'), i: 7},
                {v: t('System'), i: 8},
                {v: t('Advertisement'), i: 9},
                {v: t('Security'), i: 10},
                {v: t('ECommerce'), i: 11},
                {v: t('Download & Documents'), i: 12},
                {v: t('Theme / Layouts'), i: 13},
                {v: t('Language package'), i: 14},
                {v: t('Data acquisition'), i: 19},
                {v: t('Collaboration'), i: 18},
                {v: t('Other'), i: 16}
            }
        }).inject(p);

        this.generellFields['writableFiles'] = new ka.Field({
            label: t('Writable files'), desc: t('Specify these files which are not automaticly overwritten during an update (if a modification exist). One file per line. Use * as wildcard. Read docs for more information'), value: pConfig.writableFiles, type: 'textarea'
        }).inject(p);*/

        this.generalFieldsObj = new ka.Parse(p, fields, {allTableItems: 1});

        var value = pConfig;


        value.screenshots = 'No Screenshots found';
        if (pConfig.screenshots) {
            value.screenshots = pConfig.screenshots.length;
        }

        if (ka.settings.system.communityId > 0 && !pConfig.owner > 0) {
            var ownerField = this.generalFieldsObj.getField('owner');
            new ka.Button(t('Set to my extension: ' + ka.settings.system.communityEmail))
            .addEvent('click', function () {
                this.setToMyExtension = ka.settings.system.communityId;
                ownerField.setValue(ka.settings.system.communityEmail);
            }.bind(this)).inject(document.id(ownerField).getElement('.ka-field-field'));
        }

        this.generalFieldsObj.setValue(value);

        var buttonBar = new ka.ButtonBar(this.panes['general']);
        buttonBar.addButton(t('Save'), this.saveGeneral.bind(this));

    },

    saveGeneral: function () {
        var req = this.generalFieldsObj.getValue();


        if (this.setToMyExtension > 0) {
            req['owner'] = this.setToMyExtension;
        }

        req.name = this.mod;

        this.loader.show();
        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/general', noCache: 1, onComplete: function () {
            this.loader.hide();
        }.bind(this)}).post(req);
    },

    loadGeneral: function () {
        this.loader.show();
        if (this.lr) this.lr.cancel();
        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/config', noCache: 1, onComplete: function (pResult) {
            this._loadGeneral(pResult.data);
            this.loader.hide();
        }.bind(this)}).get({name: this.mod});
    },

    loadLayouts: function () {
        this.loader.show();
        if (this.lr) this.lr.cancel();
        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/config', noCache: 1, onComplete: function (pResult) {
            this._loadLayouts(pResult.data);
            this.loader.hide();
        }.bind(this)}).get({name: this.mod});
    },

    _loadLayouts: function (pConfig) {
        this.panes['layouts'].empty();
        var p = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['layouts']);

        this.layoutsAddThemeButton = new Element('div').inject(p);

        if (pConfig.themes) {
            Object.each(pConfig.themes, function (templates, themeTitle) {
                this._layoutsAddTheme(themeTitle, templates);
            }.bind(this));
        }

        var buttonBar = new ka.ButtonBar(this.panes['layouts']);

        buttonBar.addButton(t('Add theme'), function () {
            this._layoutsAddTheme('Theme title', {});
        }.bind(this));
        buttonBar.addButton(t('Save'), this.saveLayouts.bind(this));
    },

    saveLayouts: function () {
        this.loader.show();

        var themes = {};
        this.panes['layouts'].getElements('div[class=themeContainer]').each(function (container) {
            var themeTitle = container.getElement('input.themeTitle').value;
            var themeTemplates = {layouts: {}, navigations: {}, contents: {}, properties: {}, publicProperties: {}};

            container.getElements('ol.layoutContainerLayout li').each(function (template) {
                themeTemplates.layouts[template.getElements('input')[0].value] = template.getElements('input')[1].value;
            });

            container.getElements('ol.layoutContainerContent li').each(function (template) {
                themeTemplates.contents[template.getElements('input')[0].value] = template.getElements('input')[1].value;
            });

            container.getElements('ol.layoutContainerNavigation li').each(function (template) {
                themeTemplates.navigations[template.getElements('input')[0].value] = template.getElements('input')[1].value;
            });

            container.getElements('div.themeProperties li').each(function (template) {
                themeTemplates.properties[template.getElements('input')[0].value] = template.getElements('input')[1].value;
            });

            container.getElements('div.publicProperties li').each(function (template) {
                themeTemplates.publicProperties[template.getElements('input')[0].value] = [template.getElements('input')[1].value, template.getElements('select')[0].value];
            });

            themes[themeTitle] = themeTemplates;
        });

        this.lr = new Request.JSON({url: _path + 'admin/system/module/saveLayouts', noCache: 1, onComplete: function () {
            this.loader.hide();
            ka.loadSettings();
        }.bind(this)}).post({name: this.mod, themes: JSON.encode(themes) });
    },

    _addPublicProperty: function (pContainer, pKey, pTitle, pType) {
        var li = new Element('li').inject(pContainer);

        new Element('input', {
            'class': 'text',
            style: 'width: 110px',
            value: (pKey) ? pKey : t('propertie_key')
        }).inject(li).focus();

        new Element('span', {
            text: ' : '
        }).inject(li);

        new Element('input', {
            'class': 'text',
            style: 'width: 140px;',
            value: (pTitle) ? pTitle : t('Propertie title')
        }).inject(li);

        new Element('span', {
            text: ' : '
        }).inject(li);


        var select = new Element('select', {

        }).inject(li);

        Object.each({
            text: 'Text',
            'number': 'Number',
            'checkbox': 'Checkbox',
            page: 'Page/Deposit',
            file: 'File'
        }, function (title, key) {
                new Element('option', {
                    html: _(title),
                    value: key
                }).inject(select);
            });

        select.value = pType;


        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Remove'),
            html: '&#xe26b;'
        }).addEvent('click', function () {
            this.win._confirm(t('Really delete'), function(ok){
                if (!ok) return;
                li.destroy();
            });
        }.bind(this)).inject(li);
    },

    _addThemeProperty: function (pContainer, pKey, pValue) {
        var li = new Element('li').inject(pContainer);

        new Element('input', {
            'class': 'text',
            value: (pKey) ? pKey : t('propertie_key')
        }).inject(li).focus();

        new Element('span', {
            text: ' : '
        }).inject(li);

        new Element('input', {
            'class': 'text',
            style: 'width: 200px;',
            value: (pValue) ? pValue : t('Propertie value')
        }).inject(li);

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Remove'),
            html: '&#xe26b;'
        }).addEvent('click', function () {

            this.win._confirm(t('Really delete'), function(ok){
                if (!ok) return;
                li.destroy();
            });
        }.bind(this)).inject(li);
    },

    _layoutsAddTheme: function (pTitle, pTemplates) {
        var myp = new Element('div', {'class': 'themeContainer'}).inject(this.layoutsAddThemeButton, 'before');

        new Element('input', {
            value: pTitle,
            'class': 'text themeTitle',
            style: 'margin: 4px; width: 250px;'
        }).inject(myp);

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Remove'),
            html: '&#xe26b;'
        }).addEvent('click', function () {
            this.win._confirm(t('Really delete this theme ?'), function (res) {
                if (!res) return;
                myp.destroy();
            }.bind(this))
        }.bind(this)).inject(myp);

        var p = new Element('div', {
            style: 'padding-left: 20px; border-bottom: 1px solid silver; padding-bottom: 2px; margin-bottom: 2px;',
            'class': 'layoutContainer'
        }).inject(myp);

        var addTemplate = function (pLayoutTitle, pLayoutFile, pTo) {
            var li = new Element('li').inject(pTo);
            new Element('input', {
                'class': 'text', value: pLayoutTitle
            }).inject(li);
            new Element('span', {text: ' : '}).inject(li);
            var file = new Element('input', {
                'class': 'text', value: pLayoutFile, style: 'width: 200px;'
            }).inject(li);
            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
                title: _('Edit template'),
                html: '&#xe00f;'
            }).addEvent('click',
                function () {
                    ka.wm.open('admin/files/edit', {file: {path: '/' + file.value}});
                }).inject(li);
            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
                title: _('Remove'),
                html: '&#xe26b;'
            }).addEvent('click', function () {
                this.win._confirm(t('Really delete this template ?'), function (res) {
                    if (!res) return;
                    li.destroy();
                }.bind(this));
            }.bind(this)).inject(li);
        }.bind(this);


        //public properties
        var title = new Element('h3', {
            html: 'Public properties'
        }).inject(p);

        var publicproperties = new Element('div', {
            'class': 'publicProperties'
        }).inject(p);

        var olpp = new Element('ol').inject(publicproperties);

        new Element('img', {
            'src': _path + PATH_MEDIA + '/admin/images/icons/add.png',
            title: t('Add public property'),
            style: 'cursor: pointer; position: relative; top: 3px; margin-left: 3px;'
        }).addEvent('click', function () {
            this._addPublicProperty(olpp);
        }.bind(this)).inject(title);

        if (pTemplates.publicProperties) {
            Object.each(pTemplates.publicProperties, function (val, key) {
                this._addPublicProperty(olpp, key, val[0], val[1]);
            }.bind(this));
        }


        //properties
        var title = new Element('h3', {
            html: 'Theme properties'
        }).inject(p);

        var properties = new Element('div', {
            'class': 'themeProperties'
        }).inject(p);

        var ol = new Element('ol').inject(properties);

        new Element('img', {
            'src': _path + PATH_MEDIA + '/admin/images/icons/add.png',
            title: t('Add property'),
            style: 'cursor: pointer; position: relative; top: 3px; margin-left: 3px;'
        }).addEvent('click', function () {
            this._addThemeProperty(ol);
        }.bind(this)).inject(title);

        if (pTemplates.properties) {
            Object.each(pTemplates.properties, function (val, key) {
                this._addThemeProperty(ol, key, val);
            }.bind(this));
        }


        /// layouts 
        var title = new Element('h3', {
            html: t('Layout templates')
        }).inject(p);

        this.layoutsLayoutContainer = new Element('ol', {
            'class': 'layoutContainerLayout'
        }).inject(p);
        new Element('img', {
            'src': _path + PATH_MEDIA + '/admin/images/icons/add.png',
            title: t('Add layout template'),
            style: 'cursor: pointer; position: relative; top: 3px; margin-left: 3px'
        }).addEvent('click', function () {
            addTemplate('My title', this.mod + '/layout_mytitle.tpl', this.layoutsLayoutContainer);
        }.bind(this)).inject(title);

        if (pTemplates.layouts) {
            Object.each(pTemplates.layouts, function (file, title) {
                addTemplate(title, file, this.layoutsLayoutContainer);
            }.bind(this));
        }


        /// contents

        var title = new Element('h3', {
            html: t('Element templates')
        }).inject(p);

        this.layoutsContentContainer = new Element('ol', {
            'class': 'layoutContainerContent'
        }).inject(p);
        new Element('img', {
            'src': _path + PATH_MEDIA + '/admin/images/icons/add.png',
            title: t('Add element template'),
            style: 'cursor: pointer; position: relative; top: 3px; margin-left: 3px'
        }).addEvent('click', function () {
            addTemplate('My title', this.mod + '/content_mytitle.tpl', this.layoutsContentContainer);
        }.bind(this)).inject(title);

        if (pTemplates.contents) {
            Object.each(pTemplates.contents, function (file, title) {
                addTemplate(title, file, this.layoutsContentContainer);
            }.bind(this));
        }

        /// navigations
        title = new Element('h3', {
            html: t('Navigation templates')
        }).inject(p);

        this.layoutsNavigationContainer = new Element('ol', {
            'class': 'layoutContainerNavigation'
        }).inject(p);
        new Element('img', {
            'src': _path + PATH_MEDIA + '/admin/images/icons/add.png',
            title: t('Add navigation template'),
            style: 'cursor: pointer; position: relative; top: 3px; margin-left: 3px'
        }).addEvent('click', function () {
            addTemplate('My title', this.mod + '/navigation_mytitle.tpl', this.layoutsNavigationContainer);
        }.bind(this)).inject(title);

        if (pTemplates.navigations) {
            Object.each(pTemplates.navigations, function (file, title) {
                addTemplate(title, file, this.layoutsNavigationContainer);
            }.bind(this));
        }

    },

    loadLanguage: function () {


        this.loader.hide();
        if (this.lr) this.lr.cancel();
        var div = this.panes['language'];
        div.empty();

        new Element('h3', {
            text: t('Translations')
        }).inject(div);

        var left = new Element('div', {style: 'position: absolute; left: 5px; top: 50px; right: 90px;'}).inject( div );
        this.langProgressBars = new ka.Progress(t('Extracting ...'), true);
        this.langProgressBars.inject( left );

        var right = new Element('div', {style: 'position: absolute; right: 10px; top: 50px;'}).inject( div )
        this.langTranslateBtn = new ka.Button(t('Translate')).inject( right );
        this.langTranslateBtn.addEvent('click', function(){
            ka.wm.open('admin/system/languages/edit', {/*lang: this.languageSelect.value, */module: this.mod});
        }.bind(this));
        this.langTranslateBtn.deactivate();

        this.lr = new Request.JSON({url: _path+'admin/system/languages/overviewExtract', noCache:1,
            onComplete: function( pRes ){

                this.langProgressBars.setUnlimited( false );
                this.langProgressBars.setValue( (pRes.countTranslated/pRes.count)*100 );

                this.langProgressBars.setText(
                    t('%1 of %2 translated')
                        .replace('%1', pRes.countTranslated)
                        .replace('%2', pRes['count'])
                );

                this.langTranslateBtn.activate();
        }.bind(this)}).post({module: this.mod/*, lang: this.languageSelect.value*/});

    },

    loadObjects: function(){


        if (this.lr) this.lr.cancel();
        this.panes['objects'].empty();

        this.pluginsPane = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['objects']);

        this.objectTBody = new Element('table', {
            'class': 'ka-Table-head ka-Table-body', //
            style: 'position: relative; top: 0px; background-color: #eee',
            cellpadding: 0, cellspacing: 0
        }).inject(this.pluginsPane);

        var tr = new Element('tr').inject(this.objectTBody);
        new Element('th', {
            text: t('Object key'),
            style: 'width: 260px;'
        }).inject(tr);

        new Element('th', {
            text: t('Object label'),
            style: 'width: 260px;'
        }).inject(tr);

        new Element('th', {
            text: t('Actions')
        }).inject(tr);

        var buttonBar = new ka.ButtonBar(this.panes['objects']);
        buttonBar.addButton(t('ORM update'), function () {
            ka.wm.open('admin/system/development/orm', {doUpdate: 1});
        }.bind(this));

        buttonBar.addButton([t('Add object'), '#icon-plus-alt'], function(){
            this.addObject();
        }.bind(this));

        this.objectSaveButton = buttonBar.addButton(t('Save'), this.saveObjects.bind(this));

        document.id(this.objectSaveButton).addClass('ka-Button-blue');

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/objects', noCache: 1,
        onComplete: function (pResult) {

            if (pResult.data) {
                Object.each(pResult.data, function (item, key) {
                    this.addObject(item, key);
                }.bind(this));
            }

            this.loader.hide();

        }.bind(this)}).get({name: this.mod});
    },

    updateOrm: function(pCmd, pCallback){

        if (this.lr) this.lr.cancel();

        this.lr = new Request.JSON({url: _path + 'admin/system/orm/'+pCmd, noCache: 1,
            onComplete: pCallback}).get();
    },

    updateOrmWriteModel: function(pCallback){

        if (this.lr) this.lr.cancel();

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/model/from-objects', noCache: 1,
            onComplete: function (response) {
                if (!response.error)
                    pCallback(response);
        }.bind(this)}).post({name: this.mod});
    },

    printOrmError: function(pResponse){
        this.objectSaveButton.stopTip(t('Failed.'));

        var div = new Element('div');

        new Element('h2', {
            text: 'ORM Error: '+pResponse.error
        }).inject(div);

        new Element('div', {
            style: 'position: absolute; top: 50px; left: 5px; right: 5px; bottom: 5px; overflow: auto; white-space: pre; background-color: white; padding: 5px;',
            text: 'ORM Error: '+pResponse.message
        }).inject(div);

        var dialog = this.win.newDialog(div, true);
        dialog.setStyle('width', '80%');
        dialog.setStyle('height', '90%');
        dialog.center();

        var ok = new ka.Button(t('Ok'))
        .addEvent('click', dialog.close)
        .setButtonStyle('blue')
        .inject(dialog.bottom);
    },

    updateORM: function(){

        this.objectSaveButton.startTip(t('Object saved. Write model.xml ...'));

        this.updateOrmWriteModel(function(){
            this.objectSaveButton.startTip(t('Saved. Update PHP models ...'));
            this.updateOrm('models', function(response){
                if (response.error){
                    this.printOrmError(response);
                } else {
                    this.objectSaveButton.startTip(t('Saved. Update database tables ...'));
                    this.updateOrm('update', function(response){
                        if (response.error){
                            this.printOrmError(response);
                        } else {
                            this.objectSaveButton.startTip(t('Done.'));
                        }
                    }.bind(this));
                }
            }.bind(this));
        }.bind(this));
    },

    writeObjectModel: function(pObjectKey){

        this.win.setLoading(true, t('Write model to model.xml'));

        new Request.JSON({url: _path+'admin/system/module/editor/model/from-object', onComplete: function(pResult){

            this.win.setLoading(false);

        }.bind(this)}).post({name: this.mod, object: pObjectKey});

    },

    saveObjects: function(){

        var objects = {};

        this.objectTBody.getChildren('.object').each(function(object){

            var definition = object.definition;
            var iKey = object.getElements('input')[0];
            var iLabel = object.getElements('input')[1];

            definition.label = iLabel.value;
            objects[iKey.value] = definition;

        });

        if (this.lr) this.lr.cancel();
        this.objectSaveButton.startTip(t('Saving ...'));

        var req = {};
        req.objects = JSON.encode(objects);
        req.name = this.mod;


        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/objects', noCache: 1, onComplete: function (res) {
            ka.loadSettings(['configs']);
            this.updateORM();
        }.bind(this)}).post(req);

    },

    openObjectSettings: function(pTr){

        this.dialog = this.win.newDialog('', true);

        this.dialog.setStyles({
            height: '95%',
            width: '95%'
        });
        this.dialog.center();

        var kaFields = {
            '__general__': {
                type: 'tab',
                tabFullPage: true,
                label: t('General'),
                depends: {
                    'desc': {
                        label: t('Description')
                    },
                    dataModel: {
                        type: 'select',
                        label: t('Class'),
                        items: {
                            'propel': t('Propel ORM'),
                            'custom': t('Custom class')
                        },
                        desc: t('Define a table or a own object class'),
                        depends: {
                            table: {
                                needValue: 'propel',
                                label: t('Table name'),
                                modifier: 'underscore|trim'
                            },
                            objectLabel: {
                                label: t('Label field'),
                                desc: t('Default field for the label.'),
                                type: 'text',
                                modifier: 'camelcase|trim|lcfirst'
                            },
                            nested: {
                                label: t('Nested Sets'),
                                desc: t('Adds fields: lvl(int), lft(int) and rgt(int)'),
                                type: 'checkbox',
                                depends: {
                                    nestedLabel: {
                                        needValue: 1,
                                        label: t('Label field')
                                    },
                                    nestedRootAsObject: {
                                        needValue: 1,
                                        label: t('Root as object (Optional)'),
                                        type: 'checkbox',
                                        depends: {
                                            nestedRootObject: {
                                                needValue: 1,
                                                label: t('Object key')
                                            },
                                            nestedRootObjectField: {
                                                needValue: 1,
                                                label: t('Foreign key'),
                                                desc: t('Which field in the current object contains the primary value of the parent object?')
                                            },
                                            nestedRootObjectLabel: {
                                                needValue: 1,
                                                label: t('Label field')
                                            },
                                            nestedRootObjectExtraFields: {
                                                needValue: 1,
                                                label: t('Extra fields (Optional)'),
                                                desc: t('Comma separated. The backend (admin/backend/objectTreeRoot) returns primary key, label and these extra fields. You may use this to get more fields in the user interface classes.'),
                                                empty: true
                                            }
                                        }
                                    }
                                }
                            },
                            'class': {
                                needValue: 'custom',
                                label: t('Class name'),
                                desc: t('Class file should be under module/&lt;extKey&gt;/model/&lt;className&gt;.class.php')
                            }
                        }
                    },
                    multiLanguage: {
                        label: t('Multi-language'),
                        type: 'checkbox',
                        desc: t("You need then a extra field 'lang' varchar(2)")
                    },
                    domainDepended: {
                        label: t('Domain depended'),
                        type: 'checkbox',
                        desc: t("You need then a extra field 'domain_id' int")
                    },
                    plugins: {
                        label: t('Plugins (View controller)'),
                        desc: t('Which plugins handles the frontend output of this object? Comma separated.')
                    }
                }
            },
            '__selection__':{
                type: 'tab',
                tabFullPage: true,
                label: t('Data'),
                depends: {
                    blacklistSelection: {
                        needValue: 1,
                        label: t('Blacklist selection'),
                        desc: t('Enter fileds which are not selectable through the REST API. Comma separated.')
                    },
                    chooserIcon: {
                        needValue: 1,
                        label: t('Chooser icon'),
                        desc: t('Relative to media/.')
                    },
                    chooserFieldType: {
                        needValue: 1,
                        label: t('Field UI'),
                        type: 'select',
                        items: {
                            'default': 'Framework',
                            'custom': 'Custom javascript class'
                        },
                        depends: {
                            'chooserFieldJavascriptClass': {
                                needValue: 'custom',
                                label: t('Javascript class name'),
                                desc: t('You can inject javascript files through extension settings to make a javascript class available.')
                            },
                            'chooserFieldDataModel': {
                                needValue: 'default',
                                label: t('Data source'),
                                type: 'select',
                                items: {
                                    'default': 'Framework',
                                    'custom': 'Custom class'
                                },
                                depends: {
                                    chooserFieldDataModelClass: {
                                        label: t('PHP Class'),
                                        needValue: 'custom',
                                        desc: t('Have to be at module/&lt;extKey&gt;/model/&lt;className&gt;.class.php')
                                    },
                                    chooserFieldDataModelCondition: {
                                        needValue: 'default',
                                        label: t('Additional condition'),
                                        type: 'condition'
                                    }
                                }
                            },
                            chooserFieldDataModelFields: {
                                label: t('Columns'),
                                type: 'fieldTable',
                                needValue: 'default',
                                desc: t('For tables or select boxes.'),
                                asFrameworkColumn: true,
                                withoutChildren: true,
                                tableitem_title_width: 200,
                                addLabel: t('Add column')
                            },
                            chooserFieldDataModelField: {
                                label: t('Label key'),
                                needValue: 'default',
                                type: 'text',
                                desc: t('In field mode')
                            }
                        }
                    },
                    __chooserBrowserTree__: {
                        againstField: 'nested',
                        needValue: 1,
                        type: 'label',
                        label: t('Browser UI (tree)'),
                        desc: t('Only for nested objects.'),
                        depends: {
                            chooserBrowserTreeDataModel: {
                                needValue: 1,
                                label: t('Data model'),
                                items: {
                                    'default': 'Framework',
                                    'custom': 'Custom class'
                                },
                                type: 'select',
                                depends: {
                                    chooserBrowserTreeDataModelClass: {
                                        label: t('PHP Class'),
                                        needValue: 'custom',
                                        desc: t('Have to be at module/&lt;extKey&gt;/&lt;className&gt;.class.php. Reade the manual for more information.')
                                    }
                                }
                            },
                            chooserBrowserTreeJavascript: {
                                label: t('Javascript UI class'),
                                items: {
                                    'default': 'Framework',
                                    'custom': 'Custom class'
                                },
                                type: 'select',
                                depends: {
                                    chooserBrowserTreeJavascriptClass: {
                                        needValue: 'custom',
                                        label: t('Javascript class'),
                                        desc: t('Define the javascript class which is used to display the chooser. Include the javascript file through "Javascript files" under tab "Extras"')
                                    }
                                }
                            },

                            chooserBrowserTreeRootObjectFixedIcon: {
                                type: 'checkbox',
                                needValue: 1,
                                againstField: 'nestedRootAsObject',
                                label: t('Fixed root icon'),
                                depends: {
                                    chooserBrowserTreeRootObjectIconPath: {
                                        needValue: 1,
                                        type: 'file',
                                        label: t('Icon field')
                                    },
                                    chooserBrowserTreeRootObjectIcon: {
                                        needValue: 0,
                                        label: t('Icon field')
                                    },
                                    chooserBrowserTreeRootObjectIconMapping: {
                                        label: t('Icon path mapping'),
                                        needValue: 0,
                                        asHash: true,
                                        type: 'array',
                                        columns: [
                                            {label: t('Value'), width: '30%'},
                                            {label: t('Icon path')}
                                        ],
                                        fields: {
                                            value: {
                                                type: 'text'
                                            },
                                            path: {
                                                type: 'file'
                                            }
                                        }
                                    }
                                }
                            },
                            chooserBrowserTreeFixedIcon: {
                                type: 'checkbox',
                                label: t('Fixed icon'),
                                depends: {
                                    chooserBrowserTreeIconPath: {
                                        needValue: 1,
                                        type: 'file',
                                        label: t('Icon field')
                                    },
                                    chooserBrowserTreeIcon: {
                                        needValue: 0,
                                        label: t('Icon field')
                                    },
                                    chooserBrowserTreeIconMapping: {
                                        label: t('Icon path mapping'),
                                        needValue: 0,
                                        type: 'array',
                                        columns: [
                                            {label: t('Value'), width: '30%'},
                                            {label: t('Icon path')}
                                        ],
                                        fields: {
                                            value: {
                                                type: 'text'
                                            },
                                            path: {
                                                type: 'file'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    chooserBrowserType: {
                        needValue: 1,
                        label: t('Browser UI (chooser)'),
                        items: {
                            'default': 'Framework',
                            'custom': 'Custom javascript class'
                        },
                        type: 'select',
                        depends: {
                            chooserBrowserJavascriptClass: {
                                needValue: 'custom',
                                label: t('Javascript class'),
                                desc: t('Define the javascript class which is used to display the chooser. Include the javascript file through "Javascript files" under tab "Extras"')
                            },
                            chooserBrowserOptions: {
                                label: t('UI properties'),
                                needValue: 'custom',
                                desc: t('You can allow extensions to set some properties when providing your object chooser.'),
                                type: 'fieldTable'
                            },
                            chooserBrowserAutoColumns: {
                                label: t('Columns in the chooser table'),
                                needValue: 'default',
                                type: 'fieldTable',
                                asFrameworkColumn: true,
                                withoutChildren: true,
                                tableitem_title_width: 200,
                                addLabel: t('Add column')
                            },
                            'chooserBrowserDataModel': {
                                type: 'select',
                                label: t('Data source'),
                                items: {
                                    'default': 'Default',
                                    'custom': 'Custom PHP class',
                                    'none': 'None'
                                },
                                depends: {
                                    chooserBrowserDataModelClass: {
                                        label: t('PHP Class'),
                                        needValue: 'custom',
                                        desc: t('Have to be at module/&lt;extKey&gt;/model/&lt;className&gt;.class.php. Reade the manual for more information.')
                                    },
                                    chooserBrowserDataModelFields: {
                                        needValue: 'custom',
                                        againstField: 'chooserBrowserType',
                                        label: t('Object fields'),
                                        desc: t('Comma separated. Without primary keys. (Ignore this when you use a own php class)')
                                    }
                                }
                            }
                        }
                    }
                }
            }
        };



        var definition = pTr.definition;

        var tbody = new Element('table', {
            width: '100%'
        }).inject(this.dialog.content);

        var kaParseObj = new ka.Parse(tbody, kaFields, {allTableItems: true, tableitem_title_width: 180}, {win: this.win});

        new ka.Button(t('Cancel')).addEvent('click', this.cancelObjectSettings.bind(this)).inject(this.dialog.bottom);

        new ka.Button(t('Apply')).addEvent('click', function(){

            var fields = Object.clone(pTr.definition.fields);
            var values = kaParseObj.getValue();

            pTr.definition = values;
            pTr.definition.fields = fields;

            this.cancelObjectSettings();

        }.bind(this))

        .setButtonStyle('blue')
        .inject(this.dialog.bottom);


        //switcher
        if (definition.table){
            definition.__dataModel__ = 'table';
        }

        if (definition)
            kaParseObj.setValue(definition);

    },

    cancelObjectSettings: function(){
        if (this.dialog){
            this.dialog.close();
            delete this.dialog;
        }

    },

    addObject: function(pDefinition, pKey){


        var tr = new Element('tr', {
            'class': 'object'
        }).inject(this.objectTBody);

        tr.definition = pDefinition || {};

        var leftTd = new Element('td').inject(tr);
        var rightTd = new Element('td').inject(tr);
        var actionTd = new Element('td').inject(tr);

        var tr2 = new Element('tr').inject(this.objectTBody);
        var bottomTd = new Element('td', {style: 'border-bottom: 1px solid silver', colspan: 4}).inject(tr2);

        var iKey = new ka.Field({
            type: 'text',
            noWrapper: true,
            modifier: 'camelcase|trim|lcfirst',
            value:pKey?pKey:''
        }, leftTd);

        new Element('input', {'class': 'text', style: 'width: 250px;', value:pDefinition?pDefinition['label']:''}).inject(rightTd);

        tr.store('key', iKey);

        var fieldsBtn = new ka.Button(t('Fields')).inject(actionTd);

        new ka.Button(t('Settings'))
        .addEvent('click', this.openObjectSettings.bind(this,tr))
        .inject(actionTd);


        if (pDefinition && pDefinition.dataModel != 'custom'){
            new ka.Button(t('Write model'), this.writeObjectModel.bind(this, pKey))
            .inject(actionTd);
        }


        if (pDefinition){
            new ka.Button(t('Window wizard'))
            .addEvent('click', this.openObjectWizard.bind(this,pKey, pDefinition))
            .inject(actionTd);
        }

        new Element('a', {
            style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
            title: _('Remove'),
            html: '&#xe26b;'
        })
        .addEvent('click', function(){
            this.win._confirm(t('Really delete'), function(ok){
                if (!ok) return;
                tr.destroy();
                tr2.destroy();
            });
        }.bind(this))
        .inject(actionTd);

//        var a = new Element('a', {
//            text: t('Fields'),
//            style: 'display: block; padding: 2px; cursor: pointer'
//        }).inject(right2Td);
//
//        new Element('img', {
//            src: _path+ PATH_MEDIA + '/admin/images/icons/tree_plus.png',
//            style: 'margin-left: 3px; margin-right: 3px;'
//        }).inject(a, 'top');
//
//        var propertyPanel = new Element('div', {
//            style: 'display: none; margin: 15px; margin-top: 5px; border: 1px solid silver; background-color: #e7e7e7;',
//            'class': 'ka-extmanager-plugins-properties-panel'
//        }).inject(bottomTd);
//
//        a.addEvent('click', function(){
//            if (propertyPanel.getStyle('display') == 'block'){
//                propertyPanel.setStyle('display', 'none');
//                this.getElement('img').set('src', _path+ PATH_MEDIA + '/admin/images/icons/tree_plus.png');
//            } else {
//                propertyPanel.setStyle('display', 'block');
//                this.getElement('img').set('src', _path+ PATH_MEDIA + '/admin/images/icons/tree_minus.png');
//            }
//
//        });

        fieldsBtn.addEvent('click', function(){

            var dialog = this.win.newDialog('', true);
            dialog.setStyles({
                width: '90%',
                height: '95%'
            });
            dialog.center();

            new ka.Button(t('Cancel')).addEvent('click', function(){dialog.close();}).inject(dialog.bottom);

            new Element('div', {
                style: 'padding: 5px; color: gray',
                text: t("You have to enter the keys as camelCased one. In the real table, we convert it to underscore, but you will work always with the camelCased version through the ORM.")
            }).inject(dialog.content);

            var fieldTable = new ka.FieldTable(dialog.content, this.win, {
                addLabel: t('Add field'),
                mode: 'object',
                keyModifier: 'camelcase|trim|lcfirst',
                withTableDefinition: true,
                withoutChildren: true
            });

            new Element('th', {
                text: t('Column name'),
                width: 150
            }).inject(fieldTable.headerTr.getFirst(), 'after');

            fieldTable.addEvent('add', function(item){
                //todo, add span to item and listen on item.getelement(input) bla
                item.underscoreDisplay = new Element('td', {
                    'text': '',
                    style: 'color: gray',
                    width: 150
                }).inject(item.tdType, 'before');


                var updateUnderscore = function(){
                    var ucv = item.iKey.getValue().replace(/([^a-z])/g, function($1){return "_"+$1.toLowerCase().replace(/[^a-z]/, '');});
                    item.underscoreDisplay.set('text', ucv);
                };

                item.iKey.addEvent('change', updateUnderscore);
                item.addEvent('set', updateUnderscore);

                updateUnderscore();
            });

            if (tr.definition.fields)
                fieldTable.setValue(tr.definition.fields);

            new ka.Button(t('Apply')).addEvent('click', function(){

                tr.definition.fields = fieldTable.getValue();
                dialog.close();

            })
            .setButtonStyle('blue')
            .inject(dialog.bottom);


        }.bind(this));
    },

    openObjectWizard: function(pKey, pDefinition){

        this.dialog = this.win.newDialog('', true);

        this.dialog.setStyles({
            height: '80%',
            width: '90%'
        });

        this.dialog.center();


        var tbody = new Element('table', {
            width: '100%'
        }).inject(this.dialog.content);

        var columns = [], fields = {};
        var fieldsActive = [];

        var colCount = 0;
        var useIt = false;

        Object.each(pDefinition.fields, function(field,key){

            useIt = false;
            if (!field.primaryKey && colCount <= 4){
                useIt = true;
                colCount++;
            }

            if (!field.primaryKey)
                fieldsActive.push(key);

            if (!field.autoIncrement)
                fields[key] = (field.label?field.label:'No label')+' ('+key+')';

            columns.push({usage: useIt, key: key, label: (field.label?field.label:'No label'), width: field.width});
        });

        var reqs = {};

        var checkClassName = function(pValue, pFieldObject, pFieldId){


            if (reqs[pFieldId])
                reqs[pFieldId].cancel();

            reqs[pFieldId] = new Request.JSON({url: _path+'admin/system/module/windowsExists', noCache: 1,
            onComplete: function(pResult){
                if(pFieldObject.existsInfo) {
                    pFieldObject.existsInfo.destroy();
                }

                if (pResult){

                    pFieldObject.existsInfo = new Element('div', {
                        style: 'color: red;',
                        text: t('This class already exists. It will be overwritten!')
                    }).inject(pFieldObject.input, 'after');

                }
            }}).get({name: this.mod, className: pValue});

        }.bind(this)

        var kaFields = {

            windowListName: {
                label: tc('objectWindowWizard', 'Window list class name'),
                regexp_replace: '',
                type: 'text',
                'default': pKey+'List',
                onChange: checkClassName
            },
            windowAddName: {
                label: tc('objectWindowWizard', 'Window add class name'),
                type: 'text',
                'default': pKey+'Add',
                onChange: checkClassName
            },
            windowEditName: {
                label: tc('objectWindowWizard', 'Window edit class name'),
                type: 'text',
                'default': pKey+'Edit',
                onChange: checkClassName
            },

            windowListColumns: {
                label: tc('objectwindowWizard', 'Window list columns'),
                type: 'array',
                columns: [
                    {label: t('Usage'), width: 50},
                    {label: t('Key'), width: 100},
                    {label: t('Label')},
                    {label: t('Width'), width: 50}
                ],
                withoutAdd: true,
                withoutRemove: true,
                fields: {
                    usage: {
                        type: 'checkbox'
                    },
                    key: {
                        type: 'label'
                    },
                    label: {
                        type: 'text'
                    },
                    width: {
                        type: 'text'
                    }

                },
                'default': columns
            },

            windowAddFields: {
                label: tc('objectwindowWizard', 'Window add fields'),
                type: 'checkboxgroup',
                items: fields,
                'default': fieldsActive
            },

            windowEditFields: {
                label: tc('objectwindowWizard', 'Window edit fields'),
                type: 'checkboxgroup',
                items: fields,
                'default': fieldsActive
            },

            addEntrypoints: {
                label: tc('objectWindowWizard', 'Create entry points'),
                type: 'checkbox',
                'default': 1
            }

        };

        var kaParseObj = new ka.Parse(tbody, kaFields, {allTableItems: true}, {win: this.win});

        this.objectWizardCloseBtn = new ka.Button(t('Cancel')).addEvent('click', function(){
            this.dialog.close();
        }.bind(this)).inject(this.dialog.bottom);

        this.objectWizardSaveBtn = new ka.Button(t('Apply')).addEvent('click', function(){

            var values = kaParseObj.getValue();
            this.dialog.canClosed = false;
            this.objectWizardCloseBtn.deactivate();
            this.objectWizardSaveBtn.deactivate();

            this.win.setLoading(true, t('Creating windows ...'));

            this.lr = new Request.JSON({url: _path + 'admin/system/module/createWindows', noCache: 1, onComplete: function (res) {

                this.win.setLoading(false);
                this.dialog.close();
                ka.loadMenu();
                ka.loadSettings();

            }.bind(this)}).post({object: pKey, name: this.mod, values: values});


        }.bind(this))
        .setButtonStyle('blue')
        .inject(this.dialog.bottom);


    },

    loadExtras: function(){

        var extrasFields = {

            __resources__: {
                type: 'childrenSwitcher',
                label: tc('extensionEditor', 'Additional JavaScript/CSS files'),
                depends: {

                    adminJavascript: {

                        label: t('Additional JavaScript files'),
                        desc: t('Will be loaded during the login. Relative to media/'),
                        type: 'array',
                        asArray: true,
                        columns: [
                            t('File')
                        ],
                        withOrder: true,
                        fields: {
                            file: {
                                type: 'text'
                            }
                        }
                    },

                    adminCss: {

                        label: t('Additional CSS files'),
                        desc: t('Will be loaded during the login. Relative to media/'),
                        type: 'array',
                        asArray: true,
                        withOrder: true,
                        columns: [
                            t('File')
                        ],
                        fields: {
                            file: {
                                type: 'text'
                            }
                        }
                    }
                }
            },


            __caches__: {
                type: 'childrenSwitcher',
                label: tc('extensionEditor', 'Cache'),
                depends: {

                    caches: {

                        label: t('Cache keys'),
                        desc: t('Define here all cache keys your extension use, so that we can delete all properly. You can optional define a method, if you have stored this cache not through our cache layer and want to do own stuff.'),
                        type: 'array',
                        columns: [
                            {label: t('Key'), width: '50%'},
                            {label: t('Method (optional)')}
                        ],
                        fields: {
                            key: {
                                type: 'text'
                            },
                            method: {
                                type: 'text'
                            }
                        }

                    },

                    cacheInvalidation: {

                        label: t('Cache invalidation keys'),
                        desc: t('Define here all "invalidation"-keys your extension use, so that we can flag all key properly.'),
                        type: 'array',
                        columns: [
                            {label: t('Key')}
                        ],
                        fields: {
                            key: {
                                type: 'text'
                            }
                        }
                    }

                }
            },

            __events__: {
                type: 'childrenSwitcher',
                label: tc('extensionEditor', 'Events'),
                depends: {

                    events: {
                        type: 'array',
                        label: t('Own events'),
                        desc: t('Here you can define events, where others can attach their code. Call krynEvent::fire() to fire it.'),
                        columns: [
                            {label: t('Key'), width: '40%'},
                            {label: t('Description')}
                        ],
                        fields: {
                            key: {
                                type: 'text'
                            },
                            desc: {
                                type: 'text'
                            }
                        }
                    },

                    attachEvents: {

                        label: t('Attach events'),
                        desc: t('You can attach here directly your methods to a event (additional to the way through krynEvent::attach())'),
                        type: 'array',
                        columns: [
                            {label: t('Key'), width: '40%'},
                            {label: t('Method')}
                        ],
                        fields: {
                            key: {
                                type: 'text'
                            },
                            desc: {
                                type: 'text'
                            }
                        }

                    }

                }

            },

            __cdn__: {
                type: 'childrenSwitcher',
                label: tc('extensionEditor', 'CDN driver'),
                depends: {

                    cdn_driver: {
                        type: 'array',
                        label: t('CDN Driver'),
                        desc: t('Here you can define driver for the file abstraction layer. The class has to be in module/&lt;extKey&gt;/&lt;class&gt;.class.php'),
                        asHash: 1,
                        columns: [
                            {label: t('Class'), width: '150'},
                            {label: t('Title'), width: '150'},
                            {label: t('Properties')}
                        ],
                        fields: {
                            'class': {
                                type: 'text'
                            },
                            title: {
                                type: 'text'
                            },
                            properties: {
                                type: 'fieldtable',
                                options: {
                                    withoutChildren: true,
                                    asFrameworkFieldDefinition: true,
                                    fieldTypesBlacklist: ['window_list', 'layoutelement']
                                }
                            }
                        }
                    }
                }
            }
        }

        if (this.lr) this.lr.cancel();
        this.panes['extras'].empty();

        this.extrasPane = new Element('div', {
            'class': 'admin-system-modules-edit-pane',
            style: 'bottom: 31px;'
        }).inject(this.panes['extras']);

        this.extraFieldsObj = new ka.Parse(this.extrasPane, extrasFields, {allTableItems:1, tableitem_title_width: 270});

        var buttonBar = new ka.ButtonBar(this.panes['extras']);
        buttonBar.addButton(t('Save'), this.saveExtras.bind(this));

        this.lr = new Request.JSON({url: _path + 'admin/system/module/editor/config', noCache: 1,
        onComplete: function (pResult) {

            if (pResult.data) {
                this.extraFieldsObj.setValue(pResult.data);
            }
            this.loader.hide();

        }.bind(this)}).get({name: this.mod});


    },

    saveExtras: function(){

        var req =this.extraFieldsObj.getValue();
        req.name = this.mod;

        this.loader.show();

        this.lr = new Request.JSON({url: _path + 'admin/system/module/saveExtras', noCache: 1, onComplete: function () {
            this.loader.hide();
            ka.loadSettings();
        }.bind(this)}).post(req);
    },

    viewType: function (pType) {
        Object.each(this.buttons, function (button, id) {
            button.setPressed(false);
            this.panes[id].setStyle('display', 'none');
        }.bind(this));
        this.buttons[pType].setPressed(true);
        this.panes[pType].setStyle('display', 'block');

        this.loader.show();
        if (this.lr) this.lr.cancel();

        this.lastType = pType;
        switch (pType) {
            case 'language':
                return this.loadLanguage();
            case 'layouts':
                return this.loadLayouts();
            case 'general':
                return this.loadGeneral();
            case 'extras':
                return this.loadExtras();
            case 'links':
                return this.loadLinks();
            case 'db':
                return this.loadDb();
            case 'windows':
                return this.loadWindows();
            case 'docu':
                return this.loadDocu();
            case 'help':
                return this.loadHelp();
            case 'plugins':
                return this.loadPlugins();
            case 'objects':
                return this.loadObjects();
        }
    }


});
