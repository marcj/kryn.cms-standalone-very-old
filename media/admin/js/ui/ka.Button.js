ka.Button = new Class({

    $eventsBackuper: false,

    initialize: function (pTitle, pOnClick, pTooltip) {
        this.main = new Element('a', {
            'class': 'ka-Button',
            href: '#',
            onclick: "return false;",
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

    setButtonStyle: function(pStyle){
        if (this.oldButtonStyle)
            this.main.removeClass('ka-Button-'+oldButtonStyle);

        this.main.addClass('ka-Button-'+pStyle);
        this.oldButtonStyle = pStyle;
        return this;
    },

    setEnabled: function(pEnabled){

        this.enabled = pEnabled;

        if (this.enabled){
            
            //add back all events
            if (this.$eventsBackuper)
                this.main.cloneEvents(this.$eventsBackuper);
            
            this.main.removeClass('ka-Button-deactivate');
        } else {

            this.$eventsBackuper = new Element('span');
            //backup all events and remove'm.
            this.$eventsBackuper.cloneEvents(this.main);
            this.main.removeEvents();

            this.main.addClass('ka-Button-deactivate');
        }

    },

    toElement: function () {
        return this.main;
    },

    inject: function(pTarget, pWhere){
        this.main.inject(pTarget, pWhere);
        return this;
    },

    addEvent: function(pType, pFn){
        this.main.addEvent(pType, pFn);
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
        this.setEnabled(true);
    },

    deactivate: function () {
        this.setEnabled(false);
    }
});
