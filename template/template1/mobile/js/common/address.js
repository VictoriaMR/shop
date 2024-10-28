$(document).ready(function(){
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
		var type = $(this).data('type');
		var obj = $('#address-container .company-name-item');
		if (type == 1) {
			obj.slideDown(200);
		} else {
			obj.slideUp(200);
			obj.find('input').val('');
		}
	});
	$('#address-container').on('click', '.switch-item', function(){
		var obj = $(this).find('.switch');
		if (obj.hasClass('on')) {
			obj.removeClass('on');
			obj.find('input').val(0);
		} else {
			obj.addClass('on');
			obj.find('input').val(1);
		}
	});
	// 保存
	$('#address-container').on('click', '.btn-save', function(){
		var form = $(this).parents('form');
		var check = true;
		form.find('[required="required"]').each(function(){
			if ($(this).parents('.item').is(':visible') && !$(this).val()) {
				inputError($(this), $(this).parents('.input-group').find('.input-title span').eq(0).text()+' is required.');
				check = false;
			}
		});
		if (!check) {
			return false;
		}
		console.log('here')
	});
});