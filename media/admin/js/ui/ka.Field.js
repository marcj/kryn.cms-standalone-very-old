
ka.FieldTypes = {};

ka.Field = new Class({

    Implements: [Options, Events],

    Binds: ['fireChange'],

    options: {

        value: null,

        small: 0,

        label: null,
        type: 'text',

        tableItem: false, //use TR as parent instead of div
        help: null,

        startEmpty: false,
        fieldWidth: null,

        'default': null,
        designMode: false,
        disabled: false,

        invisible: false,

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

    initialize: function (pField, pContainer, pFieldId) {

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

                this.main = pContainer;
                this.main.store('ka.Field', this);
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
                }).addEvent('mouseover',function () {
                    this.setStyle('opacity', 1);
                }).addEvent('mouseout',function () {
                    this.setStyle('opacity', 0.7);
                }).addEvent('click',function () {
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
            this.fieldPanel.setStyle('width', this.options.fieldWidth);


        if (this.field.invisible == 1) {
            this.main.setStyle('display', 'none');
        }

        this.findWin();

        this.renderField();

        if (!this.field.startEmpty && typeOf(this.field.value) != 'null') {
            this.fieldObject.setValue(this.field.value, true);
        } else if (typeOf(this.field['default']) != 'null'){
            this.fieldObject.setValue(this.field['default'], true);
        }

        if (this.options.disabled){
            this.fieldObject.setDisabled(true);
        }

    },

    renderField: function () {

        this.options.type = this.options.type?this.options.type:'text';
        var clazz = ka.FieldTypes[this.options.type] || ka.FieldTypes[this.options.type.capitalize()];

        if (clazz){
            this.fieldObject = new clazz(this, this.options);
        } else {
            throw 'The ka.Field type '+this.options.type+' is not available.';
        }

        return;

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
            case 'language':

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

        this.getValue = function(){
            var res = [];
            if (multiple) {
                _this.inputVals.getElements('option').each(function (option) {
                    res.include(option.value);
                });
            } else {
                res = _this.select.getValue();
            }
            return res;
        };

    },

    highlight: function () {
        this.fieldObject.highlight();
    },

    isValid: function () {
        var ok = true;
        if (this.field.designMode) return ok;

        if (this.isHidden()) return ok;

        ok = this.fieldObject.isValid();

        return ok;
    },

    isOk: function(){

        var status = this.isValid();
        this.fieldObject.showNotValid(status);
        return status;
    },

    getValue: function () {
        return this.fieldObject.getValue();
    },

    toString: function () {
        return this.getValue();
    },

    setValue: function (pValue, pInternal){

        if (typeOf(pValue) == 'null' && this.field['default']) {
            pValue = this.field['default'];
        }

        if (this.fieldObject) {
            this.fieldObject.setValue(pValue, pInternal);
        }

        if (pInternal) {
            this.fireChange();
        } else {
            this.fireEvent('check-depends');
        }
    },

    fireChange: function(){
        this.fireEvent('change', [this.getValue(), this, this.id]);
        this.fireEvent('check-depends');
        this.isOk();

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

    toElement: function(){
        return ( this.field.tableItem ) ? this.tr : this.main;
    },

    inject: function (pTo, pP) {

        if (this.options.noWrapper){
            throw 'There is no way to inject a ka.field with noWrapper=1.';
        }

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

        if (typeOf(pValues) === 'null') {
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
        //todo: whats that?
        
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
