ka.Progress = new Class({

    value: 0,
    autoTitle: false,

    initialize: function( pTitle, pUnlimited ){
    
        this.main = new Element('div', {
            'class': 'ka-progress',
            html: pTitle
        });
        
        if( pUnlimited )
            this.main.addClass('ka-progress-unlimited');
        else {
            this.progress = new Element('div', {
                'class': 'ka-progress-bar'
            }).inject( this.main );
        }
        
        this.text = new Element('div', {
            'class': 'ka-progress-text',
            html: pTitle
        }).inject( this.main );
        
        if( !pTitle ){
            this.autoTitle = true;
        }
    },
    
    setValue: function( pValue ){
        if( !pValue ) pValue = 0;
        if( pValue > 100 ) pValue = 100;
    
        this.value = pValue;
        this.progress.setStyle('width', pValue+'%');
        
        if( this.autoTitle ){
            this.setText( pValue+'%' );
        }
    },
    
    getValue: function(){
        return this.value;
    },
    
    setText: function( pTitle ){
        this.text.set('html', pTitle);
    },
    
    toElement: function(){
        return this.main;
    }

});