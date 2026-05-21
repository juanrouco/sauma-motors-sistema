<?php 

require_once('class.dbaccess.php');
require_once('class.datoempresa.php');

class DatosEmpresa extends DBAccess
{
	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DatosEmpresa";
		$sql.= " LIMIT 1";
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oDatoEmpresa = new DatoEmpresa();
		$oDatoEmpresa->ParseFromArray($oRow);
		
		return $oDatoEmpresa;
	}
	

	public function GetAllByPais(Pais $oPais)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_DatosEmpresa ";
		$sql.= " WHERE IdPais = " . DB::Number($oPais->IdPais);

		if (!($oRes = $this->GetQuery($sql)))
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oDatoEmpresa = new DatoEmpresa();
			$oDatoEmpresa->ParseFromArray($oRow);
			
			array_push($arr, $oDatoEmpresa);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}

	
	public function GetAllByProvincia(Provincia $oProvincia)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_DatosEmpresa ";
		$sql.= " WHERE IdProvincia = " . DB::Number($oProvincia->IdProvincia);

		if (!($oRes = $this->GetQuery($sql)))
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oDatoEmpresa = new DatoEmpresa();
			$oDatoEmpresa->ParseFromArray($oRow);
			
			array_push($arr, $oDatoEmpresa);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}
	

	public function GetAllByPartido(Partido $oPartido)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_DatosEmpresa";
		$sql.= " WHERE IdPartido = " . DB::Number($oPartido->IdPartido);

		if (!($oRes = $this->GetQuery($sql)))
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oDatoEmpresa = new DatoEmpresa();
			$oDatoEmpresa->ParseFromArray($oRow);
			
			array_push($arr, $oDatoEmpresa);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}


	public function GetAllByLocalidad(Localidad $oLocalidad)
	{	
		$sql = "SELECT *";
		$sql.= " FROM TB_DatosEmpresa";
		$sql.= " WHERE IdLocalidad = " . DB::Number($oLocalidad->IdLocalidad);

		if (!($oRes = $this->GetQuery($sql)))
			return false;

		$arr = array();
					
		while ($oRow = $oRes->GetRow())	
		{	
			$oDatoEmpresa = new DatoEmpresa();
			$oDatoEmpresa->ParseFromArray($oRow);
			
			array_push($arr, $oDatoEmpresa);
			
			$oRes->MoveNext();
		}
		
		return $arr;
	}

	
	public function Update(DatoEmpresa $oDatoEmpresa)
	{	
		$where = '1';
		
		$arr = array
		(			
			'RazonSocial'				=> DB::String($oDatoEmpresa->RazonSocial),
			'TelefonoCodigoArea'		=> DB::String($oDatoEmpresa->TelefonoCodigoArea),
			'Telefono'					=> DB::String($oDatoEmpresa->Telefono),
			'TelefonoCodigoArea2'		=> DB::String($oDatoEmpresa->TelefonoCodigoArea2),
			'Telefono2'					=> DB::String($oDatoEmpresa->Telefono2),
			'CodigoAreaFax'				=> DB::String($oDatoEmpresa->CodigoAreaFax),
			'Fax'						=> DB::String($oDatoEmpresa->Fax),
			'Email'						=> DB::String($oDatoEmpresa->Email),
			'IdPais'					=> DB::Number($oDatoEmpresa->IdPais),
			'IdProvincia'				=> DB::Number($oDatoEmpresa->IdProvincia),
			'IdLocalidad'				=> DB::Number($oDatoEmpresa->IdLocalidad),
			'CodigoPostal'				=> DB::String($oDatoEmpresa->CodigoPostal),
			'DomicilioCalle'			=> DB::String($oDatoEmpresa->DomicilioCalle),
			'DomicilioNumero'			=> DB::String($oDatoEmpresa->DomicilioNumero),
			'DomicilioPiso'				=> DB::String($oDatoEmpresa->DomicilioPiso),
			'DomicilioDpto'				=> DB::String($oDatoEmpresa->DomicilioDpto),
			'PaginaWeb'					=> DB::String($oDatoEmpresa->PaginaWeb),
			'ComercianteHabitualista'	=> DB::String($oDatoEmpresa->ComercianteHabitualista)
		);
		
		if (!DBAccess::Update('TB_DatosEmpresa', $arr, $where))
			return false;
		
		return $oDatoEmpresa;
	}
}

?>