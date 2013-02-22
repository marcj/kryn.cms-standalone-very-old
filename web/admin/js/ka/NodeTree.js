ka.NodeTree = new Class({
    Extends: ka.ObjectTree,

    addRootIcon: function(pItem, pA){

        pA.icon = new Element('span', {
            style: 'font-family: Icomoon; font-size: 16px; position: relative; vertical-align: middle; left: -1px',
            html: '&#xe182;'
        }).inject(pA, 'top');

    },

    addItemIcon: function(pItem, pA){


        var icon = '&#xe21f;'; //&#xe33b;
        var icon = '&#xe20e;'; //&#xe33b;

        if (pItem.type == 1) icon = '&#xe413;'; //link
        if (pItem.type == 2) icon = '&#xe223;'; //folder
        if (pItem.type == 3) icon = '&#xe27d;'; //deposit


        pA.icon = new Element('span', {
            'class': 'ka-objectTree-item-masks',
            style: 'font-family: Icomoon; font-size: 16px;',
            html: icon
        }).inject(pA, 'top');


        if ((pItem.type == 0 || pItem.type == 1) && pItem.visible == 0) {
            //pA.icon.setStyle('color', 'silver');
            pA.setStyle('color', '#999');
        }

        if (pItem.accessDenied == 1) {
            pA.icon.setStyle('color', 'red');
        }

        if (pItem.type == 0 && pItem.accessFromGroups != "" && typeOf(pItem.accessFromGroups) == 'string') {
            new Element('span', {
                'class': 'ka-objectTree-item-masks',
                style: 'font-family: Icomoon; font-size: 12px;position: absolute; left: -3px; bottom: -3px; color: green; text-shadow: 1px -1px 1px white;',
                html: '&#xe3ff;'
            }).inject(pA.icon, 'top');
        }


    }

});