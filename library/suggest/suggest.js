var objXMLHttp;
//for IE
if (window.ActiveXObject)
{ 
	objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
} 
//for Mozilla
else if (window.XMLHttpRequest)
{ 
	objXMLHttp = new XMLHttpRequest();
}

function SendXMLRequestSuggestAjax(Module, Command, FunctionReady, arrParams)
{
	var objXMLHttp;
	var url;
	var o = new Object();
	var oNode, oNodeTmp;
	var objStr;
	
	// the default error
	o.Status = new Object();
	o.Status.Id = 4;
	o.Status.Description = 'Internal error';
	
	if (window.ActiveXObject) //for IE
	{ 
		objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
	} 
	else if (window.XMLHttpRequest) //for Mozilla
	{ 
		objXMLHttp = new XMLHttpRequest();
	}

	//url = '../library/suggest/xmlsuggest.php';
	//url+= '?class=' + Module;
	//url+= '&function=' + Command;

	url = '../xml/xmlhelper.php';
	url+= '?module=' + Module;
	url+= '&command=' + Command;

	if (arrParams)
	{
		for (var key in arrParams)
		{
			if (typeof arrParams[key] == "function")
				continue;
				
			url+= '&' + key + '=' + arrParams[key];
		}
	}

	//alert(url);
	$j.ajax(url, {
		timeout: 1000,
		success: function(objXML, textStatus, objXMLHttp) {			
		if (!objXML.documentElement || objXML.documentElement.nodeName != 'Request')
		{
			o.Status.Description+= '\n' + objXMLHttp.responseText;
			return o;
		}

		oNode = objXML.firstChild;
		while (oNode)
		{
			objStr = '';
			oNodeTmp = oNode;
			while (oNodeTmp)
			{
				if (oNodeTmp.nodeName == '#document')
					objStr = objStr.substring(1);
				else if (oNodeTmp.nodeName == '#text')
					objStr = ' = "' + oNodeTmp.nodeValue.replace(/"/g, '\\"') + '";'; 
				else if (oNodeTmp.nodeName == 'Row')
				{
					if (oNode == oNodeTmp)
					{
						if (parseInt(oNodeTmp.getAttribute('id')) == 0)
							objStr = '.Rows = Array( new Object() );';
						else
							objStr = '.Rows['+(oNodeTmp.getAttribute('id'))+'] = new Object();';
					}
					else
						objStr = '.Rows['+(oNodeTmp.getAttribute('id'))+']' + objStr;
				}
				else
				{
					if (oNode == oNodeTmp && oNodeTmp.childNodes.length >= 1)
						objStr = '.' + oNodeTmp.nodeName + ' = new Object();';
					else if (oNode == oNodeTmp)
						objStr = '.' + oNodeTmp.nodeName + ' = "";';
					else
					{
						//FIXME: Agregado para caracteres especiales
						//objStr = '.' + oNodeTmp.nodeName + ToString(objStr);
						objStr = '.' + oNodeTmp.nodeName + objStr;
					}
				}
				oNodeTmp = oNodeTmp.parentNode;
			}
			
			try
			{
				eval(objStr); 
			}
			catch (e)
			{
				alert(objStr + ' ' + e.description);
			}

			if (oNode.firstChild)
				oNode = oNode.firstChild;
			else if (oNode.nextSibling)
				oNode = oNode.nextSibling;
			else
			{
				while (oNode.parentNode && !oNode.parentNode.nextSibling)
					oNode = oNode.parentNode;
				if (oNode && oNode.parentNode)
					oNode = oNode.parentNode.nextSibling;
				else
					oNode = null;
			}
		}
		
		/* TODO: por ahora solo funciona como sincrónico */
		if (FunctionReady)
			FunctionReady(Request);
	}});
}

function SendXMLRequestSuggest(Module, Command, FunctionReady, arrParams)
{
	var objXMLHttp;
	var url;
	var o = new Object();
	var oNode, oNodeTmp;
	var objStr;
	
	// the default error
	o.Status = new Object();
	o.Status.Id = 4;
	o.Status.Description = 'Internal error';
	
	if (window.ActiveXObject) //for IE
	{ 
		objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
	} 
	else if (window.XMLHttpRequest) //for Mozilla
	{ 
		objXMLHttp = new XMLHttpRequest();
	}

	//url = '../library/suggest/xmlsuggest.php';
	//url+= '?class=' + Module;
	//url+= '&function=' + Command;

	url = '../xml/xmlhelper.php';
	url+= '?module=' + Module;
	url+= '&command=' + Command;

	if (arrParams)
	{
		for (var key in arrParams)
		{
			if (typeof arrParams[key] == "function")
				continue;
				
			url+= '&' + key + '=' + arrParams[key];
		}
	}

	//alert(url);

	objXMLHttp.open("GET", url, false, "", "");
		
	if (window.XMLHttpRequest)
		objXMLHttp.send(null);
	else
		objXMLHttp.send();

	if (objXMLHttp.readyState == 4 && objXMLHttp.status == 200)
		objXML = objXMLHttp.responseXML;
	else
		return o;
						
	if (!objXML.documentElement || objXML.documentElement.nodeName != 'Request')
	{
		o.Status.Description+= '\n' + objXMLHttp.responseText;
		return o;
	}

	oNode = objXML.firstChild;
	while (oNode)
	{
		objStr = '';
		oNodeTmp = oNode;
		while (oNodeTmp)
		{
			if (oNodeTmp.nodeName == '#document')
				objStr = objStr.substring(1);
			else if (oNodeTmp.nodeName == '#text')
				objStr = ' = "' + oNodeTmp.nodeValue.replace(/"/g, '\\"') + '";'; 
			else if (oNodeTmp.nodeName == 'Row')
			{
				if (oNode == oNodeTmp)
				{
					if (parseInt(oNodeTmp.getAttribute('id')) == 0)
						objStr = '.Rows = Array( new Object() );';
					else
						objStr = '.Rows['+(oNodeTmp.getAttribute('id'))+'] = new Object();';
				}
				else
					objStr = '.Rows['+(oNodeTmp.getAttribute('id'))+']' + objStr;
			}
			else
			{
				if (oNode == oNodeTmp && oNodeTmp.childNodes.length >= 1)
					objStr = '.' + oNodeTmp.nodeName + ' = new Object();';
				else if (oNode == oNodeTmp)
					objStr = '.' + oNodeTmp.nodeName + ' = "";';
				else
				{
					//FIXME: Agregado para caracteres especiales
					//objStr = '.' + oNodeTmp.nodeName + ToString(objStr);
					objStr = '.' + oNodeTmp.nodeName + objStr;
				}
			}
			oNodeTmp = oNodeTmp.parentNode;
		}
		
		try
		{
			eval(objStr); 
		}
		catch (e)
		{
			alert(objStr + ' ' + e.description);
		}

		if (oNode.firstChild)
			oNode = oNode.firstChild;
		else if (oNode.nextSibling)
			oNode = oNode.nextSibling;
		else
		{
			while (oNode.parentNode && !oNode.parentNode.nextSibling)
				oNode = oNode.parentNode;
			if (oNode && oNode.parentNode)
				oNode = oNode.parentNode.nextSibling;
			else
				oNode = null;
		}
	}
	
	/* TODO: por ahora solo funciona como sincrónico */
	if (FunctionReady)
		FunctionReady(Request);
		
	return Request;
}

if (!IncSearch) 
{
	var IncSearch = {};
}

/*-- IncSearch.Suggest --------------------------------*/
IncSearch.Suggest = Class.create();
IncSearch.Suggest.prototype = 
{
 	initialize: function(input, suggestArea, extraInput, arrParams) 
	{
		this.arrParams 			= $(arrParams);
		this.input 				= $(input);
		this.extraInput 		= $(extraInput);
		this.suggestArea 		= $(suggestArea);
		this.module 			= null;
		this.command 			= null;
		this.keyList 			= new Array();
		this.candidateList 		= new Array();
		this.suggestList 		= null;
		this.suggestIndexList 	= null;
		this.activePosition 	= null;
		this.timer 				= null;
		this.activeHandler 		= null;
		this.renderHandler 		= null;
		this.idField 			= null;
		this.valueField 		= null;
		this.filterField 		= null;
		this.inputValueBackup 	= null;
		this.oldText 			= this.getInputText();

		if (arguments[5]) this.setOptions(arguments[5]);
	
		// reg event
		Event.observe(this.input, 'focus', this.checkLoop.bindAsEventListener(this), false);
		Event.observe(this.input, 'blur', this.blur.bindAsEventListener(this), false);
	
		if (window.opera) 
		{
			Event._observeAndCache(this.input, 'keypress', this.keyevent.bindAsEventListener(this), false);
		} 
		else 
		{
		  	Event.observe(this.input, 'keypress', this.keyevent.bindAsEventListener(this), false);
		}
	
		// init
		this.clearSuggestArea();
  	},

	// options
	interval: 		500,
  	dispMax: 		20,
	listTagName: 	'div',
	prefix: 		false,
	ignoreCase: 	true,
	highlight: 		true,
	dispAllKey: 	true,

	setOptions: function(options) 
	{
		if (options.interval != undefined)
			this.interval = options.interval;
		
		if (options.dispMax != undefined)
			this.dispMax = options.dispMax;
		
		if (options.listTagName != undefined)
			this.listTagName = options.listTagName;
		
		if (options.prefix != undefined)
			this.prefix = options.prefix;
		
		if (options.ignoreCase != undefined)
			this.ignoreCase = options.ignoreCase;
		
		if (options.highlight != undefined)
			this.highlight = options.highlight;
		
		if (options.dispAllKey != undefined)
			this.dispAllKey = options.dispAllKey;
	},

  	activeDisplay: function(elm) 
	{
    	elm.className = 'select';
  	},

  	unactiveDisplay: function(elm) 
	{
    	elm.className 				= '';
		elm.style.borderLeftColor 	= ''; 
		elm.style.borderTopColor 	= ''; 
		elm.style.borderRightColor 	= ''; 
		elm.style.borderBottomColor = ''; 
		elm.style.backgroundColor 	= '';
		elm.style.color 			= '';
  	},

  	moverDisplay: function(elm) 
	{
    	elm.className = 'over';
  	},

  	moveOutDisplay: function(elm) 
	{		
    	elm.className 				= '';
		elm.style.borderLeftColor 	= ''; 
		elm.style.borderTopColor 	= ''; 
		elm.style.borderRightColor 	= ''; 
		elm.style.borderBottomColor = ''; 
		elm.style.backgroundColor 	= '';
		elm.style.color 			= '';
  	},

  	moveOutAll: function() 
	{		
		if (this.suggestList != null && this.suggestList.length > 0) 
		{
			for (var i=0; i<this.suggestList.length; i++)
				this.moveOutDisplay(this.suggestList[i]);
		}
  	},

  	isMatch: function(value, pattern) 
	{
    	var matchPos;
		var str;
		
		if (pattern.indexOf('%20') != -1)
		{
			pos = pattern.indexOf('%20');
			
			str = pattern.substr(0, pos);
			str+= ' ';
			str+= pattern.substr(pos + 3, pattern.length);
			
			pattern = str;
		}
	
		if (this.ignoreCase) 
		{
		  	pos = value.toLowerCase().indexOf(pattern.toLowerCase());
		} 
		else 
		{
		  	pos = value.indexOf(pattern);
		}
	
		if ((this.prefix && (pos != 0)) || (!this.prefix && (pos == -1)))
			return null;
	
		if (this.highlight) 
		{
			var str = '';
			str+= '<div>';
			str+= value.substr(0, pos);
			str+= '<b>';
			str+= value.substr(pos, pattern.length);
			str+= '</b>';
			str+= value.substr(pos + pattern.length);
			str+= '</div>';
			
		  	return (str);
		} 
		else 
		{
			return value;
		}
  	},

  	checkLoop: function() 
	{
		var text = this.getInputText();
		
		if (text != this.oldText) 
		{
		  	this.oldText = text;
		  	this.search();
		}
		
		if (this.timer) 
			clearTimeout(this.timer);
			
		this.timer = setTimeout(this.checkLoop.bind(this), this.interval);
  	},

  	keyevent: function(event) 
	{
		if (!this.timer) 
		{
		  	this.timer = setTimeout(this.checkLoop.bind(this), this.interval);
		}
	
		if ((this.dispAllKey && event.ctrlKey) && (this.getInputText() == '') && (!this.suggestList) && (event.keyCode == Event.KEY_DOWN))
		{
		  	Event.stop(event);
		  	this.dispAllSuggest();
		} 
		else if ((event.keyCode == Event.KEY_UP) || (event.keyCode == Event.KEY_DOWN))
		{
		  	Event.stop(event);
		  	this.moveActiveList(event.keyCode);
		} 
		else if ((event.keyCode == Event.KEY_RETURN) || (event.keyCode == Event.KEY_TAB)) 
		{
		  	if (this.suggestList) 
			{
				// Si solo tenemos 1 elemento lo seleccionamos
				if (this.candidateList.length == 1) 
				{
					this.active(0);
				}
				Event.stop(event);
				this.clearSuggestArea();
		  	}
		} 
		else if (event.keyCode == Event.KEY_ESC) 
		{
		  	if (this.suggestList) 
			{
				Event.stop(event);
				this.clearSuggestArea();
		  	}

			if (this.activeHandler != null)
			{
				eval(this.activeHandler + '("", "");');
			}
			
			this.input.blur();
		}
  	},

  	search: function() 
	{
		// init
		this.clearSuggestArea();
	
		var textValue = this.getInputText();
		if (textValue == '') 
			return;
			
		textValue = textValue.replace(' ', '%20');
	
		var resultList 	= new Array();
		var temp 		= null; 
		var id 			= null;
	
		this.suggestIndexList = new Array();
	
		var idField 	= '';
		var valueField 	= '';
		var filterField = '';
		
		this.candidateList.clear();
		this.keyList.clear();
				
		var module 		= this.module;
		var command 	= this.command;
		var arrParams 	= new Array();

		arrParams[this.filterField] = textValue;

		if (this.arrParams)
		{
			for (var key in this.arrParams)
			{
				if (typeof this.arrParams[key] == "function")
					continue;
	
				if (typeof this.arrParams[key] == "class")
					continue;

				arrParams[key] = this.arrParams[key];
			}
		}

		var sf = this;
		SendXMLRequestSuggestAjax(module, command, function(obj) {
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			var Objects = obj.Response.Rows;
			
			for (var i=0; Objects && i<Objects.length; i++)
			{
				var object 	= Objects[i];
				var id 		= null;
				var value 	= null;

				id 		= object[sf.idField];
				value 	= object[sf.valueField];

				sf.candidateList.push(value);

				if (id != undefined)
					sf.keyList.push(id);
			}	

			for (i=0; i<sf.candidateList.length; i++) 
			{
				if ((temp = sf.isMatch(sf.candidateList[i], textValue)) != null) 
				{
					resultList.push(temp);
					sf.suggestIndexList.push(i);
		
					if (sf.dispMax != 0 && resultList.length >= sf.dispMax) 
						break;
				}
				else
				{
					temp = sf.candidateList[i];
					resultList.push(temp);
					sf.suggestIndexList.push(i);
		
					if (sf.dispMax != 0 && resultList.length >= sf.dispMax) 
						break;		  
				}
			}

			if (resultList != 0)
			{
				sf.createSuggestArea(resultList);
			}
			}, arrParams);
		
  	},

  	clearSuggestArea: function() 
	{
		this.suggestArea.innerHTML 		= '';
		this.suggestArea.style.display 	= 'none';
		this.suggestList 				= null;
		this.suggestIndexList 			= null;
		this.activePosition 			= null;
  	},

  	createSuggestArea: function(resultList) 
	{	
		this.suggestList = new Array();
		this.inputValueBackup = this.input.value;
	
		for (var i=0; i<resultList.length; i++) 
		{
		  	var elm = document.createElement(this.listTagName);
			
		  	if (this.renderHandler != null)
			{
				elm.innerHTML = eval(this.renderHandler + '("' + resultList[i] + '");');
			}
		  	else
			{
		  		elm.innerHTML = resultList[i];
			}
		  
		  	this.suggestArea.appendChild(elm);
	
		  	Event.observe(elm, 'click', new Function('event', 'this.listClick(event, ' + i + ');').bindAsEventListener(this), false);
		  	Event.observe(elm, 'mouseover', new Function('event', 'this.listOver(event, ' + i + ');').bindAsEventListener(this), false);
		  	Event.observe(elm, 'mouseout', new Function('event', 'this.listOut(event, ' + i + ');').bindAsEventListener(this), false);
	
		  	this.suggestList.push(elm);
		}
	
		this.suggestArea.style.display 	= '';
		this.suggestArea.style.position = 'absolute';
		this.suggestArea.style.top 		= absoluteTop(this.input) + this.input.offsetHeight;
		this.suggestArea.style.left 	= absoluteLeft(this.input);
		this.suggestArea.style.width 	= this.input.offsetWidth;
		this.suggestArea.style.border 	= '1px solid #999999';		
  	},

  	moveActiveList: function(keyCode) 
	{
		if (!this.suggestList || this.suggestList.length == 0)
		{
		  	return;
    	}

    	this.unactive();
		
    	if (keyCode == Event.KEY_UP) 
		{
      		// up
      		if (this.activePosition == null) 
			{
        		this.activePosition = this.suggestList.length - 1;
      		}
			else
			{
				if (this.activePosition > 0) 
				{
        			this.activePosition--;
				}
				else
				{
					this.activePosition = this.suggestList.length - 1;
				}
      		}
    	}
		else if (keyCode == Event.KEY_DOWN) 
		{
      		// down
      		if (this.activePosition == null) 
			{
        		this.activePosition = 0;
      		}
			else
			{
				if (this.activePosition < this.suggestList.length - 1)
				{
        			this.activePosition++;
				}
				else
				{
	        		this.activePosition = 0;
				}
      		}
    	}

    	this.active(this.activePosition);
  	},

  	active: function(index) 
	{
		//this.moveOutAll();

    	this.activeDisplay(this.suggestList[index]);

		var desc 	= this.candidateList[index];
		var key		= this.keyList[index];	

		if (this.activeHandler != null)
		{
			eval(this.activeHandler + '("' + key + '", "' + desc + '");');
		}
		else
		{
	    	this.setInputText(desc);
		}

    	this.oldText = this.getInputText();
    	this.input.focus();
  	},

  	unactive: function() 
	{
		if (this.suggestList != null && this.suggestList.length > 0 && this.activePosition != null) 
		{
		  	this.unactiveDisplay(this.suggestList[this.activePosition]);
		}
  	},

  	blur: function(event) 
	{
		this.unactive();
		this.oldText = this.getInputText();
	
		if (this.timer) 
			clearTimeout(this.timer);
			
		this.timer = null;
	
		setTimeout((function(){ this.clearSuggestArea(); }).bind(this), 500);
  	},

  	listClick: function(event, index) 
	{
		if (this.suggestList) 
		{
			// Si solo tenemos 1 elemento lo seleccionamos
			if (this.candidateList.length == 1) 
			{
				this.active(0);
			}
			
			Event.stop(event);
			this.clearSuggestArea();
		}
  	},

  	listOver: function(event, index) 
	{
		var elm = Event.element(event);
		
		this.moverDisplay(elm);

		this.unactive();
		
		if (this.activePosition != index)
		{
			this.activePosition = index;
			this.active(index);
		}
  	},

  	listOut: function(event, index) 
	{
		var elm = Event.element(event);

		if (!this.suggestList) 
			return;
	
		if (index == this.activePosition) 
		{
			this.moveOutDisplay(elm);
			//this.activeDisplay(elm);
		}
		else
		{
		  	this.unactiveDisplay(elm);
		}
  	},

  	getInputText: function() 
	{
    	return this.input.value;
  	},

  	setInputText: function(text) 
	{
    	this.input.value = text;
  	},

	dispAllSuggest: function() 
	{
		// init
		this.clearSuggestArea();
		this.oldText = this.getInputText();
		this.suggestIndexList = new Array();
		
		for (var i=0; i<this.candidateList.length; i++) 
		{
			this.suggestIndexList.push(i);
		}

		this.createSuggestArea(this.candidateList);
	}
};