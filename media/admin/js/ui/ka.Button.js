ka.Button = new Class({

    events: {},

    initialize: function (pTitle, pOnClick, pTooltip) {
        this.main = new Element('a', {
            'class': 'ka-Button',
            text: (typeOf(pTitle) == 'string') ? pTitle : '',
            title: (pTooltip) ? pTooltip : null
        });

        this.setText(pTitle);

        if (pOnClick) {
            this.main.addEvent('click', pOnClick);
        }
    },

    setText: function (pText) {

        if (typeOf(pText) == 'element' && pText.inject) {
            pText.inject(this.main);

        } else if (typeOf(pText) == 'array'){
            this.main.set('text', pText[0]);

            if (typeOf(pText[1]) == 'string'){

                if (pText[1].substr(0,1) == '#'){
                    this.main.addClass(pText[1].substr(1));
                } else {
                    new Element('img', {
                        src: ka.mediaPath(pText[1])
                    }).inject(this.main, 'top');
                }
            }

        } else {
            this.main.set('text', pText);
        }
    },

    toElement: function () {
        return this.main;
    },

    setStyle: function (p1, p2) {
        this.main.setStyle(p1, p2);
        return this;
    },

    setStyles: function (p1) {
        this.main.setStyles(p1);
        return this;
    },

    inject: function (p1, p2) {
        this.main.inject(p1, p2);
        return this;
    },

    addEvent: function (p1, p2) {
        if( !this.events[p1] ){
            this.events[p1] = [];
        }
        this.events[p1].include( p2 );

        this.main.addEvent(p1, p2);
        return this;
    },

    removeEvent: function (p1, p2) {
        if( this.events[p1] ){
            this.events[p1].erase(p2);
        }

        this.main.removeEvent(p1, p2);
        return this;
    },

    fireEvent: function (p1) {
        this.main.fireEvent(p1);
        return this;
    },

    getParent: function (p1) {
        this.main.getParent(p1);
        return this;
    },

    removeEvents: function (p1) {
        this.main.removeEvents(p1);
    },

    set: function (p1, p2) {
        this.main.set(p1, p2);
        return this;
    },

    focus: function () {
        this.main.focus();
    },

    startTip: function (pText) {
        if (!this.toolTip) {
            this.toolTip = new ka.Tooltip(this.main, pText);
        }

        this.toolTip.setText(pText);
        this.toolTip.show();
    },

    stopTip: function (pText) {
        this.toolTip.stop(pText);
    },

    show: function () {
        this.main.setStyle('display', 'inline-block');
    },

    hide: function () {
        this.main.setStyle('display', 'none');
    },

    isHidden: function(){
        return this.main.getStyle('display') == 'none';
    },

    activate: function () {
        Object.each(this.events, function(events,id){
            Array.each(events, function(event){

                this.main.addEvent(id, event);

            }.bind(this));
        }.bind(this));
        this.main.removeClass('ka-Button-deactivate');
    },

    deactivate: function () {
        this.main.removeEvents();
        this.main.addClass('ka-Button-deactivate');
    }
});
