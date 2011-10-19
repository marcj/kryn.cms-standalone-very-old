ka.Progress = new Class({

    initialize: function( pProcent, pTitle ){
    
        this.procent = pProcent;
    
        this.main = new Element('div', {
            'class': 'ka-progress',
            html: pTitle
        });
        
        if( typeOf(pProcent) == 'null' || pProcent === false )
            this.main.addClass('ka-progress-unlimited');
    },
    
    setText: function( pTitle ){
        this.main.set('html', pTitle);
    },
    
    toElement: function(){
        return this.main;
    }

});