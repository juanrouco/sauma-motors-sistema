<?php

define('DEBUG_SQL_COMMANDS', 0);
define('DEBUG_SQL_ERRORS', 0);

require_once('class.db.php');
require_once('class.config.php');

class DBAccess
{
	static $db = undefined;

	public function __construct()
	{
		if (DBAccess::$db == undefined)
		{
			DBAccess::$db = new DB();
			
			DBAccess::$db->Open
			(
				Config::Database_Host, 
				Config::Database_User, 
				Config::Database_Pass, 
				Config::Database_Name, 
				Config::Database_Port
			);
		}
	}

	public function GetQuery($Query)
	{
		return DBAccess::$db->GetQuery($Query);
	}

	public function ExecQuery($Query)
	{
		return DBAccess::$db->ExecQuery($Query);
	}
	
	public function Insert($table, $arr)
	{
		return DBAccess::$db->Insert($table, $arr);
	}
	
	public function Update($table, $arr, $where)
	{
		return DBAccess::$db->Update($table, $arr, $where);
	}
	
	public function Delete($table, $where)
	{
		return DBAccess::$db->Delete($table, $where);
	}

	public function GetLastInsertId()
	{
		return DBAccess::$db->GetLastInsertId();
	}

	static public function GetLastError()
	{
		if (!DBAccess::$db)
			return 'No connection';
			
		return DBAccess::$db->GetLastError();
	}
	
	static public function GetLastSQL()
	{
		if (!DBAccess::$db)
			return '';
			
		return DBAccess::$db->lastSQL;
	}

	public function Begin()
	{
		return DBAccess::$db->Begin();
	}

	public function Commit()
	{
		return DBAccess::$db->Commit();
	}

	public function Rollback()
	{
		return DBAccess::$db->Rollback();
	}
}

?>