<title>Administrador</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Pragma" content="no-cache" />
<link href="../css/basico_backend.css" rel="stylesheet" type="text/css">
<link type="text/css" rel="stylesheet" href="../library/calendar/calendar.css" />
<link type="text/css" rel="stylesheet" href="../css/jquery-ui-1.8.20.custom.css" />
<script language="javascript" src="../library/calendar/calendar_us.js"></script>
<script src="http://code.jquery.com/jquery-1.7.0.js"></script>

<script language="javascript" src="../js/jquery-ui-1.8.20.custom.min.js"></script>


<script type="text/javascript">
	$j = jQuery.noConflict();
	function getCaretPosition(ctrl) {
		var CaretPos = 0;    // IE Support
		if (document.selection) {
			ctrl.focus();
			var Sel = document.selection.createRange();
			Sel.moveStart('character', -ctrl.value.length);
			CaretPos = Sel.text.length;
		}
		// Firefox support
		else if (ctrl.selectionStart || ctrl.selectionStart == '0') {
			CaretPos = ctrl.selectionStart;
		}

		return CaretPos;
	}

	function setCaretPosition(ctrl, pos) {
		if (ctrl.setSelectionRange) {
			ctrl.focus();
			ctrl.setSelectionRange(pos,pos);
		}
		else if (ctrl.createTextRange) {
			var range = ctrl.createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
		}
	}
	
	$j(document).ready(function() {
		$j('input[type="text"]').bind('keyup', function (e) {
			var caretPosition = getCaretPosition(this);
		if (e.which >= 97 && e.which <= 122) {
			var newKey = e.which - 32;
			// I have tried setting those
			e.keyCode = newKey;
			e.charCode = newKey;
		}

		$j(this).val(($j(this).val()).toUpperCase());
			setCaretPosition(this, caretPosition);
	});
	});
</script>