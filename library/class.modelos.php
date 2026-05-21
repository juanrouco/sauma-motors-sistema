<?php 

require_once('class.dbaccess.php');
require_once('class.modelo.php');
require_once('class.modelosmigracion.php');
require_once('class.seriesmigracion.php');
require_once('class.marcas.php');
require_once('class.tiposmodelo.php');
require_once('class.categoriasmodelo.php');
require_once('class.unidades.php');
require_once('class.filter.php');
require_once('class.page.php');
require_once('excel_reader/class.xlsreader.php');
require_once('excel_export/class.xlsexport.php');


class Modelos extends DBAccess implements IFilterable
{
	public function ParseFilter(array $filter)
	{
		$sql = '';
		
		if ((isset($filter['DenominacionComercial'])) && ($filter['DenominacionComercial'] != ''))
		{
			$sql.= " AND (DenominacionComercial LIKE '%" . DB::StringUnquoted($filter['DenominacionComercial']) . "%'";
			$sql.= " OR DenominacionComercial IS NULL)";
		}

		if ((isset($filter['CodigoComercial'])) && ($filter['CodigoComercial'] != ''))
		{
			$sql.= " AND (CodigoComercial LIKE '%" . DB::StringUnquoted($filter['CodigoComercial']) . "%'";
			$sql.= " OR CodigoComercial IS NULL)";
		}

		if ((isset($filter['NumeroVinPrefijo'])) && ($filter['NumeroVinPrefijo'] != ''))
			$sql.= " AND NumeroVinPrefijo LIKE '%" . DB::StringUnquoted($filter['NumeroVinPrefijo']) . "%'";

		if ((isset($filter['IdTipoModelo'])) && ($filter['IdTipoModelo'] != ''))
			$sql.= " AND IdTipoModelo = " . DB::Number($filter['IdTipoModelo']);

		if ((isset($filter['IdMarcaMotor'])) && ($filter['IdMarcaMotor'] != ''))
			$sql.= " AND IdMarcaMotor = " . DB::Number($filter['IdMarcaMotor']);

		if ((isset($filter['IdMarcaChasis'])) && ($filter['IdMarcaChasis'] != ''))
			$sql.= " AND IdMarcaChasis = " . DB::Number($filter['IdMarcaChasis']);

		if ((isset($filter['IdMarcaVehiculo'])) && ($filter['IdMarcaVehiculo'] != ''))
			$sql.= " AND IdMarcaVehiculo = " . DB::Number($filter['IdMarcaVehiculo']);

		if ((isset($filter['Obsoleto'])) && ($filter['Obsoleto'] !== ''))
			$sql.= " AND Obsoleto = " . DB::Bool($filter['Obsoleto']);

		if ((isset($filter['ConStock'])) && ($filter['ConStock'] != ''))
		{
			if ($filter['ConStock'] == '1')
				$sql.= " AND IdModelo IN (SELECT IdModelo FROM TB_Unidades WHERE IdEstado = " . DB::Number(EstadoUnidad::Stock) . ")";
			if ($filter['ConStock'] == '0')
				$sql.= " AND IdModelo NOT IN (SELECT IdModelo FROM TB_Unidades WHERE IdEstado = " . DB::Number(EstadoUnidad::Stock) . ")";
		}
		
		return $sql;
	}


	public function GetAll(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdMarcaVehiculo, DenominacionComercial ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllModelos(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY DenominacionComercial";
		$sql.= " ORDER BY IdMarcaVehiculo, DenominacionComercial ASC";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllOrdered(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY DenominacionComercial";
		$sql.= " ORDER BY DenominacionComercial";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}
	
	public function GetAllNumeroLista(array $filter = NULL, Page $oPage = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " GROUP BY CodigoComercial";
		$sql.= " ORDER BY DenominacionModelo";
		$sql.= ($oPage) ? Pageable::ParsePage($oPage) : "";
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}	

	public function GetAllByTipoModelo(TipoModelo $oTipoModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE IdTipoModelo = " . DB::Number($oTipoModelo->IdTipoModelo);
		
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByCategoriaModelo(CategoriaModelo $oCategoriaModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE IdCategoriaModelo = " . DB::Number($oCategoriaModelo->IdCategoriaModelo);
		
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetAllByMarca(Marca $oMarca)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE ";
		$sql.= " ( ";
		$sql.= " 	IdMarcaVehiculo = " . DB::Number($oMarca->IdMarca);
		$sql.= " 	OR ";
		$sql.= " 	IdMarcaMotor = " . DB::Number($oMarca->IdMarca);
		$sql.= " 	OR ";
		$sql.= " 	IdMarcaChasis = " . DB::Number($oMarca->IdMarca);
		$sql.= " ) ";
		
						
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;
	}


	public function GetById($IdModelo)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE IdModelo = " . DB::Number($IdModelo);
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModelo = new Modelo();
		$oModelo->ParseFromArray($oRow);
		
		return $oModelo;		
	}


	public function GetByCodigoComercial($CodigoComercial)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE CodigoComercial = " . DB::String($CodigoComercial);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModelo = new Modelo();
		$oModelo->ParseFromArray($oRow);
		
		return $oModelo;		
	}
	
	public function GetByCodigoComercialAndPrefijoVin($CodigoComercial, $PrefijoVin)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE CodigoComercial = " . DB::String($CodigoComercial);	
		$sql.= " AND NumeroVinPrefijo = " . DB::String($PrefijoVin);
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModelo = new Modelo();
		$oModelo->ParseFromArray($oRow);
		
		return $oModelo;		
	}
	
