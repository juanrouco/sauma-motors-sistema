<?php

class Config
{
	/*------------------------ Configuracion General -----------------------*/
	const SumaUsados = 0;
	const Flete = 281;
	const Seguro = 0.001;
	const Formularios = 280;
	const PercepcionIIBB = 0.035;
	const RetencionIVA = 0.03;
	const FleteFormulario = 2900;
	const GestoriaLibertador = 0;
	const GestoriaOtro = 0;
	const Comision0Km = 1;
	const ComisionUsados = 1;
	/* Nombre de la empresa */
	const NombreEmpresa = 'Sistema Consecionaria';									
	/* Url sin ningun subdirectorio del sitio */
	const UrlSitio = 'http://192.168.1.100';			
	/* Url con el directorio del sitio en ingles */
	const UrlSitioEnglish = 'http://192.168.1.100/_admin_/';	
	/* Url con el directorio del sitio en espa�ol */
	const UrlSitioEspanol = 'http://192.168.1.100/_admin_/';	
	/* Directorio de la imagen que ir� por default cuando no haya una imagen cargada */	
	const ImagenDefault	= 'images/no_foto.jpg';								
	/* Correo electronico del administrador */
	const CorreoAdministrador = 'info@grupotolosa.com.ar';								
	/* Direccion de correo no-reply */
	const MailNoReply = 'no-reply@grupotolosa.com.ar';						

	/*------------------------ Fin Configuracion General -----------------------*/

	/* -------------------------------------------------------------------------------------------- */
	/* -------------------------------------------------------------------------------------------- */


	/*------------------------ Configuracion Base de Datos -----------------------*/
	
	/* Host para la conexi�n a la base de datos */
	const Database_Host	= 'db';
	/* Nombre de usuario para conectarse a la base de datos */	
	const Database_User	= 'root';	
	/* Contrase�a para conectarse a la base de datos */		
	const Database_Pass	= '';		
	/* Nombre de la base de datos */	
	const Database_Name	= 'benelli_com_ar';
	/* Puerto de la base de datos, si se desconoce el puerto, poner NULL (sin comillas) */	
	const Database_Port	= NULL;			
	
	/*------------------------ Fin Configuracion Base de Datos -----------------------*/

	/* -------------------------------------------------------------------------------------------- */
	/* -------------------------------------------------------------------------------------------- */
}

?>