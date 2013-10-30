ka.Content = new Class({

    Binds: ['onOver', 'onOut', 'remove', 'fireChange'],
    Implements: [Options, Events],

    drop: null,

    contentObject: null,
    currentType: null,
    currentTemplate: null,

    contentContainer: null,

    initialize: function(pContent, pContainer, pDrop) {
        this.drop = pDrop;
        this.renderLayout(pContainer);
        this.setValue(pContent);
    },

    getSlot: function() {
        return this.main.getParent('.ka-slot').kaSlotInstance;
    },

    getEditor: function() {
        return this.getSlot().getEditor();
    },

    renderLayout: function(container) {
        this.main = new Element('div', {
            'class': 'ka-content '
        }).inject(container);

        this.main.addListener('dragstart', function(e) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('application/json', JSON.encode(this.getValue()));
            //this.main.set('draggable');
        }.bind(this));

        this.main.addListener('dragend', function(e) {
            if ('move' === e.dataTransfer.dropEffect) {
                this.destroy();
            }
        }.bind(this));

        this.main.kaContentInstance = this;

        this.actionBar = new Element('div', {
            'class': 'ka-normalize ka-content-actionBar'
        }).inject(this.main);

        this.addActionBarItems();
    },

    destroy: function() {
        if (this === this.getEditor().getContentField().getSelected()) {
            this.getEditor().getContentField().deselect();
        }
        this.main.destroy();
    },

    fireChange: function() {
        this.updateUI();
        this.fireEvent('change');
    },

    addActionBarItems: function() {

        var moveBtn = new Element('span', {
            html: '&#xe0c6;',
            'class': 'icon ka-content-actionBar-move',
            title: t('Move content')
        }).inject(this.actionBar);

        moveBtn.addEvent('mouseover', function() {
            this.main.set('draggable', true);
        }.bind(this));

        moveBtn.addEvent('mouseout', function() {
            this.main.set('draggable');
        }.bind(this));

        new Element('a', {
            html: '&#xe26b;',
            href: 'javascript: ;',
            title: t('Remove content'),
            'class': 'icon'
        })
            .addEvent('click', function(e) {
                e.stop();
                this.remove();
            }.bind(this))
            .inject(this.actionBar);

    },

    remove: function() {
        this.getEditor().deselect();
        this.main.destroy();
    },

//    onOver: function () {
//        this.actionBar.inject(this.main);
//    },
//
//    onOut: function () {
//        this.actionBar.dispose();
//    },

    toElement: function() {
        return this.contentContainer || this.main;
    },

    setPreview: function(visible) {
        this.preview = visible;

        if (this.preview) {
            this.loadPreview();
        } else {
            this.loadTemplate(this.value);
        }
    },

    loadPreview: function() {
        delete this.currentTemplate;

        if (this.lastRq) {
            this.lastRq.cancel();
        }

        this.lastRq = new Request.JSON({url: _pathAdmin + 'admin/content/preview', noCache: true,
                onComplete: function(pResponse) {
                    this.actionBar.dispose();
                    this.main.empty();
                    this.main.set('html', pResponse.data);
                }.bind(this)}
        ).get({
                template: this.value.template,
                type: this.value.type,
                content: this.value.content
            });
    },

    loadTemplate: function() {
        if (this.lastRq) {
            this.lastRq.cancel();
        }

        if (null !== this.currentTemplate && this.currentTemplate == this.value.template) {
            return;
        }

        this.lastRq = new Request.JSON({url: _pathAdmin + 'admin/content/template', noCache: true,
                onComplete: function(pResponse) {
                    this.actionBar.dispose();
                    this.main.empty();
                    this.main.set('html', pResponse.data);

                    this.contentContainer = this.main.getElement('.ka-content-container') || new Element('div').inject(this.main);

                    delete this.contentObject;
                    this.currentTemplate = this.value.template;
                    this.actionBar.inject(this.main, 'top');

                    return this.setValue(this.value);
                }.bind(this)}
        ).get({
                template: this.value.template,
                type: this.value.type
            });
    },

    focus: function() {
        if (this.contentObject) {
            this.contentObject.focus();
            this.nextFocus = false;
        } else {
            this.nextFocus = true;
        }
    },

    getValue: function() {
        if (this.selected) {
            this.value = this.value || {};

            this.value.template = this.template.getValue();
        }

        if (this.contentObject) {
            this.value.content = this.contentObject.getValue();
        }

        return this.value;
    },

    loadInspector: function() {
        var inspectorContainer = this.getEditor().getContentField().getInspectorContainer();

        inspectorContainer.empty();

        this.template = new ka.Field({
            label: t('Content Layout'),
            width: 'auto',
            type: 'contentTemplate',
            inputWidth: '100%'
        }, inspectorContainer);

        this.template.addEvent('change', function() {
            return this.updateUI();
        }.bind(this));

        this.template.setValue(this.value.template || null);

        this.inspectorContainer = new Element('div', {
            style: 'padding-top: 5px;'
        }).inject(inspectorContainer);
    },

    updateUI: function() {
        this.value = this.getValue();
        if (this.preview) {
            this.loadPreview();
        } else {
            this.loadTemplate();
        }
    },

    setSelected: function(selected) {
        if (selected) {
            this.main.addClass('ka-content-selected');
            this.loadInspector();
            if (this.contentObject && this.contentObject.selected) {
                this.contentObject.selected(this.inspectorContainer);
            }
        } else {
            this.main.removeClass('ka-content-selected');
            if (this.contentObject && this.contentObject.deselected) {
                this.contentObject.deselected();
            }
            if (this.inspectorContainer) {
                this.inspectorContainer.destroy();
            }
        }
        this.selected = selected;
    },

    setValue: function(pValue) {
        this.value = pValue;

        if (
            !this.currentType
                || !this.contentObject
                || pValue.type != this.currentType
                || !this.currentTemplate
                || this.currentTemplate != pValue.template
            ) {

            if (null === this.currentTemplate || this.currentTemplate != pValue.template) {
                return this.loadTemplate(pValue);
            }

            if (!ka.ContentTypes) {
                throw 'No ka.ContentTypes loaded.';
            }

            var clazz = ka.ContentTypes[pValue.type] || ka.ContentTypes[pValue.type.capitalize()];
            if (clazz) {
                this.contentObject = new clazz(this, this.options);
            } else {
                throw tf('ka.ContentType `%s` not found.', pValue.type);
            }

            this.contentObject.addEvent('change', this.fireChange);

            if (this.nextFocus) {
                this.focus();
            }
            this.currentType = pValue.type;
        }

        this.contentObject.setValue(pValue.content);

        if (this.selected) {
            this.setSelected(true);
        }
    }

});