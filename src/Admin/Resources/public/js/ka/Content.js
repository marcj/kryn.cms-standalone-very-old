ka.Content = new Class({

    Binds: ['onOver', 'onOut', 'remove', 'fireChange'],
    Implements: [Options, Events],

    options: {
    },

    slot: null,
    contentObject: null,
    currentType: null,
    currentTemplate: null,

    contentContainer: null,

    initialize: function (pContent, pSlot, pOptions) {
        this.slot = pSlot;
        this.setOptions(pOptions);

        this.renderLayout();

        this.setValue(pContent);
    },

    getSlot: function () {
        return this.slot;
    },

    renderLayout: function () {
        this.main = new Element('div', {
            'class': 'ka-content'
        }).inject(this.slot);

        this.main.addListener('dragstart', function(e) {
            e.dataTransfer.setData('application/json', JSON.encode(this.getValue()));
            this.main.set('draggable');
        }.bind(this));

        this.main.kaContentInstance = this;

        this.actionBar = new Element('div', {
            'class': 'ka-normalize ka-content-actionBar'
        }).inject(this.main);

        this.addActionBarItems();
    },

    fireChange: function () {
        this.fireEvent('change');
    },

    addActionBarItems: function () {

        var moveBtn = new Element('span', {
            html: '&#xe0c6;',
            'class': 'icon ka-content-actionBar-move',
            title: t('Move content')
        }).inject(this.actionBar);

        moveBtn.addEvent('mouseover', function() {
            this.main.set('draggable', true);
        }.bind(this));

//        moveBtn.addEvent('mouseout', function() {
//            this.main.set('draggable', false);
//        }.bind(this));

//        moveBtn.addEvent('mouseover', function() {
//            this.main.set('draggable', true);
//        });

        new Element('a', {
            html: '&#xe26b;',
            href: 'javascript: ;',
            title: t('Remove content'),
            'class': 'icon'
        })
            .addEvent('click', this.remove)
            .inject(this.actionBar);

    },

    remove: function () {
        this.main.destroy();
    },

//    onOver: function () {
//        this.actionBar.inject(this.main);
//    },
//
//    onOut: function () {
//        this.actionBar.dispose();
//    },

    toElement: function () {
        return this.contentContainer || this.main;
    },

    loadTemplate: function (pValue) {

        this.lastRq = new Request.JSON({url: _pathAdmin + 'admin/content/template', noCache: true,
            onComplete: function (pResponse) {
                this.actionBar.dispose();
                this.main.empty();
                this.main.set('html', pResponse.data);

                this.contentContainer = this.main.getElement('.ka-content-container');

                this.currentTemplate = pValue.template;
                this.actionBar.inject(this.main, 'top');
                return this.setValue(pValue);
            }.bind(this)}).get({
                template: pValue.template
            });
    },

    focus: function () {
        if (this.contentObject) {
            this.contentObject.focus();
            this.nextFocus = false;
        } else {
            this.nextFocus = true;
        }
    },

    getValue: function () {

        if (this.contentObject) {
            this.value.content = this.contentObject.getValue();
        }

        return this.value;
    },

    setValue: function (pValue) {

        this.value = pValue;

        if (!this.currentType || pValue.type != this.currentType || !this.currentTemplate ||
            this.currentTemplate != pValue.template) {

            if (!this.currentTemplate || this.currentTemplate != pValue.template) {
                return this.loadTemplate(pValue);
            }

            if (!ka.ContentTypes) {
                throw 'No ka.ContentTypes loaded.';
            }

            var clazz = ka.ContentTypes[pValue.type] || ka.ContentTypes[pValue.type.capitalize()];
            if (clazz) {
                this.contentObject = new clazz(this);
            } else {
                throw tf('ka.ContentType `%s` not found.', pValue.type);
            }

            if (this.nextFocus) {
                this.focus();
            }
            this.currentType = pValue.type;
        }

        this.contentObject.setValue(pValue.content);
        this.contentObject.addEvent('change', this.fireChange);

    }

});