ka.Editor = new Class({

    Binds: ['onOver', 'onOut'],
    Implements: [Options, Events],

    options: {
        nodePk: null
    },

    container: null,
    preview: 0,

    initialize: function(pContainer, pOptions){

        this.setOptions(pOptions);

        this.container = pContainer || document.body;

        this.adjustAnchors();
        this.searchSlots();
        this.renderSidebar();

        this.container.addClass('ka-editor');


        this.container.addEvent('mouseenter:relay(.ka-content)', this.onOver);
        this.container.addEvent('mouseleave:relay(.ka-content)', this.onOut);
    },

    onOver: function(pEvent, pElement){
        if (this.lastHoveredContentInstance)
            this.lastHoveredContentInstance.onOut();

        if (pElement && pElement.kaContentInstance){
            pElement.kaContentInstance.onOver(pEvent);
            this.lastHoveredContentInstance = pElement.kaContentInstance;
        }
    },

    onOut: function(pEvent, pElement){
        logger(pElement);
        if (pElement && pElement.kaContentInstance){
            pElement.kaContentInstance.onOut(pEvent);
            delete this.lastHoveredContentInstance;
        }
    },

    adjustAnchors: function(){

        this.container.getElements('a').each(function(a){
            a.href = a.href + ((a.href.indexOf('?') > 0) ? '&' : '?') + '_kryn_editor=1';
        });

    },

    renderSidebar: function(){

        this.sidebar = new Element('div',{
            'class': 'ka-editor-sidebar'
        }).inject(this.container);

        this.showSlots = new Element('a', {
            'html': '&#xe2da;',
            href: 'javascript: ;',
            'class': 'icon ka-editor-sidebar-item ka-editor-sidebar-item-showslots',
            title: t('Show available slots')
        })
        .addEvent('mouseenter', function(){ this.highlightSlots(true); }.bind(this))
        .addEvent('mouseleave', function(){ this.highlightSlots(false); }.bind(this))
        .inject(this.sidebar);


        this.showPreview = new Element('a', {
            'html': '&#xe28d;',
            href: 'javascript: ;',
            'class': 'icon ka-editor-sidebar-item ka-editor-sidebar-item-splitter',
            title: t('Toggle preview')
        })
        .addEvent('click', function(){ this.togglePreview(); }.bind(this))
        .inject(this.sidebar);


        this.highlightSave();

        Object.each(ka.ContentTypes, function(content, type){
            this.addContentTypeIcon(type, content);
        }.bind(this));

        this.saveBtn = new Element('a', {
            'html': '&#xe2a4;',
            href: 'javascript: ;',
            title: t('Save changes'),
            'class': 'icon ka-editor-sidebar-item ka-editor-sidebar-item-save'
        })
        .addEvent('click', function(){ this.save(); }.bind(this))
        .inject(this.sidebar);

        this.initDragNDrop.delay(500, this);
    },

    save: function(){




    },

    initDragNDrop: function(){

        //this.slots.addEvent('mousedown:relay(.ka-content)', this.startDrag.bind(this));
        this.sidebar.addEvent('mousedown:relay(.ka-editor-sidebar-draggable)', this.startDrag.bind(this));

    },

    startDrag: function(pEvent, pElement){

        var body = pElement.getDocument().body;

        var clone = pElement.clone().setStyles(pElement.getCoordinates()).setStyles({
            opacity: 0.7,
            position: 'absolute'
        }).inject(body);

        clone.addClass('ka-editor-sidebar-draggable-active');


        var drag = new Drag.Move(clone, {

            droppables: this.slots,
            snap: 0,

            onDrop: function(dragging, slot){

                dragging.destroy();
                this.highlightSlots(false);
                this.highlightSlotsBubbles(false);

                if (slot){
                    slot.setStyle('background-color');
                    slot.kaSlot.addContent({type: pElement.kaContentType}, true);
                }
            }.bind(this),
            onEnter: function(dragging, slot){
                slot.setStyle('background-color', 'rgba(34, 124, 160,0.4)');
            },
            onLeave: function(dragging, slot){
                slot.setStyle('background-color');
            },
            onCancel: function(dragging, slot){
                dragging.destroy();
                this.highlightSlots(false);
                this.highlightSlotsBubbles(false);
                if (slot) slot.setStyle('background-color');
            }.bind(this)
        });

        this.highlightSlots(true);
        this.highlightSlotsBubbles(true);
        drag.start(pEvent);

    },

    highlightSlotsBubbles: function(pHighlight){
        if (!pHighlight && this.lastTimer){
            this.lastBubbles.invoke('destroy');
            return clearInterval(this.lastBubbleTimer);
        }

        this.lastBubbles = [];

        this.slots.each(function(slot){

            var bubble = new Element('div', {
                'class': 'ka-editor-slot-infobubble',
                text: t('Drag and drop it here')
            }).inject(slot.getDocument().body);

            bubble.position({
                relativeTo: slot,
                position: 'centerTop',
                edge: 'centerBottom'
            });

            bubble.kaEditorOriginTop = bubble.getStyle('top').toInt()-10;
            bubble.setStyle('top', bubble.kaEditorOriginTop);
            bubble.kaEditorIsOrigin  = true;

            bubble.set('tween', {transition: Fx.Transitions.Quad.easeOut, duration: 1500});

            this.lastBubbles.push(bubble);

        }.bind(this));

        var delta = 8;

        var jump = function(){

            Array.each(this.lastBubbles, function(bubble){

                if (bubble.kaEditorIsOrigin){
                    bubble.tween('top', bubble.kaEditorOriginTop-delta);
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

    highlightSave: function(pHighlight){

        if (!pHighlight && this.lastTimer) return clearInterval(this.lastTimer);

        this.timerIdx = 0;

        this.lastTimer = (function(){
            if (++this.timerIdx%2){
                this.saveBtn.tween('color', '#66ACF3');
            } else {
                this.saveBtn.tween('color', '#ffffff');
            }
        }).periodical(500, this);

    },

    togglePreview: function(){
        var active = ++this.preview % 2;

        if (active){
            this.showPreview.addClass('ka-editor-sidebar-item-active');
        } else {
            this.showPreview.removeClass('ka-editor-sidebar-item-active');
        }


    },

    addContentTypeIcon: function(pType, pContent){

        var type = new pContent;

        var a = new Element('a', {
            'html': type.icon,
            href: 'javascript: ;',
            'class': 'icon ka-editor-sidebar-item ka-editor-sidebar-draggable'
        })
        .inject(this.sidebar);

        a.kaContentType = pType;

    },

    highlightSlots: function(pEnter){
        if (pEnter)
            this.slots.addClass('ka-slot-highlight');
        else
            this.slots.removeClass('ka-slot-highlight');
    },

    searchSlots: function(){

        this.slots = this.container.getElements('.ka-slot');

        Array.each(this.slots, function(slot){
            this.initSlot(slot);
        }.bind(this));

    },

    initSlot: function(pDomSlot){

        pDomSlot.slotInstance = new ka.Slot(pDomSlot, this.options);

    }


});