ka.LayoutSplitter = new Class({

    Implements: [Events, Options],

    options: {
        container: null,
        min: null
    },

    direction: 'left',
    cell: null,


    initialize: function(pCell, pDirection, pOptions){

        this.setOptions(pOptions);

        this.cell = pCell;
        this.direction = pDirection;

        if (!this.options.container)
            this.options.container = pCell;

        this.renderLayout();

        this.mapEvent();

    },

    renderLayout: function(){

        this.main = new Element('div', {
            'class': 'ka-Splitter-main'
        }).inject(this.options.container);

        this.main.addClass('ka-Splitter-main-'+this.direction.toLowerCase());


    },


    mapEvent: function(){

        var diffX = 0;

        /*var drag = new Drag(this.main, {
         onStart: function(element){
         this.startPosition = this.main.getPosition(this.cell);
         }.bind(this),
         onDrag: function(element){
         diffX = this.main.getPosition(this.cell).x - this.startPosition.x;
         this.cell.setStyle('width', this.cell.getStyle('width').toInt() + diffX);
         }.bind(this)
         });*/

        var map = {left: 'w', right: 'e', 'top': 'n', bottom: 's'};

        var key = map[this.direction.toLowerCase()];

        if (!key) key = this.direction.toLowerCase();

        var height, width, x, y, newHeight, newWidth, newY, newX, max;
        var minWidth = this.options.min ? this.options.min : 5;
        var minHeight = this.options.min ? this.options.min : 5;

        var self = this;

        var options = {
            handle: this.main,
            style: false,
            modifiers: {
                x: !['s', 'n'].contains(key)?'dragX':null,
                y: !['e', 'w'].contains(key)?'dragY':null
            },
            snap: 0,
            onBeforeStart: function(pElement){
                pElement.dragX = 0;
                pElement.dragY = 0;
                height = pElement.getStyle('height').toInt();
                width  = pElement.getStyle('width').toInt();
                y  = pElement.getStyle('top').toInt();
                x  = pElement.getStyle('left').toInt();

                newWidth = newHeight = newY = newX = null;

                max = ka.adminInterface.getDesktop().getSize();
            },
            onDrag: function(pElement){

                if (key === 'n' || key == 'ne' || key == 'nw'){
                    newHeight = height-pElement.dragY;
                    newY = y+pElement.dragY;
                }

                if (key === 's' || key == 'se' || key == 'sw')
                    newHeight = height+pElement.dragY;

                if (key === 'e' || key == 'se' || key == 'ne')
                    newWidth = width+pElement.dragX;

                if (key === 'w' || key == 'sw' || key == 'nw'){
                    newWidth = width-pElement.dragX;
                    newX = x+pElement.dragX;
                }

                if (newWidth !== null && (newWidth > max.x || newWidth < minWidth) )
                    newWidth = newX = null;

                if (newHeight !== null && (newHeight > max.y || newHeight < minHeight))
                    newHeight = newY = null;

                if (newX !== null && newX > 0)
                    pElement.setStyle('left', newX);

                if (newY !== null && newY > 0)
                    pElement.setStyle('top', newY);

                if (newWidth !== null)
                    pElement.setStyle('width', newWidth);

                if (newHeight !== null)
                    pElement.setStyle('height', newHeight);

                self.cell.fireEvent('resize');

            }

        };

        new Drag(this.cell.get('tag') == 'td' ? this.cell : this.cell.getParent('td'), options);


    }
});