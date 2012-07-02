var users_users_acl = new Class({

    groupDivs: {},
    userDivs: {},

    objectDivs: {},

    currentAcls: [],
    loadedAcls: [],
    currentObject: false,
    currentConstraint: false,

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
            src: _path+ PATH_MEDIA + '/admin/images/icon-search-loupe.png',
            style: 'position: absolute; left: 11px; top: 8px;'
        }).inject(this.win.titleGroups)


        this.tabs = new ka.tabPane(this.right, true, this.win);

        this.entryPointTab = this.tabs.addPane(t('Entry points'), '');
        this.objectTab = this.tabs.addPane(t('Objects'), '');

        this.tabs.hide();

        this.win.setLoading(true);

        this.loadEntryPoints();
        this.loadObjects();

        document.id(this.tabs.buttonGroup).setStyle('position', 'absolute');
        document.id(this.tabs.buttonGroup).setStyle('left', 200);
        document.id(this.tabs.buttonGroup).setStyle('top', 0);

        this.loadList();

        this.win.setLoading(false);
    },

    loadObjectRules: function(pObjectKey){


        //ka.getObjectDefinition(pObjectKey);
        this.currentObject = pObjectKey;

        this.btnAddExact.setStyle('display', 'none');

        this.objectsExactContainer.empty();

        this.objectList.getElements('.ka-list-combine-item').removeClass('active');
        this.objectDivs[pObjectKey].addClass('active');

        var definition = ka.getObjectDefinition(pObjectKey);

        if (definition.nested){

            this.lastObjectTree = new ka.objectTree(this.objectsExactContainer, pObjectKey, {
                openFirstLevel: true,
                rootId: 1,
                move: false,
                withContext: false
            });

        } else {
            this.btnAddExact.setStyle('display', 'inline');
        }

        //todo, if nested, we'd also display rules of parent object which have sub=1

        this.currentConstraint = -1;

        this.renderObjectRules();
        this.showRules();

    },

    renderObjectRules: function(){

        this.currentAcls.sortOn('prio', Array.DESCENDING);

        this.objectRulesContainer.empty();

        var ruleCounter = {
            all: 0,
            custom: 0,
            exact: 0
        };

        var modeCounter = {
            0: 0, 1: 0, 2: 0, 3: 0, 4: 0, 5: 0
        };

        var ruleGrouped = [true, {}, {}];

        Array.each(this.currentAcls, function(rule){
            if (rule.object != this.currentObject) return;

            modeCounter[rule.mode]++;

            if (rule.constraint_type == 2){
                ruleCounter.custom++;
            } else if (rule.constraint_type == 1){
                ruleCounter.exact++;
            } else
                ruleCounter.all++;


            if (rule.constraint_type >= 1){

                if (!ruleGrouped[rule.constraint_type][rule.constraint_code])
                    ruleGrouped[rule.constraint_type][rule.constraint_code] = [];

                ruleGrouped[rule.constraint_type][rule.constraint_code].push(rule);
            }

        }.bind(this));

        this.selectModes.setText(-1,  tc('usersAclModes', 'All rules')+' ('+this.currentAcls.length+')');
        this.selectModes.setText(0,  tc('usersAclModes', 'Combined')+' ('+modeCounter[0]+')');
        this.selectModes.setText(1,  tc('usersAclModes', 'List')+' ('+modeCounter[1]+')');
        this.selectModes.setText(2,  tc('usersAclModes', 'View')+' ('+modeCounter[2]+')');
        this.selectModes.setText(3,  tc('usersAclModes', 'Add')+' ('+modeCounter[3]+')');
        this.selectModes.setText(4,  tc('usersAclModes', 'Edit')+' ('+modeCounter[4]+')');
        this.selectModes.setText(5,  tc('usersAclModes', 'Delete')+' ('+modeCounter[5]+')');

        this.objectsExactContainer.empty();
        Object.each(ruleGrouped[1], function(rules, code){

            var div = new Element('div', {
                'class': 'ka-list-combine-item'
            }).inject(this.objectsExactContainer);

            div.addEvent('click', this.filterRules.bind(this, 1, code, div));

            var title = new Element('span', {
                text: 'object://'+this.currentObject+'/'+code
            }).inject(div);
            this.loadObjectLabel(title);
            var title = new Element('span', {
                text: ' ('+rules.length+')'
            }).inject(div);

        }.bind(this));

        this.objectsCustomContainer.empty();
        Object.each(ruleGrouped[2], function(rules, code){

            var div = new Element('div', {
                'class': 'ka-list-combine-item'
            }).inject(this.objectsCustomContainer);
            div.addEvent('click', this.filterRules.bind(this, 2, code, div));

            var span = new Element('span').inject(div);
            this.humanReadableCondition(code, span);
            var span = new Element('span', {text: ' ('+rules.length+')'}).inject(div);

        }.bind(this));


        this.objectsAllCount.set('text', '('+ruleCounter.all+')');
        this.objectsCustomSplitCount.set('text', '('+ruleCounter.custom+')');
        this.objectsExactSplitCount.set('text', '('+ruleCounter.exact+')');

        Array.each(this.currentAcls, function(rule){
            if (rule.object != this.currentObject) return;

            if (this.currentConstraint == -1){
                this.renderObjectRulesAdd(rule);
            }

        }.bind(this));

        if (this.rulesSort)
            delete this.rulesSort;

        this.rulesSort = new Sortables(this.objectRulesContainer, {
                handle: '.users-acl-object-rule-mover',
                clone: true,
                constrain: true,
                revert: true,
                opacity: 1
            }
        )

        //this.addObjectRule(pObjectKey, {});
        //this.loadObjectRule();

    },

    filterRules: function(pConstraintType, pConstraintCode, pDomObject){

        if (typeOf(pConstraintType) != 'null'){

            this.objectConstraintsContainer.getElements('.active').removeClass('active');

            if (pDomObject)
                pDomObject.addClass('active');

            this.lastConstraintType = pConstraintType;
            this.lastConstraintCode = pConstraintCode;

        } else {
            pConstraintType = this.lastConstraintType;
            pConstraintCode = this.lastConstraintCode;
        }

        this.objectRulesContainer.getChildren().each(function(child){

            var show = false;
            var completelyHide = false;

            if (typeOf(pConstraintType) != 'null'){
                if (pConstraintType === false || child.rule.constraint_type == pConstraintType){

                    if (pConstraintType === false || pConstraintType == 0 || (pConstraintType >= 1 && pConstraintCode == child.rule.constraint_code)){
                        show = true;
                    }
                }
            } else {
                show = true;
            }

            if (this.lastRulesModeFilter !== false){
                if (this.lastRulesModeFilter != child.rule.mode){
                    show = false;
                    completelyHide = true;
                }
            }

            if (show){
                if (child.savedHeight){
                    child.morph({
                        'height': child.savedHeight,
                        paddingTop: 6,
                        paddingBottom: 6
                    });
                } else
                    child.savedHeight = child.getSize().y-12;

                child.addClass('ka-list-combine-item');

            } else {

                if (!child.savedHeight)
                    child.savedHeight = child.getSize().y-12;

                if (completelyHide)
                    child.removeClass('ka-list-combine-item');

                child.morph({
                    'height': completelyHide==true?0:1,
                    paddingTop: 0,
                    paddingBottom: 0
                });
            }

        }.bind(this));

    },

    renderObjectRulesAdd: function(pRule){

        var div = new Element('div', {
            'class': 'ka-list-combine-item users-acl-object-rule'
        }).inject(this.objectRulesContainer);

        div.rule = pRule;

        new Element('img', {
            'class': 'users-acl-object-rule-mover',
            src: _path+'media/users/admin/images/users-acl-item-mover.png'
        }).inject(div);


        var status = 'accept';
        if (pRule.access == 0){
            status = 'exclamation';
        } else if(pRule.access == 2){
            status = 'arrow_turn_bottom_left';
        }

        new Element('img', {
            'class': 'users-acl-object-rule-status',
            src: _path+'media/admin/images/icons/'+status+'.png'
        }).inject(div);

        var mode = 'arrow_in'; //0, combined

        switch(pRule.mode){
            case '1': mode = 'application_view_list'; break; //list
            case '2': mode = 'application_form'; break; //view detail
            case '3': mode = 'application_form_add'; break; //add
            case '4': mode = 'application_form_edit'; break; //edit
            case '5': mode = 'application_form_delete'; break; //delete
        }

        new Element('img', {
            'class': 'users-acl-object-rule-mode',
            src: _path+'media/admin/images/icons/'+mode+'.png'
        }).inject(div);


        var title = t('All objects');

        if (pRule.constraint_type == 1)
            title = 'object://'+this.currentObject+'/'+pRule.constraint_code;
        if (pRule.constraint_type == 2)
            title = '';

        var title = new Element('span', {
            text: title
        }).inject(div);

        if (pRule.constraint_type == 2){
            var span = new Element('span').inject(title);
            this.humanReadableCondition(pRule.constraint_code, span);
        } else if (pRule.constraint_type == 1){
            this.loadObjectLabel(title);
        }

        if (pRule.mode >= 1 && pRule.mode <= 3){

            var fieldSubline = new Element('div', {
                'class': 'users-acl-object-rule-subline'
            }).inject(div);

            var comma;

            if (pRule.fields){

                var definition = ka.getObjectDefinition(this.currentObject);

                var fieldsObj = JSON.decode(pRule.fields);

                var primaries = ka.getPrimaryListForObject(this.currentObject);
                if (primaries){
                    var primaryField = primaries[0];
                    var primaryLabel = definition.fields[primaryField].label || primaryField;
                }

                Object.each(fieldsObj, function(def, key){

                    field = key;
                    if(definition && definition.fields[field] && definition.fields[field].label){

                        field = definition.fields[field].label;

                        new Element('span', {text: field}).inject(fieldSubline);

                        var imgSrc;
                        var subcomma;

                        if (typeOf(def) == 'object' || typeOf(def) == 'array'){

                            new Element('span', {text: '['}).inject(fieldSubline);

                            var span = new Element('span').inject(fieldSubline);

                            if (typeOf(def) == 'array'){
                                Array.each(def, function(rule){

                                    var span = new Element('span').inject(fieldSubline);
                                    this.humanReadableCondition(rule.condition, span);
                                    if (rule.access){
                                        new Element('img', {src: _path+'media/admin/images/icons/accept.png'}).inject(span);
                                    } else {
                                        new Element('img', {src: _path+'media/admin/images/icons/exclamation.png'}).inject(span);
                                    }
                                    subcomma = new Element('span', {text: ', '}).inject(fieldSubline);

                                }.bind(this));
                            } else {

                                var primaryLabel = '';
                                Object.each(def, function(access, id){

                                    var span = new Element('span', {
                                        text: primaryLabel+' = '+id
                                    }).inject(span);

                                    if (access){
                                        new Element('img', {src: _path+'media/admin/images/icons/accept.png'}).inject(span);
                                    } else {
                                        new Element('img', {src: _path+'media/admin/images/icons/exclamation.png'}).inject(span);
                                    }
                                    new Element('img', {src: imgSrc}).inject(span);
                                    subcomma = new Element('span', {text: ', '}).inject(fieldSubline);

                                }.bind(this));
                            }

                            if (subcomma)
                                subcomma.destroy();

                            new Element('span', {text: ']'}).inject(fieldSubline);

                        } else if (!def){
                            imgSrc = _path+'media/admin/images/icons/exclamation.png';
                        } else if(def){
                            imgSrc = _path+'media/admin/images/icons/accept.png';
                        }

                        if (imgSrc)
                            new Element('img', {src: imgSrc}).inject(fieldSubline);
                    }

                    comma = new Element('span', {text: ', '}).inject(fieldSubline);

                }.bind(this));

                comma.destroy();

            } else {
                new Element('span', {text: t('All fields')}).inject(fieldSubline);
            }
        }

        var actions = new Element('div', {
            'class': 'users-acl-object-rule-actions'
        }).inject(div);

        new Element('img', {
            src: _path+'media/admin/images/icons/pencil.png',
            title: t('Edit rule')
        })
        .addEvent('click', this.openEditRuleDialog.bind(this, this.currentObject, div))
        .inject(actions);

        new Element('img', {
            src: _path+'media/admin/images/icons/delete.png',
            title: t('Delete rule')
        }).inject(actions);


    },

    loadObjectLabel: function(pDomObject){

        var uri = pDomObject.get('text');
        new Request.JSON({url: _path+'admin/backend/objectGetLabel', onComplete: function(pResult){

            if (!pResult || pResult.error || !pResult.values){
                pDomObject.set('text', 'Object not found. '+uri);
                return;
            };

            var title = [];
            Object.each(pResult.values, function(value, key){
                title.push(value);
            });

            pDomObject.set('text', title.join(', '));

        }}).get({object: uri});

        //http://ilee/admin/backend/objectGetLabel?url=object://news/3
    },

    humanReadableCondition: function(pCondition, pDomObject){

        if (typeOf(pCondition) == 'string')
            pCondition = JSON.decode(pCondition);

        if (typeOf(pCondition) != 'array') return;

        var field = '';
        var definition = ka.getObjectDefinition(this.currentObject);

        var span = new Element('span');

        Array.each(pCondition, function(condition){

            if (typeOf(condition) == 'string'){
                new Element('span', {text: ' '+((condition.toLowerCase()=='and')?t('and'):t('or'))+' '}).inject(span);
            } else {

                if (typeOf(condition[0]) == 'array'){
                    //group
                    new Element('span', {text: '('}).inject(span);
                    var sub= new Element('span').inject(span);
                    this.humanReadableCondition(condition, sub);
                    new Element('span', {text: ')'}).inject(span);

                } else {

                    field = condition[0];
                    if(definition && definition.fields[field] && definition.fields[field].label)
                        field = definition.fields[field].label;

                    new Element('span', {text: field+' '+condition[1]+' '+condition[2]}).inject(span);
                }
            }

        }.bind(this));

        pDomObject.empty();
        span.inject(pDomObject);

    },

    addObjectRule: function(pObjectKey, pRule){

        var div = new Element('div', {
            'class': 'users-acl-object-rule'
        }).inject(this.objectRulesContainer);

        var title = new Element('div', {
            'class': 'users-acl-object-rule-title'
        }).inject(div);

        var subLine = new Element('div', {
            'class': 'users-acl-object-rule-subline'
        }).inject(div);

        var actions = new Element('div', {
            'class': 'users-acl-object-rule-actions'
        }).inject(div);

        var titleSpan = new Element('span', {
            'class': 'users-acl-object-rule-titlespan'
        }).inject(title);

        new Element('span', {
            text: t('All')
        }).inject(titleSpan);

        new Element('img', {
            src: _path+ PATH_MEDIA + '/admin/images/icons/pencil.png',
            title: t('Choose')
        }).inject(titleSpan);


        /* actions */
        new Element('img', {
            src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_up.png',
            title: t('Up')
        }).inject(actions);

        new Element('img', {
            src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_down.png',
            title: t('Down')
        }).inject(actions);

        new Element('img', {
            src: _path+ PATH_MEDIA + '/admin/images/icons/delete.png',
            title: t('Delete')
        }).inject(actions);

        /* subline */

        return;

        new Element('span', {
            text: t('Default access'),
            style: 'position: relative; top: -6px; padding-right: 5px;'
        }).inject(subLine);
        div.access = new ka.Select(subLine);

        div.access.add(2, 'Inherited');
        div.access.add(1, 'Allow');
        div.access.add(0, 'Deny');

        new Element('span', {
            text: t('With sub-items'),
            style: 'position: relative; top: -6px; padding: 0px 5px;'
        }).inject(subLine);
        div.withSub = new ka.Checkbox(subLine);

        div.tabPane = new ka.tabPane(div);

        div.tabPane.addPane('View');
        div.tabPane.addPane('Add');
        div.tabPane.addPane('Edit');

        div.fieldsList = this.renderObjectFields(list.pane, pObjectKey);

    },

    renderObjectFields: function(pPane, pObjectKey){

        var result  = {};

        result.getValue = function(){};
        result.setValue = function(){};

        var definition = ka.getObjectDefinition(pObjectKey);

        if (!definition.fields || typeOf(definition.fields) != 'object') return result;

        result.table = new Element('table', {
            width: '100%',
            cellspacing: 0,
            cellpadding: 0
        }).inject(pPane);

        result.tbody = new Element('tbody').inject(result.table);

        var td;

        Object.each(definition.fields, function(field, fieldKey){

            var tr = new Element('tr').inject(result.tbody);

            new Element('td', {
                text: field.label,
                width: 150
            }).inject(tr);

            td = new Element('td', {
                width: 100
            }).inject(tr);

            td.access = new ka.Select(td);
            td.access.add(2, 'Inherited');
            td.access.add(1, 'Allow');
            td.access.add(0, 'Deny');

            td = new Element('td', {
                text: field.type
            }).inject(tr);

            if (['select', 'object', 'file', 'page'].contains(field.type)){


            }

        }.bind(this));


        return result;
    },

    addObjectsToList: function(pConfig, pExtKey){

        new Element('div', {
            'class': 'ka-list-combine-splititem',
            text: ka.getExtensionTitle(pExtKey)
        }).inject(this.objectList);

        Object.each(pConfig.objects, function(object, objectKey){

            var div = new Element('div', {
                'class': 'ka-list-combine-item'
            })
            .addEvent('click', this.loadObjectRules.bind(this, objectKey))
            .inject(this.objectList);

            var h2 = new Element('h2', {
                text: object.label || objectKey
            }).inject(div);

            div.count = new Element('span', {
                style: 'font-size: normal; color: silver;'
            }).inject(h2);

            if (object.desc){
                new Element('div',{
                    'class': 'subline',
                    text: object.desc
                }).inject(div);
            }

            this.objectDivs[objectKey] = div;

        }.bind(this));

    },

    loadObjects: function(){

        this.objectList = new Element('div', {
            'class': 'users-acl-object-list'
        })
        .inject(this.objectTab.pane);

        this.objectConstraints = new Element('div', {
            'class': 'users-acl-object-constraints'
        })
        .inject(this.objectTab.pane);

        this.objectRulesFilter = new Element('div', {
            'class': 'kwindow-win-title users-acl-object-constraints-title',
            text: t('Constraints')
        }).inject(this.objectConstraints);

        var div = new Element('div', {
            style: 'padding-top: 12px;'
        }).inject(this.objectRulesFilter);

        new ka.Button(t('Deselect'))
        .addEvent('click', this.filterRules.bind(this, false))
        .inject(div);

        /*
        var h3 = new Element('h3', {
            text: t('Constraints')
        }).inject(this.objectConstraints);

        new Element('a', {
            text: ' ['+t('Remove filter')+']',
            href: 'javascript: ;',
            style: 'font-size: 10px; color: gray;'
        })
        .addEvent('click', this.filterRules.bind(this, [false]))
        .inject(h3);
        */

        this.objectConstraintsContainer = new Element('div', {
            'class': 'users-acl-object-constraints-container'
        })
        .inject(this.objectConstraints);

        var allDiv = new Element('div', {
            'class': 'ka-list-combine-item'
        }).inject(this.objectConstraintsContainer);

        allDiv.addEvent('click', this.filterRules.bind(this, 0, null, allDiv));

        var h2 = new Element('div', {
            text: t('All objects')
        }).inject(allDiv);

        this.objectsAllCount = new Element('span',{
            style: 'padding-left: 5px;',
            text: '(0)'
        }).inject(h2);

        this.objectsCustomSplit = new Element('div', {
            'class': 'ka-list-combine-splititem',
            text: t('Custom')
        }).inject(this.objectConstraintsContainer);

        this.objectsCustomSplitCount = new Element('span',{
            style: 'color: gray; padding-left: 5px;',
            text: '(0)'
        }).inject(this.objectsCustomSplit);

        this.objectsCustomContainer = new Element('div',{
        }).inject(this.objectConstraintsContainer);

        new Element('img' ,{
            src: _path+ PATH_MEDIA + '/admin/images/icons/add.png',
            style: 'cursor: pointer; position: relative; top: -1px; float: right;',
            title: t('Add')
        }).inject(this.objectsCustomSplit);

        this.objectsExactSplit = new Element('div', {
            'class': 'ka-list-combine-splititem',
            text: t('Exact')+' '
        }).inject(this.objectConstraintsContainer);

        this.objectsExactContainer = new Element('div', {
        }).inject(this.objectConstraintsContainer);

        this.objectsExactSplitCount = new Element('span',{
            style: 'color: gray; padding-left: 5px;',
            text: '(0)'
        }).inject(this.objectsExactSplit);

        this.btnAddExact = new Element('img' ,{
            src: _path+ PATH_MEDIA + '/admin/images/icons/add.png',
            style: 'cursor: pointer; position: relative; top: -1px; float: right;',
            title: t('Add')
        }).inject(this.objectsExactSplit);

        this.objectRules = new Element('div', {
            'class': 'users-acl-object-rules'
        })
        .inject(this.objectTab.pane);

        this.objectRulesFilter = new Element('div', {
            'class': 'kwindow-win-title users-acl-object-rules-filter',
            text: t('Rules')
        }).inject(this.objectRules);

        this.objectRulesInfo = new Element('div', {
            'class': 'users-acl-object-rules-info'
        }).inject(this.objectRulesFilter);

        new Element('div',{
            text: t('Most important rule shall be on the top.')
        }).inject(this.objectRulesInfo);

        var div = new Element('div', {
            text: t('Filter modes')+': ',
            style: 'line-height: 24px;'
        }).inject(this.objectRulesInfo);

        this.selectModes = new ka.Select(div);

        document.id(this.selectModes).setStyle('width', 120);

        this.selectModes.addImage(-1, tc('usersAclModes', 'All rules'), 'admin/images/icons/tick.png');
        this.selectModes.addImage(0,  tc('usersAclModes', 'Combined'), 'admin/images/icons/arrow_in.png');
        this.selectModes.addImage(1,  tc('usersAclModes', 'List'), 'admin/images/icons/application_view_list.png');
        this.selectModes.addImage(2,  tc('usersAclModes', 'View'), 'admin/images/icons/application_form.png');
        this.selectModes.addImage(3,  tc('usersAclModes', 'Add'), 'admin/images/icons/application_form_add.png');
        this.selectModes.addImage(4,  tc('usersAclModes', 'Edit'), 'admin/images/icons/application_form_edit.png');
        this.selectModes.addImage(5,  tc('usersAclModes', 'Delete'), 'admin/images/icons/application_form_delete.png');

        this.selectModes.addEvent('change', function(value){

            if (value == -1)
                this.lastRulesModeFilter = false;
            else
                this.lastRulesModeFilter = value;

            this.filterRules();

        }. bind(this));

        this.objectRulesContainer = new Element('div', {
            'class': 'users-acl-object-rules-container'
        })
        .inject(this.objectRules);

        this.addObjectsToList(ka.settings.configs.admin, 'admin');
        this.addObjectsToList(ka.settings.configs.users, 'users');

        Object.each(ka.settings.configs, function(config, extKey){

            if (!config.objects || extKey == 'admin' || extKey == 'users' || typeOf(config.objects) != 'object') return;
            this.addObjectsToList(config, extKey);

        }.bind(this));

    },

    applyEditRuleDialog: function(){

        var value = this.editRuleKaObj.getValue();



    },

    openEditRuleDialog: function(pObject, pRuleDiv){

        pObject = 'news';

        this.editRuleDialog = this.win.newDialog('', true);

        this.editRuleDialog.setStyles({
            width: '90%',
            height: '90%'
        });

        this.editRuleDialog.center();

        //this.editRuleDialog.content
        new ka.Button('Cancel')
        .addEvent('click', function(){
            this.editRuleDialog.close();
        }.bind(this))
        .inject(this.editRuleDialog.bottom);

        new ka.Button('Apply')
        .addEvent('click', this.applyEditRuleDialog.bind(this))
        .inject(this.editRuleDialog.bottom);

        new Element('h2', {
            text: t('Edit rule')
        }).inject(this.editRuleDialog.content);


        var fields = {

            constraint_type: {
                label: t('Constraint type'),
                type: 'select',
                input_width: 140,
                items: {
                    '0': t('All objects'),
                    '1': t('Exact object'),
                    '2': t('Custom condition')
                }
            },

            constraint_code_condition: {
                label: t('Constraint'),
                needValue: '2',
                againstField: 'constraint_type',
                type: 'objectCondition',
                object: pObject,
                startWith: 1
            },

            constraint_code_exact: {
                label: t('Object'),
                needValue: '1',
                againstField: 'constraint_type',
                type: 'object',
                object: pObject
            },

            access: {
                label: t('Access'),
                type: 'select',
                input_width: 140,
                items: {
                    '2': [t('Inherited'), 'admin/images/icons/arrow_turn_bottom_left.png'],
                    '0': [t('Deny'), 'admin/images/icons/exclamation.png'],
                    '1': [t('Allow'), 'admin/images/icons/accept.png']
                }

            },

            mode: {
                label: t('Mode'),
                type: 'select',
                input_width: 140,
                items: {
                    '0': [tc('usersAclModes', 'Combined'), 'admin/images/icons/arrow_in.png'],
                    '1': [tc('usersAclModes', 'List'), 'admin/images/icons/application_view_list.png'],
                    '2': [tc('usersAclModes', 'View'), 'admin/images/icons/application_form.png'],
                    '3': [tc('usersAclModes', 'Add'), 'admin/images/icons/application_form_add.png'],
                    '4': [tc('usersAclModes', 'Edit'), 'admin/images/icons/application_form_edit.png'],
                    '5': [tc('usersAclModes', 'Delete'), 'admin/images/icons/application_form_delete.png']
                }
            },

            __fields__: {
                label: t('Fields'),
                needValue: ['0','2','3','4'],
                againstField: 'mode',
                type: 'label'
            },

            fields: {
                noWrapper: true,
                needValue: ['0','2','3','4'],
                againstField: 'mode',
                type: 'custom',
                'class': 'users_acl_rule_fields',
                object: pObject
            }

        };

        this.editRuleKaObj = new ka.parse(this.editRuleDialog.content, fields, {allTableItems:1, tableitem_title_width: 180}, {win: this.win});

        var rule = Object.clone(pRuleDiv.rule || {});

        if (rule.constraint_type == 2){
            rule.constraint_code_condition = rule.constraint_code;
        }
        if (rule.constraint_type == 1){
            rule.constraint_code_exact = rule.constraint_code;
        }

        logger(rule);
        this.editRuleKaObj.setValue(rule);

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

        this.lastRq = new Request.JSON({url: _path+'admin/users/acl/search', noCache: 1,
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
                    .addEvent('click', this.loadRules.bind(this, 'user', item))
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
                    .addEvent('click', this.loadRules.bind(this, 'group', item))
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
        logger(pType);
        if (!div) return;

        this.left.getElements('.ka-list-combine-item').removeClass('active');
        div.addClass('active');

        var title;
        if (pType == 'user')
            title = t('User %s').replace('%s', pItem['username']);
        else
            title = t('Group %s').replace('%s', pItem['name']);

        this.win.setTitle(title);

        this.loadAcls(pType, pItem.rsn);

    },

    loadAcls: function(pType, pId){

        this.win.setLoading(true, null, {left: 216});

        if (this.lrAcls)
            this.lrAcls.cancel();

        this.lrAcls = new Request.JSON({
            url: _path+'admin/users/acl',
            noCache: true,
            onComplete: this.setAcls.bind(this)
        }).get({type: pType, id: pId});

    },

    hideRules: function(){
        this.objectConstraints.setStyle('display', 'none');
        this.objectRules.setStyle('display', 'none');
    },

    showRules: function(){
        this.objectConstraints.setStyle('display', 'block');
        this.objectRules.setStyle('display', 'block');
    },

    setAcls: function(pAcls){

        if (!pAcls) pAcls = [];

        this.currentAcls = pAcls;
        this.loadedAcls = Array.clone(this.currentAcls);

        var counter = {};

        Array.each(this.currentAcls, function(acl){

            if (counter[acl.object])
                counter[acl.object]++;
            else
                counter[acl.object] = 1;

        }.bind(this));

        Object.each(this.objectDivs, function(dom, key){

            if (!counter[key]) counter[key] = 0;
            dom.count.set('text', ' ('+counter[key]+')');
        });


        this.objectList.getElements('.ka-list-combine-item').removeClass('active');

        this.hideRules();
        this.tabs.show();
        this.win.setLoading(false);
    }


});
