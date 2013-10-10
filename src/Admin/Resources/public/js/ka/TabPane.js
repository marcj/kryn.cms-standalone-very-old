ka.TabPane = new Class({
    Implements: Events,

    index: -1,

    initialize: function (pParent, pFull, pUseThisAsWindowHeader) {
        this.box = new Element('div', {
            'class': 'kwindow-win-tabPane' + (pFull ? ' ka-tabPane-full' : '')
        }).inject(pParent);

        if (pUseThisAsWindowHeader) {
            pUseThisAsWindowHeader.setContentStick(true);
            this.buttonGroup = pUseThisAsWindowHeader.addSmallTabGroup();
            this.box.addClass('ka-tabPane-tabsInWindowHeader');
        } else {
            this.buttonGroup = new ka.SmallTabGroup(this.box);
        }

        this.paneBox = new Element('div', {'class': 'kwindow-win-tabPane-pane'}).inject(this.box);

        this.panes = [];
        this.buttons = [];
    },

    getTabs: function () {
        var tabs = this.paneBox.getChildren('.ka-tabPane-pane');
        var result = [];

        Array.each(tabs, function (tab) {
            result.push({
                pane: tab,
                button: tab.button,
                id: tab.id
            });
        });

        return result;
    },

    toElement: function () {
        return this.box;
    },

    setHeight: function (pHeight) {

        this.paneBox.setStyle('height', pHeight);
    },

    rerender: function () {
        this.buttonGroup.rerender();
    },

    getSelected: function () {

        if (this.index == -1) {
            return;
        }

        return {
            pane: this.panes[this.index],
            button: this.buttons[this.index],
            id: this.index
        };

    },

    addPane: function (pTitle, pImageSrc) {

        var id = this.panes.length;
        var res = {};
        res.pane = new Element('div', {
            'class': 'ka-tabPane-pane ka-scrolling'
        }).inject(this.paneBox);

        this.panes.include(res.pane);

        var btn = this.buttonGroup.addButton(pTitle, this._to.bind(this, id), pImageSrc);

        btn.tabPane = this;
        this.buttons.include(btn);
        res.button = btn;

        res.button.pane = res.pane;
        res.pane.button = res.button;
        res.hide = res.button.hide;
        res.show = res.button.show;
        res.id = id;
        res.pane.id = id;

        if (this.index == -1) {
            this.to(0);
        }

        return res;
    },

    _to: function (id) {
        this.fireEvent('change', id);
        this.to(id);
    },

    to: function (id) {
        this.index = id;

        this.panes.each(function (pane) {
            pane.setStyle('display', 'none');
        });
        this.buttons.each(function (button) {
            button.setPressed(false);
        });

        this.panes[ id ].setStyle('display', 'block');
        this.buttons[ id ].setPressed(true);

    },

    remove: function (pId) {

        this.buttons[pId].destroy();
        this.panes[pId].destroy();

        this.buttonGroup.rerender();

        delete this.buttons[pId];
        delete this.panes[pId];

    },

    destroy: function () {
        this.box.destroy();
    },

    inject: function (pTo, pWhere) {
        this.box.inject(pTo, pWhere);
    },

    hide: function () {
        this.box.setStyle('display', 'none');
        this.buttonGroup.hide();
    },

    show: function () {
        this.buttonGroup.show();
        this.box.setStyle('display', 'block');
    }
});
