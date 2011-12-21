ka.Select = new Class({
    Implements: Events,

    arrow: 'inc/template/admin/images/icons/tree_minus.png',

    opened: false,
    value: null,

    items: {},

    a: {},

    initialize: function (pContainer) {

        this.box = new Element('div', {
            'class': 'ka-normalize ka-Select-box'
        }).addEvent('click', this.toggle.bindWithEvent(this));

        this.title = new Element('div', {
            'class': 'ka-Select-box-title'
        }).addEvent('mousedown', function (e) {
            e.preventDefault();
        }).inject(this.box);

        this.arrowBox = new Element('div', {
            'class': 'ka-Select-arrow'
        }).inject(this.box);

        this.arrow = new Element('img', {
            src: _path + this.arrow,
        }).inject(this.arrowBox);

        this.chooser = new Element('div', {
            'class': 'ka-Select-chooser ka-normalize'
        });

        this.chooser.addEvent('click', function (e) {
            e.stop();
        });

        if (pContainer)
            this.box.inject(pContainer)

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

    addSplit: function (pLabel) {
        new Element('div', {
            html: pLabel,
            'class': 'group'
        }).inject(this.chooser);
    },

    add: function (pId, pLabel, pPos) {

        this.items[ pId ] = pLabel;

        this.a[pId] = new Element('a', {
            html: pLabel,
            href: 'javascript:;'
        }).addEvent('click', function () {

            this.setValue(pId, true);
            this.close();

        }.bind(this))

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
        this.title.set('html', this.items[ pValue ]);
        this.box.set('title', (this.items[ pValue ] + "").stripTags());

        Object.each(this.a, function (item, id) {
            item.removeClass('active');
            if (id == pValue && pValue != '') {
                item.addClass('active');
            }
        });

        //chrome rendering bug
        this.arrowBox.setStyle('right', 3);
        (function () {
            this.arrowBox.setStyle('right', 2);
        }.bind(this)).delay(10);

        if (pEvent) {
            this.fireEvent('change', pValue);
        }

        return true;
    },

    getValue: function () {
        return this.value;
    },

    toggle: function (e) {

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

        if (this.box.getParent('.kwindow-win-titleGroups'))
            this.chooser.addClass('ka-Select-darker');
        else
            this.chooser.removeClass('ka-Select-darker');

        this.box.addClass('ka-Select-box-open');
        ka.openDialog({
            element: this.chooser,
            target: this.box,
            onClose: this.close.bind(this)
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
