document.addEventListener('DOMContentLoaded', function () {
	var jumpForm = document.getElementById('search-page-jump-form');
	if (jumpForm) {
		jumpForm.addEventListener('submit', function () {
			var input = document.getElementById('search-page-jump-input');
			if (input) {
				var val = parseInt(input.value, 10);
				var max = parseInt(input.getAttribute('max'), 10) || 1;
				if (isNaN(val) || val < 1) input.value = 1;
				else if (val > max) input.value = max;
			}
		});
	}
});
