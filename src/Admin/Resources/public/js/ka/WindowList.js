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

        if (pContainer) {
            this.container = pContainer;
        }
        else {
            this.container = this.win.content;
        }

        this.container.empty();

        this.container.setStyle('overflow', 'hidden');
        this.page = 1;
        this.checkboxes = [];

        this.sortField = '';
        this.sortDirection = 'ASC';

        //this.oriWinCode = this.win.code;
        this.oriWinEntryPoint = this.win.entryPoint;

        this.load();
        var _this = this;

        this.fReload = this.softReload.bind(this);

        this.win.addEvent('softReload', this.fReload);

    },

    softReload: function (pCode) {
        if (this.win.closed) {
            return;
        }

        if (pCode == this.win.getEntryPoint()) {
            this.reload();
        }
    },

    reload: function () {
        this.loadPage(this.currentPage);
    },

    load: function () {
        var _this = this;

        this.container.set('html',
            '<div style="text-align: center; padding: 50px; color: silver">' + t('Loading definition ...') + '</div>');

        new Request.JSON({url: _pathAdmin + this.win.getEntryPoint(), noCache: true, onComplete: function (res) {
            if (res.error) {
                this.container.set('html', '<div style="text-align: center; padding: 50px; color: red">' +
                    tf('Failed. Error %s: %s', res.error, res.message) + '</div>');
                return false;
            }

            this.render(res.data);
            this.classLoaded = true;
            this.fireEvent('render');

        }.bind(this)}).get({_method: 'options'});
    },

    deleteItem: function (pItem) {
        var _this = this;
        this.lastRequest = new Request.JSON({url: _pathAdmin + this.win.getEntryPoint() +
            '?cmd=deleteItem', noCache: true, onComplete: function (res) {

            //todo, handle errors
            this.win.softReload();
            this._deleteSuccess();
            this.reload();
        }.bind(this)}).post({
                item: pItem
            });
    },

    _deleteSuccess: function () {
    },

    click: function (pColumn) {

        if (this.columns && this.columns[pColumn]) {

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
                src: _path + 'bundles/admin/images/icons/' + pic,
                align: 'top'
            }).inject(pItem);
        }

        this.loadPage(this.currentPage);
    },

    getSortField: function () {

        var field = null, direction = 'asc';

        if ((typeOf(this.classProperties.order) == 'array' && this.classProperties.order.length > 0) ||
            (typeOf(this.classProperties.order) == 'object' && Object.getLength(this.classProperties.order) > 0)) {

            if (typeOf(this.classProperties.order) == 'array') {
                Array.each(this.classProperties.order, function (order, f) {
                    if (!field) {
                        field = order.field;
                        direction = order.direction;
                    }
                });
            } else if (typeOf(this.classProperties.order) == 'object') {
                Object.each(this.classProperties.order, function (order, f) {
                    if (!field) {
                        field = f;
                        direction = order;
                    }
                });
            }
        } else {
            //just use first column
            if (this.classProperties.columns) {
                Object.each(this.classProperties.columns, function (col, id) {
                    if (field) {
                        return;
                    }
                    field = id;
                })
            }
        }

        return {
            field: field,
            direction: direction
        }
    },

    checkClassProperties: function () {

        if (!this.classProperties.columns || !Object.getLength(this.classProperties.columns)) {
            this.win.alert(t('This window class does not have columns defined.'));
            return false;
        }

        return true;
    },

    render: function (pValues) {
        this.classProperties = pValues;

        if (!this.checkClassProperties()) {
            return false;
        }

        var sort = this.getSortField();
        this.sortField = sort.field;
        this.sortDirection = sort.direction;

        this.container.empty();

        this.renderTopActionBar();
        this.renderMultilanguage();

        this.renderLayout();
        this.renderActionBar();

        this.renderLoader();

        this.renderFinished();
    },

    renderFinished: function () {
        if (this.options.noInitLoad == true) {
            return;
        }

        if (!this.loadAlreadyTriggeredBySearch) {
            if (this.columns) {
                var sort = this.getSortField();
                //logger('sort: '+sort.field);
                this.click(sort.field);
            } else {
                this.loadPage(1);
            }
        }

        this.container.tween('opacity', 1);
    },

    renderLoader: function () {
        this.loader = new ka.Loader(this.main);
    },

    renderMultilanguage: function () {

        if (this.classProperties.multiLanguage && !this.languageSelect) {

            this.languageSelect = new ka.Select(this.topActionBar);
            document.id(this.languageSelect).setStyle('width', 150);

            this.languageSelect.addEvent('change', this.changeLanguage.bind(this));

            var hasSessionLang = false;
            Object.each(ka.settings.langs, function (lang, id) {

                this.languageSelect.add(id, lang.langtitle + ' (' + lang.title + ', ' + id + ')');
                if (id == window._session.lang) {
                    hasSessionLang = true;
                }

            }.bind(this));

            if (hasSessionLang) {
                this.languageSelect.setValue(window._session.lang);
            }
        }
    },

    renderLayout: function () {
        this.container.setStyle('opacity', 0);
        this.renderLayoutTable();
        this.renderSearchPane();
    },

    renderLayoutNested: function (pContainer) {
        var objectOptions = {};

        pContainer.empty();

        objectOptions.type = 'tree';
        objectOptions.objectKey = this.classProperties.object;
        objectOptions.entryPoint = this.win.getEntryPoint();
        objectOptions.scopeChooser = false;
        objectOptions.noWrapper = true;

        if (this.languageSelect) {
            objectOptions.scopeLanguage = this.languageSelect.getValue();
        }

        this.nestedField = new ka.Field(objectOptions);

        this.nestedField.inject(pContainer, 'top');

        if (this.classProperties.edit) {
            this.nestedField.addEvent('select', this.nestedItemSelected.bind(this));
        }
    },

    nestedItemSelected: function () {

    },

    addNestedRoot: function () {

        //open
        ka.entrypoint.open(ka.entrypoint.getRelative(this.win.getEntryPoint(),
            this.classProperties.nestedRootAddEntrypoint), {
            lang: (this.languageSelect) ? this.languageSelect.getValue() : false
        }, this);

    },

    openAddItem: function () {
        ka.entrypoint.open(ka.entrypoint.getRelative(this.win.getEntryPoint(), this.classProperties.addEntrypoint), {
            lang: (this.languageSelect) ? this.languageSelect.getValue() : false
        }, this);

    },

    renderLayoutTable: function () {
        this.table = new ka.Table(null, {
            selectable: true,
            safe: false
        });

        this.win.addEvent('resize', this.table.updateTableHeader);

        document.id(this.table).addClass('ka-Table-windowList');

        this.table.inject(this.container);

        //[ ["label", optionalWidth], ["label", optionalWidth], ... ]
        var columns = [];

        var indexOffset = 0;
        if (this.classProperties.remove == true) {
            indexOffset = 1;
            var input = new Element('input', {
                value: 1,
                type: 'checkbox'
            }).addEvent('click', function () {
                    var checked = this.checked;
                    this.checkboxes.each(function (checkbox) {
                        checkbox.checked = checked;
                    });
                }.bind(this));
            columns.push([input, 21]);
        }

        this.columns = {};
        if (!this.classProperties.columns || this.classProperties.columns.length == 0) {
            this.win.alert(t('This class does not contain any columns.'), function () {
                this.win.close();
            }.bind(this));
            throw 'Class does not contain columns.';
        }

        var columnsIds = [];
        Object.each(this.classProperties.columns, function (column, columnId) {
            columns.push([t(column.label), column.width, column.align]);
            columnsIds.push(columnId);
        }.bind(this));

        /*** edit-Th ***/
        if (this.classProperties.remove == true || this.classProperties.edit == true ||
            this.classProperties.itemActions) {
            this.titleIconTdIndex = columns.length;
            columns.push(['', 40]);
        }

        this.table.setColumns(columns);

        this.table.addEvent('selectHead', function (item) {
            var index = item.getParent().getChildren().indexOf(item) - indexOffset;
            this.click(columnsIds[index]);
        }.bind(this));
        this.titleIconTd = this.table.getColumn(this.titleIconTdIndex);
    },

    changeLanguage: function () {
        if (this.classProperties.asNested) {
            //todo
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

                var fieldObj = new ka.Field(field, this.searchPane, {win: this.win}).addEvent('change',
                    this.doSearch.bind(this));

                this.searchFields.set(mkey, fieldObj);

                if (field.type == 'select') {
                    fieldObj.select.add('', _('-- Please choose --'), 'top');
                    fieldObj.setValue('');
                }

                if (this.win.params && this.win.params.filter) {
                    Object.each(this.win.params.filter, function (item, key) {
                        if (item == mkey) {
                            fieldObj.setValue(this.win.params.item[key]);
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
        if (this.searchEnabled == 1) {
            this.filterButton.set('class', 'ka-list-search-button');
            this.searchEnabled = 0;
            this.searchPane.tween('height', 1);
            this.main.tween('bottom', 30);
        } else {
            this.searchEnabled = 1;
            this.filterButton.set('class', 'ka-list-search-button ka-list-search-button-active');
            this.searchPane.tween('height', 121);
            this.main.tween('bottom', 120 + 30);
        }
    },

    renderActionBar: function () {
        var _this = this;

        this.actionBarNavigation = new Element('div', {
            'class': 'ka-list-actionBarNavigation'
        }).inject(this.container);

        this.navigateActionBar = new ka.ButtonGroup(this.actionBarNavigation, {
            onlyIcons: true
        });

        this.ctrlPrevious =
            this.navigateActionBar.addButton(t('Go to previous page'), '#icon-arrow-left-15', function () {
                this.loadPage(parseInt(_this.ctrlPage.value) - 1);
            }.bind(this));

        this.ctrlPage = new Element('input', {
            'class': 'ka-Input-text ka-list-actionBar-page'
        }).addEvent('keydown',function (e) {
                if (e.key == 'enter') {
                    _this.loadPage(parseInt(_this.ctrlPage.value));
                }
                if (['backspace', 'left', 'right'].indexOf(e.key) == -1 && (!parseInt(e.key) + 0 > 0)) {
                    e.stop();
                }
            }).inject(this.navigateActionBar);

        this.ctrlMax = new Element('div', {
            'class': 'ka-list-actionBar-page-count',
            text: '/ 0'
        }).inject(this.navigateActionBar);

        this.ctrlNext = this.navigateActionBar.addButton(t('Go to next page'), '#icon-arrow-right-15', function () {
            this.loadPage(parseInt(_this.ctrlPage.value) + 1);
        }.bind(this));
    },

    renderTopActionBar: function (container) {
        this.topActionBar = container || this.win.getTitleGroupContainer();

        this.actionsNavi = new ka.ButtonGroup(this.topActionBar);
        document.id(this.actionsNavi).addClass('ka-window-list-buttonGroup');

        if (this.classProperties.remove) {
            this.actionsNavi.addButton(t('Remove'), ka.mediaPath(this.classProperties.removeIcon), function () {
                this.removeSelected();
            }.bind(this));
        }

        if (this.classProperties.asNested && (this.classProperties.nestedRootAdd)) {

            this.addRootBtn = this.actionsNavi.addButton(this.options.nestedRootAddLabel ||
                this.classProperties.nestedRootAddLabel,
                ka.mediaPath(this.classProperties.nestedRootAddIcon), function () {
                    this.addNestedRoot();
                }.bind(this));
        }

        if (this.classProperties.add) {

            this.addBtn = this.actionsNavi.addButton(this.options.addLabel || this.classProperties.addLabel,
                ka.mediaPath(this.classProperties.addIcon), function () {
                    this.openAddItem();
                }.bind(this));
        }

        this.actionBarSearchBtn = this.actionsNavi.addButton(t('Search'), '#icon-search');

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
         this.exportNavi.addButton(_('Export'), _path + 'bundles/admin/images/icons/table_go.png', this.exportTable.bind(this));
         }

         if (this.classProperties['import']) {
         this.exportNavi.addButton(_('Import'), _path + 'bundles/admin/images/icons/table_row_insert.png');
         }


         }*/
    },

    removeSelected: function () {
        if (this.getSelected() != false) {
            this.win._confirm(_('Really remove selected?'), function (res) {
                if (!res) {
                    return;
                }

                if (this.loader) {
                    this.loader.show();
                }

                new Request.JSON({url: _pathAdmin + this.win.getEntryPoint() +
                    '?cmd=removeSelected', noCache: 1, onComplete: function (res) {

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
            filter: this.searchEnabled,
            filterVals: (this.searchEnabled) ? this.getSearchVals() : '',
            language: (this.languageSelect) ? this.languageSelect.getValue() : false,
            orderByDirection: this.sortDirection,
            params: JSON.encode(this.win.params)
        });
        if (this.lastExportForm) {
            this.lastExportForm.destroy();
            this.lastExportFrame.destroy();
        }
        this.lastExportForm = new Element('form', {
            action: _pathAdmin + this.win.getEntryPoint() + '?cmd=exportItems&' + params.toQueryString(),
            method: 'post',
            target: 'myExportFrame' + this.win.id
        }).inject(document.hiddenElement);
        this.lastExportFrame = new IFrame(null, {
            name: 'myExportFrame' + this.win.id
        }).inject(document.hiddenElement);
        this.lastExportForm.submit();
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
        if (this.table) {
            this.table.empty();
        }
    },

    loadItemCount: function (callback) {

        var req = {};

        if (this.searchEnabled) {
            var vals = this.getSearchVals();
            Object.each(vals, function (val, id) {
                req['_' + id] = val;
            });
        }

        this.lastRequest = new Request.JSON({url: _pathAdmin + this.win.getEntryPoint() + '/:count',
            noCache: true,
            onComplete: function (res) {
                if (!res || res.error) {
                    this.win.alert(tf('There was an error: %s: %s', res.error, res.message));
                } else {
                    this.itemsCount = res.data;
                    this.maxPages = Math.ceil(res.data / this.classProperties.itemsPerPage);

                    this.ctrlMax.set('text', '/ ' + this.maxPages);
                    if (callback) {
                        callback();
                    }
                }
            }.bind(this)}).get(req);

    },

    loadPage: function (pPage) {

        pPage = pPage || 1;
        if ('null' === typeOf(this.itemsCount)) {
            this.loadItemCount(function () {
                this.loadPage(pPage);
            }.bind(this));
            return;
        }

        if (this.lastResult && pPage != 1) {
            if (pPage > this.maxPages) {
                return;
            }
        }

        if (pPage <= 0) {
            return;
        }

        if (this.lastLoadPageRequest) {
            this.lastLoadPageRequest.cancel();
        }

        this.prepareLoadPage();

        if (this.loader) {
            this.loader.show();
        }

        var req = {};
        this.ctrlPage.value = pPage;

        req.offset = (this.classProperties.itemsPerPage * pPage) - this.classProperties.itemsPerPage;
        req.lang = (this.languageSelect) ? this.languageSelect.getValue() : null;

        req.orderBy = {};
        req.orderBy[this.sortField] = this.sortDirection;

        if (this.searchEnabled) {
            var vals = this.getSearchVals();
            Object.each(vals, function (val, id) {
                req['_' + id] = val;
            });
        }

        this.lastLoadPageRequest = new Request.JSON({url: _pathAdmin + this.win.getEntryPoint(),
            noCache: true,
            onComplete: function (res) {
                this.currentPage = pPage;

                this.renderItems(res.data);
            }.bind(this)}).get(req);
    },

    renderItems: function (pResult) {
        this.checkboxes = [];

        if (this.loader) {
            this.loader.hide();
        }

        this.lastResult = pResult;

        [this.ctrlPrevious, this.ctrlNext].each(function (item) {
            item.setStyle('opacity', 1);
        });

        if (this.lastNoItemsDiv) {
            this.lastNoItemsDiv.destroy();
        }

        if (1 === this.currentPage && 0 === pResult.length) {
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

        if (this.currentPage <= 1) {
            //this.ctrlFirst.setStyle('opacity', 0.2);
            this.ctrlPrevious.setStyle('opacity', 0.2);
        }

        if (this.currentPage >= this.maxPages) {
            this.ctrlNext.setStyle('opacity', 0.2);
            //this.ctrlLast.setStyle('opacity', 0.2);
        }

        this.table.empty();

        if (pResult) {
            Array.each(pResult, function (item) {
                this.addItem(item);
            }.bind(this));
        }
    },

    select: function (pItem) {
        var tr = pItem.getParent();

        if (this._lastSelect == tr) {
            return;
        }

        if (this._lastSelect) {
            this._lastSelect.removeClass('active');
        }

        tr.addClass('active');
        this._lastSelect = tr;
    },

    openEditItem: function (pItem) {
        ka.entrypoint.open(ka.entrypoint.getRelative(this.win.getEntryPoint(), this.classProperties.editEntrypoint), {
            item: pItem
        }, this.win.getId(), true, this.win.getId());
    },

    addItem: function (pItem) {
        var _this = this;

        var row = [];

        var pk = ka.getObjectUrlId(this.classProperties['object'], pItem);

        if (this.classProperties.remove == true) {

            var checkbox = new Element('input', {
                value: pk,
                type: 'checkbox'
            });
            this.checkboxes.include(checkbox);

            if (!pItem['remove']) {
                checkbox.disabled = true;
            }
            row.push(checkbox);
        }

        Object.each(this.classProperties.columns, function (column, columnId) {
            var value = ka.getObjectFieldLabel(pItem, column, columnId, this.classProperties['object']);
            row.push(value);
        }.bind(this));

        if (this.classProperties.remove == true || this.classProperties.edit == true ||
            this.classProperties.itemActions) {
            var icon = new Element('div', {
                'class': 'edit'
            });

            row.push(icon);

            if (this.classProperties.itemActions && this.classProperties.itemActions.each) {
                this.classProperties.itemActions.each(function (action) {

                    var action = null;
                    if (typeOf(action) == 'array') {

                        if (action[1].substr(0, 1) == '#') {

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
                            ka.wm.open(action[2], {item: pItem, filter: action[3]});
                        }).inject(icon);
                    }

                    if (typeOf(action) == 'object') {

                        if (action.icon.substr(0, 1) == '#') {

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
                            ka.entrypoint.open(action.entrypoint, {item: pItem}, this);
                        }).inject(icon);
                    }
                });
            }

            if (pItem.edit) {
                var editIcon = null;

                if (_this.classProperties.editIcon.substr(0, 1) == '#') {

                    editIcon = new Element('div', {
                        style: 'cursor: pointer; display: inline-block; padding: 0px 1px;',
                        'class': _this.classProperties.editIcon.substr(1)
                    }).inject(icon);

                } else {
                    editIcon = new Element('img', {
                        src: ka.mediaPath(_this.classProperties.editIcon)
                    }).inject(icon);
                }

                editIcon.addEvent('click',function () {

                    ka.entrypoint.open(ka.entrypoint.getRelative(_this.win.getEntryPoint(),
                        _this.classProperties.editEntrypoint), {item: pItem}, this);

                }).inject(icon);
            }
            if (pItem['remove']) {

                var removeIcon = _this.classProperties.removeIconItem ? _this.classProperties.removeIconItem :
                    _this.classProperties.removeIcon;

                if (typeOf(removeIcon) == 'string') {
                    var deleteBtn = null;

                    if (removeIcon.substr(0, 1) == '#') {

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
                            if (!res) {
                                return;
                            }
                            _this.deleteItem(pItem);
                        });
                    });
                }
            }
        }

        var tr = this.table.addRow(row);

        tr.addEvent('click:relay(td)', function (e, item) {
                this.select(item);
            }.bind(this))
            .addEvent('dblclick', function (e) {
                if (this.classProperties.edit) {
                    this.openEditItem(pItem);
                }
            }.bind(this));
    }
})
