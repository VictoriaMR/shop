const RESETPASSWD = {
	init: function() {
		const _this = this;
		$('.token-btn').on('click', function(){
			const obj = $(this).parent().find('[name="token"]');
			const token = obj.val();
			if (token === '') {
				_this.error(obj.parent(), 'This Token is required.');
				return false;
			}
			if (token.length !== 32) {
				_this.error(obj.parent(), 'This Token is Invalid.');
				return false;
			}
			$(this).parent().submit();
		});
		$('.password-btn').on('click', function(){
			const obj = $(this).parent().find('[name="password"]');
			const password = obj.val();
			if (password === '') {
				_this.error(obj.parent(), 'This Password is required.');
				return false;
			}
			if (!VERIFY.password(password)) {
				_this.error(obj.parent(), 'This Password is Invalid.');
				return false;
			}
			$.post(URI+'login/resetPassword', $(this).parents('form').serializeArray(), function(res){
				if (res.code === '200') {
					TIPS.success(res.message);
					setTimeout(function(){
						window.location.href = res.data.url;
					}, 2000);
				} else {
					TIPS.error(res.message);
				}
			});
		});
	},
	error(obj, msg){
		obj.addClass('error');
		obj.parent().find('.error-msg').remove();
		obj.parent().append('<p class="error-msg">'+msg+'</p>');
	},
	loading: function(obj, msg) {
		obj.data('text', obj.text());
		obj.text(msg).attr('disabled', true);
	},
	loaded: function(obj) {
		obj.text(obj.data('text')).attr('disabled', false);
	}
};