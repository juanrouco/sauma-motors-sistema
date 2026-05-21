<?php

abstract class Entidades 
{ 		
	const Efectivo				= 1;
	const Paypal				= 2;
	const MercadoPago			= 3;
	const Tarjeta				= 4;
	const CuentaCorriente		= 5;
	const DineroMail			= 6;
	const ContraReembolso		= 7;
	const TransferenciaDeposito	= 8;

	static function GetById($IdEntidad)
	{
		switch($IdEntidad)
		{
			case self::Efectivo:
				return "Efectivo";

			case self::Paypal:
				return "Paypal";
				
			case self::MercadoPago:
				return "MercadoPago";

			case self::Tarjeta:
				return "Tarjeta";

			case self::CuentaCorriente:
				return "Cuenta Corriente";

			case self::DineroMail:
				return "Dinero Mail";

			case self::ContraReembolso:
				return "Contra Reembolso";

			case self::TransferenciaDeposito:
				return "Transferencia - Deposito";

			default:
				return "No Asignado";
		}
	}
	
	static function GetAll()
	{
		return array
		(
			array("IdEntidad" => self::Efectivo, 				"Descripcion" => "Efectivo"),
			array("IdEntidad" => self::Paypal, 					"Descripcion" => "Paypal"),
			array("IdEntidad" => self::MercadoPago, 			"Descripcion" => "Mercado Pago"),
			array("IdEntidad" => self::Tarjeta, 				"Descripcion" => "Tarjeta"),
			array("IdEntidad" => self::CuentaCorriente, 		"Descripcion" => "Cuenta Corriente"),
			array("IdEntidad" => self::DineroMail, 				"Descripcion" => "Dinero Mail"),
			array("IdEntidad" => self::ContraReembolso, 		"Descripcion" => "Contra Reembolso"),
			array("IdEntidad" => self::TransferenciaDeposito, 	"Descripcion" => "Transferencia - Deposito")
		);
	}	
}

?>