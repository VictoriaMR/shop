document.addEventListener('DOMContentLoaded', function () {
	// 分页快速跳转表单交互
	var jumpForm = document.getElementById('newin-page-jump-form');
	if (jumpForm) {
		jumpForm.addEventListener('submit', function (e) {
			var pageInput = document.getElementById('newin-page-jump-input');
			if (!pageInput) return;
			var targetPage = parseInt(pageInput.value, 10);
			var maxPage = parseInt(pageInput.getAttribute('max'), 10) || 1;
			if (isNaN(targetPage) || targetPage < 1) {
				targetPage = 1;
			} else if (targetPage > maxPage) {
				targetPage = maxPage;
			}
			pageInput.value = targetPage;
		});
	}
});
