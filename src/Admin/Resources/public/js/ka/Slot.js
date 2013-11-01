ka.Slot = new Class({

    Binds: ['fireChange'],
    Implements: [Options, Events],

    options: {
        node: {},
        standalone: true
    },

    slot: null,
    slotParams: {},
    editor: null,

    initialize: function(pDomSlot, pOptions, pEditor) {
        this.slot = pDomSlot;
        this.slot.kaSlotInstance = this;
        this.setOptions(pOptions);
        this.editor = pEditor;

        var params = this.slot.get('params') || '';
        this.slotParams = JSON.decode(params) || {};

        this.renderLayout();
        this.mapDragEvents();

        if (this.options.standalone) {
            this.loadContents();
        }
    },

    getParam: function(key) {
        return this.slotParams[key];
    },

    getEditor: function() {
        return this.editor;
    },

    getBoxId: function() {
        return this.slotParams.id;
    },

    mapDragEvents: function() {
        this.slot.addListener('dragover', function(e) {
            return this.checkDragOver(e);
        }.bind(this), false);

        this.slot.addListener('dragleave', function(e) {
            this.removePlaceholder = true;
            (function(){
                if (this.removePlaceholder && this.lastPlaceHolder) {
                    this.lastPlaceHolder.destroy();
                }
                delete this.removePlaceholder;
            }).delay(100, this);
        }.bind(this), false);

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

            var items = pEvent.dataTransfer.files.length > 0 ? pEvent.dataTransfer.files : pEvent.dataTransfer.items,
                data, content;

            if (!items && pEvent.dataTransfer.types) {
                items = [];
                Array.each(pEvent.dataTransfer.types, function(type) {
                    var dataType = pEvent.dataTransfer.getData(type);
                    items.push({
                        type: type,
                        getAsString: function(cb) {
                            cb(dataType);
                        }
                    });
                });
            }

            if (this.lastPlaceHolder) {
                if (items) {
                    Array.each(items, function(item) {

                        data = null;

                        if ('application/json' === item.type) {
                            item.getAsString(function(data) {
                                if (data && (!JSON.validate(data) || !(data = JSON.decode(data)))) {
                                    data = null;
                                }
                                if (data) {
                                    content = this.addContent(data, true, item);
                                    document.id(content).inject(this.lastPlaceHolder, 'before');
                                }

                                this.lastPlaceHolder.destroy();
                            }.bind(this));
                        } else {
                            //search for plugin that handles it
                            Object.each(ka.ContentTypes, function(type, key) {
                                if ('array' === typeOf(type.mimeTypes) && type.mimeTypes.contains(item.type)) {
                                    data = {
                                        type: key
                                    };
                                }
                            });

                            if (data) {
                                content = this.addContent(data, true, item);
                                document.id(content).inject(this.lastPlaceHolder, 'before');
                                this.lastPlaceHolder.destroy();
                            }
                        }

                    }.bind(this));
                } else {
                    this.lastPlaceHolder.destroy();
                }
            }

            pEvent.stopPropagation();
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

            //pEvent.dataTransfer.dropEffect = 'move';

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

            pEvent.stopPropagation();
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
        this.setValue(pResponse.data);
    },

    setValue: function(contents) {
        this.slot.empty();
        if ('array' === typeOf(contents)) {
            Array.each(contents, function(content) {
                this.addContent(content)
            }.bind(this));
        }
    },

    toElement: function() {
        return this.slot;
    },

    hasChanges: function() {
        return JSON.encode(this.oldValue) != JSON.encode(this.getValue());
    },

    setPreview: function(visible) {
        this.slot.getChildren('.ka-content').each(function(content, idx) {
            content.kaContentInstance.setPreview(visible);
        });
    },

//    /**
//     *
//     * @param {ka.SaveProgress} saveProgress
//     * @returns {Array}
//     */
//    getValue: function(saveProgress) {
//        var contents = [], result = [];
//
//        var slotSaveManager = new ka.SaveProgressManager({
//            /**
//             *
//             * @param {ka.SaveProgress} saveProgress
//             */
//            onDone: function(saveProgress) {
//                contents.push(saveProgress.getValue());
//            },
//            onAllDone: function(){
//                saveProgress.done(contents);
//            },
//            onAllProgress: function(progress) {
//                saveProgress.progress(progress);
//            }
//        });
//
//        var self = this;
//        var contentItems = this.slot.getChildren('.ka-content');
//        contentItems.each(function(content, idx) {
//            if (!content.kaContentInstance) {
//                return;
//            }
//
//            var contentSaveProgress = new ka.SaveProgress({
//                onDone: function(saveProgress) {
//                    var value = saveProgress.getValue();
//                    value.boxId = self.slotParams.id;
//                    value.sortableId = idx;
//                    this.setValue(value);
//                    slotSaveManager.done(this);
//                },
//                onProgress: function(){
//                    slotSaveManager.progress(this);
//                }
//            });
//            slotSaveManager.addSaveProgress(contentSaveProgress);
//            content.contentSaveProgress = contentSaveProgress;
//        }.bind(this));
//
//        contentItems.each(function(content, idx) {
//            result.push(content.kaContentInstance.getValue(content.contentSaveProgress));
//        });
//
//        return result;
//    },

    getId: function() {
        return this.slotParams.id;
    },

    getContents: function() {
        var contents = [];
        this.slot.getChildren('.ka-content').each(function(content, idx) {
            if (content.kaContentInstance) {
                content.kaContentInstance.setBoxId(parseInt(this.getId()));
                content.kaContentInstance.setSortId(idx + 1);
                contents.push(content.kaContentInstance);
            }
        }.bind(this));
        return contents;
    },

    addContent: function(pContent, pFocus, pDrop) {
        if (!pContent) {
            pContent = {type: 'text'};
        }

        if (!pContent.template) {
            pContent.template = '@CoreBundle/content_default.tpl';
        }

        var content = new ka.Content(pContent, this.slot, pDrop);
        content.addEvent('change', this.fireChange);

        if (pFocus) {
            this.getEditor().getContentField().select(content);
            content.focus();
        }

        return content;
    }

});