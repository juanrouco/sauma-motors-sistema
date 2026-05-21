
				<form name="frmData" id="frmData" method="post" action="<?=$strParams?>">
					<input type="hidden" name="Submitted" id="Submitted" value="1" />
					<input type="hidden" name="IdMinutaEspera" id="IdMinutaEspera" value="<?=$IdMinutaEspera?>" />
					<input type="hidden" name="IdModelo" id="IdModelo" value="<?=$IdModelo?>" />
					<input type="hidden" name="IdColor" id="IdColor" value="<?=$IdColor?>" />
					<input type="hidden" name="IdColor2" id="IdColor2" value="<?=$IdColor2?>" />
					<input type="hidden" name="IdColor3" id="IdColor3" value="<?=$IdColor3?>" />
					<input type="hidden" name="UsadoIdMarca" id="UsadoIdMarca" value="<?=$UsadoIdMarca?>" />
					<input type="hidden" name="UsadoIdMarca2" id="UsadoIdMarca2" value="<?=$UsadoIdMarca2?>" />
					<input type="hidden" name="UsadoIdColor" id="UsadoIdColor" value="<?=$UsadoIdColor?>" />
					<input type="hidden" name="UsadoIdColor2" id="UsadoIdColor2" value="<?=$UsadoIdColor2?>" />
                    
					<table width="90%"  border="0" align="center" cellpadding="5" cellspacing="0">
						<tr>
							<td class="bordeGris">
								<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos de la Preventa</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                        <td valign="top">
                                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
																<tr>
																	<td>
																		<table border="0" align="left" cellpadding="0" cellspacing="0">
																			<tr>
																				<td><div id="margen" align="left">Veh&iacute;culo Modelo:</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<input type="text" name="VehiculoModelo" id="VehiculoModelo" class="camporFormularioSimple" maxlength="128" value="<?=$VehiculoModelo?>" autocomplete="off" />
																						<script language="">
																						SUGGESTRequest('Modelos', 'GetAll', 'VehiculoModelo', 'FilterModelo', 'IdModelo', 'DenominacionComercial', 'FilterDenominacionComercial', null);
                                                                                    </script>
																					</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
																<tr>
                                                                    <td height="20"><?php if ($err & 1) { ?><li style="color:#FF0000;">Ingrese el prefijo vin</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
																	<td>
																		<table border="0" align="left" cellpadding="0" cellspacing="0">
																			<tr>
																				<td><div id="margen" align="left">Color 1:</div></td>
																				<td><div id="margen" align="left">Cod.</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<input type="text" name="Color" id="Color" class="camporFormularioSuggest" maxlength="128" value="<?=$Color?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
																						<script language="javascript">
																						SUGGESTRequest('Colores', 'GetAll', 'Color', 'FilterColor', 'IdColor', 'Nombre', 'FilterNombre', null);
																						</script>
																					</div>
																				</td>
																				<td>
																					<div align="left">
																						<input type="text" name="ColorCodigo" id="ColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo?>" readonly="readonly" />
																						
																					</div>
																				</td>
																				<td><span style="color:#FF0000;">&nbsp;(*)</span></td>
																			</tr>
																		</table>
																	</td>
																	<td><span style="color:#FF0000;">&nbsp;</span></td>
																</tr>
																<tr>
                                                                    <td height="20"><?php if ($err & 16) { ?><li style="color:#FF0000;">Ingrese el color principal</li><?php } ?></td>
                                                                </tr>
																<tr>
																	<td>
																		<table border="0" align="left" cellpadding="0" cellspacing="0">
																			<tr>
																				<td><div id="margen" align="left">Color 2:</div></td>
																				<td><div id="margen" align="left">Cod.</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<input type="text" name="Color2" id="Color2" class="camporFormularioSuggest" maxlength="128" value="<?=$Color2?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
																						<script language="javascript">
																						SUGGESTRequest('Colores', 'GetAll', 'Color2', 'FilterColor2', 'IdColor', 'Nombre', 'FilterNombre', null);
																						</script>
																					</div>
																				</td>
																				<td>
																					<div align="left">
																						<input type="text" name="ColorCodigo2" id="ColorCodigo2" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo2?>" readonly="readonly" />
																						
																					</div>
																				</td>
																				<td>&nbsp;</td>
																			</tr>
																		</table>
																	</td>
																	<td><span style="color:#FF0000;">&nbsp;</span></td>
																</tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
																<tr>
																	<td>
																		<table border="0" align="left" cellpadding="0" cellspacing="0">
																			<tr>
																				<td><div id="margen" align="left">Color 3:</div></td>
																				<td><div id="margen" align="left">Cod.</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<input type="text" name="Color3" id="Color3" class="camporFormularioSuggest" maxlength="128" value="<?=$Color3?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
																						<script language="javascript">
																						SUGGESTRequest('Colores', 'GetAll', 'Color3', 'FilterColor3', 'IdColor', 'Nombre', 'FilterNombre', null);
																						</script>
																					</div>
																				</td>
																				<td>
																					<div align="left">
																						<input type="text" name="ColorCodigo3" id="ColorCodigo3" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$ColorCodigo3?>" readonly="readonly" />
																						
																					</div>
																				</td>
																				<td>&nbsp;</td>
																			</tr>
																		</table>
																	</td>
																	<td><span style="color:#FF0000;">&nbsp;</span></td>
																</tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Cliente:</div></td>
                                                                                <td><div id="margen" align="left">Id.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Cliente" id="Cliente" class="camporFormularioSuggest" maxlength="128" value="<?=$Cliente?>" onkeyup="javascript: StrToUpper(this.id);" onblur="javascript: VerificarCliente();" autocomplete="Off" />
                                                                                        <script language="javascript">
                                                                                        SUGGESTRequest('Clientes', 'GetAll', 'Cliente', 'FilterCliente', 'IdCliente', 'RazonSocial', 'FilterRazonSocial', null);
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdCliente" id="IdCliente" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdCliente?>" readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td><input type="button" id="btnAddCliente" class="botonBasico"  onClick="javascript:AddCliente();" value=" + " /></td>
                                                                            </tr>
                                                                            <tr id="trModificarCliente" <?= $IdCliente == '' ?  'style="display:none;"' : '' ?>>
                                                                                <td height="20"><a href="#" class="linkMenu" onclick="javascript:ModCliente();">Modificar datos del Cliente</a></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">
																		<?php if ($err & 2) { ?><li style="color:#FF0000;">Ingrese el cliente</li><?php } ?>
																		<?php if ($err & 65536) { ?><li style="color:#FF0000;">El cliente debe tener cargados su telefono y Email.<br>Por favor, carguelos modificandolo.</li><?php } ?>
																	</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Vendedor:</div></td>
                                                                                <td><div id="margen" align="left">Id.</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Usuario" id="Usuario" class="camporFormularioSuggest" maxlength="128" value="<?=$Usuario?>" onkeyup="javascript: StrToUpper(this.id);"  autocomplete="Off" />
                                                                                        <script language="javascript">
                                                                                        var arrParams = new Array();
                                                                                        //arrParams['FilterIdPerfil'] = '<?=Usuario::Vendedor?>';
                                                                                        SUGGESTRequest('Usuarios', 'GetAllSuggest', 'Usuario', 'FilterUsuario', 'IdUsuario', 'Nombre', 'FilterUsuario', arrParams);
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="IdUsuario" id="IdUsuario" class="camporFormularioChicoSuggest" maxlength="5" value="<?=$IdUsuario?>" readonly="readonly" />
                                                                                        
                                                                                    </div>
                                                                                </td>
                                                                                <td>&nbsp;</td>
                                                                                <td><input type="button" id="btnAddUsuario" class="botonBasico"  onClick="javascript:AddUsuario();" value=" + " /></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 4) { ?><li style="color:#FF0000;">Ingrese el vendedor</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Fecha de Minuta:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input name="FechaMinuta" type="text" class="camporFormularioMediano" id="FechaMinuta" value="<?=$FechaMinuta?>" size="12" maxlength="12" />
                                                                                        <script language="javascript">
                                                                                        new tcal({'formname': 'frmData', 'controlname': 'FechaMinuta'});
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 8) { ?>
                                                                    <li style="color:#FF0000;">Ingrese la fecha de la minuta</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Fecha de Vencimiento:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input name="FechaVencimiento" type="text" class="camporFormularioMediano" id="FechaVencimiento" value="<?=$FechaVencimiento?>" size="12" maxlength="12" />
                                                                                        <script language="javascript">
                                                                                        new tcal({'formname': 'frmData', 'controlname': 'FechaVencimiento'});
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Fecha de Estimada Retiro:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input name="FechaRetiro" type="text" class="camporFormularioMediano" id="FechaRetiro" value="<?=$FechaRetiro?>" size="12" maxlength="12" />
                                                                                        <script language="javascript">
                                                                                        new tcal({'formname': 'frmData', 'controlname': 'FechaRetiro'});
                                                                                        </script>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>
                                                                                            <input type="checkbox" name="EntregaUsado" id="EntregaUsado" value="1" onchange="javascript: VerificarEntregaUsado(this.checked);" <?=($EntregaUsado) ? 'checked="checked"' : ''?> />&nbsp;Entrega Usado
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <label>
                                                                                            <input type="checkbox" name="Financiacion" id="Financiacion" value="1" onchange="javascript: VerificarFinanciacion(this.checked);" <?=($Financiacion) ? 'checked="checked"' : ''?> />&nbsp;Requiere Financiaci&oacute;n
                                                                                        </label>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td>&nbsp;</td>
                                                        <td valign="top">
                                                            <table border="0" align="center" cellpadding="0" cellspacing="0">
																<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Precio de Venta:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="Precio" id="Precio" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$Precio?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20"><?php if ($err & 1024) { ?><li style="color:#FF0000;">Ingrese precio de venta</li><?php } ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Gastos Otorgamiento:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosOtorgamiento" id="GastosOtorgamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosOtorgamiento?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Gastos Gestor&iacute;a:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosPatentamiento" id="GastosPatentamiento" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosPatentamiento?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Otros Gastos:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="GastosFlete" id="GastosFlete" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$GastosFlete?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr id="trAcreedor" style="display:none;">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Acreedor:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <select name="IdAcreedor" id="IdAcreedor" class="camporFormularioSimple">
																							<option value="">Seleccione el Acreedor</option>
																							<?php
																							foreach ($arrAcreedores as $oAcreedor)
																							{
																								$selected = '';
																								if ($oAcreedor->IdAcreedor == $IdAcreedor)
																									$selected = 'selected="selected"';
																							?>
																							<option value="<?= $oAcreedor->IdAcreedor ?>" <?= $selected ?>><?= $oAcreedor->RazonSocial ?></option>
																							<?php
																							}
																							?>
																						</select>
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trAcreedorError" style="display: none">
                                                                    <td height="20"><?php if ($err & 16384) { ?><li style="color:#FF0000;">Seleccione el acreedor prendario</li><?php } ?></td>
                                                                </tr>
																<tr id="trFinanciacionCapital" style="display:none;">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Capital a Financiar:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="FinanciacionCapital" id="FinanciacionCapital" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCapital?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;(*)</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>                                                               
                                                                <tr id="trFinanciacionCapitalError" style="display: none">
                                                                    <td height="20"><?php if ($err & 32) { ?><li style="color:#FF0000;">Ingrese el capital a financiar</li><?php } ?></td>
                                                                </tr>															
																<tr id="trPlazoPrenda" style="display: none">
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Plazo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="FinanciacionCuotas" id="FinanciacionCuotas" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$FinanciacionCuotas?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr> 					
																<tr id="trPlazoPrendaError" style="display: none">
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                                <tr id="trQuebranto" style="display: none">
                                                                    <td>
                                                                        <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td colspan="2" align="left"><a id="calcular-cuotas" href="#">[Calcular cuotas] </a></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td colspan="2" id="cuotas-container"></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                <tr id="trQuebrantoError" style="display: none">
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
																<?php /*<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Se&ntilde;a:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="DepositoGarantia" id="DepositoGarantia" class="camporFormularioSimple" maxlength="10" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$DepositoGarantia?>" />
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>*/ ?>
																<tr>
                                                                    <td>
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Observaciones:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <textarea name="Observaciones" id="Observaciones" class="camporFormularioSimple" style="height: 75px"><?=$Observaciones?></textarea>
                                                                                    </div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
																<tr>
                                                                    <td height="20">&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                            	</table>
                                           	</div>
                                        </td>
                                    </tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr id="trDatosUsadoTitulo">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
                                                    <tr>
                                                        <td height="40" align="center"><span class="tituloPagina">Datos del Usado</span></td>
                                                    </tr>
                                                </table>
                                           	</div>
                                        </td>
                                    </tr>                                    
                                    <tr id="trDatosUsado">
                                    	<td>
                                        	<div align="center">
                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGris">
                                                    <tr>
                                                    	<td>&nbsp;</td>
                                                    </tr>                                          
                                                    <tr>
                                                    	<td valign="top">
                                                        	<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <?php
																			if ($MostrarEliminarUsado)
																			{
																			?>
																			<tr>
                                                                                <td>
                                                                                    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td width="10">
                                                                                                <div align="left">
                                                                                                    <input type="checkbox" name="EliminarUsado1" id="EliminarUsado1" value="1" <?= $EliminarUsado1 == 1 ? 'checked="checked"' : '' ?> />
                                                                                                </div>
																							</td>
																							<td>
																								<div align="left">
																									Eliminar Usado
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"></td>
                                                                            </tr>
																			<?php
																			}
																			?>
																			<tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Marca:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoMarca" id="UsadoMarca" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoMarca?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'UsadoMarca', 'FilterUsadoMarca', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadocolorCodigo" id="UsadoMarcaCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoMarcaCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td><input type="button" id="btnAddColor" class="botonBasico" onClick="javascript:AddMarca('Usado');" value=" + " /></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 64) { ?><li style="color:#FF0000;">Ingrese la marca</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Modelo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoModelo" id="UsadoModelo" class="camporFormularioSimple" maxlength="255" value="<?=$UsadoModelo?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 128) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Color:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoColor" id="UsadoColor" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoColor?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Colores', 'GetAll', 'UsadoColor', 'FilterUsadoColor', 'IdColor', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadocolorCodigo" id="UsadoColorCodigo" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoColorCodigo?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                            <td><input type="button" id="btnAddColor" class="botonBasico" onClick="javascript:AddColor('Usado');" value=" + " /></td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
																			<tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Dominio:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoDominio" id="UsadoDominio" class="camporFormularioSimple" maxlength="6" value="<?=$UsadoDominio?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <?php
																			if ($MostrarEliminarUsado)
																			{
																			?>
																			<tr>
                                                                                <td height="18">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
																			<?php
																			}
																			?>
																			<tr>
                                                                                <td><div id="margen" align="left">A&ntilde;o:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <select name="UsadoModeloAnio" id="UsadoModeloAnio" class="camporFormularioSimple">
                                                                                            <option value="">[SELECCIONE]</option>
                                                                                            <?php $year = date('Y'); ?>
                                                                                            <?php for ($i=$year-15; $i<=$year; $i++) { ?>
                                                                                            <option value="<?=$i?>" <?=($UsadoModeloAnio == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                 	</div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 256) { ?><li style="color:#FF0000;">Seleccione el a&ntilde;o</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Kilometraje:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoKilometraje" id="UsadoKilometraje" class="camporFormularioSimple" maxlength="12" value="<?=$UsadoKilometraje?>" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Importe:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoValuacion" id="UsadoValuacion" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoValuacion?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 512) { ?><li style="color:#FF0000;">Ingrese el importe del usado</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Info:</div></td>
                                                                            </tr>
																			<tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoInfo" id="UsadoInfo" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoInfo?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
																			<tr>
																				<td><div id="margen" align="left">Arreglos:</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<input type="text" name="UsadoArreglos" id="UsadoArreglos" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoArreglos?>"  />
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td height="20">&nbsp;</td>
																			</tr>
																			<tr>
																				<td><div id="margen" align="left">Observaciones:</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<textarea name="UsadoObservaciones" id="UsadoObservaciones" class="camporFormularioSimple" style="height: 75px"><?=$UsadoObservaciones?></textarea>
																					</div>
																				</td>
																			</tr>

                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
														</td>
													</tr>
													<tr>
														<td>&nbsp;</td>
													</tr>
													<tr class="bordeGrisFondo">
														<td height="40" align="center"><span class="tituloPagina">Segundo Usado</span></td>
													</tr>
													<tr>
														<td>&nbsp;</td>
													</tr>
													<tr>
														<td>
															<table border="0" align="center" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <?php
																			if ($MostrarEliminarUsado)
																			{
																			?>
																			<tr>
                                                                                <td>
                                                                                    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td width="10">
                                                                                                <div align="left">
                                                                                                    <input type="checkbox" name="EliminarUsado2" id="EliminarUsado2" value="1" <?= $EliminarUsado2 == 1 ? 'checked="checked"' : '' ?> />
                                                                                                </div>
																							</td>
																							<td>
																								<div align="left">
																									Eliminar Usado
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"></td>
                                                                            </tr>
																			<?php
																			}
																			?>
																			<tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Marca:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoMarca2" id="UsadoMarca2" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoMarca2?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Marcas', 'GetAll', 'UsadoMarca2', 'FilterUsadoMarca2', 'IdMarca', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoMarcaCodigo2" id="UsadoMarcaCodigo2" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoMarcaCodigo2?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Modelo:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoModelo2" id="UsadoModelo2" class="camporFormularioSimple" maxlength="255" value="<?=$UsadoModelo2?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 2048) { ?><li style="color:#FF0000;">Ingrese el modelo</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Color:</div></td>
                                                                                            <td><div id="margen" align="left">Cod.</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoColor2" id="UsadoColor2" class="camporFormularioSuggest" maxlength="128" value="<?=$UsadoColor2?>" onkeyup="javascript: StrToUpper(this.id);" autocomplete="off" />
                                                                                                    <script language="javascript">
                                                                                                    SUGGESTRequest('Colores', 'GetAll', 'UsadoColor2', 'FilterUsadoColor2', 'IdColor', 'Nombre', 'FilterNombre', null);
                                                                                                    </script>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoColorCodigo2" id="UsadoColorCodigo2" class="camporFormularioChicoSuggest" maxlength="3" value="<?=$UsadoColorCodigo2?>" readonly="readonly" />
                                                                                                    
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>&nbsp;</td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
																			<tr>
                                                                                <td>
                                                                                    <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                                        <tr>
                                                                                            <td><div id="margen" align="left">Dominio:</div></td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div align="left">
                                                                                                    <input type="text" name="UsadoDominio2" id="UsadoDominio2" class="camporFormularioSimple" maxlength="6" value="<?=$UsadoDominio2?>" onkeyup="javascript: StrToUpper(this.id);" />
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    </table>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                    <td>&nbsp;</td>
                                                                    <td valign="top">
                                                                        <table border="0" align="left" cellpadding="0" cellspacing="0">
                                                                            <?php
																			if ($MostrarEliminarUsado)
																			{
																			?>
																			<tr>
                                                                                <td height="18">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
																			<?php
																			}
																			?>
																			<tr>
                                                                                <td><div id="margen" align="left">A&ntilde;o:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <select name="UsadoModeloAnio2" id="UsadoModeloAnio2" class="camporFormularioSimple">
                                                                                            <option value="">[SELECCIONE]</option>
                                                                                            <?php $year = date('Y'); ?>
                                                                                            <?php for ($i=$year-15; $i<=$year; $i++) { ?>
                                                                                            <option value="<?=$i?>" <?=($UsadoModeloAnio2 == $i) ? 'selected="selected"' : '';?>><?=$i?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                 	</div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 4096) { ?><li style="color:#FF0000;">Seleccione el a&ntilde;o</li><?php } ?></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Kilometraje:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoKilometraje2" id="UsadoKilometraje2" class="camporFormularioSimple" maxlength="12" value="<?=$UsadoKilometraje2?>" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Importe:</div></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoValuacion2" id="UsadoValuacion2" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoValuacion2?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20"><?php if ($err & 8192) { ?><li style="color:#FF0000;">Ingrese el importe del usado</li><?php } ?></td>
                                                                            </tr>
																			
                                                                            <tr>
                                                                                <td><div id="margen" align="left">Info:</div></td>
                                                                            </tr>
																			<tr>
                                                                                <td>
                                                                                    <div align="left">
                                                                                        <input type="text" name="UsadoInfo2" id="UsadoInfo2" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoInfo2?>" />
                                                                                 	</div>
                                                                                </td>
                                                                                <td><span style="color:#FF0000;">&nbsp;</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td height="20">&nbsp;</td>
                                                                            </tr>
																			<tr>
																				<td><div id="margen" align="left">Arreglos:</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<input type="text" name="UsadoArreglos2" id="UsadoArreglos2" class="camporFormularioSimple" maxlength="12" onKeyPress="javascript: if (event.keyCode &lt; 45 || event.keyCode &gt; 57) event.returnValue = false;" value="<?=$UsadoArreglos2?>"  />
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td height="20">&nbsp;</td>
																			</tr>
																			<tr>
																				<td><div id="margen" align="left">Observaciones:</div></td>
																			</tr>
																			<tr>
																				<td>
																					<div align="left">
																						<textarea name="UsadoObservaciones2" id="UsadoObservaciones2" class="camporFormularioSimple" style="height: 75px"><?=$UsadoObservaciones2?></textarea>
																					</div>
																				</td>
																			</tr>

                                                                        </table>
                                                                    </td>
                                                                </tr>
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
										<td>&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td><div align="center"></div></td>
						</tr>
					</table>
					<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0" class="bordeGrisFondo">
						<tr>
							<td height="30">
								<div align="center">
									<input type="submit" name="btnAceptar" class="botonBasico" id="btnAceptar" value="Aceptar" />
									<input type="button" name="btnCancelar" class="botonBasico" id="btnCancelar" onclick="javascript: window.location.href = 'minutasespera.php<?=$strParams?>';" value="Cancelar" />
								</div>
							</td>
						</tr>
					</table>
				</form>