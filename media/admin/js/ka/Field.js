
ka.FieldTypes = {};

ka.Field = new Class({

    Implements: [Options, Events],

    Binds: ['fireChange', 'checkValid'],

    options: {

        value: null,
        withAsteriskIfRequired: true,

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

        returnDefault: false, //If this field has a default value, the field value won't be returned in the ka.FieldForm object per default if the value is the default value.

        cookieStorage: false, //stores the value in a cookie, and set it after a second load

        noWrapper: false //doesnt include the ka-field wrapper (title, description, etc), and inject the field controls directly to pContainer with just a single div around it.
    },

    handleChildsMySelf: false, //defines whether this object handles his child visibility itself

    field: {},
    refs: {},
    key: '',
    depends: {},
    childContainer: false,
    container: false,

    parent: null, //parent ka.Field instance

    children: {},

    /**
     *
     * @param  {Object} pDefinition
     * @param  {Element} pContainer
     * @param  {String} pKey Optional
     */
    initialize: function (pDefinition, pContainer, pKey) {

        this.key = pKey;

        if (pDefinition.type == 'predefined'){
            var definition = ka.getObjectDefinition(pDefinition.object);
            this.field = Object.clone(definition.fields[pDefinition.field]);

            if (pDefinition.label)
                this.field.label = pDefinition.label;

        } else {
            this.field = Object.clone(pDefinition);
        }

        this.calledDefinition = Object.clone(pDefinition);

        this.setOptions(this.field);
        this.container = pContainer;

        if (this.options.noWrapper){


            if (this.options.tableItem || (pContainer && (pContainer.get('tag') == 'table' || pContainer.get('tag') == 'tbody'))) {

                //we should be appear as a non-table element but got a table element as contain.
                //so create a tr>td[colspan=2] and set this.options.tableItem to true, that
                //this.inject works correct.

                this.options.tableItem = true;

                this.tr = new Element('tr', {
                    'class': 'ka-Field ka-field-main'
                });
                
                this.tr.instance = this;
                this.tr.store('ka.Field', this);

                this.main = new Element('td', {
                    'class': 'ka-Field-inputTd',
                    colspan: 2
                }).inject(this.tr);

            } else {

                this.main = new Element('div', {'class': 'ka-Field'});
                this.main.instance = this;
                this.main.store('ka.Field', this);
            }

            this.fieldPanel = this.main;

        } else {

            if (!this.options.tableItem && pContainer && (pContainer.get('tag') == 'table' || pContainer.get('tag') == 'tbody')) {

                //we should be appear as a non-table element but got a table element as contain.
                //so create a tr>td[colspan=2] and set this.options.tableItem to true, that
                //this.inject works correct.

                this.options.tableItem = true;

                this.tr = new Element('tr', {
                    'class': 'ka-Field ka-field-main'
                });

                this.tr.instance = this;

                this.tr.store('ka.Field', this);

                this.main = new Element('td', {
                    colspan: 2
                }).inject(this.tr);

                this.title = new Element('div', {
                    'class': 'ka-field-title selectable'
                }).inject(this.main);


            } else if (this.options.tableItem) {
                this.tr = new Element('tr', {
                    'class': 'ka-Field ka-field-main'
                });
                this.tr.instance = this;
                this.tr.store('ka.Field', this);

                this.title = new Element('td', {
                    'class': 'ka-field-tdtitle selectable',
                    width: (this.options.tableitem_title_width) ? this.options.tableitem_title_width : '40%'
                }).inject(this.tr);

                this.main = new Element('td', {
                    'class': 'ka-Field-inputTd'
                }).inject(this.tr);

            } else {
                this.main = new Element('div', {
                    'class': 'ka-Field ka-field-main'
                });
                this.main.instance = this;
                this.main.store('ka.Field', this);

                if (this.options.small) {
                    this.main.set('class', 'ka-field-main ka-field-main-small');
                }

                this.title = new Element('div', {
                    'class': 'ka-field-title selectable'
                }).inject(this.main);
            }


            if (this.options.label) {
                this.titleText = new Element('div', {
                    'class': 'title',
                    html: this.options.label
                }).inject(this.title);
            }

            if (this.options.help && this.titleText) {
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
                    ka.wm.open('admin/help', {id: this.options.help});
                }.bind(this)).inject(this.titleText);
            }

            if (this.options.desc) {
                this.descText = new Element('div', {
                    'class': 'desc',
                    html: this.options.desc
                }).inject(this.title);
            }

            this.fieldPanel = new Element('div', {
                'class': 'ka-field-field'
            }).inject(this.main);
        }

        if (this.options.required && this.options.withAsteriskIfRequired && this.titleText){
            this.titleText.appendText('*');
        }

        if (pContainer)
            this.inject(pContainer);

        if (this.options.fieldWidth)
            this.fieldPanel.setStyle('width', this.options.fieldWidth);

        if (this.options.fieldWidth && typeOf(this.options.fieldWidth) == 'string' && this.options.fieldWidth.indexOf('%') > 0){
            this.fieldPanel.addClass('ka-field-field-without-margin');
        }

        if (this.options.invisible == 1) {
            this.main.setStyle('display', 'none');
        }

        this.findWin();

        this.renderField();

        if (!this.options.startEmpty && typeOf(this.options.value) != 'null') {
            this.setValue(this.options.value, true);

        } else if (typeOf(this.field['default']) != 'null'){
            this.setValue(this.field['default'], true);

        } else if (this.options.cookieStorage){
            var cookieValue = Cookie.read(this.options.cookieStorage);
            if (typeOf(cookieValue) != 'null'){
                this.setValue(JSON.decode(cookieValue), true);
            }
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
            throw 'The ka.Field type `'+this.options.type+'` is not available.';
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

    showInvalid: function(pText){
        this.fieldObject.showInvalid(pText);
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
        return this.fieldObject.checkValid();
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
     * Returns the appropriate class instance of the given type
     * in ka.ui.FieldTypes[<type>].
     *
     * @return {Object}
     */
    getFieldObject: function(){
        return this.fieldObject;
    },

    /**
     * Sets the value.
     *
     * @param {Mixed} pValue
     * @param {Boolean} pInternal Fires fireChange() which fires the 'change' event. Default is false.
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
            this.checkValid();
        }
    },

    /**
     * A binded function, that fires 'change', 'check-depends' events and isOk() method.
     *
     */
    fireChange: function(){
        var value = this.getValue();

        this.fireEvent('change', [value, this, this.key]);
        this.fireEvent('check-depends');

        this.updateCookieStorage(value);
    },

    updateCookieStorage: function(pValue){

        if (this.options.cookieStorage){
            Cookie.write(this.options.cookieStorage, JSON.encode(pValue));
        }

    },

    /**
     * Finds the ka.Window instance through a DOM lookup.
     *
     * @return {ka.Window} The window instance or null
     */
    findWin: function () {

        if (this.win) return this.win;

        var win = this.toElement().getParent('.kwindow-border');
        if (!win) return null;

        this.win = win.retrieve('win');

        return this.win;
    },

    /**
     * Creates and injects a children container.
     *
     * @return {Element} The newly created child container, or null if already exist.
     */
    prepareChildContainer: function(){

        if (this.childContainer) return null;

        if (this.options.tableItem) {

            this.childrenContainerTr = new Element('tr').inject(document.id(this), 'after');
            this.childrenContainerTd = new Element('td', {colspan: 2, style: 'padding: 0px; border-bottom: 0px;'}).inject(this.childrenContainerTr);

            this.childContainer = new Element('div', {
                'class': 'ka-Field-childrenContainer ka-fields-sub'
            }).inject(this.childrenContainerTd);

        } else {
            this.childContainer = new Element('div', {
                'class': 'ka-Field-childrenContainer ka-fields-sub'
            }).inject(document.id(this), 'after');
        }

        this.childContainer.instance = this;

        this.fireEvent('childrenPrepared');

        return this.childContainer;
    },

    /**
     * Returns true if this item has a visibility-condition parent or
     * a parent of a structured ka.FieldForm object, not a DOM parent.
     * @return {Boolean} [description]
     */
    hasParent: function(){
        return this.parent !== null;
    },

    /**
     * Returns the visibility-condition parent or the parent of a
     * structured ka.FieldForm object, not the DOM parent.
     *
     * @return {ka.Field}
     */
    getParent: function(){
        if (!this.parent){
            var parentChildrenContainer = this.toElement().getParent('.ka-Field-childrenContainer');
            if (parentChildrenContainer)
                return parentChildrenContainer.instance;
        }
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

    /**
     * Returns the container from our children.
     *
     * @return {Element} null if not exist
     */
    getChildrenContainer: function(){
        return this.childContainer;
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
        this.inject(pField, 'before');
    },


    /**
     * Injects the field after pField.
     *
     * @param  {ka.Field} pField
     */
    injectAfter: function(pField){
        this.inject(pField, 'after');
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
        pP = pP?pP:'bottom';

        if (instanceOf(pTo, ka.Field) && pP == 'after' && pTo.toElement().get('tag') == 'tr' && pTo.getChildrenContainer()){
            //since in table mode the children container is actually under the ka-Field dom element, we
            //have to assign the pTo to the children container.
            pTo = pTo.getChildrenContainer();
        } else if(instanceOf(pTo, ka.Field)){

            if (pP == 'bottom' || pP == 'top'){
                pTo.prepareChildContainer();
            }
            pTo = pTo.toElement();
        }

        field.dispose();

        if (this.containerAutoTable && this.containerAutoTable.hasClass('ka-Field-autotable')){
            if (this.containerAutoTable.getChildren('.ka-Field').length === 0){
                //it's our own autotable, so delete it.
                this.containerAutoTable.destroy();
                delete this.containerAutoTable;
            }
        }

        if (this.options.tableItem){

            if (pTo.get('tag') != 'tbody' && pTo.get('tag') != 'table'){
                //target is not a table/tbody, we need to create one or find one

                logger(this.options.label);
                logger(pTo);
                
                if (pTo.get('tag') == 'tr'){
                    this.containerAutoTable = pTo.getParent('table');
                } else {
                    //guess, we need one
                    if (pP == 'bottom' || pP == 'top')
                        this.containerAutoTable = pTo.getLast('.ka-Field-autotable');
                    else if (pP == 'before')
                        this.containerAutoTable = pTo.getPrevious('.ka-Field-autotable');
                    else if (pP == 'after'){
                        this.containerAutoTable = pTo.getNext('.ka-Field-autotable');
                    }

                    if (!this.containerAutoTable){
                        this.containerAutoTable = new Element('table', {'class': 'ka-Field-autotable', width: '100%'})
                        .inject(pTo, pP);
                    }
                }
            }

            var targetTable = this.containerAutoTable || pTo;

            var tbody = targetTable.getChildren('tbody').length>0?targetTable.getChildren('tbody')[0]:targetTable;

            if (field.getDocument() != pTo.getDocument())
                pTo.getDocument().adoptNode(field);

            field.inject(tbody);

        } else {


            //find a valid container
            
            while (((pP == 'top' || pP == 'bottom') && pTo.get('tag') == 'table') ||
                   ((pP == 'after' || pP == 'before') && pTo.get('tag') == 'tbody' || pTo.get('tag') == 'tr')
                ){
                pTo = pTo.getParent();
            }

            if (field.getDocument() != pTo.getDocument())
                pTo.getDocument().adoptNode(field);

            field.inject(pTo, pP);

        }

        if (this.getChildrenContainer()){
            this.getChildrenContainer().inject(field, 'after');
        }

        this.findWin();

        return this;
    },

    /**
     * Destroys the whole item incl. children container (and all of his containing children).
     *
     */
    destroy: function () {

        var field = this.toElement();

        //are we between 2 ka-field-autotables ? maybe we can merge it
        if (field.getPrevious() && field.getNext() && field.getNext().hasClass('ka-Field-autotable') && field.getPrevious().hasClass('ka-Field-autotable')){
            var next = field.getNext();
            var tbodyNext = next.getChildren('tbody').length>0?next.getChildren('tbody')[0]:next;
            var previous = field.getPrevious();
            var tbodyPrevious = previous.getChildren('tbody').length>0?previous.getChildren('tbody')[0]:previous;
            tbodyNext.getChildren().inject(previous);
            next.destroy();
        }

        field.destroy();

        if (this.options.tableItem){

            if (this.containerAutoTable && this.containerAutoTable.hasClass('ka-Field-autotable')){

                var tbody = this.containerAutoTable.getChildren('tbody').length>0?
                             this.containerAutoTable.getChildren('tbody')[0]:this.containerAutoTable;

                if (tbody.getChildren('.ka-Field').length === 0){
                    //we're alone, delete the auto table
                    this.containerAutoTable.destroy();
                }
            }
        } else {
            
            

        }

        if (this.getChildrenContainer())
            this.getChildrenContainer().destroy();
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
     * Returns all children ka.Fields.
     *
     * Not by the field definition, but by the DOM structure.
     * So, when there're ka.Field objects in our children container,
     * then we have children, otherwise not.
     *
     * @return {Array} Array with ka.Field instances
     */
    getChildren: function(){
        var children = [];

        if (this.getChildrenContainer()){
            this.getChildrenContainer().getChildren('.ka-Field').each(function(child){
                children.push(child.instance);
            });
        }

        return children;
    },

    /**
     * Returns the key of this field, if set.
     *
     * @return {String}
     */
    getKey: function(){
        return this.key;
    },

    getDefinition: function(){

        var definition = this.calledDefinition;

        var children = this.getChildren();

        if (children.length > 0){

            definition.children = {};

            Array.each(children, function(child){
                definition.children[child.getKey()] = child.getDefinition();
            });
        }

        return definition;

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
        if (this.options.extClass) {
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
