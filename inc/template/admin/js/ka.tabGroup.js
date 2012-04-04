ka.tabGroup = new Class({

    'className': 'ka-tabGroup',

    initialize: function (pParent) {
        this.box = new Element('div', {
            'class': this.className
        }).inject(pParent);
    },

    toElement: function(){
        return this.box;
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
        this.box.setStyle('display', 'inline');
    },

    rerender: function (pFirst) {

        var items = this.box.getElements('a');

        items.removeClass('ka-tabGroup-item-first');
        items.removeClass('ka-tabGroup-item-last');

        if (items.length > 0){
            items[0].addClass('ka-tabGroup-item-first');
            items.getLast().addClass('ka-tabGroup-item-last');
        }

    },

    addButton: function (pTitle, pButtonSrc, pOnClick) {

        var button = new Element('a', {
            'class': 'ka-tabGroup-item',
            title: pTitle,
            text: pTitle
        }).inject(this.box);

        if (pButtonSrc) {
            new Element('img', {
                src: pButtonSrc
            }).inject(button, 'top');
        }

        this.setMethods(button, pOnClick);

        return button;

    },

    setMethods: function(pButton, pOnClick){
        if (pOnClick) {
            pButton.addEvent('click', pOnClick);
        }

        pButton.hide = function () {
            pButton.store('visible', false);
            pButton.setStyle('display', 'none');
            this.rerender();
        }.bind(this);

        pButton.show = function () {
            pButton.store('visible', true);
            pButton.setStyle('display', 'inline');
            this.rerender();
        }.bind(this);

        pButton.startTip = function (pText) {
            if (!this.toolTip) {
                this.toolTip = new ka.tooltip(pButton, pText);
            }
            this.toolTip.setText(pText);
            this.toolTip.show();
        }

        pButton.stopTip = function (pText) {
            if (this.toolTip) {
                this.toolTip.stop(pText);
            }
        }

        pButton.setPressed = function (pPressed) {
            if (pPressed) {
                pButton.addClass('ka-tabGroup-item-active');
            } else {
                pButton.removeClass('ka-tabGroup-item-active');
            }
        }

        pButton.isPressed = function(){
            return pButton.hasClass('ka-tabGroup-item-active');
        }

        pButton.store('visible', true);
        this.rerender(true);
    }
});
