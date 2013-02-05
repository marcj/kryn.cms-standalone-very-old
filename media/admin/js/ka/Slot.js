ka.Slot = new Class({

    Implements: [Options, Events],

    options: {
        nodePk: null
    },

    slot: null,
    slotParams: {},

    initialize: function(pDomSlot, pOptions){

        this.slot = pDomSlot;
        this.setOptions(pOptions);

        var params = this.slot.get('params');
        this.slotParams = JSON.decode(params);

        this.renderLayout();

        this.loadContents();

    },

    renderLayout: function(){

        this.slot.empty();

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

        this.addActions();
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

    addContent: function(pContent){

        if (!pContent)
            pContent = {type: 'text'};

        new ka.Content(pContent, this);
    }


});