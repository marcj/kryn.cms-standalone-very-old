ka.Layout = new Class({


    options: {

        /**
         * Defines how the layout should look like.
         *
         * [x, y]
         *
         * @var {ArraY}
         */
        layout: [1,1],


        /**
         * `horizontal` or `vertical`.
         * @var {String}
         */
        mode: 'horizontal'
    },


    initialize: function(pContainer, pOptions){

        this.setOptions(pOptions);


    }







});