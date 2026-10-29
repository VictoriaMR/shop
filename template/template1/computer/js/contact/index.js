document.addEventListener('DOMContentLoaded', function () {
	var form = document.getElementById('js-contact-form');
	if (!form) return;

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var btn = document.getElementById('js-contact-submit');
		var fb = document.getElementById('js-contact-feedback');
		var nameEl = document.getElementById('ct-name');
		var emailEl = document.getElementById('ct-email');
		var subjectEl = document.getElementById('ct-subject');
		var contentEl = document.getElementById('ct-content');

		var payload = {
			name: nameEl ? nameEl.value.trim() : '',
			email: emailEl ? emailEl.value.trim() : '',
			subject: subjectEl ? subjectEl.value : 'General Inquiry',
			content: contentEl ? contentEl.value.trim() : ''
		};

		if (fb) {
			fb.classList.remove('success', 'error');
			fb.style.display = 'none';
		}

		var origText = btn ? btn.textContent : 'Send Message';
		if (btn) {
			btn.disabled = true;
			btn.textContent = 'Sending...';
		}

		post('/contact/send', payload, function (res) {
			if (btn) {
				btn.disabled = false;
				btn.textContent = origText;
			}
			if (res && (res.code === 1 || res.code === 200)) {
				if (fb) {
					fb.classList.add('success');
					fb.textContent = res.msg || 'Your message has been sent!';
					fb.style.display = 'block';
				} else {
					Tool.tips(res.msg || 'Your message has been sent!');
				}
				form.reset();
			} else {
				var errMsg = (res && res.msg) ? res.msg : 'Failed to send message.';
				if (fb) {
					fb.classList.add('error');
					fb.textContent = errMsg;
					fb.style.display = 'block';
				} else {
					Tool.tips(errMsg);
				}
			}
		});
		return false;
	});
});
