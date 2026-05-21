function RealizarBusquedaPopup(urlAjax, dataUrl, popupTitle)
{
	$j('body').addClass("loading"); 
	$j.ajax(urlAjax,{
		data: dataUrl,
		success: function(data) {
			$j('#modal-popup').html(data);	
			$j('body').removeClass("loading"); 
					
			$j('#modal-popup').dialog({
				closeOnEscape: true,
				title: popupTitle,
				width: 700,
				height: 550,
				modal: true
			});
		}
	});
}