	public function GetByPrefijoVin($PrefijoVin, $repsol = false)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE NumeroVinPrefijo = " . DB::String($PrefijoVin);	
		if ($repsol)
			$sql.= " AND DenominacionComercial LIKE '%REPSOL%'";	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		$arr = array();
			
		while ($oRow = $oRes->GetRow())	
		{	
			$oModelo = new Modelo();
			$oModelo->ParseFromArray($oRow);
			
			array_push($arr, $oModelo);
			
			$oRes->MoveNext();
		}	
		
		return $arr;	
	}
	
	public function GetByPrefijoVinAndCodigoComercial($PrefijoVin, $CodigoComercial)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE NumeroVinPrefijo = " . DB::String($PrefijoVin);	
		$sql.= " AND CodigoComercial = " . DB::String($CodigoComercial);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModelo = new Modelo();
		$oModelo->ParseFromArray($oRow);
		
		return $oModelo;		
	}
	

	public function GetByNombre($Nombre)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE Nombre RLIKE " . DB::String($Nombre);	
			
		if (!($oRes = $this->GetQuery($sql)))
			return false;
			
		if (!($oRow = $oRes->GetRow()))
			return false;
		
		$oModelo = new Modelo();
		$oModelo->ParseFromArray($oRow);
		
		return $oModelo;		
	}

	
	public function GetCountRows(array $filter = NULL)
	{
		$sql = "SELECT *";
		$sql.= " FROM TB_Modelos";
		$sql.= " WHERE 1";
		$sql.= ($filter) ? $this->ParseFilter($filter) : "";
		$sql.= " ORDER BY IdModelo DESC";
		
		if (!($oRes = $this->GetQuery($sql)))
			return false;
		
		$CountRows = $oRes->NumRows();
		
		return $CountRows;
	}
	
	private function GetArrayForSql(Modelo $oModelo)
	{
		$arr = array
		(
			'IdTipoCombustible' 		=> DB::Number($oModelo->IdTipoCombustible),
			'IdTipoModelo' 				=> DB::Number($oModelo->IdTipoModelo),
			'IdCategoriaModelo' 		=> DB::Number($oModelo->IdCategoriaModelo),
			'IdMarcaVehiculo' 			=> DB::Number($oModelo->IdMarcaVehiculo),
			'IdMarcaMotor' 				=> DB::Number($oModelo->IdMarcaMotor),
			'IdMarcaChasis' 			=> DB::Number($oModelo->IdMarcaChasis),
			'IdTipoVehiculo'			=> $oModelo->IdTipoVehiculo ?  DB::Number($oModelo->IdTipoVehiculo) : 'NULL',
			'IdTipoCarroceria'			=> $oModelo->IdTipoCarroceria ?  DB::Number($oModelo->IdTipoCarroceria) : 'NULL',
			'IdTipoUso'					=> $oModelo->IdTipoUso ?  DB::Number($oModelo->IdTipoUso) : 'NULL',
			'IdDestinoVehiculo'			=> $oModelo->IdDestinoVehiculo ?  DB::Number($oModelo->IdDestinoVehiculo) : 'NULL',		
			'NumeroVinPrefijo' 			=> DB::String($oModelo->NumeroVinPrefijo),
			'CodigoComercial' 			=> DB::String($oModelo->CodigoComercial),
			'DenominacionModelo' 		=> DB::String($oModelo->DenominacionModelo),
			'DenominacionComercial' 	=> DB::String($oModelo->DenominacionComercial),
			'Anio' 						=> DB::Number($oModelo->Anio),
			'Peso' 						=> DB::Number($oModelo->Peso),
			'PrecioPublicoNeto' 		=> DB::Number($oModelo->PrecioPublicoNeto),
			'PrecioPublicoTotalIva' 	=> DB::Number($oModelo->PrecioPublicoTotalIva),			
			'MesPrecioTotal' 			=> DB::Number($oModelo->MesPrecioTotal),			
			'Patentamiento' 			=> DB::Number($oModelo->Patentamiento),
			'Flete'						=> DB::Number($oModelo->Flete),
			'Precio1' 					=> DB::Number($oModelo->Precio1),			
			'Precio2' 					=> DB::Number($oModelo->Precio2),
			'Ganancia1' 				=> DB::Number($oModelo->Ganancia1),
			'Ganancia2' 				=> DB::Number($oModelo->Ganancia2),			
			'Iva' 						=> DB::Number($oModelo->Iva),
			'Prenda' 					=> DB::Number($oModelo->Prenda),
			'Otorgamiento' 				=> DB::Number($oModelo->Otorgamiento),
			'ImpuestoInterno'			=> DB::Number($oModelo->ImpuestoInterno),
			'BonificacionExtra' 		=> DB::Number($oModelo->BonificacionExtra),
			'DescuentoReventa'			=> DB::Number($oModelo->DescuentoReventa),
			'PrecioCompra'				=> DB::Number($oModelo->PrecioCompra),
			'ReventaPrecio'				=> DB::Number($oModelo->ReventaPrecio),
			'Cilindrada'				=> DB::String($oModelo->Cilindrada),
			'Electrolito'				=> DB::Number($oModelo->Electrolito),
			'FleteFormularios'			=> DB::Number($oModelo->FleteFormularios),
			'GTIN'						=> DB::String($oModelo->GTIN),
			'Cufe'						=> DB::String($oModelo->Cufe),
			'Obsoleto'					=> DB::String($oModelo->Obsoleto)
		);
		
		return $arr;
	}
	
	public function Create(Modelo $oModelo)
	{
		$arr = $this->GetArrayForSql($oModelo);
		
		if (!$this->Insert('TB_Modelos', $arr))
			return false;

		/* asignamos el id generado */
		$oModelo->IdModelo = DBAccess::GetLastInsertId();
			
		return $oModelo;
	}
	
	
	public function Update(Modelo $oModelo)
	{
		$where = " IdModelo = " . DB::Number($oModelo->IdModelo);
		
		$arr = $this->GetArrayForSql($oModelo);
		
		if (!DBAccess::Update('TB_Modelos', $arr, $where))
			return false;
		
		return $oModelo;
	}
	

	public function Delete($IdModelo)
	{
		if (!DBAccess::$db->Begin())
			return false;
			
		$where = " IdModelo = " . DB::Number($IdModelo);

		if (!DBAccess::Delete('TB_Modelos', $where))
		{
			DBAccess::$db->Rollback();	
			return false;
		}

		DBAccess::$db->Commit();
		
		return true;	
	}
	
	private function ConvertirPrecio($Precio)
	{
		if (!$Precio)
			return 0 ;
		$arrPrecio = explode(' ', $Precio);
		
		return str_replace(',', '', $arrPrecio[0]);
	}
		
	public function ImportPrecios($FileName)
	{
		/* declaramos variables necesarias */
		$oModelos	= new Modelos();
		
		/* processamos el archivo */		 
		$arrData = new Spreadsheet_Excel_Reader(Modelo::PathCsvImportBack . $FileName);
		
		if (!DBAccess::$db->Begin())		
			return false;

		$CountCreate = 0;

		/* procesamos el archivo */
		$Row = 1;
		for ($i=2; $i<=$arrData->sheets[0]['numRows']; $i++) 
		{
			$Modelo = $arrData->sheets[0]['cells'][$i];
			$err						= 0;			
			$arrPrefijosVin	 			= trim($Modelo[1]);
			$Nombre			 			= trim($Modelo[2]);
			$PrecioPublicoNeto	 		= $this->ConvertirPrecio($Modelo[3]);			
			$ImpuestoInterno			= $this->ConvertirPrecio($Modelo[4]);
			$Flete						= $this->ConvertirPrecio($Modelo[5]);
			$BonificacionExtra			= $this->ConvertirPrecio($Modelo[6]);
			$Prenda			 			= $this->ConvertirPrecio($Modelo[7]);
			$DescuentoReventa			= $this->ConvertirPrecio($Modelo[8]);
			$PrecioPublicoTotalIva 		= $this->ConvertirPrecio($Modelo[9]);
			$Otorgamiento 				= $this->ConvertirPrecio($Modelo[10]);
			$PrecioCompra 				= $this->ConvertirPrecio($Modelo[11]);
			$ReventaPrecio				= $this->ConvertirPrecio($Modelo[12]);			
			$Precio1					= $this->ConvertirPrecio($Modelo[13]);
			$Precio2					= $this->ConvertirPrecio($Modelo[14]);
			$FleteFormularios			= $this->ConvertirPrecio($Modelo[15]);
			$Patentamiento				= $this->ConvertirPrecio($Modelo[16]);
			$Patentamiento3				= $this->ConvertirPrecio($Modelo[17]);
			$Patentamiento4				= $this->ConvertirPrecio($Modelo[18]);
			
			
			if ($arrPrefijosVin == '')
				$err+= 1;
			
			if ($err == 0)
			{
				$arrPrefijosVin = explode(',', $arrPrefijosVin);
				
				foreach ($arrPrefijosVin as $PrefijoVin)
				{
					$repsol = false;
					if (strpos($Nombre, 'REPSOL') !== false)
						$repsol = true;
					$arrModelo = $this->GetByPrefijoVin($PrefijoVin, $repsol);
					if (!$arrModelo)
						$strError.= "El registro N&deg; " . $Row . " posee un prefijo de vin inexistente: " . $PrefijoVin . ". <br>";
					else
					{
						$oModelo = $arrModelo[0];
						$oModelo->PrecioPublicoNeto		= $PrecioPublicoNeto;
						$oModelo->ImpuestoInterno		= $ImpuestoInterno;
						$oModelo->PrecioPublicoTotalIva = $PrecioPublicoTotalIva;
						$oModelo->Flete					= $Flete;
						$oModelo->PrecioCompra			= $PrecioCompra;
						$oModelo->Precio1				= $Precio1;
						$oModelo->Precio2				= $Precio2;
						$oModelo->Patentamiento			= $Patentamiento;
						$oModelo->ReventaPrecio			= $ReventaPrecio;
						$oModelo->FleteFormularios	= $FleteFormularios;
						$oModelo->Prenda				= $Prenda;
						$oModelo->BonificacionExtra		= $BonificacionExtra;
						$oModelo->DescuentoReventa		= $DescuentoReventa;
						$oModelo->Otorgamiento			= $Otorgamiento;
						
						$oModelos->Update($oModelo);
						$Edit++;
					}
				}
			}
			else
			{
				if ($err & 1)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a no posee prefijos de vin. <br>";
				if ($err & 2)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el Precio Neto es incorrecto. <br>";
				if ($err & 4)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que la bonificaci&oacute;n de compra es incorrecta. <br>";
				if ($err & 8)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que la bonificaci&oacute;n de venta es incorrecta. <br>";
				if ($err & 16)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que la bonificaci&oacute;n extra es incorrecta. <br>";
				if ($err & 32)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el flete y formulario es incorrecto. <br>";
				if ($err & 64)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el descuento reventa es incorrecto. <br>";
				if ($err & 128)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el porcentaje de otorgamiento es incorrecto. <br>";
				if ($err & 256)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el impuesto interno es incorrecto. <br>";
				}					
			
			$Row++;
		}

		DBAccess::$db->Commit();
		
		if ($Creados)
		{
			$strError.= "<br> Se actualizaron " . $Edit . " modelos.";		
		}		
		
		return $strError;
	}
	
		
	public function Import($FileName)
	{
		/* declaramos variables necesarias */
		$oModelos			= new Modelos();
		
		/* processamos el archivo */		 
		$arrData = new Spreadsheet_Excel_Reader(Modelo::PathCsvImportBack . $FileName);
		
		if (!DBAccess::$db->Begin())		
			return false;

		$CountCreate = 0;

		/* procesamos el archivo */
		$Row = 1;
		for ($i=4; $i<=$arrData->sheets[0]['numRows']; $i++) 
		{
			$Modelo = $arrData->sheets[0]['cells'][$i];

			$err						= 0;			
			$Codigo			 			= trim($Modelo[1]);
			$PrecioPublicoNeto	 		= trim(str_replace(",", ".", $Modelo[2]));			
			$Precio2					= trim(str_replace(",", ".", $Modelo[3]));
			$Flete						= trim(str_replace(",", ".", $Modelo[4]));
			$BonificacionExtra			= trim(str_replace(",", ".", $Modelo[5]));
			$FleteFormularios 			= trim(str_replace(",", ".", $Modelo[6]));
			$Otorgamiento 				= trim(str_replace(",", ".", $Modelo[7]));
			$DescuentoReventa			= trim(str_replace(",", ".", $Modelo[8]));			
			$ImpuestoInterno			= trim(str_replace(",", ".", $Modelo[9]));
			
			if (!($oModelo = $oModelos->GetByCodigoComercial($Codigo)))
				$err+= 1;
			if ($PrecioPublicoNeto == '' || !is_numeric($PrecioPublicoNeto))
				$err+= 2;
			if ($Precio2 == '' || !is_numeric($Precio2))
				$err+= 4;
			if ($Flete == '' || !is_numeric($Flete))
				$err+= 8;
			if ($BonificacionExtra == '' || !is_numeric($BonificacionExtra))
				$err+= 16;
			if ($FleteFormularios == '' || !is_numeric($FleteFormularios))
				$err+= 32;
			if ($DescuentoReventa == '' || !is_numeric($DescuentoReventa))
				$err+= 64;
			if ($Otorgamiento == '' || !is_numeric($Otorgamiento))
				$err+= 128;
			if ($ImpuestoInterno == '')
				$ImpuestoInterno = 0;
			if (!is_numeric($ImpuestoInterno))
				$err+= 256;
			
			if ($err == 0)
			{
				$oModelo->PrecioPublicoNeto 	= $PrecioPublicoNeto;
				$oModelo->Precio2 	= $Precio2;
				$oModelo->Flete 	= $Flete;
				$oModelo->BonificacionExtra 	= $BonificacionExtra;
				$oModelo->FleteFormularios 		= $FleteFormularios;				
				$oModelo->DescuentoReventa 		= $DescuentoReventa;
				$oModelo->Otorgamiento 			= $Otorgamiento;
				$oModelo->ImpuestoInterno 		= $ImpuestoInterno;
				$oModelo->CalcularValores();
				
				if ($this->Update($oModelo))
					$Edit++;
			}
			else
			{
				if ($err & 1)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a el c&oacute;digo de modelo es inv&aacute;lido. <br>";
				if ($err & 2)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el Precio Neto es incorrecto. <br>";
				if ($err & 4)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que la bonificaci&oacute;n de compra es incorrecta. <br>";
				if ($err & 8)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que la bonificaci&oacute;n de venta es incorrecta. <br>";
				if ($err & 16)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que la bonificaci&oacute;n extra es incorrecta. <br>";
				if ($err & 32)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el flete y formulario es incorrecto. <br>";
				if ($err & 64)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el descuento reventa es incorrecto. <br>";
				if ($err & 128)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el porcentaje de otorgamiento es incorrecto. <br>";
				if ($err & 256)
					$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el impuesto interno es incorrecto. <br>";
				}					
			
			$Row++;
		}

		DBAccess::$db->Commit();
		
		if ($Creados)
		{
			$strError.= "<br> Se actualizaron " . $Edit . " modelos.";		
		}		
		
		return $strError;
	}
		
		
	public function ExportXlsToUpdate(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oMarcas 			= new Marcas();
		$oTiposModelo 		= new TiposModelo();
		$oCategoriasModelo 	= new CategoriasModelo();

		/* obtenemos el listado de datos a exportar */			
		$arrModelos = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"LISTA DE PRECIO",						
			"",
			"",
			"",
			"",
			"",
			""
		);
		
		$arrData[] = array
		(
			"CODIGO LISTA",			
			"PRECIO PUBLICO",
			"",
			"",
			"",
			"",
			"OTORGAMIENTO",
			"REVENTA",	
			"IMPUESTO INTERNO"
		);

		$arrData[] = array
		(
			"",
			"NETO",			
			"BONIFICACION COMPRA",
			"BONIFICACION VENTA",
			"BONIFICACION EXTRA",
			"FLETE Y FORM",			
			"",
			"DESCUENTO REVENTA",			
			""
		);
				
		foreach ($arrModelos as $oModelo)
		{	
			$oTipoModelo 		= $oTiposModelo->GetById($oModelo->IdTipoModelo);
			$oCategoriaModelo 	= $oCategoriasModelo->GetById($oModelo->IdCategoriaModelo);
			$oMarcaMotor 		= $oMarcas->GetById($oModelo->IdMarcaMotor);
			$oMarcaChasis 		= $oMarcas->GetById($oModelo->IdMarcaChasis);
			$oMarcaVehiculo 	= $oMarcas->GetById($oModelo->IdMarcaVehiculo);

			/* almacenamos el registro */
			$arrData[] = array
			(				
				trim($oModelo->CodigoComercial),			
				trim($oModelo->PrecioPublicoNeto),
				trim($oModelo->Precio2),
				trim($oModelo->Flete),
				trim($oModelo->BonificacionExtra),
				trim($oModelo->FleteFormularios),
				trim($oModelo->Otorgamiento),
				trim($oModelo->DescuentoReventa),
				trim($oModelo->ImpuestoInterno)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'modelos';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
		
		
	public function ExportReporteQuincenalCsv(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oUnidades 			= new Unidades();

		/* obtenemos el listado de datos a exportar */			
		$arrModelos = $this->GetAllModelos();
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"MODELO",			
			"CANTIDAD VENDIDAS",
			"CANTIDAD EN STOCK",
			"CANTIDAD RECIBIDAS"
		);
				
		foreach ($arrModelos as $oModelo)
		{	
			$filterVendido = array();
			$filterVendido['IdUbicacion'] 		= trim($_REQUEST['FilterUbicacion']);	
			$filterVendido['FechaDesde'] 		= $filter['FechaDesde'];	
			$filterVendido['FechaHasta'] 		= $filter['FechaHasta'];
			$filterVendido['IdModelo'] 			= $oModelo->IdModelo;
			$TotalVendido 	= $oUnidades->GetTotalReporteVendidos($filterVendido);	
			$filterVendido['FechaDesde'] 		= null;	
			$filterVendido['FechaHasta'] 		= null;
			$TotalStock 	= $oUnidades->GetTotalReporteStock($filterVendido);	
			$filterVendido['FechaArriboEstimadaDesde'] 		= $filter['FechaDesde'];	
			$filterVendido['FechaArriboEstimadaHasta'] 		= $filter['FechaHasta'];
			$TotalRecibido 	= $oUnidades->GetTotalReporteRecibido($filterVendido);

			/* almacenamos el registro */
			$arrData[] = array
			(				
				trim($oModelo->DenominacionComercial),			
				trim($TotalVendido->CantidadTotal),
				trim($TotalStock->CantidadTotal),
				trim($TotalRecibido->CantidadTotal)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'reporte quincenal';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}

	/*public function ExportXlsToUpdate(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		/*$oMarcas 			= new Marcas();
		$oTiposModelo 		= new TiposModelo();
		$oCategoriasModelo 	= new CategoriasModelo();

		/* obtenemos el listado de datos a exportar */			
		/*$arrModelos = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		/*$arrData[] = array
		(
			"LISTA DE PRECIO",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			""
		);
		
		$arrData[] = array
		(
			"PREFIJO VIN",
			"CODIGO LISTA",
			"DENOMINACION COMERCIAL",
			"CODIGO VEHICULO MARCA",
			"ID VEHICULO TIPO",
			"ID VEHICULO CATEGORIA",
			"ID TIPO COMBUSTIBLE",
			"VEHICULO MODELO",
			"ANIO",
			"PESO IMPONIBLE",
			"CODIGO MOTOR MARCA",
			"CODIGO CHASIS MARCA",
			"IVA",
			"OTORGAMIENTO",
			"PRENDA",
			"PRECIO PUBLICO",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"MINUTA DE VENTA",
			"",
			"REVENTA",
			"",
			"",
			"",			
			"IMPUESTO INTERNO"
		);

		$arrData[] = array
		(
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"",
			"NETO",
			"TOTAL C/IVA",
			"BONIFICACION COMPRA",
			"BONIFICACION VENTA",
			"BONIFICACION EXTRA",
			"FLETE Y FORM",
			"PATENTAMIENTO",			
			"TOTAL GENERAL",			
			"FLETE Y FORM",
			"PATENTAMIENTO",			
			"BONIFICACION GM",
			"RECUP. BONIF.",
			"DESCUENTO",
			"TOTAL FYF",
			""
		);
				
		foreach ($arrModelos as $oModelo)
		{	
			$oTipoModelo 		= $oTiposModelo->GetById($oModelo->IdTipoModelo);
			$oCategoriaModelo 	= $oCategoriasModelo->GetById($oModelo->IdCategoriaModelo);
			$oMarcaMotor 		= $oMarcas->GetById($oModelo->IdMarcaMotor);
			$oMarcaChasis 		= $oMarcas->GetById($oModelo->IdMarcaChasis);
			$oMarcaVehiculo 	= $oMarcas->GetById($oModelo->IdMarcaVehiculo);

			/* almacenamos el registro */
			/*$arrData[] = array
			(
				trim($oModelo->NumeroVinPrefijo),
				trim($oModelo->CodigoComercial),
				trim($oModelo->DenominacionComercial),
				trim('COD.: ' . $oMarcaVehiculo->Codigo),
				trim($oTipoModelo->IdTipoModelo),
				trim($oCategoriaModelo->IdCategoriaModelo),
				trim($oModelo->IdTipoCombustible),
				trim($oModelo->DenominacionModelo),
				trim($oModelo->Anio),
				trim($oModelo->Peso),
				trim('COD.: ' . $oMarcaMotor->Codigo),
				trim('COD.: ' . $oMarcaChasis->Codigo),
				trim($oModelo->Iva),
				trim($oModelo->Otorgamiento),
				trim($oModelo->Prenda),
				trim($oModelo->PrecioPublicoNeto),
				trim($oModelo->Precio1Iva),
				trim($oModelo->Precio2),
				trim($oModelo->Flete),
				trim($oModelo->BonificacionExtra),
				trim($oModelo->FleteFormularios),
				trim($oModelo->Patentamiento),
				trim($oModelo->Precio1),
				trim($oModelo->FleteFormularios),
				trim($oModelo->Patentamiento),
				trim($oModelo->Precio2),
				trim($oModelo->Precio2 * (1 - $oModelo->RecuperoBonificacion / 100)),
				trim($oModelo->DescuentoReventa),
				trim($oModelo->Ganancia2),
				trim($oModelo->ImpuestoInterno)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		/*$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'modelos';
		
		/* convertimos el array de datos a Excel */
		/*$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		/*$oXlsExport->Download();
			
		return true;	
	}*/

	public function ExportXls(array $filter = NULL)
	{
		/* declaramos variables necesarias */
		$oMarcas 			= new Marcas();
		$oTiposModelo 		= new TiposModelo();
		$oCategoriasModelo 	= new CategoriasModelo();

		/* obtenemos el listado de datos a exportar */			
		$arrModelos = $this->GetAll($filter);
				
		$arrData = array();
		
		/* determinamos el encabezado */
		$arrData[] = array
		(
			"ID MODELO",
			"NRO. VIN PREFIJO",
			"CODIGO LISTA",
			"DENOMINACION COMERCIAL",
			"VEHICULO MARCA",
			"VEHICULO TIPO",
			"VEHICULO CATEGORIA",
			"VEHICULO MODELO",
			"ANIO",
			"PESO IMPONIBLE",
			"MOTOR MARCA",
			"CHASIS MARCA",
			"IVA",
			"OTORGAMIENTO",
			"PRENDA",
			"FABRICA - PRECIO Neto",
			"FABRICA - TOTAL C/IVA",
			"FABRICA - BONIFICACION C/IVA",
			"FABRICA - PRECIO TOTAL",
			"CONSECIONARIA - SUBTOTAL",
			"CONSECIONARIA - FLETE",
			"CONSECIONARIA - BONIFICACION Adicional",
			"LISTA MES - PRECIO TOTAL",
			"LISTA MES - PATENTAMIENTO",
			"LISTA MES - Total General",
			"MINUTA MES - PRECIO",
			"MINUTA MES - FLETE",
			"MINUTA MES - PATENTAMIENTO",
			"REVENTA- PRECIO",
			"REVENTA - BONIFICACION"
		);
				
		foreach ($arrModelos as $oModelo)
		{	
			$oTipoModelo 		= $oTiposModelo->GetById($oModelo->IdTipoModelo);
			$oCategoriaModelo 	= $oCategoriasModelo->GetById($oModelo->IdCategoriaModelo);
			$oMarcaMotor 		= $oMarcas->GetById($oModelo->IdMarcaMotor);
			$oMarcaChasis 		= $oMarcas->GetById($oModelo->IdMarcaChasis);
			$oMarcaVehiculo 	= $oMarcas->GetById($oModelo->IdMarcaVehiculo);

			/* almacenamos el registro */
			$arrData[] = array
			(
				trim($oModelo->IdModelo),
				trim($oModelo->NumeroVinPrefijo),
				trim($oModelo->CodigoComercial),
				trim($oModelo->DenominacionComercial),
				trim($oMarcaVehiculo->Nombre),
				trim($oTipoModelo->Nombre),
				trim($oCategoriaModelo->Nombre),
				trim($oModelo->DenominacionModelo),
				trim($oModelo->Anio),
				trim($oModelo->Peso),
				trim($oMarcaMotor->Nombre),
				trim($oMarcaChasis->Nombre),
				trim($oModelo->Iva),
				trim($oModelo->Otorgamiento),
				trim($oModelo->Prenda),
				trim($oModelo->FabricaPrecioNeto),
				trim($oModelo->FabricaPrecioTotalIva),
				trim($oModelo->FabricaBoinificacion),
				trim($oModelo->FabricaPrecioTotal),
				trim($oModelo->PrecioCompra),
				trim($oModelo->ConsecionariaGastosFlete),
				trim($oModelo->ConsecionariaBonificacion),
				trim($oModelo->MesPrecioTotal),
				trim($oModelo->MesGastosPatentamiento),
				trim($oModelo->Ganancia1),
				trim($oModelo->VentaPrecio),
				trim($oModelo->VentaGastosFlete),
				trim($oModelo->VentaGastosPatentamiento),
				trim($oModelo->Ganancia2),
				trim($oModelo->ReventaBonificacion)
			);
		}		
		
		/* generamos el objeto para exportar el listado a excel*/
		$oXlsExport = new XlsExport();
		$oXlsExport->FileName = 'modelos';
		
		/* convertimos el array de datos a Excel */
		$oXlsExport->WriteArray($arrData); 
		
		/* obligamos a la descarga del archivo */
		$oXlsExport->Download();
			
		return true;	
	}
	
	public function ImportTxt($FileName)
	{
		/* declaramos variables necesarias */
		$FechaImportacion = '';
		
		$oMarcas = new Marcas();
		$oModelosMigracion = new ModelosMigracion();
		$oSeriesMigracion = new SeriesMigracion();
		$oTiposModelo = new TiposModelo();
		$oCategoriasModelo = new CategoriasModelo();
		
		/* processamos el archivo */		 
		$fp = fopen(Unidad::PathFile . $FileName, 'r');
		//$arrData = new Spreadsheet_Excel_Reader(Unidad::PathCsvImportBack . $FileName);
		
		if (!DBAccess::$db->Begin())		
			return false;

		$CountCreate = 0;

		/* procesamos el archivo */
		$Row = 1;
		$count == 0;
		try
		{
		$strError = '';
		while ( !feof($fp) )
		{
			
			$line = fgets($fp, 2048);
			
			$Cliente = str_getcsv($line, '|');
print_r($Cliente);
print_r('<br />');
			
			if ($count != 0)
			{
			
				$err						= 0;			
				$CodigoInterno		 		= trim($Cliente[0]);
				$NroSerie			 		= trim($Cliente[1]);
				$NroVin				 		= trim($Cliente[2]);
				$CodigoMarca		 		= trim($Cliente[3]);
				$CodigoModelo		 		= trim($Cliente[4]);
				$CodigoEstado		 		= trim($Cliente[5]);
				$CodigoCliente		 		= trim($Cliente[6]);
				$CodigoColor		 		= trim($Cliente[7]);
				$Tipo				 		= trim($Cliente[8]);
				$Anio				 		= trim($Cliente[9]);
				$Denominacion		 		= trim($Cliente[10]);
				$NroMotor			 		= trim($Cliente[11]);
				$MarcaMotor			 		= trim($Cliente[12]);
				$NroChasis			 		= trim($Cliente[13]);
				$MarcaChasis			 	= trim($Cliente[14]);
				$Patente				 	= trim($Cliente[15]);
				$CodUbicacion			 	= trim($Cliente[16]);
				
				$oMarca = $oMarcas->GetByCodigo($CodigoMarca);
				$oMarcaMotor = $oMarcas->GetByNombre($MarcaMotor);
				$oMarcaChasis = $oMarcas->GetByNombre($MarcaChasis);
				$oModeloMigracion = $oModelosMigracion->GetByCodigo($CodigoModelo);
				
				$oSerieMigracion = $oSeriesMigracion->GetByCodigo($oModeloMigracion->IdModeloMigracion, substr($NroSerie,0, 5));
				$oTipoModelo = $oTiposModelo->GetByNombre($Tipo);
				$oCategoriaModelo = $oCategoriasModelo->GetByNombre($Tipo);
				
				/*if (!$oModeloMigracion->Denominacion)
				{
					print_r($oModeloMigracion->Denominacion);Exit;
				}*/
				
				if (!(!$oCategoriaModelo && !$oTipoModelo && !$oMarca && !$oMarcaMotor && !$oMarcaMotor && !$oModeloMigracion && $Codigo == '' && $Denominacion == '' && $Iva == ''))
				{
					
					$encontrado = false;
					
					if ($arrModelos = $this->GetByPrefijoVin($NroSerie))
					{
						foreach ($arrModelos as $oModeloAux)
						{
							if ($oModeloAux->NumeroVinPrefijo == $NroSerie && $oModeloAux->DenominacionComercial == $Denominacion)
								$encontrado  = true;
						}
					}
					
					
					if ($err == 0 && !$encontrado)
					{
						$oModelo = new Modelo();
						$oModelo->IdMarcaVehiculo = $oMarca->IdMarca;
						$oModelo->IdMarcaMotor = $oMarcaMotor->IdMarca;
						$oModelo->IdMarcaChasis = $oMarcaChasis->IdMarca;
						$oModelo->NumeroVinPrefijo	= $NroSerie;
						$oModelo->Anio	= $Anio;
						$oModelo->DenominacionModelo 	= $Denominacion;
						$oModelo->DenominacionComercial 	= $Denominacion;
						$oModelo->Iva	= $oSerieMigracion->Iva;
						$oModelo->CodigoComercial	= $oSerieMigracion->Codigo;
						$oModelo->IdTipoModelo = $oTipoModelo->IdTipoModelo;
						$oModelo->IdCategoriaModelo = $oCategoriaModelo->IdCategoriaModelo;
						$oModelo->IdTipoCombustible = 1;
						
							
						if ($oModelo = $this->Create($oModelo))
						{
							$CountCreate++;
							$FechaImportacion = $Fecha;
						}
						
					}
					else
					{
						if ($err & 1)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a el n&uacute;mero de prefijo de Vin es inv&aacute;lido " . $PrefijoVin . ". <br>";
						if ($err & 2)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de Vin es incorrecto. <br>";
						if ($err & 4)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el n&uacute;mero de motor es incorrecto. <br>";
						if ($err & 8)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el color es inv&aacute;lido " . $Color . ". <br>";
						if ($err & 16)
							$strError.= "El registro N&deg; " . $Row . " no se ha podido actualizar debido a que el a&ntilde;o es incorrecto. <br>";				
					}					
					
					$Row++;
				}
			}
			$count++;
			}
			$strError.= "Se importaron " . $CountCreate . " unidades.";
		}
		catch(Exception $e)
		{
		}
		if ($strError != '')
		{
			DBAccess::$db->Rollback();
		}
		else
		{
			DBAccess::$db->Commit();
		}
		
		if ($Creados)
		{
			$strError.= "<br> Se crearon " . $Edit . " unidades.";		
		}		
		
		$res = new stdClass();
		$res->Mensaje = $strError;
		$res->Fecha = $FechaImportacion;
		return $res;
	}
}

?>