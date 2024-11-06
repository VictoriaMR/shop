/* index_index */
function initSelectContent(name) {
	var obj = $(name).find('.select-content');
	var obj2 = obj.find('.item.active');
	if (obj2.length > 0 && obj.scrollTop() <= 0) {
		obj.scrollTop(obj2.position().top - obj.height()/2 - obj2.height()*2);
	}
}
function initAddress(config) {

}
function initCountry(data) {
	if (!data) return false;
	var pObj = $('#address-container');
	var obj = pObj.find('input[name="country_code2"]');
	obj.parents('.input-group').removeClass('error');
	var zObj = $('#zone-container');
	obj.val(data.code);
	obj.next().text(data.name);
	pObj.find('.dialing-code').text('+'+data.dialing_code);
	zObj.find('.select-content .item').hide();
	if (zObj.find('[data-code2="'+data.code+'"]').length > 0) {
		zObj.find('[data-code2="'+data.code+'"]').show();
		pObj.find('.state-item .select-group').show();
		pObj.find('.state-item .input-content').hide();
	} else {
		pObj.find('.state-item .input-content').show();
		pObj.find('.state-item .select-group').hide();
	}
}
function initZone(data) {
	var pObj = $('#address-container .state-item');
	var obj = pObj.find('input[name="zone_name"]');
	obj.parents('.input-group').removeClass('error');
	obj.val(data.name);
	pObj.find('.title').eq(0).text(data.name);
}
$(document).ready(function(){
	initCountry($('#country-container .item.active').data());
	$('.address-edit-btn').on('click', function(){
		var config = $(this).data('config');
		showModal('#address-container');
		initAddress(config);
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
		var obj = $(this);
		var form = obj.parents('form');
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
		// loading(obj);
		$.post('/api/address', form.serializeArray(), function(res){
			showTips(res);
			if (res.code) {
				setTimeOut(window.location.reload, 300);
			} else {
				loaded(obj);
			}
		});
	});
	
	// 点击选择弹窗
	$('#address-container').on('click', '.select-group', function(){
		var to = $(this).data('to');
		initSelectContent(to);
		showModal(to);
	});
	$('.modal').on('click', '.select-content .item', function(){
		$(this).addClass('active').siblings().removeClass('active');
		var obj = $(this).parents('.modal');
		if (obj.data('type') == 'country') {
			initCountry($(this).data());
			// 初始化state
			$('#address-container .state-item .title').text('Select State/Province');
			$('#address-container .state-item [name="zone_name"]').val('');
		} else {
			initZone($(this).data());
		}
		obj.find('.close-btn').click();
	});
});
