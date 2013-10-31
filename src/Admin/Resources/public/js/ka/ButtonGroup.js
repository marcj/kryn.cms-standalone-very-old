ka.ButtonGroup = new Class({

    Implements: [Events, Options],

    options: {
        onlyIcons: false
    },

    initialize: function (pParent, pOptions) {

        this.setOptions(pOptions);

        this.box = new Element('div', {
            'class': 'ka-ButtonGroup'
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

    activate: function() {
        this.box.getChildren('a.ka-Button').each(function(item) {
            item.kaButton.activate();
        });
    },

    deactivate: function() {
        this.box.getChildren('a.ka-Button').each(function(item) {
            item.kaButton.deactivate();
        });
    },

    addButton: function (pTitle, pIcon, pOnClick) {
        var title = pTitle;
        if (pIcon) {
            title = [title, pIcon];
        }

        var button = new ka.Button(title, pOnClick).inject(this.box);
        document.id(button).addClass('ka-buttonGroup-item');
        return button;
    },

    addIconButton: function (pTitle, pIcon, pOnClick) {
        var title = '';
        if (pIcon) {
            title = ['', pIcon];
        }

        var button = new ka.Button(title, pOnClick).inject(this.box);
        document.id(button).set('title', pTitle);
        document.id(button).addClass('ka-buttonGroup-item');
        return button;
    },

    setPressed: function (pPressed) {

        this.box.getChildren().each(function (button) {
            button.setPressed(pPressed);
        });

    }
});
