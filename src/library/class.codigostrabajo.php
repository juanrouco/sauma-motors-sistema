<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.codigotrabajo.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_reader/class.xlsreader.php');

class CodigosTrabajo extends DBAccess implements IFilterable
{

	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		
		if ($filter['IdModeloPV'] != null && $filter['IdModeloPV'] != "")
		{	
			$sql.= " AND a.IdModeloPV = " . DB::Number($filter['IdModeloPV']);			
		}
				
		if ($filter['Descripcion'] != null && $filter['Descripcion'] != "")
		{	
			$sql.= " AND (a.Descripcion RLIKE '" . DB::StringUnquoted($filter['Descripcion']) . "'";
			$sql.= " OR a.Descripcion IS NULL)";
		}
		
		if ($filter['CodigoHistorico'] != null && $filter['CodigoHistorico'] != "")
		{	
			$sql.= " AND a.CodigoHistorico RLIKE '" . DB::StringUnquoted($filter['CodigoHistorico']) . "'";
		}
		
		if ($filter['Codigo'] != null && $filter['Codigo'] != "")
		{	
			$sql.= " AND a.Codigo RLIKE '" . DB::StringUnquoted($filter['Codigo']) . "'";
		}

		return $sql;
	}	
	

	public function GetPagesCount(Page $oPage, $filter = false)
	{	
		$sql = "SELECT COUNT(1) AS Count";
		$sql.= " FROM TB_CodigosTrabajo a";
		$sql.= " WHERE 1";
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		
		$sql.= " GROUP BY a.Codigo";
		
		if (!($oRes = $this->GetQuery($sql)) )		
			return false;
		
		if ( !($oRow = $oRes->GetRow()) )
			return false;
			
		$CountRows = $oRes->NumRows();

		$Count = ceil($CountRows / $oPage->Size);

		return $Count;
	}
			
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_CodigosTrabajo a";
		$sql.= " INNER JOIN TB_ModelosPV m ON m.IdModeloPV = a.IdModeloPV";
		$sql.= " WHERE 1";

		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);
		$sql.= " ORDER BY m.Modelo, a.Codigo";		

		if ($oPage != NULL)
			$sql.= " " . Pageable::ParsePage($oPage);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oCodigoTrabajo = new CodigoTrabajo();
			$oCodigoTrabajo->ParseFromArray($oRow);
			
			
			array_push($arr, $oCodigoTrabajo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}
	
	public function GetById($IdCodigoTrabajo)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_CodigosTrabajo a";
		$sql.= " WHERE a.IdCodigoTrabajo = " . DB::Number($IdCodigoTrabajo);	

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oCodigoTrabajo = new CodigoTrabajo();
		$oCodigoTrabajo->ParseFromArray($oRow);

		
		return $oCodigoTrabajo;		
	}

	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT a.*";
		$sql.= " FROM TB_CodigosTrabajo a";
		$sql.= " WHERE 1";		
		
		if ($filter)
			$sql.= " " . $this->ParseFilter($filter);

		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(CodigoTrabajo $oCodigoTrabajo)
	{
		$arr = array
		(
			'IdModeloPV'				=> DB::Number($oCodigoTrabajo->IdModeloPV),
			'Descripcion'				=> DB::String($oCodigoTrabajo->Descripcion),
			'CodigoHistorico'			=> DB::String($oCodigoTrabajo->CodigoHistorico),
			'Codigo'					=> DB::String($oCodigoTrabajo->Codigo),
			'Tiempo'					=> DB::Number($oCodigoTrabajo->Tiempo),
			'IdArticulo'				=> DB::Number($oCodigoTrabajo->IdArticulo)
		);
		return $arr;
	}
	
	public function Create(CodigoTrabajo $oCodigoTrabajo)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->GetArrayDB($oCodigoTrabajo);

		if (!DBAccess::Insert('TB_CodigosTrabajo', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

				
		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oCodigoTrabajo;
	}
	
	
	public function Update(CodigoTrabajo $oCodigoTrabajo)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = $this->GetArrayDB($oCodigoTrabajo);

		$where = " IdCodigoTrabajo = " . (int)$oCodigoTrabajo->IdCodigoTrabajo;
		
		if (!DBAccess::Update('TB_CodigosTrabajo', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oCodigoTrabajo;
	}
	
	public function Delete($IdCodigoTrabajo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdCodigoTrabajo = " . DB::Number($IdCodigoTrabajo);
		if (!DBAccess::Delete('TB_CodigosTrabajo', $where))
		{
				DBAccess::$db->Rollback();	
				return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
		
	public function ExportToPDF($outputPdf = '', array $filter = NULL)
	{
		$pdf = new FPDF('P', 'cm', 'A4');
		$arr = array();
		
		$arr = $this->GetAll($filter);
		$paginaActual = 0;
		$cont = 50;
		$x = 0;
		$y = 3;
		
		foreach ($arr as $oProveedor)
		{	  
			if ($cont == 50)
			{
				$pdf->AddPage();
				$paginaActual++;
				$cont = 0;
				$y = 3;

				$pdf->SetFont('Arial','B', 15);
				$pdf->Text($x + 8, $y - 2, "Reporte de Proveedores");

				$pdf->SetFont('Arial','B', 6.5);				
				$pdf->Text($x + 1, $y - 1, "Apellido y Nombre");
				$pdf->Text($x + 4.8, $y - 1, "Proveedor");
				$pdf->Text($x + 7.4, $y - 1, "Cuit / Cuil");
				$pdf->Text($x + 12.5, $y - 1, "Email");
				$pdf->Text($x + 16.6, $y - 1, "Telefono");
				
				$pdf->SetFont('Arial','B', 9);
				$pdf->Text($x + 18.5, $y + 25.6, "Pagina ".$paginaActual);				
			}

			$pdf->SetFont('Arial', 'B', 6.1);
			$pdf->Text($x + 1, $y, $oProveedor->Apellido . ", " . $oProveedor->Nombre);
			$pdf->Text($x + 4.8, $y, $oProveedor->Proveedor);
			$pdf->Text($x + 7.4, $y, $oProveedor->CuitCuil);
			$pdf->Text($x + 12.5, $y, $oProveedor->Email);
			$pdf->Text($x + 16.6, $y, $oProveedor->Telefono);
			
			$cont++;
			$y+=0.5;
		}
		
		$pdf->Output($outputPdf);	
	}
	
	
	public function ExportCsv(array $filter = NULL)
	{
		$Ivas			= new Ivas();
		
		if (!DBAccess::$db->Begin())		
			return false;
		
		$FileName = "Codigos Trabajo.xls";
		
		header("Pragma: no-cache");
		header("Expires: -1");
		header("Cache-Control: no-store, no-cache, must-revalidate");		
		header("Content-Type: application/x-unknown");
		$header = "Content-Disposition: attachment; filename=" . $FileName . ";";
		header($header);
			
		$arrCodigosTrabajo = $this->GetAll($filter);
				
		$Separador 	= "\t";
		$SaltoLinea = "\n";
				
		$csv.= "IdModeloPV";
		$csv.= $Separador;
		$csv.= "Descripcion";
		$csv.= $Separador;
		$csv.= "CodigoHistorico";
		$csv.= $Separador;
		$csv.= "Codigo";	
		$csv.= $SaltoLinea;
	
		foreach ($arrCodigosTrabajo as $oCodigoTrabajo)
		{				
			
			$csv.= str_replace('(\t|\n)','', trim($oCodigoTrabajo->IdModeloPV));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCodigoTrabajo->Descripcion));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oProveedor->CodigoHistorico));
			$csv.= $Separador;
			$csv.= str_replace('(\t|\n)','', trim($oCodigoTrabajo->Codigo));		
			$csv.= $SaltoLinea;			
		}		
		
		DBAccess::$db->Commit();

		print($csv);
		
		return true;	
	}
}

?>