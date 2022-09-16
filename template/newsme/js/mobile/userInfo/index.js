$(function(){
	USERINFO.init();
});
const USERINFO = {
	init: function() {
		const _this = this;
		$('#userinfo-page .name-content .iconfont').on('click', function(){
			_this.show();
			if ($('#info-edit-modal .dialing-list ul li').length === 0) {
				TIPS.loading($('#info-edit-modal .dialog'));
				$.post('/userInfo/getInfo', {}, function(res){
					TIPS.loadout($('#info-edit-modal .dialog'));
					if (res.code === '200') {
						const text = $('#info-edit-modal [name="dialing_code"]').val();
						if (res.data) {
							let html = '';
							const list = res.data;
							for (let i=0; i<list.length; i++) {
								html += '<li key="'+i+'" '+(('+'+list[i]) === text ? 'class="active"' : '')+'>+'+list[i]+'</li>';
							}
							$('#info-edit-modal .dialing-list ul').html(html);
						}
					} else {
						_this.close();
						TIPS.message(res.message);
					}
				});
			}
		});
		$('#info-edit-modal').on('click', '.top-close-btn,.mask,.cancel-btn', function(){
			_this.close();
		});
		//选择区号点击
		$('#info-edit-modal').on('click', '.dialing-code-btn', function(event){
			event.stopPropagation();
			const obj = $(this).parent();
			if (obj.hasClass('open')) {
				obj.removeClass('open');
				obj.find('.iconfont').removeClass('icon-xiangshang2').addClass('icon-xiangxia2');
			} else {
				obj.addClass('open');
				obj.find('.iconfont').removeClass('icon-xiangxia2').addClass('icon-xiangshang2');
				const key = $('.dialing-list li.active').attr('key');
				if (key) {
					$('.dialing-list ul').scrollTop((parseInt(key) - 3)*22*(result_font/100));
				}
			}
		});
		$('#info-edit-modal').on('click', '.dialog', function(){
			const obj = $('.dialing-list').parent();
			obj.removeClass('open');
			obj.find('.iconfont').removeClass('icon-xiangshang2').addClass('icon-xiangxia2');
		});
		$('#info-edit-modal').on('click', '.dialing-list, .dialing-list input', function(event){
			event.stopPropagation();
		});
		//区号筛选
		$('#info-edit-modal').on('input propertychange', '.dialing-list input', function(){
			const val = $(this).val();
			if (val == '') {
				$(this).next().find('li').show();
			} else {
				$(this).next().find('li').each(function(){
					const text = $(this).text();
					if (text.toUpperCase().indexOf(val.toUpperCase()) === -1) {
						$(this).hide();
					} else {
						$(this).show()
					}
				});
				if ($(this).next().find('li:visible').length === 0) {
					$(this).parent().find('.empty-result').show();
				} else {
					$(this).parent().find('.empty-result').hide();
				}
			}
		});
		//区号点击
		$('.dialing-list').on('click', 'li', function(event){
			event.stopPropagation();
			const val = $(this).text();
			$(this).addClass('active').siblings().removeClass('active');
			$(this).parents('.item').find('input[name="dialing_code"]').val(val);
			$('#info-edit-modal .dialing-code-btn .text').text(val);
			const obj = $(this).parents('.input-group');
			obj.removeClass('open');
			obj.find('.iconfont').removeClass('icon-xiangshang2').addClass('icon-xiangxia2');
		});
		//点击保存
		$('#info-edit-modal .save-btn').on('click', function(){
			let check = true;
			$('#info-edit-modal form [required="required"]').each(function(){
				if ($(this).val() === '') {
					$(this).parent().addClass('error');
					let text = $(this).parent().find('.name .text').text();
					if (!text) {
						text = $(this).parents('.item').find('.name .text').text();
					}
					$(this).parent().append('<p class="error-tips">\
						<span>'+text+' is required.</span>\
						<span class="triangle"></span>\
					</p>');
					check = false;
				}
			});
			if (!check) {
				const key = parseInt($('#info-edit-modal .error').eq(0).attr('key'));
				const top = key * 61 + (key - 1) * 18 * (100/result_font);
				$('#info-edit-modal .dialog .content').animate({'scrollTop': top}, 200);
				return false;
			}
			if (_this.callback) {
				_this.callback();
				return;
			}
			TIPS.loading($('#info-edit-modal .dialog'));
			$.post('/userInfo/editInfo', $('#info-edit-modal form').serializeArray(), function(res) {
				if (res.code === '200') {
					TIPS.success(res.message);
					setTimeout(function(){
						window.location.reload();
					}, 500);
				} else {
					TIPS.loadout($('#info-edit-modal .dialog'));
					TIPS.error(res.message);
				}
			});
		});
		//头像
		if ($('#userinfo-page .name-content .name').length > 0) {
			$('#userinfo-page .image-avatar img').imageUpload('avatar', 'avatar', function(res){
				$.post('/userInfo/updateAvatar', res);
			});
		}
	},
	show: function() {
		$('#info-edit-modal').show().find('.mask').show();
		setTimeout(function(){
			$('#info-edit-modal .dialog').addClass('popup');
		}, 50);
		TIPS.stop();
	},
	close: function(){
		$('#info-edit-modal .dialog').removeClass('popup');
		$('#info-edit-modal .mask').fadeOut(200, function(){
			$('#info-edit-modal').hide();
		});
		TIPS.start();
	},
};