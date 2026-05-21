<?php

abstract class Modules
{
	static function LoadModule($Name)
	{
		// check if the module exists
		if (!file_exists(dirname(__FILE__) . "/../modules/" . strtolower($Name) . ".php"))
			return false;

		require_once(dirname(__FILE__) . "/../modules/" . strtolower($Name) . ".php"); 

		$classname = "Module$Name";
		
		// check if the expected class exists
		if (!class_exists($classname))
			return false;
		
		$module = new $classname($Name);
		
		return $module;
	}
	

	static function WriteClientFunctions()
	{
		static $justOne;
		
		if ($justOne)
			return;
			
		?><script language="javascript" type="text/javascript">

		function SendXMLRequest(Module, Command, FunctionReady, arrParams)
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
							objStr = '.' + oNodeTmp.nodeName + ToString(objStr);
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
		

		function Get(Name)
		{
			return document.getElementById(Name);
		}
		
		
		function ShowSection(Name)
		{
			if (!Get(Name))
				return;
				
			Get(Name).style.display = '';			
		}
		
		
		function HideSection(Name)
		{
			if (!Get(Name))
				return;
				
			Get(Name).style.display = 'none';
		}
		
		
		function SetInnerText(HTMLElement, Text)
		{
			var isIE = (window.navigator.userAgent.indexOf("MSIE") > 0);  

			if (!isIE)
				HTMLElement.textContent = Text;
			else
			 	HTMLElement.innerText = Text;  
		} 
		
		
		function GetInnerText(HTMLElement)
		{
			var isIE = (window.navigator.userAgent.indexOf("MSIE") > 0);  

			if (!isIE)
				return HTMLElement.textContent;
			else
				return HTMLElement.innerText;  
		} 
		
		function CheckDecimal(decimal)
		{
			var decimalPattern=  /^[-+]?[0-9]+(\.[0-9]+)?$/;   
			if(decimal.match(decimalPattern))   
			{   
				return true;  
			}  
			else  
			{   
				return false;  
			}  
		}

		function SaveComision(IdMinuta, IndiceComision)
		{
			if (IdMinuta && IdMinuta != 0 && CheckDecimal(IndiceComision))
			{
				var arr 	= new Array();
				arr['IdMinuta'] = IdMinuta;
				arr['IndiceComision'] = IndiceComision;
		
				obj = SendXMLRequest('Comisiones', 'Update', null, arr);
				if (obj.Status.Id != 0)
				{
					alert(obj.Status.Description);
					return;
				}
			}
			
		}
		
		function SaveComentariosCuentaGestoriaUsados(IdCuentaGestoria, Comentarios)
		{
			if (IdCuentaGestoria && IdCuentaGestoria != 0)
			{
				var arr 	= new Array();
				arr['IdCuentaGestoria'] = IdCuentaGestoria;
				arr['Comentarios'] = Comentarios;
		
				obj = SendXMLRequest('CuentasGestoriaUsados', 'Update', null, arr);
				if (obj.Status.Id != 0)
				{
					alert(obj.Status.Description);
					return;
				}
			}
			
		}
		
		function SaveComentariosCuentaGestoria(IdCuentaGestoria, Comentarios)
		{
			if (IdCuentaGestoria && IdCuentaGestoria != 0)
			{
				var arr 	= new Array();
				arr['IdCuentaGestoria'] = IdCuentaGestoria;
				arr['Comentarios'] = Comentarios;
		
				obj = SendXMLRequest('CuentasGestoria', 'Update', null, arr);
				if (obj.Status.Id != 0)
				{
					alert(obj.Status.Description);
					return;
				}
			}
			
		}

		function LoadPaises(Element, IdSelected)
		{
			var arr 	= new Array();
			var opts 	= Get(Element).options;
			var obj;
			var opt;
			var oPaises;
						
			opts.length = 0;
			opts.add(new Option('[SELECCIONE]', ''));
		
			obj = SendXMLRequest('Paises', 'GetAll', null, arr);
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			oPaises = obj.Response.Rows;
			
			for (var i=0; oPaises && i<oPaises.length; i++)
			{
				var oPais = oPaises[i];
			
				opt = new Option(oPais.Nombre, oPais.IdPais);
				opt.selected = (oPais.IdPais == IdSelected);
				opts.add(opt);
			}	
		}
		

		function LoadProvincias(Element, IdPais, IdSelected)
		{
			var arr 	= new Array();
			var opts 	= Get(Element).options;
			var obj;
			var opt;
			var oProvincias;
						
			opts.length = 0;
			opts.add(new Option('Indistinto', ''));
		
			if (IdPais == '')
				IdPais = 0;
		
			arr['IdPais'] = IdPais;
			obj = SendXMLRequest('Provincias', 'GetAll', null, arr);
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			oProvincias = obj.Response.Rows;
			
			for (var i=0; oProvincias && i<oProvincias.length; i++)
			{
				var oProvincia = oProvincias[i];
			
				opt = new Option(oProvincia.Nombre, oProvincia.IdProvincia);
				opt.selected = (oProvincia.IdProvincia == IdSelected);
				opts.add(opt);
			}	
		}
		

		function LoadPartidos(Element, IdProvincia, IdSelected)
		{
			var arr 	= new Array();
			var opts 	= Get(Element).options;
			var obj;
			var opt;
			var oPartidos;
						
			opts.length = 0;
			opts.add(new Option('Indistinto', ''));
		
			if (IdProvincia == '')
				IdProvincia = 0;
		
			arr['IdProvincia'] = IdProvincia;
			obj = SendXMLRequest('Partidos', 'GetAll', null, arr);
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			oPartidos = obj.Response.Rows;
			
			for (var i=0; oPartidos && i<oPartidos.length; i++)
			{
				var oPartido = oPartidos[i];
			
				opt = new Option(oPartido.Nombre, oPartido.IdPartido);
				opt.selected = (oPartido.IdPartido == IdSelected);
				opts.add(opt);
			}	
		}
		
		function LoadPlanesCuotas(Element, IdFormaPago, IdSelected)
		{
			var arr 	= new Array();
			var opts 	= Get(Element).options;
			var obj;
			var opt;
			var oPlanesCuotas;
						
			opts.length = 0;
			
			if (IdFormaPago == '')
				IdFormaPago = 0;
		
			arr['IdFormaPago'] = IdFormaPago;
			obj = SendXMLRequest('PlanesCuotas', 'GetAll', null, arr);
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			oPlanesCuotas = obj.Response.Rows;
			
			for (var i=0; oPlanesCuotas && i<oPlanesCuotas.length; i++)
			{
				var oPlanCuota = oPlanesCuotas[i];
			
				opt = new Option(oPlanCuota.Nombre, oPlanCuota.IdPlanCuota);
				opt.selected = (oPlanCuota.IdPlanCuota == IdSelected);
				opts.add(opt);
			}	
		}
			

		function LoadSectores(Element, IdSelected)
		{
			var arr 	= new Array();
			var opts 	= Get(Element).options;
			var obj;
			var opt;
			var oSectores;
						
			opts.length = 0;
			opts.add(new Option('[SELECCIONE]', ''));
		
			obj = SendXMLRequest('Sectores', 'GetAll', null, arr);
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			oSectores = obj.Response.Rows;
			
			for (var i=0; oSectores && i<oSectores.length; i++)
			{
				var oSector = oSectores[i];
			
				opt = new Option(oSector.Nombre, oSector.IdSector);
				opt.selected = (oSector.IdSector == IdSelected);
				opts.add(opt);
			}	
		}
		
		function GetPedidosRepuestos(IdArticulo)
		{
			var arr = new Array();
			var obj;
			var oArticulo;

			if (Codigo == '')
				return;
						
			arr['IdArticulo'] = IdArticulo;
			obj = SendXMLRequest('Articulos', 'GetPedidosRepuestos', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oArticulo = obj.Response;
		
			return oArticulo;
		}
		
		

		function LoadPerfiles(Element, IdSelected)
		{
			var arr 	= new Array();
			var opts 	= Get(Element).options;
			var obj;
			var opt;
			var oPerfiles;
						
			opts.length = 0;
			opts.add(new Option('[SELECCIONE]', ''));
		
			obj = SendXMLRequest('Perfiles', 'GetAll', null, arr);
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			oPerfiles = obj.Response.Rows;
			
			for (var i=0; oPerfiles && i<oPerfiles.length; i++)
			{
				var oPerfil = oPerfiles[i];
			
				opt = new Option(oPerfil.Nombre, oPerfil.IdPerfil);
				opt.selected = (oPerfil.IdPerfil == IdSelected);
				opts.add(opt);
			}	
		}


		function GetModelo(IdModelo)
		{
			var arr = new Array();
			var obj;
			var oModelo;
			
			if ((IdModelo == '') || (IdModelo == '0'))
				return;
				
			arr['IdModelo'] = IdModelo;
			obj = SendXMLRequest('Modelos', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oModelo = obj.Response;
		
			return oModelo;	
		}


		function GetFacturaCompra(IdFacturaCompra)
		{
			var arr = new Array();
			var obj;
			var oFacturaCompra;
			
			if ((IdFacturaCompra == '') || (IdFacturaCompra == '0'))
				return;
				
			arr['IdFacturaCompra'] = IdFacturaCompra;
			obj = SendXMLRequest('FacturasCompras', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oFacturaCompra = obj.Response;
		
			return oFacturaCompra;	
		}


		function GetUnidad(IdUnidad)
		{
			var arr = new Array();
			var obj;
			var oUnidad;

			arr['IdUnidad'] = IdUnidad;
			obj = SendXMLRequest('Unidades', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oUnidad = obj.Response;
		
			return oUnidad;	
		}
		
		function GetUnidadByNumeroVin(NumeroVin)
		{
			var arr = new Array();
			var obj;
			var oUnidad;

			arr['FilterNumeroVin'] = NumeroVin;
			obj = SendXMLRequest('Unidades', 'GetByNumeroVin', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oUnidad = obj.Response;
		
			return oUnidad;	
		}
		
		function GetUnidadByNumeroChasis(NumeroChasis)
		{
			var arr = new Array();
			var obj;
			var oUnidad;

			arr['FilterNumeroChasis'] = NumeroChasis;
			obj = SendXMLRequest('Unidades', 'GetByNumeroChasis', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oUnidad = obj.Response;
		
			return oUnidad;	
		}

		function GetColor(IdColor)
		{
			var arr = new Array();
			var obj;
			var oColor;

			if ((IdColor == '') || (IdColor == '0'))
				return;
						
			arr['IdColor'] = IdColor;
			obj = SendXMLRequest('Colores', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oColor = obj.Response;
		
			return oColor;	
		}
		

		function GetUbicacion(IdUbicacion)
		{
			var arr = new Array();
			var obj;
			var oMarca;

			if ((IdUbicacion == '') || (IdUbicacion == '0'))
				return;
						
			arr['IdUbicacion'] = IdUbicacion;
			obj = SendXMLRequest('Ubicaciones', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oUbicacion = obj.Response;
		
			return oUbicacion;	
		}
		
		function GetTallerUnidad(IdTallerUnidad)
		{
			var arr = new Array();
			var obj;
			var oTallerUnidad;

			if ((IdTallerUnidad == '') || (IdTallerUnidad == '0'))
				return;
						
			arr['IdTallerUnidad'] = IdTallerUnidad;
			obj = SendXMLRequest('TallerUnidades', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTallerUnidad = obj.Response;
		
			return oTallerUnidad;	
		}
		
		function GetOrdenTrabajo(IdOrdenTrabajo)
		{
			var arr = new Array();
			var obj;
			var oOrdenTrabajo;

			if ((IdOrdenTrabajo == '') || (IdOrdenTrabajo == '0'))
				return;
						
			arr['IdOrdenTrabajo'] = IdOrdenTrabajo;
			obj = SendXMLRequest('OrdenesTrabajo', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oOrdenTrabajo = obj.Response;
		
			return oOrdenTrabajo;	
		}
		
		function GetOrdenesTrabajoTareas(IdOrdenTrabajo)
		{
			var arr 	= new Array();
			var obj;
			var opt;
			var oOrdenesTrabajoTareas;
			
			arr['IdOrdenTrabajo'] = IdOrdenTrabajo;
			obj = SendXMLRequest('OrdenesTrabajoTareas', 'GetAll', null, arr);
			if (obj.Status.Id != 0)
			{
				alert(obj.Status.Description);
				return;
			}
			
			oOrdenesTrabajoTareas = obj.Response.Rows;
			
			return oOrdenesTrabajoTareas;
		}

		function GetEstadoUnidad(IdEstado)
		{
			var arr = new Array();
			var obj;
			var oMarca;

			if ((IdEstado == '') || (IdEstado == '0'))
				return;
						
			arr['IdEstado'] = IdEstado;
			obj = SendXMLRequest('EstadosUnidad', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oEstadoUnidad = obj.Response;
		
			return oEstadoUnidad;	
		}
		

		function GetMarca(IdMarca)
		{
			var arr = new Array();
			var obj;
			var oMarca;

			if ((IdMarca == '') || (IdMarca == '0'))
				return;
						
			arr['IdMarca'] = IdMarca;
			obj = SendXMLRequest('Marcas', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oMarca = obj.Response;
		
			return oMarca;	
		}
		
		function GetTipoVehiculo(IdTipoVehiculo)
		{
			var arr = new Array();
			var obj;
			var oTipoVehiculo;

			if ((IdTipoVehiculo == '') || (IdTipoVehiculo == '0'))
				return;
						
			arr['IdTipoVehiculo'] = IdTipoVehiculo;
			obj = SendXMLRequest('TiposVehiculo', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTipoVehiculo = obj.Response;
		
			return oTipoVehiculo;	
		}
		
		function GetTipoUso(IdTipoUso)
		{
			var arr = new Array();
			var obj;
			var oTipoUso;

			if ((IdTipoUso == '') || (IdTipoUso == '0'))
				return;
						
			arr['IdTipoUso'] = IdTipoUso;
			obj = SendXMLRequest('TiposUso', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTipoUso = obj.Response;
		
			return oTipoUso;	
		}
		
		function GetTipoCarroceria(IdTipoCarroceria)
		{
			var arr = new Array();
			var obj;
			var oTipoCarroceria;

			if ((IdTipoCarroceria == '') || (IdTipoCarroceria == '0'))
				return;
						
			arr['IdTipoCarroceria'] = IdTipoCarroceria;
			obj = SendXMLRequest('TiposCarroceria', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTipoCarroceria = obj.Response;
		
			return oTipoCarroceria;	
		}
		
		function GetDestinoVehiculo(IdDestinoVehiculo)
		{
			var arr = new Array();
			var obj;
			var oDestinoVehiculo;

			if ((IdDestinoVehiculo == '') || (IdDestinoVehiculo == '0'))
				return;
						
			arr['IdDestinoVehiculo'] = IdDestinoVehiculo;
			obj = SendXMLRequest('DestinosVehiculo', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oDestinoVehiculo = obj.Response;
		
			return oDestinoVehiculo;	
		}
		
		function GetRubro(IdRubro)
		{
			var arr = new Array();
			var obj;
			var oRubro;

			if ((IdRubro == '') || (IdRubro == '0'))
				return;
						
			arr['IdRubro'] = IdRubro;
			obj = SendXMLRequest('Rubros', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oRubro = obj.Response;
		
			return oRubro;	
		}
		
		function GetPais(IdPais)
		{
			var arr = new Array();
			var obj;
			var oPais;

			if ((IdPais == '') || (IdPais == '0'))
				return;
						
			arr['IdPais'] = IdPais;
			obj = SendXMLRequest('Paises', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oPais = obj.Response;
		
			return oPais;	
		}
		
		function GetProvincia(IdProvincia)
		{
			var arr = new Array();
			var obj;
			var oProvincia;

			if ((IdProvincia == '') || (IdProvincia == '0'))
				return;
						
			arr['IdProvincia'] = IdProvincia;
			obj = SendXMLRequest('Provincias', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oProvincia = obj.Response;
		
			return oProvincia;	
		}
		
		function GetProveedor(IdProveedor)
		{
			var arr = new Array();
			var obj;
			var oProveedor;

			if ((IdProveedor == '') || (IdProveedor == '0'))
				return;
						
			arr['IdProveedor'] = IdProveedor;
			obj = SendXMLRequest('Proveedores', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oProveedor = obj.Response;
		
			return oProveedor;	
		}
		
		function GetConcepto(IdConcepto)
		{
			var arr = new Array();
			var obj;
			var oConcepto;

			if ((IdConcepto == '') || (IdConcepto == '0'))
				return;
						
			arr['IdConcepto'] = IdConcepto;
			obj = SendXMLRequest('Conceptos', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oConcepto = obj.Response;
		
			return oConcepto;	
		}

		function GetTipoModelo(IdTipoModelo)
		{
			var arr = new Array();
			var obj;
			var oTipoModelo;

			if ((IdTipoModelo == '') || (IdTipoModelo == '0'))
				return;
						
			arr['IdTipoModelo'] = IdTipoModelo;
			obj = SendXMLRequest('TiposModelo', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTipoModelo = obj.Response;
		
			return oTipoModelo;	
		}
		

		function GetCategoriaModelo(IdCategoriaModelo)
		{
			var arr = new Array();
			var obj;
			var oCategoriaModelo;

			if ((IdCategoriaModelo == '') || (IdCategoriaModelo == '0'))
				return;
						
			arr['IdCategoriaModelo'] = IdCategoriaModelo;
			obj = SendXMLRequest('CategoriasModelo', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oCategoriaModelo = obj.Response;
		
			return oCategoriaModelo;	
		}


		function GetUsuario(IdUsuario)
		{
			var arr = new Array();
			var obj;
			var oUsuario;

			if ((IdUsuario == '') || (IdUsuario == '0'))
				return;
						
			arr['IdUsuario'] = IdUsuario;
			obj = SendXMLRequest('Usuarios', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
		
			oUsuario = obj.Response;
		
			return oUsuario;	
		}
		

		function GetAcreedor(IdAcreedor)
		{
			var arr = new Array();
			var obj;
			var oAcreedor;

			if ((IdAcreedor == '') || (IdAcreedor == '0'))
				return;
						
			arr['IdAcreedor'] = IdAcreedor;
			obj = SendXMLRequest('Acreedores', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oAcreedor = obj.Response;
		
			return oAcreedor;
		}

		function GetCupon(Numero)
		{
			var arr = new Array();
			var obj;
			var oCupon;

			if ((Numero == '') || (Numero == '0'))
				return;
						
			arr['Numero'] = Numero;
			obj = SendXMLRequest('CuponesDescuento', 'GetByNumero', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oCupon = obj.Response;
		
			return oCupon;
		}

		function GetCliente(IdCliente)
		{
			var arr = new Array();
			var obj;
			var oCliente;

			if ((IdCliente == '') || (IdCliente == '0'))
				return;
						
			arr['IdCliente'] = IdCliente;
			obj = SendXMLRequest('Clientes', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oCliente = obj.Response;
		
			return oCliente;	
		}
		
		function GetPlanCuota(IdPlanCuota)
		{
			var arr = new Array();
			var obj;
			var oPlanCuota;

			if ((IdPlanCuota == '') || (IdPlanCuota == '0'))
				return;
						
			arr['IdPlanCuota'] = IdPlanCuota;
			obj = SendXMLRequest('PlanesCuotas', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oPlanCuota = obj.Response;
		
			return oPlanCuota;	
		}
		

		function GetTipoDocumento(IdTipoDocumento)
		{
			var arr = new Array();
			var obj;
			var oTipoDocumento;

			if ((IdTipoDocumento == '') || (IdTipoDocumento == '0'))
				return;
						
			arr['IdTipoDocumento'] = IdTipoDocumento;
			obj = SendXMLRequest('TiposDocumento', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTipoDocumento = obj.Response;
		
			return oTipoDocumento;	
		}
		

		function GetTipoIva(IdTipoIva)
		{
			var arr = new Array();
			var obj;
			var oTipoIva;

			if ((IdTipoIva == '') || (IdTipoIva == '0'))
				return;
						
			arr['IdTipoIva'] = IdTipoIva;
			obj = SendXMLRequest('TiposIva', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTipoIva = obj.Response;
		
			return oTipoIva;	
		}
		

		function GetFormulario(IdFormulario)
		{
			var arr = new Array();
			var obj;
			var oFormulario;

			if ((IdFormulario == '') || (IdFormulario == '0'))
				return;
						
			arr['IdFormulario'] = IdFormulario;
			obj = SendXMLRequest('Formularios', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oFormulario = obj.Response;
		
			return oFormulario;	
		}


		function GetTipoFormulario(IdTipoFormulario)
		{
			var arr = new Array();
			var obj;
			var oTipoFormulario;

			if ((IdTipoFormulario == '') || (IdTipoFormulario == '0'))
				return;
						
			arr['IdTipoFormulario'] = IdTipoFormulario;
			obj = SendXMLRequest('TiposFormulario', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTipoFormulario = obj.Response;
		
			return oTipoFormulario;	
		}


		function GetProfesion(IdProfesion)
		{
			var arr = new Array();
			var obj;
			var oProfesion;

			if ((IdProfesion == '') || (IdProfesion == '0'))
				return;
						
			arr['IdProfesion'] = IdProfesion;
			obj = SendXMLRequest('Profesiones', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oProfesion = obj.Response;
		
			return oProfesion;	
		}
		

		function GetEstadoCivil(IdEstadoCivil)
		{
			var arr = new Array();
			var obj;
			var oEstadoCivil;

			if ((IdEstadoCivil == '') || (IdEstadoCivil == '0'))
				return;
						
			arr['IdEstadoCivil'] = IdEstadoCivil;
			obj = SendXMLRequest('EstadosCiviles', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oEstadoCivil = obj.Response;
		
			return oEstadoCivil;	
		}
		
		
		function GetPartido(IdPartido)
		{
			var arr = new Array();
			var obj;
			var oPartido;

			if ((IdPartido == '') || (IdPartido == '0'))
				return;
						
			arr['IdPartido'] = IdPartido;
			obj = SendXMLRequest('Partidos', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oPartido = obj.Response;
		
			return oPartido;
		}
		
		
		function GetLocalidad(IdLocalidad)
		{
			var arr = new Array();
			var obj;
			var oLocalidad;

			if ((IdLocalidad == '') || (IdLocalidad == '0'))
				return;
						
			arr['IdLocalidad'] = IdLocalidad;
			obj = SendXMLRequest('Localidades', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oLocalidad = obj.Response;
		
			return oLocalidad;
		}


		function GetMinuta(IdMinuta)
		{
			var arr = new Array();
			var obj;
			var oMinuta;

			if ((IdMinuta == '') || (IdMinuta == '0'))
				return;
			
			arr['IdMinuta'] = IdMinuta;
			obj = SendXMLRequest('Minutas', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oMinuta = obj.Response;
		
			return oMinuta;
		}
		
		function GetCuentaGestoria(IdMinuta)
		{
			var arr = new Array();
			var obj;
			var oMinuta;

			if ((IdMinuta == '') || (IdMinuta == '0'))
				return;
			
			arr['IdMinuta'] = IdMinuta;
			obj = SendXMLRequest('Gestorias', 'GetByIdMinuta', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oGestoria = obj.Response;
		
			return oGestoria;
		}
		
		function GetMinutaUsado(IdMinuta)
		{
			var arr = new Array();
			var obj;
			var oMinuta;

			if ((IdMinuta == '') || (IdMinuta == '0'))
				return;
			
			arr['IdMinuta'] = IdMinuta;
			obj = SendXMLRequest('MinutasUsados', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oMinuta = obj.Response;
		
			return oMinuta;
		}
		
		function GetUsado(IdUsado)
		{
			var arr = new Array();
			var obj;
			var oUsado;

			if ((IdUsado == '') || (IdUsado == '0'))
				return;
			
			arr['IdUsado'] = IdUsado;
			obj = SendXMLRequest('Usados', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oUsado = obj.Response;
		
			return oUsado;
		}


		function GetFacturaUnidad(IdFactura)
		{
			var arr = new Array();
			var obj;
			var oFacturaUnidad;

			if ((IdFactura == '') || (IdFactura == '0'))
				return;
						
			arr['IdFactura'] = IdFactura;
			obj = SendXMLRequest('FacturaUnidades', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oFacturaUnidad = obj.Response;
		
			return oFacturaUnidad;
		}
		
		function GetComprobante(IdComprobante)
		{
			var arr = new Array();
			var obj;
			var oComprobante;

			if ((IdComprobante == '') || (IdComprobante == '0'))
				return;
						
			arr['IdComprobante'] = IdComprobante;
			obj = SendXMLRequest('Comprobantes', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oComprobante = obj.Response;
		
			return oComprobante;
		}
		
		function GetArticuloByCodigo(Codigo)
		{
			var arr = new Array();
			var obj;
			var oArticulo;

			if (Codigo == '')
				return;
						
			arr['Codigo'] = Codigo;
			obj = SendXMLRequest('Articulos', 'GetByCodigo', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oArticulo = obj.Response;
		
			return oArticulo;
		}
		
		function GetArticulo(IdArticulo)
		{
			var arr = new Array();
			var obj;
			var oArticulo;

			if (IdArticulo == '')
				return;
						
			arr['IdArticulo'] = IdArticulo;
			obj = SendXMLRequest('Articulos', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oArticulo = obj.Response;
		
			return oArticulo;
		}
		
		function GetCodigoTrabajo(IdCodigoTrabajo)
		{
			var arr = new Array();
			var obj;
			var oCodigoTrabajo;

			if (IdCodigoTrabajo == '')
				return;
						
			arr['IdCodigoTrabajo'] = IdCodigoTrabajo;
			obj = SendXMLRequest('CodigosTrabajo', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oCodigoTrabajo = obj.Response;
		
			return oCodigoTrabajo;
		}
		
		function GetTareaTrabajo(IdTareaTrabajo)
		{
			var arr = new Array();
			var obj;
			var oTareaTrabajo;

			if (IdTareaTrabajo == '')
				return;
						
			arr['IdTareaTrabajo'] = IdTareaTrabajo;
			obj = SendXMLRequest('TareasTrabajo', 'GetById', null, arr);
			if (obj.Status.Id != 0)
				return;
			
			oTareaTrabajo = obj.Response;
		
			return oTareaTrabajo;
		}


		function AddProveedor()
		{
			var Url = 'proveedores_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		
		function AddPais()
		{
			var Url = 'paises_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
				

		function AddProvincia(IdPais)
		{
			var Url = 'provincias_add_popup.php?IdPais=' + IdPais;
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddPartido(IdPais, IdProvincia)
		{
			var Url = 'partidos_add_popup.php?IdPais=' + IdPais + '&IdProvincia=' + IdProvincia;
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
				

		function AddSector()
		{
			var Url = 'sectores_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddPerfil()
		{
			var Url = 'perfiles_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
				

		function AddColor(Tipo)
		{
			var Url = 'colores_add_popup.php?Tipo=' + Tipo;
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddUbicacion()
		{
			var Url = 'ubicaciones_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddEstadoUnidad()
		{
			var Url = 'estadosunidad_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500');
		}
				

		function AddMarca(Tipo)
		{
			var Url = 'marcas_add_popup.php?Tipo=' + Tipo;
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		
		function AddRubro()
		{
			var Url = 'rubros_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddTipoModelo()
		{
			var Url = 'tiposmodelo_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
				

		function AddCategoriaModelo()
		{
			var Url = 'categoriasmodelo_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}


		function AddTipoDocumento(Tipo)
		{
			var Url = 'tiposdocumento_add_popup.php?Tipo=' + Tipo;
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddCliente()
		{
			var Url = 'clientes_add_popup.php';
			
			window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
		}
		
		function AddClienteResumen()
		{
			var Url = 'clientes_resumen_add_popup.php';
			
			window.open(Url, this.target, 'width=1000,height=620,scrollbars=yes'); 
		}
		
		function AddTallerUnidad(functionCerrar)
		{
			var Url = 'tallerunidades_add_popup.php';
			
			window.open(Url, this.target, 'width=1000,height=580,scrollbars=yes'); 
			
			if (functionCerrar)
				functionCerrar();
		}

		function AddClienteCondominio()
		{
			var Url = 'clientes_add_popup.php?Condominio=1';
			
			window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
		}
		
		function AddClienteReventa()
		{
			var Url = 'clientes_add_popup.php?Reventa=1';
			
			window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
		}
		
		function AddClienteCondominioConyugue()
		{
			var Url = 'clientes_add_popup.php?Condominio=1&Conyuge=' + Get('IdCliente').value;
			
			window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
		}

		function AddAcreedor()
		{
			var Url = 'acreedores_add_popup.php';
			
			window.open(Url, this.target, 'width=1000,height=700,scrollbars=yes'); 
		}


		function AddUsuario()
		{
			var Url = 'usuarios_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddTipoIva()
		{
			var Url = 'tiposiva_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddProfesion(Tipo)
		{
			var Url = 'profesiones_add_popup.php';
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddEstadoCivil(Tipo)
		{
			var Url = 'estadosciviles_add_popup.php' + Tipo;
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}
		

		function AddLocalidad(tipo)
		{
			var Url = 'localidades_add_popup.php?Tipo=' + tipo;
			
			window.open(Url, this.target, 'width=800,height=500'); 
		}


		function WordBreak(str)
		{
			var c, i, j;
			var arr;
			var newstr = '';
			
			if (str == undefined)
				return str;
				
			str = str.replace('\n', ' ');
			arr = str.split(' ');
			
			for (i=0; i<arr.length; i++)
			{
				c = 0;
				for (j=0; j<arr[i].length; j++)
				{
					newstr+= arr[i].substr(j, 1);
					if (c >= 30)
					{
						newstr += ' ';
						c = 0;
					}
					c++;
				}
				newstr+= ' ';
			}
			
			return newstr;
		}
		
		
		function ismaxlength(obj)
		{
			var mlength = obj.getAttribute ? parseInt(obj.getAttribute("maxlength")) : ""
			if (obj.getAttribute && obj.value.length>mlength)
				obj.value = obj.value.substring(0,mlength)
		}


		function IsNumeric(sText)
		{
			var ValidChars = "0123456789.";
			var IsNumber = true;
			var Char;
			
			for (i = 0; i < sText.length && IsNumber == true; i++) 
			{ 
				Char = sText.charAt(i); 
				if (ValidChars.indexOf(Char) == -1) 
				{
					IsNumber = false;
				}
			}
			
			return IsNumber;
		}


		function IsDate(dateStr)
		{
			
			var datePat = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
			var matchArray = dateStr.match(datePat); // is the format ok?
			
			if (matchArray == null)
			{
				return false;
			}
			
			day = matchArray[1];
			month = matchArray[3];
			year = matchArray[5];
			
			// check month range
			if (month < 1 || month > 12)							
				return false;
			
			if (day < 1 || day > 31)
				return false;
			
			if ((month==4 || month==6 || month==9 || month==11) && day==31)
				return false;
			
			// check for february 29th
			if (month == 2)
			{
				var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
				if (day > 29 || (day==29 && !isleap))
				{
					return false;
				}
			}
			
			return true; // date is valid
		}
		

		function IsEmail(Email)
		{
			if (Email == "")
				return false;
		
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(Email))
				return true;

			return false;
		}

		
		function ToString(str)
		{
			var html_entity_decode = new Array();

//			html_entity_decode['&amp;'] 	= '&';
//			html_entity_decode['&aacute;'] 	= 'á';
//			html_entity_decode['&Acirc;'] 	= 'Â';
//			html_entity_decode['&acirc;'] 	= 'â';
//			html_entity_decode['&AElig;'] 	= 'Æ';
//			html_entity_decode['&aelig;'] 	= 'æ';
//			html_entity_decode['&Agrave;'] 	= 'À';
//			html_entity_decode['&agrave;'] 	= 'à';
//			html_entity_decode['&Aring;'] 	= 'Å';
//			html_entity_decode['&aring;'] 	= 'å';
//			html_entity_decode['&Atilde;'] 	= 'Ã';
//			html_entity_decode['&atilde;'] 	= 'ã';
//			html_entity_decode['&Auml;'] 	= 'Ä';
//			html_entity_decode['&auml;'] 	= 'ä';
//			html_entity_decode['&Eacute;'] 	= 'É';
//			html_entity_decode['&eacute;'] 	= 'e';
//			html_entity_decode['&Ecirc;'] 	= 'Ê';
//			html_entity_decode['&ecirc;'] 	= 'ê';
//			html_entity_decode['&Egrave;'] 	= 'È';
//			html_entity_decode['&ucirc;'] 	= 'û';
//			html_entity_decode['&Ugrave;'] 	= 'Ù';
//			html_entity_decode['&ugrave;'] 	= 'ù';
//			html_entity_decode['&Uuml;'] 	= 'Ü';
//			html_entity_decode['&uuml;'] 	= 'ü';
//			html_entity_decode['&Yacute;'] 	= 'Ý';
//			html_entity_decode['&yacute;'] 	= 'ý';
//			html_entity_decode['&yuml;'] 	= 'ÿ';
//			html_entity_decode['&Yuml;'] 	= 'Ÿ';
//			html_entity_decode['&gt;'] 		= '>';
//			html_entity_decode['&lt;'] 		= '<';
//			html_entity_decode['&ntilde;'] 	= 'ñ';
//			html_entity_decode['&Ntilde;'] 	= 'Ñ';

			html_entity_decode['&amp;'] 	= '&';
			html_entity_decode['&aacute;'] 	= '\u00e1';
			html_entity_decode['&Acirc;'] 	= 'A';
			html_entity_decode['&acirc;'] 	= 'a';
			html_entity_decode['&AElig;'] 	= 'a';
			html_entity_decode['&aelig;'] 	= 'a';
			html_entity_decode['&Agrave;'] 	= 'A';
			html_entity_decode['&agrave;'] 	= 'a';
			html_entity_decode['&Aring;'] 	= 'A';
			html_entity_decode['&aring;'] 	= 'a';
			html_entity_decode['&Atilde;'] 	= 'A';
			html_entity_decode['&atilde;'] 	= 'a';
			html_entity_decode['&Auml;'] 	= 'A';
			html_entity_decode['&auml;'] 	= 'a';
			html_entity_decode['&Eacute;'] 	= '\u00c9';
			html_entity_decode['&eacute;'] 	= '\u00e9';
			html_entity_decode['&Ecirc;'] 	= 'E';
			html_entity_decode['&ecirc;'] 	= 'e';
			html_entity_decode['&Egrave;'] 	= 'E';
			html_entity_decode['&iacute;'] 	= '\u00ed';
			html_entity_decode['&oacute;'] 	= '\u00f3';
			html_entity_decode['&uacute;'] 	= '\u00fa';
			html_entity_decode['&ucirc;'] 	= 'u';
			html_entity_decode['&Ugrave;'] 	= 'U';
			html_entity_decode['&ugrave;'] 	= 'u';
			html_entity_decode['&Uuml;'] 	= 'U';
			html_entity_decode['&uuml;'] 	= 'u';
			html_entity_decode['&Yacute;'] 	= 'Y';
			html_entity_decode['&yacute;'] 	= 'y';
			html_entity_decode['&yuml;'] 	= 'y';
			html_entity_decode['&Yuml;'] 	= 'Y';
			html_entity_decode['&gt;'] 		= '>';
			html_entity_decode['&lt;'] 		= '<';
			html_entity_decode['&ntilde;'] 	= '\u00f1';
			html_entity_decode['&Ntilde;'] 	= '\u00d1';

			try
			{
				for (var index in html_entity_decode)
				{
					if (str.length > 0 && str.indexOf(index) != -1)
					{
						str = str.replace(index, html_entity_decode[index]);	
					}
				}
			}
			catch(e) {}
			
			return str;	
		}
		
				
		function DateFromDBtoOutput(dateStr)
		{
			var longitud;
			var SplitArray;
			var OutputDate;
			var SplitDay;
			
			if (dateStr == null)
				return false;			
			
			SplitArray = dateStr.split ("-"); // el separador es el espacio
			
			SplitDay =	SplitArray[2].split (" "); 				
			
			OutputDate = SplitDay[0] + "/" + SplitArray[1] + "/" + SplitArray[0];
			
			return OutputDate; 
		}
		
		
		function OptionSetSelected(element, value)
		{
			var i;
			var opts = Get(element).options;
			
			if (!element)
				return false;
			
			for (i=0; i<opts.length; i++)
			{
				if (opts[i].value == value)
				{
					opts[i].selected = true;
					return true;
				}
			}
			
			return false;
		}
		

		function ClearCombo(element, texto)
		{
			var i;
			var opts = Get(element).options;
				
			if (!element)
				return false;

			if (opts == undefined)
				return false;

			if (texto == null) 
				texto = '[Seleccione...]';
				
			for (i=opts.length; i>0; i--)
				opts[i] = null;
			
			opts[0] = new Option(texto, '0');
		}

		
		function URLEncode(plaintext)
		{
			var SAFECHARS = "0123456789" +					// Numeric
							"ABCDEFGHIJKLMNOPQRSTUVWXYZÑ" +	// Alphabetic
							"abcdefghijklmnopqrstuvwxyzñ" +
							"-_.!~*'()";					// RFC2396 Mark characters
			var HEX = "0123456789ABCDEF";
		
			var encoded = "";
			for (var i = 0; i < plaintext.length; i++)
			{
				var ch = plaintext.charAt(i);
				if (ch == " ")
				{
					encoded += "+";				// x-www-urlencoded, rather than %20
				}
				else if (SAFECHARS.indexOf(ch) != -1)
				{
					encoded += ch;
				}
				else
				{
					var charCode = ch.charCodeAt(0);
					if (charCode > 255)
					{
						alert( "Unicode Character '" 
								+ ch 
								+ "' cannot be encoded using standard URL encoding.\n" +
								  "(URL encoding only supports 8-bit characters.)\n" +
								  "A space (+) will be substituted." );
						encoded += "+";
					}
					else
					{
						encoded += "%";
						encoded += HEX.charAt((charCode >> 4) & 0xF);
						encoded += HEX.charAt(charCode & 0xF);
					}
				}
			} // for
		
			return encoded;
		}
		
		
		function URLDecode(encodedtext)
		{
		   var HEXCHARS = "0123456789ABCDEFabcdef"; 
		   var encoded = encodedtext;
		   var plaintext = "";
		   var i = 0;
		   
		   while (i < encoded.length)
		   {
			   var ch = encoded.charAt(i);
			   if (ch == "+")
			   {
				   plaintext += " ";
				   i++;
			   }
			   else if (ch == "%")
			   {
					if (i < (encoded.length-2)
							&& HEXCHARS.indexOf(encoded.charAt(i+1)) != -1 
							&& HEXCHARS.indexOf(encoded.charAt(i+2)) != -1 )
					{
						plaintext += unescape( encoded.substr(i,3) );
						i += 3;
					}
					else
					{
						alert( 'Bad escape combination near ...' + encoded.substr(i) );
						plaintext += "%[ERROR]";
						i++;
					}
				}
				else
				{
				   plaintext += ch;
				   i++;
				}
			} // while
			
			return plaintext;
		}


		function toHTML(str)
		{
			var html_entity_decode = new Array();
			
			html_entity_decode['\u00e1'] 	= '&aacute;'; 	//á
			html_entity_decode['\u00c9'] 	= '&eacute;'; 	//é
			html_entity_decode['\u00e9'] 	= '&iacute;'; 	//í
			html_entity_decode['\u00ed'] 	= '&oacute;'; 	//ó
			html_entity_decode['\u00f3'] 	= '&uacute;'; 	//ú			
			html_entity_decode['&'] 		= '&amp;';		//&
			
			try
			{
				for (var index in html_entity_decode) 
				{
					if (str.indexOf(index) != -1)
					{
						str = str.replace(index, html_entity_decode[index]);	
					}
				}
			}
			catch(e) {}
			
			return str;	
		}
		
		
		function StrToUpper(Element)
		{
			
			/*var Campo = document.getElementById(Element);
			
			if (Campo == undefined)
				return false;
			
			Campo.value = Campo.value.toUpperCase();
			
			return true;*/
		}
		
		
		function format_number(pnumber, decimals)
		{
			if (isNaN(pnumber)) { return 0};
			if (pnumber=='') { return 0};
			
			var snum = new String(pnumber);
			var sec = snum.split('.');
			var whole = parseFloat(sec[0]);
			var result = '';
			
			if(sec.length > 1)
			{
				var dec = new String(sec[1]);
				dec = String(parseFloat(sec[1])/Math.pow(10,(dec.length - decimals)));
				dec = String(whole + Math.round(parseFloat(dec))/Math.pow(10,decimals));
				var dot = dec.indexOf('.');
				if(dot == -1)
				{
					dec += '.'; 
					dot = dec.indexOf('.');
				}
				
				while(dec.length <= dot + decimals) { dec += '0'; }
				result = dec;
			} 
			else
			{
				var dot;
				var dec = new String(whole);
				dec += '.';
				dot = dec.indexOf('.');		
				
				while(dec.length <= dot + decimals) { dec += '0'; }
				result = dec;
			}	
			
			return result;
		}		
		</script><?php
		
		$justOne = true;
	}
}


function toString($str)
{
    $html_entity_decode = array 
	(
        "&amp;" 	=>  "&",
        "&aacute;" 	=>  "á",
        "&Acirc;" 	=>  "Â",
        "&acirc;" 	=>  "â",
        "&AElig;" 	=>  "Æ",
        "&aelig;" 	=>  "æ",
        "&Agrave;" 	=>  "À",
        "&agrave;" 	=>  "à",
        "&Aring;" 	=>  "Å",
        "&aring;" 	=>  "å",
        "&Atilde;" 	=>  "Ã",
        "&atilde;" 	=>  "ã",
        "&Auml;" 	=>  "Ä",
        "&auml;" 	=>  "ä",
        "&Eacute;" 	=>  "É",
        "&eacute;" 	=>  "é",
        "&Ecirc;" 	=>  "Ê",
        "&ecirc;" 	=>  "ê",
        "&Egrave;" 	=>  "È",
        "&ucirc;" 	=>  "û",
        "&Ugrave;" 	=>  "Ù",
        "&ugrave;" 	=>  "ù",
        "&Uuml;" 	=>  "Ü",
        "&uuml;" 	=>  "ü",
        "&Yacute;" 	=>  "Ý",
        "&yacute;" 	=>  "ý",
        "&yuml;" 	=>  "ÿ",
        "&Yuml;" 	=>  "Ÿ",
		"&gt;" 		=>  ">",
		"&lt;" 		=>  "<",
		"&uacute;" 	=>  "ú",
		"&Uacute;" 	=>  "Ú",
		"&iacute;" 	=>  "í",
		"&Iacute;" 	=>  "Í",
		"&oacute;" 	=>  "ó",
		"&Oacute;" 	=>  "Ó",
		"&Ntilde;"	=>	"Ñ",
		"&ntilde;"	=>	"ñ"
    );
	
    foreach ($html_entity_decode as $key => $value) 
	{
        $str = str_replace($key, $value, $str);
    }
	
    return $str;	
}


function toHTML($str)
{
	$html_entities = array 
	(
        "&" =>  "&amp;",
        "á" =>  "&aacute;",
        "Â" =>  "&Acirc;",
        "â" =>  "&acirc;",
        "Æ" =>  "&AElig;",
        "æ" =>  "&aelig;",
        "À" =>  "&Agrave;",
        "à" =>  "&agrave;",
        "Å" =>  "&Aring;",
        "å" =>  "&aring;",
        "Ã" =>  "&Atilde;",
        "ã" =>  "&atilde;",
        "Ä" =>  "&Auml;",
        "ä" =>  "&auml;",
        "Ç" =>  "C",
        "ç" =>  "c",
        "É" =>  "&Eacute;",
        "é" =>  "&eacute;",
        "Ê" =>  "&Ecirc;",
        "ê" =>  "&ecirc;",
        "È" =>  "&Egrave;",
		"ñ" =>  "&ntilde;",
		"Ñ" =>  "&Ntilde;",
        "û" =>  "&ucirc;",
        "Ù" =>  "&Ugrave;",
        "ù" =>  "&ugrave;",
        "Ü" =>  "&Uuml;",
        "ü" =>  "&uuml;",
        "Ý" =>  "&Yacute;",
        "ý" =>  "&yacute;",
        "ÿ" =>  "&yuml;",
        "Ÿ" =>  "&Yuml;",
		">" =>  "&gt;",
		"<" =>  "&lt;",
		"Í" =>  "&Iacute;",
		"í"	=>	"&iacute;",
		"Ó"	=>	"&Oacute;",
		"ó"	=>	"&oacute;",
		"Á"	=>	"&Aacute;",
		"Ú"	=>	"&Uacute;",
		"ú"	=>	"&uacute;"		
    );

	/*    
	foreach ($html_entities as $key => $value) 
	{
		if ($str == utf8_encode($key))
	        $str = str_replace(utf8_encode($key), $value, $str);
    }
	*/

    foreach ($html_entities as $key => $value) 
	{
		$str = str_replace(utf8_encode($key), $value, $str);
    }

    return $str;
}


/* XML helper functions */
function ProcessArray($arr)
{
	$ret = '';
	foreach ($arr as $name => $value)
	{
		if (is_numeric($name))	$ret.= "<Row id=\"$name\">";
		else					$ret.= "<".$name.">";
		if (is_bool($obj->$name))
		{
			if ($obj->$name)
				$ret.= "true";
			else
				$ret.= "false";
		}
		elseif (is_array($value))
			$ret.= ProcessArray($value);
		elseif (is_object($value))
			$ret.= ProcessObject($value);
		elseif (is_scalar($value))
			$ret.= ProcessScalar($value);
			
		if (is_numeric($name))	$ret.= "</Row>";
		else					$ret.= "</".$name.">";
	}
	return $ret;
}

function ProcessObject($obj)
{
	$ret = '';
	foreach (get_object_vars($obj) as $name => $value)
	{		
		$ret.= "<".$name.">";			
		if (is_bool($value))
		{
			if ($value)
				$ret.= "true";
			else
				$ret.= "false";
		}
		elseif (is_array($obj->$name))
			$ret.= ProcessArray($value);
		elseif (is_object($obj->$name))
			$ret.= ProcessObject($value);
		elseif (is_scalar($obj->$name))
			$ret.= ProcessScalar($value);
		$ret.= "</".$name.">";
	}
	return $ret;
}

function ProcessScalar($scalar)
{
	//return str_replace("&", "&amp;", toHTML($scalar));
	return str_replace("&", "&amp;", toString($scalar));
}
/* Fin XML helper functions */

function _urlencode($str)
{
	$str = str_replace("&", "%26", $str);
	$str = str_replace("?", "%3f", $str);
	$str = str_replace("/", "%2f", $str);
	return $str;
}


function _urldecode($str)
{
	$str = str_replace("%26", "&", $str);
	$str = str_replace("%3f", "?", $str);
	$str = str_replace("%2f", "/", $str);
	return $str;
}

?>