ka.Table = new Class({

    Implements: [Options, Events],

    tableBody: false,
    safe: false,

    options: {
        absolute: true, //full size
        selectable: false,
        multi: false,
        alignTop: true,
        hover: true, //hover effect
        safe: true //htmlentities
    },

    /**
     * Constructor
     *
     * @param  array pColumns [ ["label", optionalWidth], ["label", optionalWidth], ... ]
     * @param  array pOptions
     */
    initialize: function (pColumns, pOptions) {
        this.setOptions(pOptions);

        if (this.options.absolute == false) {
            this.main = new Element('div', {
            });
        } else {
            this.main = new Element('div', {
                style: 'position: absolute; left: 0px; right: 0px; bottom: 0px; top: 0px;'
            });
        }

        if (this.options.hover) {
            this.main.addClass('ka-Table-hover');
        }

        if (this.options.alignTop) {
            this.main.addClass('ka-Table-alignTop');
        }

        if (this.options.selectable == true) {
            this.main.addEvent('click:relay(td)', function (e, item) {
                this.fireEvent('preSelect');

                if (!this.multi || (this.multi && (e.ctrl != true && e.meta != true))) {
                    item.getParent().getParent().getChildren('tr').removeClass('active');
                }
                item.getParent().addClass('active');
                e.stop();
                this.fireEvent('select', item);
            }.bind(this));

            this.main.addEvent('click:relay(th)', function (e, item) {
                e.stop();
                this.fireEvent('selectHead', item);
            }.bind(this));

            this.main.addEvent('click', function (e) {
                this.fireEvent('preDeselect');
                e.target.getElements('tr').removeClass('active');
                this.fireEvent('deselect');
            }.bind(this));
        }

        if (pColumns && typeOf(pColumns) == 'array') {
            this.setColumns(pColumns);
        }

    },

    deselect: function () {
        this.tableBody.getElements('tr').removeClass('active');
        this.tableBody.getElements('tr').removeClass('ka-table-body-item-active');
    },

    selected: function () {
        return this.tableBody.getElement('tr.active') || this.tableBody.getElement('tr.ka-table-body-item-active');
    },

    inject: function (pTo, pWhere) {
        this.main.inject(pTo, pWhere);
        return this;
    },

    hide: function () {
        this.main.setStyle('display', 'none');
    },

    show: function () {
        this.main.setStyle('display', 'block');
    },

    toElement: function () {
        return this.main;
    },

    /**
     * @param {Boolean} pActivate
     */
    loading: function (pActivate) {

        if (!pActivate && this.loadingOverlay) {

            if (this.tableBody) {
                this.tableBody.setStyle('opacity', 1);
            }

            this.loadingOverlay.destroy();

        } else if (pActivate) {

            if (this.tableBody) {
                this.tableBody.setStyle('opacity', 0.5);
            }

            if (this.loadingOverlay) {
                this.loadingOverlay.destroy();
            }

            this.loadingOverlay = new Element('div', {
                style: 'position: absolute; left: 0px; top: 21px; right: 0px; bottom: 0px; text-align: center;'
            }).inject(this.body);

            new Element('img', {
                src: _path + 'bundles/admin/images/ka-tooltip-loading.gif'
            }).inject(this.loadingOverlay);

            new Element('div', {
                html: _('Loading ...')
            }).inject(this.loadingOverlay);

            var size = this.loadingOverlay.getSize();
            this.loadingOverlay.setStyle('padding-top', (size.y / 2) - 50);
        }
    },

    /**
     *
     * @param {Integer} index Starting at 1
     */
    getColumn: function (index) {
        return this.tableHead.getChildren()[index - 1];
    },

    /**
     * Set the columns of the table.
     *
     * @param {Array} pColumns  [ ["label", optionalWidth], ["label", optionalWidth], ... ]
     */
    setColumns: function (pColumns) {
        this.columns = pColumns;

        if (!pColumns && typeOf(pColumns) != 'array') {
            return;
        }

        this.main.empty();

        if (this.options.absolute) {
            this.head = new Element('div', {
                'class': 'ka-Table-head-container'
            }).inject(this.main);

            if (this.options.absolute == false) {
                this.head.setStyle('position', 'relative');
            }

            this.tableHead = new Element('table', {
                'class': 'ka-Table-head',
                cellpadding: 0, cellspacing: 0
            }).inject(this.head, 'top');

            if (this.body) {
                this.body.destroy();
            }

            if (this.options.absolute == false) {
                this.body = new Element('div', {
                    style: 'position: relative;'
                }).inject(this.main);
            } else {
                this.body = new Element('div', {
                    'class': 'ka-Table-body-container'
                }).inject(this.main);
            }

            this.tableBody = new Element('table', {
                'class': 'ka-Table-body ka-Table-body-absolute',
                cellpadding: 0, cellspacing: 0
            }).inject(this.body, 'bottom');
        } else {
            this.table = new Element('table', {
                'class': 'ka-Table'
            }).inject(this.main);

            this.tableHead = new Element('thead').inject(this.table);
            this.tableBody = new Element('tbody').inject(this.table);

            if (this.options.hover) {
                this.table.addClass('ka-Table-hover');
            }

            if (this.options.alignTop) {
                this.table.addClass('ka-Table-alignTop');
            }
        }

        var tr = new Element('tr').inject(this.tableHead);
        pColumns.each(function (column, index) {

            var th = new Element('th', {
                html: ('element' !== typeOf(column[0]) ? column[0] : null),
                styles: {
                    width: (column[1]) ? column[1] : null
                }
            }).inject(tr);

            if ('element' === typeOf(column[0])) {
                column[0].inject(th);
            }

        }.bind(this));
    },

    getRows: function () {
        var children = this.tableBody.getChildren();
        if (typeOf(children) == 'array' && children[0].get('tag') == 'tbody') {
            return children[0].getChildren();
        } else {
            return children;
        }
    },

    addRow: function (pValues, pIndex) {
        if (!pIndex) {
            pIndex = this.tableBody.getElements('tr').length + 1;
        }

        var row = pValues;

        var tr = new Element('tr').inject(this.tableBody);
        tr.store('rowIndex', pIndex);

        var count = 0;
        this.columns.each(function (column, index) {
            var html = "";
            if ((typeOf(row[count]) == 'string' || typeOf(row[count]) == 'number') && !row[count].inject) {
                html = row[count];
            }

            var td = new Element('td', {
                width: (column[1]) ? column[1] : null
            }).inject(tr);

            if (this.safe || this.options.safe) {
                if (column[2] == 'html') {
                    td.set('html', html);
                } else {
                    td.set('text', html);
                }
            } else {
                td.set('html', html);
            }

            td.store('rowIndex', pIndex);

            if (row[count] && row[count].inject) {
                row[count].inject(td);
            }

            count++;

        }.bind(this));

        return tr;
    },

    empty: function () {

        this.tableBody.empty();

    },

    setValues: function (pValues) {

        this.tableBody.empty();

        if (typeOf(pValues) == 'array') {
            pValues.each(function (row) {
                this.addRow(row);
            }.bind(this));
        }
    }


});
