ka.layoutElement = new Class({

    Implements: ka.Base,

    layoutBoxes: {},


    initialize: function (pContainer, pInitialTemplate, pWin) {

        this.container = pContainer;
        this.layout = this.container;
        this.win = pWin;

        if (pInitialTemplate) {
            this.loadTemplate(pInitialTemplate);
        } else {
            this.fetchSlots();
        }

    },

    getValue: function () {

        if (this.loadingLayout) {
            return this.getThisValue;
        }

        var res = {};

        Object.each(this.layoutBoxes, function (layoutBox, boxId) {
            if (layoutBox.getValue()) {
                res[ boxId ] = layoutBox.getValue();
            }
        }.bind(this));

        if (Object.getLength(res) == 0) {
            return;
        }

        return res;
    },

    setValue: function (pVal) {

        this.setThisValue = pVal;
        this.getThisValue = pVal;

        if (this.loadingDone) {
            this._setValue();
        }
    },

    _setValue: function () {

        if (this.setThisValue) {
            Object.each(this.layoutBoxes, function (layoutBox, boxId) {
                layoutBox.clear();
                layoutBox.setContents(this.setThisValue[boxId]);
            }.bind(this));
        }
    },

    loadTemplate: function (pTemplate) {

        if (this.template == pTemplate) return;

        this.getThisValue = this.getValue();

        this.loadingLayout = true;

        this.template = pTemplate;

        this.layout.empty();

        this.mkTable(this.layout).set('height', '100%');
        this.mkTr();
        var td = this.mkTd().set('align', 'center').set('valign', 'center');

        new Element('img', {
            src: _path + 'inc/template/admin/images/ka-tooltip-loading.gif'
        }).inject(td);

        this.loadingDone = false;

        new Request.JSON({
            url: _path + 'admin/backend/loadLayoutElementFile/',
            noCache: 1,
            onComplete: this.renderLayout.bind(this)
        }).post({template: pTemplate});

    },

    deselectAll: function () {

        if (!this.layoutBoxes) return;

        Object.each(this.layoutBoxes, function (box, id) {
            box.deselectAll();
        });

    },

    renderLayout: function (pTemplate) {

        if (!pTemplate || !pTemplate.layout) return;

        this.layout.set('html', pTemplate.layout);

        this.fetchSlots();
        this.loadingLayout = false;
    },

    fetchSlots: function () {

        this.layoutBoxes = this.renderLayoutElements(this.layout);

        this.loadingDone = true;
        this._setValue();

    },

    renderLayoutElements: function (pDom) {

        var layoutBoxes = {};
        pDom.getElements('.kryn_layout_content, .kryn_layout_slot').each(function (item) {

            var options = {};
            if (item.get('params')) {
                var options = JSON.decode(item.get('params'));
            }

            if (item.hasClass('kryn_layout_slot')) {
                layoutBoxes[ options.id ] = new ka.layoutBox(item, options, this);
            } //options.name, this.win, options.css, options['default'], this, options );
            else {
                layoutBoxes[ options.id ] = new ka.contentBox(item, options, this);
            }

        }.bind(this));

        return layoutBoxes;
    }

});