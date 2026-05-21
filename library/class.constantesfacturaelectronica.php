<?php

class ConstantesFacturaElectronica
{
	//Tipos de comprobantes
	const FacturaA					= 1;
	const NotaDebitoA				= 2;
	const NotaCreditoA				= 3;
	const FacturaB					= 6;
	const NotaDebitoB				= 7;
	const NotaCreditoB				= 8;
	const FacturaElectronicaA		= 201;
	const NotaDebitoElectronicaA	= 202;
	const NotaCreditoElectronicaA	= 203;
	const FacturaElectronicaB		= 206;
	const NotaDebitoElectronicaB	= 207;
	const NotaCreditoElectronicaB	= 208;
	
	//Tipos de concepto
	const ConceptoProducto = 1;
	const ConceptoServicio = 2;
	const ConceptoProdServ = 3;
	
	//tipos de documento
	const CedulaPcia	= 1;
	const Cuit			= 80;
	const Cuil			= 86;
	const Cedula		= 87;
	const LE			= 89;
	const LC			= 90;
	const DNI			= 96;
	const Pasaporte		= 94;
	const DocumentoOtro	= 99;
	
	//Alicuotas de IVA
	const Iva0	= 3;
	const Iva10	= 4;
	const Iva21	= 5;
	const Iva27	= 6;
	const Iva5	= 8;
	const Iva25	= 9;
	
	//monedas
	const Pesos = "PES";
	
	//Tipos de tributo
	const ImpuestosNacionales	= 1;
	const ImpuestosProvinciales = 2;
	const ImpuestosMunicipales	= 3;
	const ImpuestosInternos		= 4;
	const ImpuestoOtro			= 99;

}

?>