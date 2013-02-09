ka.Dialog = new Class({

    Binds: ['center', 'close', 'updateBottomPosition'],
    Implements: [Events, Options],

    options: {

        title: '',

        minWidth: null,
        minHeight: null,
        maxHeight: null,
        maxWidth: null,
        width: null,
        height: null

    },

    canClosed: true,

    initialize: function(pParent, pOptions){

        this.container = instanceOf(pParent, ka.Window) ? pParent.getContentContainer() : pParent;

        this.setOptions(pOptions);

        this.renderLayout();

        if (instanceOf(pParent, ka.Window)){
            this.window = pParent;
            this.window.addEvent('resize', this.center);
        } else {
            this.container.getDocument().getWindow().addEvent('resize', this.center);
            if (!this.container.getDocument().hiddenCount) this.container.getDocument().hiddenCount = 0;
            this.container.getDocument().hiddenCount++;
            this.container.getDocument().body.addClass('hide-scrollbar');
        }

    },

    renderLayout: function(){

        this.overlay = new Element('div', {
            'class': 'ka-dialog-overlay'
        }).inject(this.container);

        this.main = new Element('div', {
            'class': 'ka-dialog selectable'
        }).inject(this.overlay);

        ['minWidth', 'maxWidth', 'minHeight', 'maxHeight', 'height', 'width'].each(function(item){
            if (typeOf(this.options[item]) != 'null')
                this.main.setStyle(item, this.options[item]);
        }.bind(this));

        this.bottom = new Element('div', {
            'class': 'ka-dialog-bottom'
        }).inject(this.main);

        this.updateBottomPosition();

        this.main.addEvent('scroll', this.updateBottomPosition);

        this.center();
    },

    updateBottomPosition: function(){

        var sHeight = this.main.getScrollSize().y;
        var sTop = this.main.getScroll().y;
        var height = this.main.getSize().y;
        var delta = -((sHeight-height) - sTop);

        if (height == sHeight){
            //no scrollBar
            var btop = this.bottom.getStyle('top').toInt();
            if (!btop) btop = 0;
            delta = btop + height - this.bottom.getPosition(this.main).y - this.bottom.getSize().y;
        }
        this.bottom.setStyle('top', delta);
    },

    setContent: function(pHtml){

        this.bottom.dispose();
        this.main.set('html', pHtml);
        this.bottom.inject(this.main);
        this.updateBottomPosition();

    },

    setText: function(pText){

        this.bottom.dispose();
        this.main.set('text', pText);
        this.bottom.inject(this.main);
        this.updateBottomPosition();

    },

    addButton: function(pTitle){
        return new ka.Button(pTitle).inject(this.bottom);
    },

    close: function(pInternal){

        if (pInternal)
            this.main.fireEvent('preClose');

        if (!this.canClosed) return;

        this.overlay.destroy();
        this.main.dispose();
        this.removeEvent('resize', this.center);

        if (pInternal)
            this.main.fireEvent('close');

        this.main.destroy();

        this.container.getDocument().hiddenCount--;
        if (this.container.getDocument().hiddenCount == 0)
            this.container.getDocument().body.removeClass('hide-scrollbar');

    },

    getBottomContainer: function(){
        return this.bottom;
    },

    center: function(){

        var size = this.container.getSize();
        var dsize = this.main.getSize();
        var left = (size.x.toInt() / 2 - dsize.x.toInt() / 2);
        var mtop = (size.y.toInt() / 2 - dsize.y.toInt() / 2);
        this.main.setStyle('left', left);
        this.main.setStyle('top', mtop);

        this.updateBottomPosition();
    },

    toElement: function(){
        return this.main;
    },

    getContentContainer: function(){
        return this.content;
    }


});