document.addEventListener('DOMContentLoaded', function () {
	var mainImage = document.getElementById('pdp-main-image');
	var thumbBtns = document.querySelectorAll('.pdp-thumb-btn');
	var skuInput = document.getElementById('pdp-selected-sku-id');
	var skuCodeText = document.getElementById('pdp-sku-id-text');
	var priceNowEl = document.getElementById('pdp-price-current');
	var priceWasEl = document.getElementById('pdp-price-old');
	var discountBadgeEl = document.getElementById('pdp-discount-badge');
	var stockBadgeEl = document.getElementById('pdp-stock-badge');
	var stockCountEl = document.getElementById('pdp-stock-count');
	var qtyInput = document.getElementById('pdp-qty-input');
	var qtyMinus = document.getElementById('pdp-qty-minus');
	var qtyPlus = document.getElementById('pdp-qty-plus');
	var btnAddBag = document.getElementById('btn-add-to-bag');
	var btnBuyNow = document.getElementById('btn-buy-now');

	var skuMapData = window.PDP_SKU_MAP || {};

	// 1. 缩略图切换主图
	function setMainGalleryImage(src) {
		if (!mainImage || !src) return;
		mainImage.setAttribute('src', src);
		for (var i = 0; i < thumbBtns.length; i++) {
			var bSrc = thumbBtns[i].getAttribute('data-full-src');
			thumbBtns[i].classList.toggle('active', bSrc === src);
		}
	}

	for (var i = 0; i < thumbBtns.length; i++) {
		thumbBtns[i].addEventListener('click', function () {
			setMainGalleryImage(this.getAttribute('data-full-src'));
		});
	}

	// 2. 规格属性选择联动
	var attrBtns = document.querySelectorAll('.pdp-attr-btn');
	function resolveActiveSku() {
		var groups = document.querySelectorAll('.pdp-attr-group');
		var comboParts = [];
		for (var g = 0; g < groups.length; g++) {
			var nid = groups[g].getAttribute('data-attrn-id');
			var activeBtn = groups[g].querySelector('.pdp-attr-btn.active');
			if (nid && activeBtn) {
				comboParts.push(nid + ':' + activeBtn.getAttribute('data-attrv-id') + ';');
			}
		}
		var comboKey = comboParts.join('');
		if (comboKey && skuMapData[comboKey]) {
			return skuMapData[comboKey];
		}
		return null;
	}

	function applySkuUI(skuObj) {
		if (!skuObj) return;
		if (skuInput) skuInput.value = skuObj.sku_id;
		if (skuCodeText) skuCodeText.textContent = skuObj.sku_id;
		if (priceNowEl && skuObj.price_format) priceNowEl.textContent = skuObj.price_format;
		if (priceWasEl && skuObj.original_price_format) priceWasEl.textContent = skuObj.original_price_format;
		if (discountBadgeEl && skuObj.discount_percent) {
			discountBadgeEl.textContent = '-' + skuObj.discount_percent + '%';
		}
		var stock = parseInt(skuObj.stock, 10) || 0;
		if (stockCountEl) stockCountEl.textContent = stock;
		if (qtyInput) {
			qtyInput.setAttribute('max', Math.max(1, stock));
			if (parseInt(qtyInput.value, 10) > stock && stock > 0) {
				qtyInput.value = stock;
			}
		}
		if (stockBadgeEl) {
			stockBadgeEl.classList.toggle('out', stock <= 0);
			stockBadgeEl.textContent = stock > 0 ? 'In Stock — Ready to Ship' : 'Out of Stock';
		}
		if (btnAddBag) btnAddBag.disabled = stock <= 0;
		if (btnBuyNow) btnBuyNow.disabled = stock <= 0;
		if (skuObj.image) {
			setMainGalleryImage(skuObj.image);
		}
	}

	for (var a = 0; a < attrBtns.length; a++) {
		attrBtns[a].addEventListener('click', function () {
			var group = this.closest('.pdp-attr-group');
			if (!group) return;
			var siblings = group.querySelectorAll('.pdp-attr-btn');
			for (var s = 0; s < siblings.length; s++) {
				siblings[s].classList.remove('active');
				siblings[s].setAttribute('aria-pressed', 'false');
			}
			this.classList.add('active');
			this.setAttribute('aria-pressed', 'true');

			var labelSpan = group.querySelector('.pdp-attr-selected');
			if (labelSpan) {
				labelSpan.textContent = this.getAttribute('data-attrv-name') || '';
			}
			var swatchImg = this.getAttribute('data-attrv-image');
			if (swatchImg) {
				setMainGalleryImage(swatchImg);
			}
			var matchedSku = resolveActiveSku();
			if (matchedSku) {
				applySkuUI(matchedSku);
			}
		});
	}

	// 3. 数量加减器
	function getCleanQty() {
		if (!qtyInput) return 1;
		var v = parseInt(qtyInput.value, 10) || 1;
		var max = parseInt(qtyInput.getAttribute('max'), 10) || 999;
		if (v < 1) v = 1;
		if (v > max) v = max;
		qtyInput.value = v;
		return v;
	}

	if (qtyMinus) {
		qtyMinus.addEventListener('click', function () {
			var q = getCleanQty();
			if (q > 1) qtyInput.value = q - 1;
		});
	}
	if (qtyPlus) {
		qtyPlus.addEventListener('click', function () {
			var q = getCleanQty();
			var max = parseInt(qtyInput.getAttribute('max'), 10) || 999;
			if (q < max) qtyInput.value = q + 1;
		});
	}
	if (qtyInput) {
		qtyInput.addEventListener('change', getCleanQty);
	}

	// 4. 加入购物车 (Add to Bag)
	if (btnAddBag) {
		btnAddBag.addEventListener('click', function () {
			var skuId = skuInput ? parseInt(skuInput.value, 10) : 0;
			var qty = getCleanQty();
			if (!skuId) {
				Tool.tips('Please select product options first.');
				return;
			}
			var origText = btnAddBag.innerHTML;
			btnAddBag.disabled = true;
			btnAddBag.textContent = 'Adding...';

			post('/cart/addToCart', { sku_id: skuId, num: qty }, function (res) {
				btnAddBag.disabled = false;
				btnAddBag.innerHTML = origText;
				if (res && res.code === 1) {
					Tool.tips(res.msg || 'Added to your shopping bag!');
					var badge = document.getElementById('header-cart-count');
					if (badge && res.data && typeof res.data.cart_count !== 'undefined') {
						badge.textContent = parseInt(res.data.cart_count, 10) || 0;
					}
				} else {
					Tool.tips((res && res.msg) ? res.msg : 'Unable to add item to bag.');
				}
			});
		});
	}

	// 5. 立即购买 (Buy It Now)
	if (btnBuyNow) {
		btnBuyNow.addEventListener('click', function () {
			var skuId = skuInput ? parseInt(skuInput.value, 10) : 0;
			var qty = getCleanQty();
			if (!skuId) {
				Tool.tips('Please select product options first.');
				return;
			}
			window.location.href = '/checkout.html?id=' + encodeURIComponent(skuId) + '&quantity=' + encodeURIComponent(qty);
		});
	}

	// 6. 详情页底部 Tab 切换
	var tabBtns = document.querySelectorAll('[data-pdp-tab]');
	var tabPanels = document.querySelectorAll('[data-pdp-panel]');
	for (var t = 0; t < tabBtns.length; t++) {
		tabBtns[t].addEventListener('click', function () {
			var target = this.getAttribute('data-pdp-tab');
			for (var j = 0; j < tabBtns.length; j++) {
				var isCurrent = tabBtns[j].getAttribute('data-pdp-tab') === target;
				tabBtns[j].classList.toggle('active', isCurrent);
				tabBtns[j].setAttribute('aria-selected', isCurrent ? 'true' : 'false');
			}
			for (var p = 0; p < tabPanels.length; p++) {
				var match = tabPanels[p].getAttribute('data-pdp-panel') === target;
				tabPanels[p].classList.toggle('hidden', !match);
			}
		});
	}
});
