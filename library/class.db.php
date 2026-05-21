<?php 

require_once('class.dataprovider.php');
require_once('class.modules.php');

class DB 
{
	private $oConn;
	public $lastSQL;
	
	public function Open($host, $user, $password, $db_name, $port)
	{
		//$this->oConn = mysql_connect($host, $user, $password, NULL, $port);
		if ($port != NULL)
		{
			$host.= ":" . $port;
		}
		
		$this->oConn = mysql_connect($host, $user, $password);
		if (!$this->oConn)
			return false;

		if (!mysql_select_db($db_name, $this->oConn)){		
			$this->oConn = false;
			return false;
		}

		return true;
	}
	
	public function Close()
	{
		if ($this->oConn == false)
			return false;
			
		$this->oConn = mysql_close($this-oConn);
		$this->oConn = false;
		
		return true;
	}
	
	public function Begin()
	{
		if (!$this->oConn)
			return false;

		//$this->oConn->autocommit(FALSE);
		
		return true;
	}
	
	public function Commit()
	{
		if (!$this->oConn)
			return false;
			
		//$this->oConn->commit();
		//$this->oConn->autocommit(TRUE);
		
		return true;
	}
	
	public function Rollback()
	{
		if (!$this->oConn)
			return false;

		//$this->oConn->rollback();
		//$this->oConn->autocommit(TRUE);
		
		return true;
	}
	
	public function GetQuery($sql)
	{
		if (!$this->oConn)
			return false;

		/*if (defined('DEBUG_SQL_COMMANDS') && DEBUG_SQL_COMMANDS == 1)
			print $sql;
		
		$this->lastSQL = $sql;
		
		$obj = $this->oConn->query($sql);
		if (!is_object($obj))
		{
			if ($obj)
			{
				return true;
			}
			else
			{
				if (defined('DEBUG_SQL_ERRORS') && DEBUG_SQL_ERRORS == 1)
					print $this->GetLastError();
					
				return false;
			}
		}*/
		
		$obj = mysql_query($sql) or die(mysql_error());
		
		return new DBResource($obj);
	}
	
	public function ExecQuery($sql)
	{
		if (!$this->GetQuery($sql))
			return false;

		return true;
	}
	
	public function Insert($table, $arr)
	{
		$f = '';
		$v = '';
		foreach ($arr as $name => $value)
		{
			$f.= "$name, ";
			$v.= "$value, ";
		}
		$f = substr($f, 0, strlen($f) - 2);
		$v = substr($v, 0, strlen($v) - 2);

		if (!$this->ExecQuery("INSERT INTO $table ($f) VALUES ($v)"))
			return false;
				
		$id = $this->GetLastInsertID();
		if ($id)	
			return $id;

		if (!$this->GetLastError())
			return true;

		return false;
	}
	
	public function Update($table, $arr, $where)
	{
		$f = '';
		foreach ($arr as $name => $value)
		{
			$f.= "$name = $value, ";
		}
		$f = substr($f, 0, strlen($f) - 2);

		if (!$this->ExecQuery("UPDATE $table SET $f WHERE $where"))
			return false;

		return true;
	}
	
	public function Delete($table, $where)
	{
		if (!$this->ExecQuery("DELETE FROM $table WHERE $where"))
			return false;
		
		return true;
	}
	
	public function GetLastInsertId()
	{
		if (!$this->oConn)
			return false;
			
		return mysql_insert_id();
	}
	
	public function GetLastError()
	{
		if (!$this->oConn)
			return 'Unknown error';

		return mysql_error();
	}
	
	static public function String($string)
	{		
		//return "'".addslashes(toHTML($string))."'";
		return "'".addslashes($string)."'";
	}
	
	static public function StringUnquoted($string)
	{
		return addslashes($string);
	}
	
	static public function Number($sNumber)
	{		
		return (is_numeric($sNumber) ? $sNumber : 'NULL');		
	}
	
