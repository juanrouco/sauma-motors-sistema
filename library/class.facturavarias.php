<?php 

require_once('class.dbaccess.php');
require_once('class.facturavaria.php');
require_once('class.comprobantes.php');
require_once('class.facturavariadetalles.php');
require_once('class.notascredito.php');
require_once('class.conceptosfacturas.php');
require_once('class.filter.php');
require_once('class.page.php');

class FacturaVarias extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['NumeroComprobante'])) && ($filter['NumeroComprobante'] != ''))
			$sql.= " AND NumeroComprobante LIKE '%" . DB::StringUnquoted($filter['NumeroComprobante']) . "%'";
		
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >=" . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <=" . DB::Date($filter['FechaHasta']);

		if ((isset($filter['Cliente'])) && ($filter['Cliente'] != ''))
			$sql.= " AND IdCliente IN (SELECT IdCliente FROM TB_Clientes WHERE RazonSocial LIKE '%" . DB::StringUnquoted($filter['Cliente']) . "%')";
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVarias";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdFactura DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oFacturaVaria = new FacturaVaria();
			$oFacturaVaria->ParseFromArray($oRow);
			
			array_push($arr, $oFacturaVaria);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	

	public function GetById($IdFactura)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVarias";
		$sql.= " WHERE IdFactura = " . DB::Number($IdFactura);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaVaria = new FacturaVaria();
		$oFacturaVaria->ParseFromArray($oRow);
		
		return $oFacturaVaria;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVarias";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(FacturaVaria $oFacturaVaria)
	{
		$arr = array
		(
			'IdCliente' 		=> DB::Number($oFacturaVaria->IdCliente),
			'IdComprobante' 	=> DB::Number($oFacturaVaria->IdComprobante),
			'NumeroComprobante' => DB::String($oFacturaVaria->NumeroComprobante),
			'Fecha' 			=> DB::Date($oFacturaVaria->Fecha),
			'Detalle' 			=> DB::String($oFacturaVaria->Detalle),
			'Subtotal' 			=> DB::Number($oFacturaVaria->Subtotal),
			'Iva10' 			=> DB::Number($oFacturaVaria->Iva10),
			'Iva21' 			=> DB::Number($oFacturaVaria->Iva21),
			'Total' 			=> DB::Number($oFacturaVaria->Total)
		);
		
		if (!$this->Insert('TB_FacturaVarias', $arr))
			return false;

		/* asignamos el id generado */
		$oFacturaVaria->IdFactura = DBAccess::GetLastInsertId();
			
		return $oFacturaVaria;
	}


	public function Update(FacturaVaria $oFacturaVaria)
	{
		$where = " IdFactura = " . DB::Number($oFacturaVaria->IdFactura);

		$arr = array
		(
			'Fecha' 			=> DB::Date($oFacturaVaria->Fecha),
			'NumeroComprobante' => DB::String($oFacturaVaria->NumeroComprobante),
			'Detalle' 			=> DB::String($oFacturaVaria->Detalle),
			'Subtotal' 			=> DB::Number($oFacturaVaria->Subtotal),
			'Iva10' 			=> DB::Number($oFacturaVaria->Iva10),
			'Iva21' 			=> DB::Number($oFacturaVaria->Iva21),
			'Total' 			=> DB::Number($oFacturaVaria->Total)
		);
		
		if (!DBAccess::Update('TB_FacturaVarias', $arr, $where))
			return false;
			
		return $oFacturaVaria;
	}
	
	
	public function Delete($IdFactura)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFactura = " . DB::Number($IdFactura);

		if (!DBAccess::Delete('TB_FacturaVariaDetalles', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_FacturaVarias', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}	
	

	public function GetByIdComprobante($IdComprobante)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_FacturaVarias";
		$sql.= " WHERE IdComprobante = " . DB::Number($IdComprobante);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oFacturaVaria = new FacturaVaria();
		$oFacturaVaria->ParseFromArray($oRow);
		
		return $oFacturaVaria;		
	}
	
	public function ExportCsv(array $filter = NULL)
	{
		$oComprobantes				= new Comprobantes();
		$oFacturaVariaDetalles 		= new FacturaVariaDetalles();
		$oClientes					= new Clientes();
		$oConceptosFacturas			= new ConceptosFacturas();
		
		$arrFacturas = $this->GetAll($filter);
		
		$arrData = array();
		
		$item = array();
		/* determinamos el encabezado */
		$item = array(
			"FECHA", 
			"TIPO FACTURA", 
			"NUMERO", 
			"CLIENTE", 
			"CUIT", 
			"NETO", 
			'IVA 10.5%', 
			'IVA 21%',
			'NO GRAVADO',
			'TOTAL');
			
		$arrConceptos = $oConceptosFacturas->GetAll();
		
		$header = array();
		foreach ($arrConceptos as $oConcepto)
		{
				$item[] = $oConcepto->Nombre;
		}
		
		$arrData[] = $item;
		
		foreach ($arrFacturas as $oFactura)
		{
			$oComprobante = $oComprobantes->GetById($oFactura->IdComprobante);
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			
			$subtotal = $oComprobante->Importe - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->ImpuestoInterno - $oComprobante->PercepcionIIBB;
			$iva10 = $oComprobante->ImporteIva10;
			$iva21 = $oComprobante->ImporteIva21;
			
			$NoGravrado = $subtotal - ($iva10 / 0.105) - ($iva21 / 0.21);
			if (abs($NoGravrado) < 0.1)
				$NoGravrado = 0;
			
			$item = array(
				trim(CambiarFecha($oComprobante->Fecha)), 
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				
				
					
				trim(number_format($subtotal - $NoGravrado, 2, ',', '.')),
				trim(number_format($iva10, 2, ',', '.')),
				trim(number_format($iva21, 2, ',', '.')),
				trim(number_format($NoGravrado, 2, ',', '.')),
				trim(number_format($oComprobante->Importe, 2, ',', '.')),
				);
			
				foreach ($arrConceptos as $oConcepto)
				{
					if ($arrFCC = $oFacturaVariaDetalles->GetAllByFacturaVariaAndConcepto($oFactura, $oConcepto->Nombre))
					{
						$imp = 0;
						foreach ($arrFCC as $oFCC)
						{
							if ($oConcepto->IvaGravado)
								$imp+= $oFCC->Importe / 1.21;
							else
								$imp+= $oFCC->Importe;
						}
						
						$item[] = number_format($imp, 2, ',', '.');
					}
					else
						$item[] = '0.00';
				}
				$arrData[] = $item;
		}
		
		foreach ($arrNotasCredito as $oNotaCredito)
		{
			$oComprobante = $oComprobantes->GetById($oNotaCredito->IdComprobante);
			$oFactura = $this->GetByIdComprobante($oNotaCredito->IdFactura);
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			
			$subtotal = $oComprobante->Importe - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->ImpuestoInterno - $oComprobante->PercepcionIIBB;
			$iva10 = $oComprobante->ImporteIva10;
			$iva21 = $oComprobante->ImporteIva21;
			
			$NoGravrado = $subtotal - ($iva10 / 0.105) - ($iva21 / 0.21);
			if (abs($NoGravrado) < 0.1)
				$NoGravrado = 0;
			
			$item = array(
				trim(CambiarFecha($oComprobante->Fecha)), 
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				
				
					
				trim(number_format($subtotal - $NoGravrado, 2, ',', '.')),
				trim(number_format($iva10, 2, ',', '.')),
				trim(number_format($iva21, 2, ',', '.')),
				trim(number_format($NoGravrado, 2, ',', '.')),
				trim(number_format($oComprobante->Importe, 2, ',', '.')),
				);
			
				foreach ($arrConceptos as $oConcepto)
				{
					if ($arrFCC = $oFacturaVariaDetalles->GetAllByFacturaVariaAndConcepto($oFactura, $oConcepto->Nombre))
					{
						$imp = 0;
						foreach ($arrFCC as $oFCC)
						{
							if ($oConcepto->IvaGravado == 1)
								$imp+= $oFCC->Importe / 1.21;
							elseif ($oConcepto->IvaGravado == 2)
								$imp+= $oFCC->Importe / 1.105;
							else
								$imp+= $oFCC->Importe;
						}
						$item[] = number_format($imp, 2, ',', '.');
					}
					else
						$item[] = '0.00';
				}
				$arrData[] = $item;
		}
		
		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'facturas varias';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}

	
}

?>