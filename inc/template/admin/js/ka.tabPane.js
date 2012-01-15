ka.tabPane = new Class({
    Implements: Events,

    index: -1,

    initialize: function (pParent, pFull, pUseThisAsWindowHeader) {
        this.box = new Element('div', {
            'class': 'kwindow-win-tabPane'+(pFull?' ka-tabPane-full':'')
        }).inject(pParent);

        if (pUseThisAsWindowHeader){
            this.buttonGroup = pUseThisAsWindowHeader.addSmallTabGroup();
            this.box.addClass('ka-tabPane-tabsInWindowHeader');
        } else if (!pFull){
            this.buttonGroup = new ka.smallTabGroup(this.box);
            this.buttonGroup.box.setStyle('position', 'relative').setStyle('top', 1);
            new Element('div', {style: 'clear: both'}).inject(this.box);
        }

        this.paneBox = new Element('div', {'class': 'kwindow-win-tabPane-pane'}).inject(this.box);

        if (pFull && !pUseThisAsWindowHeader){
            this.buttonGroup = new ka.smallTabGroup(this.box);
            new Element('div', {style: 'clear: both'}).inject(this.box);
        }

        this.panes = [];
        this.buttons = [];
    },

    toElement: function(){
        return this.box;
    },

    setHeight: function (pHeight) {

        this.paneBox.setStyle('height', pHeight);
    },

    rerender: function () {
        this.buttonGroup.rerender();
    },

    addPane: function (pTitle, pImageSrc) {

        var id = this.panes.length;
        var res = {};
        res.pane = new Element('div', {
            'class': 'ka-tabPane-pane'
        }).inject(this.paneBox);

        this.panes.include(res.pane);

        var btn = this.buttonGroup.addButton(pTitle, this._to.bind(this, id), pImageSrc);

        this.buttons.include(btn);
        res.button = btn;
        res.id = id;

        if (this.index == -1){
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
            button.setStyle('border-bottom', '1px solid #EEEEEE');
        })

        this.buttons[ id ].setPressed(true);
        this.buttons[ id ].setStyle('border-bottom', '0px');
        this.panes[ id ].setStyle('display', 'block');

    },

    destroy: function () {
        this.box.destroy();
    },

    inject: function (pTo, pWhere) {
        this.box.inject(pTo, pWhere);
    },

    hide: function () {
        this.box.setStyle('display', 'none');
    },

    show: function () {
        this.box.setStyle('display', 'block');
    }
});
