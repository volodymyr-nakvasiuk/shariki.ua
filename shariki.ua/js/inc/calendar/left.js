js.module("inc.calendar.left");
js.include("jquery.min");
js.include("jquery.ui.datepicker-ru");
$(function() {
	$( "#datepicker" ).datepicker({
		dateFormat: 'yy-mm-dd',
		defaultDate: phpParams.date,
		onSelect: function(dateText, inst) {
			window.location.href = '/calendar/?d='+dateText;
		}
	});
});