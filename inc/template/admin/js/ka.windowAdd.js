ka.windowAdd = new Class({
    Extends: ka.windowEdit,
    initialize: function( pWin, pContainer ){
        this.windowAdd = true;
        this.parent(pWin, pContainer);
    },
    loadItem: function(){
        //ist in render() am ende also lösche unnötigen balast
        this.loader.hide();
        if( this.saveNoClose )
            this.saveNoClose.hide();
            
        
        if( this.actionsNaviDel )
            this.actionsNaviDel.hide();
            
        if( this.previewBtn )
            this.previewBtn.hide();
            
        var first = this.win.content.getElement('input[type=text]');
        if( first )
            first.focus();
    }
});
