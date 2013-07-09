ka.FieldTypes.Array = new Class({

    Extends: ka.FieldAbstract,

    options: {
        asHash: false,

        /**
         *
         * Structure
         *
         * [
         *    {label: 'Column 1', width: 50},
         *    {label: 'Column 2', width: '25%'},
         *    {label: 'Column 3'} //flexible width
         * ]
         *
         * @var {Array}
         */
        columns: [],

        /**
         * All ka.Field definitions in one big hash.
         *
         * Example:
         *
         * {
         *
         *    field1: {
         *        label: 'Field 1',
         *        type: 'text'
         *    },
         *
         *    field2: {
         *        label: 'Really ?',
         *        type: 'checkbox'
         *    },
         *
         *    field3: {
         *        label: 'With stuff?',
         *        type: 'checkbox',
         *        children: {
         *            field4: {
         *                label: t('A text'),
         *                type: 'text'
         *            },
         *            field5: {
         *                label: t('B text'),
         *                type: 'text'
         *            }
         *        }
         *    }
         *
         *
         * }
         *
         * @var {Object}
         */
        fields: {},

        /**
         * With how many items should we start?
         * @var {Number}
         */
        startWith: 0
    },

    createLayout: function () {

        var table = new Element('table', {
            cellpadding: 2,
            cellspacing: 0,
            width: '100%',
            'class': 'ka-field-array'
        }).inject(this.fieldInstance.fieldPanel);

        //this.fieldInstance.fireChange.setStyle('margin-left', 11);

        var thead = new Element('thead').inject(table);
        this.tbody = new Element('tbody').inject(table);

        var actions = new Element('div', {

        }).inject(this.fieldInstance.fieldPanel);

        var tr = new Element('tr').inject(thead);
        Array.each(this.options.columns, function (col) {

            var td = new Element('th', {
                valign: 'top',
                text: typeOf(col) == 'object' ? col.label : col
            }).inject(tr);

            if (typeOf(col) == 'object') {
                if (col.desc) {
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

        if (this.options.withOrder) {
            td.setStyle('width', 52);
        }

        if (!this.options.withoutAdd) {
            new ka.Button(this.options.addText ? this.options.addText : [t('Add'), '#icon-plus-alt'])
                .addEvent('click', this.addRow.bind(this, [null])).inject(actions);
        }

        Object.each(this.options.fields, function (item, key) {
            if (!this.first) {
                this.first = key;
            } else if (!this.second) {
                this.second = key;
            }
        }.bind(this));

        this.fieldLength = Object.getLength(this.options.fields);

        if (this.options.startWith && this.options.startWith > 0) {
            for (var i = 0; i < this.options.startWith; i++) {
                this.addRow();
            }
        }
    },

    isValid: function () {

        var valid = true;

        this.tbody.getChildren('tr').each(function (tr) {

            if (!valid) {
                return;
            }

            Object.each(tr.fields, function (field) {
                if (!valid) {
                    return;
                }
                if (!field.isValid()) {
                    valid = false;
                }
            });
        });

        return valid;
    },

    checkValid: function () {

        var valid = true;

        this.tbody.getChildren('tr').each(function (tr) {

            Object.each(tr.fields, function (field) {
                if (!field.checkValid()) {
                    valid = false;
                }
            });
        });

        return valid;
    },

    getValue: function () {

        var res = this.options.asHash ? {} : [];

        var ok = true;

        this.tbody.getChildren('tr').each(function (tr) {
            if (ok == false) {
                return;
            }

            var row = this.options.asArray ? [] : {};

            Object.each(tr.fields, function (field, fieldKey) {

                if (ok == false) {
                    return;
                }

                if (!field.isValid()) {
                    ok = false;
                } else {

                    if (this.options.asArray) {
                        if (this.fieldLength == 1) {
                            row = field.getValue();
                        }
                        else {
                            row.push(field.getValue());
                        }
                    } else {
                        row[fieldKey] = field.getValue();
                    }
                }

            }.bind(this));

            if (this.options.asHash) {

                if (this.fieldLength > 2) {

                    var hash = {};
                    var i = -1;

                    Object.each(row, function (rvalue, rkey) {
                        i++;
                        if (i == 0) {
                            return;
                        }
                        hash[rkey] = rvalue;

                    });

                    res[row[this.first]] = hash;
                } else {
                    res[row[this.first]] = row[this.second];
                }
            } else {
                res.push(row);
            }

        }.bind(this));

        if (ok == false) {
            return;
        }

        return res;
    },

    setValue: function (pValue) {
        this.tbody.empty();

        if (typeOf(pValue) == 'string') {
            pValue = JSON.decode(pValue);
        }

        if (this.options.asHash) {

            if (this.fieldLength > 2) {

                Object.each(pValue, function (item, idx) {

                    var val = {};
                    val[this.first] = idx;
                    Object.each(item, function (iV, iK) {
                        val[iK] = iV;
                    });
                    this.addRow(val);

                }.bind(this));

            } else {

                Object.each(pValue, function (item, idx) {

                    var val = {};
                    val[this.first] = idx;
                    val[this.second] = item;

                    this.addRow(val);
                }.bind(this));
            }
        } else {
            Array.each(pValue, function (item) {
                if (this.options.asArray) {
                    if (this.fieldLength == 1) {
                        var nItem = {};
                        nItem[this.first] = item;
                        this.addRow(nItem);
                    } else {

                        var nItem = {};
                        var index = 0;
                        Object.each(this.options.fields, function (def, key) {
                            nItem[key] = item[index];
                            index++;
                        });
                        this.addRow(nItem);
                    }
                } else {
                    this.addRow(item);
                }
            }.bind(this));
        }

    },

    addRow: function (pValue) {
        if (this.options.designMode) {
            return;
        }

        var tr = new Element('tr').inject(this.tbody);
        tr.fields = {};

        Object.each(this.options.fields, function (field, fieldKey) {
            var copy = Object.clone(field);
            copy.noWrapper = 1;

            var td = new Element('td', {
                'class': 'ka-field',
                valign: 'top'
            }).inject(tr);

            if (copy.children) {
                var parseFields = {};
                parseFields[fieldKey] = copy;
                var nField = new ka.FieldForm(td, parseFields, {allTableItems: true}, {win: this.win});
            } else {
                var nField = new ka.Field(copy, td, {win: this.win});
            }

            nField.addEvent('change', this.fieldInstance.fireChange);

            if ('null' !== typeOf(pValue[fieldKey])) {
                nField.setValue(pValue[fieldKey]);
            }

            tr.fields[fieldKey] = nField;
        }.bind(this));

        var td;
        if (this.options.withOrder || !this.options.withoutRemove) {
            td = new Element('td', {valign: 'top', width: 10, style: 'white-space: nowrap'}).inject(tr);
        }

        if (this.options.withOrder) {

            new Element('a', {
                'class': 'text-button-icon icon-arrow-up-14',
                title: t('Move up'),
                href: 'javascript: ;'
            }).addEvent('click',function () {
                if (tr.getPrevious()) {
                    tr.inject(tr.getPrevious(), 'before');
                }
            }).inject(td);

            new Element('a', {
                'class': 'text-button-icon icon-arrow-down-14',
                title: t('Move down'),
                href: 'javascript: ;'
            }).addEvent('click',function () {
                if (tr.getNext()) {
                    tr.inject(tr.getNext(), 'after');
                }
            }).inject(td);

        }

        if (!this.options.withoutRemove) {
            new Element('a', {
                'class': 'text-button-icon icon-minus-5',
                title: t('Remove'),
                href: 'javascript: ;'
            }).addEvent('click', function () {
                this.fieldInstance.fireChange();
                tr.destroy();
            }.bind(this)).inject(td);
        }

    }

});