var admin_files = new Class({
    
    initialize: function( pWindow ){
        this.win = pWindow;   
        this.kaFiles = new ka.files( this.win, this.win.content, {
            withSidebar: true
        });
    }
});
