ka.FieldTypes.Array = new Class({
    
    Extends: ka.FieldAbstract,

    createLayout: function(){

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


        if (this.options.withOrder){
            td.setStyle('width', 52);
        }

        
        if (!this.options.withoutAdd){
            new ka.Button(this.options.addText ? this.options.addText : [t('Add'), '#icon-plus-alt'])
            .addEvent('click', this.addRow.bind(this, [null])).inject(actions);
        }

        Object.each(this.options.fields, function(item, key){
            if (!this.first){
                this.first = key;
            } else if (!this.second){
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



    getValue: function(){

        var res = this.options.asHash?{}:[];

        var ok = true;

        this.tbody.getChildren('tr').each(function (tr) {
            if (ok == false) return;

            var row = this.options.asArray?[]:{};

            Object.each(tr.fields, function (field, field_key) {

                if (ok == false) return;

                if (!field.isOk()) {
                    ok = false;
                } else {

                    if (this.options.asArray){
                        if (this.fieldLength == 1)
                            row = field.getValue();
                        else
                            row.push(field.getValue());
                    } else
                        row[field_key] = field.getValue();
                }

            }.bind(this));

            if (this.options.asHash){

                if (this.fieldLength > 2){

                    var hash = {};
                    var i = -1;

                    Object.each(row, function(rvalue, rkey){
                        i++;
                        if (i == 0) return;
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

        if (ok == false) return;

        return res;
    },


    setValue: function (pValue) {
        this.tbody.empty();

        if (typeOf(pValue) == 'string') {
            pValue = JSON.decode(pValue);
        }

        if (this.options.asHash){

            if (this.fieldLength > 2){

                Object.each(pValue, function (item, idx) {

                    var val = {};
                    val[this.first] = idx;
                    Object.each(item, function(iV, iK){
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
                if (this.options.asArray){
                    if (this.fieldLength == 1){
                        var nItem = {};
                        nItem[this.first] = item;
                        this.addRow(nItem);
                    } else {

                        var nItem = {};
                        var index = 0;
                        Object.each(this.options.fields, function(def, key){
                            nItem[key] = item[indexx];
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

        if (this.options.designMode) return;

        var tr = new Element('tr').inject(this.tbody);
        tr.fields = {};

        Object.each(this.options.fields, function (field, field_key) {

            if (!field.panel_width) field.panel_width = '100%';

            var copy = Object.clone(field);
            field.noWrapper = 1;

            var td = new Element('td', {
                'class': 'ka-field'
            }).inject(tr);

            var nField = new ka.Field(field, td, {win: this.win});

            nField.addEvent('change', this.fieldInstance.fireChange);


            if (pValue && pValue[field_key]) {
                nField.setValue(pValue[field_key]);
            }

            tr.fields[field_key] = nField;

        }.bind(this));

        if (this.options.withOrder || !this.options.withoutRemove){
            var td = new Element('td').inject(tr);
        }

        if (this.options.withOrder){

            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
                title: t('Move up'),
                html: '&#xe2ca;'
            }).addEvent('click', function () {
                if(tr.getPrevious())
                    tr.inject(tr.getPrevious(), 'before');
            }).inject(td);


            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
                title: t('Move down'),
                html: '&#xe2cc;'
            }).addEvent('click', function () {
                if(tr.getNext())
                    tr.inject(tr.getNext(), 'after');
            }).inject(td);

        }

        if (!this.options.withoutRemove){
            new Element('a', {
                style: "cursor: pointer; font-family: 'icomoon'; padding: 0px 2px;",
                title: _('Remove'),
                html: '&#xe26b;'
            }).addEvent('click', function () {
                tr.destroy();
            }).inject(td);
        }


    }

});