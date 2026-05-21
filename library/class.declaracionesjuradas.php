<?php 

require_once('class.dbaccess.php');
require_once('class.declaracionjurada.php');
require_once('class.tiposformulario.php');
require_once('class.filter.php');
require_once('class.page.php');

class DeclaracionesJuradas extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';

		if ((isset($filter['IdDeclaracionJurada'])) && ($filter['IdDeclaracionJurada'] != ''))
			$sql.= " AND IdDeclaracionJurada = " . DB::Number($filter['IdDeclaracionJurada']);

		if ((isset($filter['Fecha'])) && ($filter['Fecha'] != ''))
			$sql.= " AND Fecha = " . DB::Date($filter['Fecha']);

		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DeclaracionesJuradas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdDeclaracionJurada DESC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oDeclaracionJurada = new DeclaracionJurada();
			$oDeclaracionJurada->ParseFromArray($oRow);
			
			array_push($arr, $oDeclaracionJurada);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdDeclaracionJurada)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DeclaracionesJuradas";
		$sql.= " WHERE IdDeclaracionJurada = " . DB::Number($IdDeclaracionJurada);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oDeclaracionJurada = new DeclaracionJurada();
		$oDeclaracionJurada->ParseFromArray($oRow);
		
		return $oDeclaracionJurada;		
	}
	

	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_DeclaracionesJuradas";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	
	public function GenerateDeclaracion()
	{
		$Mensaje			= '';
		$oFormularios 		= new Formularios();
		$oTiposFormulario 	= new TiposFormulario();
		
		/* obtenemos el listado de formularios que requieren declaracion jurada */
		$arrTiposFormulario = $oTiposFormulario->GetAllForDeclaracionJurada();
		
		foreach ($arrTiposFormulario as $oTipoFormulario)
		{
			$Mensaje.= '<br>';
			$Mensaje.= 'DECLARACION JURADA DE CONSUMO DE SOLICITUDES TIPO "01" ';
			$Mensaje.= (($oTipoFormulario->IdTipoFormulario == 1) ? 'NACIONAL' : 'IMPORTADO') . ': ';
			
			/* obtenemos los fomularios sin declarar correspondiente al tipo */
			$arrFormularios = $oFormularios->GetAllForDeclaracionJurada($oTipoFormulario->IdTipoFormulario);

			/* si hay formulario para declarar, entonces generamos la delcaracion */
			if (count($arrFormularios) > 0)
			{
				$oDeclaracionJurada = new DeclaracionJurada();
				$oDeclaracionJurada->Fecha = date('Y-m-d');
				
				$arr = array
				(
					'Fecha' 	=> DB::String($oDeclaracionJurada->Fecha),
					'IdTipo' 	=> DB::String($oTipoFormulario->IdTipoFormulario)
				);
				
				if (!$this->Insert('TB_DeclaracionesJuradas', $arr))
					return false;
		
				/* asignamos el id generado */
				$oDeclaracionJurada->IdDeclaracionJurada = DBAccess::GetLastInsertId();
				
				foreach ($arrFormularios as $oFormulario)
				{
					$oFormulario->IdDeclaracion = $oDeclaracionJurada->IdDeclaracionJurada;
					
					$oFormularios->Update($oFormulario);
				}
				
				$Mensaje.= count($arrFormularios) . ' FORMULARIO DECLARADOS';
			}
			else
			{
				$Mensaje.= count($arrFormularios) . ' NO HAY FORMULARIO PARA DECLARAR';
			}
		}
			
		return $Mensaje;
	}
}

?>