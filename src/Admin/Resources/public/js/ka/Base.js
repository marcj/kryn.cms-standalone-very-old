ka.Base = new Class({
    Implements: [Events, Options],

    toElement: function() {
        return this.main;
    },

    getWin: function() {
        var win = this.toElement().getParent('.kwindow-border');
        if (win) {
            return win.windowInstance;
        }
    },

    inject: function(target, position) {
        this.toElement.inject(target, position);
        return this;
    },

    setStyles: function(styles){
        this.toElement().setStyles(styles);
        return this;
    },

    setStyle: function(key, value){
        this.toElement().setStyle(key, value);
        return this;
    },

    getStyle: function(key){
        this.toElement().getStyle(key);
        return this;
    },

    mkTable: function (pTarget) {
        if (pTarget) {
            this.oldTableTarget = pTarget;
        }

        if (!pTarget && this.oldTableTarget) {
            pTarget = this.oldTableTarget;
        }

        var table = new Element('table', {width: '100%'}).inject(pTarget);
        new Element('tbody').inject(table);
        this.setTable(table);
        return table;
    },

    setTable: function (pTable) {
        this.baseCurrentTable = pTable;
        this.baseCurrentTBody = pTable.getElement('tbody');
    },

    mkTr: function () {
        this.currentTr = new Element('tr').inject(this.baseCurrentTBody);
        return this.currenTr;
    },

    mkTd: function (pVal) {
        var opts = {};
        if (typeOf(pVal) == 'string') {
            opts.html = pVal;
        }
        return new Element('td', opts).inject(this.currentTr);
    }

});