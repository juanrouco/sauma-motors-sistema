<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.gestoria.php');
require_once('class.filter.php');
require_once('class.page.php');

class Gestorias extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdMinuta'])) && ($filter['IdMinuta'] != ''))
			$sql.= " AND g.IdMinuta = " . DB::Number($filter['IdMinuta']);

		if ((isset($filter['IdUnidad'])) && ($filter['IdUnidad'] != ''))
			$sql.= " AND m.IdUnidad = " . DB::Number($filter['IdUnidad']);

		if ((isset($filter['FechaGestion'])) && ($filter['FechaGestion'] != ''))
			$sql.= " AND g.FechaGestion = " . DB::Date($filter['FechaGestion']);

		return $sql;
	}	
	

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT g.*";
		$sql.= " FROM TB_Gestorias g";
		$sql.= " INNER JOIN TB_Minutas m ON g.IdMinuta = m.IdMinuta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY g.IdGestoria DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oGestoria = new Gestoria();
			$oGestoria->ParseFromArray($oRow);
			
			array_push($arr, $oGestoria);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByLocalidad(Localidad $oLocalidad)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Gestorias";
		$sql.= " WHERE DomicilioFiscalIdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oGestoria = new Gestoria();
			$oGestoria->ParseFromArray($oRow);
			
			array_push($arr, $oGestoria);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetById($IdGestoria)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Gestorias";
		$sql.= " WHERE IdGestoria = " . DB::Number($IdGestoria);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oGestoria = new Gestoria();
		$oGestoria->ParseFromArray($oRow);

		return $oGestoria;		
	}


	public function GetByMinuta(Minuta $oMinuta)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Gestorias";
		$sql.= " WHERE IdMinuta = " . DB::Number($oMinuta->IdMinuta);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oGestoria = new Gestoria();
		$oGestoria->ParseFromArray($oRow);

		return $oGestoria;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT g.*";
		$sql.= " FROM TB_Gestorias g";
		$sql.= " INNER JOIN TB_Minutas m ON g.IdMinuta = m.IdMinuta";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayDB(Gestoria $oGestoria)
	{
		$arr = array
		(
			'IdMinuta'						=> DB::Number($oGestoria->IdMinuta),
			'IdTipoUso'						=> DB::Number($oGestoria->IdTipoUso),
			'IdClienteCondominio'			=> DB::Number($oGestoria->IdClienteCondominio),
			'CondominioConyuge'				=> DB::Bool($oGestoria->CondominioConyuge),
			'PorcentajeTitularidad'			=> DB::Number($oGestoria->PorcentajeTitularidad),
			'NumeroCertificado'				=> DB::String($oGestoria->NumeroCertificado),
			'DomicilioFiscalCalle'			=> DB::String($oGestoria->DomicilioFiscalCalle),
			'DomicilioFiscalNumero'			=> DB::String($oGestoria->DomicilioFiscalNumero),
			'DomicilioFiscalPiso'			=> DB::String($oGestoria->DomicilioFiscalPiso),
			'DomicilioFiscalDpto'			=> DB::String($oGestoria->DomicilioFiscalDpto),
			'DomicilioFiscalIdLocalidad'	=> DB::Number($oGestoria->DomicilioFiscalIdLocalidad),
			'FechaGestion'					=> DB::Date($oGestoria->FechaGestion),
			'SociedadHecho'					=> DB::Bool($oGestoria->SociedadHecho)
		);
		
		return $arr;
	}
	
	public function Create(Gestoria $oGestoria)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->GetArrayDB($oGestoria);

		if (!DBAccess::Insert('TB_Gestorias', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oGestoria->IdGestoria = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oGestoria;
	}
	
	
	public function Update(Gestoria $oGestoria)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = $this->GetArrayDB($oGestoria);

		$where = " IdGestoria = " . (int)$oGestoria->IdGestoria;
		
		if (!DBAccess::Update('TB_Gestorias', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oGestoria;
	}


	public function Delete($IdGestoria)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdGestoria = " . DB::Number($IdGestoria);
		if (!DBAccess::Delete('TB_GestoriaCedulas', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_GestoriaSocios', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}
		if (!DBAccess::Delete('TB_Gestorias', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>