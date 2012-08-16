ka.Select = new Class({
    Implements: [Events, Options],

    opened: false,
    value: null,

    items: {},
    a: {},
    enabled: true,

    options: {

        items: false, //array or object
        store: false, //string
        object: false, //for object chooser
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
            'class': 'ka-Select-arrow icon-triangle-2'
        }).inject(this.box);

        this.chooser = new Element('div', {
            'class': 'ka-Select-chooser ka-normalize'
        });

        this.chooser.addEvent('click', function (e) {
            if (!e || !(item = e.target)) return;
            if (!item.hasClass('ka-select-chooser-item') && !(item = item.getParent('.ka-select-chooser-item'))) return;

            this.setValue(item.kaSelectId, true);
            this.close();
        }.bind(this));

        if (pContainer)
            this.box.inject(pContainer)

        if (this.options.items){
            if (typeOf(this.options.items) == 'object'){
                Object.each(this.options.items, function(label, key){
                    this.add(key, label);
                }.bind(this))
            }

            if (typeOf(this.options.items) == 'array'){
                Array.each(this.options.items, function(label){
                    this.add(label, label);
                }.bind(this))
            }

            this.fireEvent('ready');
        } else if (this.options.object){

            this.loadObjectItems();

        }

    },

    loadObjectItems: function(){

        this.lastRq = new Request.JSON({url: _path+'admin/backend/objectGetItems',
            noErrorReporting: true,
            onComplete: function(res){

                if(!res){
                    this.add('0', t('No items.'));
                    this.setEnabled(false);
                    return;
                }

                if (res.error == 'no_object_access'){

                    this.add('0', t('No access to this object.'));
                    this.setEnabled(false);

                } else if (res.error == 'object_not_found'){

                    this.add('0', t('No access to this object.'));
                    this.setEnabled(false);

                } else {

                    Object.each(res, function(value, id){

                        this.add(id, Object.values(value).join(', '));

                    }.bind(this));

                    this.fireEvent('ready');
                }

            }.bind(this)
        }).get({
            object: this.options.object
        });

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

        if (typeOf(this.items[ pId ]) == 'null') return;

        this.a[pId].setStyle('display', 'none');

        if (this.value == pId){

            var found = false, before, first;
            Object.each(this.items,function(label, id){
                if (found) return;
                if (!first) first = id;
                if (before && id == pId){
                    found = true;
                    return;
                }

                before = id;
            }.bind(this));

            if (found){
                this.setValue(before);
            } else {
                this.setValue(first);
            }
        }

    },

    showOption: function(pId){

        if (typeOf(this.items[ pId ]) == 'null') return;

        this.a[pId].setStyle('display');

    },

    addSplit: function (pLabel) {
        new Element('div', {
            html: pLabel,
            'class': 'group'
        }).inject(this.chooser);
    },

    setText: function(pId, pLabel){

        if (typeOf(this.items[ pId ]) == 'null') return;

        this.items[ pId ] = pLabel;

        var img;
        if (this.a[pId].getElement('img')){
            img = this.a[pId].getElement('img');
            img.dispose();
        }

        this.a[pId].set('text', pLabel);

        if (img){
            img.inject(this.a[pId], 'top');
        }

        this.title.set('class', 'ka-Select-box-title '+this.a[pId].get('class').replace('ka-select-chooser-item', ''));


        if (this.value == pId){
            this.title.set('text', this.items[ pId ]);
            this.box.set('title', (this.items[ pId ] + "").stripTags());

            if (this.a[pId].getElement('img')){
                this.a[pId].getElement('img').clone().inject(this.title, 'top');
            }
        }
    },

    addImage: function (pId, pLabel, pIcon, pPos) {

        return this.add(pId, pLabel, pPos, pIcon);
    },

    add: function (pId, pLabel, pPos, pIcon) {

        if (typeOf(pLabel) == 'array'){
            pImagePath = pLabel[1];
            pLabel = pLabel[0];
        }

        if (typeOf(pLabel) != 'string')
            pLabel = pId;

        this.items[ pId ] = pLabel;


        this.a[pId] = new Element('a', {
            text: pLabel,
            'class': 'ka-select-chooser-item',
            href: 'javascript:;'
        });

        this.a[pId].kaSelectId = pId;

        if (pIcon && typeOf(pIcon) == 'string'){
            if (pIcon.substr(0,1) == '#'){
                this.a[pId].addClass(pIcon.substr(1));
            } else {
                new Element('img', {
                    src: ka.mediaPath(pIcon)
                }).inject(this.a[pId], 'top');
            }
        }

        if (!pPos) {
            this.a[pId].inject(this.chooser);
        } else if (pPos == 'top') {
            this.a[pId].inject(this.chooser, 'top');
        } else if (this.a[pPos]) {
            this.a[pId].inject(this.a[pPos], 'after');
        }

        if (this.value == null) {
            this.setValue(pId);
        }

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

    setValue: function (pValue, pEvent) {

        if (!this.items[ pValue ]) return false;

        this.value = pValue;
        this.title.set('text', this.items[ pValue ]);
        this.box.set('title', (this.items[ pValue ] + "").stripTags());

        this.title.set('class', 'ka-Select-box-title '+this.a[pValue].get('class').replace('ka-select-chooser-item', ''));

        if (this.a[pValue].getElement('img')){
            this.a[pValue].getElement('img').clone().inject(this.title, 'top');
        }

        Object.each(this.a, function (item, id) {
            item.removeClass('active');
            if (id == pValue) {
                item.addClass('active');
            }
        });

        if (pEvent) {
            this.fireEvent('change', pValue);
        }

        return true;
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
    },

    open: function () {

        if (!this.enabled) return;

        if (this.box.getParent('.kwindow-win-titleGroups'))
            this.chooser.addClass('ka-Select-darker');
        else
            this.chooser.removeClass('ka-Select-darker');

        this.box.addClass('ka-Select-box-open');

        ka.openDialog({
            element: this.chooser,
            target: this.box,
            onClose: this.close.bind(this),
            offset: {y: -1}
        });

        this.checkChooserSize();

        return;

    },

    checkChooserSize: function(){

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

    },

    toElement: function () {
        return this.box;
    }

});
