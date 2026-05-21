<?php
require_once('../inc_library.php'); 

$oClientes = new Clientes();

$arrClientes = $oClientes->GetAll();

foreach ($arrClientes as $oCliente)
{
	$oClientes->ActualizarPercepciones($oCliente);
}

?>
ok