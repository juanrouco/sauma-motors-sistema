<?php

interface IDataProvider
{
	function MoveFirst();
	function MoveNext();
	function GetCols();
	function GetRow();
	function Seek($RecordNum);
	function GetRowsCount();
}

interface IDataProviderColumn
{
	function GetName();
	function GetType();
	function GetLength();
	function GetDecimal();
}

?>