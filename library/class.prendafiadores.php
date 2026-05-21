<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.prendafiador.php');
require_once('class.personatipos.php');
require_once('class.tiposdocumento.php');
require_once('class.estadosciviles.php');
require_once('class.filter.php');
require_once('class.page.php');

class PrendaFiadores extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		return $sql;
	}	
	

	public function GetAllByPrenda(Prenda $oPrenda)
	{	
		$sql = " SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE IdPrenda = " . DB::Number($oPrenda->IdPrenda);
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaFiador = new PrendaFiador();
			$oPrendaFiador->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaFiador);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByPais(Pais $oPais)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE IdNacionalidad = " . DB::Number($oPais->IdPais);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaFiador = new PrendaFiador();
			$oPrendaFiador->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaFiador);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByLocalidad(Localidad $oLocalidad)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE DomicilioIdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaFiador = new PrendaFiador();
			$oPrendaFiador->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaFiador);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByTipoDocumento(TipoDocumento $oTipoDocumento)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE DocumentoTipo = " . DB::Number($oTipoDocumento->IdTipoDocumento);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaFiador = new PrendaFiador();
			$oPrendaFiador->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaFiador);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByProfesion(Profesion $oProfesion)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE IdProfesion = " . DB::Number($oProfesion->IdProfesion);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaFiador = new PrendaFiador();
			$oPrendaFiador->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaFiador);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByEstadoCivil(EstadoCivil $oEstadoCivil)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE IdEstadoCivil = " . DB::Number($oEstadoCivil->IdEstadoCivil);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oPrendaFiador = new PrendaFiador();
			$oPrendaFiador->ParseFromArray($oRow);
			
			array_push($arr, $oPrendaFiador);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetById($IdFiador)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE IdFiador = " . DB::Number($IdFiador);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oPrendaFiador = new PrendaFiador();
		$oPrendaFiador->ParseFromArray($oRow);

		return $oPrendaFiador;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_PrendaFiadores";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(PrendaFiador $oPrendaFiador)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdPrenda'				=> DB::Number($oPrendaFiador->IdPrenda),
			'RazonSocial'			=> DB::String($oPrendaFiador->RazonSocial),
			'DomicilioCalle'		=> DB::String($oPrendaFiador->DomicilioCalle),
			'DomicilioNumero'		=> DB::String($oPrendaFiador->DomicilioNumero),
			'DomicilioPiso'			=> DB::String($oPrendaFiador->DomicilioPiso),
			'DomicilioDpto'			=> DB::String($oPrendaFiador->DomicilioDpto),
			'DomicilioIdLocalidad'	=> DB::Number($oPrendaFiador->DomicilioIdLocalidad),
			'DocumentoTipo'			=> DB::Number($oPrendaFiador->DocumentoTipo),
			'DocumentoNumero'		=> DB::String($oPrendaFiador->DocumentoNumero),			
			'FechaNacimiento'		=> DB::Date($oPrendaFiador->FechaNacimiento),
			'IdProfesion'			=> DB::Number($oPrendaFiador->IdProfesion),
			'IdNacionalidad'		=> DB::Number($oPrendaFiador->IdNacionalidad),
			'IdEstadoCivil'			=> DB::Number($oPrendaFiador->IdEstadoCivil),
			'Descripcion'			=> DB::String($oPrendaFiador->Descripcion),
			'Posicion'				=> DB::Number($oPrendaFiador->Posicion)
		);

		if (!DBAccess::Insert('TB_PrendaFiadores', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oPrendaFiador->IdFiador = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oPrendaFiador;
	}
	
	
	public function Update(PrendaFiador $oPrendaFiador)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdPrenda'				=> DB::Number($oPrendaFiador->IdPrenda),
			'RazonSocial'			=> DB::String($oPrendaFiador->RazonSocial),
			'DomicilioCalle'		=> DB::String($oPrendaFiador->DomicilioCalle),
			'DomicilioNumero'		=> DB::String($oPrendaFiador->DomicilioNumero),
			'DomicilioPiso'			=> DB::String($oPrendaFiador->DomicilioPiso),
			'DomicilioDpto'			=> DB::String($oPrendaFiador->DomicilioDpto),
			'DomicilioIdLocalidad'	=> DB::Number($oPrendaFiador->DomicilioIdLocalidad),
			'DocumentoTipo'			=> DB::Number($oPrendaFiador->DocumentoTipo),
			'DocumentoNumero'		=> DB::String($oPrendaFiador->DocumentoNumero),			
			'FechaNacimiento'		=> DB::Date($oPrendaFiador->FechaNacimiento),
			'IdProfesion'			=> DB::Number($oPrendaFiador->IdProfesion),
			'IdNacionalidad'		=> DB::Number($oPrendaFiador->IdNacionalidad),
			'IdEstadoCivil'			=> DB::Number($oPrendaFiador->IdEstadoCivil),
			'Descripcion'			=> DB::String($oPrendaFiador->Descripcion),
			'Posicion'				=> DB::Number($oPrendaFiador->Posicion)
		);

		$where = " IdFiador = " . (int)$oPrendaFiador->IdFiador;
		
		if (!DBAccess::Update('TB_PrendaFiadores', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oPrendaFiador;
	}
	
	
	public function Delete($IdFiador)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdFiador = " . DB::Number($IdFiador);

		if (!DBAccess::Delete('TB_PrendaFiadores', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>