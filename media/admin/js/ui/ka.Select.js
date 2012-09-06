ka.Select = new Class({
    Implements: [Events, Options],

    Binds: ['addItemToChooser', 'checkScroll', 'search'],

    opened: false,
    value: null,

    /**
     * Items if we have fixed items.
     * @type {Object}
     */
    items: [],

    /**
     * Items which should not be visible.
     * @type {Object}
     */
    hideItems: {},

    /**
     * Items that are currently visible in the chooser.
     * @type {Object}
     */
    currentItems: [],

    a: {},
    enabled: true,

    cachedObjectItems: {},

    objectFields: [],
    loaded: 0,
    maximumItemsReached: false,
    whileFetching: false,
    loaderId: 0,
    backupedTitle: false,

    labelTemplate:
        '{if kaSelectImage}'+
            '{var isVectorIcon = kaSelectImage.substr(0,1) == "#"} '+
            '{if kaSelectImage && isVectorIcon}<span class="{kaSelectImage.substr(1)}">{/if}'+
            '{if kaSelectImage && !isVectorIcon}<img src="{kaSelectImage}" />{/if}'+
        '{/if}'+
        '{label}'+
        '{if kaSelectImage && isVectorIcon}</span>{/if}',

    options: {

        items: false, //array or object
        store: false, //string
        object: false, //for object chooser
        objectLabel: false, //string
        objectFields: false, //or a array
        labelTemplate: false,
        customValue: false //boolean

    },

    initialize: function (pContainer, pOptions) {

        this.setOptions(pOptions);

        this.box = new Element('div', {
            'class': 'ka-normalize ka-Select-box ka-Select-box-active'
        }).addEvent('click', this.toggle.bind(this));

        this.box.instance = this;

        this.title = new Element('div', {
            'class': 'ka-Select-box-title'
        })
        .addEvent('mousedown', function (e) {
            e.preventDefault();
        })
        .inject(this.box);

        this.arrowBox = new Element('div', {
            'class': 'ka-Select-arrow icon-arrow-17'
        }).inject(this.box);

        this.chooser = new Element('div', {
            'class': 'ka-Select-chooser ka-normalize'
        });

        if (!ka.mobile){
            this.input = new Element('input', {
                style: 'height: 1px; position: absolute; top: -10px;'
            }).inject(this.box);
            this.input.addEvent('keyup', this.search);
        }

        this.chooser.addEvent('click', function (e) {
            if (!e || !(item = e.target)) return;
            if (!item.hasClass('ka-select-chooser-item') && !(item = item.getParent('.ka-select-chooser-item'))) return;

            this.setValue(item.kaSelectId, true);
            this.close();
        }.bind(this));

        this.chooser.addEvent('scroll', this.checkScroll);

        if (pContainer)
            this.box.inject(pContainer);


        if (this.options.items){
            if (typeOf(this.options.items) == 'object'){
                Object.each(this.options.items, function(label, key){
                    this.items.push({key: key, label: label});
                }.bind(this));
            }

            if (typeOf(this.options.items) == 'array'){
                Array.each(this.options.items, function(label){
                    this.items.push({key: label, label: label});
                }.bind(this));
            }

        } else if (this.options.object){
            //this.loadObjectItems();
            
            var fields = [];
            if (this.options.objectFields)
                fields = this.options.objectFields;
            else if (this.options.objectLabel)
                fields.push(this.options.objectLabel);
            else {
                var definition = ka.getObjectDefinition(this.options.object);
                fields.push(definition.objectLabel);
            }
            if (typeOf(fields) == 'string'){
                fields = fields.split(',').replace(/[^a-zA-Z0-9_]/g, '');
            }
            this.objectFields = fields;
        }
        
        this.fireEvent('ready');

    },

    loadObjectItems: function(pOffset, pCallback){

        if (this.lastRq) this.lastRq.cancel();

        this.lastRq = new Request.JSON({url: _path+'admin/backend/object/'+this.options.object,
            noErrorReporting: ['NoAccessException'],
            onComplete: function(response){

                if (response.error){
                    //handle error
                    //todo
                    return false;
                } else {
                    
                    var items = [];

                    Array.each(response.data, function(item){

                        var id = ka.getObjectUrlId(this.options.object, item);
                        items.push({
                            key: id,
                            label: item
                        });
                        this.cachedObjectItems[id] = item;

                    }.bind(this));

                    pCallback(items);
                }
            }.bind(this)
        }).get({
            object: this.options.object,
            offset: pOffset,
            limit: 20,
            fields: this.objectFields.join(',')
        });

    },

    reset: function(){
        this.chooser.empty();
        this.maximumItemsReached = false;
        this.loaded = 0;
        this.currentItems = {};
    },

    checkScroll: function(){

        if (this.maximumItemsReached) return;
        if (this.whileFetching) return;

        var scrollPos = this.chooser.getScroll();
        var scrollMax = this.chooser.getScrollSize();
        var maxY = scrollMax.y - this.chooser.getSize().y;

        if (scrollPos.y+10 < maxY) return;

        this.rerenderChooser();

    },

    search: function(pEvent){

        if (pEvent.key == 'esc'){
            this.input.value = '';
            this.close();
            return;
        }

        if (pEvent.key == 'enter' || pEvent.key == 'down' || pEvent.key == 'up'){
            //todo, do action
            this.input.value = '';
            
            return;
        }

        this.reset();
        this.rerenderChooser();

        if (this.lastDeleteQuery) clearTimeout(this.lastDeleteQuery);
        this.lastDeleteQuery = this.deleteQuery.delay(1000, this);

    },

    deleteQuery: function(){
        this.input.value = '';
    },

    rerenderChooser: function(){

        if (!this.box.hasClass('ka-Select-box-open')) return false;

        //this.chooser.empty();
        if (this.maximumItemsReached) return false;

        if (this.whileFetching) return false;

        this.whileFetching = true;

        //show small loader
        //
        if (this.input && this.input.value){
            
            if (!this.title.inSearchMode)
                this.backupedTitle = this.title.get('html');
            
            this.title.set('text', this.input.value);
            this.title.setStyle('color', 'gray');
            this.title.inSearchMode = true;

        } else if(this.backupedTitle !== false) {
            this.title.set('html', this.backupedTitle);
            this.title.setStyle('color');
            this.backupedTitle = false;
            this.title.inSearchMode = false;
        }

        this.lastLoader = new Element('a', {
            'text': t('Still loading ...'),
            style: 'display: none;'
        }).inject(this.chooser);

        this.lastLoader.loaderId = this.loaderId++;

        var loaderId = this.lastLoader.loaderId;

        (function(){
            if (this.lastLoader && this.lastLoader.loaderId == loaderId){
                this.lastLoader.setStyle('display', 'block');
            }
        }).delay(1000, this);

        var items = this.dataProxy(this.loaded, function(pItems){

            if (typeOf(pItems) == 'array'){

                Array.each(pItems, this.addItemToChooser);

                this.checkChooserSize();
            }

            this.loaded += pItems.length;
            if (!pItems.length)//no items left
                this.maximumItemsReached = true;


            this.lastLoader.destroy();
            delete this.lastLoader;

            this.whileFetching = false;
            this.checkScroll();
            
        }.bind(this));

    },

    addItemToChooser: function(pItem){
        
        var a;

        if (pItem.isSplit){
            a = new Element('div', {
                html: pItem.label,
                'class': 'group'
            }).inject(this.chooser);

        } else {
            a = new Element('a', {
                'class': 'ka-select-chooser-item',
                html: this.renderLabel(pItem.label)
            });

            if (this.input && this.input.value){

                var regex = new RegExp('('+ka.pregQuote(this.input.value)+')', 'gi');
                var match = a.get('text').match(regex);
                if (match){
                    a.set('html', a.get('html').replace(regex, '<b>$1</b>'));
                } else {
                    a.destroy();
                    return false;
                }
            }

            a.inject(this.chooser);

            // new Element('div', {
            //     html: pLabel,
            //     'class': 'group'
            // }).inject(this.chooser);

            if (pItem.key == this.value){
                a.addClass('icon-checkmark-6');
                a.addClass('ka-select-chooser-item-selected');
            }



            a.kaSelectId = pItem.key;
            a.kaSelectItem = pItem;
            this.currentItems[pItem.key] = a;
        }

    },

    renderLabel: function(pData){

        var data = pData;

        if (typeOf(data) == 'string')
            data = {label: data};
        else if (typeOf(data) == 'array'){
            //image
            data = {label: data[0], kaSelectImage: data[1]};
        }

        if (!data.kaSelectImage) data.kaSelectImage = '';

        var template = this.labelTemplate;

        if (typeOf(this.options.labelTemplate) == 'string'){
            template = this.options.labelTemplate;
        }

        if (template == this.labelTemplate && this.options.object && this.objectFields.length > 0){
            //we have no custom layout, but objectFields
            var label = [];
            Array.each(this.objectFields, function(field){
                label.push(pData[field]);
            });
            data.label = label.join(', ');
        }

        return mowla.fetch(template, data);
    },

    //returns always max 20
    dataProxy: function(pOffset, pCallback){

        var items = [];

        if (this.items.length > 0){
            //we have static items
            var i = pOffset;
            while (i++ >= 0){

                if (i >= this.items.length) break;
                if (items.length == 20) break;

                items.push(this.items[i]);
            }

            pCallback(items);
        } else if (this.options.object){
            //we have object items
            this.loadObjectItems(pOffset, pCallback);
        }

    },

    setEnabled: function(pEnabled){

        this.enabled = pEnabled;
        this.arrowBox.setStyle('opacity', pEnabled?1:0.4);

        if (this.enabled) this.box.addClass('ka-Select-box-active');
        else this.box.removeClass('ka-Select-box-active');

        this.title.setStyle('opacity', pEnabled?1:0.4);

    },

    inject: function (p, p2) {
        this.box.inject(p, p2);

        return this;
    },

    destroy: function () {
        this.chooser.destroy();
        this.box.destroy();
        this.chooser = null;
        this.box = null;
    },

    remove: function(pId){
        if (typeOf(this.items[ pId ]) == 'null') return;

        this.hideOption(pId);
        delete this.items[pId];
        delete this.a[pId];

    },

    hideOption: function(pId){

        hideItems[pId] = true;

        this.rerenderChooser();

        // if (typeOf(this.items[ pId ]) == 'null') return;

        // this.a[pId].setStyle('display', 'none');

        // if (this.value == pId){

        //     var found = false, before, first;
        //     Object.each(this.items,function(label, id){
        //         if (found) return;
        //         if (!first) first = id;
        //         if (before && id == pId){
        //             found = true;
        //             return;
        //         }

        //         before = id;
        //     }.bind(this));

        //     if (found){
        //         this.setValue(before);
        //     } else {
        //         this.setValue(first);
        //     }
        // }

    },

    showOption: function(pId){

        delete hideItems[pId];
        this.rerenderChooser();

        // if (typeOf(this.items[ pId ]) == 'null') return;

        // this.a[pId].setStyle('display');

    },

    addSplit: function (pLabel) {

        this.items.push({
            label: label,
            isSplit: true
        });

        this.rerenderChooser();

        // new Element('div', {
        //     html: pLabel,
        //     'class': 'group'
        // }).inject(this.chooser);
    },

    /**
     * Adds a item to the static list.
     *
     * @param {String} pId
     * @param {Mixed} pLabel String or array ['label', 'imageSrcOr#Class']
     * @param {[type]} pPos   Starts with 0
     * @param {[type]} pIcon
     */
    add: function (pId, pLabel, pPos) {

        if (typeOf(pLabel) == 'array'){
            pImagePath = pLabel[1];
            pLabel = pLabel[0];
        }

        if (pPos == 'top'){
            this.items.splice(0, 1, {key: pId, label: pLabel});
        } else if(pPos > 0){
            this.items.splice(pPos, 1, {key: pId, label: pLabel});
        } else {
            this.items.push({key: pId, label: pLabel});
        }

        return this.rerenderChooser();

    },

    setStyle: function (p, p2) {
        this.box.setStyle(p, p2);
        return this;
    },

    empty: function () {

        this.items = {};
        this.value = null;
        this.title.set('html', '');
        this.chooser.empty();

    },

    getLabel: function(pId, pCallback){

        var data;
        if (this.items.length > 0){

            //search for i
            for (var i = this.items.length-1; i >= 0; i--){
                if (pId == this.items[i].key){
                    data = this.items[i];
                    break;
                }
            }
            pCallback(data);
        } else if (this.options.object){
            //maybe in objectcache?
            if (this.cachedObjectItems[pId]){
                item = this.cachedObjectItems[pId];
                var id = ka.getObjectUrlId(this.options.object, item);
                pCallback({
                    key: id,
                    label: item
                });
            } else {
                //we need a request
                if (this.lastLabelRequest) this.lastLabelRequest.cancel();

                this.lastLabelRequest = new Request.JSON({
                    url: _path+'admin/backend/object/'+this.options.object+'/'+pId,
                    onComplete: function(response){

                        if (!response.error){

                            var id = ka.getObjectUrlId(this.options.object, response.data);
                            pCallback({
                                key: id,
                                label: response.data
                            });
                        }
                    }.bind(this)
                }).get({
                    fields: this.objectFields.join(',')
                });
            }
        }

    },

    setValue: function (pValue, pInternal) {

        this.value = pValue;
        this.getLabel(pValue, function(item){
            if (typeOf(item) != 'null')
                this.title.set('html', this.renderLabel(item.label));
            else
                this.title.set('text', t('-- not found --'));
        }.bind(this));

    },

    getValue: function () {
        return this.value;
    },

    toggle: function () {
        if (this.chooser.getParent()) {
            this.close();
        } else {
            this.open();
        }
    },

    close: function(){
        this.chooser.dispose();
        this.box.removeClass('ka-Select-box-open');
        this.reset();

        if(this.backupedTitle !== false) {
            this.title.set('html', this.backupedTitle);
            this.backupedTitle = false;
        }
        this.title.setStyle('color');
        this.title.inSearchMode = false;
    },

    open: function () {

        if (!this.enabled) return;

        if (this.box.getParent('.kwindow-win-titleGroups'))
            this.chooser.addClass('ka-Select-darker');
        else
            this.chooser.removeClass('ka-Select-darker');

        this.box.addClass('ka-Select-box-open');
        
        if (this.input)
            this.input.focus();

        this.rerenderChooser();


        return;

    },

    checkChooserSize: function(){

        ka.openDialog({
            element: this.chooser,
            target: this.box,
            onClose: this.close.bind(this),
            offset: {y: -1}
        });

        if (this.borderLine)
            this.borderLine.destroy();

        this.box.removeClass('ka-Select-withBorderLine');

        var csize = this.chooser.getSize();
        var bsize = this.box.getSize();

        if (bsize.x < csize.x){

            var diff = csize.x-bsize.x;

            this.borderLine = new Element('div', {
                'class': 'ka-Select-borderline',
                styles: {
                    width: diff
                }
            }).inject(this.chooser);

            this.box.addClass('ka-Select-withBorderLine');
        } else if (bsize.x - csize.x < 4 && bsize.x - csize.x >= 0){
            this.box.addClass('ka-Select-withBorderLine');
        }

        if (window.getSize().y < csize.y){
            this.chooser.setStyle('height', window.getSize().y);
        }

    },

    toElement: function () {
        return this.box;
    }

});
