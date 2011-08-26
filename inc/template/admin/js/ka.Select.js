ka.Select = new Class({
    Implements: Events,

    arrow: 'inc/template/admin/images/icons/tree_minus.png',
    
    opened: false,
    value: null,
    
    items: {},
    
    a: {},
    
    initialize: function(){
    
        this.box = new Element('div', {
            'class': 'ka-Select-box'
        })
        .addEvent('click', this.toggle.bindWithEvent(this));
        
        this.title = new Element('div', {
            'class': 'ka-Select-box-title'
        })
        .addEvent('mousedown', function(e){ e.preventDefault(); })
        .inject( this.box );
        
        this.arrowBox = new Element('div', {
            'class': 'ka-Select-arrow'
        })
        .inject( this.box );
        
        this.arrow = new Element('img', {
            src: _path+this.arrow,
        }).inject( this.arrowBox );
        
        this.chooser = new Element('div', {
            'class': 'ka-Select-chooser ka-normalize'
        });
        
        this.chooser.addEvent('click', function(e){
            e.stop();
        });
        
        var target = document.body;
        if( this.box.getParent('.kwindow-border') ){
            target = this.box.getParent('.kwindow-border');
        }
        this.chooser.inject( target );
        
        this.chooser.setStyle('display', 'none');
        
    },
    
    inject: function( p, p2 ){
        this.box.inject( p, p2 );
        var target = p.getWindow().document.body;
        if( this.box.getParent('.kwindow-border') ){
            target = this.box.getParent('.kwindow-border');
        }
        this.chooser.inject( target );
        this.chooser.getWindow().document.body.addEvent('click', this.close.bind(this));
        return this;
    },
    
    destroy: function(){
        this.chooser.destroy();
        this.box.destroy();
        this.chooser = null;
        this.box = null;
    },
    
    addSeparator: function( pLabel ){
        
        new Element('a', {
            html: pLabel,
            href: 'javascript:;',
            'class': 'ka-Select-separator'
        })
        .inject( this.chooser );
    
    },
    
    add: function( pId, pLabel ){
    
        this.items[ pId ] = pLabel;
        
        this.a[pId] = new Element('a', {
            html: pLabel,
            href: 'javascript:;'
        })
        .addEvent('click', function(){
            
            this.setValue( pId, true);
            this.close();
            
        }.bind(this))
        .inject( this.chooser );
        
        
        if( this.value == null ) {
            this.setValue( pId );
        }
        
    },
    
    setStyle: function( p, p2 ){
        this.box.setStyle( p, p2 );
    },
    
    empty: function(){
    
        this.items = {};
        this.value = null;
        this.title.set('html', '');
        this.chooser.empty();
    
    },
    
    setValue: function( pValue, pEvent ){
        
        if( !this.items[ pValue ] ) return false;
        
        this.value = pValue;
        this.title.set('html', this.items[ pValue ]);
        this.box.set('title', (this.items[ pValue ]+"").stripTags() );
        
        Object.each(this.a, function(item,id){
            item.removeClass('active');
            if( id == pValue && pValue != '' ){
                item.addClass('active');
            }
        });
        
        //chrome rendering bug
        this.arrowBox.setStyle('right', 5);
        (function(){
            this.arrowBox.setStyle('right', 0);
        }.bind(this)).delay(10);
        
        if( pEvent )
            this.fireEvent('change', pValue);
            
        return true;
    },
    
    getValue: function(){
        return this.value;
    },
    
    toggle: function( e ){
        if( e && e.stop ){
            e.stop();
        }
        if( this.opened == true )
            this.close();
        else {
            if( e && e.stop ){
                this.chooser.getWindow().document.body.fireEvent('click');
            }
            this.open();
        }
    },
    
    open: function(){
        this.chooser.setStyle('display', 'block');
        this.chooser.getWindow().document.body.addEvent('click', this.close.bind(this));
        
        this.chooser.position({
            relativeTo: this.box,
            position: 'bottomRight',
            edge: 'upperRight'
        });
        
        var pos = this.chooser.getPosition();
        var size = this.chooser.getSize();
        
        var bsize = this.chooser.getWindow().getSize( $('desktop') );
        var wscroll = this.chooser.getWindow().getScroll();
        
        if( size.y+pos.y > bsize.y+wscroll.y )
            this.chooser.setStyle('height', (bsize.y+wscroll.y)-pos.y-10);
    
        //new ka.blocker( this.chooser ).addEvent('click', this.close.bind(this));
        
        this.opened = true;
    
    },
    
    close: function(){
    
        this.opened = false;
        if( this.chooser )
            this.chooser.setStyle('display', 'none');
    
    },
    
    toElement: function(){
        return this.box;
    }

});