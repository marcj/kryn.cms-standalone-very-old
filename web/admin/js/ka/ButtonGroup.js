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

        this.boxWrapper = new Element('div', {
            'class': 'kwindow-win-buttonGroup-wrapper'
        }).inject(this.box);
    },

    toElement: function(){
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
        this.box.setStyle('display', 'block');
    },

    rerender: function () {
        return;
    },

    addButton: function (pTitle, pIcon, pOnClick) {

        var wrapper = new Element('a', {
            'class': 'kwindow-win-buttonWrapper',
            href: 'javascript:;'
        }).inject(this.boxWrapper);

        if (typeOf(pTitle) == 'string') {
            wrapper.set(this.options.onlyIcons ? 'title' : 'text', pTitle);
        } else if (pTitle && pTitle.inject) {
            pTitle.inject(wrapper);
            wrapper.setStyle('padding', '3px 0px');
        }

        var imgWrapper = new Element('span').inject( wrapper );

        if (typeOf(pIcon) == 'string'){
            if (pIcon.substr(0,1) == '#'){
                imgWrapper.addClass(pIcon.substr(1));
            } else {
                new Element('img', {
                    src: pIcon,
                    height: 14
                }).inject(imgWrapper, 'top');
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

        wrapper.store('oriClass', wrapper.get('class'));

        wrapper.setPressed = function (pPressed) {
            if (pPressed) {
                wrapper.set('class', wrapper.retrieve('oriClass') + ' buttonHover');
            } else {
                wrapper.set('class', wrapper.retrieve('oriClass'));
            }
        }

        wrapper.store('visible', true);
        this.buttons.include(wrapper);
        _this.rerender();

        return wrapper;
    },

    setPressed: function(pPressed){

        this.boxWrapper.getChildren().each(function (button) {
            button.setPressed(pPressed);
        });

    }
});
