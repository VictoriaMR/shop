const FORGET = {
	init: function() {
		const _this = this;
		$('#forget-page .btn').on('click', function(){
			const obj = $('[name="email"]');
			const email = obj.val();
			if (email === '') {
				_this.error(obj.parent(), 'This Email is required.');
				return false;
			}
			if (!VERIFY.email(email)) {
				_this.error(obj.parent(), 'This Email is Invalid.');
				return false;
			}
			const _thisObj = $(this);
			_this.loading(_thisObj, 'Send...');
			$.post('/login/passwordVerify', {email: email}, function(res) {
				_this.loaded(_thisObj);
				if (res.code === 200 || res.code === '200') {
					TIPS.success(res.message);
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