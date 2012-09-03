ka.FieldTypes.UsersPicture = new Class({
    
    Extends: ka.FieldAbstract,
    
    createLayout: function(){
        
        this.value = '';
        
        this.main = new Element('div', {
            style: 'text-align: center; width: 100px; position: relative; border: 1px solid gray;background-color: #f8f8f8; height: 100px;'
        }).inject(this.fieldInstance.fieldPanel);
        
        this.noPic();
    },
    
    noPic: function(){

        new Element('div', {
            text: _('No picture chosen.'),
            style: 'padding-top: 30px; color: gray;'
        }).inject( this.main );
        
        new ka.Button(_('Choose'))
        .addEvent('click', this.choose.bind(this))
        .inject( this.main );
        
    },
    
    choose: function(){
        
        var _this = this;
        ka.wm.openWindow( 'admin', 'backend/chooser', null, -1, {onChoose: function( pValue ){
            _this.setValue( pValue, true );
            this.win.close();//close paes/chooser windows -> onChoose.bind(this) in chooser-event handler
        },
        value: this._value,
        cookie: this.options.cookie,
        domain: this.options.domain,
        display: this.options.display,
        opts: {upload: 1, files:1}});
        
    },
    
    getValue: function(){
        return this.value;
    },
    
    setValue: function( pValue, pRender ){
        if( !pValue || pValue == '' ) return;
        
        this.main.empty();
        
        this.main.set('text', _('Resizing ...'));
        new Request.JSON({url: _path+'admin/users/users/resizeImg', noCache: 1, onComplete: function(res){
            
            this.main.empty();
            new Element('img', {
                src: _path+res,
                width: 100,
                height: 100
            }).inject( this.main );

            var div = new Element('div', {
                style: 'position: absolute; bottom: 5px; left: 5px; right: 5px; text-align: center'
            }).inject( this.main );

            new ka.Button(_('Choose'))
            .addEvent('click', this.choose.bind(this))
            .inject( div );
            
        }.bind(this)}).post({path: pValue});
            
        this.value = pValue;
        
    },
    
    highlight: function(){
        this.main.highlight();
    }
    
});