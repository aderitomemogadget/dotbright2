(function($) {
	$.datepicker.regional['en'] = {
		prevText: '&#x3c;Ant',
		nextText: 'Sig&#x3e;',
		currentText: 'Hoy',
		monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','May','Jun',
		'Jul','Aug','Sep','Oct','Nov','Dec'],
		dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		dayNamesShort: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
		dayNamesMin: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
		weekHeader: 'Sm',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['en']);
 
	$.timepicker.regional['en'] = {
		prev: 'Previous',
		timeOnlyTitle: 'Choose a hour',
		timeText: 'Hour',
		hourText: 'Hours',
		minuteText: 'Minutes',
		secondText: 'Seconds',
		millisecText: 'Milliseconds',
		timezoneText: 'Time Zone',
		currentText: 'Today',
		closeText: 'Submit',
		timeFormat: 'hh:mm',
		amNames: ['a.m.', 'AM', 'A'],
		pmNames: ['p.m.', 'PM', 'P'],
		ampm: false,
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['en']);
})(jQuery);