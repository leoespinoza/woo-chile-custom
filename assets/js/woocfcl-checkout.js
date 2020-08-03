jQuery(document).ready(function($) {
	$('select.woocfcl-enhanced-select').select2({
		minimumResultsForSearch: 10,
		allowClear : true,
		placeholder: $(this).data('placeholder')
	}).addClass('enhanced');
});