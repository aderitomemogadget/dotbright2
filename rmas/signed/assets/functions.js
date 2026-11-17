$(document).ready(function() {
	
	var options = {
			defaultAction: 'drawIt',
			drawOnly: true,
			lineTop: 120,
			lineMargin: 20,
			lineWidth: 2,
			penColour: '#000',
			
	}

	$('.sigPad').signaturePad(options);

});