	static public function Date($date)
	{
        if (($ret = DB::parseDate($date, '%Y-%m-%d %H:%M:%s')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d/%m/%Y %H:%M:%s')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d/%m/%Y %H:%M')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d/%m/%Y %H')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d/%m/%Y')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%Y-%m-%d %H:%M:%s')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d-%m-%Y %H:%M:%s')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d-%m-%Y %H:%M')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d-%m-%Y %H')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%d-%m-%Y')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%Y-%m-%d %H:%M')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%Y-%m-%d %H')) != false)
                return "'$ret'";

        if (($ret = DB::parseDate($date, '%Y-%m-%d')) != false)
                return "'$ret'";

        return 'NULL';
	}

	
	static public function Bool($sBool)
	{
		if ($sBool == true || $sBool == 1)
			return '1';
		return '0';
	}

	static public function parseDate( $date, $format ) 
	{
		// Builds up date pattern from the given $format, keeping delimiters in place.
		if( !preg_match_all( "/%([YmdHMsu])([^%])*/", $format, $formatTokens, PREG_SET_ORDER ) ) 
			return false;
	
		foreach( $formatTokens as $formatToken ) 
		{
			$delimiter = preg_quote( $formatToken[2], "/" );
			if($formatToken[1] == 'Y') 
			{
				$datePattern .= '(.{1,4})'.$delimiter;
			} 
			elseif($formatToken[1] == 'u') 
			{
				$datePattern .= '(.{1,5})'.$delimiter;
			} 
			else 
			{
				$datePattern .= '(.{1,2})'.$delimiter;
			}
		}
		
		// Splits up the given $date
		if( !preg_match( "/^".$datePattern."/", $date, $dateTokens) ) 
		{
			return false;
		}
	  
		$dateSegments = array();
		for($i = 0; $i < count($formatTokens); $i++) 
		{
			$dateSegments[$formatTokens[$i][1]] = $dateTokens[$i+1];
		}
	
		// Reformats the given $date into rfc3339
		if( $dateSegments["Y"] && $dateSegments["m"] && $dateSegments["d"] ) 
		{
			if( ! checkdate ( $dateSegments["m"], $dateSegments["d"], $dateSegments["Y"] )) 
			{ 
				return false; 
			}
			
			$dateReformated =
			str_pad($dateSegments["Y"], 4, '0', STR_PAD_LEFT)
			."-".str_pad($dateSegments["m"], 2, '0', STR_PAD_LEFT)
			."-".str_pad($dateSegments["d"], 2, '0', STR_PAD_LEFT);
		} 
		else 
		{
			return false;
		}
	  
		if( $dateSegments["H"] && $dateSegments["M"] ) 
		{
			$dateReformated .=
			" ".str_pad($dateSegments["H"], 2, '0', STR_PAD_LEFT)
			.':'.str_pad($dateSegments["M"], 2, '0', STR_PAD_LEFT);
	
			if( $dateSegments["s"] ) 
			{
				$dateReformated .=
				":".str_pad($dateSegments["s"], 2, '0', STR_PAD_LEFT);
				if( $dateSegments["u"] ) 
				{
					$dateReformated .=
					'.'.str_pad($dateSegments["u"], 5, '0', STR_PAD_RIGHT);
				}
			}
		}
	
		return $dateReformated;
	}
}

class DBColumn implements IDataProviderColumn
{
	private $Name;
	
	function DBColumn($dbcol)
	{
		$this->Name = $dbcol->name;
	}

	function GetName()
	{
		return $this->Name;
	}

	function GetType()
	{
	}

	function GetLength()
	{
	}

	function GetDecimal()
	{
	}

}

class DBResource implements IDataProvider
{
	private $oRS;
	private $row;
	
	function DBResource($oRS)
	{
		$this->oRS = $oRS;
		$this->row = false;
		$this->MoveFirst();	
	}

	public function GetCols()
	{
		$oCols = array();
		if (!$this->oRS)
			return false;

		foreach ($this->oRS->fetch_fields() as $dbcol)
		{
			$oCol = new DBColumn($dbcol);
			$oCols[] = $oCol;
		}
		return $oCols;	
	}
	
	public function GetRow()
	{
		return $this->row;
	}

	public function MoveFirst()
	{
		$this->Seek(0);
	}

	public function Seek($recordnum)
	{
		if (!$this->oRS)
			return false;
		
		if ($recordnum != 0)
			mysql_data_seek($this->oRS, $recordnum);
		
		if ($this->NumRows() != 0)
			$this->row = mysql_fetch_assoc($this->oRS);
			
		return true;
	}
	
	public function MoveNext()
	{
		if (!$this->oRS)
			return false;
		$this->row = mysql_fetch_assoc($this->oRS);
	}

	public function NumRows()
	{
		return (int)@mysql_num_rows($this->oRS);
	}

	public function Close()
	{
		if (!$this->oRS)
			return false;
			
		$this->oRS = mysql_close();
		
		return true;
	}
	
	public function GetRowsCount()
	{
		return -1;
	}	
}

?>