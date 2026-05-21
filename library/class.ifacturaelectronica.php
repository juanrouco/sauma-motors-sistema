<?php

interface IFacturaElectronica
{
	function SetNumeroComprobante($NumeroComprobante);
	function SetFechaComprobante($FechaComprobante);
	function ActualizarFactura();
	function ObtenerComprobante();
	function ObtenerComprobanteAfipAsociado();
}

?>