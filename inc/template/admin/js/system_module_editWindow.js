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
        this.methodTab  = this.tabPane.addPane(t('Class methods'));
        this.methodTab  = this.tabPane.addPane(t('Custom methods'));

        this.btnGroup = this.win.addButtonGroup();
        this.saveBtn = this.btnGroup.addButton(t('Save'), _path + 'inc/template/admin/images/button-save.png', function () {
            this.save();
        }.bind(this));

        var generalFields = {
            '__file__': {
                label: t('File'),
                disabled: true
            },
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
                type: 'childrenswitcher',
                depends: {
                    versioning: {
                        label: t('Versioning'),
                        desc: t('This is the old way of versioning. Stores a json copy of the table row in the table system_frameworkversion. Please consider to use workspace option instead.')
                    }
                }
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

            var dialog = this.win.newDialog('<b>'+t('New tab')+'</b>');
            dialog.setStyle('width', 400);

            var d = new Element('div', {
                style: 'padding: 5px 0px;'
            }).inject(dialog.content);

            var table = new Element('table').inject(d);
            var tbody = new Element('tbody').inject(table);

            var tr = new Element('tr').inject(tbody);

            new Element('td', {text: t('Tab id:')}).inject(tr);
            var td = new Element('td').inject(tr);
            var iId = new Element('input', {'class': 'text'}).inject(td);

            var tr = new Element('tr').inject(tbody);
            new Element('td', {text: t('Tab label:')}).inject(tr);
            var td = new Element('td').inject(tr);
            var iLabel =new Element('input', {'class': 'text'}).inject(td);


            new ka.Button(t('Cancel'))
                .addEvent('click', function(){
                dialog.close();
            })
                .inject(dialog.bottom);

            new ka.Button(t('Apply'))
            .addEvent('click', function(){

                if (iId.value.substr(0,2) == '__')
                    iId.value = '__' + iId.value;

                if (iId.value.substr(iId.value.length-2) == '__')
                    iId.value += '__';

                this.addWindowEditTab(iId.value, iLabel.value);
                dialog.close();
            }.bind(this))
            .inject(dialog.bottom);


            dialog.center();


        }.bind(this))
        .inject(this.actionBar);

        new ka.Button(t('Add custom field'))
        .addEvent('click', function(){

            var currentTab = this.winTabPane.getSelected();

            var items = currentTab.pane.fieldContainer.getChildren();

            this.addWindowEditField(currentTab,
                'field_'+(items.length+1), {type: 'text', label: 'Field '+(items.length+1)});

        }.bind(this))
        .inject(this.actionBar);


        new ka.Button(t('Add predefined object field'))
        .addEvent('click', function(){

            var dialog = this.win.newDialog('<b>'+t('Add predefined object field')+'</b>');
            dialog.setStyle('width', 400);

            var d = new Element('div', {
                style: 'padding: 5px 0px;'
            }).inject(dialog.content);

            var table = new Element('table').inject(d);
            var tbody = new Element('tbody').inject(table);

            var tr = new Element('tr').inject(tbody);

            var dataModel = this.generalObj.getValue('dataModel');
            if (dataModel == 'object'){
                var object = this.generalObj.getValue('object');

                if (!object){
                    new Element('td', {colspan: 2, text: t('Please define first the object key under General.')}).inject(tr);
                } else {


                    var definition = ka.getObjectDefinition(object);
                    logger(object);
                    logger(definition);
                    if (!definition){
                        new Element('td', {colspan: 2, text: t('Can not find the object definition of %s.').replace('%s', object)}).inject(tr);
                    } else {
                        new Element('td', {text: t('Object field:')}).inject(tr);
                        var td = new Element('td').inject(tr);
                        var select = new ka.Select();

                        Object.each(definition.fields, function(field, key){

                            select.add(key, field.label?field.label:key);

                        });

                        select.inject(td);
                    }
                }
            } else {
                new Element('td', {colspan: 2, text: t('This windows does not use a object as data model.')}).inject(tr);
            }


            new ka.Button(t('Cancel'))
                .addEvent('click', function(){
                dialog.close();
            })
            .inject(dialog.bottom);

            if (select){
                new ka.Button(t('Apply'))
                .addEvent('click', function(){



                    dialog.close();
                }.bind(this))
                .inject(dialog.bottom);
            }

            dialog.center();

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


        this.win.clearTitle();
        this.win.addTitle(this.win.params.module);
        this.win.addTitle(this.win.params.className);


        this.lr = new Request.JSON({url: _path+'admin/system/module/getWindowDefinition', noCache:1,
        onComplete: this.renderWindowDefinition.bind(this)}).get({
            name: this.win.params.module,
            'class': this.win.params.className
        });

    },

    renderWindowDefinition: function(pDefinition){

        this.definition = pDefinition;


        this.generalObj.setValue(pDefinition.properties);
        this.loadWindowClass(pDefinition['class']);

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
            height: 'auto',
            zIndex: null
        });

        win.addEvent('toFront', function(){
            win.border.setStyle('zIndex', null);
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
                var tab = this.addWindowEditTab('general', '[[General]]');

                Object.each(this.definition.properties.fields, function(field, key){
                    this.addWindowEditField(tab, key, field);
                }.bind(this));
            }

            //tab fields with tab
            if (typeOf(this.definition.properties.tabFields) == 'object'){

                Object.each(this.definition.properties.tabFields, function(fields, tabKey){

                    var tab = this.addWindowEditTab(tabKey.replace(/[^a-zA-Z0-9_\-]/, ''), tabKey);

                    Object.each(fields, function(field, key){
                        this.addWindowEditField(tab, key, field);
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
            document.id(this.windowEditFields[this.lastLoadedField]).setStyle('outline', '0px');
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
            withoutChildren: true,
            asFrameworkFieldDefinition: true
        });

        if (field){
            document.id(field).setStyle('outline', '1px dashed green');

        }

        this.lastLoadedField = pKey;
    },

    addWindowEditField: function(pTab, pKey, pField){

        var field = Object.clone(pField);

        var field = new ka.field(
            field,
            pTab.pane.fieldContainer,
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
        var tab = this.winTabPane.addPane(pTitle, pIcon);

        tab.pane.fieldContainer = new Element('div')
        .inject(tab.pane);

        this.windowEditTabs[pTabKey] = tab;

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/pencil.png',
            title: t('Edit'),
            style: 'margin-left: 4px;',
            'class': 'ka-system-module-editWindow-tab-edit'
        }).inject(tab.button);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            title: t('Delete'),
            'class': 'ka-system-module-editWindow-tab-edit'
        })
        .addEvent('click', function(e){
            e.stop();

            this.winTabPane.remove(tab.id);
        }.bind(this))
        .inject(this.windowEditTabs[pTabKey].button);

        return tab;
    }

})