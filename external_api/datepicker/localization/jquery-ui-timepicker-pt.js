/* Portuguese translation for the jQuery Timepicker Addon */
/* Written by Luan Almeida */
(function($) {
	$.datepicker.regional['pt'] = {
		prevText: '&#x3c;Ant',
		nextText: 'Sig&#x3e;',
		currentText: 'Hoy',
		monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
		'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
		'Jul','Ago','Set','Out','Nov','Dec'],
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabádo'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		weekHeader: 'Sm',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['pt']);
 
	$.timepicker.regional['pt'] = {
		prev: 'Anterior',
		timeOnlyTitle: 'Escolha uma hora',
		timeText: 'Hora',
		hourText: 'Horas',
		minuteText: 'Minutos',
		secondText: 'Segundos',
		millisecText: 'Milissegundos',
		timezoneText: 'Fuso horário',
		currentText: 'Hoje',
		closeText: 'Submeter',
		timeFormat: 'hh:mm',
		amNames: ['a.m.', 'AM', 'A'],
		pmNames: ['p.m.', 'PM', 'P'],
		ampm: false,
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['pt']);
})(jQuery);
