// 认证函数
const VERIFY = {
	mobile: function (mobile) {
		return this.check(mobile, /^1[3456789]\d{9}$/);
	},
	email: function (email) {
		return this.check(email, /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/);
	},
	password: function (password) {
		return this.check(password, /^[0-9A-Za-z]{6,}/);
	},
	code: function (code) {
		return this.check(code, /^[a-zA-Z0-9]{4,}/);
	},
	check: function (input, reg) {
		return typeof input === 'string' && reg.test(input.trim());
	}
};

// 工具
const Tool = {
	loading: function (obj, type) {
		this.obj = obj;
		if (typeof type === 'undefined') {
			type = 1;
		}
		obj.setAttribute('data-text', obj.innerHTML);
		obj.innerHTML = '<div class="dots type-' + type + '"><div></div><div></div><div></div></div>';
	},
	hide: function () {
		if (this.obj) {
			this.obj.innerHTML = this.obj.getAttribute('data-text');
		}
	},
	tips: function (text, time) {
		if (typeof time === 'undefined') {
			time = 2.5;
		}
		var obj = document.getElementById('pop-tips');
		if (!obj) {
			obj = document.createElement('div');
			obj.id = 'pop-tips';
			document.body.appendChild(obj);
		}
		obj.innerHTML = text;
		clearTimeout(this.setTimeoutId);
		this.setTimeoutId = setTimeout(function () {
			if (obj && obj.parentNode) {
				obj.remove();
			}
		}, time * 1000);
	}
};

function distT(text, replace) {
	if (typeof _language_translate !== 'undefined' && _language_translate[text]) {
		if (replace) {
			return _language_translate[text].replace(replace);
		}
		return _language_translate[text];
	}
	return text;
}

function post(url, param, callback) {
	var formData = new FormData();
	for (var i in param) {
		if (Object.prototype.hasOwnProperty.call(param, i)) {
			formData.append(i, param[i]);
		}
	}
	formData.append('is_ajax', '1');
	fetch(url, {
		method: 'POST',
		body: formData,
		headers: {
			'X-Requested-With': 'XMLHttpRequest'
		}
	})
		.then(function (response) {
			return response.json();
		})
		.then(function (data) {
			if (typeof callback === 'function') {
				callback(data);
			}
		})
		.catch(function (error) {
			if (typeof callback === 'function') {
				callback({ code: 0, msg: error.message });
			}
		});
}

document.addEventListener('DOMContentLoaded', function () {
	// 1. 滚动吸顶阴影与回到顶部按钮控制
	var stickyHeader = document.getElementById('sticky-header');
	var backToTopBtn = document.getElementById('back-to-top');

	function onWindowScroll() {
		var y = window.scrollY || document.documentElement.scrollTop || 0;
		if (stickyHeader) {
			stickyHeader.classList.toggle('is-scrolled', y > 36);
		}
		if (backToTopBtn) {
			backToTopBtn.classList.toggle('visible', y > 320);
		}
	}
	window.addEventListener('scroll', onWindowScroll, { passive: true });
	onWindowScroll();

	if (backToTopBtn) {
		backToTopBtn.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}

	// 2. 顶部搜索表单校验
	var searchForm = document.querySelector('.search-form');
	if (searchForm) {
		searchForm.addEventListener('submit', function (e) {
			var input = searchForm.querySelector('input[name="keyword"]');
			if (input && !input.value.trim()) {
				e.preventDefault();
				input.focus();
				Tool.tips(distT('search_empty'));
			}
		});
	}

	// 3. 底部邮件订阅交互
	var newsletterForm = document.getElementById('footer-newsletter-form');
	if (newsletterForm) {
		newsletterForm.addEventListener('submit', function (e) {
			e.preventDefault();
			var emailInput = document.getElementById('footer-newsletter-email');
			if (!emailInput || !VERIFY.email(emailInput.value)) {
				Tool.tips(distT('email_invalid'));
				if (emailInput) emailInput.focus();
				return false;
			}
			emailInput.value = '';
			Tool.tips(distT('subscribe_success'));
			return false;
		});
	}

	// 4. 全局分页快速跳转表单边界校验
	document.addEventListener('submit', function (e) {
		var jumpForm = e.target && e.target.classList && e.target.classList.contains('page-jump-form') ? e.target : null;
		if (!jumpForm) return;
		var pageInput = jumpForm.querySelector('.page-jump-input');
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

	// 5. 全局商品卡片心愿单收藏交互 (Event Delegation)
	document.addEventListener('click', function (e) {
		var wishBtn = e.target.closest ? e.target.closest('.wishlist-btn') : null;
		if (!wishBtn) return;
		e.preventDefault();
		e.stopPropagation();

		var spuId = parseInt(wishBtn.getAttribute('data-spu-id'), 10);
		if (!spuId) return;

		post('/userInfo/wish', { spu_id: spuId }, function (res) {
			if (res && res.code === 1) {
				var isLiked = wishBtn.classList.toggle('liked');
				var svg = wishBtn.querySelector('svg');
				if (svg) {
					svg.setAttribute('fill', isLiked ? 'currentColor' : 'none');
				}
				Tool.tips(res.msg || distT('added_to_wishlist'));
			} else if (res && res.code === 10001) {
				Tool.tips(res.msg || distT('need_login'));
				setTimeout(function () {
					window.location.href = '/login.html';
				}, 900);
			} else {
				Tool.tips((res && res.msg) ? res.msg : distT('need_login'));
			}
		});
	});

	// 5. 异步同步访客统计、购物车数量与登录状态
	if (typeof window.REQUEST_PARAM === 'object' && window.REQUEST_PARAM !== null) {
		var statParams = Object.assign({}, window.REQUEST_PARAM, {
			cart: document.getElementById('header-cart-count') ? 1 : 0,
			login: document.getElementById('header-user-name') ? 1 : 0
		});
		post('/api/stat', statParams, function (res) {
			if (!res || !res.data) return;
			var cartBadge = document.getElementById('header-cart-count');
			if (cartBadge && typeof res.data.cart_count !== 'undefined') {
				cartBadge.textContent = parseInt(res.data.cart_count, 10) || 0;
			}
			var member = res.data.member;
			if (member && member.mem_id) {
				var userNameEl = document.getElementById('header-user-name');
				var userGreetingEl = document.getElementById('header-user-greeting');
				var userLinkEl = document.getElementById('header-user-link');
				var guestCtaEl = document.getElementById('account-guest-cta');
				var displayName = member.first_name || member.nickname || member.email || 'Account';
				if (userNameEl) userNameEl.textContent = displayName;
				if (userGreetingEl) userGreetingEl.textContent = 'Hi, Member';
				if (userLinkEl) userLinkEl.setAttribute('href', '/userInfo.html');
				if (guestCtaEl) guestCtaEl.classList.add('hidden');
			}
		});
	}
});
