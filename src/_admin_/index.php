<?php

require_once('../inc_library.php');

/* destruye la sesion y la vuelve a iniciar, */ 
/* para que queden todos los valores null */
Session::Logout();
Session::Initialize();

$Submit = isset($_REQUEST['Submitted']) ? $_REQUEST['Submitted'] : "";

if ($Submit)
{
	$User = ((isset($_REQUEST['User']) && (!empty($_REQUEST['User'])))) ? $_REQUEST['User'] : "";
	$Pass = ((isset($_REQUEST['Pass']) && (!empty($_REQUEST['Pass'])))) ? $_REQUEST['Pass'] : "";

	if ((trim($User) != "") && (trim($Pass) != ""))
	{
		$retValue = Session::Login($User, $Pass);
		
		if ($retValue === Session::LoginError)
		{
			Session::ForceLogin($User, 'index.php', $retValue);
			exit();
		}
		else
		{	
			header("Location: home.php");
		}
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//Dtd HTML 4.01 transitional//EN" "http://www.w3.org/tr/html4/loose.dtd">
<html>
<head>
<?php include('include/head.inc.php'); ?>
</head>
<body onLoad="javascript:document.frmLogin.User.focus();">

<p>&nbsp;</p>

<form action="" id="frmLogin" name="frmLogin" method="post" target="_top">
	<input type="hidden" name="Submitted" id="Submitted" value="1">
	
	<table width="250" border="0" align="center" cellpadding="5" cellspacing="0" class="bordeGris">
		<tr>
			<td>
				<table width="100%" border=0 align="center" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">
					<tbody>
						<tr bgcolor="#FFFFFF">
							<td align=middle bgcolor="#FFFFFF" class="bordeGris">
								<div align="center">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td><div align="center"><img src="images/logo_compania.jpg" width="160"></div></td>
										</tr>
										<tr>
											<td height="30">
												<table width="100%"  border="0" cellpadding="0" cellspacing="0" id="linea">
													<tr>
														<td height="1" background="images/linea_punteada.gif"><div align="center"></div></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td>
												<table width="100%" border="0" align="center" cellpadding="3" cellspacing="0" style="border-collapse: collapse;">
													<tbody>
														<tr valign="middle">
															<td width="10">&nbsp;</td>
															<td><div align="left"><strong>Usuario:</strong></div></td>
															<td width="10">&nbsp;</td>
														</tr>
														<tr valign="middle">
															<td width="10">&nbsp;</td>
															<td><div align="left"><input type="text" name="User" class="campoLogin" id="User" value="<?=$Usuario?>"></div></td>
															<td width="10">&nbsp;</td>
														</tr>
														<tr valign="middle">
															<td width="10">&nbsp;</td>
															<td><div align="left"><StrONG>Contrase&ntilde;a:</StrONG></div></td>
															<td width="10">&nbsp;</td>
														</tr>
														<tr valign="middle">
															<td width="10">&nbsp;</td>
															<td><div align="left"><input type="password" name="Pass" class="campoLogin" id="Pass" ></div></td>
															<td width="10">&nbsp;</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<td height="2" align=middle><div align="center"></div></td>
						</tr>
					</table>
					<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr valign="middle">
							<td height="30"><div align="right"><input type="submit" class="botonBasico" value="Enviar"></div></td>
							<td width="20">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</form>

<p>&nbsp;</p>

</body>
</html>
