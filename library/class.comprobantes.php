<?php 

require_once('class.dbaccess.php');
require_once('class.clientes.php');
require_once('class.comprobante.php');
require_once('class.comprobanteestados.php');
require_once('class.comprobantetipos.php');
require_once('class.tiposiva.php');
require_once('class.misc.php');
require_once('class.operaciontipos.php');
require_once('class.notascredito.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('class.localidades.php');
require_once('class.provincias.php');
require_once('class.notascredito.php');
require_once('class.facturaspostventas.php');
require_once('class.ordenestrabajofranquicias.php');
require_once('excel_export/class.xlsexport.php');


class Comprobantes extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = ' WHERE 1';

		if ((isset($filter['IdTipoComprobante'])) && ($filter['IdTipoComprobante'] != ''))
			$sql.= " AND IdTipoComprobante = " . DB::Number($filter['IdTipoComprobante']);

		if ((isset($filter['Numero'])) && ($filter['Numero'] != ''))
			$sql.= " AND Numero LIKE '%" . DB::StringUnquoted($filter['Numero']) . "%'";

		if ((isset($filter['NumeroCompleto'])) && ($filter['NumeroCompleto'] != ''))
			$sql.= " AND CONCAT(Prefijo, '-', Numero) LIKE '%" . DB::StringUnquoted($filter['NumeroCompleto']) . "%'";
			
		if ((isset($filter['Prefijo'])) && ($filter['Prefijo'] != ''))
			$sql.= " AND Prefijo LIKE '%" . DB::StringUnquoted($filter['Prefijo']) . "%'";

		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
		
		return $sql;
	}
	
	public function ParseFilterPercepciones(array $filter)
	{
		$sql = ' WHERE PercepcionIIBB > 0';

		if ((isset($filter['IdTipoComprobante'])) && ($filter['IdTipoComprobante'] != ''))
			$sql.= " AND IdTipoComprobante = " . DB::Number($filter['IdTipoComprobante']);

		if ((isset($filter['Numero'])) && ($filter['Numero'] != ''))
			$sql.= " AND Numero LIKE '%" . DB::StringUnquoted($filter['Numero']) . "%'";
			
		if ((isset($filter['Prefijo'])) && ($filter['Prefijo'] != ''))
			$sql.= " AND Prefijo LIKE '%" . DB::StringUnquoted($filter['Prefijo']) . "%'";

		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND IdEstado = " . DB::Number($filter['IdEstado']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND Fecha <= " . DB::Date($filter['FechaHasta']);
		
		return $sql;
	}
	
	public function ParseFilterPV(array $filter)
	{
		$sql = ' WHERE 1';

		if ((isset($filter['IdTipoComprobante'])) && ($filter['IdTipoComprobante'] != ''))
			$sql.= " AND f.IdTipoComprobante = " . DB::Number($filter['IdTipoComprobante']);

		if ((isset($filter['Numero'])) && ($filter['Numero'] != ''))
			$sql.= " AND f.Numero LIKE '%" . DB::StringUnquoted($filter['Numero']) . "%'";
			
		if ((isset($filter['Prefijo'])) && ($filter['Prefijo'] != ''))
			$sql.= " AND f.Prefijo LIKE '%" . DB::StringUnquoted($filter['Prefijo']) . "%'";

		if ((isset($filter['IdEstado'])) && ($filter['IdEstado'] != ''))
			$sql.= " AND f.IdEstado = " . DB::Number($filter['IdEstado']);
			
		if ((isset($filter['FechaDesde'])) && ($filter['FechaDesde'] != ''))
			$sql.= " AND f.Fecha >= " . DB::Date($filter['FechaDesde']);
			
		if ((isset($filter['FechaHasta'])) && ($filter['FechaHasta'] != ''))
			$sql.= " AND f.Fecha <= " . DB::Date($filter['FechaHasta']);
		
		return $sql;
	}

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdTipoComprobante, Prefijo, Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oComprobante = new Comprobante();
			$oComprobante->ParseFromArray($oRow);
			
			array_push($arr, $oComprobante);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetAllPercepciones(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= ($filter) ? $this->ParseFilterPercepciones($filter) : "";
		$sql.= " ORDER BY Fecha, Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oComprobante = new Comprobante();
			$oComprobante->ParseFromArray($oRow);
			
			array_push($arr, $oComprobante);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetLastPrefijo($IdTipoComprobante, $Prefijo)
	{
		$sql = "SELECT IdComprobante, IdTipoComprobante, Prefijo, Numero";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdTipoComprobante = " . DB::Number($IdTipoComprobante);	
		$sql.= " AND Prefijo = " . DB::String($Prefijo);
		$sql.= " ORDER BY Numero DESC";
		$sql.= " LIMIT 1";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComprobante = new Comprobante();
		$oComprobante->ParseFromArray($oRow);
		
		return $oComprobante;		
	}
	
	public function GetLibroVentas(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " AND IdEstado <> 1";
		$sql.= " AND Numero <> '00000000'";
		$sql.= " AND (IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaB);
		$sql.= " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ")";
		$sql.= " ORDER BY Fecha, Prefijo, Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oComprobante = new Comprobante();
			$oComprobante->ParseFromArray($oRow);
			
			array_push($arr, $oComprobante);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetAllByIdOrdenTrabajo($IdOrdenTrabajo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo);
		$sql.= " OR (";
		$sql.= " (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA);
		$sql.= " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ")";
		$sql.=	" AND IdComprobante IN(SELECT nc.IdComprobante FROM TB_NotasCredito nc INNER JOIN TB_Comprobantes c ON nc.IdFactura = c.IdComprobante WHERE c.IdOrdenTrabajo = " . DB::Number($IdOrdenTrabajo) . ") )";
		$sql.= " ORDER BY IdComprobante, Prefijo, Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oComprobante = new Comprobante();
			$oComprobante->ParseFromArray($oRow);
			
			array_push($arr, $oComprobante);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetReportePostVenta(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT f.*";
		$sql.= " FROM TB_Comprobantes f";
		$sql.= " LEFT JOIN TB_OrdenesTrabajo ot ON ot.IdOrdenTrabajo = f.IdOrdenTrabajo";
		//$sql.= " LEFT JOIN TB_Compras c ON c.IdFactura = f.IdComprobante";
		$sql.= ($filter) ? $this->ParseFilterPV($filter) : "";
		$sql.= " AND (f.IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaA) . " OR f.IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaB) . " OR f.IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR f.IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ")";
		$sql.= " AND f.IdEstado <> " . DB::Number(ComprobanteEstados::Libre);
		$sql.= " AND (";
		$sql.= "	ot.IdOrdenTrabajo IS NOT NULL";
		$sql.= "	OR";
		$sql.= "	f.Prefijo = '0002'";
		$sql.= "	OR";
		$sql.= "	f.Prefijo = '0003'";
		$sql.= ")";
		$sql.= " ORDER BY f.Fecha, f.Prefijo, f.Numero";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oComprobante = new Comprobante();
			$oComprobante->ParseFromArray($oRow);
			
			array_push($arr, $oComprobante);
			
			$oRes->MoveNext();
		}

		return $arr;
	}
	
	public function GetLibroVentasTotales(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT SUM(IF (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ",(IF (ImporteIva21 IS NULL, 0, ImporteIva21 * 100 / 21) + IF (ImporteIva10 IS NULL, 0, ImporteIva10 * 100 / 10.5)) * -1, IF (ImporteIva21 IS NULL, 0, ImporteIva21 * 100 / 21) + IF (ImporteIva10 IS NULL, 0, ImporteIva10 * 100 / 10.5))) AS NetoGravado, 
			SUM(IF (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ", ImporteIva21 * -1, ImporteIva21)) AS Iva21, 
			SUM(IF (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ", ImporteIva10 * -1, ImporteIva10)) AS Iva10, 
			SUM(IF (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ", PercepcionIIBB * -1, PercepcionIIBB)) AS PercepcionIIBB, 
			SUM(IF (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ", (Importe - PercepcionIIBB - ImpuestoInterno - (IF (ImporteIva21 IS NULL, 0, ImporteIva21 * 100 / 21) + IF (ImporteIva10 IS NULL, 0, ImporteIva10 * 100 / 10.5)) - (IF (ImporteIva21 IS NULL, 0, ImporteIva21) + IF (ImporteIva10 IS NULL, 0, ImporteIva10))) * -1, Importe - PercepcionIIBB - ImpuestoInterno - (IF (ImporteIva21 IS NULL, 0, ImporteIva21 * 100 / 21) + IF (ImporteIva10 IS NULL, 0, ImporteIva10 * 100 / 10.5)) - (IF (ImporteIva21 IS NULL, 0, ImporteIva21) + IF (ImporteIva10 IS NULL, 0, ImporteIva10)))) AS NoGra, 
			SUM(IF (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ", (ImpuestoInterno) * -1, ImpuestoInterno)) AS ImpuestoInterno, 
			SUM(IF (IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ", Importe * -1, Importe)) AS Total";
		$sql.= " FROM TB_Comprobantes";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " AND IdEstado <> 1";
		$sql.= " AND Numero <> '00000000'";
		$sql.= " AND (IdEstado <> 3 OR IdComprobante IN (SELECT IdFactura FROM TB_NotasCredito))";
		$sql.= " AND (IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaB);
		$sql.= " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ")";		
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!$oRow = $oRes->GetRow())
			return false;
		
		$oTotales = new stdClass();
		$oTotales->NetoGravado 	= $oRow['NetoGravado'];
		$oTotales->Iva21 		= $oRow['Iva21'];
		$oTotales->Iva10 		= $oRow['Iva10'];
		$oTotales->PercepcionIIBB 		= $oRow['PercepcionIIBB'];
		$oTotales->NoGra		= $oRow['NoGra'];
		$oTotales->ImpuestoInterno		= $oRow['ImpuestoInterno'];
		$oTotales->Total		= $oRow['Total'];
		
		return $oTotales;
	}

	public function GetById($IdComprobante)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdComprobante = " . DB::Number($IdComprobante);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComprobante = new Comprobante();
		$oComprobante->ParseFromArray($oRow);
		
		return $oComprobante;		
	}
	

	public function GetByNumero($IdTipoComprobante, $Numero)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdTipoComprobante = " . DB::Number($IdTipoComprobante);	
		$sql.= " AND Numero = " . DB::String($Numero);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComprobante = new Comprobante();
		$oComprobante->ParseFromArray($oRow);
		
		return $oComprobante;		
	}

	public function GetByNumeroPrefijo($IdTipoComprobante, $Prefijo, $Numero)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdTipoComprobante = " . DB::Number($IdTipoComprobante);	
		$sql.= " AND Prefijo = " . DB::String($Prefijo);	
		$sql.= " AND Numero = " . DB::String($Numero);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComprobante = new Comprobante();
		$oComprobante->ParseFromArray($oRow);
		
		return $oComprobante;		
	}

	public function GetNext($IdTipoComprobante)
	{
		$sql = "SELECT IdComprobante, IdTipoComprobante, Prefijo, MIN(Numero) AS Numero";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdTipoComprobante = " . DB::Number($IdTipoComprobante);	
		$sql.= " AND IdEstado = " . DB::Number(ComprobanteEstados::Libre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComprobante = new Comprobante();
		$oComprobante->ParseFromArray($oRow);
		
		return $oComprobante;		
	}
	
	public function GetNextPrefijo($IdTipoComprobante, $Prefijo)
	{
		$sql = "SELECT IdComprobante, IdTipoComprobante, Prefijo, MIN(Numero) AS Numero";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdTipoComprobante = " . DB::Number($IdTipoComprobante);	
		$sql.= " AND IdEstado = " . DB::Number(ComprobanteEstados::Libre);
		$sql.= " AND Prefijo = " . DB::String($Prefijo);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComprobante = new Comprobante();
		$oComprobante->ParseFromArray($oRow);
		
		return $oComprobante;		
	}


	public function GetNextCargaLote($IdTipoComprobante)
	{
		$sql = "SELECT IdComprobante, IdTipoComprobante, Prefijo, MAX(Numero) AS Numero";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdTipoComprobante = " . DB::Number($IdTipoComprobante);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oComprobante = new Comprobante();
		$oComprobante->ParseFromArray($oRow);
		
		return $oComprobante;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	public function GetLibroVentasCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Comprobantes";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " AND IdEstado <> 1";
		$sql.= " AND Numero <> '00000000'";
		$sql.= " AND (IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::FacturaB);
		$sql.= " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoA) . " OR IdTipoComprobante = " . DB::Number(ComprobanteTipos::NotaCreditoB) . ")";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}	

	public function CheckLoteLibre($IdTipoComprobante, $PrefijoDesde, $NumeroDesde, $PrefijoHasta, $NumeroHasta)
	{
		$sql = "SELECT MIN(Numero) AS MinimoUtilizado,";
		$sql.= " MAX(Numero) AS MaximoUtilizado";
		$sql.= " FROM TB_Comprobantes";
		$sql.= " WHERE IdTipoComprobante = " . DB::Number($IdTipoComprobante);	
		$sql.= " AND Prefijo >= " . DB::Number($PrefijoDesde);	
		$sql.= " AND Prefijo <= " . DB::Number($PrefijoHasta);	
		$sql.= " AND Numero >= " . DB::Number($NumeroDesde);	
		$sql.= " AND Numero <= " . DB::Number($NumeroHasta);	

		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return true;

		return $oRow;
	}

	
	public function CreateLote($IdTipoComprobante, $PrefijoDesde, $NumeroDesde, $PrefijoHasta, $NumeroHasta)
	{
		if (!DBAccess::$db->Begin())		
			return false;

		$PrefijoDesde 	= (int)$PrefijoDesde;
		$NumeroDesde 	= (int)$NumeroDesde;
		$PrefijoHasta 	= (int)$PrefijoHasta;
		$NumeroHasta 	= (int)$NumeroHasta;
		
		$MaxLongPrefijo = 4;
		$MaxLongNumero 	= 8;
		
		for ($i=$PrefijoDesde; $i<=$PrefijoHasta; $i++)
		{
			if ($i == $PrefijoDesde)
			{
				$j = $NumeroDesde;
			}
			elseif ($i > $PrefijoDesde)
			{
				$j = 0;
			}
			
			for ($j; $j<=$NumeroHasta; $j++)
			{
				/* armamos el prefijo */
				$Prefijo = '';
				for ($k=1; $k<=$MaxLongPrefijo; $k++)
				{
					if ($k > strlen($i))
					{
						$Prefijo.= '0';
					}
				}
				$Prefijo.= $i;

				/* armamos el numero */
				$Numero = '';
				for ($k=1; $k<=$MaxLongNumero; $k++)
				{
					if ($k > strlen($j))
					{
						$Numero.= '0';
					}
				}
				$Numero.= $j;
				
				/* cremaos el objeto */
				$oComprobante = new Comprobante();
				
				$oComprobante->IdTipoComprobante 	= $IdTipoComprobante;
				$oComprobante->Prefijo 	= $Prefijo;
				$oComprobante->Numero 	= $Numero;
				$oComprobante->IdEstado = ComprobanteEstados::Libre;
								
				if (!$this->Create($oComprobante))
				{
					DBAccess::$db->Rollback();	
					return false;
				}
			}
		}

		DBAccess::$db->Commit();
		
		return true;
	}
	
	private function GetArrayDB(Comprobante $oComprobante)
	{
		$arr = array
		(
			'IdTipoComprobante' => DB::Number($oComprobante->IdTipoComprobante),
			'Prefijo' 			=> DB::String($oComprobante->Prefijo),
			'Numero' 			=> DB::String($oComprobante->Numero),
			'IdEstado' 			=> DB::Number($oComprobante->IdEstado),
			'FechaAnulada'		=> DB::Date($oComprobante->FechaAnulada),
			'IdCliente'			=> DB::Number($oComprobante->IdCliente),
			'Importe'			=> DB::Number($oComprobante->Importe),
			'Fecha'				=> DB::Date($oComprobante->Fecha),
			'IdOrdenTrabajo'	=> DB::Number($oComprobante->IdOrdenTrabajo),
			'ImporteIva21'		=> DB::Number($oComprobante->ImporteIva21),
			'ImporteIva10'		=> DB::Number($oComprobante->ImporteIva10),
			'ImpuestoInterno'	=> DB::Number($oComprobante->ImpuestoInterno),
			'PercepcionIIBB'	=> DB::Number($oComprobante->PercepcionIIBB),
			'Cae'				=> DB::String($oComprobante->Cae),
			'Archivo'			=> DB::String($oComprobante->Archivo)
		);
		
		return $arr;
	}
	
	public function Create(Comprobante $oComprobante)
	{
		$arr = $this->GetArrayDB($oComprobante);
		
		if (!$this->Insert('TB_Comprobantes', $arr))
			return false;

		/* asignamos el id generado */
		$oComprobante->IdComprobante = DBAccess::GetLastInsertId();
			
		return $oComprobante;
	}
	
	
	public function Update(Comprobante $oComprobante)
	{
		$where = " IdComprobante = " . DB::Number($oComprobante->IdComprobante);
		
		$arr = $this->GetArrayDB($oComprobante);
		
		if (!DBAccess::Update('TB_Comprobantes', $arr, $where))
			return false;
		
		return $oComprobante;
	}
	

	public function Delete($IdComprobante)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdComprobante = " . DB::Number($IdComprobante);
		if (!DBAccess::Delete('TB_Comprobantes', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	

	public function ExportXls(array $filter = NULL)
	{
		/* obtenemos el listado de datos a exportar */			
		$arrComprobantes = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array("Comprobante");
				
		foreach ($arrComprobantes as $oComprobante)
		{	
			/* almacenamos el registro */
			$arrData[] = array(trim($oComprobante->Nombre));
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'comprobantes';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
	
	public function ExportReporteCsv(array $filter = NULL)
	{
		$oClientes = new Clientes();
	
		/* obtenemos el listado de datos a exportar */			
		$arrComprobantes = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"FECHA", 
			"TIPO FACTURA", 
			"NUMERO", 
			"CLIENTE", 
			"CUIL", 
			"NETO", 
			"IVA 10,5%", 
			"IVA 21%", 
			"TOTAL");
				
		foreach ($arrComprobantes as $oComprobante)
		{	
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			/* almacenamos el registro */
			$arrData[] = array(
				trim(CambiarFecha($oComprobante->Fecha)),
				trim(ComprobanteTipos::GetDescripcionById($oComprobante->IdTipoComprobante)),
				trim($oComprobante->Prefijo . ' - ' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oCliente->ClaveFiscalNumero),
				trim(number_format($oComprobante->Importe - $oComprobante->ImporteIva, 2, ',', '.')),
				trim(number_format(0, 2, ',', '.')),
				trim(number_format($oComprobante->ImporteIva, 2, ',', '.')),
				trim(number_format($oComprobante->Importe, 2, ',', '.'))
				);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'contabilidad_reporte';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
	
	public function ExportLibroIvaCsv(array $filter = NULL)
	{
		$oClientes = new Clientes();
		$oTiposIva = new TiposIva();
		$oNotasCredito = new NotasCredito();
		$oFacturasUnidades = new FacturaUnidades();
		$oFacturasUsados	= new FacturaUsados();
		$oFacturaVarias = new FacturaVarias();
		$oLocalidades	= new Localidades();
		$oProvincias	= new Provincias();
	
		/* obtenemos el listado de datos a exportar */			
		$arrComprobantes = $this->GetLibroVentas($filter);
				
		$arrData = array();
		
		$arrData[] = array(
			"ACTION MOTORSPORTS S.R.L.");
		
		$arrData[] = array(
			"Av. Del Libertador 2275, Olivos");
		$arrData[] = array(
			"Venta de autos, camionetas y utilitarios, nuevos");
		$arrData[] = array(
			"30-71194065-7");
			
		$arrData[] = array(
			"DESDE " . $filter['FechaDesde'] . " HASTA " . $filter['FechaHasta']);
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"Fecha", 
			"Tipo", 
			"Numero", 
			"Razon Social", 
			"Condicion", 
			"Cuit", 
			"Neto Grav.", 
			"IVA 21%",
			"IVA 10,5%", 
			"Ret. IVA", 
			"Perc. IIBB", 
			"No Gra.",  
			"Imp. Int.",  
			"T. Comprobante",
			"Concepto",
			"Provincia",
			"Cuidad");	
				
		foreach ($arrComprobantes as $oComprobante)
		{	
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
			$oLocalidad = $oLocalidades->GetById($oCliente->DomicilioIdLocalidad);
			$oProvincia = $oProvincias->GetById($oLocalidad->IdProvincia);
			/* almacenamos el registro */
			$oNotaCredito = $oNotasCredito->GetByIdFactura($oComprobante->IdComprobante);
			if (($oComprobante->FechaAnulada || $oComprobante->IdEstado == ComprobanteEstados::Anulado) && !$oNotaCredito)
			{
				$arrData[] = array(
					trim(str_replace('-', '/', CambiarFecha($oComprobante->Fecha))),
					trim(ComprobanteTipos::GetTipoById($oComprobante->IdTipoComprobante) . "V"),
					trim(ComprobanteTipos::GetLetraById($oComprobante->IdTipoComprobante) . $oComprobante->Prefijo . '-' . $oComprobante->Numero),
					trim('ANULADA'), 
					trim($oTipoIva->Codigo),
					trim(''),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(''),
					trim(''),
					trim('')
				);
			}
			else
			{
				$totalIva10 = $oComprobante->ImporteIva10 * 100 / 10.5;
				$totalIva21 = $oComprobante->ImporteIva21 * 100 / 21;
				$NoGravado = $oComprobante->Importe - $oComprobante->ImpuestoInterno - $totalIva10 - $totalIva21 - $oComprobante->ImporteIva21 - $oComprobante->ImporteIva10 - $oComprobante->PercepcionIIBB;
				$NoGravado = $NoGravado < 0.2 ? 0 : $NoGravado;
				$Concepto = '';
				if ($oComprobante->Prefijo == '0002' || $oComprobante->Prefijo == '0003')
					$Concepto = 'POSTVENTA';
				elseif (!$oComprobante->IdOrdenTrabajo)
				{
					if ($oFacturaUnidad = $oFacturasUnidades->GetByIdComprobante($oComprobante->IdComprobante))
						$Concepto = 'AUTO';
					elseif ($oFacturaUsado = $oFacturasUsados->GetByIdComprobante($oComprobante->IdComprobante))
						$Concepto = 'USADO';
					elseif ($oFacturaVaria = $oFacturaVarias->GetByIdComprobante($oComprobante->IdComprobante))
						$Concepto = 'FACTURAS VARIAS';
				}
				
				
				$arrData[] = array(
					trim(str_replace('-', '/', CambiarFecha($oComprobante->Fecha))),
					trim(ComprobanteTipos::GetTipoById($oComprobante->IdTipoComprobante) . "V"),
					trim(ComprobanteTipos::GetLetraById($oComprobante->IdTipoComprobante) . $oComprobante->Prefijo . '-' . $oComprobante->Numero),
					trim($oCliente->RazonSocial), 
					trim($oTipoIva->Codigo),
					trim($oCliente->ClaveFiscalNumero != '' ? substr_replace(substr_replace(str_replace('-', '', $oCliente->ClaveFiscalNumero), '-', 10, 0), '-', 2, 0) : ($oCliente->DocumentoNumero ? $oCliente->DocumentoNumero : '')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($totalIva10 + $totalIva21, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->ImporteIva21, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->ImporteIva10, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->PercepcionIIBB, 2, ',', '.') . '0'),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($NoGravado, 2, ',', '.') . '0'),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->ImpuestoInterno, 2, ',', '.')),
					trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->Importe, 2, ',', '.')),
					trim($Concepto),
					trim($oProvincia->Nombre),
					trim($oLocalidad->Nombre)
				);
			}
		}
		
		$oTotales = $this->GetLibroVentasTotales($filter, $oPage);
		
		$arrData[] = array(
			"Neto Gravado: " . number_format($oTotales->NetoGravado, 2, ',', '.'));
		/*$arrData[] = array(
			"Neto Gravado 10.50: " . number_format($oTotales->Iva10 * 100 / 10.5, 2, ',', '.'));
		$arrData[] = array(
			"Neto Gravado 21.00: " . number_format($oTotales->Iva21 * 100 / 21, 2, ',', '.'));*/
		$arrData[] = array(
			"IVA 21.00: " . number_format($oTotales->Iva21, 2, ',', '.'));
		$arrData[] = array(
			"IVA 10.50: " . number_format($oTotales->Iva10, 2, ',', '.'));
		$arrData[] = array(
			"Retencion IVA: " . number_format(0, 2, ',', '.'));
		$arrData[] = array(
			"Percepcion IIBB: " . number_format($oTotales->PercepcionIIBB, 2, ',', '.'));
		$arrData[] = array(
			"No Gravado: " . number_format($oTotales->NoGra, 2, ',', '.'));
		$arrData[] = array(
			"Imp. Interno: " . number_format($oTotales->ImpuestoInterno, 2, ',', '.'));
		$arrData[] = array(
			"Total: " . number_format($oTotales->Total, 2, ',', '.'));
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'libro iva ventas';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
	
	public function GenerarArchivoPercepciones($filter)
	{
		$oClientes = new Clientes();
		
		$arrData = $this->GetAllPercepciones($filter);
		
		$SaltoLinea = "\r\n";
		
		$txt = '';
		
		$FileName = "PERCEPCIONES.txt";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
		
		foreach ($arrData as $oComprobante)
		{
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			$Cuit = str_replace('-', '', $oCliente->ClaveFiscalNumero);
			$Cuit = substr_replace(substr_replace($Cuit, '-', 10, 0), '-', 2, 0);
			
			if ($txt != '')
				$txt.= $SaltoLinea;
			$txt.= $Cuit;
			$txt.= str_replace('-', '-', CambiarFecha($oComprobante->Fecha));
			$txt.= ComprobanteTipos::GetTipoById4($oComprobante->IdTipoComprobante) . ComprobanteTipos::GetLetraById($oComprobante->IdTipoComprobante) . $oComprobante->Prefijo . $oComprobante->Numero;
			$txt.= ComprobanteTipos::GetSignoById2($oComprobante->IdTipoComprobante) . str_pad(number_format($oComprobante->Importe - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->ImpuestoInterno - $oComprobante->PercepcionIIBB, 2, ',', ''), 11, '0', STR_PAD_LEFT);
			$txt.= ComprobanteTipos::GetSignoById2($oComprobante->IdTipoComprobante) . str_pad(number_format($oComprobante->PercepcionIIBB, 2, ',', ''), 10, '0', STR_PAD_LEFT);
			$txt.= 'A';
			
		}
		
		print_r($txt);
	}	
	
	public function ExportReportePVCsv(array $filter = NULL)
	{
		$oClientes 					= new Clientes();
		$oTiposIva 					= new TiposIva();
		$oFacturasPostVentas		= new FacturasPostVentas();
		$oOrdenesTrabajo			= new OrdenesTrabajo();
		$oOrdenesTrabajoFranquicias = new OrdenesTrabajoFranquicias();
		$oNotasCredito				= new NotasCredito();
	
		/* obtenemos el listado de datos a exportar */			
		$arrComprobantes = $this->GetReportePostVenta($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array(
			"Fecha", 
			"Tipo", 
			"Numero", 
			"Razon Social", 
			"Condicion", 
			"Cuit", 
			"Neto Grav.", 
			"IVA 21%",
			"IVA 27%",
			"IVA 10,5%", 
			"Ret. IVA", 
			"Perc. IB", 
			"Percep.", 
			"No Gra.",  
			"T. Comprobante",
			"REPUESTOS",
			"MANO DE OBRA",
			"CHAPA Y PINTURA",
			"DONACIONES");
				
		foreach ($arrComprobantes as $oComprobante)
		{	
			$oCliente = $oClientes->GetById($oComprobante->IdCliente);
			$oTipoIva = $oTiposIva->GetById($oCliente->IdTipoIva);
			
			$TotalRepuestos = 0;
			$TotalManoObra = 0;
			$TotalChapaYPintura = 0;
			$TotalDonaciones = 0;
			$Neto = $oComprobante->Importe - $oComprobante->ImporteIva10 - $oComprobante->ImporteIva21 - $oComprobante->PercepcionIIBB;
			$oComprobanteF = $oComprobante;
			if ($oComprobante->IdTipoComprobante == ComprobanteTipos::NotaCreditoA ||$oComprobante->IdTipoComprobante == ComprobanteTipos::NotaCreditoB)
			{
				$oNotaCredito = $oNotasCredito->GetByIdComprobante($oComprobante->IdComprobante);
				$oComprobanteF = $this->GetById($oNotaCredito->IdFactura);
			}
			$oFacturaPostVenta = $oFacturasPostVentas->GetByIdComprobante($oComprobanteF->IdComprobante);
			if ($oFacturaPostVenta->IdOrdenTrabajo && $oOrdenTrabajo = $oOrdenesTrabajo->GetById($oFacturaPostVenta->IdOrdenTrabajo))
			{
				if ($oOrdenTrabajoFranquicia = $oOrdenesTrabajoFranquicias->GetByIdFactura($oFacturaPostVenta->IdFacturaPostVenta))
				{		
					$TotalManoObra = 0;
					$TotalRepuestos = 0;
					$TotalChapaYPintura = $oOrdenTrabajo->ImporteNetoChapaYPintura();
					if ($TotalChapaYPintura < $Neto)
					{
							$TotalRepuestos = $Neto - $TotalChapaYPintura;
					}
					else
					{
						$TotalChapaYPintura = $Neto;
					}
					$TotalManoObra = 0;
				}
				else
				{
					$arrOrdenTrabajoFranquicias = $oOrdenesTrabajoFranquicias->GetByIdOrdenTrabajo($oOrdenTrabajo->IdOrdenTrabajo);
					
					$TotalFranquicia = 0;
					if ($arrOrdenTrabajoFranquicias)
					{
						foreach ($arrOrdenTrabajoFranquicias as $oFranquicia)
						{
							$oFacturaPostVentaF = $oFacturasPostVentas->GetById($oFranquicia->IdFactura);
							$oComprobanteF = $this->GetById($oFacturaPostVentaF->IdComprobante);
							if ($oComprobanteF->IdEstado == 3)
									continue;
							$arrItems = $oFacturaPostVentaF->GetAllItems();
							$Interes = 0;
							foreach ($arrItems as $oItem)
							{
								if ($oItem->Interes)
									$Interes+= $oItem->ImporteBruto;
							}
							$TotalFranquicia+= $oFranquicia->Importe - $oComprobanteF->PercepcionIIBB - $Interes;
						}
						$TotalFranquicia = $TotalFranquicia / 1.21;
					}
					
					$TotalManoObra = $oOrdenTrabajo->ImporteManoObraNetoCalculado();
					$TotalRepuestos = $oOrdenTrabajo->ImporteRepuestosNetoCalculado();
					$TotalChapaYPintura = $oOrdenTrabajo->ImporteNetoChapaYPintura() - $TotalFranquicia;
					if ($TotalChapaYPintura < 0)
					{
							$TotalRepuestos += $TotalChapaYPintura;
							$TotalChapaYPintura = 0;
					}
					$TotalManoObra = $Neto - $TotalRepuestos - $TotalChapaYPintura;
				}
			}
			else
			{
				if ($oComprobante->ImporteIva21 && $oComprobante->ImporteIva21 > 0)
					$TotalRepuestos = $oComprobante->Importe - $oComprobante->ImporteIva21 - $oComprobante->PercepcionIIBB;
				else
					$TotalDonaciones = $oComprobante->Importe - $oComprobante->ImporteIva21 - $oComprobante->PercepcionIIBB;
			}
			
			
			/* almacenamos el registro */
			$arrData[] = array(
				trim(str_replace('-', '/', CambiarFecha($oComprobante->Fecha))),
				trim(ComprobanteTipos::GetTipoById($oComprobante->IdTipoComprobante) . "V"),
				trim(ComprobanteTipos::GetLetraById($oComprobante->IdTipoComprobante) . $oComprobante->Prefijo . '-' . $oComprobante->Numero),
				trim($oCliente->RazonSocial), 
				trim($oTipoIva->Codigo),
				trim($oCliente->ClaveFiscalNumero != '' ? substr_replace(substr_replace(str_replace('-', '', $oCliente->ClaveFiscalNumero), '-', 10, 0), '-', 2, 0) : ''),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($Neto, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->ImporteIva21, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->ImporteIva10, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->PercepcionIIBB, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format(0, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($oComprobante->Importe, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($TotalRepuestos, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($TotalManoObra, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($TotalChapaYPintura, 2, ',', '.')),
				trim(ComprobanteTipos::GetSignoById($oComprobante->IdTipoComprobante) . number_format($TotalDonaciones, 2, ',', '.'))
				);
		}
		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'reporte post venta';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}	
}

?>