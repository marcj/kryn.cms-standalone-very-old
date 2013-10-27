ka.ui = ka.ui || {};

ka.ui.ImageCrop = new Class({

    initialize: function(container) {
        this.container = container;
        this.createLayout();
    },

    toElement: function() {
        return this.main;
    },

    createLayout: function() {
        this.main = Element('div', {
            'class': 'ka-ui-ImageCrop ka-Full'
        });

        Array.each(['top', 'right', 'bottom', 'left'], function(direction) {
            var name = 'overlay' + direction.ucfirst();
            this[name] = new Element('div', {
                'class': 'ka-ui-ImageCrop-overlay ka-ui-ImageCrop-' + name,
                styles: {
                    opacity: 0.9
                }
            }).inject(this.main);
        }.bind(this));
        Array.each(['top', 'right', 'bottom', 'left'], function(direction) {
            var name = 'overlay' + direction.ucfirst();
            this[name] = new Element('div', {
                'class': 'ka-ui-ImageCrop-overlay ka-ui-ImageCrop-' + name,
                styles: {
                    opacity: 0.9
                }
            }).inject(this.main);
        }.bind(this));

        this.win = new Element('div', {
            'class': 'ka-ui-imageCrop-window',
            styles: {
                height: '200px',
                width: '200px'
            }
        }).inject(this.main);
        this.main.inject(this.container);

        this.win.position({
            relativeTo: this.main
        });

        this.setupSizer();

        this.mover = new Drag.Move(this.win, {
            container: this.main,
            onStart: function() {
                this.canvasSize = this.main.getSize();
            }.bind(this),
            onDrag: this.updateOverlay.bind(this)
        });

        this.updateOverlay();
    },

    setupSizer: function() {
        this.sizer = {};

        ['n', 'e', 's', 'w', 'ne','se', 'sw', 'nw'].each(function(item) {
            this.sizer[item] = new Element('div', {
                'class': 'ka-ui-ImageCrop-sizer ka-ui-ImageCrop-sizer-' + item
            }).inject(this.win);
        }.bind(this));

        this.win.dragX = 0;
        this.win.dragY = 0;

        var canvasSize = this.canvasSize || this.main.getSize();

        var minWidth = 50;
        var minHeight = 50;

        Object.each(this.sizer, function(item, key) {
            var height, width, x, y, newHeight, newWidth, newY, newX, max;

            var options = {
                handle: item,
                style: false,
                preventDefault: true,
                stopPropagation: true,
                modifiers: {
                    x: !['s', 'n'].contains(key) ? 'dragX' : null,
                    y: !['e', 'w'].contains(key) ? 'dragY' : null
                },
                snap: 0,
                onBeforeStart: function(pElement) {
                    pElement.dragX = 0;
                    pElement.dragY = 0;
                    height = pElement.getStyle('height').toInt();
                    width = pElement.getStyle('width').toInt();
                    y = pElement.getStyle('top').toInt();
                    x = pElement.getStyle('left').toInt();

                    newWidth = newHeight = newY = newX = null;

                    max = canvasSize;
                },
                onDrag: function(pElement, pEvent) {

                    if (key === 'n' || key == 'ne' || key == 'nw') {
                        newHeight = height - pElement.dragY;
                        newY = y + pElement.dragY;
                    }

                    if (key === 's' || key == 'se' || key == 'sw') {
                        newHeight = height + pElement.dragY;
                    }

                    if (key === 'e' || key == 'se' || key == 'ne') {
                        newWidth = width + pElement.dragX;
                    }

                    if (key === 'w' || key == 'sw' || key == 'nw') {
                        newWidth = width - pElement.dragX;
                        newX = x + pElement.dragX;
                    }

                    if (newWidth !== null && (newWidth > max.x || newWidth < minWidth)) {
                        newWidth = newX = null;
                    }

                    if (newHeight !== null && (newHeight > max.y || newHeight < minHeight)) {
                        newHeight = newY = null;
                    }

                    if (newX !== null && newX > 0) {
                        pElement.setStyle('left', newX);
                    }

                    if (newY !== null && newY > 0) {
                        pElement.setStyle('top', newY);
                    }

                    if (newWidth !== null) {
                        pElement.setStyle('width', newWidth);
                    }

                    if (newHeight !== null) {
                        pElement.setStyle('height', newHeight);
                    }


                    if (newWidth !== null || newHeight !== null || newX !== null || newY !== null) {
                        this.updateOverlay();
                    }
                }.bind(this)
            };

            new Drag(this.win, options);
        }.bind(this));
    },

    updateOverlay: function() {
        var coordinates = this.win.getCoordinates(this.main);
        //        var size          = this.win.getSize();
        var canvasSize = this.canvasSize || (this.canvasSize = this.main.getSize());

        //
        //        console.log(coordinates);
        //        console.log(canvasSize, size);
        this.overlayTop.setStyle('height', coordinates.top);
        this.overlayBottom.setStyle('height', canvasSize.y - coordinates.bottom);

        this.overlayLeft.setStyles({
            width: coordinates.left,
            top: coordinates.top,
            bottom: canvasSize.y - coordinates.bottom
        });
        this.overlayRight.setStyles({
            width: canvasSize.x - coordinates.right,
            top: coordinates.top,
            bottom: canvasSize.y - coordinates.bottom
        });
    }
});