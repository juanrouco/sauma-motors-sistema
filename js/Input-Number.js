var InputNumber = new Class({
    Implements: Events,

    initialize: function(cl, max, min){    
        this._cl    = cl
        this._max   = (max!=undefined && max!="" ) ? max : 999;
        this._min   = (min!=undefined && min!="" ) ? min : 0;  
        this._enabled = true    
        this.init();
    },
    
    init: function(){
        var _here = this
        $$("."+this._cl).each(function(el){
            _here.addHtml(el);
        });

    },
    
    addHtml: function(el){
        var _here = this;
        
        var value  = el.get('html');
        var input  = new Element('div', {'class': 'nm-input fl', 'html': value});
        var div    = new Element('div', {'class': 'fl'});
        var add    = new Element('div', {'class': 'nm-add'});
        var remove = new Element('div', {'class': 'nm-remove'});
        //var inputH = new Element('input', {'class': 'nm-th', 'id': 'nm-'+el.id, 'name': 'nm-'+el.id, 'type': 'hidden', 'value': value});
        var ids     = el.id.split("-")
        input._id    = ids[1]
        input._rowid = ids[2]
        input._value = value

        add.addEvent('click', function(){
            if(_here._enabled){
                var value  = input.get('html').toInt();
                if(value<_here._max)
                    value++               
                _here.updateValue(input,  value)
            }
        });
        
        remove.addEvent('click', function(){
            if(_here._enabled){
                var value  = input.get('html').toInt();
                if(value>_here._min)
                    value--
                _here.updateValue(input,  value)
            }
        });


        el.set('html', '')
        //el.grab(inputH);
        div.grab(add);
        div.grab(remove);
        el.grab(input);
        el.grab(div);
    },
    
    updateValue: function(input,  value){
        input.set('html', value);
        input._value = value
        //inputH.set('value', value)
        this.getTotal(input);
    },
    
    getTotal: function (input){
        var total = 0
        $$(".nm-th").each(function(el){
            total+= el.get('value').toInt();
        });

        this.fireEvent("onUpdate", [input._id, input._rowid, input._value]);
        //location.reaload(true);
    },
    
    disabled: function(){
        this._enabled = false
    },
    enabled: function(){
        this._enabled = true
    }
    
    
});
