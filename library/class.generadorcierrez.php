<?php
require_once("WSpooler/WSpooler.php");

class GeneradorCierreZ
{
	protected $Host;
	protected $Port;
	protected $Separador;
	protected $WSpooler;	
	protected $Compra;
	protected $Ret;
	
	public function __construct()
	{
		$this->Host = '192.168.1.36';
		$this->Port = 1000;
		$this->Separador = chr(10);
		$this->WSpooler = new CWSpooler();
	}
		
	public function RealizarCierreZ()
	{
		$this->Ret = $this->WSpooler->if_open($this->Host, $this->Port);
		
		try
		{
			$comando = "@DailyClose|Z" . $this->Separador;
			$this->Ret = $this->WSpooler->if_write($comando);
		}
		catch(Exception $ex)
		{
		}	
		$this->WSpooler->if_close();		
	}
}

?>