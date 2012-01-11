var admin_system_module_editWindow = new Class({

    initialize: function(pWin){

        this.win = pWin;

        this._createLayout();
    },

    _createLayout: function(){

        this.tabPane = new ka.tabPane(this.win.content, true, this.win);

        this.generalTab = this.tabPane.addPane(t('General'));
        this.windowTab  = this.tabPane.addPane(t('Window'));
        this.methodTab  = this.tabPane.addPane(t('Overwrite methods'));

        this.btnGroup = this.win.addButtonGroup();
        this.saveBtn = this.btnGroup.addButton(t('Save'), _path + 'inc/template/admin/images/button-save.png', function () {
            this.save();
        }.bind(this));
        var generalFields = {
            'class': {
                label: t('Class'),
                type: 'select',
                items: {
                    windowEdit: t('Window edit'),
                    windowAdd: t('Window add'),
                    windowList: t('Window list'),
                    windowCombine: t('Window combine')
                }
            },
            dataModel: {
                label: t('Data model'),
                type: 'select',
                items: {
                    object: t('Object'),
                    table: t('Table')
                },
                depends: {
                    table: {
                        needValue: 'table',
                        label: t('Table name')
                    },
                    primary: {
                        needValue: 'table',
                        label: t('Primary field')
                    },
                    object: {
                        needValue: 'object',
                        label: t('Object key')
                    }
                }
            },

            titleField: {
                label: t('Window title field'),
                desc: t('Defines which field the window should use for his title.')
            },

            workspace: {
                label: t('Workspace'),
                type: 'checkbox',
                help: 'admin/extensions-object-workspace',
                desc: t('This is a kind of a versioning. All changes goes into the workspace versioning table and will only be merged in your orin table behind the object, when the user publishs his workspace to LIVE. The object or table needs a extra field \'live\' for this.')
            },

            multiLanguage: {
                label: t('Multilingual'),
                type: 'checkbox',
                desc: t("The windows gets then a language chooser on the right top bar. The object or table needs a extra field 'lang' for this.")
            },

            multiDomain: {
                label: t('Multi domain'),
                type: 'checkbox',
                desc: t("Useful, when these objects are categorized usually under domains. The windows gets then a domain chooser on the right top bar. The object or table needs a extra field 'domain_rsn' for this.")
            },

            __optional__: {
                label: t('Optional'),
                type: 'childrenswitcher'
            }

        };

        var table = new Element('table', {width: '100%'}).inject(this.generalTab.pane);
        this.generalTbody = new Element('tbody').inject(table);

        this.generalObj = new ka.parse(this.generalTbody, generalFields, {allTableItems:true, tableitem_title_width: 250}, {win:this.win});

        //window
        this.windowPane = new Element('div', {
            'class': 'ka-system-module-editWindow-windowPane'
        }).inject(this.windowTab.pane);


        this.windowInspector = new Element('div', {
            'class': 'ka-system-module-editWindow-windowInspector'
        }).inject(this.windowTab.pane);

        new Element('h3',{
            text: t('Inspector')
        }).inject(this.windowInspector);

        this.loadWindowClass('windowEdit');

        this.loadInfo();
    },

    loadInfo: function(){


    },

    loadWindowClass: function(pClass){


        if (pClass == 'windowEdit' || pClass == 'windowAdd'){

            var win = new ka.kwindow();

            win.borderDragger.detach();
            document.id(win).inject(this.windowPane);

            document.id(win).setStyles({
                left: 25,
                top: 25,
                right: 25,
                bottom: 25,
                width: 'auto',
                height: 'auto'
            });

            new ka.windowEdit(win, win.content);

            this.winTabPane = new ka.tabPane(win.content, true, win);

            var general = this.winTabPane.addPane(t('General'));
            var l = this.winTabPane.addPane('Add tab', _path+'inc/template/admin/images/icons/add.png');

            new ka.field({
                label: 'Title'
            }, general.pane, {win: win});

            new ka.field({
                label: 'Foo',
                type: 'select',
                items: {
                    Blaa: 'Baaar'
                }
            }, general.pane, {win: win});

            new ka.Button(t('Add field')).inject(general.pane);

        }


    }

})