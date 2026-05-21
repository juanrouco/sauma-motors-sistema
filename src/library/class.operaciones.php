<?php

abstract class Operaciones
{ 		
	const Create 		= 1;
	const Update 		= 2;
	const Delete		= 3;
	const ListAll		= 4;
	const GetRow		= 5;
	const Activate		= 6;
	const Disactivate	= 7;
	const Mail			= 8;

	static function GetById($IdOperacion)
	{
		switch($IdOperacion)
		{
			case self::Create:
				return "Create";
				
			case self::Update:
				return "Update";

			case self::Delete:
				return "Delete";

			case self::ListAll:
				return "List All";

			case self::GetRow:
				return "Get Row";
				
			case self::Activate:
				return "Activate";
				
			case self::Disactivate:
				return "Disactivate";

			case self::Mail:
				return "Mail";

			default:
				return "No Asignado";
		}
	}
	
	
	static function PrintResult($IdOperacion, $Status)
	{
		$DivStyle 	= '';
		$Image 		= '';
		$Text		= '';
		
		if ($IdOperacion != '') 
		{			
			switch ($IdOperacion)
			{
				case self::Create:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "Registro dado de alta correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al procesar el alta del registro. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
	
				case self::Update:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "Registro modificado correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al procesar la modificaci&oacute;n del registro. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
	
				case self::Delete:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "Registro dado de baja correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al procesar la baja del registro. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
	
				case self::ListAll:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "Registros listados correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al obtener el listado de registros. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
	
				case self::GetRow:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "Registro obtenido correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al obtener los datos del registro. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
					
				case self::Activate:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "Registro activado correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al procesar la activaci&oacute;n del registro. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
					
				case self::Disactivate:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "Registro desactivado correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al procesar la desactivaci&oacute;n del registro. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
	
				case self::Mail:
					if ($Status)
					{
						$DivStyle = "border: 2px solid #00CC00; padding: 5px; background:#CCFFCC;";
						$Image = "images/iconos/check.gif";
						$Text = "El mail se ha enviado correctamente.";
					}
					else
					{
						$DivStyle = "border: 2px solid #FF0000; padding: 5px; background:#FFCC99;";
						$Image = "images/iconos/permisos.gif";
						$Text = "Error al enviar el mail. Intente nuevamente o comun&iacute;quese con el administrador del sitio.";
					}
					break;
	
				default:
					break;
			}

			print '<tr id="trOperationResult">';
			print '	<td>';
			print '	 <table width="100%" border="0" cellpadding="0" cellspacing="0" style="' . $DivStyle . '">';
			print '	  <tr>';
			print '	   <td>';
			print '		<img src="' . $Image . '" border="0" /><strong style="padding-left: 10px">';
			print '		<label id="lblOperationResult">' . $Text . '</label></strong>';
			print '	   </td>';
			print '	   <td align="right">';
			print '		<a href="#m" class="linkBasico" onclick="javascript: HideResult();">[Ocultar Detalle]</a>';
			print '	   </td>';
			print '	  </tr>';
			print '	 </table>';
			print '	</td>';
			print '</tr>';
			
			?>
			<script language=javascript>

			function HideResult()
			{
				HideSection('trOperationResult');
				
				return true;
			}

			function ShowResult()
			{
				ShowSection('trOperationResult');
				
				setTimeout("HideSection('trOperationResult');", 3000);
				
				return true;
			}
			
			ShowResult();
			
			</script>
			<?php
		}
	}
}

?>