ka.Slot = new Class({

    Binds: ['fireChange'],
    Implements: [Options, Events],

    options: {
        node: {}
    },

    slot: null,
    slotParams: {},
    editor: null,

    initialize: function(pDomSlot, pOptions, pEditor) {
        this.slot = pDomSlot;
        this.slot.kaSlotInstance = this;
        this.setOptions(pOptions);
        this.editor = pEditor;

        var params = this.slot.get('params');
        this.slotParams = JSON.decode(params);

        this.renderLayout();
        this.mapDragEvents();

        this.loadContents();
    },

    getEditor: function() {
        return this.editor;
    },

    mapDragEvents: function() {
        this.slot.addListener('dragover', function(e) {
            return this.checkDragOver(e);
        }.bind(this));

        this.slot.addListener('dragleave', function(e) {
            this.removePlaceholder = true;
            (function(){
                if (this.removePlaceholder && this.lastPlaceHolder) {
                    this.lastPlaceHolder.destroy();
                }
                delete this.removePlaceholder;
            }).delay(100, this);
        }.bind(this));

        this.slot.addListener('drop', function(e) {
            return this.checkDrop(e);
        }.bind(this), false);
    },

    checkDrop: function(pEvent) {
        var target = pEvent.toElement || pEvent.target;
        var slot = this.slot;
        if (target) {
            if (!target.hasClass('ka-slot')) {
                slot = target.getParent('.ka-slot');
                if (slot !== this.slot) {
                    //the target slot is not this slot instance.
                    return;
                }
            }

            if (this.lastPlaceHolder) {
                var data = pEvent.dataTransfer.getData('application/json');
                if (data && JSON.validate(data) && (data = JSON.decode(data))) {
                    var content = this.addContent(data, true);
                    document.id(content).inject(this.lastPlaceHolder, 'after');
                }
                this.lastPlaceHolder.destroy();
            }

            pEvent.preventDefault();
            return false;
        }
    },

    checkDragOver: function(pEvent) {
        var target = pEvent.toElement || pEvent.target;
        var slot = this.slot, content;

        if (target) {
            if (!target.hasClass('ka-slot')) {
                slot = target.getParent('.ka-slot');
                if (slot !== this.slot) {
                    //the target slot is not this slot instance.
                    return;
                }
            }

            delete this.removePlaceholder;

            content = target.hasClass('ka-content') ? target : target.getParent('.ka-content');

            if (!this.lastPlaceHolder) {
                this.lastPlaceHolder = new Element('div', {
                    'class': 'ka-editor-drag-placeholder'
                });
            }

            var zoom = (parseInt(this.slot.getDocument().body.style.zoom || 100) / 100);

            //upper area or bottom?
            if (content) {
                var injectPosition = 'after';
                if (pEvent.pageY / zoom - content.getPosition(document.body).y < (content.getSize().y / 2)) {
                    injectPosition = 'before';
                }
                this.lastPlaceHolder.inject(content, injectPosition);
            } else {
                slot.getChildren().each(function(child) {
                    if (pEvent.pageY / zoom > child.getPosition(document.body).y + 5) {
                        content = child;
                    }
                });

                if (content) {
                    this.lastPlaceHolder.inject(content, 'after');
                } else {
                    this.lastPlaceHolder.inject(slot, pEvent.pageY / zoom > (slot.getSize().y / 2 ) ? 'top' : 'bottom');
                }
            }

            pEvent.preventDefault();
            return false;
        }
    },

    renderLayout: function() {
        this.slot.empty();
    },

    fireChange: function() {
        this.fireEvent('change');
    },

    loadContents: function() {
        if (this.options.node.id) {
            this.lastRq = new Request.JSON({url: _pathAdmin + 'admin/object/Core:Content', noCache: true,
                onComplete: this.renderContents.bind(this)}).get({
                    _boxId: this.slotParams.id,
                    _nodeId: this.options.node.id,
                    order: {sort: 'asc'}
                });
        }
    },

    renderContents: function(pResponse) {
        Array.each(pResponse.data, function(content) {
            this.addContent(content)
        }.bind(this));

        this.oldValue = this.getValue();
    },

    toElement: function() {
        return this.slot;
    },

    hasChanges: function() {
        return JSON.encode(this.oldValue) != JSON.encode(this.getValue());
    },

    getValue: function() {
        var contents = [];
        var data;

        this.slot.getChildren('.ka-content').each(function(content, idx) {
            if (!content.kaContentInstance) {
                return;
            }
            data = content.kaContentInstance.getValue();
            data.boxId = this.slotParams.id;
            data.sortableId = idx;
            contents.push(data);
        }.bind(this));

        return contents;

    },

    addContent: function(pContent, pFocus) {
        if (!pContent) {
            pContent = {type: 'text'};
        }

        if (!pContent.template) {
            pContent.template = '@CoreBundle/content_default.tpl';
        }

        var content = new ka.Content(pContent, this);
        content.addEvent('change', this.fireChange);

        if (pFocus) {
            content.focus();
        }

        return content;
    }

});