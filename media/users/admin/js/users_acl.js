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

    },

    renderObjectRules: function(){

        this.currentAcls.sortOn('prio');

        this.objectRulesContainer.empty();

        var ruleCounter = {
            all: 0,
            custom: 0,
            exact: 0
        };

        var ruleGrouped = [true, {}, {}];

        Array.each(this.currentAcls, function(rule){
            if (rule.object != this.currentObject) return;

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

        this.objectsExactContainer.empty();
        Object.each(ruleGrouped[1], function(rules, code){

            new Element('div', {
                'class': 'ka-list-combine-item',
                text: '#'+code+' ('+rules.length+')'
            }).inject(this.objectsExactContainer);

        }.bind(this));

        this.objectsCustomContainer.empty();
        Object.each(ruleGrouped[2], function(rules, code){

            new Element('div', {
                'class': 'ka-list-combine-item',
                text: this.humanReadableCondition(code)+' ('+rules.length+')'
            }).inject(this.objectsCustomContainer);

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

        //this.addObjectRule(pObjectKey, {});
        //this.loadObjectRule();

    },

    renderObjectRulesAdd: function(pRule){

        var div = new Element('div', {
            'class': 'ka-list-combine-item users-acl-object-rule'
        }).inject(this.objectRulesContainer);

        var status = 'accept';
        if (!pRule.access){
            status = 'exclamation';
        } else if(pRule.access == 2){
            status = 'arrow_turn_up_left';
        }

        new Element('img', {
            'class': 'users-acl-object-rule-status',
            src: _path+'media/admin/images/icons/'+status+'.png'
        }).inject(div);

        var mode = 'application_view_list'; //0, list

        switch(pRule.mode){
            case '1': mode = 'application_form'; break; //view detail
            case '2': mode = 'application_form_add'; break; //add
            case '3': mode = 'application_form_edit'; break; //edit
            case '4': mode = 'application_form_delete'; break; //delete
        }

        new Element('img', {
            'class': 'users-acl-object-rule-mode',
            src: _path+'media/admin/images/icons/'+mode+'.png'
        }).inject(div);


        var title = t('All objects');

        if (pRule.constraint_type == 2)
            title = 'Custom: '+this.humanReadableCondition(pRule.constraint_code);

        if (pRule.constraint_type == 1)
            title = '#'+pRule.constraint_code;

        var title = new Element('span', {
            text: title
        }).inject(div);

        if (pRule.mode >= 1 && pRule.mode <= 3){
            var fieldsText = t('All fields');

            if (pRule.fields){

                var definition = ka.getObjectDefinition(this.currentObject);

                var fieldsObj = JSON.decode(pRule.fields);
                fieldsText = [];
                Object.each(fieldsObj, function(def, key){

                    field = key;
                    if(definition && definition.fields[field] && definition.fields[field].label)
                        field = definition.fields[field].label;
                    fieldsText.push(field);
                }.bind(this));
                fieldsText = fieldsText.join(', ');
            }

            var fields = new Element('div', {
                'class': 'users-acl-object-rule-subline',
                text: fieldsText
            }).inject(div);
        }

        var actions = new Element('div', {
            'class': 'users-acl-object-rule-actions'
        }).inject(div);

        new Element('img', {
            src: _path+'media/admin/images/icons/delete.png',
            title: t('Delete rule')
        }).inject(actions);


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

                    res += field+' '+condition[1]+' '+condition[2];
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

            this.objectDivs[objectKey] = div;

            var h2 = new Element('h2', {
                text: object.label
            }).inject(div);

            new Element('span', {
                text: ' (#'+objectKey+')',
                style: 'color: silver'
            }).inject(h2);

            if (object.desc){
                new Element('div',{
                    'class': 'subline',
                    text: object.desc
                }).inject(div);
            }

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

        new Element('h3', {
            text: t('Constraints')
        }).inject(this.objectConstraints);


        this.objectConstraintsContainer = new Element('div', {
            'class': 'users-acl-object-constraints-container'
        })
        .inject(this.objectConstraints);

        var allDiv = new Element('div', {
            'class': 'ka-list-combine-item'
        }).inject(this.objectConstraintsContainer);

        var h2 = new Element('div', {
            text: t('All objects')
        }).inject(allDiv);

        this.objectsAllCount = new Element('span',{
            style: 'color: gray; padding-left: 5px;',
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

        new Element('h3', {
            text: t('Rules')
        }).inject(this.objectRules);

        this.objectRulesInfo =new Element('div', {
            'class': 'users-acl-object-rules-info'
        }).inject(this.objectRules);

        new Element('div',{
            text: t('Most important rule shall be on the top.')
        }).inject(this.objectRulesInfo);


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

    setAcls: function(pAcls){

        if (!pAcls) pAcls = [];

        this.currentAcls = pAcls;
        this.loadedAcls = Array.clone(this.currentAcls);

        this.tabs.show();
        this.win.setLoading(false);
    }


});
