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
            
        this.saveNoClose = new ka.Button(_('Add'))
        .addEvent('click', function(){
            this._save(true);
        }.bind(this))
        .inject( this.actions );
        
        if( this.win.params && this.win.params.relation_params ){
	        Object.each(this.win.params.relation_params, function(value,id){
	            if( this.fields[ id ] )
                    this.fields[ id ].setValue( value );
	        }.bind(this));
        }
            
        if( this.actionsNaviDel )
            this.actionsNaviDel.hide();
            
        if( this.previewBtn )
            this.previewBtn.hide();
            
        var first = this.win.content.getElement('input[type=text]');
        if( first )
            first.focus();
    }
});
