var admin_system_module_editWindow = new Class({

    windowEditFields: {}, //ka.field object
    windowEditTabs: {}, //addtabPane object

    newCode: {}, //for class methods, code after modifing
    customCode: {}, //for class methods, presaved code

    customMethods: {}, //for custom methods
    customMethodItems: {}, //ref to <a> element of the custom method list

    classMethods: {

        windowList: [
            'saveItem', '__construct', 'prepareFieldDefinition', 'prepareFieldItem', 'deleteItem', 'removeSelected',
            '_removeN2N', 'filterSql', 'where', 'sql', 'countSql', 'getItems', 'exportItems', 'acl'
        ],
        windowAdd: [
            '__construct', 'prepareFieldDefinition', 'prepareFieldItem', 'init', 'loadPreviewPages', 'unlock', 'lock',
            'canLock', 'cachePluginsRelations', 'getItem', 'saveItem', 'getPreviewUrls'
        ],
        windowEdit: [
            '__construct', 'prepareFieldDefinition', 'prepareFieldItem', 'init', 'loadPreviewPages', 'unlock', 'lock',
            'canLock', 'cachePluginsRelations', 'getItem', 'saveItem', 'getPreviewUrls'
        ]

    },

    initialize: function(pWin){

        this.win = pWin;

        this._createLayout();
    },

    _createLayout: function(){

        this.tabPane = new ka.tabPane(this.win.content, true, this.win);

        this.generalTab = this.tabPane.addPane(t('General'));
        this.windowTabEdit  = this.tabPane.addPane(t('Window'));
        this.windowTabList  = this.tabPane.addPane(t('Window'));
        this.methodTab  = this.tabPane.addPane(t('Class methods'));
        this.customMethodTab  = this.tabPane.addPane(t('Custom methods'));

        this.windowTabList.hide();

        this.btnGroup = this.win.addButtonGroup();
        this.saveBtn = this.btnGroup.addButton(t('Save'), _path + 'inc/template/admin/images/button-save.png', this.save.bind(this));

        var generalFields = {
            '__file__': {
                label: t('File'),
                disabled: true
            },
            'class': {
                label: t('Class'),
                type: 'select',
                items: {
                    adminWindowEdit: t('Window edit'),
                    adminWindowAdd: t('Window add'),
                    adminWindowList: t('Window list'),
                    adminWindowCombine: t('Window combine')
                },
                onChange: function(value){

                    this.win._confirm(t('This change requires a reload of the whole editor. Unchanged content will be lost. Continue?'),
                    function(a){

                        if (a){
                            this.generalObj.getField('class').setValue(value);
                            this.lastClass = value;

                            //reload
                            this.useThisClassAfterReload = value;
                            this.loadInfo();
                        }

                    }.bind(this));

                    this.generalObj.getField('class').setValue(this.lastClass);

                    this.lastClass = value;

                }.bind(this)
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
                    },
                    preview: {
                        needValue: 'object',
                        label: t('Preview possibility'),
                        desc: t('Requires defined plugins at the object'),
                        type: 'checkbox'
                    }
                }
            },

            titleField: {
                label: t('Window title field'),
                needValue: 'adminWindowEdit',
                againstField: 'class',
                desc: t('Defines which field the window should use for his title.')
            },

            workspace: {
                label: t('Workspace'),
                type: 'checkbox',
                needValue: 'object',
                againstField: 'dataModel',
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
                        type: 'checkbox',
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
        }).inject(this.windowTabEdit.pane);

        this.actionBar = new Element('div', {
            'class': 'ka-system-module-editWindow-actionbar'
        }).inject(this.windowTabEdit.pane);

        this.windowEditAddTabBtn = new ka.Button(t('Add tab'))
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

                if (iId.value.substr(0,2) != '__')
                    iId.value = '__' + iId.value;

                if (iId.value.substr(iId.value.length-2) != '__')
                    iId.value += '__';

                this.addWindowEditTab(iId.value, iLabel.value);
                dialog.close();
            }.bind(this))
            .inject(dialog.bottom);


            dialog.center();


        }.bind(this))
        .inject(this.actionBar);

        this.windowEditAddFieldBtn = new ka.Button(t('Add custom field'))
        .addEvent('click', function(){

            var currentTab = this.winTabPane.getSelected();
            var currentTab = this.winTabPane.getSelected();

            var items = currentTab.pane.fieldContainer.getChildren();

            this.addWindowEditField(currentTab.pane,
                'field_'+(items.length+1), {type: 'text', label: 'Field '+(items.length+1)});

        }.bind(this))
        .inject(this.actionBar);

        var select;

        this.windowEditAddPredefinedFieldBtn = new ka.Button(t('Add predefined object field'))
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

                    if (!definition){
                        new Element('td', {colspan: 2, text: t('Can not find the object definition of %s.').replace('%s', object)}).inject(tr);
                    } else {
                        new Element('td', {text: t('Object field:')}).inject(tr);
                        var td = new Element('td').inject(tr);
                        select = new ka.Select();

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

                    var currentTab = this.winTabPane.getSelected();

                    var items = currentTab.pane.fieldContainer.getChildren();

                    this.addWindowEditField(currentTab.pane,
                        select.getValue(), {
                            type: 'predefined',
                            object: this.generalObj.getValue('object'),
                            field: select.getValue()
                        }, true);

                    dialog.close();
                }.bind(this))
                .inject(dialog.bottom);
            }

            dialog.center();

        }.bind(this))
        .inject(this.actionBar);

        this.windowInspector = new Element('div', {
            'class': 'ka-system-module-editWindow-windowInspector'
        }).inject(this.windowTabEdit.pane);

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

        this.inspectorBtnApply = new ka.Button(t('Apply'))
        .addEvent('click', this.applyFieldProperties.bind(this))
        .inject(this.windowInspectorActionbar);

        this.inspectorBtnCancel = new ka.Button(t('Cancel'))
        .addEvent('click', this.cancelFieldProperties.bind(this))
        .inject(this.windowInspectorActionbar);

        this.cancelFieldProperties();

        this.loadInfo();
    },

    save: function(){


        var general = this.generalObj.getValue();


        var methods = {}

        this.methodContainer.getElements('a').each(function(item){
            var key = item.get('text');

            if (this.newCode[key]){
                methods[key] = this.newCode[key];
            }
        }.bind(this));

        this.customMethodContainer.getElements('a').each(function(item){
            var key = item.get('text');

            if (this.customMethods[key]){
                methods[key] = this.customMethods[key];
            }
        }.bind(this));

        var res = {
            name: this.win.params.module,
            'class': this.win.params.className,
            general: general,
            methods: methods
        };

        if (general['class'] == 'adminWindowEdit' || general['class'] == 'adminWindowAdd'){

            var tabs = this.winTabPane.buttonGroup.box.getChildren();

            var fields = {};

            Array.each(tabs, function(button, idx){

                var definition = Object.clone(button.retrieve('definition'));
                var key = button.retrieve('key');

                if (!key && definition.label){
                    key = definition.label.toLowerCase().replace(/\W/, '-');
                } else if (!key){
                    return;
                }

                fields[key] = definition;

                var depends = {};
                var iIdx = 0, kaFieldObj;

                var extractFields = function(pDom, pDependsRef){

                    var subfields = pDom.getChildren('.ka-field-main');
                    Array.each(subfields, function(field, idx){

                        var fKey = field.retrieve('key');
                        var fField = Object.clone(field.retrieve('field'));

                        kaFieldObj = field.retrieve('ka.field');
                        if (kaFieldObj.childContainer){
                            fField.depends = {};
                            extractFields(kaFieldObj.childContainer, fField.depends);
                        }

                        if (false && fField.type == 'predefined' && fField.object == this.generalObj.getValue('object')){
                            //if the type is predefeind and the object is the same as in the class
                            //then we dont need to save the whole definition.
                            //todo, what is with children? deactivated
                            pDependsRef[iIdx] = fKey;
                            iIdx++;
                        } else {
                            pDependsRef[fKey] = fField;
                        }
                    });

                };

                extractFields(button.pane, depends);

                fields[key]['depends'] = depends;


            });

            res.fields = fields;
        }



        if (general['class'] == 'adminWindowList' || general['class'] == 'adminWindowCombine'){

            var fields = this.windowListObj.getValue();

            Object.each(fields, function(value,key){

                res.general[key] = value;

            });

        }


        this.saveBtn.startTip(t('Saving ...'));

        if (this.lastSaveReq)
            this.lastSaveReq.cancel();

        this.lastSaveReq = new Request.JSON({url: _path+'admin/system/module/saveWindowClass', noCache: 1,

        noErrorReporting: true,
        onComplete: function(res){

            if (res.error == 'no_writeaccess'){
                this.win._alert(t('No writeaccess to file: %s').replace('%s', res.error_file));
                this.saveBtn.stopTip(t('Failed'));
                return;
            }


            if (res){
                this.saveBtn.stopTip(t('Done'));
            } else {
                this.saveBtn.stopTip(t('Failed'));
            }


        }.bind(this)}).post(res);

    },

    loadInfo: function(){


        this.win.clearTitle();
        this.win.addTitle(this.win.params.module);
        this.win.addTitle(this.win.params.className);

        if (this.lr)
            this.lr.cancel();

        this.win.setLoading(true);

        this.lr = new Request.JSON({url: _path+'admin/system/module/getWindowDefinition', noCache:1,
        onComplete: this.renderWindowDefinition.bind(this)}).get({
            name: this.win.params.module,
            'class': this.win.params.className,
            parentClass: this.useThisClassAfterReload
        });

    },

    renderWindowDefinition: function(pDefinition){

        this.definition = pDefinition;

        if (this.useThisClassAfterReload){
            this.definition['class'] = this.useThisClassAfterReload;
            delete this.useThisClassAfterReload;
        }

        this.lastClass = this.definition['class'];

        //compatibility
        pDefinition.properties.dataModel = pDefinition.properties.object?'object':'table';

        this.generalObj.setValue(pDefinition.properties);
        this.generalObj.getField('class').setValue(this.definition['class']);

        this.loadWindowClass(pDefinition['class']);

        if (!this.definition.methods)
            this.definition.methods = {};

        if (!this.definition.parentMethods)
            this.definition.parentMethods = {};

        //prepare class methods
        Object.each(this.definition.methods, function(code, key){
            this.newCode[key] = "<?php\n\n"+code+"\n?>";

            if (!this.definition.parentMethods[key]){
                this.customMethods[key] = this.newCode[key];
            }

        }.bind(this));

        //class methods Tab
        this.methodTab.pane.empty();

        this.methodContainer = new Element('div', {
            'class': 'ka-system-module-windowEdit-method-container'
        }).inject(this.methodTab.pane);

        this.methodRight = new Element('div', {
            'class': 'ka-system-module-windowEdit-method-right ka-system-module-windowEdit-method-codemirror'
        }).inject(this.methodTab.pane);

        this.methodActionBar = new Element('div', {
            'class': 'ka-system-module-windoeEdit-method-actionbar'
        }).inject(this.methodTab.pane);

        new ka.Button(t('Undo'))
        .addEvent('click', function(){
            if (this.methodEditor)
                this.methodEditor.undo();
        }.bind(this))
        .inject(this.methodActionBar);

        new ka.Button(t('Redo'))
        .addEvent('click', function(){
            if (this.methodEditor)
                this.methodEditor.redo();
        }.bind(this))
        .inject(this.methodActionBar);

        new ka.Button(t('Remove overwrite'))
        .addEvent('click', function(){

            if (this.methodEditor && this.lastMethodItem){
                this.lastMethodItem.removeClass('active');

                var code = this.lastMethodItem.get('text');
                delete this.newCode[code];
                delete this.definition.methods[code];
                this.selectMethod(this.lastMethodItem);

            }
        }.bind(this))
        .inject(this.methodActionBar);

        Object.each(this.definition.parentMethods, function(code, item){

            var a = new Element('a', {
                'class': 'ka-system-module-windowEdit-methods-item',
                text: item
            }).inject(this.methodContainer);

            if (this.definition.methods && this.definition.methods[item]){
                a.addClass('active');
            }

            a.addEvent('click', this.selectMethod.bind(this, a));

        }.bind(this));


        //custom methods Tab
        this.customMethodTab.pane.empty();

        this.customMethodContainer = new Element('div', {
            'class': 'ka-system-module-windowEdit-method-container',
            style: 'bottom: 35px; border-bottom: 1px solid silver; '
        }).inject(this.customMethodTab.pane);

        var addBtnContainer = new Element('div', {
            'class': 'ka-system-module-windowEdit-method-add'
        }).inject(this.customMethodTab.pane);

        new ka.Button(t('Add method'))
        .addEvent('click', function(){

            var dialog = this.win.newDialog('<b>'+t('New method')+'</b>');
            dialog.setStyle('width', 400);

            var d = new Element('div', {
                style: 'padding: 5px 0px;'
            }).inject(dialog.content);

            var table = new Element('table').inject(d);
            var tbody = new Element('tbody').inject(table);

            var fnDefinition = {
                name: {
                    type: 'text',
                    label: t('Name'),
                    required_regexp: /^[a-zA-Z0-9_]*$/,
                    empty: false
                },
                arguments: {
                    type: 'text', label: t('Arguments'), desc: t('Comma sperated')
                },
                visibility: {
                    type: 'select', label: t('Visibility'), type: 'select',
                    items: {public: t('Public'), private: t('Private')}
                },
                static: {
                    type: 'checkbox', label: t('Static')
                }
            }

            var fnDefinitionObj = new ka.parse(tbody, fnDefinition, {allTableItems: true}, {win: this.win});

            new ka.Button(t('Cancel'))
            .addEvent('click', function(){
                dialog.close();
            })
            .inject(dialog.bottom);

            new ka.Button(t('Apply'))
            .addEvent('click', function(){

                if (fnDefinitionObj.isOk()){

                    var name = fnDefinitionObj.getValue('name');
                    if (this.definition.parentMethods[name]){
                        this.win._alert(t('This method name is already in used by the parent class. Please write your code in the class methods tab.'));
                        return;
                    }


                    if (this.customMethods[name]){
                        this.win._alert(t('This method does already exists. Please choose another name.'));
                        return;
                    }

                    //this.customMethods[name] = "<?php\n\n    ";

                    this.customMethods[name] = fnDefinitionObj.getValue('visibility')+" ";

                    this.customMethods[name] += fnDefinitionObj.getValue('static')==1?"static ":"";


                    this.customMethods[name] += "function "+name+"("+fnDefinitionObj.getValue('arguments')+"){";

                    this.renderCustomMethodList();

                    this.selectCustomMethod(this.customMethodItems[name]);
                    dialog.close();
                }


            }.bind(this))
            .inject(dialog.bottom);

            dialog.center();

        }.bind(this))
        .inject(addBtnContainer);

        this.customMethodRight = new Element('div', {
            'class': 'ka-system-module-windowEdit-method-right ka-system-module-windowEdit-method-codemirror'
        }).inject(this.customMethodTab.pane);

        this.customMethodActionBar = new Element('div', {
            'class': 'ka-system-module-windoeEdit-method-actionbar'
        }).inject(this.customMethodTab.pane);

        new ka.Button(t('Undo'))
        .addEvent('click', function(){
            if (this.customMethodEditor)
                this.customMethodEditor.undo();
        }.bind(this))
        .inject(this.customMethodActionBar);

        new ka.Button(t('Redo'))
        .addEvent('click', function(){
            if (this.customMethodEditor)
                this.customMethodEditor.redo();
        }.bind(this))
        .inject(this.customMethodActionBar);

        this.renderCustomMethodList();


        this.win.setLoading(false);

    },

    renderCustomMethodList: function(){

        delete this.customMethodItems;
        this.customMethodItems = {};

        this.customMethodContainer.empty();

        Object.each(this.customMethods, function(code, key){

            var a = new Element('a', {
                'class': 'ka-system-module-windowEdit-customMethods-item',
                text: key
            })
            .inject(this.customMethodContainer);

            new Element('img', {
                src: _path+'inc/template/admin/images/icons/pencil.png',
                'class': 'ka-system-module-windowEdit-methods-item-pencil',
                title: t('Edit')
            })
            .addEvent('click', function(e){
                e.stopPropagation();
                this.openCustomMethodEditor(a);
            }.bind(this))
            .inject(a);

            new Element('img', {
                src: _path+'inc/template/admin/images/icons/delete.png',
                'class': 'ka-system-module-windowEdit-methods-item-remove',
                title: t('Remove')
            })
            .addEvent('click', function(e){

                e.stopPropagation();
                this.win._confirm(t('Really remove?'), function(res){

                    if (!res) return;
                    delete this.customMethods[key];

                    if (this.lastCustomMethodItem == a){
                        this.customMethodEditor.setValue('');
                        delete this.lastCustomMethodItem;
                    }

                    a.destroy();
                }.bind(this));

            }.bind(this))
            .inject(a);

            a.addEvent('click', this.selectCustomMethod.bind(this,a));

            this.customMethodItems[key] = a;

        }.bind(this));

    },

    parseMethodDefintion: function(pCode){

        var res = pCode.match(/(public|private)\s*(static|)\s*function ([a-zA-Z0-9]*)\(([^\)]*)\)\s*{/);

        if (res){

            return {
                visibility: res[1],
                static: res[2]!=""?true:false,
                name: res[3],
                arguments: res[4]
            };

        }

        return {};

    },

    openCustomMethodEditor: function(pA){

        var key = pA.get('text');

        var parsedInfos = this.parseMethodDefintion(this.customMethods[key]);

        var dialog = this.win.newDialog('<b>'+t('Edit method')+'</b>');
        dialog.setStyle('width', 400);

        var d = new Element('div', {
            style: 'padding: 5px 0px;'
        }).inject(dialog.content);

        var table = new Element('table').inject(d);
        var tbody = new Element('tbody').inject(table);

        var fnDefinition = {
            name: {
                type: 'text',
                label: t('Name'),
                required_regexp: /^[a-zA-Z0-9_]*$/,
                empty: false
            },
            arguments: {
                type: 'text', label: t('Arguments'), desc: t('Comma sperated')
            },
            visibility: {
                type: 'select', label: t('Visibility'), type: 'select',
                items: {public: t('Public'), private: t('Private')}
            },
            static: {
                type: 'checkbox', label: t('Static')
            }
        }

        var fnDefinitionObj = new ka.parse(tbody, fnDefinition, {allTableItems: true}, {win: this.win});

        new ka.Button(t('Cancel'))
            .addEvent('click', function(){
            dialog.close();
        })
            .inject(dialog.bottom);

        new ka.Button(t('Apply'))
            .addEvent('click', function(){

            if (fnDefinitionObj.isOk()){

                var name = fnDefinitionObj.getValue('name');
                if (this.definition.parentMethods[name]){
                    this.win._alert(t('This method name is already in used by the parent class. Please write your code in the class methods tab.'));
                    return;
                }

                //this.customMethods[name] = "<?php\n\n    ";

                var pos = this.customMethods[key].indexOf('{');
                var lPos = this.customMethods[key].indexOf('}');
                var codeContent = this.customMethods[key].substring(pos+1, lPos);

                var newCode = fnDefinitionObj.getValue('visibility')+" ";

                newCode += fnDefinitionObj.getValue('static')==1?"static ":"";

                newCode += "function "+name+"("+fnDefinitionObj.getValue('arguments')+"){";

                delete this.customMethods[key];
                this.customMethods[name] = "<?php\n\n    "+newCode + codeContent+"}\n\n?>";

                var selectThis = this.lastCustomMethodItem == this.customMethodItems[key];

                this.renderCustomMethodList();

                if (selectThis)
                    this.selectCustomMethod(this.customMethodItems[name]);

                dialog.close();
            }


        }.bind(this))
            .inject(dialog.bottom);

        dialog.center();

        fnDefinitionObj.setValue(parsedInfos);

    },

    checkCurrentEditor: function(){

        if (this.methodEditor && this.lastMethodItem){

            var code = this.lastMethodItem.get('text');

            var newCode = this.methodEditor.getValue();

            if (this.customCode[code] != newCode){
                this.newCode[code] = newCode;
                this.lastMethodItem.addClass('active');
            }

        }

    },

    checkCurrentCustomEditor: function(){
        if (this.customMethodEditor && this.lastCustomMethodItem){

            var code = this.lastCustomMethodItem.get('text');
            this.customMethods[code] = this.customMethodEditor.getValue();

        }
    },

    selectCustomMethod: function(pA){

        this.customMethodContainer.getChildren().removeClass('selected');

        $$(this.customMethodRight, this.customMethodActionBar).setStyle('display', 'block');

        pA.addClass('selected');
        var name = pA.get('text');

        if (this.customMethods[name].substr(0,5) != '<?php'){
            this.customMethods[name] = "<?php\n\n    "+this.customMethods[name]+"\n\n       //my code\n    }\n\n?>";
        }

        this.lastCustomMethodItem = pA;

        var php = this.customMethods[name];

        if (!this.customMethodEditor){
            this.customMethodEditor = CodeMirror(this.customMethodRight, {
                value: php,
                lineNumbers: true,
                onCursorActivity: this.onEditorCursorActivity,
                onChange: function(pEditor, pChanged){
                    this.onEditorChange(pEditor, pChanged);
                    this.checkCurrentCustomEditor();
                }.bind(this),
                mode: "php"
            });
            
            CodeMirror.autoLoadMode(this.customMethodEditor,  "php");
        } else {
            this.customMethodEditor.setValue(php);
            this.customMethodEditor.clearHistory();
        }

    },

    selectMethod: function(pA){

        this.methodContainer.getChildren().removeClass('selected');
        pA.addClass('selected');

        $$(this.methodRight, this.methodActionBar).setStyle('display', 'block');

        var code = pA.get('text');
        var php;

        if (this.definition.methods[code])
            php = "<?php\n\n"+this.definition.methods[code]+"\n?>";

        if (this.newCode[code])
            php = this.newCode[code];

        if (!php){
            php = "<?php\n\n"+this.definition.parentMethods[code]+"\n" +
                "        //my custom code here\n\n" +
                "        return $result;\n\n" +
                "    }"+"\n\n?>";

            this.customCode[code] = php;

            this.methodRight.addClass('deactivateTabIndex')

            if (!this.lastMethodNotOverwritten){
                this.lastMethodNotOverwritten = new Element('div', {
                    html: '<h2>'+t('Not overwritten.')+'</h2>',
                    'class': 'ka-system-module-windowEdit-methods-notoverwritten',
                    style: 'display: block'
                }).inject(this.methodRight.getParent());
                this.lastMethodNotOverwritten.setStyle('opacity', 0.8);

                new ka.Button('Overwrite now')
                .addEvent('click', function(){
                    this.lastMethodNotOverwritten.destroy();
                    delete this.lastMethodNotOverwritten;
                }.bind(this))
                .inject(this.lastMethodNotOverwritten);
            }

        } else if (this.lastMethodNotOverwritten){
            this.lastMethodNotOverwritten.destroy();
            delete this.lastMethodNotOverwritten;
        }

        this.lastMethodItem = pA;

        if (!this.methodEditor){
            this.methodEditor = CodeMirror(this.methodRight, {
                value: php,
                lineNumbers: true,
                onCursorActivity: this.onEditorCursorActivity,
                onChange: function(pEditor, pChanged){
                    this.onEditorChange(pEditor, pChanged);
                    this.checkCurrentEditor();
                }.bind(this),
                mode: "php"
            });

            CodeMirror.autoLoadMode(this.methodEditor,  "php");
        } else {
            this.methodEditor.setValue(php);
            this.methodEditor.clearHistory();
        }

    },

    onEditorChange: function(pEditor, pChanges){

        //todo, push this feature to the codemirror github repo (as "limit" option)

        //if the user want to remove the linebreak in first editable line
        if (pChanges.from.line == 2 && pChanges.to.line == 3 && pChanges.to.ch == 0){
            pEditor.replaceRange("\n", pChanges.from);
            pEditor.setCursor(pChanges.to);
        }

        //if the user want to remove the linebreak in the line before the last editable line
        if (pChanges.to.line == pEditor.lineCount()-2 && pChanges.to.ch == 0){
            pEditor.replaceRange("\n", pEditor.getCursor(false));
        }

    },

    onEditorCursorActivity: function(pEditor){

        //todo, push this feature to the codemirror github repo (as "limit" option)

        var firstPos = pEditor.getCursor(true);
        var lastPos = pEditor.getCursor(false);

        var newFirstPos = firstPos, newLastPos = lastPos, hasBeenChanged = false;

        if (firstPos.line < 3){
            firstPos.line = 3;
            firstPos.ch = 0;
            hasBeenChanged = true;
        }

        if (pEditor.lineCount() > 6 && lastPos.line > pEditor.lineCount()-4){

            lastPos.line = pEditor.lineCount()-4;
            lastPos.ch = pEditor.getLine(lastPos.line).length;
            hasBeenChanged = true;
        }

        if (hasBeenChanged){
            if (firstPos == lastPos){
                pEditor.setCursor(firstPos);
            } else{
                pEditor.setSelection(firstPos, lastPos);
            }
        }

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

    windowListAddColumn: function(){


        this.previewWin;

    },

    loadWindowClass: function(pClass){

        this.windowEditAddTabBtn.hide();
        this.windowEditAddFieldBtn.hide();
        this.windowEditAddPredefinedFieldBtn.hide();

        this.windowTabEdit.hide();
        this.windowTabList.hide();

        if (pClass == 'adminWindowList' || pClass == 'adminWindowCombine'){
            this.windowTabList.show();

            var listFields = {


                columns: {

                    label: t('Columns'),
                    type: 'fieldTable',
                    options: {
                        asFrameworkColumn: true,
                        withoutChildren: true,
                        addLabel: t('Add column')
                    }

                },

                itemLayout: {
                    label: t('Item layout (Optional)'),
                    desc: t('Default behaviour is that the system extracts the first three columns and build it on it own. You can define here your own item HTML. Use as id attribute the column key.')+
                    '<br/>'+t('Example:')+'<br/>'+
                    '&lt;div id="title"&gt;&lt;/div&gt;<br/>'+
                    '&lt;div style="font-size: 10px;" id="subtitle"&gt;&lt;/div&gt;',
                    type: 'codemirror',
                    input_height: 150,
                    input_width: '98%'
                },

                itemsPerPage: {

                    label: t('Items per page'),
                    type: 'number'

                },

                order: {

                    label: t('Default order'),
                    type: 'array',
                    columns: [
                        {label: t('Field'), width: '60%'},
                        {label: t('Direction')}
                    ],
                    withOrder: true,
                    fields: {
                        field: {
                            type: 'text'
                        },
                        direction: {
                            type: 'select',
                            items: {
                                asc: 'ASC',
                                desc: 'DESC'
                            }
                        }
                    }

                },

                __search__: {
                    type: 'childrenswitcher',
                    label: 'Search',
                    depends: {
                        __search__kind__: {
                            type: 'select',
                            input_width: 150,
                            label: t('Definition'),
                            items: {
                                quick: t('Quickdefinition from defined columns'),
                                custom: t('Complete custom fields')
                            },
                            depends: {
                                filter: {
                                    type: 'text',
                                    needValue: 'quick',
                                    label: t('Search fields'),
                                    desc: t('Comma seperated. Use only  defined columns from above.')
                                },
                                filterCustom: {
                                    label: t('Search fields'),
                                    needValue: 'custom',
                                    type: 'feeeeieldTable',
                                    options: {
                                        asFrameworkSearch: true,
                                        withoutChildren: true,
                                        addLabel: t('Add field'),
                                        asFrameworkFieldDefinition: true
                                    }
                                }
                            }
                        }

                    }
                },

                __actions__: {

                    label: t('Actions'),
                    type: 'childrenswitcher',
                    depends: {

                        add: {

                            label: t('Add functionality'),
                            type: 'checkbox',
                            depends: {
                                addIcon: {
                                    label: t('Icon file'),
                                    type: 'file',
                                    needValue: 1
                                },
                                addEntrypoint: {
                                    label: t('Entrypoint'),
                                    type: 'object',
                                    needValue: 1,
                                    object: 'system_entrypoint',
                                    withoutObjectWrapper: 1,
                                    desc: t('Default is &lt;current&gt;/add')
                                }
                            }

                        },

                        edit: {

                            label: t('Edit functionality'),
                            type: 'checkbox',
                            depends: {
                                editIcon: {
                                    label: t('Icon file'),
                                    type: 'file',
                                    needValue: 1,
                                    objectOptions: {
                                        returnPath: 1,
                                        onlyLocal: 1
                                    }
                                },
                                editEntrypoint: {
                                    label: t('Entrypoint'),
                                    type: 'object',
                                    needValue: 1,
                                    object: 'system_entrypoint',
                                    desc: t('Default is &lt;current&gt;/edit')
                                }
                            }

                        },

                        remove: {

                            label: t('Remove functionality'),
                            type: 'checkbox',
                            depends: {

                                removeIcon: {
                                    label: t('Icon file'),
                                    type: 'file',
                                    needValue: 1,
                                    objectOptions: {
                                        returnPath: 1,
                                        onlyLocal: 1
                                    }
                                }

                            }

                        },

                        itemActions: {
                            label: t('Item actions'),
                            desc: t('This generates on each record a extra icon which opens the defined entry point.'),
                            type: 'array',
                            withOrder: true,
                            columns: [
                                {'label': t('Entry point'), desc: t('The path to the entry point')},
                                {'label': t('Title')},
                                {'label': t('Icon path'), desc: t('Relative to inc/template/')}
                            ],
                            fields: {
                                entrypoint: {
                                    type: 'object',
                                    input_width: 100,
                                    withoutObjectWrapper: 1,
                                    object: 'system_entrypoint'
                                },
                                label: {
                                    type: 'text'
                                },
                                icon: {
                                    type: 'object',
                                    input_width: 100,
                                    object: 'file',
                                    withoutObjectWrapper: 1,
                                    objectOptions: {
                                        onlyLocal: 1,
                                        returnPath: 1
                                    }
                                }
                            }
                        }
                    }

                },

                __export__: {
                    label: t('Export'),
                    type: 'childrenswitcher',
                    depends: {
                        export: {
                            label: t('Export'),
                            type: 'checkbox',
                            desc: t('Provide a export button in the window header.')
                        },


                        export_types: {
                            label: t('Types'),
                            needValue: 1,
                            againstField: 'export',
                            type: 'checkboxgroup',
                            items: {
                                csv: t('CSV'),
                                json: t('JSON'),
                                xml: t('XML')
                            }
                        },

                        export_custom: {
                            needValue: 1,
                            width: 400,
                            againstField: 'export',
                            label: t('Custom export'),
                            type: 'array',
                            columns: [
                                {'label': t('Method')},
                                {'label': t('Label')}
                            ],
                            fields: {
                                method: {
                                    type: 'text'
                                },
                                label: {
                                    type: 'text'
                                }
                            }
                        }

                    }
                }

            };


            var table = new Element('table', {width: '100%', style: 'table-layout: fixed;'}).inject(this.windowTabList.pane);
            this.windowListTbody = new Element('tbody').inject(table);

            if (pClass != 'adminWindowCombine'){
                delete listFields.itemLayout;
            }

            this.windowListObj = new ka.parse(this.windowListTbody, listFields, {allTableItems:1}, {win: this.win});


            //compatibility
            if (this.definition.properties){
                if (this.definition.properties.orderBy){
                    this.definition.properties.order = [];
                    this.definition.properties.order.include({
                        field: this.definition.properties.orderBy,
                        direction: this.definition.properties.orderByDirection ? this.definition.properties.orderByDirection.toLowerCase() : 'asc'
                    });
                }

                if (this.definition.properties.secondOrderBy){
                    if (!this.definition.properties.order) this.definition.properties.order = [];
                    this.definition.properties.order.include({
                        field: this.definition.properties.secondOrderBy,
                        direction: this.definition.properties.secondOrderByDirection ? this.definition.properties.secondOrderByDirection.toLowerCase() : 'asc'
                    });
                }

                if (this.definition.properties.iconEdit)
                    this.definition.properties.editIcon = '/admin/images/icons/'+this.definition.properties.iconEdit;

                if (this.definition.properties.iconAdd)
                    this.definition.properties.addIcon = '/admin/images/icons/'+this.definition.properties.iconAdd;

                if (this.definition.properties.iconDelete)
                    this.definition.properties.removeIcon = '/admin/images/icons/'+this.definition.properties.iconDelete;

                if (this.definition.properties.filter && typeOf(this.definition.properties.filter) == 'array')
                    this.definition.properties.filter = this.definition.properties.filter.join(',');

                if (this.definition.properties.filterCustom && typeOf(this.definition.properties.filterCustom) == 'array' &&
                    Object.getLength(this.definition.properties.filterCustom) > 0)
                    this.definition.properties.__search__kind__ = 'custom';
            }

            this.windowListObj.setValue(this.definition.properties);
        }

        if (pClass == 'adminWindowEdit' || pClass == 'adminWindowAdd'){

            this.windowTabEdit.show();
            this.windowEditAddTabBtn.show();
            this.windowEditAddFieldBtn.show();
            this.windowEditAddPredefinedFieldBtn.show();

            var win = this.newWindow();

            //new ka.windowEdit(win, win.content);

            this.winTabPane = new ka.tabPane(win.content, true, win);

            this.winTabPaneSortables = new Sortables(null, {
                revert: { duration: 500, transition: 'elastic:out' },
                dragOptions: {},
                opacity: 0.7,
                onSort: function(){
                    (function(){
                        this.winTabPane.buttonGroup.rerender();
                    }).delay(50, this);
                }.bind(this)
            });

            this.windowEditTabs = {};

            //normal fields without tab
            if (typeOf(this.definition.properties.fields) == 'object'){

                //do we have on the first leve tabs?

                var doWeHaveTabs = false;
                Object.each(this.definition.properties.fields, function(item, key){
                    if (item.type == 'tab'){
                        doWeHaveTabs = true;
                    }
                });

                if (!doWeHaveTabs){
                    var tab = this.addWindowEditTab('general', {label: '[[General]]', type: 'tab'});

                    Object.each(this.definition.properties.fields, function(field, key){
                        this.addWindowEditField(tab.pane, key, field);
                    }.bind(this));
                } else {
                    Object.each(this.definition.properties.fields, function(tab, tkey){

                        var tabObj = this.addWindowEditTab(tkey, tab);

                        Object.each(tab.depends, function(field, key){
                            this.addWindowEditField(tabObj.pane, key, field);
                        }.bind(this));

                    }.bind(this));
                }
            }

            //tab fields with tab, bacjward compatibility
            if (typeOf(this.definition.properties.tabFields) == 'object'){

                Object.each(this.definition.properties.tabFields, function(fields, tabKey){

                    var tab = this.addWindowEditTab(tabKey.replace(/[^a-zA-Z0-9_\-]/, ''), {label: tabKey});

                    Object.each(fields, function(field, key){
                        this.addWindowEditField(tab.pane, key, field);
                    }.bind(this));
                }.bind(this));

            }

            if (!!this.definition.properties.fields.length && !!this.definition.properties.tabFields.length){
                this.addWindowEditTab('general', {label: '[[General]]', type: 'tab'});
            }
        }


    },

    cancelFieldProperties: function(){

        this.inspectorBtnCancel.hide();
        this.inspectorBtnApply.hide();

        this.windowInspectorContainer.empty();

        if (this.lastFieldProperty)
            delete this.lastFieldProperty;

        if (this.lastLoadedField){
            if (this.windowEditFields[this.lastLoadedField])
                document.id(this.windowEditFields[this.lastLoadedField]).setStyle('outline');
        }

        if (this.lastLoadedField)
            delete this.lastLoadedField;

    },

    applyFieldProperties: function(){

        if (instanceOf(this.windowEditFields[this.lastLoadedField], ka.field)){

            var val = this.lastFieldProperty.getValue();

            var oField = document.id(this.windowEditFields[this.lastLoadedField]);
            var oFieldObj = this.windowEditFields[this.lastLoadedField];
            delete this.windowEditFields[this.lastLoadedField];

            var tab = this.winTabPane.getSelected();
            var field = this.addWindowEditField(tab.pane, val.key, val.definition);

            var nField = document.id(field);
            nField.inject(oField, 'after');

            if (oField.childContainer){
                field.prepareChildContainer();
                oField.childContainer.getChildren().inject(field.childContainer);
            }

            this.lastLoadedField = val.key;
            document.id(this.windowEditFields[this.lastLoadedField]).setStyle('outline', '1px dashed green');

            oField.destroy();

        } else {

            var btn = this.windowEditFields[this.lastLoadedField].button;

            var children = btn.getChildren();
            children.adopt();
            var definition = this.toolbarTabItemObj.getValue();

            definition.type = 'tab';
            var key = definition.key;
            delete definition.key;

            if (key.substr(0,2) != '__')
                key = '__' + key;
            if (key.substr(key.length-2) != '__')
                key += '__';

            //tab
            btn.set('text', definition.label);
            btn.store('key', key);
            btn.store('definition', definition);

            children.inject(btn);


        }

    },

    loadToolbar: function(pKey){

        if (this.lastLoadedField && this.windowEditFields[this.lastLoadedField]){

            if (instanceOf(this.windowEditFields[this.lastLoadedField], ka.field))
                document.id(this.windowEditFields[this.lastLoadedField]).setStyle('outline');
            else
                document.id(this.windowEditFields[this.lastLoadedField].button).setStyle('border');
        }

        this.inspectorBtnCancel.show();
        this.inspectorBtnApply.show();

        var field = this.windowEditFields[pKey];

        this.windowInspectorContainer.empty();

        if (this.lastFieldProperty){
            delete this.lastFieldProperty;
        }

        if (!instanceOf(field, ka.field)){

            field.button.setStyle('border', '1px dashed green');

            this.toolbarTabItemDef = {
                key: {
                    type: 'text', label: t('ID'), desc: t('Will be surrounded with __ and __ (double underscore) if its not already.')
                },
                label: {
                    type: 'text', label: t('Label')
                },
                layout: {
                    type: 'textarea', label: t('Content layout (Optional)'),
                    height: 200,
                    desc: t('If you want to have a own layout in this content tab, then just type here the HTML.')+
                        "\n"+t('Use in your fields as target the same ID as the id attribute in these HTML elements.')
                },
                __optional__: {
                    label: t('More'),
                    type: 'childrenswitcher',
                    depends: {
                        'needValue': {
                            label: tc('kaFieldTable', 'Visibility condition (Optional)'),
                            desc: t("Shows this field only, if the field defined below or the parent field has the defined value. String, JSON notation for arrays and objects, /regex/ or 'javascript:(value=='foo'||value.substr(0,4)=='lala')'")
                        },
                        againstField: {
                            label: tc('kaFieldTable', 'Visibility condition field (Optional)'),
                            desc: t("Define the key of another field if the condition should not against the parent. Use JSON notation for arrays and objects. String or Array")
                        }
                    }
                }
            };

            this.toolbarTabItemObj = new ka.parse(this.windowInspectorContainer, this.toolbarTabItemDef);

            var values = field.button.retrieve('definition');
            values.key = field.button.retrieve('key');

            this.toolbarTabItemObj.setValue(values);

            this.lastLoadedField = pKey;
            return;
        }


        var definition = document.id(field).retrieve('field') || {};

        this.lastFieldProperty = new ka.fieldProperty(pKey, definition, this.windowInspectorContainer, {
            arrayKey: true,
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

    addWindowEditField: function(pParentKey, pKey, pField){
        var field, errorMsg;

        field = Object.clone(pField);
        field.designMode = true;

        var errorDiv, parentContainer;

        if (typeOf(pParentKey) == 'string' && this.windowEditFields[pParentKey]){

            if (this.windowEditFields[pParentKey].pane && this.windowEditFields[pParentKey].pane.hasClass('kwindow-win-tabPane-pane'))
                parentContainer = this.windowEditFields[pParentKey].pane.fieldContainer;
            else {
                this.windowEditFields[pParentKey].prepareChildContainer();
                parentContainer = this.windowEditFields[pParentKey].childContainer;
            }
        }

        if (typeOf(pParentKey) == 'element'){
            parentContainer = pParentKey;
        }

        var field = new ka.field(
            field,
            parentContainer,
            {win: this.win}
        );

        var titleDiv = field.title || null;

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/add.png',
            title: t('Add child'),
            style: 'position: absolute; right: 65px; top: 0px; width: 13px; cursor: pointer;'
        })
        .addEvent('click', function(){

            var key = document.id(field).retrieve('key');
            this.windowEditFields[key].prepareChildContainer();
            var child = this.windowEditFields[key].childContainer;
            var count = child.getElements('.ka-field-main').length+1;
            this.addWindowEditField(key, 'field_'+count, {label: 'Field '+count});

        }.bind(this))
        .inject(titleDiv || errorDiv);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/pencil.png',
            title: t('Edit'),
            style: 'position: absolute; right: 50px; top: 0px; width: 13px; cursor: pointer;'
        })
        .addEvent('click', function(){
            this.loadToolbar(pKey);
        }.bind(this))
        .inject(titleDiv || errorDiv);

        if (errorDiv){
            return errorDiv;
        }

        document.id(field).store('field', pField);
        document.id(field).store('key', pKey);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/delete.png',
            title: t('Delete'),
            style: 'position: absolute; right: 35px; top: 0px; width: 13px; cursor: pointer;'
        })
        .addEvent('click', function(){
            this.win._confirm(t('Really delete?'), function(ok){
                if(!ok) return;
                document.id(field).destroy();
                delete this.windowEditFields[document.id(field).retrieve('key')];
            }.bind(this))
        }.bind(this))
        .inject(titleDiv || errorDiv);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_up.png',
            style: 'position: absolute; right: 20px; top: 0px; width: 13px; cursor: pointer;'
        })
        .addEvent('click', function(){
            if (document.id(field).getPrevious())
                document.id(field).inject(document.id(field).getPrevious(), 'before');
        })
        .inject(titleDiv || errorDiv);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/arrow_down.png',
            style: 'position: absolute; right: 5px; top: 0px; width: 13px; cursor: pointer;'
        })
        .addEvent('click', function(){
            if (document.id(field).getNext())
                document.id(field).inject(document.id(field).getNext(), 'before');
        })
        .inject(titleDiv || errorDiv);

        this.windowEditFields[pKey] = field;

        if (pField.depends){
            Object.each(pField.depends, function(field, key){
                this.addWindowEditField(pKey, key, field);
            }.bind(this));
        }

        return this.windowEditFields[pKey];

    },

    addWindowEditTab: function(pTabKey, pDefinition, pIcon){

        if (pTabKey.substr(0,2) != '__')
            pTabKey = '__' + pTabKey;

        if (pTabKey.substr(pTabKey.length-2) != '__')
            pTabKey += '__';

        var tab = this.winTabPane.addPane(pDefinition.label, pIcon);

        this.windowEditFields[pTabKey] = tab;

        tab.pane.fieldContainer = new Element('div').inject(tab.pane);

        this.windowEditTabs[pTabKey] = tab;

        this.winTabPaneSortables.addItems(tab.button);

        tab.button.store('key', pTabKey);
        tab.button.store('definition', pDefinition);

        new Element('img', {
            src: _path+'inc/template/admin/images/icons/pencil.png',
            title: t('Edit'),
            style: 'margin-left: 4px;',
            'class': 'ka-system-module-editWindow-tab-edit'
        })
        .addEvent('click', this.loadToolbar.bind(this, pTabKey))
        .inject(tab.button);

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