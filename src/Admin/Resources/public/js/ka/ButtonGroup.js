ka.ButtonGroup = new Class({

    Implements: [Events, Options],

    options: {
        onlyIcons: false
    },

    initialize: function (pParent, pOptions) {

        this.setOptions(pOptions);
        this.buttons = [];

        this.box = new Element('div', {
            'class': 'kwindow-win-buttonGroup'
        }).inject(pParent);
    },

    toElement: function () {
        return this.box;
    },

    destroy: function () {
        this.box.destroy();
    },

    setStyle: function (p, p2) {
        this.box.setStyle(p, p2);
    },

    inject: function (pTo, pWhere) {
        this.box.inject(pTo, pWhere);
    },

    hide: function () {
        this.box.setStyle('display', 'none');
    },

    show: function () {
        this.box.setStyle('display', 'inline-block');
    },

    rerender: function () {
        return;
    },

    addButton: function (pTitle, pIcon, pOnClick) {

        var wrapper = new Element('a', {
            'class': 'ka-buttonGroup-item',
            href: 'javascript:;'
        }).inject(this.box);

        if (typeOf(pTitle) == 'string') {
            wrapper.set(this.options.onlyIcons ? 'title' : 'text', pTitle);
        } else if (pTitle && pTitle.inject) {
            pTitle.inject(wrapper);
            wrapper.setStyle('padding', '3px 0px');
        }

        if (typeOf(pIcon) == 'string') {
            if (this.options.onlyIcons) {
                wrapper.addClass('ka-buttonGroup-item-only-icon');
            }
            if (pIcon.substr(0, 1) == '#') {
                wrapper.addClass('ka-buttonGroup-item-with-icon');
                wrapper.addClass(pIcon.substr(1));
            } else {
                new Element('img', {
                    src: pIcon,
                    height: 14
                }).inject(wrapper, 'top');
            }
        }

        if (pOnClick) {
            wrapper.addEvent('click', pOnClick);
        }

        var _this = this;
        wrapper.hide = function () {
            wrapper.store('visible', false);
            wrapper.setStyle('display', 'none');
            _this.rerender();
        }

        wrapper.startTip = function (pText) {
            if (!this.toolTip) {
                this.toolTip = new ka.Tooltip(wrapper, pText);
            }
            this.toolTip.setText(pText);
            this.toolTip.show();
        }

        wrapper.stopTip = function (pText) {
            if (this.toolTip) {
                this.toolTip.stop(pText);
            }
        }

        wrapper.show = function () {
            wrapper.store('visible', true);
            wrapper.setStyle('display', 'inline');
            _this.rerender();
        }

        wrapper.setPressed = function (pPressed) {
            if (pPressed) {
                wrapper.addClass('ka-buttonGroup-item-active');
            } else {
                wrapper.removeClass('ka-buttonGroup-item-active');
            }
        }

        wrapper.store('visible', true);
        this.buttons.include(wrapper);
        _this.rerender();

        return wrapper;
    },

    setPressed: function (pPressed) {

        this.box.getChildren().each(function (button) {
            button.setPressed(pPressed);
        });

    }
});
