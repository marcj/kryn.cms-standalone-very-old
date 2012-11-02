
ka.FieldTypes = {};

ka.Field = new Class({

    Implements: [Options, Events],

    Binds: ['fireChange'],

    options: {

        value: null,

        small: 0,

        label: null,
        type: 'text',

        tableItem: false, //use TR as parent instead of div
        help: null,

        startEmpty: false,
        fieldWidth: null,

        'default': null,
        designMode: false,
        disabled: false,

        invisible: false,

        noWrapper: false //doesnt include the ka-field wrapper (title, description, etc), and inject the field controls directly to pContainer with just a single div around it.
    },

    handleChildsMySelf: false, //defines whether this object handles his child visibility itself

    field: {},
    refs: {},
    id: '',
    depends: {},
    childContainer: false,
    container: false,

    children: {},

    initialize: function (pField, pContainer, pFieldId) {

        this.id = pFieldId;

        if (pField.type == 'predefined'){
            var definition = ka.getObjectDefinition(pField.object);
            this.field = Object.clone(definition.fields[pField.field]);

            if (pField.label)
                this.field.label = pField.label;

        } else {
            this.field = Object.clone(pField);
        }

        this.setOptions(this.field);
        this.container = pContainer;

        if (this.options.noWrapper){

            if (this.field.tableItem) {

                this.tr = new Element('tr', {
                    'class': 'ka-Field'
                });
                
                this.tr.instance = this;

                this.tr.store('ka.Field', this);

                this.main = new Element('td', {
                    colspan: 2
                }).inject(this.tr);

                this.tr.inject(pContainer || document.hidden);
            } else {

                this.main = new Element('div', {'class': 'ka-Field'}).inject(pContainer);
                this.main.instance = this;
                this.main.store('ka.Field', this);
            }

            this.fieldPanel = this.main;

        } else {

            if (this.field.tableItem) {
                this.tr = new Element('tr', {
                    'class': 'ka-Field ka-field-main'
                });
                this.tr.instance = this;
                this.tr.store('ka.Field', this);

                this.title = new Element('td', {
                    'class': 'ka-field-tdtitle selectable',
                    width: (this.field.tableitem_title_width) ? this.field.tableitem_title_width : '40%'
                }).inject(this.tr);

                this.main = new Element('td', {
                    'class': 'ka-Field-inputTd'
                }).inject(this.tr);

                if (pContainer){
                    if (pContainer.get('tag') != 'table' && pContainer.get('tag') != 'tbody'){
                        var autotable = pContainer.getLast('.ka-Field-autotable');
                        if (autotable){
                            pContainer = autotable;
                        } else {
                            pContainer = new Element('table', {'class': 'ka-Field-autotable', width: '100%'})
                            .inject(pContainer);
                        }
                    }
                }

                this.tr.inject(pContainer || document.hidden);

            } else {
                this.main = new Element('div', {
                    'class': 'ka-Field ka-field-main'
                });
                this.main.instance = this;
                this.main.store('ka.Field', this);

                if (this.field.small) {
                    this.main.set('class', 'ka-field-main ka-field-main-small');
                }

                this.title = new Element('div', {
                    'class': 'ka-field-title selectable'
                }).inject(this.main);

                this.main.inject(pContainer || document.hidden);
            }


            if (this.field.label) {
                this.titleText = new Element('div', {
                    'class': 'title',
                    html: this.field.label
                }).inject(this.title);
            }

            if (this.field.help && this.titleText) {
                new Element('img', {
                    src: _path + PATH_MEDIA + '/admin/images/icons/help_gray.png',
                    width: 14,
                    style: 'float: right; cursor: pointer; position: relative; top: -1px;',
                    title: _('View help to this field'),
                    styles: {
                        opacity: 0.7
                    }
                }).addEvent('mouseover',function () {
                    this.setStyle('opacity', 1);
                }).addEvent('mouseout',function () {
                    this.setStyle('opacity', 0.7);
                }).addEvent('click',function () {
                    ka.wm.open('admin/help', {id: this.field.help});
                }.bind(this)).inject(this.titleText);
            }

            if (this.field.desc) {
                this.descText = new Element('div', {
                    'class': 'desc',
                    html: this.field.desc
                }).inject(this.title);
            }

            this.fieldPanel = new Element('div', {
                'class': 'ka-field-field'
            }).inject(this.main);
        }

        if (this.options.fieldWidth)
            this.fieldPanel.setStyle('width', this.options.fieldWidth);

        if (this.options.fieldWidth && typeOf(this.options.fieldWidth) == 'string' && this.options.fieldWidth.indexOf('%') > 0){
            this.fieldPanel.addClass('ka-field-field-without-margin');
        }


        if (this.field.invisible == 1) {
            this.main.setStyle('display', 'none');
        }

        this.findWin();

        this.renderField();

        if (!this.field.startEmpty && typeOf(this.field.value) != 'null') {
            this.fieldObject.setValue(this.field.value, true);
        } else if (typeOf(this.field['default']) != 'null'){
            this.fieldObject.setValue(this.field['default'], true);
        }

        if (this.options.disabled){
            this.fieldObject.setDisabled(true);
        }

    },

    renderField: function () {

        this.options.type = this.options.type?this.options.type:'text';
        var clazz = ka.FieldTypes[this.options.type] || ka.FieldTypes[this.options.type.capitalize()];

        if (clazz){
            this.fieldObject = new clazz(this, this.options);
        } else {
            throw 'The ka.Field type '+this.options.type+' is not available.';
        }

        return;

        if (this.field.type)
            this.field.type = this.field.type.toLowerCase();


        switch (this.field.type) {
            case 'password':
                this.renderPassword();
                break;
            case 'select':
                this.renderSelect();
                break;
            case 'textlist':
                this.renderTextlist();
                break;
            case 'textarea':
                this.renderTextarea();
                break;
            case 'array':
                this.renderArray();
                break;
            case 'wysiwyg':
                this.renderWysiwyg();
                break;
            case 'date':
                this.renderDate();
                break;
            case 'datetime':
                this.renderDate({time: true});
                break;
            case 'checkbox':
                this.renderCheckbox();
                break;
            case 'file':
            case 'filechooser':

                this.field.withoutObjectWrapper = 1;
                this.field.objectOptions = {
                    returnPath: 1,
                    onlyLocal: 1
                };

                this.renderChooser(['file']);
                break;
            case 'pagechooser':
            case 'page':
            case 'node':
                this.renderChooser(['node']);
                break;
            case 'object':
                this.renderChooser(typeOf(this.field.object)=='array'?this.field.object:[this.field.object]);
                break;
            case 'chooser':
                this.renderChooser();
                break;
            case 'filelist':
                this.renderFileList();
                break;
            case 'multiupload':
                this.initMultiUpload();
                break;
            case 'layoutelement':
                this.initLayoutElement();
                break;
            case 'headline':
                this.renderHeadline();
                break;
            case 'info':
                this.renderInfo();
                break;
            case 'label':
                this.renderLabel(true);
                break;
            case 'html':
                this.renderLabel();
                break;
            case 'imagegroup':
                this.renderImageGroup();
                break;
            case 'custom':
                this.renderCustom();
                break;
            case 'integer':
            case 'number':
                this.renderNumber();
                break;
            case 'childrenswitcher':
                this.renderChildrenSwitcher();
                break;
            case 'checkboxgroup':
                this.renderCheckboxGroup();
                break;
            case 'windowlist':
                this.renderWindowList();
                break;
            case 'fieldtable':
                this.renderFieldTable();
                break;
            case 'codemirror':
                this.renderCodemirror();
                break;
            case 'condition':
                this.renderCondition();
                break;
            case 'objectcondition':

                this.renderCondition({
                    object: this.field.object
                });

                break;
            case 'fieldcondition':

                this.renderCondition({
                    object: this.options.object,
                    field: this.options.field
                });

                break;
            case 'lang':
            case 'language':

                this.field.items = {}
                Object.each(ka.settings.langs, function (lang, id) {
                    this.field.items[id] = lang.langtitle + ' (' + lang.title + ', ' + id + ')';
                }.bind(this));

                if (this.options.multi)
                    this.renderTextlist();
                else
                    this.renderSelect();

                break;

            case 'text':
            default:
                this.renderText();
                break;
        }
        if (this.input) {

            /*
            if (this.field.length + 0 > 0) {
                this.input.setStyle('width', (this.field.length.toInt() * 9));
            }
            */

            this.input.store('oldClass', this.input.get('class'));
        }
    },

    /**
     * Highlights the field.
     *
     */
    highlight: function () {
        this.fieldObject.highlight();
    },

    /**
     * Detects if the entered data is valid.
     *
     * This means:
     *  - if options.required==true and the user entered a value
     *  - if options.requiredRegex and the value passes the regex
     *
     * @return {Boolean}
     */
    isValid: function () {
        var ok = true;

        if (this.isHidden()) return ok;

        ok = this.fieldObject.isValid();

        return ok;
    },

    showNotValid: function(pText){
        this.fieldObject.showNowValid(pText);
    },

    showValid: function(){
        this.fieldObject.showValid();
    },

    /**
     * Detects if the entered data is valid and shows a visual
     * symbol if not.
     *
     * This means:
     *  - if options.required==true and the user entered a value
     *  - if options.requiredRegex and the value passes the regex
     *
     * @return {Boolean} true if everything is ok
     */
    checkValid: function(){

        var status = this.isValid();
        if (status) this.fieldObject.showValid();
        else this.fieldObject.showNotValid();
        return status;
    },

    /**
     * Returns the value of the field.
     *
     * @return {Mixed}
     */
    getValue: function () {
        return this.fieldObject.getValue();
    },

    /**
     * toString() method.
     *
     * @return {Mixed}
     */
    toString: function () {
        return this.getValue();
    },

    /**
     * Sets the value.
     *
     * @param {Mixed} pValue
     * @param {Boolean} pInternal Fires fireChange() whichs fires the 'change' event. Default is false.
     */
    setValue: function (pValue, pInternal){

        if (typeOf(pValue) == 'null' && this.field['default']) {
            pValue = this.field['default'];
        }

        if (this.fieldObject) {
            this.fieldObject.setValue(pValue, pInternal);
        }

        if (pInternal) {
            this.fireChange();
        } else {
            this.fireEvent('check-depends');
        }
    },

    /**
     * A binded function, that fires 'change', 'check-depends' events and isOk() method.
     *
     */
    fireChange: function(){
        this.fireEvent('change', [this.getValue(), this, this.id]);
        this.fireEvent('check-depends');
        this.checkValid();
    },

    /**
     * Finds the ka-window instance through a DOM lookup.
     *
     * @return {ka.Window} The window instance or null
     */
    findWin: function () {

        if (this.win) return;

        var win = this.toElement().getParent('.kwindow-border');
        if (!win) return;

        this.win = win.retrieve('win');
    },

    /**
     * Creates and injects a children container.
     *
     * @return {Element} The newly created child container, or null if already exist.
     */
    prepareChildContainer: function(){

        if (this.childContainer) return;

        if (this.field.tableItem) {
            var tr = new Element('tr').inject(document.id(this), 'after');
            var td = new Element('td', {colspan: 2, style: 'padding: 0px; border-bottom: 0px;'}).inject(tr);

            this.childContainer = new Element('div', {
                'class': 'ka-fields-sub'
            }).inject(td);

        } else {
            this.childContainer = new Element('div', {
                'class': 'ka-fields-sub'
            }).inject(document.id(this), 'after');
        }

        return this.childContainer;
    },

    /**
     * Returns true if this item has a visibility-condition parent or
     * a parent of a structured ka.Parse object, not a DOM parent.
     * @return {Boolean} [description]
     */
    hasParent: function(){
        return this.parent !== null;
    },

    /**
     * Returns the visibility-condition parent or the parent of a
     * structured ka.Parse object, not the DOM parent.
     *
     * @return {ka.Field}
     */
    getParent: function(){
        return this.parent;
    },

    /**
     * Returns the root element.
     *
     * @return {Element}
     */
    toElement: function(){
        return this.tr || this.main;
    },

    /**
     * Removes the item and the children container from the DOM.
     *
     */
    dispose: function(){

        var field = this.tr || this.main;

        this.oldMainParent = field.getParent();

        field.dispose();

        if (this.childContainer){
            this.oldChildParent = this.childContainer.getParent();
            this.childContainer.dispose();
        }

    },

    getChildrenContainer: function(){
        this.childContainer;
    },

    /**
     * Oposit of dispose(). Injects/Inserts the
     * main element and childContainer back to the origin position.
     *
     * Only works after a call of dispose() (since we need this.oldMainParent
     * and this.oldChildParent)
     *
     */
    insert: function(){

        var field = this.tr || this.main;

        field.inject(this.oldMainParent);

        if (this.childContainer)
            this.childContainer.inject(this.oldChildParent);

    },

    /**
     * Returns the previous ka.Field element in the DOM.
     * @return {ka.Field}
     */
    getPrevious: function(){

        var previous = this.toElement().getPrevious('.ka-Field');

        return previous ? previous.instance : null;
    },

    /**
     * Returns the next ka.Field element in the DOM.
     *
     * @return {ka.Field}
     */
    getNext: function(){

        var next = this.toElement().getNext('.ka-Field');

        return next ? next.instance : null;
    },

    /**
     * Injects the field before pField.
     *
     * @param  {ka.Field} pField
     */
    injectBefore: function(pField){
        this.inject(pField.toElement(), 'before');
    },


    /**
     * Injects the field after pField.
     *
     * @param  {ka.Field} pField
     */
    injectAfter: function(pField){
        this.inject(pField.toElement(), 'after');
    },

    /**
     * Search for a previous ka.Field object and inject before it.
     *
     * @return {ka.Field} The previous ka.Field if found.
     */
    moveUp: function(){

        var previous = this.toElement().getPrevious('.ka-Field');

        if (previous) this.inject(previous.instance, 'before');

        return previous;
    },

    /**
     * Search for a following ka.Field object and inject after it.
     *
     * @return {ka.Field} The following ka.Field if found.
     */
    moveDown: function(){

        var next = this.toElement().getNext('.ka-Field');

        if (next) this.inject(next.instance, 'after');

        return next;
    },

    /**
     * Injects the item incl. children container to pTo
     * @param  {Element} pTo Target element
     * @param  {String}  pP  Can be 'top', 'bottom', 'after', or 'before'. Default is 'bottom'
     * @return {ka.Field}    this
     */
    inject: function (pTo, pP) {

        var field = this.toElement();

        if (instanceOf(pTo, ka.Field) && pP == 'after' && pTo.toElement().get('tag') == 'tr' && pTo.getChildrenContainer()){
            //since in table mode the children container is actually under the ka-Field dom element, we
            //have to assign the pTo to the children container.
            pTo = pTo.getChildrenContainer();
        } else if(instanceOf(pTo, ka.Field)){
            pTo = pTo.toElement();
        }

        if (this.options.tableItem){
            
            var autotable = field.getParent();

            if (autotable.get('tag') == 'tbody')
                autotable = autotable.getParent();

            var tbody = autotable.getChildren('tbody')?autotable.getChildren('tbody'):autotable;

            //maybe we need to move the autotable or create one?
            if (tbody.getChildren().length > 1){
                //yepp, we're not alone in this table, create a new one
                
                autotable = new Element('table', {'class': 'ka-Field-autotable', width: '100%'})
                .inject(pTo, pP);

                if (field.getDocument() != pTo.getDocument())
                    pTo.getDocument().adoptNode(field);

                field.inject(autotable);

            } else {
                //we're alone, move table
                
                if (autotable.getDocument() != pTo.getDocument())
                    pTo.getDocument().adoptNode(autotable);
                
                autotable.inject(pTo, pP);
            }

            if (this.childContainer)
                this.childContainer.inject(field, 'after');


        } else {

            if (field.getDocument() != pTo.getDocument())
                pTo.getDocument().adoptNode(field);

            field.inject(pTo, pP);

        }

        this.findWin();

        return this;
    },

    /**
     * Destroys the whole item incl. children container (and all of his containing children).
     *
     */
    destroy: function () {
        var field = this.tr || this.main;
        field.destroy();

        if (this.options.tableItem){

            var autotable = field.getParent();

            if (autotable.get('tag') == 'tbody')
                autotable = autotable.getParent();

            var tbody = autotable.getChildren('tbody')?autotable.getChildren('tbody'):autotable;

            if (tbody.getChildren().length == 1){
                autotable.destroy();
            }
        }

        if (this.childContainer)
            this.childContainer.destroy();
    },

    /**
     * Hides the item incl the children container.
     *
     */
    hide: function () {

        if (this.childContainer && this.childContainer.hide) this.childContainer.hide();

        var field = this.tr || this.main;

        field.setStyle('display', 'none');

        this.fireEvent('check-depends');
        this.fireEvent('hide');
    },


    /**
     * Returns true if the element is hidden through a visibility-condition or custom hide() call.
     *
     * @return {Boolean}
     */
    isHidden: function () {
        var field = this.tr || this.main;

        return field.getStyle('display') == 'none';
    },

    /**
     * Let the item appears.
     */
    show: function () {

        var field = this.tr || this.main;

        field.setStyle('display', field.get('tag') == 'tr' ? 'table-row' : 'block');

        this.fireEvent('check-depends');
        this.fireEvent('show');
    },

    /**
     * DO WE USE IT?
     *
     * @return {[type]} [description]
     */
    initLayoutElement: function () {

        _win = this.refs.win;

        this.main.setStyle('width', '');
        this.main.addClass('selectable');

        this.obj = new ka.field_layoutElement(this);

        this._setValue = this.obj.setValue.bind(this.obj);
        this.getValue = this.obj.getValue.bind(this.obj);
    },

    setArrayValue: function (pValues, pKey) {

        if (typeOf(pValues) === 'null') {
            this.setValue(null, true);
            return;
        }

        var values = pValues;
        var keys = pKey.split('[');
        var notFound = false;
        Array.each(keys, function(key){

            if (notFound) return;
            if (values[ key.replace(']', '')]) {
                values = values[ key.replace(']', '')];
            } else {
                notFound = true;
            }

        });

        if (!notFound) {
            this.setValue(values);
        }
    },

    /**
     * DO WE USE IT?
     *
     * @return {[type]} [description]
     */
    initMultiUpload: function () {
        //todo: whats that?
        
        //need to pass the win instance seperatly otherwise the setOptions method will thrown an error
        _win = this.refs.win;
        this.refs.win = false;


        _this = this;
        //init ext js class
        if (this.field.extClass) {
            try {
                this.obj = new window[ this.field.extClass ](this.field, _win, _this);
            } catch (e) {

                this.obj = new ka.field_multiUpload(this.field, _win, _this);
            }
        } else {
            this.obj = new ka.field_multiUpload(this.field, _win, _this);
        }

        this.isOk = this.obj.isEmpty.bind(this.obj);
    }
});
