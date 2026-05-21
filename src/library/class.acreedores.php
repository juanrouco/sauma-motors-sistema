<?php

require_once('class.db.php');
require_once('class.dbaccess.php');
require_once('class.acreedor.php');
require_once('class.personatipos.php');
require_once('class.tiposdocumento.php');
require_once('class.tiposiva.php');
require_once('class.profesiones.php');
require_once('class.estadosciviles.php');
require_once('class.usuarios.php');
require_once('class.filter.php');
require_once('class.page.php');

class Acreedores extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['RazonSocial'])) && ($filter['RazonSocial'] != ''))
			$sql.= " AND RazonSocial LIKE '%" . DB::StringUnquoted($filter['RazonSocial']) . "%'";

		if ((isset($filter['Email'])) && ($filter['Email'] != ''))
			$sql.= " AND Email LIKE '%" . DB::StringUnquoted($filter['Email']) . "%'";

		if ((isset($filter['ClaveFiscalNumero'])) && ($filter['ClaveFiscalNumero'] != ''))
			$sql.= " AND ClaveFiscalNumero LIKE '%" . DB::StringUnquoted($filter['ClaveFiscalNumero']) . "%'";

		if ((isset($filter['IdTipoPersona'])) && ($filter['IdTipoPersona'] != ''))
			$sql.= " AND IdTipoPersona = " . DB::Number($filter['IdTipoPersona']);

		return $sql;
	}	
	

	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Acreedores";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY RazonSocial";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		$arr = array();

		while ($oRow = $oRes->GetRow())	
		{	
			$oAcreedor = new Acreedor();
			$oAcreedor->ParseFromArray($oRow);
			
			array_push($arr, $oAcreedor);
			
			$oRes->MoveNext();
		}	
		
		return $arr;		
	}


	public function GetAllByLocalidad(Localidad $oLocalidad)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_Acreedores";
		$sql.= " WHERE DomicilioIdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);

		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
	
		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oAcreedor = new Acreedor();
			$oAcreedor->ParseFromArray($oRow);
			
			array_push($arr, $oAcreedor);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}

	
	public function GetAllByTipoDocumento(TipoDocumento $oTipoDocumento)
	{	
		$sql = " SELECT *";
		$sql.= " FROM TB_Acreedores";
		$sql.= " WHERE DocumentoTipo = " . DB::Number($oTipoDocumento->IdTipoDocumento);
		
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oAcreedor = new Acreedor();
			$oAcreedor->ParseFromArray($oRow);
			
			array_push($arr, $oAcreedor);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdAcreedor)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Acreedores";
		$sql.= " WHERE IdAcreedor = " . DB::Number($IdAcreedor);	
			
		if ( !($oRes = $this->GetQuery($sql)) )
			return false;
			
		if ( !($oRow = $oRes->GetRow()) )
			return false;
		
		$oAcreedor = new Acreedor();
		$oAcreedor->ParseFromArray($oRow);

		return $oAcreedor;		
	}


	public function GetCountRows(array $filter = NULL)
	{
		$sql = " SELECT *";
		$sql.= " FROM TB_Acreedores";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";

		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function Create(Acreedor $oAcreedor)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
	
		$arr = array
		(
			'IdTipoPersona'					=> DB::Number($oAcreedor->IdTipoPersona),
			'IdNacionalidad'				=> $oAcreedor->IdNacionalidad ? DB::Number($oAcreedor->IdNacionalidad) : 'NULL',
			'NumeroInscripcion'				=> DB::String($oAcreedor->NumeroInscripcion),
			'RazonSocial'					=> DB::String($oAcreedor->RazonSocial),
			'DomicilioCalle'				=> DB::String($oAcreedor->DomicilioCalle),
			'DomicilioNumero'				=> DB::String($oAcreedor->DomicilioNumero),
			'DomicilioPiso'					=> DB::String($oAcreedor->DomicilioPiso),
			'DomicilioDpto'					=> DB::String($oAcreedor->DomicilioDpto),
			'DomicilioIdLocalidad'			=> DB::Number($oAcreedor->DomicilioIdLocalidad),
			'DomicilioCodigoPostal'			=> DB::String($oAcreedor->DomicilioCodigoPostal),
			'TelefonoCodigoArea'			=> DB::String($oAcreedor->TelefonoCodigoArea),
			'Telefono'						=> DB::String($oAcreedor->Telefono),
			'DocumentoTipo'					=> DB::Number($oAcreedor->DocumentoTipo),
			'DocumentoNumero'				=> DB::String($oAcreedor->DocumentoNumero),			
			'DocumentoExpedido'				=> DB::String($oAcreedor->DocumentoExpedido),
			'FechaNacimiento'				=> DB::Date($oAcreedor->FechaNacimiento),
			'ClaveFiscalTipo'				=> DB::Number($oAcreedor->ClaveFiscalTipo),
			'ClaveFiscalNumero'				=> DB::String($oAcreedor->ClaveFiscalNumero),
			'Email'							=> DB::String($oAcreedor->Email),
			'EnteJuridicoOtorgacion'		=> DB::String($oAcreedor->EnteJuridicoOtorgacion),
			'EnteJuridicoDatosInscripcion'	=> DB::String($oAcreedor->EnteJuridicoDatosInscripcion),
			'EnteJuridicoFechaInscripcion'	=> DB::Date($oAcreedor->EnteJuridicoFechaInscripcion)
		);

		if (!DBAccess::Insert('TB_Acreedores', $arr))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		$oAcreedor->IdAcreedor = DBAccess::GetLastInsertId();	

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
			
		return $oAcreedor;
	}
	
	
	public function Update(Acreedor $oAcreedor)
	{
		/* inicia una transaccion */
		if (!DBAccess::$db->Begin())
			return false;
		
		$arr = array
		(
			'IdTipoPersona'					=> DB::Number($oAcreedor->IdTipoPersona),
			'IdNacionalidad'				=> $oAcreedor->IdNacionalidad ? DB::Number($oAcreedor->IdNacionalidad) : 'NULL',
			'NumeroInscripcion'				=> DB::String($oAcreedor->NumeroInscripcion),
			'RazonSocial'					=> DB::String($oAcreedor->RazonSocial),
			'DomicilioCalle'				=> DB::String($oAcreedor->DomicilioCalle),
			'DomicilioNumero'				=> DB::String($oAcreedor->DomicilioNumero),
			'DomicilioPiso'					=> DB::String($oAcreedor->DomicilioPiso),
			'DomicilioDpto'					=> DB::String($oAcreedor->DomicilioDpto),
			'DomicilioIdLocalidad'			=> DB::Number($oAcreedor->DomicilioIdLocalidad),
			'DomicilioCodigoPostal'			=> DB::String($oAcreedor->DomicilioCodigoPostal),
			'TelefonoCodigoArea'			=> DB::String($oAcreedor->TelefonoCodigoArea),
			'Telefono'						=> DB::String($oAcreedor->Telefono),
			'DocumentoTipo'					=> DB::Number($oAcreedor->DocumentoTipo),
			'DocumentoNumero'				=> DB::String($oAcreedor->DocumentoNumero),			
			'DocumentoExpedido'				=> DB::String($oAcreedor->DocumentoExpedido),
			'FechaNacimiento'				=> DB::Date($oAcreedor->FechaNacimiento),
			'ClaveFiscalTipo'				=> DB::Number($oAcreedor->ClaveFiscalTipo),
			'ClaveFiscalNumero'				=> DB::String($oAcreedor->ClaveFiscalNumero),
			'Email'							=> DB::String($oAcreedor->Email),
			'EnteJuridicoOtorgacion'		=> DB::String($oAcreedor->EnteJuridicoOtorgacion),
			'EnteJuridicoDatosInscripcion'	=> DB::String($oAcreedor->EnteJuridicoDatosInscripcion),
			'EnteJuridicoFechaInscripcion'	=> DB::Date($oAcreedor->EnteJuridicoFechaInscripcion)
		);

		$where = " IdAcreedor = " . (int)$oAcreedor->IdAcreedor;
		
		if (!DBAccess::Update('TB_Acreedores', $arr, $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}			

		/* finaliza la transaccion */
		DBAccess::$db->Commit();
		
		return $oAcreedor;
	}
	
	
	public function Delete($IdAcreedor)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdAcreedor = " . DB::Number($IdAcreedor);

		if (!DBAccess::Delete('TB_Acreedores', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}		

		DBAccess::$db->Commit();
		
		return true;	
	}	
}

?>