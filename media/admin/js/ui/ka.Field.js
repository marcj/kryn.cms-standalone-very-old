ka.Field = new Class({

    Implements: [Options, Events],

    options: {
        small: 0,
        label: null,
        type: 'text',
        tableItem: false, //use TR as parent instead of div
        help: null,
        startEmpty: false,
        fieldWidth: null,
        'default': null,
        designMode: false,

        object: null,
        field: null,

        noWrapper: false //doesnt include the ka-field wrapper, and inject the field controls directly to pContainer
    },

    handleChildsMySelf: false, //defines whether this object handles his child visibility itself

    field: {},
    refs: {},
    id: '',
    depends: {},
    childContainer: false,
    container: false,

    children: {},

    initialize: function (pField, pContainer, pRefs, pFieldId) {

        this.id = pFieldId;

        if (pField.type == 'predefined'){
            var definition = ka.getObjectDefinition(pField.object);
            this.field = Object.clone(definition.fields[pField.field]);

            if (pField.label)
                this.field.label = pField.label;

        } else {
            this.field = Object.clone(pField);
        }

        this.setOptions(this.field);
        this.container = pContainer;

        if (typeOf(pRefs) == 'object') {
            Object.each(pRefs, function (item, key) {
                this.refs[key] = item;
            }.bind(this));
        }

        if (!this.field.value) this.field.value = '';


        if (this.options.noWrapper){

            if (this.field.tableItem) {
                this.tr = new Element('tr', {
                });
                this.tr.store('ka.Field', this);

                this.main = new Element('td', {
                    colspan: 2
                }).inject(this.tr);
                this.tr.inject(pContainer || document.hidden);
            } else {

                this.main = new Element('div', {
                });
                this.main.store('ka.Field', this);

                this.main.inject(pContainer || document.hidden);
            }

            this.fieldPanel = this.main;

        } else {

            if (this.field.tableItem) {
                this.tr = new Element('tr', {
                    'class': 'ka-field-main'
                });
                this.tr.store('ka.Field', this);

                this.title = new Element('td', {
                    'class': 'ka-field-tdtitle',
                    width: (this.field.tableitem_title_width) ? this.field.tableitem_title_width : '40%'
                }).inject(this.tr);

                this.main = new Element('td', {
                }).inject(this.tr);

                this.tr.inject(pContainer || document.hidden);

            } else {
                this.main = new Element('div', {
                    'class': 'ka-field-main'
                });
                this.main.store('ka.Field', this);

                if (this.field.panel_width) {
                    this.main.setStyle('width', this.field.panel_width);
                } else if (!this.field.small) {
                    this.main.setStyle('width', 330);
                }

                if (this.field.type == 'headline') {
                    new Element('div', {
                        style: 'clear: both;'
                    }).inject(this.main);
                    new Element('h2', {
                        'class': 'ka-field-headline',
                        html: t(this.field.label)
                    }).inject(this.main);
                    return;
                }

                if (this.field.small) {
                    this.main.set('class', 'ka-field-main ka-field-main-small');
                }
                this.title = new Element('div', {
                    'class': 'ka-field-title'
                }).inject(this.main);

                this.main.inject(pContainer || document.hidden);
            }


            if (this.field.label) {
                this.titleText = new Element('div', {
                    'class': 'title',
                    html: this.field.label
                }).inject(this.title);
            }

            if (this.field.help && this.titleText) {
                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/help_gray.png',
                    width: 14,
                    style: 'float: right; cursor: pointer; position: relative; top: -1px;',
                    title: _('View help to this field'),
                    styles: {
                        opacity: 0.7
                    }
                }).addEvent('mouseover',
                    function () {
                        this.setStyle('opacity', 1);
                    }).addEvent('mouseout',
                    function () {
                        this.setStyle('opacity', 0.7);
                    }).addEvent('click',
                    function () {
                        ka.wm.open('admin/help', {id: this.field.help});
                    }.bind(this)).inject(this.titleText);
            }

            if (this.field.desc) {
                new Element('div', {
                    'class': 'desc',
                    html: this.field.desc
                }).inject(this.title);
            }

            this.fieldPanel = new Element('div', {
                'class': 'ka-field-field'
            }).inject(this.main);
        }

        if (this.options.fieldWidth)
            this.fieldPanel.setStyle('width', this.options.fieldWidth)


        if (this.field.invisible == 1) {
            this.main.setStyle('display', 'none');
        }

        if (this.refs.win) {
            this.win = this.refs.win;
        } else {
            this.findWin();
        }

        this.addEvent('change', function () {
            this.fireEvent('check-depends');
            this.updateOkInfo();
        }.bind(this));

        if (this.field.designMode){
            try {

                this.renderField();

            } catch(e){

                if (this.tr)
                    this.tr.destroy();

                if (this.main)
                    this.main.destroy();

                throw e;

                return false;
            }
        } else {
            this.renderField();
        }

        if (this.field['default'] && this.field['default'] != "" && this.field.type != 'datetime') {
            this.setValue(this.field['default'], true);
        }

        if (!this.field.startEmpty && this.field.value) {
            this.setValue(this.field.value, true);
        }

        if (this.input) {
            if (this.input.get('tag') == 'input' && this.input.get('class') == 'text') {
                this.input.store('oldBg', this.input.getStyle('background-color'));
                this.input.addEvent('focus', function () {
                    this.setStyle('border', '1px solid black');
                    this.setStyle('background-color', '#fff770');
                });
                this.input.addEvent('blur', function () {
                    this.setStyle('border', '1px solid silver');
                    this.setStyle('background-color', this.retrieve('oldBg'));
                });
            }

            if (this.field.disabled) {
                this.input.set('disabled', true);
            }
        }
    },

    toElement: function () {
        if (this.field.tableItem) {
            return this.tr;
        } else {
            return this.main;
        }
    },

    renderField: function () {

        if (this.field.type)
            this.field.type = this.field.type.toLowerCase();


        switch (this.field.type) {
            case 'password':
                this.renderPassword();
                break;
            case 'select':
                this.renderSelect();
                break;
            case 'textlist':
                this.renderTextlist();
                break;
            case 'textarea':
                this.renderTextarea();
                break;
            case 'array':
                this.renderArray();
                break;
            case 'wysiwyg':
                this.renderWysiwyg();
                break;
            case 'date':
                this.renderDate();
                break;
            case 'datetime':
                this.renderDate({time: true});
                break;
            case 'checkbox':
                this.renderCheckbox();
                break;
            case 'file':
            case 'filechooser':

                this.field.withoutObjectWrapper = 1;
                this.field.objectOptions = {
                    returnPath: 1,
                    onlyLocal: 1
                };

                this.renderChooser(['file']);
                break;
            case 'pagechooser':
            case 'page':
            case 'node':
                this.renderChooser(['node']);
                break;
            case 'object':
                this.renderChooser(typeOf(this.field.object)=='array'?this.field.object:[this.field.object]);
                break;
            case 'chooser':
                this.renderChooser();
                break;
            case 'filelist':
                this.renderFileList();
                break;
            case 'multiupload':
                this.initMultiUpload();
                break;
            case 'layoutelement':
                this.initLayoutElement();
                break;
            case 'headline':
                this.renderHeadline();
                break;
            case 'info':
                this.renderInfo();
                break;
            case 'label':
                this.renderLabel(true);
                break;
            case 'html':
                this.renderLabel();
                break;
            case 'imagegroup':
                this.renderImageGroup();
                break;
            case 'custom':
                this.renderCustom();
                break;
            case 'integer':
            case 'number':
                this.renderNumber();
                break;
            case 'childrenswitcher':
                this.renderChildrenSwitcher();
                break;
            case 'checkboxgroup':
                this.renderCheckboxGroup();
                break;
            case 'windowlist':
                this.renderWindowList();
                break;
            case 'fieldtable':
                this.renderFieldTable();
                break;
            case 'codemirror':
                this.renderCodemirror();
                break;
            case 'condition':
                this.renderCondition();
                break;
            case 'objectcondition':

                this.renderCondition({
                    object: this.field.object
                });

                break;
            case 'fieldcondition':

                this.renderCondition({
                    object: this.options.object,
                    field: this.options.field
                });

                break;
            case 'lang':

                this.field.items = {}
                Object.each(ka.settings.langs, function (lang, id) {
                    this.field.items[id] = lang.langtitle + ' (' + lang.title + ', ' + id + ')';
                }.bind(this));

                if (this.options.multi)
                    this.renderTextlist();
                else
                    this.renderSelect();

                break;

            case 'text':
            default:
                this.renderText();
                break;
        }
        if (this.input) {

            /*
            if (this.field.length + 0 > 0) {
                this.input.setStyle('width', (this.field.length.toInt() * 9));
            }
            */

            this.input.store('oldClass', this.input.get('class'));
        }
    },

    renderCondition: function(pOptions){

        var addCondition, addGroup, renderValues, objectDefinition;

        var dateConditions = ['= NOW()', '!=  NOW()', '<  NOW()', '>  NOW()', '<=  NOW()', '>=  NOW()'];

        var reRender = function(pTarget){

            pTarget.getChildren().removeClass('ka-field-condition-withoutRel');

            var first = pTarget.getFirst();
            if (first) first.addClass('ka-field-condition-withoutRel');

        }

        addCondition = function(pTarget, pValues, pCondition){

            var div = new Element('div', {
                'class': 'ka-field-condition-item'
            }).inject(pTarget);

            var table = new Element('table', {
                style: 'width: 100%; table-layout: fixed; background-color: transparent;'
            }).inject(div);

            var tbody = new Element('tbody').inject(table);
            var tr = new Element('tr').inject(tbody);

            new Element('td', {
                'class': 'ka-field-condition-leftBracket',
                text: '('
            }).inject(tr);

            var td = new Element('td', {style: 'width: 40px', 'class': 'ka-field-condition-relContainer'}).inject(tr);

            var relSelect = new ka.Select(td);
            document.id(relSelect).setStyle('width', '100%');
            relSelect.add('AND', 'AND');
            relSelect.add('OR', 'OR');

            div.relSelect = relSelect;

            if (pCondition)
                relSelect.setValue(pCondition.toUpperCase());

            var td = new Element('td', {style: 'width: 25%'}).inject(tr);

            if (pOptions){
                div.iLeft = new ka.Select(td, {
                    customValue: true
                });

                document.id(div.iLeft).setStyle('width', '100%');

                objectDefinition = ka.getObjectDefinition(pOptions.object);

                if (pOptions.field){

                    div.iLeft.add(pOptions.field, objectDefinition.fields[pOptions.field].label||pOptions.field);
                    div.iLeft.setEnabled(false);

                } else {
                    Object.each(objectDefinition.fields, function(def, key){
                        div.iLeft.add(key, def.label||key);
                    }.bind(this));
                }

            } else {
                div.iLeft = new Element('input', {
                    'class': 'text',
                    style: 'width: 100%',
                    value: pValues?pValues[0]:''
                }).inject(td);

                div.iLeft.getValue = function(){return this.value;};
            }


            if (pValues)
                div.iLeft.setValue(pValues[0]);

            var td = new Element('td', {style: 'width: 41px; text-align: center'}).inject(tr);
            var select = new ka.Select(td);
            div.iMiddle = select;

            document.id(select).setStyle('width', '100%');

            ['=', '!=', '<', '>', '<=', '>=', 'LIKE', 'IN', 'NOT IN', 'REGEXP',
                '= CURRENT_USER', '!= CURRENT_USER'].each(function(item){
                select.add(item, item);
            });

            if (pValues)
                select.setValue(pValues[1]);

            var rightTd = new Element('td', {style: 'width: 25%'}).inject(tr);
            div.iRight = new Element('input', {
                'class': 'text',
                style: 'width: 100%',
                value: pValues?pValues[2]:''
            }).inject(rightTd);
            div.iRight.getValue = function(){return this.value;};

            if (pOptions){
                var updateRightTdField = function(){

                    var chosenField = div.iLeft.getValue();

                    var fieldDefinition = Object.clone(objectDefinition.fields[chosenField]);

                    if (div.iRight)
                        var backupedValue = div.iRight.getValue();

                    delete div.iRight;

                    rightTd.empty();

                    if (fieldDefinition.primaryKey){
                        if (['=', '!=', 'IN', 'NOT IN'].contains(div.iMiddle.getValue())){
                                fieldDefinition = {
                                    type: 'object',
                                    object: pOptions.object,
                                    withoutObjectWrapper: true
                                };

                            if (div.iMiddle.getValue() == 'IN'){
                                fieldDefinition.multi = 1;
                            }
                        } else {
                            fieldDefinition.type = 'text';
                        }
                    }

                    if (div.iMiddle.getValue() == 'IN' || div.iMiddle.getValue() == 'NOT IN'){
                        if (fieldDefinition.type == 'select')
                            fieldDefinition.type = 'textlist';
                        else
                            fieldDefinition.multi = 1;
                    }

                    if (['LIKE', 'REGEXP'].contains(div.iMiddle.getValue())){
                        fieldDefinition = {type: 'text'};
                    }

                    if (fieldDefinition.type == 'object' && fieldDefinition.object == 'user'){
                        ['= CURRENT_USER', '!= CURRENT_USER'].each(function(item){
                            div.iMiddle.showOption(item);
                        });
                    } else {
                        ['= CURRENT_USER', '!= CURRENT_USER'].each(function(item){
                            div.iMiddle.hideOption(item);
                        });
                    }

                    if (fieldDefinition.type == 'date'|| fieldDefinition.type == 'datetime'){
                        dateConditions.each(function(item){div.iMiddle.add(item)});
                    } else {
                        dateConditions.each(function(item){div.iMiddle.remove(item)});
                    }

                    fieldDefinition.noWrapper = true;
                    fieldDefinition.fieldWidth = '100%';

                    if (!dateConditions.contains(div.iMiddle.getValue())){

                        div.iRight = new ka.Field(
                            fieldDefinition, rightTd
                        );

                        div.iRight.code = div.iMiddle.getValue()+'_'+chosenField;;

                        if (backupedValue)
                            div.iRight.setValue(backupedValue);
                    }

                };

                div.iLeft.addEvent('change', updateRightTdField);
                div.iMiddle.addEvent('change', updateRightTdField);

                updateRightTdField();
            }

            var actions = new Element('td', {style: 'width: '+parseInt((16*4)+3)+'px'}).inject(tr);

            new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_up.png'})
            .addEvent('click', function(){
                if (div.getPrevious()){
                    div.inject(div.getPrevious(), 'before');
                    reRender(pTarget);
                }
            })
            .inject(actions);
            new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_down.png'})
            .addEvent('click', function(){
                if (div.getNext()){
                    div.inject(div.getNext(), 'after');
                    reRender(pTarget);
                }
            }).inject(actions);

            new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/delete.png'})
            .addEvent('click', function(){
                this.win._confirm(t('Really delete?'), function(a){
                    if (!a) return;
                    div.destroy();
                    reRender(pTarget);
                })
            }.bind(this))
            .inject(actions);

            new Element('td', {
                'class': 'ka-field-condition-leftBracket',
                text: ')'
            }).inject(tr);

            reRender(pTarget);

        }.bind(this);

        addGroup = function(pTarget, pValues, pCondition){

            var div = new Element('div', {
                'class': 'ka-field-condition-group'
            }).inject(pTarget);

            var relContainer = new Element('span', {
                'class': 'ka-field-condition-relContainer',
                style: 'position: absolute; left: -52px;'
            }).inject(div);

            var relSelect = new ka.Select(relContainer);
            document.id(relSelect).setStyle('width', '47px');
            relSelect.add('AND', 'AND');
            relSelect.add('OR', 'OR');
            div.relSelect = relSelect;

            if (pCondition)
                relSelect.setValue(pCondition.toUpperCase());

            var con = new Element('div', {
                'class': 'ka-field-condition-container'
            }).inject(div);
            div.container = con;

            new ka.Button(t('Add condition'))
            .addEvent('click', addCondition.bind(this, con))
            .inject(con, 'before');

            new ka.Button(t('Add group'))
            .addEvent('click', addGroup.bind(this, con))
            .inject(con, 'before');

            var actions = new Element('span', {style: 'position: relative; top: 3px; width: '+parseInt((16*4)+3)+'px'}).inject(con, 'before');

            new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_up.png'})
                .addEvent('click', function(){
                if (div.getPrevious()){
                    div.inject(div.getPrevious(), 'before');
                    reRender(pTarget);
                }
            })
                .inject(actions);
            new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/arrow_down.png'})
                .addEvent('click', function(){
                if (div.getNext()){
                    div.inject(div.getNext(), 'after');
                    reRender(pTarget);
                }
            }).inject(actions);

            new Element('img', {src: _path+ PATH_MEDIA + '/admin/images/icons/delete.png'})
                .addEvent('click', function(){
                this.win._confirm(t('Really delete?'), function(a){
                    if (!a) return;
                    div.destroy();
                    reRender(pTarget);
                })
            }.bind(this))
            .inject(actions);

            reRender(pTarget);

            renderValues(pValues, con);
        }.bind(this);

        renderValues = function (pValue, pTarget, pLastRel){
            if (typeOf(pValue) == 'array'){

                var lastRel = pLastRel || '';

                Array.each(pValue, function(item){

                    if (typeOf(item) == 'array' && typeOf(item[0]) == 'array'){
                        //item is a group
                        addGroup(pTarget, item, lastRel)

                    } else if(typeOf(item) == 'array'){
                        //item is a condition
                        addCondition(pTarget, item, lastRel)

                    } else if(typeOf(item) == 'string'){
                        lastRel = item;
                    }
                });
            }
        };

        var con = new Element('div', {
            'class': 'ka-field-condition-container'
        }).inject(this.fieldPanel);

        new ka.Button(t('Add condition'))
        .addEvent('click', addCondition.bind(this, con))
        .inject(this.fieldPanel);

        new ka.Button(t('Add group'))
        .addEvent('click', addGroup.bind(this, con))
        .inject(this.fieldPanel);

        if (this.field.startWith){
            for(var i=0; i<this.field.startWith;i++)
                addCondition(con);
        }

        this._setValue = function(pValue){

            con.empty();

            if (typeOf(pValue) == 'string'){
                try {
                    pValue = JSON.decode(pValue);
                } catch(e){

                }
            }

            if(typeOf(pValue) == 'array' && typeOf(pValue[0]) == 'string')
                pValue = [pValue];

            if (typeOf(pValue) == 'array'){
                renderValues(pValue, con);
            }

        }.bind(this);

        var extractValues = function(pTarget){

            var result = [];

            pTarget.getChildren().each(function(item){

                if (item.hasClass('ka-field-condition-item')){

                    if (!item.hasClass('ka-field-condition-withoutRel'))
                        result.push(item.relSelect.getValue());

                    result.push([
                        item.iLeft.getValue(),
                        item.iMiddle.getValue(),
                        item.iRight.getValue()
                    ])
                }

                if (item.hasClass('ka-field-condition-group')){
                    if (!item.hasClass('ka-field-condition-withoutRel'))
                        result.push(item.relSelect.getValue());
                    result.push(extractValues(item.container));
                }

            });

            return result;

        }

        this.getValue = function(){
            return extractValues(con);
        }

    },


    renderCheckboxGroup: function(){

        var addCheckbox = function(pKey, pLabel){

            var div = new Element('div').inject(this.fieldPanel);

            var id = (new Date()).getTime()+'_kaField_checkboxgroup_'+pKey;
            var cb = new Element('input', {
                type: 'checkbox',
                id: id
            }).inject(div).store('key', pKey);

            new Element('label', {
                'for': id,
                text: pLabel,
                style: 'position: relative; top: -2px;'
            }).inject(div);

        }.bind(this);

        if (this.field.items){

            if (typeOf(this.field.items) == 'object'){
                Object.each(this.field.items, function(label, key){
                    addCheckbox(key, label);
                }.bind(this));
            }

        }

        var items = this.fieldPanel.getElements('input');

        this._setValue = function(pValue){
            if (typeOf(pValue) == 'array'){

                items.set('checked', false);

                Array.each(items, function(item){

                    if (pValue.indexOf(item.retrieve('key')) == -1){
                        item.set('checked', false);
                    } else {
                        item.set('checked', true);
                    }

                });

            }

        }.bind(this);

        this.getValue = function(){

            var res = [];
            Array.each(items, function(item){

                if (item.get('checked')) res.include(item.retrieve('key'));

            });

            return res;
        };

    },

    renderFieldTable: function(){

        this.fieldTable = new ka.FieldTable(this.fieldPanel, this.win, this.field.options);

        this.getValue = function(){
            return this.fieldTable.getValue();
        }.bind(this);

        this._setValue = function(p){
            this.fieldTable.setValue(p);
        }.bind(this);

    },


    renderChildrenSwitcher: function(){

        this.title.empty();

        var a = new Element('a', {
            text: this.field.label,
            style: 'display: block; padding: 2px; cursor: pointer; position: relative; left: -5px;'
        }).inject(this.title);

        new Element('img', {
            src: _path+ PATH_MEDIA + '/admin/images/icons/tree_plus.png',
            style: 'margin-left: 2px; margin-right: 3px;'
        }).inject(a, 'top');

        this.value = 0;

        this.getValue = function(){
            return this.value;
        }.bind(this);

        this.handleChildsMySelf = true;

        this._setValue = function(pValue){
            this.value = pValue || 0;
            if (!this.childContainer) return;

            if (this.value == 0){
                this.childContainer.setStyle('display', 'none');
                a.getElement('img').set('src', _path+ PATH_MEDIA + '/admin/images/icons/tree_plus.png');
            } else {
                this.childContainer.setStyle('display', 'block');
                a.getElement('img').set('src', _path+ PATH_MEDIA + '/admin/images/icons/tree_minus.png');
            }
        }.bind(this);

        a.addEvent('click', function(){
            this._setValue( this.value==0?1:0)
        }.bind(this));

        //with check-depends we have this.childContainer
        this.addEvent('check-depends', function(){
            this._setValue(this.value);
        }.bind(this));

    },

    renderLabel: function (pStripHtml) {

        var div = new Element('div').inject(this.fieldPanel);

        this._setValue = function (pVal) {
            if (!pVal){
                div.set('text', '');
                return;
            }
            this.value = pVal;
            if (pStripHtml) {
                div.set('text', pVal);
            } else {
                div.set('html', pVal);
            }
        }.bind(this);

        this.getValue = function () {
            return this.value;
        }.bind(this);
    },

    renderTextlist: function () {

        var _this = this;
        var _searchValue;

        var box, timer, boxHead, boxBody, lastRq, curSelection;

        var div = new Element('div', {
            'class': 'ka-field-textlist'
        }).inject(this.fieldPanel);

        if (this.field.width) {
            div.setStyle('width', this.field.width);
        }

        var input = new Element('input', {
            autocomplete: false,
            tabindex: 0,
            style: 'width: 7px;'
        }).inject(div);

        var clear = new Element('div', {
            'class': 'ka-field-textlist-clear'
        }).addEvent('click',
            function (e) {
                if (_this.field.store) {
                    input.setStyle('left', '');
                    input.setStyle('position', '');
                    input.focus();
                    active = input;
                    input.value = '';
                    div.getElements('.ka-field-textlist-item-active').removeClass('ka-field-textlist-item-active');
                    searchValue();
                    e.stop();
                }
            }).inject(div);

        new Element('img', {
            src: _path + PATH_MEDIA + '/admin/images/icons/tree_minus.png'
        }).inject(clear);

        var active = input;

        var addTextlistItem = function (pLabel, pValue) {

            if (!pValue) {
                pValue = pLabel;
            }

            if (!_this.field.doubles || _this.field.doubles != true || _this.field.doubles != 1) {
                //check for doubles

                var found = false;
                div.getElements('.ka-field-textlist-item').each(function (item) {
                    if (found == true) return;
                    if (item.retrieve('value') == pValue) {
                        found = true;
                    }

                });
                if (found) return;
            }

            var item = new Element('div', {
                'class': 'ka-field-textlist-item'
            }).inject(input, 'before');

            var title = new Element('span', {
                text: pLabel ? pLabel : '...'
            }).inject(item);

            if (!pLabel) {

                new Request.JSON({url: _path + 'admin/' + _this.field.store, onComplete: function (res) {
                    if (res) {
                        title.set('text', res.label);
                    } else {
                        title.set('text', _('##Failed##'));
                    }

                }}).get({cmd: 'item', id: pValue});
            }

            item.addEvent('mousedown', function (e) {

                e.stop();
                input.setStyle('left', -5000);
                input.setStyle('position', 'absolute');
                active.removeClass('ka-field-textlist-item-active');
                active = this;
                active.addClass('ka-field-textlist-item-active');
                input.focus();
                input.value = '';

            });

            item.store('value', pValue);

            new Element('a', {
                text: 'x'
            }).addEvent('mousedown',
                function (e) {
                    e.stop();
                    if (active == this.getParent()) {
                        var next = this.getParent().getNext();
                        if (!next.hasClass('ka-field-textlist-item') && next.get('tag') != 'input') {
                            next = this.getParent().getPrevious();
                        }
                        if (!next.hasClass('ka-field-textlist-item') && next.get('tag') != 'input') {
                            next = input;
                        }
                        if (next.get('tag') == 'input') {
                            active = input;
                            input.setStyle('left', '');
                            input.setStyle('position', '');
                            input.focus();
                        } else {
                            active = next;
                            active.addClass('ka-field-textlist-item-active');
                        }
                    }
                    this.getParent().destroy();
                }).inject(item);

        };

        var checkAndCreateItem = function () {

            if (boxBody.getElement('.active')) {
                var item = boxBody.getElement('.active');
                addTextlistItem(item.get('text'), item.retrieve('value'));
            }

        }

        var updatePosition = function () {
            if (box && box.getParent) {

                box.position({
                    relativeTo: div,
                    position: 'bottomLeft',
                    edge: 'upperLeft'
                });

                var pos = box.getPosition();
                var size = box.getSize();

                var bsize = window.getSize();

                var height;

                if (size.y + pos.y > bsize.y) {
                    height = bsize.y - pos.y - 10;
                }

                if (height) {

                    if (height < 100) {

                        box.position({
                            relativeTo: div,
                            position: 'upperLeft',
                            edge: 'bottomLeft'
                        });

                    } else {
                        box.setStyle('height', height);
                    }

                }

                timer = updatePosition.delay(500);
            }
        }

        _searchValue = function (pValue) {
            if (lastRq) {
                lastRq.cancel();
            }

            var lastRq = new Request.JSON({url: _path + 'admin/' + _this.field.store, noCache: 1, onComplete: function (res) {

                boxBody.empty();
                if (typeOf(res) != 'object') {
                    boxBody.set('html', _('No results.'));
                } else {
                    Object.each(res, function (label, value) {
                        var a = new Element('a', {
                            text: label
                        }).inject(boxBody);

                        a.addEvent('mousedown', function (e) {
                            boxBody.getElements('a').removeClass('active');
                            this.addClass('active');
                            active = input;
                            checkAndCreateItem();
                            this.removeClass('active');
                            input.focus();
                            input.value = '';
                            e.stop();
                        });
                        a.store('value', value);
                    });
                    if (boxBody.getElement('a') && pValue) {
                        boxBody.getElement('a').addClass('active');
                    }
                }

            }}).post({search: pValue});
        }

        var searchValue = function (pValue) {

            if (!box) {
                var target = document.body;
                if (_this.fieldPanel.getParent('.kwindow-border')) {
                    target = _this.fieldPanel.getParent('.kwindow-border');
                }
                box = new Element('div', {
                    'class': 'ka-field-textlist-searchbox'
                }).inject(target);

                if (timer) {
                    clearTimeout(timer);
                }

                updatePosition();

                /*boxHead = new Element('div', {
                 'class': 'ka-field-textlist-searchbox-head'
                 }).inject( box );
                 boxHeadC = new Element('div', {
                 'class': 'ka-field-textlist-searchbox-head-c'
                 }).inject( boxHead );

                 new Element('input', {
                 'class': 'ka-field-textlist-searchbox-head'
                 }).inject( boxHeadC );
                 */

                boxBody = new Element('div', {
                    'class': 'ka-field-textlist-searchbox-body'
                }).inject(box);
            }
            _searchValue(pValue);
        }

        var hideSearchBox = function () {
            if (box) {
                boxBody.empty();
                box.destroy();
                box = null;
            }
        }

        if (this.field.store) {
            input.addEvent('blur', hideSearchBox);
            window.addEvent('click', hideSearchBox);
            input.addEvent('focus', function () {
                if (this.value.length > 0) {
                    searchValue(this.value);
                }
            });
        }


        input.addEvent('keydown', function (e) {
            if (e.key == 'enter' && this.value.length > 0) {
                if (_this.field.store) {
                    checkAndCreateItem();
                } else {
                    addTextlistItem(this.value);
                }
                this.value = '';
            }

            if (e.key == 'top' || e.key == 'bottom') {
                e.stop();
            }

            if (e.key == 'backspace') {
                if (active.get('tag') == 'div') {
                    this.inject(active, 'after');
                    active.destroy();
                    active = this;
                    this.setStyle('left', '');
                    this.setStyle('position', '');
                    this.focus();
                }
            }

            var oldActive = active;
            if ((e.key == 'left' || e.key == 'backspace' ) && this.value.length == 0) {
                if (active.getPrevious() && active.get('tag') == 'input') {
                    if (active.get('tag') == 'input') {
                        this.setStyle('left', -5000);
                        this.setStyle('position', 'absolute');
                        active.removeClass('ka-field-textlist-item-active');
                        active = this.getPrevious();
                        active.addClass('ka-field-textlist-item-active');
                    }
                }
                if (oldActive.get('tag') != 'input') {
                    this.inject(active, 'before');
                    active.removeClass('ka-field-textlist-item-active');
                    active = this;
                    this.setStyle('left', '');
                    this.setStyle('position', '');
                    this.focus();
                }
            }

            if (e.key == 'right' && this.value.length == 0) {
                if (active.getNext() && active.getNext().hasClass('ka-field-textlist-item') && active.get('tag') == 'input') {
                    if (active.get('tag') == 'input') {
                        this.setStyle('left', -5000);
                        this.setStyle('position', 'absolute');
                        active.removeClass('ka-field-textlist-item-active');
                        active = this.getNext();
                        active.addClass('ka-field-textlist-item-active');
                    }
                }
                if (oldActive.get('tag') != 'input') {
                    this.inject(active, 'after');
                    active.removeClass('ka-field-textlist-item-active');
                    active = this;
                    this.setStyle('left', '');
                    this.setStyle('position', '');
                    this.focus();
                }
            }

            if (_this.field.store) {
                if (e.key == 'down') {
                    var oldActive = boxBody.getElement('.active');
                    if (oldActive && oldActive.getNext()) {
                        oldActive.getNext().addClass('active');
                        oldActive.removeClass('active');
                    }
                }

                if (e.key == 'up') {
                    var oldActive = boxBody.getElement('.active');
                    if (oldActive && oldActive.getPrevious()) {
                        oldActive.getPrevious().addClass('active');
                        oldActive.removeClass('active');
                    }
                }
            }
        });

        var lastSearch = false;
        input.addEvent('keyup', function (e) {
            if (_this.field.store) {
                if (this.value.length > 0) {
                    if (lastSearch == this.value) {
                        return;
                    }
                    lastSearch = this.value;
                    searchValue(this.value);
                } else {
                    hideSearchBox();
                    lastSearch = '';
                }
            }
            this.setStyle('width', 6.5 * (this.value.length + 1));
        });

        div.addEvent('click', function (e) {
            if (e.target && !e.target.hasClass('ka-field-textlist')) return;
            input.inject(clear, 'before');
            input.setStyle('position', '');
            input.setStyle('left', '');
            if (active) {
                active.removeClass('ka-field-textlist-item-active');
            }
            input.focus();
            active = input;
        });

        this._setValue = function (pValue) {

            div.getElements('.ka-field-textlist-item').destroy();
            if (pValue == '' || !pValue) return;

            if (typeOf(pValue) == 'string') pValue = JSON.decode(pValue);

            if (_this.field.store) {
                Array.each(pValue, function (item) {
                    addTextlistItem(false, item);
                });
            } else {
                Array.each(pValue, function (item) {
                    addTextlistItem(item);
                });
            }
        }

        this.getValue = function () {
            var res = [];
            div.getElements('.ka-field-textlist-item').each(function (item) {
                res.include(item.retrieve('value'));
            });
            return res;
        }

    },

    renderCustom: function () {
        var _this = this;

        if (window[this.field['class']]) {

            this.customObj = new window[this.field['class']](this.field, this.fieldPanel, this.refs);

            this.customObj.addEvent('change', function () {
                this.fireChange();
            }.bind(this));

            this._setValue = this.customObj.setValue.bind(this.customObj);
            this.getValue = this.customObj.getValue.bind(this.customObj);
            this.isOk = this.customObj.isEmpty.bind(this.customObj);
            this.highlight = this.customObj.highlight.bind(this.customObj);

        } else {
            alert('Custom field: ' + this.field['class'] + '. Can not find this javascript class.');
        }
    },

    renderArray: function () {

        var table = new Element('table', {
            cellpadding: 2,
            cellspacing: 0,
            width: '100%',
            'class': 'ka-field-array'
        }).inject(this.fieldPanel);

        this.fieldPanel.setStyle('margin-left', 11);

        if (this.field.width) {
            this.fieldPanel.setStyle('width', this.field.width);
        }

        var thead = new Element('thead').inject(table);
        var tbody = new Element('tbody').inject(table);

        var actions = new Element('div', {

        }).inject(this.fieldPanel);


        var tr = new Element('tr').inject(thead);
        Array.each(this.field.columns, function (col) {

            var td = new Element('th', {
                valign: 'top',
                text: typeOf(col) == 'object'?col.label:col
            }).inject(tr);

            if (typeOf(col) == 'object'){
                if (col.desc){
                    new Element('div', {
                        'class': 'ka-field-array-column-desc',
                        text: col.desc
                    }).inject(td);
                }
                if (col.width) {
                    td.set('width', col.width);
                }
            }
        });
        var td = new Element('th', {
            style: 'width: 30px'
        }).inject(tr);


        if (this.field.withOrder){
            td.setStyle('width', 52);
        }

        var addRow = function (pValue) {

            if (this.field.designMode) return;

            var tr = new Element('tr').inject(tbody);
            tr.fields = {};

            Object.each(this.field.fields, function (field, field_key) {

                if (!field.panel_width) field.panel_width = '100%';

                var copy = Object.clone(field);
                field.noWrapper = 1;

                var td = new Element('td', {
                    'class': 'ka-field'
                }).inject(tr);

                var nField = new ka.Field(field, td, {win: this.win});

                if (pValue && pValue[field_key]) {
                    nField.setValue(pValue[field_key]);
                }

                tr.fields[field_key] = nField;

            }.bind(this));

            if (this.field.withOrder || !this.field.withoutRemove){
                var td = new Element('td').inject(tr);
            }

            if (this.field.withOrder){

                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/arrow_up.png',
                    style: 'cursor: pointer;',
                    title: t('Move up')
                }).addEvent('click', function () {
                    if(tr.getPrevious())
                        tr.inject(tr.getPrevious(), 'before');
                }).inject(td);


                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/arrow_down.png',
                    style: 'cursor: pointer;',
                    title: t('Move down')
                }).addEvent('click', function () {
                    if(tr.getNext())
                        tr.inject(tr.getNext(), 'after');
                }).inject(td);

            }

            if (!this.field.withoutRemove){
                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/delete.png',
                    style: 'cursor: pointer;',
                    title: _('Remove')
                }).addEvent('click', function () {
                    tr.destroy();
                }).inject(td);
            }


        }.bind(this);

        if (!this.field.withoutAdd){
            new ka.Button(this.field.addText ? this.field.addText : [t('Add'), '#icon-plus-alt']).addEvent('click', addRow).inject(actions);
        }

        var first, second;
        Object.each(this.field.fields, function(item, key){
            if (!first){
                first = key;
            } else if (!second){
                second = key;
            }
        });

        var fieldLength = Object.getLength(this.field.fields);

        this.getValue = function () {

            var res = this.field.asHash?{}:[];

            var ok = true;

            tbody.getChildren('tr').each(function (tr) {
                if (ok == false) return;

                var row = this.field.asArray?[]:{};

                Object.each(tr.fields, function (field, field_key) {

                    if (ok == false) return;

                    if (!field.isOk()) {
                        ok = false;
                    } else {

                        if (this.field.asArray){
                            if (fieldLength == 1)
                                row = field.getValue();
                            else
                                row.push(field.getValue());
                        } else
                            row[field_key] = field.getValue();
                    }

                }.bind(this));

                if (this.field.asHash){

                    if (fieldLength > 2){

                        var hash = {};
                        var i = -1;

                        Object.each(row, function(rvalue, rkey){
                            i++;
                            if (i == 0) return;
                            hash[rkey] = rvalue;

                        });

                        res[row[first]] = hash;
                    } else {
                        res[row[first]] = row[second];
                    }
                } else {

                    res.push(row);
                }

            }.bind(this));

            if (ok == false) return;

            return res;
        }.bind(this);


        this._setValue = function (pValue) {
            tbody.empty();

            if (typeOf(pValue) == 'string') {
                pValue = JSON.decode(pValue);
            }

            if (this.field.asHash){

                if (fieldLength > 2){

                    Object.each(pValue, function (item, idx) {

                        var val = {};
                        val[first] = idx;
                        Object.each(item, function(iV, iK){
                            val[iK] = iV;
                        });
                        addRow(val);

                    });

                } else {

                    Object.each(pValue, function (item, idx) {

                        var val = {};
                        val[first] = idx;
                        val[second] = item;

                        addRow(val);
                    });
                }
            } else {
                Array.each(pValue, function (item) {
                    if (this.field.asArray){
                        if (fieldLength == 1){
                            var nItem = {};
                            nItem[first] = item;
                            addRow(nItem);
                        } else {

                            var nItem = {};
                            var index = 0;
                            Object.each(this.field.fields, function(def, key){
                                nItem[key] = item[indexx];
                                index++;
                            });
                            addRow(nItem);
                        }
                    } else {
                        addRow(item);
                    }
                }.bind(this));
            }

        }.bind(this);

        if (this.field.startWith && this.field.startWith > 0) {
            for (var i = 0; i < this.field.startWith; i++) {
                addRow();
            }
        }

    },

    renderWindowList: function () {

        var div = new Element('div', {
            styles: {
                height: this.field.height
            }
        }).inject(this.fieldPanel);

        if (!this.field.panel_width) {
            this.main.setStyle('width', '');
        }

        var titleGroups = new Element('div', {
            'class': 'kwindow-win-title kwindow-win-titleGroups',
            style: 'display: none; top: 0px;padding: 3px; height: 25px; min-height: 25px;'
        }).inject(div);

        var content = new Element('div', {
            'class': 'kwindow-win-content',
            style: 'top: 36px;'
        }).inject(div);

        var pos = this.field['window'].indexOf('/');
        var module = this.field['window'].substr(0, pos);
        var code = this.field['window'].substr(pos + 1);

        var win = {};
        Object.append(win, this.win);

        Object.append(win, {
            content: content,
            extendHead: function () {
                titleGroups.setStyle('display', 'block');
            },
            addButtonGroup: function () {
                titleGroups.setStyle('display', 'block');
                return new ka.ButtonGroup(titleGroups);
            },
            module: module,
            code: code,
            _confirm: this.win._confirm,
            params: {},
            id: this.win.id
        });

        this.getValue = function () {
        };

        this._setValue = function (pValue) {

            if (!this.list) {
                this.list = new ka.WindowList(win, {
                    relation_table: pValue.table,
                    relation_params: pValue.params
                });
            } else {
                this.list.options.relation_params = pValue.params;

                if (this.list.classLoaded == true) {
                    this.list.loadPage(1, true);
                } else {
                    this.list.addEvent('render', function () {
                        this.list.loadPage(1, true);
                    }.bind(this));
                }

            }
        }.bind(this);
    },

    renderImageGroup: function () {

        this.input = new Element('div', {
            style: 'padding: 5px;',
            'class': 'ka-field-imageGroup'
        }).inject(this.fieldPanel);

        this.imageGroup = new ka.ImageGroup(this.input);

        this.imageGroupImages = {};

        Object.each(this.field.items, function (image, value) {

            this.imageGroupImages[ value ] = this.imageGroup.addButton(image.label, image.src);

        }.bind(this));

        this.imageGroup.addEvent('change', function () {

            this.fireChange();

        }.bind(this));

        this.getValue = function () {

            var value = false;
            Object.each(this.imageGroupImages, function (button, tvalue) {
                if (button.hasClass('buttonHover')) {
                    value = tvalue;
                }
            });

            return value;
        }

        this._setValue = function (pValue) {

            Object.each(this.imageGroupImages, function (button, tvalue) {
                button.removeClass('buttonHover');
                if (pValue == tvalue) {
                    button.addClass('buttonHover');
                }
            });
        }

    },

    renderHeadline: function () {

        this.input = new Element('h2', {
            html: this.field.label
        }).inject(this.fieldPanel);

    },

    renderFileList: function (pOpts) {
        var relHeight = (this.field.height) ? this.field.height : 150;
        var main = new Element('div', {
            styles: {
                position: 'relative',
                'height': relHeight,
                'width': (this.field.width) ? this.field.width : null
            }
        }).inject(this.fieldPanel);

        var wrapper = new Element('div', {
            style: 'position: absolute; left: 0px; top: 0px; bottom: 0px; right: 18px;'
        }).inject(main);

        this.input = new Element('select', {
            size: (this.field.size) ? this.field.size : 5,
            style: 'width: 100%',
            'class': 'ka-field',
            styles: {
                'height': (this.field.height) ? this.field.height : null
            }
        }).inject(wrapper);
        var input = this.input;


        var addFile = function (pPath) {
            new Element('option', {
                value: pPath,
                text: pPath
            }).inject(input);
            this.fireChange();
        }

        this.addImgBtn = new Element('img', {
            src: _path + PATH_MEDIA + '/admin/images/icons/add.png',
            style: 'position: absolute; top: 0px; right: 0px; cursor: pointer;'
        }).addEvent('click', function () {

            ka.wm.openWindow('admin', 'backend/chooser', null, -1, {onChoose: function (pValue) {
                addFile(pValue);
                this.win.close();//close paes/chooser windows -> onChoose.bind(this) in chooser-event handler
            },
                opts: {files: 1, upload: 1}
            });

        }.bind(this)).inject(main);


        this.addImgBtn = new Element('img', {
            src: _path + PATH_MEDIA + '/admin/images/icons/delete.png',
            style: 'position: absolute; top: 19px; right: 0px; cursor: pointer;'
        }).addEvent('click', function () {
            input.getElements('option').each(function (option) {
                if (option.selected) option.destroy();
            });
        }.bind(this)).inject(main);


        var _this = this;
        this.getValue = function () {
            var res = [];
            _this.input.getElements('option').each(function (option) {
                res.include(option.value);
            });
            return res;
        }

        this._setValue = function (pValues) {
            input.empty();
            if (typeOf(pValues) == 'string') pValues = JSON.decode(pValues);
            if (typeOf(pValues) != 'array') return;
            pValues.each(function (item) {
                new Element('option', {
                    text: item,
                    value: item
                }).inject(input);
            });
        }

    },

    setInputActive: function (pSet) {
        if (!pSet)
            this.input.addClass('text-inactive');
        else
            this.input.removeClass('text-inactive');
    },


    renderChooser: function (pObjects) {

        if (!pObjects) return;

        var definition = ka.getObjectDefinition(pObjects[0]);

        if (definition.chooserFieldJavascriptClass){

            if (!window[definition.chooserFieldJavascriptClass]){
                logger('Can no load custom object field class "'+definition.chooserFieldJavascriptClass+'" for object '+pObjects[0]);
                return;
            }

            this.customObj = new window[definition.chooserFieldJavascriptClass](this.field, this.fieldPanel, this);

            this.customObj.addEvent('change', function () {
                this.fireChange();
            }.bind(this));

            this._setValue = this.customObj.setValue.bind(this.customObj);
            this.getValue = this.customObj.getValue.bind(this.customObj);
            this.isOk = this.customObj.isEmpty.bind(this.customObj);
            this.highlight = this.customObj.highlight.bind(this.customObj);

        } else {

            if (this.field.objectRelation == 'nToM' || this.field.multi == 1){
                this.renderChooserMulti(pObjects);
            } else {
                this.renderChooserSingle(pObjects);
            }
        }

    },

    renderChooserMulti: function(pObjects){

        this.renderChooserColumns = [];

        this.objectDefinition = ka.getObjectDefinition(pObjects[0]);

        if (this.objectDefinition.chooserUseOwnClass != 1){
            Object.each(this.objectDefinition.chooserFieldDataModelFields, function(field,key){
                this.renderChooserColumns.include([
                    field.label?field.label:key,
                    field.width?field.width:null
                ]);
            }.bind(this));


        }

        this.renderChooserColumns.include(["", 50]);

        this.chooserTable = new ka.Table(this.renderChooserColumns, {absolute: false, selectable: true});

        this.chooserTable.inject(this.fieldPanel);
        this.renderObjectTableNoItems();

        //compatibility
        if (this.field.domain){
            if (!this.field.objectOptions) this.field.objectOptions = {};
            if (!this.field.objectOptions.node) this.field.objectOptions.node = {};
            this.field.objectOptions.node.domain = this.field.domain;
        }


        this._value = [];

        var chooserParams = {
            onSelect: function (pId) {

                this._value.include(ka.getObjectId(pId));
                this.renderObjectTable();

            }.bind(this),
            value: this._value,
            cookie: this.field.cookie,
            objects: pObjects,
            objectOptions: this.field.objectOptions
        };

        if (this._value)
            chooserParams.value = this._value;

        if (this.field.cookie)
            chooserParams.cookie = this.field.cookie;

        if (this.field.domain)
            chooserParams.domain = this.field.domain;


        var button = new ka.Button(t('Add')).addEvent('click', function () {

            if (this.field.designMode) return;
            ka.wm.openWindow('admin', 'backend/chooser', null, -1, chooserParams);

        }.bind(this))

        button.inject(this.fieldPanel);

        this._setValue = function(pVal){

            this._value = pVal;

            if (!this._value) this._value = [];

            if (typeOf(this._value) != 'array') this._value = [this._value];

            this.renderObjectTable();

        }.bind(this);

        this.getValue = function(){
            return this._value;
        }

    },

    renderObjectTableNoItems: function(){

        var tr = new Element('tr').inject(this.chooserTable.tableBody);
        new Element('td', {
            colspan: this.renderChooserColumns.length,
            style: 'text-align: center; color: gray; padding: 5px;',
            text: t('Empty')
        }).inject(tr);
    },

    renderObjectTable: function(){

        this.chooserTable.empty();

        this.objectTableLoaderQueue = {};

        if (!this._value || this._value.length == 0){
            this.renderObjectTableNoItems();
        } else {
            logger(this._value);
            Array.each(this._value, function(id){

                var row = [];

                var extraColumns = [];
                Object.each(this.objectDefinition.chooserFieldDataModelFields, function(field,key){
                    extraColumns.include(key);
                });

                var placeHolders = {};

                Array.each(extraColumns, function(col){

                    placeHolders[col] = new Element('span');
                    row.include(placeHolders[col]);

                });

                if (typeOf(id) == 'object')
                    id = ka.getObjectUrlId(this.field.object, id);

                this.renderObjectTableLoadItem(id, placeHolders);

                var actionBar = new Element('div');
                new Element('img', {
                    src: _path+ PATH_MEDIA + '/admin/images/icons/delete.png'
                }).inject(actionBar);

                row.include(actionBar);

                this.chooserTable.addRow(row);

            }.bind(this));
        }
    },

    renderObjectTableLoadItem: function(pId, pPlaceHolders){

        if (this.objectLastTableLoaderTimer){
            clearTimeout(this.objectLastTableLoaderTimer);
        }
        this.objectTableLoaderQueue[pId] = pPlaceHolders;

        this.objectLastTableLoaderTimer = this.doObjectTableLoad.delay(50, this);
    },

    doObjectTableLoad: function(){

        var url = 'object://'+this.field.object+'/';
        var ids = [];

        Object.each(this.objectTableLoaderQueue, function(placeholders, id){
            ids.push(id);
        });
        url += ids.join('/');

        if (this.lastRq)
            this.lastRq.cancel();

        this.lastRq = new Request.JSON({url: _path+'admin/backend/objects',
        noErrorReporting: true,
        onComplete: function(res){

            if(!res) return;

            if (res.error == 'no_object_access'){

                this.chooserTable.empty();
                new Element('div', {
                    text: t('No access to this object.'),
                    style: 'color: red; padding: 4px;'
                }).inject(this.chooserTable, 'after');

            } else if (res.error == 'object_not_found'){

                this.chooserTable.empty();
                new Element('div', {
                    text: t('Object definition not found.'),
                    style: 'color: red; padding: 4px;'
                }).inject(this.chooserTable, 'after');

            } else {

                var fields = ka.getObjectDefinition(this.field.object).fields;

                Object.each(this.objectTableLoaderQueue, function(placeholders, id){

                    Object.each(placeholders, function(td, colId){

                        if (res.data[id]){
                            var value = ka.getListLabel(res.data[id], fields[colId], colId);
                            td.set('text', value);
                        } else {
                            td.set('text', t('--not found--'));
                        }
                    });

                });

            }


        }.bind(this)}).get({
            uri: url
        });

    },

    renderChooserSingle: function(pObjects){

        var table = new Element('table', {
            style: 'width: 100%', cellpadding: 0, cellspacing: 0
        }).inject(this.fieldPanel);

        var tbody = new Element('tbody').inject(table);

        var tr = new Element('tr').inject(tbody);
        var leftTd = new Element('td').inject(tr);
        var rightTd = new Element('td', {width: '50px'}).inject(tr);

        this.input = new Element('input', {
            'class': 'text chooser text-inactive',
            type: 'text',
            disabled: true,
            style: 'width: 100%'
        })
        .addEvent('keyup',function () {
            this.fireEvent('blur');
        }).inject(leftTd);

        if (this.field.input_width){
            this.input.setStyle('width', this.field.input_width);
        }

        var div = new Element('span').inject(this.fieldPanel);

        //compatibility
        if (this.field.domain){
            if (!this.field.objectOptions) this.field.objectOptions = {};
            if (!this.field.objectOptions.node) this.field.objectOptions.node = {};
            this.field.objectOptions.node.domain = this.field.domain;
        }

        var chooserParams = {
            onSelect: function (pUrl) {
                this.setValue(pUrl, true);
            }.bind(this),
            value: this._value,
            cookie: this.field.cookie,
            objects: pObjects,
            objectOptions: this.field.objectOptions
        };

        if (this._value)
            chooserParams.value = this._value;

        if (this.field.cookie)
            chooserParams.cookie = this.field.cookie;

        if (this.field.domain)
            chooserParams.domain = this.field.domain;


        var button = new ka.Button(t('Choose')).addEvent('click', function () {

            if (this.field.designMode) return;
            ka.wm.openWindow('admin', 'backend/chooser', null, -1, chooserParams);

        }.bind(this))
        .inject(rightTd);

        this._setValue = function (pVal, pIntern) {

            if (typeOf(pVal) == 'null' || pVal === false || pVal === '' || !ka.getObjectId(pVal)) {
                this._value = '';
                this.input.value = '';
                this.input.title = '';
                return;
            }

            pVal = String.from(pVal);

            if ((typeOf(pVal) == 'string' && pVal.substr(0, 'object://'.length) != 'object://')){
                pVal = 'object://'+pObjects[0]+'/'+ka.urlEncode(pVal);
            }
            this._value = pVal;

            this.objectGetLabel(this._value, function(pLabel){
                this.input.value = pLabel;
            });

            this.input.title = ka.getObjectId(pVal);

        }

        this.getValue = function () {
            var val = (this._value) ? this._value : this.input.value;

            if (this.field.withoutObjectWrapper && typeOf(val) == 'string' && val.substr(0, 'object://'.length) == 'object://'){
                return ka.getObjectId(val);
            }
            return val;
        }
    },

    objectGetLabel: function(pUrl, pCallback){

        if (this.lastPageChooserGetUrlRequest) {
            this.lastPageChooserGetUrlRequest.cancel();
        }

        this.lastPageChooserGetUrlRequest = new Request.JSON({url: _path + 'admin/backend/object-label', noCache: 1, onComplete: function (res) {
            if (!res.error){

                if (res.values){
                    var definition = ka.getObjectDefinition(res.object);
                    var value = res.values[definition.chooserFieldDataModelField];

                    this._automaticUrl = value;
                    this.input.value = value;
                } else {
                    this.input.value = '';
                    this._automaticUrl = '';
                }
            } else {
                this.input.value = res.error;
            }
            this.input.fireEvent('blur');
        }.bind(this)}).post({object: pUrl});

    },

    renderSelect: function () {
        var _this = this;
        var multiple = ( this.field.multi || this.field.multiple );
        var sortable = this.field.sortable;

        var selWidth = 133;
        if (this.field.tinyselect) {
            selWidth = 75;
        }
        if (sortable) {
            selWidth -= 8;
        }

        if (!this.field.tableItems && this.field.table_items) {
            this.field.tableItems = this.field.table_items;
        }

        if (multiple && (!this.field.size || this.field.size + 0 < 4 )) {
            this.field.size = 4;
        }

        if (multiple) {
            this.input = new Element('select', {
                size: this.field.size,
                style: 'width: 100%'
            }).addEvent('change', function () {
                this.fireChange();
            }.bind(this)).inject(this.fieldPanel);
        }

        if (!this.field.tableItems && this.field.items) {
            this.field.tableItems = this.field.items;
        }


        var label = _this.field.table_label;
        var key = _this.field.table_key ? _this.field.table_key : _this.field.table_id;

        if (_this.field.relation == 'n-n') {
            var label = _this.field['n-n'].right_label;
            var key = _this.field['n-n'].right_key;
        }

        if (multiple) {

            this.renderItems = function () {

                _this.input.empty();

                if (typeOf(this.field.tableItems) == 'array') {

                    this.field.tableItems.each(function (item) {
                        if (!item) return;

                        if (_this.field.lang && item.lang != _this.field.lang && item.lang) return;

                        var text = '';
                        if (_this.field.table_view) {
                            $H(_this.field.table_view).each(function (val, mykey) {
                                var _val = '';
                                switch (val) {
                                    case 'time':
                                        _val = new Date(item[mykey] * 1000).format('db');
                                        break;
                                    default:
                                        _val = item[mykey];
                                }
                                text = text + ', ' + _val;
                            });
                            text = text.substr(2, text.length);
                        } else if (item && item[label]) {
                            text = item[label];
                        }

                        var t = new Element('option', {
                            text: text,
                            value: item[key]
                        })
                        if (t && _this.input) {
                            t.inject(_this.input);
                        }

                    });
                } else if (typeOf(this.field.tableItems) == 'object') {

                    Object.each(this.field.tableItems, function (item, key) {
                        var t = new Element('option', {
                            text: item,
                            value: key
                        })
                        if (t && _this.input) {
                            t.inject(_this.input);
                        }
                    });
                }

            }.bind(this);

            this.main.setStyle('width', 355);
            //if( this.field.small )
            //    this.main.setStyle('height', 80);
            //else
            //    this.main.setStyle('height', 115);

            var table = new Element('table').inject(this.input.getParent());
            var tbody = new Element('tbody').inject(table);

            var tr = new Element('tr').inject(tbody);
            var td = new Element('td').inject(tr);
            var td2 = new Element('td', {width: 32, style: 'vertical-align: middle;'}).inject(tr);
            var td3 = new Element('td').inject(tr);

            this.input.setStyle('width', selWidth);


            this.input.inject(td);

            var toRight = new ka.Button('»').addEvent('click', function () {
                if (this.input.getSelected()) {
                    this.input.getSelected().each(function (obj) {
                        var clone = obj.clone();
                        clone.inject(this.inputVals);
                        obj.set('disabled', true);
                        obj.set('selected', false);
                    }.bind(this));
                }
            }.bind(this)).setStyle('left', -2).inject(td2);

            new Element('span', {html: "<br /><br />"}).inject(td2);

            var toLeft = new ka.Button('«').addEvent('click', function () {
                if (this.inputVals.getSelected()) {
                    if (this.input.getElement('option[value=' + this.inputVals.value + ']')) {
                        this.input.getElement('option[value=' + this.inputVals.value + ']').set('disabled', false);
                    }
                    this.inputVals.getSelected().destroy();
                }
            }.bind(this)).setStyle('left', -2).inject(td2);


            this.input.addEvent('dblclick', function () {
                toRight.fireEvent('click');
            }.bind(this))


            this.inputVals = new Element('select', {
                size: this.field.size,
                'class': 'ka-field',
                style: 'width: ' + selWidth + 'px'
            }).addEvent('dblclick', function () {
                toLeft.fireEvent('click');
            }.bind(this)).inject(td3);


            if (this.field.tinyselect) {
                this.inputVals.setStyle('width', 75);
            }

            this.renderItems();

        } else {
            ///not mutiple
            this.select = new ka.Select();

            this.select.addEvent('change', function () {
                this.fireChange();
            }.bind(this));

            if (this.field.input_width)
                document.id(this.select).setStyle('width', this.field.input_width);

            this.select.inject(this.fieldPanel);

            this.renderItems = function () {

                var value = this.select.getValue();
                this.select.empty();


                if (typeOf(this.field.tableItems) == 'array') {
                    if (key) {
                        Array.each(this.field.tableItems, function (item) {

                            if (this.field.multiLanguage && this.field.lang && item.lang != this.field.lang && item.lang) return;

                            this.select.add(item[key], item[label]);
                        }.bind(this));
                    }

                } else if (typeOf(this.field.tableItems) == 'object') {

                    Object.each(this.field.tableItems, function (item, key) {

                        if (this.field.multiLanguage && this.field.lang && item.lang != this.field.lang && item.lang) return;

                        this.select.add(key, item);
                    }.bind(this));

                }

                if (value) {
                    this.select.setValue(value);
                }

            }.bind(this);

            this.renderItems();

        }

        if (sortable) {
            var td4 = new Element('td').inject(tr);
            var elUp = new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/arrow_up.png',
                style: 'display: block; cursor: pointer;'
            }).addEvent('click', function () {
                if (!this.inputVals.getElements('option') || this.inputVals.getElements('option').length < 2 || !this.inputVals.getSelected()) {
                    return;
                }

                var selOption = this.inputVals.getSelected();
                //check if el is top
                if (!selOption.getPrevious('option') || !$defined(selOption.getPrevious('option')[0])) {
                    return;
                }
                var selOptionClone = selOption.clone(true).inject(selOption.getPrevious('option')[0], 'before');
                selOption.destroy();

            }.bind(this)).inject(td4);

            new Element('div', {html: "<br /><br />"}).inject(td4);
            // var elDown = new ka.Button('Dw').addEvent('click',
            var elDown = new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/arrow_down.png',
                style: 'display: block; cursor: pointer;'
            }).addEvent('click', function () {

                if (!this.inputVals.getElements('option') || this.inputVals.getElements('option').length < 2 || !this.inputVals.getSelected()) {
                    return;
                }

                var selOption = this.inputVals.getSelected();

                //check if el is top                
                if (!selOption.getNext('option') || !$defined(selOption.getNext('option')[0])) {
                    return;
                }

                var selOptionClone = selOption.clone(true).inject(selOption.getNext('option')[0], 'after');
                selOption.destroy();

            }.bind(this)).inject(td4);
        }

        if (this.field.directory) {
            document.id(this.select).set('title', t('This list is based on files on this directory:') + ' ' + this.field.directory);
            new Element('div', {
                text: t('Based on:') + ' ' + this.field.directory,
                style: 'font-size: 11px; color: silver'
            }).inject(this.select || this.input, 'after');
        }

        this._setValue = function (pValue) {

            if (multiple) {
                this.inputVals.empty();
                this.input.getElements('option').set('disabled', false);


                this.input.getElements('option').each(function (option) {
                    option.selected = false;
                });
            }

            if (_this.field['relation'] == 'n-n' || multiple) {
                if (typeOf(pValue) == 'string') pValue = JSON.decode(pValue);
                if (typeOf(pValue) != 'array') pValue = [];
            }

            if (_this.field['relation'] == 'n-n') {

                pValue.each(function (_item) {
                    _this.input.getElements('option').each(function (option) {
                        if (option.value == _item[_this.field['n-n'].middle_keyright]) {
                            if (multiple) {
                                option.clone().inject(this.inputVals);
                                option.set('disabled', true);
                                option.set('selected', false);
                            } else {
                                option.selected = true;
                            }
                        }
                    }.bind(this));
                }.bind(this));

            } else if (multiple && !sortable) {

                this.input.getElements('option').each(function (option) {
                    if (pValue.contains(option.value)) {
                        option.clone().inject(this.inputVals);
                        option.set('disabled', true);
                        option.set('selected', false);
                    }
                }.bind(this));

            } else if (multiple) {
                pValue.each(function (pItem) {
                    iSelOption = this.input.getElement('option[value="' + pItem + '"]');
                    if ($defined(iSelOption) && typeOf(iSelOption) != 'null') {

                        iSelOption.clone().inject(this.inputVals);
                        iSelOption.set('disabled', true);
                        iSelOption.set('selected', false);
                    }
                }.bind(this));


            } else {
                this.select.setValue(pValue);
            }

        };

        this.getValue = function () {
            var res = [];
            if (multiple) {
                _this.inputVals.getElements('option').each(function (option) {
                    res.include(option.value);
                });
            } else {
                res = _this.select.getValue();
            }
            return res;
        }

    },

    renderWysiwyg: function () {
        this.lastId = 'WindowField' + this.fieldId + ((new Date()).getTime()) + '' + $random(123, 5643) + '' + $random(13284134, 1238845294);
        //this.lastId = 'field'+(new Date()).getTime();

        this.input = new Element('textarea', {
            id: this.lastId,
            name: this.lastId,
            value: this.field.value,
            'class': 'ka-field',
            style: 'width: 100%',
            styles: {
                'height': (this.field.height) ? this.field.height : 80,
                'width': (this.field.width) ? this.field.width : ''
            }
        }).inject(this.fieldPanel);

        this.fieldPanel.addClass('selectable');

        var mooeditable = initWysiwyg(this.input);

        return;
    },

    renderDate: function (pOptions) {
        this.input = new Element('input', {
            'class': 'text ka-field-dateTime',
            type: 'text',
            style: 'width: 100%'
        }).inject(this.fieldPanel);

        var datePicker = new ka.DatePicker(this.input, pOptions);

        if (this.field.input_width)
            this.input.setStyle('width', this.field.input_width);

        if (this.refs.win) {
            this.refs.win.addEvent('resize', datePicker.updatePos.bind(datePicker));
            this.refs.win.addEvent('move', datePicker.updatePos.bind(datePicker));
        }

        datePicker.addEvent('change', function () {
            this.fireChange();
        }.bind(this));

        this.getValue = function () {
            return datePicker.getTime();
        };
        this._setValue = function (pVal) {
            datePicker.setTime((pVal != 0) ? pVal : false);
        }.bind(this);

        if (this.field['default'] && this.field['default'] != "") {
            var time = new Date(this.field['default']).getTime();
            if (this.field['default']) {
                var time = new Date().getTime();
            }
            this.setValue(time, true);
        }
    },

    renderCheckbox: function () {
        var _this = this;

        this.checkbox = new ka.Checkbox(this.fieldPanel);

        this.getValue = function(){
            return this.checkbox.getValue() == true? 1:0;
        }.bind(this);

        this.checkbox.addEvent('change', function(){
            this.fireChange();
        }.bind(this));

        this._setValue = function(pValue){
            if (typeOf(pValue) == 'null') pValue = this.field['default'] || false;
            if (typeOf(pValue) == 'string') pValue = pValue.toInt();
            this.checkbox.setValue(pValue+0);
        }.bind(this);


    },

    renderNumber: function () {

        this.renderText();

        this.input.addEvent('keyup', function () {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });

        this.getValue = function(){
            return parseFloat(this.input.value);
        }.bind(this);

    },

    renderText: function () {
        var _this = this;
        this.input = new Element('input', {
            'class': 'text gradient',
            type: 'text',
            style: 'width: 100%'
        }).inject(this.fieldPanel);

        if (this.field.length) this.input.set('maxlength', this.field.length);

        if (this.field.input_width)
            this.input.setStyle('width', this.field.input_width);


        var _this = this;

        this.input.addEvent('change', function(){
            this.fireChange();
        }.bind(this));

        this.input.addEvent('keyup', function(){
            this.fireChange();
        }.bind(this));

        if (this.field.width) {
            this.input.setStyle('width', this.field.width);
        }

        if (this.field.check == 'kurl') {

            this.input.addEvent('keyup', function (e) {

                var old = this.getSelectedRange();
                var o = ['ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', 'ß'];

                o.each(function (char) {
                    if (this.value.contains(char)) {
                        old.start++;
                        old.end++;
                    }
                }.bind(this));

                this.value = _this.checkKurl(this.value);

                /*if( this.value.substr(0, 1) == '-' )
                 this.value = this.value.substr( 1, this.value.length );
                 */

                this.selectRange(old.start, old.end);

            });
        }

        this._setValue = function (pVal) {
            if (typeOf(pVal) == 'null') pVal = '';

            this.input.value = pVal;
        }

        this.getValue = function () {
            return this.input.value;
        }
    },

    checkKurl: function (pValue) {
        if (this.field.check == 'kurl') {
            return pValue.replace(/Ä/g, 'AE').replace(/ä/g, 'ae').replace(/Ö/g, 'OE').replace(/ö/g, 'oe').replace(/Ü/g, 'UE').replace(/ü/g, 'ue').replace(/ß/g, 'ss').replace(/\W/g, '-').toLowerCase();
        } else {
            return pValue;
        }
    },

    renderCodemirror: function(){

        this.editorPanel = new Element('div', {
            style: 'border: 1px solid silver; min-height: 50px; background-color: white;'
        }).inject(this.fieldPanel);


        if (this.field.input_width)
            this.editorPanel.setStyle('width', this.field.input_width);

        if (this.field.input_height){

            var cssClassName = 'codemirror_'+(new Date()).getTime()+'_'+Number.random(0, 10000)+'_'+Number.random(0, 10000);

            if (typeOf(this.field.input_height) == 'number' || !this.field.input_height.match('[^0-9]')){
                this.field.input_height += 'px';
            }

            new Stylesheet().addRule('.'+cssClassName+' .CodeMirror-scroll', {
                height: this.field.input_height
            });

            this.editorPanel.addClass(cssClassName);

        }

        var options = {
            lineNumbers: true,
            mode: 'htmlmixed',
            value: ''
        };

        if (this.field.codemirrorOptions){
            Object.each(this.field.codemirrorOptions, function(value, key){
                options[key] = value;
            });
        }
        this.editor = CodeMirror(this.editorPanel, options);

        this.editor.setOption("mode", options.mode);
        CodeMirror.autoLoadMode(this.editor, options.mode);

        this._setValue = function(pValue){

            this.editor.setValue(pValue?pValue:"");

        }.bind(this);

        this.getValue = function(){

            return this.editor.getValue();

        }.bind(this);

        var refresh = function(){
            this.editor.refresh();
        }.bind(this);

        var window = this.fieldPanel.getParent('.kwindow-border');
        if (this.win){
            this.win.addEvent('resize', refresh);
        } else if (window){
            this.win.retrieve('win').addEvent('resize', refresh);
        }

        var tabPane = this.fieldPanel.getParent('.ka-tabPane-pane');
        if (tabPane){
            tabPane.button.addEvent('show', refresh);
        }

        this.addEvent('show', refresh);


    },

    renderTextarea: function () {
        var _this = this;
        this.input = new Element('textarea', {
            'class': 'ka-field',
            styles: {
                'height': (this.field.inputHeight) ? this.field.inputHeight : 80,
                'width': (this.field.inputWidth) ? this.field.inputWidth : '100%'
            }
        }).inject(this.fieldPanel);

        this.input.addEvent('change', function(){
            this.fireChange();
        }.bind(this));

        this.input.addEvent('keyup', function(){
            this.fireChange();
        }.bind(this));

        this._setValue = function (pVal) {
            if (typeOf(pVal) == 'null') pVal = '';

            this.input.value = pVal;
        }

        this.getValue = function () {
            return this.input.value;
        }

    },

    renderPassword: function () {
        var _this = this;
        this.input = new Element('input', {
            'class': 'text',
            type: 'password',
            style: 'width: 100%'
        }).inject(this.fieldPanel);

        if (this.field.length) this.input.set('maxlength', this.field.length);

        if (this.field.input_width)
            this.input.setStyle('width', this.field.input_width);

        this.input.addEvent('change', function(){
            this.fireChange();
        }.bind(this));

        this.input.addEvent('keyup', function(){
            this.fireChange();
        }.bind(this));

        this._setValue = function (pVal) {
            if (typeOf(pVal) == 'null') pVal = '';

            this.input.value = pVal;
        }

        this.getValue = function () {
            return this.input.value;
        }

    },

    setIsOk: function (pIsOk) {

        if (this.emptyIcon) this.emptyIcon.destroy();
        if (!this.input) return;

        if (pIsOk) return;

        this.emptyIcon = new Element('img', {
            src: _path + PATH_MEDIA + '/admin/images/icons/exclamation.png',
            'class': 'ka-field-emptyIcon'
        }).inject(this.input.getParent());

        this.input.set('class', this.input.get('class') + ' empty');
    },

    highlight: function () {
        if (!this.input) return;
        this.input.highlight();
    },

    /**
     * @deprecated
     */
    isEmpty: function () {
        return this.isOk();
    },

    isFieldValid: function () {
        var ok = true;

        if (this.isHidden()) return ok;

        if ((this.field.empty === "0" || this.field.empty === 0 || this.field.empty === false) && this.getValue() === '')
            ok = false;

        if (this.field.required_regexp){
            var rx = new RegExp(this.field.required_regexp);
            if (!rx.test(this.getValue().toString())){
                ok = false;
            }
        }

        return ok;
    },

    updateOkInfo: function(){
        if (this.field.designMode) return;
        var status = this.isFieldValid();
        this.setIsOk(status);
    },

    isOk: function(){
        if (this.field.designMode) return;

        var status = this.isFieldValid();
        this.setIsOk(status);
        return status;
    },

    getValue: function () {
        if (!this.input) return;
        return this.input.value;
    },

    toString: function () {
        return this.getValue();
    },

    setValue: function (pValue, pIntern) {

        if (typeOf(pValue) == 'null' && this.field['default']) {
            pValue = this.field['default'];
        }

        if (this._setValue) {
            this._setValue(pValue, pIntern);
        }

        if (pIntern) {
            this.fireChange();
        } //fires check-depends too
        else {
            this.fireEvent('check-depends');
        }
    },

    fireChange: function(){

        this.fireEvent('change', [this.getValue(), this, this.id]);

    },

    _setValue: function (pValue, pIntern) {
        //Override this function to define a own setter
    },

    //should not be used anymore
    //use instead: this.fireEvent('change', this.getValue());
    onChange: function () {
        this.fireEvent('change', [this.getValue(), this, this.id]);
    },

    findWin: function () {

        if (this.win) return;

        var win = this.toElement().getParent('.kwindow-border');
        if (!win) return;

        this.win = win.retrieve('win');
    },

    prepareChildContainer: function(){

        if (this.childContainer) return;

        if (this.field.tableItem) {
            var tr = new Element('tr').inject(document.id(this), 'after');
            var td = new Element('td', {colspan: 2, style: 'padding: 0px; border-bottom: 0px;'}).inject(tr);

            this.childContainer = new Element('div', {
                'class': 'ka-fields-sub'
            }).inject(td);

        } else {
            this.childContainer = new Element('div', {
                'class': 'ka-fields-sub'
            }).inject(document.id(this), 'after');
        }

    },

    toElement: function () {
        return ( this.field.tableItem ) ? this.tr : this.main;
    },

    inject: function (pTo, pP) {

        if (this.field.onlycontent) {
            this.fieldPanel.inject(pTo, pP);
            return this;
        }


        if (this.main.getDocument() != pTo.getDocument()) {
            pTo.getDocument().adoptNode(this.tr || this.main);
        }

        if (this.tr) {
            this.tr.inject(pTo, pP);
        } else {
            this.main.inject(pTo, pP);
        }

        if (this.customObj) {
            this.customObj.inject(this.fieldPanel);
        }

        this.findWin();

        return this;
    },

    destroy: function () {
        this.main.destroy();
    },

    hide: function () {

        if (this.childContainer && this.childContainer.hide) this.childContainer.hide();

        if (this.tr) {
            this.tr.setStyle('display', 'none');
        } else {
            this.main.setStyle('display', 'none');
        }

        this.fireEvent('check-depends');
        this.fireEvent('hide');
    },


    /**
     * Is hidden because a depends issue.
     */
    isHidden: function () {
        if (this.tr) {
            if (this.tr.getStyle('display') == 'none') {
                return true;
            }
        } else if (this.main.getStyle('display') == 'none') {
            return true;
        }
        return false;
    },

    show: function () {
        if (this.tr) {
            this.tr.setStyle('display', 'table-row');
        } else {
            this.main.setStyle('display', 'block');
        }

        this.fireEvent('check-depends');
        this.fireEvent('show');
    },


    initLayoutElement: function () {

        _win = this.refs.win;

        this.main.setStyle('width', '');
        this.main.addClass('selectable');

        this.obj = new ka.field_layoutElement(this);

        this._setValue = this.obj.setValue.bind(this.obj);
        this.getValue = this.obj.getValue.bind(this.obj);
    },

    setArrayValue: function (pValues, pKey) {

        if (pValues == null) {
            this.setValue(null, true);
            return;
        }

        var values = pValues;
        var keys = pKey.split('[');
        var notFound = false;
        Array.each(keys, function (key) {

            if (notFound) return;
            if (values[ key.replace(']', '')]) {
                values = values[ key.replace(']', '')];
            } else {
                notFound = true;
            }

        });

        if (!notFound) {
            this.setValue(values);
        }
    },

    initMultiUpload: function () {
        //need to pass the win instance seperatly otherwise the setOptions method will thrown an error 
        _win = this.refs.win;
        this.refs.win = false;


        _this = this;
        //init ext js class
        if (this.field.extClass) {
            try {
                this.obj = new window[ this.field.extClass ](this.field, _win, _this);
            } catch (e) {

                this.obj = new ka.field_multiUpload(this.field, _win, _this);
            }
        } else {
            this.obj = new ka.field_multiUpload(this.field, _win, _this);
        }

        this.isOk = this.obj.isEmpty.bind(this.obj);
    }
});
