document.addEventListener('DOMContentLoaded', function () {
	document.addEventListener('click', function (e) {
		var trigger = e.target.closest ? e.target.closest('.js-faq-trigger') : null;
		if (!trigger) return;
		var item = trigger.closest('.faq-accordion-item');
		if (item) {
			item.classList.toggle('is-open');
		}
	});
});
