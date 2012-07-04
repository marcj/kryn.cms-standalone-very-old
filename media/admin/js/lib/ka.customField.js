/**
 *
 * ka.CustomField - The mother class of a custom ka.Field.
 *
 *
 * If your class depends on another class of your project or
 * of another extension, please surround your whole class in this:
 * window.addEvent('init', function(){
 *
 *    //your class definition here
 *
 * });
 *
 */



ka.CustomField = new Class({

    Implements: [Events, Options],

    main: null,

    options: {

        type: ''

    },

    refs: {
        win: false
    },

    fieldContainer: false,

    /**
     * Example of a constructor.
     *
     */
    initialize: function (pOptions, pFieldContainer, pRefs) {

        this.setOptions(pOptions);
        if (pRefs && pRefs.win) this.refs.win = pRefs; //ref to the current window object

        this.fieldContainer = pFieldContainer;
        this.main = new Element('div');

        this._createLayout();
    },


    /**
     * render your field, create all necessary stuff and inject to this.main
     */
    _createLayout: function () {


    },

    /**
     * Return here you root dom object, which will then be injected in the ka.field wrapper.
     */
    toElement: function(){
        this.main;
    },


    /**
     * The mother class calls this function when your field should display a new value.
     *
     */
    setValue: function (pValue) {
    },

    /**
     * The mother class calls this function when he want to know the value of your field.
     */
    getValue: function (pValue) {
    },

    /**
     * The mother class calls this function to detect whether this field is empty or not. (Only if pField.empty is false)
     * Please make sure, that this function returns false or true.
     *
     */
    isEmpty: function () {
    },

    /**
     * The mother class calls this function as a visually notification if something wrong in the value.
     */
    highlight: function () {
    },

    /**
     *
     */
    setEnabled: function(pEnabled){

    }

});