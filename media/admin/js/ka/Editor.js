ka.Editor = new Class({

    Implements: [Options, Events],

    options: {
        nodePk: null
    },

    container: null,

    initialize: function(pContainer, pOptions){

        this.setOptions(pOptions);

        this.container = pContainer || document.body;

        this.adjustAnchors();
        this.searchSlots();
    },

    adjustAnchors: function(){

        this.container.getElements('a').each(function(a){
            a.href = a.href + ((a.href.indexOf('?') > 0) ? '&' : '?') + '_kryn_editor=1';
        });

    },

    searchSlots: function(){

        this.slots = this.container.getElements('.kryn-slot');

        Array.each(this.slots, function(slot){
            this.initSlot(slot);
        }.bind(this));

    },

    initSlot: function(pDomSlot){

        pDomSlot.slotInstance = new ka.Slot(pDomSlot, this.options);

    }


});