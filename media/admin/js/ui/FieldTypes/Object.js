ka.FieldTypes.Object = new Class({
    
    Extends: ka.FieldAbstract,
    options: {
        object: null,
        objects: null,
        withoutObjectWrapper: false,
        combobox: false
    },

    createLayout: function(){

        if (typeOf(this.options.object) == 'string') this.options.objects = [this.options.object];

        if (!this.options.objects) throw 'No objects given for object chooser.';

        var definition = ka.getObjectDefinition(this.options.objects[0]);

        if (!definition){
            this.fieldInstance.fieldPanel.set('text', t('Object not found %s').replace('%s', this.options.objects[0]));
            throw 'Object not found '+this.options.objects[0];
        }

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

        //TODO, do it w/o chooserFieldDataModelFields, use instead labelTemplate

        this.chooserTable.empty();

        this.objectTableLoaderQueue = {};

        if (!this.objectId || this.objectId.length == 0){
            this.renderObjectTableNoItems();
        } else {
            Array.each(this.objectId, function(id){

                var row = [];

                var placeHolder = new Element('span');
                row.include(placeHolder);

                if (typeOf(id) == 'object')
                    id = ka.getObjectUrlId(this.options.object, id);

                ka.getObjectLabel(ka.getObjectUrl(this.options.object, id), function(label){
                    placeHolder.set('html', label);
                });
                //this.renderObjectTableLoadItem(id, placeHolder);

                var actionBar = new Element('div');

                var remoteIcon = new Element('a', {
                    'class': 'text-button-icon',
                    href: 'javascript:;',
                    title: t('Remove'),
                    html: '&#xe04a;'
                }).inject(actionBar);

                row.include(actionBar);

                var tr = this.chooserTable.addRow(row);
                remoteIcon.addEvent('click', function(){
                    tr.destroy();
                    this.updateThisValue();
                }.bind(this));

                tr.kaFieldObjectId = id;

            }.bind(this));
        }
    },

    updateThisValue: function(){

        var rows = this.chooserTable.getRows();

        this.objectId = [];
        Array.each(rows, function(row){
            this.objectId.push(row.kaFieldObjectId);
        }.bind(this));

    },

    /*renderObjectTableLoadItem: function(pId, pPlaceHolders){

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
                //chooserFieldDataModelFieldTemplate

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

    */

    renderChooserSingle: function(){

        var table = new Element('table', {
            style: 'width: 100%', cellpadding: 0, cellspacing: 0
        }).inject(this.fieldInstance.fieldPanel);

        var tbody = new Element('tbody').inject(table);

        var tr = new Element('tr').inject(tbody);
        var leftTd = new Element('td').inject(tr);
        var rightTd = new Element('td', {width: '50px'}).inject(tr);

        this.field = new ka.Field({
            noWrapper: true
        }, leftTd)

        this.input = this.field.getFieldObject().input;
        this.input.addClass('ka-Input-disabled');
        this.input.disabled = true;

        if (this.options.combobox){
            this.input.disabled = false;
            this.input.addEvent('focus', function(){
                this.input.removeClass('ka-Input-disabled');
                this._lastValue = this.input.value;

                if (this.objectId){
                    this.lastObjectLabel = this.input.value;
                    this.lastObjectId = this.objectId;
                }
            }.bind(this));

            this.input.addEvent('blur', function(){
                if (this.input.value == this.lastObjectLabel){
                    this.objectId = this.lastObjectId;
                    this.input.addClass('ka-Input-disabled');
                    return;
                }

                if (typeOf(this._lastValue) != 'null' && this.input.value != this._lastValue){
                    //changed it, so we delete this.objectValue since its now a custom value
                    delete this.objectId;
                    this.input.removeClass('ka-Input-disabled');
                } else if (this.objectId){
                    this.input.addClass('ka-Input-disabled');
                }
            }.bind(this));
        }

        if (this.options.inputWidth){
            this.input.setStyle('width', this.options.inputWidth);
        }

        var div = new Element('span').inject(this.fieldInstance.fieldPanel);

        var chooserParams = {
            onSelect: function (pUrl) {
                this.setValue(pUrl, true);
            }.bind(this),
            value: this.objectId,
            cookie: this.options.cookie,
            objects: this.options.objects,
            chooserOptions: this.options.chooserOptions
        };

        if (this.objectId)
            chooserParams.value = this.objectId;

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
                this.objectId = '';
                this.input.value = '';
                this.input.title = '';
                return;
            }

            pVal = String.from(pVal);

            if ((typeOf(pVal) == 'string' && pVal.substr(0, 'object://'.length) != 'object://')){
                pVal = 'object://'+this.options.objects[0]+'/'+ka.urlEncode(pVal);
            }
            this.objectId = pVal;

            this.showLabel(this.objectId);

            this.input.title = ka.getObjectId(pVal);

        };

        this.getValue = function(){
            if (!this.objectId) return this.input.value;

            var val = this.objectId;

            if (this.options.withoutObjectWrapper && typeOf(val) == 'string' && val.substr(0, 'object://'.length) == 'object://'){
                return ka.getObjectId(val);
            }
            return val;
        }

    },

    renderChooserMulti: function(){

        this.renderChooserColumns = [];

        this.objectDefinition = ka.getObjectDefinition(this.options.objects[0]);

        this.renderChooserColumns.include([""]);
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

        var chooserParams = {
            onSelect: function (pId) {

                if (!this.objectId) this.objectId = [];

                var id = ka.getObjectId(pId);

                this.objectId.include(ka.getObjectId(pId));
                this.renderObjectTable();

            }.bind(this),
            value: this.objectId,
            cookie: this.options.cookie,
            objects: this.options.objects,
            chooserOptions: this.options.chooserOptions
        };

        if (this.objectId)
            chooserParams.value = this.objectId;

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

            this.objectId = pVal;

            if (!this.objectId) this.objectId = [];

            if (typeOf(this.objectId) != 'array') this.objectId = [this.objectId];

            this.renderObjectTable();

        }.bind(this);

        this.getValue = function(){
            return this.objectId;
        };

    },

    showLabel: function(pObjectUri){

        ka.getObjectLabel(pObjectUri, function(pLabel){
            if (pLabel === false){
                if (!this.options.combobox) {
                    this.input.value = 'not found: '+pObjectUri;
                    this.input.removeClass('ka-Input-disabled');
                    delete this.objectId;
                } else {
                    this.input.value = ka.urlDecode(ka.getCroppedObjectId(pObjectUri));
                }
            } else {
                this.input.value = pLabel;
                this.input.addClass('ka-Input-disabled');
            }

        }.bind(this));

        /*
        //TODO overhaul

        if (this.lastPageChooserGetUrlRequest) {
            this.lastPageChooserGetUrlRequest.cancel();
        }

        var objectKey = ka.getObjectKey(pObjectUri)
        var definition = ka.getObjectDefinition(objectKey);

        var fields = definition.labelField;
        if (definition.chooserFieldDataModelFields)
            fields = definition.chooserFieldDataModelFields;

        this.lastPageChooserGetUrlRequest = new Request.JSON({url: _path + 'admin/backend/object', noCache: 1, onComplete: function(response){
            if (!response.error){

                if (response.data){
                    //var definition = ka.getObjectDefinition(res.object);
                    //var value = res.values[definition.chooserFieldDataModelField];
                    var data = response.data;

                    var label = data[definition.labelField];

                    if (!this.options.fieldTemplate && !data.label && !label){
                        Object.each(data, function(item){
                            if (!data.label) data.label = item;
                        });
                        if (!data.label) data.label = '';
                    } else {
                        data.label = label;
                    }

                    var value = mowla.fetch(this.options.fieldTemplate?this.options.fieldTemplate:this.fieldTemplate, data);

                    this.input.value = value;
                    this.input.addClass('ka-Input-disabled');
                } else if (!this.options.combobox) {
                    this.input.value = 'not found: '+pObjectUri;
                    this.input.removeClass('ka-Input-disabled');
                    delete this.objectId;
                } else {
                    this.input.value = ka.getCroppedObjectId(pObjectUri);
                }
            } else if (!this.options.combobox) {
                this.input.value = response.error;
            }
            this.input.fireEvent('blur');
        }.bind(this)}).get({uri: pObjectUri, fields: fields});
        */

    }

});