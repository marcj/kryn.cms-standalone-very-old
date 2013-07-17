ka.Editor = new Class({

    Binds: ['onOver', 'onOut', 'contentMouseDown', 'contentSidebarMouseDown', 'checkChange'],
    Implements: [Options, Events],

    options: {
        node: {},
        id: '',
        standalone: false
    },

    container: null,
    preview: 0,

    initialize: function (pOptions, pContainer) {
        this.setOptions(pOptions);

        this.container = pContainer || document.documentElement;

        this.adjustAnchors();
        this.searchSlots();

        top.window.fireEvent('krynEditorLoaded', this);
    },

    getId: function() {
        return this.options.id;
    },

    getNode: function(){
        return this.options.node;
    },

    onOver: function (pEvent, pElement) {
        if (this.lastHoveredContentInstance) {
            this.lastHoveredContentInstance.onOut();
        }

        if (pElement.getDocument().body.hasClass('ka-editor-dragMode')) {
            return;
        }

        if (pElement && pElement.kaContentInstance) {
            pElement.kaContentInstance.onOver(pEvent);
            this.lastHoveredContentInstance = pElement.kaContentInstance;
        }
    },

    onOut: function (pEvent, pElement) {
        if (pElement && pElement.kaContentInstance) {
            pElement.kaContentInstance.onOut(pEvent);
            delete this.lastHoveredContentInstance;
        }
    },

    adjustAnchors: function () {
        var params = {};
        params._kryn_editor = 1;
        params._kryn_editor_id = this.options.id;

        var options = Object.clone(this.options);
        delete options.id;
        delete options.node;
        params._kryn_editor_options = options;

        params = Object.toQueryString(params);

        this.container.getElements('a').each(function (a) {
            if (a.href) {
                a.href = a.href + ((a.href.indexOf('?') > 0) ? '&' : '?') + params
            }
        }.bind(this));
    },

    getValue: function () {
        this.slots = this.container.getElements('.ka-slot');

        var contents = [];

        Array.each(this.slots, function (slot) {
            if (slot.kaSlotInstance) {
                contents = contents.concat(slot.kaSlotInstance.getValue());
            }
        });

        return contents;
    },

    getUrl: function () {
        return _path + 'admin/object/Core:Node/' + this.options.node.id + '?_method=patch';
    },

    save: function () {
        if (this.lastSaveRq) {
            this.lastSaveRq.cancel();
        }

        var contents = this.getValue();

        this.lastSaveRq = new Request.JSON({url: this.getUrl(), onComplete: function (pResponse) {

        }.bind(this)}).post({content: contents});
    },

    highlightSlotsBubbles: function (pHighlight) {
        if (!pHighlight) {
            if (this.lastBubbles) {
                this.lastBubbles.invoke('destroy');
            }
            if (this.lastBubbleTimer) {
                clearInterval(this.lastBubbleTimer);
            }
            return;
        }

        this.lastBubbles = [];

        this.slots.each(function (slot) {

            var bubble = new Element('div', {
                'class': 'ka-editor-slot-infobubble',
                text: t('Drag and drop it here')
            }).inject(slot.getDocument().body);

            bubble.position({
                relativeTo: slot,
                position: 'centerTop',
                edge: 'centerBottom'
            });

            bubble.kaEditorOriginTop = bubble.getStyle('top').toInt() - 10;
            bubble.setStyle('top', bubble.kaEditorOriginTop);
            bubble.kaEditorIsOrigin = true;

            bubble.set('tween', {transition: Fx.Transitions.Quad.easeOut, duration: 1500});

            this.lastBubbles.push(bubble);

        }.bind(this));

        var delta = 8;

        var jump = function () {

            Array.each(this.lastBubbles, function (bubble) {

                if (bubble.kaEditorIsOrigin) {
                    bubble.tween('top', bubble.kaEditorOriginTop - delta);
                    bubble.kaEditorIsOrigin = false;
                } else {
                    bubble.tween('top', bubble.kaEditorOriginTop);
                    bubble.kaEditorIsOrigin = true;
                }

            });

        }.bind(this);

        jump();
        this.lastBubbleTimer = jump.periodical(1500, this);
    },

    highlightSave: function (pHighlight) {
        if (this.saveBtn) {
            if (!pHighlight && this.lastTimer) {
                clearInterval(this.lastTimer);
                delete this.lastTimer;
                this.saveBtn.tween('color', '#ffffff');
                return;
            } else if (this.lastTimer) {
                return;
            }

            this.timerIdx = 0;

            this.lastTimer = (function () {
                if (++this.timerIdx % 2) {
                    this.saveBtn.tween('color', '#2A8AEC');
                } else {
                    this.saveBtn.tween('color', '#ffffff');
                }
            }).periodical(500, this);
        }
    },

    togglePreview: function () {
        var active = ++this.preview % 2;

        if (active) {
            this.showPreview.addClass('ka-editor-sidebar-item-active');
        } else {
            this.showPreview.removeClass('ka-editor-sidebar-item-active');
        }

    },


    highlightSlots: function (pEnter) {
        if (pEnter) {
            this.slots.addClass('ka-slot-highlight');
        } else {
            this.slots.removeClass('ka-slot-highlight');
        }
    },

    searchSlots: function () {
        this.slots = this.container.getElements('.ka-slot');

        Array.each(this.slots, function (slot) {
            this.initSlot(slot);
        }.bind(this));
    },

    hasChanges: function () {
        this.slots = this.container.getElements('.ka-slot');

        var hasChanges = false;

        Array.each(this.slots, function (slot) {
            if (slot.kaSlotInstance) {
                hasChanges |= slot.kaSlotInstance.hasChanges();
            }
        });

        return hasChanges;
    },

    checkChange: function () {
        this.highlightSave(this.hasChanges());
    },

    initSlot: function (pDomSlot) {
        pDomSlot.slotInstance = new ka.Slot(pDomSlot, this.options, this);
        pDomSlot.slotInstance.addEvent('change', this.checkChange);
    }


});