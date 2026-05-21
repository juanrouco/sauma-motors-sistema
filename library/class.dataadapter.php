<?php

require_once('class.dataprovider.php');

class DataAdapterColumn implements IDataProviderColumn
{
	private $Name;
	
	public function __construct($Name)
	{
		$this->Name = $Name;	
	}
	
	public function GetName()
	{
		return $this->Name;
	}
	
	public function GetType()
	{
		return false;
	}
	
	public function GetLength()
	{
		return false;
	}
	
	public function GetDecimal()
	{
		return false;
	}
	
}

class DataAdapter implements IDataProvider
{
	private $data;
	private $current;
	private $cols;
	
	public function __construct($arr)
	{
		if (is_array($arr))
			$this->data = array_values($arr);
		else
			$this->data = array();
			
		$this->cols = array();
	}
	
	public function MoveFirst()
	{
		$this->current = 0;
	}
	
	public function MoveNext()
	{
		if ($this->current >= count($this->data))
			return false;
		$this->current++;
	}
	
	public function GetCols()
	{
		if (is_object($this->data[0]))
			$cols = array_keys(get_object_vars($this->data[0]));
		else if (is_array($this->data[0]))
			$cols = array_keys($this->data[0]);
		else
			return false;
		
		$arr = array();	
		foreach ($cols as $col)
			array_push($arr, new DataAdapterColumn($col));

		return $arr;
	}
	
	public function GetRow()
	{
		if (is_object($this->data[$this->current]))
			return get_object_vars($this->data[$this->current]);
			
		if (is_array($this->data[$this->current]))
		{
			$arr = array();
			foreach ($this->GetCols() as $col)
			{
				$value = each($this->data[$this->current]);
				$arr[$col->GetName()] = $value['value'];
			}
			return $arr;
		}
	}
	
	public function Seek($RecordNum)
	{
		if ($RecordNum < 0 || $RecordNum > $this->GetRowsCount())
			return false;
			
		$this->current = $RecordNum;
	}
	
	public function GetRowsCount()
	{
		return count($this->data);
	}
}

?>