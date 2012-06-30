ka.contentBox = new Class({

    initialize: function (pElement, pOptions, pClassObj) {

        this.win = pClassObj.win;
        this.saved = 0;
        this.contents = [];
        this.pageInst = pClassObj;
        this.contentCss = pOptions.css;
        this.alloptions = pOptions;

        this.defaultTemplate = pOptions['default'];

        if (pElement) {
            pElement.empty();
        }

        this.main = new Element('div', {
            'class': 'ka-layoutBox-main' // ka-contentBox-main'
        }).inject(pElement);

        this.contentContainer = new Element('div', {
            'class': 'ka-layoutBox-container'
        }).inject(this.main);


        this.defaultContent = {
            type: pOptions.type,
            template: this.defaultTemplate,
            hide: false,
            noActions: true
        };

        this.layoutContent = new ka.layoutContent(this.defaultContent, this.contentContainer, this);

        this.contentContainer.store('layoutBox', this);

        return;

    },

    inject: function (pTo, pPos) {
        this.main.inject(pTo, pPos);
    },

    clear: function () {
        this.layoutContent.remove();
    },

    setContents: function (pContents) {

        this.layoutContent.remove();

        var firstContent = false;
        if (pContents && typeOf(pContents) == 'array') {
            pContents.each(function (content) {
                if (firstContent == false) {
                    firstContent = content;
                }
            }.bind(this));

            if (firstContent && this.layoutContent) {
            }
        }


        var content = this.defaultContent;
        if (firstContent) {
            content = firstContent;
        }

        content.noActions = true;

        this.layoutContent = new ka.layoutContent(content, this.contentContainer, this);
    },

    getValue: function (pAndClose) {
        return this.getContents(pAndClose);
    },

    deselectAll: function (pWithoutContent) {
        var selected = 0;


        if (!this.layoutContent) return;

        if (this.layoutContent.isRemoved) return;

        this.layoutContent.deselectChilds();

        if (this.layoutContent != pWithoutContent) {
            this.layoutContent.deselect();
        }
    },

    getContents: function (pAndClose) {

        if (!this.layoutContent) return [];
        if (this.layoutContent.isRemoved) return [];

        var res = [];
        var content = this.layoutContent.getValue(pAndClose);

        delete content.noActions;
        if (!content.content) {
            return;
        }

        res.include(content);

        return res;

    }

});