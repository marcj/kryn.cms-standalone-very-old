ka.Slot = new Class({

    Implements: [Options, Events],

    options: {
        nodePk: null
    },

    slot: null,
    slotParams: {},

    initialize: function(pDomSlot, pOptions){

        this.slot = pDomSlot;
        this.slot.kaSlot = this;
        this.setOptions(pOptions);

        var params = this.slot.get('params');
        this.slotParams = JSON.decode(params);

        this.renderLayout();

        this.loadContents();

    },

    renderLayout: function(){

        this.slot.empty();

        return;
        this.header = new Element('div', {
            'class': 'ka-slot-header'
        }).inject(this.slot);

        this.headerInner = new Element('div', {
            'class': 'ka-slot-header-inner'
        }).inject(this.header);

        this.headerTitle = new Element('div', {
            'class': 'ka-slot-header-title',
            text: this.slotParams.name
        }).inject(this.headerInner);

        this.headerActions = new Element('div', {
            'class': 'ka-slot-header-actions'
        }).inject(this.headerInner);

        this.addActions();
    },

    loadContents: function(){

        this.lastRq = new Request.JSON({url: _path+'admin/object/Core.Content', noCache: true,
        onComplete: this.renderContents.bind(this)}).get({
            _boxId: this.slotParams.id,
            _nodeId: this.options.nodePk
        });

    },

    renderContents: function(pResponse){

        Array.each(pResponse.data, function(content){
            this.addContent(content)
        }.bind(this));
    },

    toElement: function(){
        return this.slot;
    },

    addActions: function(){

        this.addContentBtn = new Element('a',{
            href: 'javascript: ;',
            html: '&#xe109;',
            title: tc('nodeEditor', 'Add content to this slot')
        })
        .addEvent('click', function(){ this.addContent(); }.bind(this))
        .inject(this.headerActions);

    },

    addContent: function(pContent, pFocus){

        if (!pContent)
            pContent = {type: 'text'};

        if (!pContent.template){
            pContent.template = 'core/content_default.tpl';
        }

        var content = new ka.Content(pContent, this);

        if (pFocus)
            content.focus();
    }


});