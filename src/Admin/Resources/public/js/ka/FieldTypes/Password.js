ka.FieldTypes.Password = new Class({
    
    Extends: ka.FieldTypes.Input,

    createLayout: function(){
        this.parent();
        this.input.set('type', 'password');
    }
});