<!DOCTYPE html>
<html>
<head>
<link href='../css/basico_backend.css' rel='stylesheet' />
<link href='../css/fullcalendar.css' rel='stylesheet' />
<link href='../css/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='../js/jquery.js'></script>
<script src='../js/jquery-ui-1.10.2.custom.min.js'></script>
<script src='../js/fullcalendar.min.js'></script>
<script>
var sobrepopup = false;
var cambiosobre = false;

	$(document).ready(function() {
	
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month'
			},
			editable: false,
			monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Dieciembre'],
			dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
			dayNamesShort : ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
			titleFormat: { 
						month: 'MMMM yyyy',                             // September 2009
						week: "d[ yyyy]{ '&#8212;'[ MMM] d MMMM yyyy}", // Sep 7 - 13 2009
						day: 'dddd d MMM, yyyy'                  // Tuesday, Sep 8, 2009
			},
			columnFormat: {
						month: 'ddd',    // Mon
						week: 'ddd d/M', // Mon 9/7
						day: 'dddd d/M'  // Monday 9/7
			},
			buttonText: {
						prev:     '&lsaquo;', // <
						next:     '&rsaquo;', // >
						prevYear: '&laquo;',  // <<
						nextYear: '&raquo;',  // >>
						today:    'hoy',
						month:    'mes',
						week:     'semana',
						day:      'd&iacute;a'
			},
			defaultView: 'month',
			slotMinutes: 15,
			timeFormat: 'H:mm',
			axisFormat: 'H(:mm)',
			allDaySlot: false,
			/*dayClick: function(date, allDay, jsEvent, view) {

				if (view.name != 'month' && !allDay) {
					debugger;
					var fechaInicio = $.fullCalendar.formatDate( date, "dd-MM-yyyy");
					var horaInicio = $.fullCalendar.formatDate( date, "HH");
					var minutoInicio = $.fullCalendar.formatDate( date, "mm");

					window.location.href= "tareas_add.php?FechaInicio=" + fechaInicio + "&HoraInicio=" + horaInicio + "&MinutoInicio=" + minutoInicio;
				}
			},*/
			events: "json-tareas.php",
			
			eventDrop: function(event, delta) {
				/*alert(event.title + ' was moved ' + delta + ' days\n' +
					'(should probably update your database)');*/
			},
			
			loading: function(bool) {
				if (bool) $('#loading').show();
				else $('#loading').hide();
			},
			eventMouseover: function(calEvent, jsEvent) {
				$('.tooltipevetn').remove();
				cambiosobre = true;
				var tooltip = '<div class="tooltipevetn"><strong>' + calEvent.title + '</strong><div class="informacion-ot"><img src="images/preloader_transparent.gif" width="50" /></div></div>';
				$("body").append(tooltip);
				$('.tooltipevetn').fadeIn('500');
				$('.tooltipevetn').css('top', $(this).offset().top - $('.tooltipevetn').height() - 25);
				$('.tooltipevetn').css('left', $(this).offset().left - 90);
				if (calEvent.id.indexOf('T') != -1)
				{
					$.ajax('json-tareas-detalles.php?IdTarea=' + calEvent.id.replace('T', ''), {
						dataTyoe: 'json',
						success: function (data, textStatus, jqXHR) {
							var json = data[0];
							var html = '';
							html+= '<div class="row"><label>Fecha de Inicio:&nbsp;</label>' + json.FechaInicio + '</div>';
							html+= '<div class="row"><label>Cliente:&nbsp;</label>' + json.Cliente.RazonSocial + '</div>';
							if (json.Cliente.Telefono != '')
								html+= '<div class="row"><label>Tel:&nbsp;</label>' + json.Cliente.Telefono + '</div>';
							if (json.Cliente.Email != '')
								html+= '<div class="row"><label>Email:&nbsp;</label>' + json.Cliente.Email + '</div>';
							html+= '<div class="row"><label>Estado:&nbsp;</label>' + json.Estado + '</div>';
							html+= '<div class="row"><label>Descripci&oacute;n:&nbsp;</label>' + json.Descripcion + '</div>';
							html+= '<div class="row"><a class="tarea-modificar" href="tareas_mod.php?IdTarea=' + json.IdTarea + '">Modificar la Tarea</a></div>';
							
							$('.informacion-ot').html(html);
						}
					});
				}
				else
				{
					$.ajax('json-presupuestos-detalles.php?IdPresupuesto=' + calEvent.id.replace('P', ''), {
						dataTyoe: 'json',
						success: function (data, textStatus, jqXHR) {
							var json = data[0];
							var html = '';
							html+= '<div class="row"><label>Fecha de Vencimiento:&nbsp;</label>' + json.FechaVencimiento + '</div>';
							//html+= '<div class="row"><label>Cliente:&nbsp;</label>' + json.Cliente.RazonSocial + '</div>';
							//html+= '<div class="row"><label>Modelo:&nbsp;</label>' + json.Modelo.DenominacionComercial + '</div>';
							if (json.Cliente.Telefono != '')
								html+= '<div class="row"><label>Tel:&nbsp;</label>' + json.Cliente.Telefono + '</div>';
							if (json.Cliente.Email != '')
								html+= '<div class="row"><label>Email:&nbsp;</label>' + json.Cliente.Email + '</div>';
							html+= '<div class="row"><label>Estado:&nbsp;</label>' + json.Estado + '</div>';
							html+= '<div class="row"><label>Observaciones:&nbsp;</label>' + json.Observaciones + '</div>';
							html+= '<div class="row"><a class="tarea-modificar" href="presupuestos_mod.php?IdPresupuesto=' + json.IdPresupuesto + '">Modificar la Factura Proforma</a></div>';
							html+= '<div class="row"><a class="tarea-modificar" target="_blank" onClick="window.open(this.href, this.target, \'width=1000,height=1000\'); return false;" href="tareas_descripcion.php?IdPresupuesto=' + json.IdPresupuesto + '">Seguimiento</a></div>';
							
							$('.informacion-ot').html(html);
						}
					});
				}
				
				$(this).mouseover(function(e) {
					$(this).css('z-index', 10000);
					$('.tooltipevetn').fadeIn('500');
					$('.tooltipevetn').fadeTo('10', 1.9);
				});
				$('.tooltipevetn').hover(function() { 
					sobrepopup = true;
				}, 
				function() {
					sobrepopup = false; 
					$(this).css('z-index', 8); 
					$('.tooltipevetn').remove(); 
				});
				setTimeout(function() { cambiosobre = false ; }, 500);
			},
			eventMouseout: function(calEvent, jsEvent) {
				setTimeout(function() {
				if (!sobrepopup && !cambiosobre) {
					$(this).css('z-index', 8);
					$('.tooltipevetn').remove();
				}
				}, 500);
			}
			
		});
		
	});

</script>
<style>

	body {
		margin-top: 40px;
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		}
		
	#loading {
		position: absolute;
		top: 5px;
		right: 5px;
		}

	#calendar {
		width: 900px;
		margin: 0 auto;
		}
		
	.tarea-modificar {
		display: block;
		width: 80%;
		text-align:center;
		margin: 10px auto;
		padding: 5px 0;
		text-decoration: none;
		border-radius: 5px;
		background: #E8E8E8;
		color: #000000;
	}

</style>
</head>
<body>
<div id='loading' style='display:none'>loading...</div>
<div id='calendar'></div>
</body>
</html>
