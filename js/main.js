window.addEvent('domready', function() {			
    init_input_number()
});	

var _input
function init_input_number(){
    _input = new InputNumber('nm');
    _input.addEvent('onUpdate', inputUpdate)
}






function loadXML(objXML)
{
	var o = new Object();
	var oNode, oNodeTmp;
	var objStr;

				alert(objXML.responseXML);

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
	
	return Request;
}





function inputUpdate(id, rowid, value){
    var ajax = new Request({
         method: 'post',
		 //url:    _web_url+_lang+"/carrito/update/"+id+"/"+rowid+"/"+value,
		 url:    "http://localhost/modulo/espanol/xml/xmlhelper.php",
		 data: 	"module=NoticiaImagenes&command=GetLastId",
		 onRequest: function(){
		      _input.disabled()
		 },

         onSuccess: function(data){
            //var data = JSON.decode(data);
			//var data = JSON.toString(data);
			
			var objXML = loadXML(data);
			
			alert(objXML);
			
            _input.enabled()
	 	 },
		 
         onFailure: function(){alert('Error al cargar datos');}
		 
      }).send();  
}
        
function redirect(url) {
    window.location.href = url;    
}

function send_form(form){
    alert("ok")
}