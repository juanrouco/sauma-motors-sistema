<?php

class ConfiguracionFactura
{
	const CertificadoHomologacion = "\\motopierprueba.crt";
	
	const CertificadoProduccion = "\\certificado.crt";
	
	const ClavePrivada = "\\clave_privada.key";
	
	const Testing = false;
	
	const UrlAutenticacionTesting = "https://wsaahomo.afip.gov.ar/ws/services/LoginCms";
	
	const UrlAutenticacionProduccion = "https://wsaa.afip.gov.ar/ws/services/LoginCms";
	
	const UrlFacturacionTesting = "https://wswhomo.afip.gov.ar/wsfev1/service.asmx?WSDL";
	
	const UrlFacturacionProduccion = "https://servicios1.afip.gov.ar/wsfev1/service.asmx?WSDL";
	
	const CuitHomologacion = "30711940657";
	/*const Cuit = "23216362799";
	
	const CuitLetras = "23-21636279-9";*/
	
	const Cuit = "30711940657";
	
	const CuitLetras = "30-71194065-7";
	
	const IIBB = "30-71194065-7";
	
	const FechaInicioActividad = "01/08/2011";
	
	const RazonSocial = "ACTION MOTORSPORTS S.R.L";
	
	const Direccion = "Av. Del Libertador 14099";
	const Direccion2 = "(1640) Martinez, Provincia de Buenos Aires";
	const DireccionAlt = "Santa Fe 1149";
	const DireccionAlt2 = "(1641) Acassuso, Provincia de Buenos Aires";
	
	const Fax = "Fax: 011 3986-3576";
	
	const MontoMiPyme = 3958316;
	
	const PuntoVenta = 5;
	const PuntoVentaPV = 4;
	
	const LeyendaMonotributo = "El crédito fiscal discriminado en el presente comprobante, sólo podrá ser computado a efectos del Régimen de Sostenimiento e Inclusión Fiscal para Contribuyentes de la Ley Nº 27.618’.";
}

?>