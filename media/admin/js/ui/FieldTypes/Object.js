ka.FieldTypes.Object = new Class({
    
    Extends: ka.FieldAbstract,

    fieldTemplate: '{label}',

    options: {
        object: null,
        objects: null,
        withoutObjectWrapper: false,
        fieldTemplate: null
    },

    createLayout: function(){

        if (typeOf(this.options.object) == 'string') this.options.objects = [this.options.object];

        if (!this.options.objects) throw 'No objects given for object chooser.';

        var definition = ka.getObjectDefinition(this.options.objects[0]);

        if (definition.chooserFieldJavascriptClass){

            if (!window[definition.chooserFieldJavascriptClass]){
                throw 'Can no load custom object field class "'+definition.chooserFieldJavascriptClass+'" for object '+this.options.objects[0];
            }

            this.customObj = new window[definition.chooserFieldJavascriptClass](this.field, this.fieldInstance.fieldPanel, this);

            this.customObj.addEvent('change', function () {
                this.fireChange();
            }.bind(this));

            this.setValue = this.customObj.setValue.bind(this.customObj);
            this.getValue = this.customObj.getValue.bind(this.customObj);
            this.isOk = this.customObj.isEmpty.bind(this.customObj);
            this.highlight = this.customObj.highlight.bind(this.customObj);

        } else {

            if (this.options.objectRelation == 'nToM' || this.options.multi == 1){
                this.renderChooserMulti(this.options.objects);
            } else {
                this.renderChooserSingle(this.options.objects);
            }
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
                    id = ka.getObjectUrlId(this.options.object, id);

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

        var url = 'object://'+this.options.object+'/';
        var ids = [];

        Object.each(this.objectTableLoaderQueue, function(placeholders, id){
            ids.push(id);
        });
        url += ids.join(',');

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

                var fields = ka.getObjectDefinition(this.options.object).fields;

                Object.each(this.objectTableLoaderQueue, function(placeholders, id){

                    Object.each(placeholders, function(td, colId){

                        if (res.data[id]){
                            var value = ka.getObjectLabel(res.data[id], fields[colId], colId);
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

    renderChooserSingle: function(){

        var table = new Element('table', {
            style: 'width: 100%', cellpadding: 0, cellspacing: 0
        }).inject(this.fieldInstance.fieldPanel);

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

        if (this.options.input_width){
            this.input.setStyle('width', this.options.input_width);
        }

        var div = new Element('span').inject(this.fieldInstance.fieldPanel);

        var chooserParams = {
            onSelect: function (pUrl) {
                this.setValue(pUrl, true);
            }.bind(this),
            value: this._value,
            cookie: this.options.cookie,
            objects: this.options.objects,
            options: this.options.chooserOptions
        };

        if (this._value)
            chooserParams.value = this._value;

        if (this.options.cookie)
            chooserParams.cookie = this.options.cookie;

        if (this.options.domain)
            chooserParams.domain = this.options.domain;


        var button = new ka.Button(t('Choose')).addEvent('click', function () {

            if (this.options.designMode) return;
            ka.wm.openWindow('admin', 'backend/chooser', null, -1, chooserParams);

        }.bind(this))
        .inject(rightTd);

        this.setValue = function (pVal, pIntern) {

            if (typeOf(pVal) == 'null' || pVal === false || pVal === '' || !ka.getObjectId(pVal)) {
                this._value = '';
                this.input.value = '';
                this.input.title = '';
                return;
            }

            pVal = String.from(pVal);

            if ((typeOf(pVal) == 'string' && pVal.substr(0, 'object://'.length) != 'object://')){
                pVal = 'object://'+this.options.objects[0]+'/'+ka.urlEncode(pVal);
            }
            this._value = pVal;

            this.objectGetLabel(this._value, function(pLabel){
                this.input.value = pLabel;
            });

            this.input.title = ka.getObjectId(pVal);

        };

        this.getValue = function () {
            var val = (this._value) ? this._value : this.input.value;

            if (this.options.withoutObjectWrapper && typeOf(val) == 'string' && val.substr(0, 'object://'.length) == 'object://'){
                return ka.getObjectId(val);
            }
            return val;
        };
    },

    renderChooserMulti: function(){

        this.renderChooserColumns = [];

        this.objectDefinition = ka.getObjectDefinition(this.options.objects[0]);

        if (this.objectDefinition.chooserUseOwnClass != 1){
            Object.each(this.objectDefinition.chooserFieldDataModelFields, function(field,key){
                this.renderChooserColumns.include([
                    field.label?field.label:key,
                    field.width?field.width:null
                ]);
            }.bind(this));
        }

        this.renderChooserColumns.include(["", 50]);

        this.chooserTable = new ka.Table(this.renderChooserColumns, {absolute: false, selectable: false});

        this.chooserTable.inject(this.fieldInstance.fieldPanel);
        this.renderObjectTableNoItems();

        //compatibility
        if (this.options.domain){
            if (!this.options.chooserOptions) this.options.chooserOptions = {};
            if (!this.options.chooserOptions.node) this.options.chooserOptions.node = {};
            this.options.chooserOptions.node.domain = this.options.domain;
        }


        this._value = [];

        var chooserParams = {
            onSelect: function (pId) {

                this._value.include(ka.getObjectId(pId));
                this.renderObjectTable();

            }.bind(this),
            value: this._value,
            cookie: this.options.cookie,
            objects: this.options.objects,
            chooserOptions: this.options.chooserOptions
        };

        if (this._value)
            chooserParams.value = this._value;

        if (this.options.cookie)
            chooserParams.cookie = this.options.cookie;

        if (this.options.domain)
            chooserParams.domain = this.options.domain;


        var button = new ka.Button(t('Add')).addEvent('click', function () {

            if (this.options.designMode) return;
            ka.wm.openWindow('admin', 'backend/chooser', null, -1, chooserParams);

        }.bind(this));

        button.inject(this.fieldInstance.fieldPanel);

        this.setValue = function(pVal){

            this._value = pVal;

            if (!this._value) this._value = [];

            if (typeOf(this._value) != 'array') this._value = [this._value];

            this.renderObjectTable();

        }.bind(this);

        this.getValue = function(){
            return this._value;
        };

    },


    objectGetLabel: function(pUrl, pCallback){

        if (this.lastPageChooserGetUrlRequest) {
            this.lastPageChooserGetUrlRequest.cancel();
        }

        this.lastPageChooserGetUrlRequest = new Request.JSON({url: _path + 'admin/backend/object', noCache: 1, onComplete: function(response){
            if (!response.error){

                if (response.data){
                    //var definition = ka.getObjectDefinition(res.object);
                    //var value = res.values[definition.chooserFieldDataModelField];
                    var data = response.data;

                    if (!this.options.fieldTemplate && !data.label){
                        Object.each(data, function(item){
                            if (!data.label) data.label = item;
                        });
                        if (!data.label) data.label = '';
                    }

                    var value = mowla.fetch(this.options.fieldTemplate?this.options.fieldTemplate:this.fieldTemplate, data);

                    this._automaticUrl = value;
                    this.input.value = value;
                } else {
                    this.input.value = '';
                    this._automaticUrl = '';
                }
            } else {
                this.input.value = response.error;
            }
            this.input.fireEvent('blur');
        }.bind(this)}).get({uri: pUrl});

    }

});