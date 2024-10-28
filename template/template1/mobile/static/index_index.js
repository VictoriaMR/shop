/* index_index */
$(document).ready(function(){
	console.log('ready address')
	$('.address-edit-btn').on('click', function(){
		var config = $(this).data('config');
		showModal('#address-container');
		initAddress(config);
	});
	function initAddress(config) {

	}
	$('#address-container').on('click', '.remove', function(){
		if ($(this).css('opacity') != '0') {
			$(this).prev().val('');
		}
	});
	$('#address-container').on('click', '.half-block', function(){
		if ($(this).hasClass('active')) {
			return false;
		}
		$(this).addClass('active');
		$(this).parents().siblings().find('.half-block').removeClass('active');
	})
});
