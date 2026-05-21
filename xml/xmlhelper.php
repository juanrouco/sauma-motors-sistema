<?php

error_reporting(E_ERROR | E_PARSE);
require_once('../library/class.modules.php');
require_once('../library/class.session.php');

Session::Initialize();

$ModuleName		= $_REQUEST['module'];
$CommandName	= $_REQUEST['command'];

// we don't want sql errors and sql statements because
// are incompatible with XML encapsulation
define('DEBUG_SQL_ERRORS', 		0);
define('DEBUG_SQL_COMMANDS', 	0);

if ($_REQUEST['html'] == '1')	header('Content-type: text/html');
else							header('Content-type: text/xml');
header("Cache-control: no-store, no-cache, must-revalidate");
header("Expires: -1");
header("Pragma: no-cache");

if ($_REQUEST['html'] != '1') print '<?xml version="1.0" encoding="iso-8859-1"?>';

/*
if (!Session::GetCurrentUser())
{
	print '<Request>';
	print '<Status>';
	print '<Id>2</Id>';
	print '<Description>Usuario no logueado</Description>';
	print '</Status>';
	print '</Request>';
	exit;
}
*/

$Module = Modules::LoadModule($ModuleName);

// if the module couldn't be loaded, send an XML error
if (!$Module)
{
	print '<Request>';
	print '<Status>';
	print '<Id>2</Id>';
	print '<Description>The module ' . $ModuleName . ' couldn\'t be loaded</Description>';
	print '</Status>';
	print '</Request>';
	exit;
}

// if the command doesn't exists, send an XML error
if (!method_exists($Module, (string)$CommandName))
{
	print '<Request>';
	print '<Status>';
	print '<Id>2</Id>';
	print '<Description>The command ' . $CommandName . ' on module ' . $ModuleName . ' doesn\'t exists</Description>';
	print '</Status>';
	print '</Request>';
	exit;
}

// collect the params
$Params = array();
foreach($_REQUEST as $ParamName => $ParamValue)
{
	if ($ParamName == 'module')
		continue;
	if ($ParamName == 'command')
		continue;
	$Params[$ParamName] = $ParamValue;
}


function errhandler($errno, $errstr, $errfile, $errline)
{
	print $errstr;
	print $errstr;
	print $errstr;
	print $errstr;
	
	return true;
}

try
{
	$Response = $Module->$CommandName($Params);

	// check if the response was negative
	if ($Response === false)
	{
		print '<Request>';
		print '<Status>';
		print '<Id>3</Id>';
		print '<Description>Command ' . $CommandName . ' on module ' . $ModuleName . ' abnormally terminated</Description>';
		print '</Status>';
		print '<Error>' . htmlentities(DBAccess::GetLastError()) . '</Error>';
		print '</Request>';
		exit;
	}

}
catch (Exception $e)
{
	print '<Request>';
	print '<Status>';
	print '<Id>4</Id>';
	print '<Description>Command ' . $CommandName . ' on module ' . $ModuleName . ' abnormally terminated</Description>';
	print '</Status>';
	print '<Error>' . htmlentities($e->getMessage()) . '</Error>';
	print '</Request>';
	exit;
}


// send the response
print '<Request>';
print '<Status><Id>0</Id><Description>Command successfully executed</Description></Status>';
print '<Response>';
if (is_array($Response))
	print ProcessArray($Response);
elseif (is_object($Response))
	print ProcessObject($Response);
elseif (is_scalar($Response))
	print ProcessScalar($Response);
print '</Response>';
print '</Request>';

?>
