Class.Mutators.Static = function(properties){
    Object.each(properties, function(prop, key) {
        this[key] = prop;
    }, this);
};