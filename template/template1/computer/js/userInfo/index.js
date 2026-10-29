document.addEventListener('DOMContentLoaded', function () {
	var panel = document.getElementById('js-profile-edit-panel');
	var toggleBtn = document.getElementById('js-toggle-edit-profile');
	var cancelBtn = document.getElementById('js-cancel-edit-profile');
	var form = document.getElementById('js-profile-form');
	var saveBtn = document.getElementById('js-save-profile-btn');

	function toggleEditPanel() {
		if (panel) {
			panel.classList.toggle('is-open');
		}
	}

	if (toggleBtn) toggleBtn.addEventListener('click', toggleEditPanel);
	if (cancelBtn) cancelBtn.addEventListener('click', toggleEditPanel);

	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var firstNameEl = form.querySelector('[name="first_name"]');
			var lastNameEl = form.querySelector('[name="last_name"]');
			var dialingEl = form.querySelector('[name="dialing_code"]');
			var phoneEl = form.querySelector('[name="phone"]');

			var payload = {
				first_name: firstNameEl ? firstNameEl.value.trim() : '',
				last_name: lastNameEl ? lastNameEl.value.trim() : '',
				dialing_code: dialingEl ? dialingEl.value : '+1',
				phone: phoneEl ? phoneEl.value.trim() : ''
			};

			if (!payload.first_name || !payload.last_name || !payload.phone) {
				Tool.tips('Please complete all required fields.');
				return false;
			}

			var origText = saveBtn ? saveBtn.textContent : 'Save Changes';
			if (saveBtn) {
				saveBtn.disabled = true;
				saveBtn.textContent = 'Saving...';
			}

			post('/userInfo/editInfo', payload, function (res) {
				if (saveBtn) {
					saveBtn.disabled = false;
					saveBtn.textContent = origText;
				}
				if (res && (res.code === 1 || res.code === 200)) {
					Tool.tips(res.msg || 'Profile updated successfully.');
					setTimeout(function () {
						window.location.reload();
					}, 400);
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Failed to update profile.');
				}
			});
			return false;
		});
	}
});
