ka.Dialog = new Class({

    Implements: [Events, Options],

    options: {



    },

    initialize: function(pParent, pOptions){


        this.container = instanceOf(pParent, ka.Window) ? pParent.getContentContainer() : pParent;

        this.setOptions(pOptions);

        this.renderLayout();

    },

    renderLayout: function(){

        this.main = new Element('div', {
            'class': 'ka-dialog'
        }).inject(this.container);



    },

    toElement: function(){
        return this.main;
    },

    getContentContainer: function(){
        return this.content;
    }


});