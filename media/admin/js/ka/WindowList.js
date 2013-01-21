ka.WindowList = new Class({
    Implements: [Events, Options],

    Binds: ['nestedItemSelected'],

    options: {

        nestedRootAddLabel: null,
        addLabel: null

    },

    loadAlreadyTriggeredBySearch: false,

    initialize: function (pWindow, pOptions, pContainer) {

        this.options = this.setOptions(pOptions);
        this.win = pWindow;

        if (pContainer)
            this.container = pContainer;
        else
            this.container = this.win.content;

        this.container.empty();

        this.container.setStyle('overflow', 'hidden');
        this.page = 1;
        this.checkboxes = [];

        this.sortField = '';
        this.sortDirection = 'ASC';
        this.currentPage = 1;

        //this.oriWinCode = this.win.code;
        this.oriWinEntryPoint = this.win.entryPoint;

        this.load();
        var _this = this;

        this.fReload = this.softReload.bind(this);

        window.addEvent('softReload', this.fReload);

    },

    softReload: function (pCode) {
        if (this.win.closed) return;

        if (pCode == this.win.getEntryPoint()) {
            this.reload();
        }
    },

    reload: function () {
        this.loadPage(this.currentPage);
    },

    load: function () {
        var _this = this;

        this.container.set('html', '<div style="text-align: center; padding: 50px; color: silver">'+t('Loading definition ...')+'</div>');

        new Request.JSON({url: _path + 'admin/' + this.win.getEntryPoint(), noCache: true, onComplete: function (res) {

            if (res.error) return false;

            this.render(res.data);
            this.classLoaded = true;
            this.fireEvent('render');

        }.bind(this)}).get({_method: 'options'});
    },

    deleteItem: function (pItem) {
        var _this = this;
        this.lastRequest = new Request.JSON({url: _path + 'admin/' + this.win.getEntryPoint() + '?cmd=deleteItem', noCache: true, onComplete: function (res) {

            //todo, handle errors
            this.win.softReload();
            this._deleteSuccess();
            this.reload();
        }.bind(this)}).post({
            item: pItem.values
        });
    },

    _deleteSuccess: function () {
    },

    click: function (pColumn) {

        if (this.columns && this.columns[pColumn]){

            pItem = this.columns[pColumn];

            if (!this.sortDirection) {
                this.sortDirection = 'ASC';
            }

            if (this.sortField != pColumn) {
                this.sortField = pColumn;
                this.sortDirection = (this.sortDirection.toLowerCase() == 'asc') ? 'asc' : 'desc';
            } else {
                this.sortDirection = (this.sortDirection.toLowerCase() == 'asc') ? 'desc' : 'asc';
            }

            pItem.getParent().getElements('img').each(function (item) {
                item.destroy();
            });

            if (this.sortDirection == 'ASC') {
                pic = 'bullet_arrow_up.png';
            } else {
                pic = 'bullet_arrow_down.png';
            }

            new Element('img', {
                src: _path + PATH_MEDIA + '/admin/images/icons/' + pic,
                align: 'top'
            }).inject(pItem);
        }

        this.loadPage(this.currentPage);
    },

    getSortField: function(){

        var field = null, direction = 'asc';

        if ( (typeOf(this.classProperties.order) == 'array' && this.classProperties.order.length > 0) ||
            (typeOf(this.classProperties.order) == 'object' && Object.getLength(this.classProperties.order) > 0) ){

            if (typeOf(this.classProperties.order) == 'array'){
                Array.each(this.classProperties.order, function(order, f){
                    if (!field){
                        field = order.field;
                        direction = order.direction;
                    }
                });
            } else if (typeOf(this.classProperties.order) == 'object'){
                Object.each(this.classProperties.order, function(order, f){
                    if (!field){
                        field = f;
                        direction = order;
                    }
                });
            }
        } else {
            //just use first column
            if (this.classProperties.columns){
                Object.each(this.classProperties.columns, function(col, id){
                    if (field) return;
                    field = id;
                })
            }
        }

        return {
            field: field,
            direction: direction
        }
    },

    checkClassProperties: function(){

        if (!this.classProperties.columns || !Object.getLength(this.classProperties.columns)){
            this.win.alert(t('This window class does not have columns defined.'));
            return false;
        }

        return true;
    },

    render: function (pValues) {
        this.classProperties = pValues;

        if (!this.checkClassProperties()) return false;

        var sort = this.getSortField();
        this.sortField = sort.field;
        this.sortDirection = sort.direction;

        this.container.empty();

        this.renderHeader();

        this.renderLayout();

        this.renderActionbar();

        this.renderLoader();

        this.renderFinished();
    },

    renderHeader: function(){

        this.headerLayout = new ka.LayoutHorizontal(this.win.getTitleGroupContainer(), {
            columns: [150, null, 250]
        });

        this.renderMultilanguage();

    },

    renderFinished: function () {

        //logger('renderFinished: '+this.options.noInitLoad+'/'+this.loadAlreadyTriggeredBySearch);
        if (this.options.noInitLoad == true) return;

        if (!this.loadAlreadyTriggeredBySearch) {
            if (this.columns) {
                var sort = this.getSortField();
                //logger('sort: '+sort.field);
                this.click(sort.field);
            } else {
                this.loadPage(1);
            }
        }

    },

    renderLoader: function () {
        this.loader = new ka.Loader().inject(this.main);
    },

    renderMultilanguage: function () {

        if (this.classProperties.multiLanguage) {

            this.languageSelect = new ka.Select(this.headerLayout.getColumn(3));
            document.id(this.languageSelect).setStyle('width', 140);

            this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

            var hasSessionLang = false;
            Object.each(ka.settings.langs, function (lang, id) {

                this.languageSelect.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');
                if (id == window._session.lang)
                    hasSessionLang = true;

            }.bind(this));

            if (hasSessionLang)
                this.languageSelect.setValue(window._session.lang);
        }
    },

    renderLayout: function () {

        if (this.classProperties.asNested){
            this.renderLayoutNested(this.container);
        } else {
            this.renderLayoutTable();
            this.renderSearchPane();
        }
    },

    renderLayoutNested: function(pContainer){

        var objectOptions = {};

        objectOptions.type = 'tree';
        objectOptions.object = this.classProperties.object;
        objectOptions.scopeChooser = false;
        objectOptions.noWrapper = true;

        if (this.languageSelect)
            objectOptions.scopeLanguage = this.languageSelect.getValue();

        this.nestedField = new ka.Field(objectOptions);

        this.nestedField.inject(pContainer, 'top');

        if (this.classProperties.edit){
            this.nestedField.addEvent('select', this.nestedItemSelected);
        }

    },

    addNestedRoot: function(){


    },

    openAddItem: function(){

        ka.entrypoint.open(ka.entrypoint.getRelative(this.win.getEntryPoint(), this.classProperties.addEntrypoint), {
            lang: (this.languageSelect) ? this.languageSelect.getValue() : false
        }, this);

    },

    renderLayoutTable: function(){

        /* head */

        this.head = new Element('div', {
            'class': 'ka-list-head'
        }).inject(this.container);

        this.headTable = new Element('table', {
            cellspacing: 0
        }).inject(this.head);

        this.headTableTHead = new Element('thead', {
            'class': 'ka-Table-head'
        }).inject(this.headTable);
        var tr = new Element('tr').inject(this.headTableTHead);

        /*** checkbox-Th ***/
        if (this.classProperties.remove == true) {
            var th = new Element('th', {
                style: 'width: 21px;'
            }).inject(tr);
            new Element('input', {
                value: 1,
                type: 'checkbox'
            }).addEvent('click',function () {
                var checked = this.checked;
                this.checkboxes.each(function (checkbox) {
                    checkbox.checked = checked;
                });
            }.bind(this)).inject(th)
        }

        /*** title-Th ***/
        this.columns = {};
        if (!this.classProperties.columns || this.classProperties.columns.length == 0){
            this.win.alert(t('This class does not contain any columns.'), function(){
                this.win.close();
            }.bind(this));
            throw 'Class does not contain column.';
        }
        Object.each(this.classProperties.columns, function (column, columnId) {
            this.columns[columnId] = new Element('th', {
                html: t(column.label)
            }).addEvent('click',
                function () {
                    this.click(columnId);
                }).inject(tr);
            if (column.width > 0) {
                this.columns[columnId].setStyle('width', column.width + 'px');
            }
        }.bind(this));

        /*** edit-Th ***/
        //fixed mirco
        if (this.classProperties.remove == true || this.classProperties.edit == true || this.classProperties.itemActions) {
            this.titleIconTd = new Element('th', {
                style: 'width: 40px;'
            }).inject(tr);
        }


        /* content */
        this.main = new Element('div', {
            'class': 'ka-list-main'
        }).inject(this.container);


        this.table = new Element('table', {
            cellspacing: 0
        }).inject(this.main);
        this.tbody = new Element('tbody', {
            'class': 'ka-Table-body'
        }).inject(this.table);

        /* bottom */

        this.bottom = new Element('div', {
            'class': 'ka-list-bottom'
        }).inject(this.container);
    },

    changeLanguage: function () {

        if (this.classProperties.asNested){



        } else {
            this.loadPage(1);
        }


    },

    renderSearchPane: function () {

        this.searchPane = new Element('div', {
            'class': 'ka-list-search-pane'
        }).inject(this.main, 'after');

        this.searchFields = new Hash();
        var doSearchNow = false;

        if (this.classProperties.filter && this.classProperties.filter.each) {
            this.classProperties.filter.each(function (filter, key) {


                var mkey = key;

                if (typeOf(key) == 'number') {
                    mkey = filter;
                }

                var field = this.classProperties.filterFields[ mkey ];


                var title = this.classProperties.columns[mkey].label;
                field.label = _(title);
                field.small = true;


                var fieldObj = new ka.Field(field, this.searchPane, {win: this.win}).addEvent('change', this.doSearch.bind(this));

                this.searchFields.set(mkey, fieldObj);

                if (field.type == 'select') {
                    fieldObj.select.add('', _('-- Please choose --'), 'top');
                    fieldObj.setValue('');
                }

                if (this.win.params && this.win.params.filter) {
                    Object.each(this.win.params.filter, function (item, key) {
                        if (item == mkey) {
                            fieldObj.setValue(this.win.params.item.values[key]);
                            doSearchNow = true;
                        }
                    }.bind(this));
                }

            }.bind(this));
        } else {
            this.filterButton.destroy();
        }

        if (doSearchNow) {
            this.toggleSearch();
            this.loadAlreadyTriggeredBySearch = true;
            this.doSearch();
        }
    },

    doSearch: function () {
        if (this.lastTimer) {
            clearTimeout(this.lastTimer);
        }

        var mySearch = function () {
            this.loadPage(1);
        }.bind(this);
        this.lastTimer = mySearch.delay(200);
    },

    getSearchVals: function () {
        var res = new Hash();

        this.searchFields.each(function (field, key) {
            res.set(key, field.getValue());
        });

        return JSON.encode(res);
    },

    toggleSearch: function () {
        if (this.searchEnable == 1) {
            this.filterButton.set('class', 'ka-list-search-button');
            this.searchEnable = 0;
            this.searchPane.tween('height', 1);
            this.main.tween('bottom', 30);
        } else {
            this.searchEnable = 1;
            this.filterButton.set('class', 'ka-list-search-button ka-list-search-button-active');
            this.searchPane.tween('height', 121);
            this.main.tween('bottom', 120 + 30);
        }
    },

    renderActionbar: function () {
        var _this = this;

        this.filterButton = new Element('a', {
            href: 'javascript: ;',
            html: _('Search'),
            'class': 'ka-list-search-button'
        }).addEvent('click', this.toggleSearch.bind(this)).inject(this.bottom);

        var myPath = _path + PATH_MEDIA + '/admin/images/icons/';
        this.navi = new Element('div', {
            'class': 'navi'
        }).inject(this.bottom);

        this.ctrlFirst = new Element('img', {
            src: myPath + 'control_start.png'
        }).addEvent('click',
            function () {
                _this.loadPage(1);
            }).inject(this.navi);

        this.ctrlPrevious = new Element('img', {
            src: myPath + 'control_back.png'
        }).addEvent('click',
            function () {
                _this.loadPage(parseInt(_this.ctrlPage.value) - 1);
            }).inject(this.navi);

        this.ctrlPage = new Element('input', {
        }).addEvent('keydown',
            function (e) {
                if (e.key == 'enter') {
                    _this.loadPage(parseInt(_this.ctrlPage.value));
                }
                if (['backspace', 'left', 'right'].indexOf(e.key) == -1 && (!parseInt(e.key) + 0 > 0)) {
                    e.stop();
                }
            }).inject(this.navi);

        this.ctrlMax = new Element('span', {
            text: '/ 0',
            style: 'position: relative; top: -3px; padding: 0px 3px 0px 3px;'
        }).inject(this.navi);

        this.ctrlNext = new Element('img', {
            src: myPath + 'control_play.png'
        }).addEvent('click',
            function () {
                _this.loadPage(parseInt(_this.ctrlPage.value) + 1);
            }).inject(this.navi);

        this.ctrlLast = new Element('img', {
            src: myPath + 'control_end.png'
        }).addEvent('click',
            function () {
                _this.loadPage(_this.lastResult.pages);
            }).inject(this.navi);


        this.renderTopActionBar(this.headerLayout.getColumn(1));
    },

    renderTopActionBar: function(pGroupContainer){

        if (this.classProperties.multiLanguage || this.classProperties.add || this.classProperties.remove || this.classProperties.custom) {
            this.win.extendHead();
        }

        if (this.classProperties.add || this.classProperties.remove || this.classProperties.custom ||
            (this.classProperties.asNested && (this.classProperties.nestedRootAdd))) {
            this.actionsNavi = new ka.ButtonGroup(pGroupContainer);
        }

        if (this.actionsNavi) {
            if (this.classProperties.remove) {
                this.actionsNavi.addButton(t('Remove selected'), ka.mediaPath(this.classProperties.removeIcon), function () {
                    this.removeSelected();
                }.bind(this));
            }

            if (this.classProperties.asNested && (this.classProperties.nestedRootAdd)){

                this.actionsNavi.addButton(this.options.nestedRootAddLabel || this.classProperties.nestedRootAddLabel, ka.mediaPath(this.classProperties.nestedRootAddIcon), function () {
                    this.addNestedRoot();
                }.bind(this));
            }


            if (this.classProperties.add) {

                this.actionsNavi.addButton(this.options.addLabel || this.classProperties.addLabel, ka.mediaPath(this.classProperties.addIcon), function () {

                    this.openAddItem();

                }.bind(this));
            }
        }

        /*
        TODO

        if (this.classProperties['export'] || this.classProperties['import']) {
            this.exportNavi = this.win.addButtonGroup();
        }

        if (this.exportNavi) {
            if (this.classProperties['export']) {
                this.exportType = new Element('select', {
                    style: 'position: relative; top: -2px;'
                })
                bject.each(this.classProperties['export'], function (fields, type) {
                    new Element('option', {
                        value: type,
                        html: t(type)
                    }).inject(this.exportType);
                }.bind(this));
                _
                this.exportNavi.addButton(this.exportType, '');
                this.exportNavi.addButton(_('Export'), _path + PATH_MEDIA + '/admin/images/icons/table_go.png', this.exportTable.bind(this));
            }

            if (this.classProperties['import']) {
                this.exportNavi.addButton(_('Import'), _path + PATH_MEDIA + '/admin/images/icons/table_row_insert.png');
            }


        }*/
    },

    removeSelected: function () {
        if (this.getSelected() != false) {
            this.win._confirm(_('Really remove selected?'), function (res) {
                if (!res)return;

                if (this.loader) {
                    this.loader.show();
                }

                new Request.JSON({url: _path + 'admin/' + this.win.getEntryPoint() + '?cmd=removeSelected', noCache: 1, onComplete: function (res) {

                    //todo, handle errors
                    if (this.loader) {
                        this.loader.hide();
                    }

                    if (this.combine) {
                        this.reload();
                    } else {
                        this.loadPage(this.currentPage);
                    }
                    this._deleteSuccess();

                }.bind(this)}).post({
                    selected: JSON.encode(this.getSelected())
                });
            }.bind(this));
        }
    },

    getSelected: function () {
        var res = [];
        this.checkboxes.each(function (check) {
            if (check.checked) {
                res.include(JSON.decode(check.value));
            }
        });
        return ( res.length > 0 ) ? res : false;
    },

    exportTable: function () {
        //TODO, order ..
        var params = new Hash({
            exportType: this.exportType.value,
            orderBy: this.sortField,
            filter: this.searchEnable,
            filterVals: (this.searchEnable) ? this.getSearchVals() : '',
            language: (this.languageSelect) ? this.languageSelect.getValue(): false,
            orderByDirection: this.sortDirection,
            params: JSON.encode(this.win.params)
        });
        if (this.lastExportForm) {
            this.lastExportForm.destroy();
            this.lastExportFrame.destroy();
        }
        this.lastExportForm = new Element('form', {
            action: _path + 'admin/' + this.win.getEntryPoint() + '?cmd=exportItems&' + params.toQueryString(),
            method: 'post',
            target: 'myExportFrame' + this.win.id
        }).inject(document.hiddenElement);
        this.lastExportFrame = new IFrame(null, {
            name: 'myExportFrame' + this.win.id
        }).inject(document.hiddenElement);
        this.lastExportForm.submit();
    },

    renderActions: function () {
        if (this.classProperties.add || this.classProperties.navi) { //wenn aktionen vorhanden, dann bar anzeigen
            this.actions = true;
        }
        if (this.actions) {
            this.table.setStyle('padding-bottom', 50);
            this.actionBar = new Element('div', {
                'class': 'ka-list-actionBar'
            }).inject(this.container);
        }
        if (this.classProperties.add) {
            this.actionAdd = new Element('a', {
                'class': 'ka-button',
                html: _('Add')
            }).inject(this.actionBar);
        }

    },

    addDummy: function () {
        var tr = new Element('tr').inject(this.tbody);
        var count = this.dummyCount + Object.getLength(this.classProperties.columns);
        new Element('td', {
            colspan: count,
            styles: {
                height: 'auto'
            }
        }).inject(tr);
    },


    prepareLoadPage: function () {
        if (this.tbody) {
            this.tbody.empty();
        }
    },

    loadPage: function (pPage) {

        if (this.lastResult && pPage != 1) {
            if (pPage > this.lastResult.pages) {
                return;
            }
        }

        if (pPage <= 0) {
            return;
        }

        if (this.lastRequest) {
            this.lastRequest.cancel();
        }

        this.prepareLoadPage();

        if (this.loader) {
            this.loader.show();
        }

        var params = {};

        /*
        if (this.options.relation_table && this.classProperties.relation) {
            var relationFields = this.classProperties.relation.fields;

            Object.each(relationFields, function (field_right, field_left) {

                if (this.options.relation_params[ field_left ]) {
                    params[field_right] = this.options.relation_params[ field_left ];
                }

            }.bind(this));
        }

        this.relation_params_filtered = params;
        */

        var req = {};
        this.ctrlPage.value = pPage;

        req.offset = (this.classProperties.itemsPerPage * pPage) - this.classProperties.itemsPerPage;
        req.lang = (this.languageSelect) ? this.languageSelect.getValue() : false;

        req.orderBy = {};
        req.orderBy[this.sortField] = this.sortDirection;

        if (this.searchEnable){
            var vals = this.getSearchVals();
            Object.each(vals, function(val,id){
                req['_'+id] = val;
            });
        }

        this.lastRequest = new Request.JSON({url: _path + 'admin/' + this.win.getEntryPoint(),
        noCache: true,
        onComplete: function (res) {
            this.currentPage = pPage;
            this.renderItems(res.data);
        }.bind(this)}).get(req);
    },

    renderItems: function (pResult) {
        var _this = this;

        this.checkboxes = [];

        if (this.loader) {
            this.loader.hide();
        }

        if (!pResult){
            pResult = {page: 0, pages:0};
        }

        this.lastResult = pResult;

        [this.ctrlFirst, this.ctrlPrevious, this.ctrlNext, this.ctrlLast].each(function (item) {
            item.setStyle('opacity', 1);
        });

        if (this.lastNoItemsDiv)
            this.lastNoItemsDiv.destroy();

        if (pResult.count == 0){
            this.ctrlPage.value = 0;
            this.lastNoItemsDiv = new Element('div', {
                style: 'position: absolute; left: 0; right: 0; top: 0; bottom: 0; background-color: #eee',
                styles: {
                    opacity: 0.6,
                    textAlign: 'center',
                    padding: 25
                },
                text: t('No entries, yet.')
            }).inject(this.main);
        }

        if (pResult.page <= 1) {
            this.ctrlFirst.setStyle('opacity', 0.2);
            this.ctrlPrevious.setStyle('opacity', 0.2);
        }

        if (pResult.page >= pResult.pages) {
            this.ctrlNext.setStyle('opacity', 0.2);
            this.ctrlLast.setStyle('opacity', 0.2);
        }

        this.ctrlMax.set('text', '/ ' + pResult.pages);

        _this.tempcount = 0;
        if (pResult.items) {
            Object.each(pResult.items, function (item) {
                _this.addItem(item);
                _this.tempcount++;
            });
        }
    },

    select: function (pItem) {
        var tr = pItem.getParent();

        if (this._lastSelect == tr) return;

        if (this._lastSelect) {
            this._lastSelect.set('class', this._lastSelect.retrieve('oldClass'));
        }

        tr.store('oldClass', tr.get('class'));
        tr.set('class', 'active');
        this._lastSelect = tr;
    },

    addItem: function (pItem) {
        var _this = this;
        var tr = new Element('tr', {
            'class': (_this.tempcount % 2) ? 'one' : 'two'
        }).inject(this.tbody);

        /*if (this.options.relation_table) {
            pItem.relation_table = this.options.relation_table;
            pItem.relation_params = this.options.relation_params_filtered;
        }*/

        if (this.classProperties.remove == true) {
            var td = new Element('td', {
                style: 'width: 21px;'
            }).inject(tr);
            if (pItem['remove']) {
                var mykey = {};
                this.classProperties.primary.each(function (primary) {
                    mykey[primary] = pItem.values[primary];
                });
                this.checkboxes.include(new Element('input', {
                    value: JSON.encode(mykey),
                    type: 'checkbox'
                }).inject(td));
            }
        }

        Object.each(this.classProperties.columns, function (column, columnId) {

            var value = ka.getObjectFieldLabel(pItem.values, column, columnId, this.options.object);

            var td = new Element('td', {
                html: value
            }).addEvent('click',function (e) {
                _this.select(this);
            }).addEvent('mousedown',function (e) {
                e.stop();
            }).addEvent('dblclick', function (e) {

                if (_this.classProperties.edit){

                    ka.entrypoint.open(ka.entrypoint.getRelative(_this.win.getEntryPoint(), _this.classProperties.editEntrypoint), {
                        item: pItem.values
                    }, this);

                }

            }).inject(tr);

            if (column.width > 0) {
                td.setStyle('width', column.width + 'px');
            }
        }.bind(this));

        if (this.classProperties.remove == true || this.classProperties.edit == true || this.classProperties.itemActions) {
            var icon = new Element('td', {
                width: 40,
                'class': 'edit'
            }).inject(tr);

            if (this.classProperties.itemActions && this.classProperties.itemActions.each) {
                this.classProperties.itemActions.each(function (action) {

                    var action = null;
                    if (typeOf(action) == 'array') {


                        if (action[1].substr(0,1) == '#'){

                            action = new Element('div', {
                                style: 'cursor: pointer; display: inline-block; padding: 0px 1px;',
                                'class': action[1].substr(1),
                                title: action[0]
                            }).inject(icon);

                        } else {
                            action = new Element('img', {
                                src: ka.mediaPath(action[1]),
                                title: action[0]
                            }).inject(icon);
                        }

                        //compatibility
                        action.addEvent('click',function () {
                            ka.wm.open(action[2], {item: pItem.values, filter: action[3]});
                        }).inject(icon);
                    }

                    if (typeOf(action) == 'object') {

                        if (action.icon.substr(0,1) == '#'){

                            action = new Element('div', {
                                style: 'cursor: pointer; display: inline-block; padding: 0px 1px;',
                                'class': action.icon.substr(1),
                                title: action.label
                            }).inject(icon);

                        } else {
                            action = new Element('img', {
                                src: ka.mediaPath(action.icon),
                                title: action.label
                            }).inject(icon);
                        }

                        //compatibility
                        action.addEvent('click',function () {
                            ka.entrypoint.open(action.entrypoint, {item: pItem.values}, this);
                        }).inject(icon);
                    }


                });
                icon.setStyle('width', 40 + (20 * this.classProperties.itemActions.length));
                this.titleIconTd.setStyle('width', 40 + (20 * this.classProperties.itemActions.length));
            }

            if (pItem.edit) {
                var editIcon = null;

                if (_this.classProperties.editIcon.substr(0,1) == '#'){

                    editIcon = new Element('div', {
                        style: 'cursor: pointer; display: inline-block; padding: 0px 1px;',
                        'class': _this.classProperties.editIcon.substr(1)
                    }).inject(icon);

                } else {
                    editIcon = new Element('img', {
                        src: ka.mediaPath(_this.classProperties.editIcon)
                    }).inject(icon);
                }

                editIcon.addEvent('click', function () {

                    ka.entrypoint.open(ka.entrypoint.getRelative(_this.win.getEntryPoint(), _this.classProperties.editEntrypoint), {item: pItem.values}, this);

                }).inject(icon);
            }
            if (pItem['remove']) {

                var removeIcon = _this.classProperties.removeIconItem?_this.classProperties.removeIconItem:_this.classProperties.removeIcon;

                if (typeOf(removeIcon) == 'string'){
                    var deleteBtn = null;

                    if (removeIcon.substr(0,1) == '#'){

                        deleteBtn = new Element('div', {
                            style: 'cursor: pointer; display: inline-block; padding: 0px 1px;',
                            'class': removeIcon.substr(1)
                        }).inject(icon);

                    } else {
                        deleteBtn = new Element('img', {
                            src: ka.mediaPath(removeIcon)
                        }).inject(icon);
                    }

                    deleteBtn.addEvent('click', function () {
                        _this.win._confirm(t('Really delete?'), function (res) {
                            if (!res) return;
                            _this.deleteItem(pItem);
                        });
                    });
                }
            }
        }
    }

})
