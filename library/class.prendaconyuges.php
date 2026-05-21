<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.prendaconyuge.php');
require_once('class.tiposdocumento.php');
require_once('class.estadosciviles.php');
require_once('class.filter.php');
require_once('class.page.php');

class PrendaConyuges extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}	
	

	public function GetAllByPrenda(Prenda $oPrenda)
	{	
		$sql = " SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE IdPrenda = " . DB::Number($oPrenda->IdPrenda);
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaConyuge = new PrendaConyuge();
			$oPrendaConyuge->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaConyuge);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByPais(Pais $oPais)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE IdNacionalidad = " . DB::Number($oPais->IdPais);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaConyuge = new PrendaConyuge();
			$oPrendaConyuge->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaConyuge);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByLocalidad(Localidad $oLocalidad)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE DomicilioIdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaConyuge = new PrendaConyuge();
			$oPrendaConyuge->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaConyuge);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByTipoDocumento(TipoDocumento $oTipoDocumento)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE DocumentoTipo = " . DB::Number($oTipoDocumento->IdTipoDocumento);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaConyuge = new PrendaConyuge();
			$oPrendaConyuge->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaConyuge);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByProfesion(Profesion $oProfesion)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE IdProfesion = " . DB::Number($oProfesion->IdProfesion);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaConyuge = new PrendaConyuge();
			$oPrendaConyuge->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaConyuge);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByEstadoCivil(EstadoCivil $oEstadoCivil)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE IdEstadoCivil = " . DB::Number($oEstadoCivil->IdEstadoCivil);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaConyuge = new PrendaConyuge();
			$oPrendaConyuge->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaConyuge);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetById($IdConyuge)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE IdConyuge = " . DB::Number($IdConyuge);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPrendaConyuge = new PrendaConyuge();
		$oPrendaConyuge->ParseFromArray($oRow);

		return $oPrendaConyuge;		
	}


	public function GetByKey($IdPrenda, $IdTipoConyuge)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE IdPrenda = " . DB::Number($IdPrenda);	
		$sql.= " AND IdTipoConyuge = " . DB::Number($IdTipoConyuge);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPrendaConyuge = new PrendaConyuge();
		$oPrendaConyuge->ParseFromArray($oRow);

		return $oPrendaConyuge;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_PrendaConyuges";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(PrendaConyuge $oPrendaConyuge)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdPrenda'					=> DB::Number($oPrendaConyuge->IdPrenda),
			'IdTipoConyuge'				=> DB::Number($oPrendaConyuge->IdTipoConyuge),
			'RazonSocial'				=> DB::String($oPrendaConyuge->RazonSocial),
			'DomicilioCalle'			=> DB::String($oPrendaConyuge->DomicilioCalle),
			'DomicilioNumero'			=> DB::String($oPrendaConyuge->DomicilioNumero),
			'DomicilioPiso'				=> DB::String($oPrendaConyuge->DomicilioPiso),
			'DomicilioDpto'				=> DB::String($oPrendaConyuge->DomicilioDpto),
			'DomicilioIdLocalidad'		=> DB::Number($oPrendaConyuge->DomicilioIdLocalidad),
			'DocumentoTipo'				=> DB::Number($oPrendaConyuge->DocumentoTipo),
			'DocumentoNumero'			=> DB::String($oPrendaConyuge->DocumentoNumero),			
			'FechaNacimiento'			=> DB::Date($oPrendaConyuge->FechaNacimiento),
			'IdProfesion'				=> DB::Number($oPrendaConyuge->IdProfesion),
			'IdNacionalidad'			=> DB::Number($oPrendaConyuge->IdNacionalidad),
			'IdEstadoCivil'				=> DB::Number($oPrendaConyuge->IdEstadoCivil)
		);

		if (!DBAccess::Insert('TB_PrendaConyuges', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oPrendaConyuge->IdFiador = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oPrendaConyuge;
	}
	
	
	public function Update(PrendaConyuge $oPrendaConyuge)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'RazonSocial'			=> DB::String($oPrendaConyuge->RazonSocial),
			'DomicilioCalle'		=> DB::String($oPrendaConyuge->DomicilioCalle),
			'DomicilioNumero'		=> DB::String($oPrendaConyuge->DomicilioNumero),
			'DomicilioPiso'			=> DB::String($oPrendaConyuge->DomicilioPiso),
			'DomicilioDpto'			=> DB::String($oPrendaConyuge->DomicilioDpto),
			'DomicilioIdLocalidad'	=> DB::Number($oPrendaConyuge->DomicilioIdLocalidad),
			'DocumentoTipo'			=> DB::Number($oPrendaConyuge->DocumentoTipo),
			'DocumentoNumero'		=> DB::String($oPrendaConyuge->DocumentoNumero),			
			'FechaNacimiento'		=> DB::Date($oPrendaConyuge->FechaNacimiento),
			'IdProfesion'			=> DB::Number($oPrendaConyuge->IdProfesion),
			'IdNacionalidad'		=> DB::Number($oPrendaConyuge->IdNacionalidad),
			'IdEstadoCivil'			=> DB::Number($oPrendaConyuge->IdEstadoCivil)
		);

		$where = " IdConyuge = " . (int)$oPrendaConyuge->IdConyuge;
		
		if (!DBAccess::Update('TB_PrendaConyuges', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oPrendaConyuge;
	}
	
	
	public function Delete($IdConyuge)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdConyuge = " . (int)$IdConyuge;

		if (!DBAccess::Delete('TB_PrendaConyuges', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>