ka.Select = new Class({
    Implements: [Events, Options],

    Binds: ['addItemToChooser', 'checkScroll', 'search', 'actions', 'focus', 'blur', 'fireChange'],

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
        maxItemsPerLoad: 20, //number
        selectFirst: true,
        customValue: false //boolean

    },

    initialize: function (pContainer, pOptions) {

        this.setOptions(pOptions);
        this.container = pContainer
        ;

        this.createLayout();
        this.mapEvents();
        this.prepareOptions();

        if (this.options.selectFirst)
            this.selectFirst();

        this.fireEvent('ready');

    },
    createLayout: function(){

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

        if (this.container)
            this.box.inject(this.container);
    },

    mapEvents: function(){
        if (!ka.mobile){
            this.input = new Element('input', {
                style: 'height: 1px; position: absolute; top: -10px;'
            }).inject(this.box);
            this.input.addEvent('keydown', this.actions);
            this.input.addEvent('keyup', this.search);
            this.input.addEvent('focus', this.focus);
            this.input.addEvent('blur', function(){
                this.blur.delay(50, this);
            }.bind(this));
        }

        this.chooser.addEvent('mousedown', function(){
            this.blockNextBlur = true;
        }.bind(this));

        this.chooser.addEvent('click', function (e) {
            if (!e || !(item = e.target)) return;
            if (!item.hasClass('ka-select-chooser-item') && !(item = item.getParent('.ka-select-chooser-item'))) return;

            this.setValue(item.kaSelectId, true);
            this.close(true);

        }.bind(this));

        this.chooser.addEvent('scroll', this.checkScroll);
    },


    prepareOptions: function(){

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
                fields = fields.replace(/[^a-zA-Z0-9_]/g, '').split(',');
            }
            this.objectFields = fields;

        }

    },

    focus: function(){
        this.box.addClass('ka-Select-box-focus');
    },

    blur: function(){
        if (this.blockNextBlur) return this.blockNextBlur = false;
        this.close();
    },

    loadObjectItems: function(pOffset, pCallback, pCount){

        if (!pCount)
            pCount = this.options.maxItemsPerLoad;

        if (this.lastRq) this.lastRq.cancel();

        this.lastRq = new Request.JSON({url: _path+'admin/backend/object/'+this.options.object,
            noErrorReporting: ['NoAccessException'],
            onCancel: function(){
                pCallback(false);
            },
            onComplete: function(response){

                if (response.error){
                    //todo, handle error
                    return false;
                } else {
                    
                    var items = [];

                    Array.each(response.data, function(item){

                        var id = ka.getObjectUrlId(this.options.object, item);

                        if (this.hideOptions && this.hideOptions.contains(id)) return;

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
            limit: pCount,
            fields: this.objectFields.join(',')
        });

    },

    reset: function(){
        this.chooser.empty();
        this.maximumItemsReached = false;

        this.loaded = 0;
        this.currentItems = {};

        if (this.lastRq) this.lastRq.cancel();

    },

    checkScroll: function(){

        if (this.maximumItemsReached) return;
        if (this.whileFetching) return;

        var scrollPos = this.chooser.getScroll();
        var scrollMax = this.chooser.getScrollSize();
        var maxY = scrollMax.y - this.chooser.getSize().y;

        if (scrollPos.y+10 < maxY) return;

        this.loadItems();

    },

    actions: function(pEvent){

        if (pEvent.key == 'esc'){
            this.input.value = '';
            this.close(true);
            return;
        }

        if (pEvent.key == 'enter' || pEvent.key == 'space' || pEvent.key == 'down' || pEvent.key == 'up'){
            var current = this.chooser.getElement('.ka-select-chooser-item-active');

            if (['down', 'up'].contains(pEvent.key)) pEvent.stop();


            if (pEvent.key == 'enter' || (this.input.value.trim() == '' && pEvent.key == 'space')){

                if (this.isOpen()){
                    this.close(true);
                    if (current){
                        this.setValue(current.kaSelectId, true);
                    }
                } else {
                    this.blockNextSearch = true;
                    this.open();
                }
                return;
            }

            if (pEvent.key == 'down'){
                if (!current){
                    var first = this.chooser.getElement('.ka-select-chooser-item');
                    if (first)
                        first.addClass('ka-select-chooser-item-active');
                } else {
                    current.removeClass('ka-select-chooser-item-active')
                    var next = current.getNext();
                    if (next){
                        next.addClass('ka-select-chooser-item-active');
                    } else {
                        var first = this.chooser.getElement('.ka-select-chooser-item');
                        if (first)
                            first.addClass('ka-select-chooser-item-active');
                    }
                }
            }

            if (pEvent.key == 'up'){
                if (!current){
                    var last = this.chooser.getLast('.ka-select-chooser-item');
                    if (last)
                        last.addClass('ka-select-chooser-item-active');
                } else {
                    current.removeClass('ka-select-chooser-item-active')
                    var previous = current.getPrevious();
                    if (previous){
                        previous.addClass('ka-select-chooser-item-active');
                    } else {
                        var last = this.chooser.getLast('.ka-select-chooser-item');
                        if (last)
                            last.addClass('ka-select-chooser-item-active');
                    }
                }
            }


            current = this.chooser.getElement('.ka-select-chooser-item-active');

            if (current){
                var position = current.getPosition(this.chooser);
                var height = +current.getSize().y;

                if (position.y+height > this.chooser.getSize().y){
                    this.chooser.scrollTo(this.chooser.getScroll().x, this.chooser.getScroll().y+(position.y-this.chooser.getSize().y)+height);
                }

                if (position.y < 0){
                    this.chooser.scrollTo(this.chooser.getScroll().x, this.chooser.getScroll().y+(position.y));
                }
            }

            return;
        }
    },

    search: function(pEvent){

        if (this.blockNextSearch) return this.blockNextSearch = false;

        if (['down', 'up', 'enter'].contains(pEvent.key)) return;

        if (this.input && this.input.value.trim() && !this.isOpen())
            this.open(true);


        this.reset();
        this.loadItems();

    },

    loadItems: function(){

        if (this.lrct) clearTimeout(this.lrct);

        this.lrct = this._loadItems.delay(50, this);

    },

    _loadItems: function(){

        //logger('renderChooser: '+(this.maximumItemsReached+0)+'/'+(this.whileFetching+0)+'/'+this.loaded);
        if (!this.box.hasClass('ka-Select-box-open')) return false;

        //this.chooser.empty();
        if (this.maximumItemsReached) return this.displayChooser();

        if (this.whileFetching) return false;

        this.whileFetching = true;

        //show small loader
        //
        if (this.input && this.input.value.trim()){
            
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

        this.lastLoaderGif = new Element('img')

        this.lastLoader.loaderId = this.loaderId++;

        var loaderId = this.lastLoader.loaderId;

        (function(){
            if (this.lastLoader && this.lastLoader.loaderId == loaderId){
                this.lastLoader.setStyle('display', 'block');
                this.displayChooser();
            }
        }).delay(1000, this);

        var items = this.dataProxy(this.loaded, function(pItems){

            if (typeOf(pItems) == 'array'){

                Array.each(pItems, this.addItemToChooser);

                this.loaded += pItems.length;

                if (!pItems.length)//no items left
                    this.maximumItemsReached = true;
            }

            this.displayChooser();

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

            if (this.input && this.input.value.trim()){

                var regex = new RegExp('('+ka.pregQuote(this.input.value.trim())+')', 'gi');
                var match = a.get('text').match(regex);
                if (match){
                    a.set('html', a.get('html').replace(regex, '<b>$1</b>'));
                } else {
                    a.destroy();
                    return false;
                }
            }

            a.inject(this.chooser);

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

        if (!data.kaSelectImage) data.kaSelectImage = '';

        return mowla.fetch(template, data);
    },

    selectFirst: function(){

        this.dataProxy(0, function(items){
            if (items){
                var item = items[0];
                if (item)
                    this.setValue(item.key, true);
            }
        }.bind(this), 1);

    },

    /**
     * Returns always max this.options.maxItemsPerLoad (20 default) items.
     *
     * @param {Integer}  pOffset
     * @param {Function} pCallback
     */
    dataProxy: function(pOffset, pCallback, pCount){

        if (!pCount) pCount = this.options.maxItemsPerLoad;

        if (this.items.length > 0){
            //we have static items
            var items = [];
            var i = pOffset-1;

            while (++i >= 0){

                if (i >= this.items.length) break;
                if (items.length == pCount) break;

                if (this.hideOptions && this.hideOptions.contains(this.items[i].key)) continue;

                items.push(this.items[i]);
            }

            pCallback(items);
        } else if (this.options.object){
            //we have object items
            this.loadObjectItems(pOffset, pCallback, pCount);
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

    addSplit: function (pLabel) {

        this.items.push({
            label: label,
            isSplit: true
        });

        this.loadItems();
    },

    showOption: function(pId){
        if (!this.hideOptions) this.hideOptions = [];
        this.hideOptions.push(pId);
    },

    hideOption: function(pId){
        if (!this.hideOptions) return;
        var idx = this.hideOptions.indexOf(pId);
        if (idx === -1) return;
        this.hideOptions.splice(idx, 1);
    },


    addImage: function (pId, pLabel, pImage, pPos) {
        return this.add(pId, [pLabel, pImage], pPos);
    },

    /**
     * Adds a item to the static list.
     *
     * @param {String} pId
     * @param {Mixed}  pLabel String or array ['label', 'imageSrcOr#Class']
     * @param {int}    pPos   Starts with 0
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

        if (typeOf(this.value) == 'null' && this.options.selectFirst){
            this.setValue(pId);
        }

        return this.loadItems();
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

                            if (response.data === false) return pCallback(false);

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

        if (typeOf(this.value) == 'null')
            return this.title.set('text', '');

        this.getLabel(pValue, function(item){
            if (typeOf(item) != 'null' && item !== false)
                this.title.set('html', this.renderLabel(item.label));
            else
                this.title.set('text', t('-- not found --'));
        }.bind(this));

        if (pInternal)
            this.fireChange();

    },

    setLabel: function(pId, pLabel){

        var i = 0, max = this.items.length;
        do {
            if (this.items[i].key == pId){
                this.items[i].label = pLabel;
                break;
            }
        } while (i++ && i < max);

        if (this.value == pId){
            this.title.set('html', pLabel);
            this.setValue(pId);
        }

    },

    fireChange: function(){
        this.fireEvent('change');
    },

    getValue: function () {
        return this.value;
    },

    toggle: function () {
        if (this.chooser.getParent()) {
            this.close(true);
        } else {
            this.open();
        }
    },

    close: function(pInternal){

        this.chooser.dispose();
        this.box.removeClass('ka-Select-box-open');
        this.reset();

        if(this.backupedTitle !== false) {
            this.title.set('html', this.backupedTitle);
            this.backupedTitle = false;
        }

        if (this.lastOverlay){
            this.lastOverlay.close();
            delete this.lastOverlay;
        }

        this.title.setStyle('color');
        this.title.inSearchMode = false;

        this.box.removeClass('ka-Select-box-focus');

        if (pInternal){

            if (this.input){
                this.input.focus();
            }
            
            this.box.addClass('ka-Select-box-focus');
        }
    },


    isOpen: function(){

        return this.box.hasClass('ka-Select-box-open');

    },

    open: function(pWithoutLoad){

        if (!this.enabled) return;

        if (this.box.getParent('.kwindow-win-titleGroups'))
            this.chooser.addClass('ka-Select-darker');
        else
            this.chooser.removeClass('ka-Select-darker');

        this.box.addClass('ka-Select-box-open');

        if (this.input && document.activeElement != this.input){
            this.input.value = '';
            this.input.focus();
        }

        if (this.lastRq) this.lastRq.cancel();

        this.box.addClass('ka-Select-box-focus');

        if (pWithoutLoad !== true)
            this.loadItems();

    },

    displayChooser: function(){


        if (!this.lastOverlay){
            this.lastOverlay = ka.openDialog({
                element: this.chooser,
                target: this.box,
                onClose:  function(){
                    this.close(true);
                }.bind(this),
                offset: {y: -1}
            });
        }

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
