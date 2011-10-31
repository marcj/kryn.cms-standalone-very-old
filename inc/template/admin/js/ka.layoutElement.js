ka.layoutElement = new Class({

    Implements: ka.Base,
    
    layoutBoxes: {},
    
    
    initialize: function( pContainer, pInitialTemplate, pWin ){

        this.container = pContainer;
        this.layout = this.container;
        this.win = pWin;
        
        logger( pInitialTemplate );
        logger( this.layout );
        
        if( pInitialTemplate )
            this.loadTemplate( pInitialTemplate );
        else
            this.fetchSlots();
        
    },
    
    getValue: function(){
        var res = {};
        Object.each(this.layoutBoxes, function(layoutBox, boxId){
            res[ boxId ] = layoutBox.getValue();
        }.bind(this));
        return res;
    },
    
    setValue: function( pVal ){
        
        this.setThisValue = pVal;
        //logger("setValue");
        //logger(pVal);
        
        if( this.loadingDone )
            this._setValue();
        
    },
    
    _setValue: function(){
        if( this.setThisValue ){
            
            Object.each(this.layoutBoxes, function(layoutBox, boxId){
                layoutBox.clear();
                layoutBox.setContents( this.setThisValue[boxId] );
            }.bind(this));
            
        }
    },
    
    loadTemplate: function( pTemplate ){
        
        if( this.template == pTemplate ) return;
        
        this.template = pTemplate;
        
        this.layout.empty();
        
        this.mkTable( this.layout ).set('height', '100%');
        this.mkTr();
        var td = this.mkTd().set('align', 'center').set('valign', 'center');
        
        new Element('img', {
             src: _path+'inc/template/admin/images/ka-tooltip-loading.gif'
        }).inject(td);

        this.loadingDone = false;

        new Request.JSON({
            url: _path+'admin/backend/loadLayoutElementFile/',
            noCache: 1,
            onComplete: this.renderLayout.bind(this)
        }).post({template: pTemplate});
        
    },
    
    deselectAll: function(  ){
        
        if( !this.layoutBoxes ) return;
        
        Object.each(this.layoutBoxes, function(box,id){
            box.deselectAll();
        });
        
    },
    
    renderLayout: function( pTemplate ){

        if( ! pTemplate || !pTemplate.layout ) return;
        
        this.layout.set('html', pTemplate.layout);
        
        this.fetchSlots();
    },
    
    fetchSlots: function(){
        
        this.layoutBoxes = this.renderLayoutElements( this.layout );
        
        this.loadingDone = true;
        this._setValue();
    
    },
    
    renderLayoutElements: function( pDom ){

        var layoutBoxes = {};
        pDom.getWindow().$$('.kryn_layout_content, .kryn_layout_slot').each(function(item){
           
            var options = {}; 
            if( item.get('params') )
                var options = JSON.decode(item.get('params'));
    
            if( item.hasClass('kryn_layout_slot') )
                layoutBoxes[ options.id ] = new ka.layoutBox( item, options, this ); //options.name, this.win, options.css, options['default'], this, options );
            else
                layoutBoxes[ options.id ] = new ka.contentBox( item, options, this );
    
        }.bind(this));
        
        return layoutBoxes;
    }
    
});