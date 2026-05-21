<?php

abstract class TipoPago
{ 		
	const Efectivo 			= 1;
	const DepositoEfectivo 	= 2;
	const Transferencia 	= 3;
	const Cheque 			= 4;
	const DepositoCheque 	= 5;
	const Redondeo 			= 6;
	const Retenciones 		= 7;
	const Pagare 			= 8;
	const Credito 			= 9;
	const Debito 			= 10;
	const CreditoPersonal 	= 11;
	const MercadoPago	 	= 12;
	const TodoPago	 		= 13;

	static function GetById($IdTipoPago)
	{
		switch($IdTipoPago)
		{
			case self::Efectivo:
				return "Efectivo";

			case self::DepositoEfectivo:
				return "Deposito Efectivo";
				
			case self::Transferencia:
				return "Transferencia";
			
			case self::Cheque:
				return "Cheque";
			
			case self::DepositoCheque:
				return "Deposito Cheque";
				
			case self::Redondeo:
				return "Correccion Redondeo";
				
			case self::Retenciones:
				return "Retenciones";
				
			case self::Pagare:
				return "Pagare";
				
			case self::Credito:
				return "Tarjeta Credito";
				
			case self::Debito:
				return "Tarjeta Debito";
				
			case self::CreditoPersonal:
				return "Credito Personal";
				
			case self::MercadoPago:
				return "Mercadopago";
				
			case self::TodoPago:
				return "TodoPago";

			default:
				return "No Asignado";
		}
	}
	
	
	static function GetAll()
	{
		return array
		(
			array("IdTipoPago" => self::Efectivo, 			"Descripcion" => "Efectivo"),
			array("IdTipoPago" => self::DepositoEfectivo, 	"Descripcion" => "Deposito Efectivo"),
			array("IdTipoPago" => self::DepositoCheque, 	"Descripcion" => "Deposito Cheque"),
			array("IdTipoPago" => self::Cheque, 			"Descripcion" => "Cheque"),
			array("IdTipoPago" => self::Transferencia, 		"Descripcion" => "Transferencia"),
			array("IdTipoPago" => self::Redondeo, 			"Descripcion" => "Correccion Redondeo"),
			array("IdTipoPago" => self::Retenciones, 		"Descripcion" => "Retenciones"),
			array("IdTipoPago" => self::Pagare, 			"Descripcion" => "Pagare"),
			array("IdTipoPago" => self::Credito, 			"Descripcion" => "Tarjeta Credito"),
			array("IdTipoPago" => self::Debito, 			"Descripcion" => "Tarjeta Debito"),
			array("IdTipoPago" => self::CreditoPersonal, 	"Descripcion" => "Credito Personal"),
			array("IdTipoPago" => self::MercadoPago, 		"Descripcion" => "Mercadopago"),
			array("IdTipoPago" => self::TodoPago, 		"Descripcion" => "TodoPago")
			
		);
	}
	
	
	static function GetAllPV()
	{
		return array
		(
			array("IdTipoPago" => self::Efectivo, 			"Descripcion" => "Efectivo"),
			array("IdTipoPago" => self::Credito, 			"Descripcion" => "Tarjeta Credito"),
			array("IdTipoPago" => self::Debito, 			"Descripcion" => "Tarjeta Debito"),
			array("IdTipoPago" => self::MercadoPago, 		"Descripcion" => "Mercadopago"),
			array("IdTipoPago" => self::TodoPago, 			"Descripcion" => "TodoPago"),
			array("IdTipoPago" => self::Transferencia, 		"Descripcion" => "Transferencia")
			
		);
	}
	
	
	static function GetIndexPV($IdTipoPago)
	{
		if ($IdTipoPago == self::Efectivo)
			return 0;
		if ($IdTipoPago == self::Credito)
			return 1;
		if ($IdTipoPago == self::Debito)
			return 2;
		if ($IdTipoPago == self::MercadoPago)
			return 3;
		if ($IdTipoPago == self::TodoPago)
			return 4;
		return 5;
	}
}

?>