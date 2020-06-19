jQuery(document).ready(function($) {
	$('select.wcscl-enhanced-select').select2({
		minimumResultsForSearch: 10,
		allowClear : true,
		placeholder: $(this).data('placeholder')
	}).addClass('enhanced');
});