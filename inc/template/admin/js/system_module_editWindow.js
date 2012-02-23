var admin_system_module_editWindow = new Class({

    windowEditFields: {}, //ka.field object
    windowEditTabs: {}, //addtabPane object
    windowEditFieldDefinition: {}, //json definition


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

        this.actionBar = new Element('div', {
            'class': 'ka-system-module-editWindow-actionbar'
        }).inject(this.windowTab.pane);

        new ka.Button(t('Add tab'))
        .addEvent('click', function(){
            this.addWindowEditTab('__tab__', 'Tab');
        }.bind(this))
        .inject(this.actionBar);

        this.windowInspector = new Element('div', {
            'class': 'ka-system-module-editWindow-windowInspector'
        }).inject(this.windowTab.pane);

        new Element('h3',{
            text: t('Inspector'),
            'class': 'ka-system-module-editWindow-windowInspector-header'
        }).inject(this.windowInspector);

        this.windowInspectorContainer = new Element('div',{
            'class': 'ka-system-module-editWindow-windowInspector-content'
        }).inject(this.windowInspector);

        this.windowInspectorActionbar = new Element('div',{
            'class': 'ka-system-module-editWindow-windowInspector-actionbar'
        }).inject(this.windowInspector);

        new ka.Button(t('Apply')).inject(this.windowInspectorActionbar);

        this.loadInfo();
    },

    loadInfo: function(){

        this.lr = new Request.JSON({url: _path+'admin/system/module/getWindowDefinition', noCache:1,
        onComplete: this.renderWindowDefinition.bind(this)}).get({
            name: this.win.params.module,
            'class': this.win.params.className
        });

    },

    renderWindowDefinition: function(pDefinition){

        this.definition = pDefinition;

        this.loadWindowClass(pDefinition.parentClass);

    },

    newWindow: function(){

        this.windowPane.empty();
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

        win.closer.removeEvents('click');
        win.minimizer.removeEvents('click');
        win.linker.destroy();

        return win;
    },

    loadWindowClass: function(pClass){


        if (pClass == 'windowEdit' || pClass == 'windowAdd'){

            var win = this.newWindow();

            //new ka.windowEdit(win, win.content);

            this.winTabPane = new ka.tabPane(win.content, true, win);

            this.windowEditTabs = {};

            //normal fields without tab
            if (typeOf(this.definition.properties.fields) == 'object'){
                this.addWindowEditTab('general', '[[General]]');

                Object.each(this.definition.properties.fields, function(field, key){
                    this.addWindowEditField('general', key, field);
                }.bind(this));
            }

            //tab fields with tab
            if (typeOf(this.definition.properties.tabFields) == 'object'){

                Object.each(this.definition.properties.tabFields, function(fields, tabKey){

                    this.addWindowEditTab(tabKey.replace(/[^a-zA-Z0-9_\-]/, ''), tabKey);

                    Object.each(fields, function(field, key){
                        this.addWindowEditField(tabKey.replace(/[^a-zA-Z0-9_\-]/, ''), key, field);
                    }.bind(this));
                }.bind(this));

            }

            if (!this.definition.properties.fields.length && !this.definition.properties.tabFields.length){
                this.addWindowEditTab('general', '[[General]]');
            }

        }


    },

    loadToolbar: function(pKey){

        if (this.lastLoadedField && this.windowEditFields[this.lastLoadedField]){
            document.id(this.windowEditFields[this.lastLoadedField]).setStyle('border', '0px');
        }

        var definition = this.windowEditFieldDefinition[pKey] || {};
        var field = this.windowEditFields[pKey];

        if (this.lastFieldProperty){

            this.windowInspectorContainer.empty();
            delete this.lastFieldProperty;

        }

        this.lastFieldProperty = new ka.fieldProperty(pKey, definition, this.windowInspectorContainer, {
            arrayKey: true,
            addLabel: t('Add field'),
            tableitem_title_width: 200,
            allTableItems: false,
            withActionsImages: false,
            withoutChildren: true
        });

        if (field){
            document.id(field).setStyle('border', '1px dotted gray');

        }

        this.lastLoadedField = pKey;
    },

    addWindowEditField: function(pTabPane, pKey, pField){

        var field = Object.clone(pField);

        var field = new ka.field(
            field,
            pTabPane.fieldContainer,
            {win: this.win}
        );

        var titleDiv = field.title;

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/pencil.png',
            title: t('Edit'),
            style: 'position: absolute; right: 50px; top: 0px; width: 13px; cursor: pointer;'
        })
        .addEvent('click', function(){
            this.loadToolbar(pKey);
        }.bind(this))
        .inject(titleDiv);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            title: t('Delete'),
            style: 'position: absolute; right: 35px; top: 0px; width: 13px; cursor: pointer;'
        }).inject(titleDiv);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_up.png',
            style: 'position: absolute; right: 20px; top: 0px; width: 13px; cursor: pointer;'
        }).inject(titleDiv);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_down.png',
            style: 'position: absolute; right: 5px; top: 0px; width: 13px; cursor: pointer;'
        }).inject(titleDiv);

        this.windowEditFields[pKey] = field;
        this.windowEditFieldDefinition[pKey] = pField;
        return this.windowEditFields[pKey];

    },

    addWindowEditTab: function(pTabKey, pTitle, pIcon){
        var pane = this.winTabPane.addPane(pTitle, pIcon);

        pane.fieldContainer = new Element('div')
            .inject(pane.pane);

        pane.addButton = new ka.Button(t('Add field')).inject(pane.pane);
        pane.addButton.addEvent('click', function(){

            var items = pane.fieldContainer.getChildren();

            this.addWindowEditField(pane,
                'field_'+(items.length+1), {type: 'text', label: 'Field '+(items.length+1)});

        }.bind(this));

        this.windowEditTabs[pTabKey] = pane;

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/pencil.png',
            title: t('Edit'),
            style: 'margin-left: 4px;',
            'class': 'ka-system-module-editWindow-tab-edit'
        }).inject(pane.button);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            title: t('Delete'),
            'class': 'ka-system-module-editWindow-tab-edit'
        }).inject(this.windowEditTabs[pTabKey].button);
    }

})