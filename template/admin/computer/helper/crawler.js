var CRAWLER = {
	init: function(callback) {
		const _this = this;
		if (!HELPERINIT.isItemPage()) {
			callback(-1, {}, '非产品详情页面!');
		} else if (HELPERINIT.isLoginPage()) {
			callback(-2, {}, '页面需要登录!');
		} else if (HELPERINIT.isVerifyPage()) {
			callback(-3, {}, '页面需要验证!');
		} else if (HELPERINIT.isDenyPage()) {
			callback(-4, {}, '页面被阻止访问!');
		} else if (HELPERINIT.isErrorPage()) {
			callback(-5, {}, HELPERINIT.isErrorPage());
		} else if (_this.isOffShelf()) {
			callback(-6, {}, '产品已下架!');
		} else {
			_this.data(function(code, data, msg) {
				localStorage.setItem('CRAWLER_DATA', JSON.stringify(data));
				callback(code, data, msg);
			});
		}
	},
	isOffShelf: function() {
		let obj = document.querySelector('.mod-detail-offline .mod-detail-offline-title, .mod-detail-offline-title');
		if (!obj) {
			obj = document.querySelector('.tb-off-sale, .tb-off-sale .tb-hint strong, .tb-btn-buy .tb-disabled');
		}
		if (!obj) {
			obj = document.querySelector('.sold-out-recommend, .tb-btn-wait, .errorDetail');
		}
		if (!obj) {
			let actObj = document.querySelector('.Actions--leftButtons--1M3KkF7');
			if (actObj && actObj.innerText.indexOf('商品已经下架') >= 0) {
				return actObj;
			}
		}
		if (!obj && typeof window.__GLOBAL_DATA !== 'undefined' && typeof window.__GLOBAL_DATA.offerDomain !== 'undefined') {
			try {
				let offerDomain = typeof window.__GLOBAL_DATA.offerDomain === 'string' ? JSON.parse(window.__GLOBAL_DATA.offerDomain) : window.__GLOBAL_DATA.offerDomain;
				if (offerDomain.tradeModel && offerDomain.tradeModel.canBookedAmount === 0) {
					return true;
				}
			} catch (e) {}
		}
		return obj;
	},
	data: function(callback) {
		const _this = this;
		switch (HELPERINIT.getDomain()) {
			case '1688.com':
				_this.get1688(callback);
				break;
			case 'taobao.com':
				_this.getTaobao(callback);
				break;
			case 'tmall.com':
				_this.getTmall(callback);
				break;
			default:
				callback(-9, {}, '未知渠道商品详情页面');
				break;
		}
	},
	get1688: function(callback) {
		const _this = this;
		if (typeof __INIT_DATA !== 'undefined' && __INIT_DATA.globalData) {
			_this.get16881(callback);
			return;
		}
		if (window.context && window.context.result && window.context.result.global && window.context.result.global.globalData) {
			_this.get16882(callback);
			return;
		}
		if (typeof iDetailData !== 'undefined') {
			_this.get16883(callback);
			return;
		}
		let countMax = 20;
		const interval = setInterval(function() {
			if (typeof __INIT_DATA !== 'undefined' && __INIT_DATA.globalData) {
				clearInterval(interval);
				_this.get16881(callback);
				return;
			}
			if (window.context && window.context.result && window.context.result.global && window.context.result.global.globalData) {
				clearInterval(interval);
				_this.get16882(callback);
				return;
			}
			if (typeof iDetailData !== 'undefined') {
				clearInterval(interval);
				_this.get16883(callback);
				return;
			}
			countMax--;
			if (countMax <= 0) {
				clearInterval(interval);
				callback(-8, {}, '获取1688数据失败!');
			}
		}, 300);
	},
	getTaobao: function(callback) {
		const _this = this;
		if (typeof g_config !== 'undefined' && g_config.idata && typeof Hub !== 'undefined') {
			_this.getTaobao2(callback);
		} else {
			_this.fetchTaobaoOrTmall(callback);
		}
	},
	getTmall: function(callback) {
		const _this = this;
		if (typeof KISSY !== 'undefined' && KISSY.version == '1.42') {
			_this.getTmallOld(callback);
		} else {
			_this.fetchTaobaoOrTmall(callback);
		}
	},
	fetchTaobaoOrTmall: function(callback) {
		const _this = this;
		let count = 0;
		const checkInterval = setInterval(function() {
			let tmp = _this.getResponse('pcdetail.data.get');
			if (tmp && tmp.item) {
				clearInterval(checkInterval);
				localStorage.setItem('google_helper_g_config', JSON.stringify(tmp));
				_this.baseInfo = tmp;
				_this.getTaobao1(callback);
				return;
			}
			if (typeof window.detailRequest !== 'undefined') {
				clearInterval(checkInterval);
				window.detailRequest.then(function(data) {
					if (data && data.item) {
						localStorage.setItem('google_helper_g_config', JSON.stringify(data));
						_this.baseInfo = data;
						_this.getTaobao1(callback);
					} else {
						_this.requestTaobaoApi(callback);
					}
				}).catch(function() {
					_this.requestTaobaoApi(callback);
				});
				return;
			}
			count++;
			if (count >= 8) {
				clearInterval(checkInterval);
				_this.requestTaobaoApi(callback);
			}
		}, 300);
	},
	requestTaobaoApi: function(callback) {
		const _this = this;
		const cookie = _this.getCookie('_m_h5_tk');
		if (!cookie) {
			callback(-1, {}, '没有获取到有效cookie，请求失败!');
			return false;
		}
		const id = HELPERINIT.isItemPage();
		const domain = HELPERINIT.getDomain();
		const t = new Date().getTime();
		const appKey = '12574478';
		let data = {
			detail_v: '3.3.2',
			id: id,
			exParams: JSON.stringify({
				id: id,
				queryParams: 'id=' + id,
				domain: location.origin,
				path_name: location.pathname
			})
		};
		data = JSON.stringify(data);
		const paramStr = cookie.split('_')[0] + '&' + t + '&' + appKey + '&' + data;
		const dataType = domain == 'taobao.com' ? 'json' : 'jsonp';
		let queryParam = {
			'jsv': '2.6.1',
			'appKey': appKey,
			't': t,
			'sign': _this.hex_md5(paramStr),
			'api': 'mtop.taobao.pcdetail.data.get',
			'v': '1.0',
			'isSec': '0',
			'ecode': '0',
			'timeout': 10000,
			'ttid': '2022@taobao_litepc_9.17.0',
			'AntiFlood': true,
			'AntiCreep': true,
			'preventFallback': true,
			'type': dataType,
			'dataType': dataType,
			'data': data
		};
		if (domain == 'taobao.com') {
			queryParam.valueType = 'string';
		}
		_this.ajax({
			url: 'https://h5api.m.' + domain + '/h5/mtop.taobao.pcdetail.data.get/1.0/',
			data: queryParam,
			dataType: dataType,
			success: function(res) {
				if (typeof res == 'string') {
					res = JSON.parse(res);
				}
				if (!res || !res.data || !res.data.item) {
					callback(-1, {}, (res && res.ret) ? res.ret.join(';') : '获取数据失败!');
					return false;
				}
				localStorage.setItem('google_helper_g_config', JSON.stringify(res.data));
				_this.baseInfo = res.data;
				_this.getTaobao1(callback);
			},
			error: function(error) {
				callback(-1, error, '请求发生错误');
			}
		});
	},
	// 1688 __INIT_DATA 页面解析
	get16881: function(callback) {
		const _this = this;
		const gData = __INIT_DATA.globalData;
		if (gData.channelType && gData.channelType.toLowerCase() == 'jgdz') {
			callback(-3, {}, '加工定制产品不可购买');
			return false;
		}
		let ret_data = {};
		let obj;
		ret_data.channel_id = 6053;
		ret_data.item_id = (gData.tempModel && gData.tempModel.offerId) ? gData.tempModel.offerId : HELPERINIT.isItemPage();
		ret_data.name = (gData.tempModel && gData.tempModel.offerTitle) ? gData.tempModel.offerTitle : _this.get1688DomTitle();
		ret_data.url = _this.getUrl(ret_data.item_id);
		obj = document.querySelector('.logistics-express-price');
		ret_data.post_fee = obj ? (parseFloat(obj.innerText.replace(/[^0-9.]/ig, '')) || 0) : 0;

		// 销量
		ret_data.sale_count = 0;
		if (typeof window.__GLOBAL_DATA !== 'undefined' && window.__GLOBAL_DATA.tempModel && window.__GLOBAL_DATA.tempModel.saledCount !== undefined) {
			ret_data.sale_count = _this.formatSale(window.__GLOBAL_DATA.tempModel.saledCount);
		} else if (__INIT_DATA.data && __INIT_DATA.data['1081181309095'] && __INIT_DATA.data['1081181309095'].data && __INIT_DATA.data['1081181309095'].data.saleNum) {
			let saleNum = _this.formatSale(__INIT_DATA.data['1081181309095'].data.saleNum);
			if (__INIT_DATA.data['1081181309095'].data.saleCountDate && __INIT_DATA.data['1081181309095'].data.saleCountDate.indexOf('年') >= 0) {
				saleNum = Math.ceil(saleNum / 3);
			}
			ret_data.sale_count = saleNum;
		} else {
			let gwRes = _this.getResponse('mtop.mbox.fc.common.gateway');
			if (gwRes && gwRes.result && gwRes.result.saleTotalStr) {
				ret_data.sale_count = _this.formatSale(gwRes.result.saleTotalStr);
			}
		}

		// 店铺信息
		let shopId = '', shopName = '', shopUrl = '';
		if (gData.tempModel) {
			shopId = gData.tempModel.sellerMemberId ? gData.tempModel.sellerMemberId.replace('b2b-', '') : '';
			shopName = gData.tempModel.companyName || '';
			shopUrl = gData.tempModel.winportUrl || '';
		}
		if (gData.offerBaseInfo && gData.offerBaseInfo.sellerWinportUrl) {
			shopUrl = gData.offerBaseInfo.sellerWinportUrl;
		}
		if ((!shopId || !shopUrl) && typeof window.__GLOBAL_DATA !== 'undefined' && window.__GLOBAL_DATA.offerDomain) {
			try {
				let offerDomain = typeof window.__GLOBAL_DATA.offerDomain === 'string' ? JSON.parse(window.__GLOBAL_DATA.offerDomain) : window.__GLOBAL_DATA.offerDomain;
				if (offerDomain.sellerModel) {
					shopId = shopId || (offerDomain.sellerModel.memberId ? offerDomain.sellerModel.memberId.replace('b2b-', '') : '');
					shopName = shopName || offerDomain.sellerModel.companyName || '';
					shopUrl = shopUrl || offerDomain.sellerModel.winportUrl || '';
				}
			} catch (e) {}
		}
		const cleanUrl1688 = _this.clean1688ShopUrl(shopUrl);
		ret_data.seller = {
			shop_id: shopId || _this.extractShopIdFromUrl(cleanUrl1688),
			shop_name: shopName,
			shop_url: cleanUrl1688,
			service: _this.get1688ServiceInfo(),
		};

		// 主图
		ret_data.pdt_picture = [];
		if (gData.images && gData.images.length > 0) {
			for (let i = 0; i < gData.images.length; i++) {
				if (gData.images[i].fullPathImageURI) {
					ret_data.pdt_picture.push(_this.formatImgUrl(gData.images[i].fullPathImageURI));
				}
			}
		}

		// 规格属性 & SKU
		ret_data.attr = {};
		ret_data.sku = {};
		let backUpPrice = _this.get1688BackupPrice(gData);
		if (gData.skuModel && gData.skuModel.skuProps && gData.skuModel.skuProps.length > 0) {
			for (let i = 0; i < gData.skuModel.skuProps.length; i++) {
				const item = gData.skuModel.skuProps[i];
				let value = {};
				for (let j = 0; j < item.value.length; j++) {
					value[j] = {
						name: item.value[j].name,
						img: item.value[j].imageUrl ? _this.formatImgUrl(item.value[j].imageUrl) : '',
					};
				}
				const fid = item.fid !== undefined ? item.fid : i;
				ret_data.attr[fid] = {
					name: item.prop,
					value: value
				};
			}
			const attrCount = Object.keys(ret_data.attr).length;
			const skuInfoMap = gData.skuModel.skuInfoMap || {};
			for (let k in skuInfoMap) {
				const item = skuInfoMap[k];
				let cleanKey = k.replace(/^(&gt;)+|(&gt;)+$/g, '>');
				let attrParts;
				if (attrCount > 1) {
					attrParts = cleanKey.indexOf('&gt;') > -1 ? cleanKey.split('&gt;') : cleanKey.split(';');
				} else {
					attrParts = [cleanKey];
				}
				let pvs = {};
				let tmpAttr = JSON.parse(JSON.stringify(ret_data.attr));
				let img = '';
				for (let r = 0; r < attrParts.length; r++) {
					if (!attrParts[r]) continue;
					let attrMap = _this.getAttr(attrParts[r], tmpAttr);
					if (!attrMap || !ret_data.attr[attrMap.name]) continue;
					pvs[ret_data.attr[attrMap.name].name] = ret_data.attr[attrMap.name].value[attrMap.value].name;
					if (attrMap.img) {
						img = attrMap.img;
					}
				}
				let price = item.discountPrice !== undefined ? item.discountPrice : (item.price !== undefined ? item.price : backUpPrice);
				const skuId = item.skuId || k;
				ret_data.sku[skuId] = {
					pvs: pvs,
					sku_map: cleanKey.replace(/&gt;/g, ';'),
					price: _this.formatPrice(price),
					stock: item.canBookCount !== undefined ? parseInt(item.canBookCount) : 0,
					img: img,
				};
			}
		} else {
			// 单规格 1688 产品
			let singleStock = 0;
			if (gData.orderParamModel && gData.orderParamModel.orderParam && gData.orderParamModel.orderParam.canBookedAmount !== undefined) {
				singleStock = parseInt(gData.orderParamModel.orderParam.canBookedAmount);
			} else if (typeof window.__GLOBAL_DATA !== 'undefined' && window.__GLOBAL_DATA.offerDomain) {
				try {
					let offerDomain = typeof window.__GLOBAL_DATA.offerDomain === 'string' ? JSON.parse(window.__GLOBAL_DATA.offerDomain) : window.__GLOBAL_DATA.offerDomain;
					if (offerDomain.tradeModel && offerDomain.tradeModel.canBookedAmount !== undefined) {
						singleStock = parseInt(offerDomain.tradeModel.canBookedAmount);
					}
				} catch (e) {}
			}
			ret_data.sku[ret_data.item_id] = {
				pvs: {},
				sku_map: '',
				price: _this.formatPrice(backUpPrice),
				stock: singleStock,
				img: ret_data.pdt_picture[0] || '',
			};
		}

		// 描述属性
		ret_data.detail = _this.get1688DetailAttrs();
		// 视频
		ret_data.video = _this.get1688Video(ret_data.pdt_picture[0] || '');
		// 详情图
		ret_data.desc_picture = [];
		_this.get1688DescPic(ret_data, callback);
	},
	// 1688 window.context.result 新版页面解析
	get16882: function(callback) {
		const _this = this;
		const globalData = window.context.result.global.globalData;
		const model = globalData.model;
		const rootData = (window.context.result.data && window.context.result.data.Root && window.context.result.data.Root.fields && window.context.result.data.Root.fields.dataJson) ? window.context.result.data.Root.fields.dataJson : {};

		let ret_data = {};
		let obj;
		ret_data.channel_id = 6053;
		ret_data.item_id = (model.offerDetail && model.offerDetail.offerId) ? model.offerDetail.offerId : HELPERINIT.isItemPage();
		ret_data.name = (model.offerDetail && model.offerDetail.subject) ? model.offerDetail.subject : _this.get1688DomTitle();
		ret_data.url = _this.getUrl(ret_data.item_id);
		obj = document.querySelector('.logistics-express-price');
		ret_data.post_fee = obj ? (parseFloat(obj.innerText.replace(/[^0-9.]/ig, '')) || 0) : 0;
		ret_data.sale_count = _this.formatSale(model.tradeModel ? (model.tradeModel.saleCount || model.tradeModel.sale_count) : 0);

		// 店铺信息
		const sellerModel = model.sellerModel || {};
		const cleanUrl16882 = _this.clean1688ShopUrl(sellerModel.winportUrl || '');
		const rawShopId16882 = sellerModel.memberId ? sellerModel.memberId.replace('b2b-', '') : (sellerModel.userId || '');
		ret_data.seller = {
			shop_id: rawShopId16882 || _this.extractShopIdFromUrl(cleanUrl16882),
			shop_name: sellerModel.companyName || '',
			shop_url: cleanUrl16882,
			service: _this.get1688ServiceInfo(),
		};

		// 主图
		ret_data.pdt_picture = [];
		if (model.offerDetail && model.offerDetail.imageList) {
			for (let i = 0; i < model.offerDetail.imageList.length; i++) {
				if (model.offerDetail.imageList[i].fullPathImageURI) {
					ret_data.pdt_picture.push(_this.formatImgUrl(model.offerDetail.imageList[i].fullPathImageURI));
				}
			}
		}

		// 规格属性 & SKU
		ret_data.attr = {};
		ret_data.sku = {};
		const skuProps = (rootData.skuModel && rootData.skuModel.skuProps) ? rootData.skuModel.skuProps : (model.tradeModel ? model.tradeModel.skuProps : null);
		const defaultPrice = model.tradeModel ? model.tradeModel.priceDisplay : 0;
		if (skuProps && skuProps.length > 0) {
			for (let i = 0; i < skuProps.length; i++) {
				const item = skuProps[i];
				let value = {};
				for (let j = 0; j < item.value.length; j++) {
					value[j] = {
						name: item.value[j].name,
						img: item.value[j].imageUrl ? _this.formatImgUrl(item.value[j].imageUrl) : '',
					};
				}
				const fid = item.fid !== undefined ? item.fid : i;
				ret_data.attr[fid] = {
					name: item.prop,
					value: value
				};
			}
			const skuMap = (model.tradeModel && model.tradeModel.skuMap) ? model.tradeModel.skuMap : {};
			for (let k in skuMap) {
				const item = skuMap[k];
				const specStr = item.specAttrs || k;
				const attrParts = specStr.split('&gt;');
				let pvs = {};
				let tmpAttr = JSON.parse(JSON.stringify(ret_data.attr));
				let img = '';
				for (let j = 0; j < attrParts.length; j++) {
					if (!attrParts[j]) continue;
					let attrMap = _this.getAttr(attrParts[j], tmpAttr);
					if (!attrMap || !ret_data.attr[attrMap.name]) continue;
					pvs[ret_data.attr[attrMap.name].name] = ret_data.attr[attrMap.name].value[attrMap.value].name;
					if (attrMap.img) {
						img = attrMap.img;
					}
				}
				let price = item.discountPrice !== undefined ? item.discountPrice : (item.price !== undefined ? item.price : defaultPrice);
				const skuId = item.skuId || k;
				ret_data.sku[skuId] = {
					pvs: pvs,
					sku_map: specStr.replace(/&gt;/g, ';'),
					price: _this.formatPrice(price),
					stock: item.canBookCount !== undefined ? parseInt(item.canBookCount) : 0,
					img: img,
				};
			}
		} else if (model.tradeModel) {
			ret_data.sku[ret_data.item_id] = {
				pvs: {},
				sku_map: '',
				price: _this.formatPrice(defaultPrice),
				stock: parseInt(model.tradeModel.canBookedAmount) || 0,
				img: ret_data.pdt_picture[0] || '',
			};
		}

		// 描述属性
		ret_data.detail = [];
		if (model.offerDetail && model.offerDetail.featureAttributes) {
			for (let i = 0; i < model.offerDetail.featureAttributes.length; i++) {
				const attrItem = model.offerDetail.featureAttributes[i];
				ret_data.detail.push({
					name: attrItem.name,
					value: attrItem.value || (attrItem.values ? attrItem.values.join(' ') : '')
				});
			}
		}

		// 视频
		ret_data.video = _this.get1688Video(ret_data.pdt_picture[0] || '');
		// 详情图
		ret_data.desc_picture = [];
		_this.get1688DescPic(ret_data, callback);
	},
	// 1688 旧版 iDetailData 页面解析
	get16883: function(callback) {
		const _this = this;
		let ret_data = {};
		ret_data.channel_id = 6053;
		ret_data.item_id = (typeof iDetailConfig !== 'undefined' && iDetailConfig.offerid) ? iDetailConfig.offerid : HELPERINIT.isItemPage();
		ret_data.name = _this.get1688DomTitle();
		ret_data.url = _this.getUrl(ret_data.item_id);
		let obj = document.querySelector('.logistics-express-price');
		ret_data.post_fee = obj ? (parseFloat(obj.innerText.replace(/[^0-9.]/ig, '')) || 0) : 0;
		ret_data.sale_count = 0;

		let shopName = '';
		let metaNick = document.querySelector('meta[property="og:product:nick"]');
		if (metaNick && metaNick.content) {
			let m = metaNick.content.match(/name=(.*);/);
			if (m) shopName = m[1];
		}
		const cleanUrl16883 = typeof iDetailConfig !== 'undefined' ? _this.clean1688ShopUrl(iDetailConfig.companySiteLink || '') : '';
		const rawShopId16883 = typeof iDetailConfig !== 'undefined' ? (iDetailConfig.memberid || iDetailConfig.userid || '') : '';
		ret_data.seller = {
			shop_id: rawShopId16883 || _this.extractShopIdFromUrl(cleanUrl16883),
			shop_name: shopName,
			shop_url: cleanUrl16883,
			service: _this.get1688ServiceInfo(),
		};

		ret_data.pdt_picture = [];
		let tabImgs = document.querySelectorAll('#dt-tab li');
		for (let i = 0; i < tabImgs.length; i++) {
			let imgdata = tabImgs[i].getAttribute('data-imgs');
			if (imgdata) {
				try {
					let parsed = JSON.parse(imgdata);
					if (parsed.original) ret_data.pdt_picture.push(_this.formatImgUrl(parsed.original));
				} catch (e) {}
			}
		}

		ret_data.attr = {};
		ret_data.sku = {};
		if (iDetailData && iDetailData.sku && iDetailData.sku.skuProps) {
			const skuProp = iDetailData.sku.skuProps;
			for (let i = 0; i < skuProp.length; i++) {
				let value = {};
				for (let j = 0; j < skuProp[i].value.length; j++) {
					value[j] = {
						name: skuProp[i].value[j].name,
						img: skuProp[i].value[j].imageUrl ? _this.formatImgUrl(skuProp[i].value[j].imageUrl) : '',
					};
				}
				ret_data.attr[i] = {
					name: skuProp[i].prop,
					value: value
				};
			}
			const skuMap = iDetailData.sku.skuMap || {};
			const fallbackPrice = (iDetailData.sku.priceRangeOriginal && iDetailData.sku.priceRangeOriginal[0]) ? iDetailData.sku.priceRangeOriginal[0][1] : 0;
			for (let k in skuMap) {
				let item = skuMap[k];
				let skuAttr = k.split('&gt;');
				let pvs = {};
				let tmpAttr = JSON.parse(JSON.stringify(ret_data.attr));
				let img = '';
				for (let j = 0; j < skuAttr.length; j++) {
					if (!skuAttr[j]) continue;
					let attrMap = _this.getAttr(skuAttr[j], tmpAttr);
					if (!attrMap || !ret_data.attr[attrMap.name]) continue;
					pvs[ret_data.attr[attrMap.name].name] = ret_data.attr[attrMap.name].value[attrMap.value].name;
					if (attrMap.img) img = attrMap.img;
				}
				let price = item.discountPrice !== undefined ? item.discountPrice : fallbackPrice;
				ret_data.sku[item.skuId || k] = {
					pvs: pvs,
					sku_map: k.replace(/&gt;/g, ';'),
					price: _this.formatPrice(price),
					stock: parseInt(item.canBookCount) || 0,
					img: img,
				};
			}
		}
		ret_data.detail = _this.get1688DetailAttrs();
		ret_data.video = _this.get1688Video(ret_data.pdt_picture[0] || '');
		ret_data.desc_picture = [];
		_this.get1688DescPic(ret_data, callback);
	},
	get1688DomTitle: function() {
		if (window.context && window.context.result && window.context.result.data && window.context.result.data.productTitle) {
			return window.context.result.data.productTitle.fields.title;
		}
		const el = document.querySelector('#mod-detail-title h1, .title-text');
		return el ? el.innerText.trim() : '';
	},
	clean1688ShopUrl: function(shopUrl) {
		if (!shopUrl) return '';
		if (shopUrl.indexOf('//') === 0) {
			shopUrl = 'https:' + shopUrl;
		}
		let rst = shopUrl.match(/^https:\/\/(.+)\.1688\.com/i);
		if (rst && rst[1]) {
			let res = rst[1].match(/([^\.]+)\.cn$/);
			if (res) {
				shopUrl = shopUrl.replace(res[0], res[1]);
			}
		}
		return shopUrl;
	},
	get1688ServiceInfo: function() {
		const _this = this;
		let service = {};
		const labelMap = {
			'综合服务': 'star',
			'物流时效': 'lgt',
			'退换体验': 'rdf',
			'纠纷解决': 'dspt',
			'品质体验': 'goods',
			'采购咨询': 'cst'
		};
		if (typeof __STORE_DATA !== 'undefined' && __STORE_DATA.components && __STORE_DATA.components['38229149'] && __STORE_DATA.components['38229149'].moduleData) {
			const mod = __STORE_DATA.components['38229149'].moduleData;
			if (mod.appData) {
				if (mod.appData.customerStar) service.star = mod.appData.customerStar;
				if (mod.tpYear) service.year = mod.tpYear;
				for (let i in mod.appData.serviceList) {
					let key = mod.appData.serviceList[i].serviceKey;
					if (key) {
						let shortKey = key.split('_')[0];
						service[shortKey] = mod.appData.serviceList[i].score;
					}
				}
			}
		}
		if (Object.keys(service).length === 0) {
			let liObjs = document.querySelectorAll('.seller-advance-info-right ul li');
			if (liObjs.length > 0) {
				for (let i = 0; i < liObjs.length; i++) {
					let spans = liObjs[i].querySelectorAll('span');
					if (spans.length >= 2) {
						let k = labelMap[spans[0].innerText.trim()] || spans[0].innerText.trim();
						service[k] = spans[1].innerText.trim();
					}
				}
				let basicSpans = document.querySelectorAll('.seller-basic-info span');
				if (basicSpans[2]) {
					service.star = basicSpans[2].innerText.trim();
				}
				let yearObj = document.querySelector('.seller-basic-info a');
				if (yearObj) {
					service.year = yearObj.innerText.replace('年', '').trim();
				}
			}
		}
		if (Object.keys(service).length === 0) {
			let asyncRes = _this.getResponse('server.moduleasyncservice');
			if (asyncRes && asyncRes.appData) {
				if (asyncRes.appData.customerStar) service.star = asyncRes.appData.customerStar;
				if (asyncRes.tpYear) service.year = asyncRes.tpYear;
				for (let j in asyncRes.appData.serviceList) {
					let key = asyncRes.appData.serviceList[j].serviceKey;
					if (key) {
						service[key.split('_')[0]] = asyncRes.appData.serviceList[j].score;
					}
				}
			}
		}
		return service;
	},
	get1688BackupPrice: function(gData) {
		let backUpPrice = 0;
		if (gData.orderParamModel && gData.orderParamModel.orderParam && gData.orderParamModel.orderParam.skuParam) {
			const skuParam = gData.orderParamModel.orderParam.skuParam;
			if (skuParam.skuRangePrices && skuParam.skuRangePrices.length > 0) {
				backUpPrice = skuParam.skuRangePrices[0].price;
			}
		}
		if (!backUpPrice && typeof window.__GLOBAL_DATA !== 'undefined' && window.__GLOBAL_DATA.offerDomain) {
			try {
				let offerDomain = typeof window.__GLOBAL_DATA.offerDomain === 'string' ? JSON.parse(window.__GLOBAL_DATA.offerDomain) : window.__GLOBAL_DATA.offerDomain;
				if (offerDomain.tradeModel && offerDomain.tradeModel.offerPriceModel && offerDomain.tradeModel.offerPriceModel.currentPrices && offerDomain.tradeModel.offerPriceModel.currentPrices.length > 0) {
					backUpPrice = offerDomain.tradeModel.offerPriceModel.currentPrices[0].price;
				} else if (offerDomain.tradeModel && offerDomain.tradeModel.displayPrice) {
					backUpPrice = offerDomain.tradeModel.displayPrice;
				}
			} catch (e) {}
		}
		if (!backUpPrice && gData.skuModel && gData.skuModel.skuPriceScale) {
			let parts = gData.skuModel.skuPriceScale.split('-');
			backUpPrice = parts[parts.length - 1];
		}
		return backUpPrice;
	},
	get1688DetailAttrs: function() {
		let detail = [];
		if (typeof __INIT_DATA !== 'undefined' && __INIT_DATA.data) {
			if (__INIT_DATA.data['1081181309201'] && __INIT_DATA.data['1081181309201'].data) {
				const list = __INIT_DATA.data['1081181309201'].data;
				for (let i = 0; i < list.length; i++) {
					detail.push({name: list[i].name, value: list[i].value});
				}
			} else {
				for (let k in __INIT_DATA.data) {
					const comp = __INIT_DATA.data[k];
					if (comp && Array.isArray(comp.data) && comp.data.length > 0 && comp.data[0].name !== undefined && comp.data[0].value !== undefined) {
						for (let i = 0; i < comp.data.length; i++) {
							detail.push({name: comp.data[i].name, value: comp.data[i].value});
						}
						break;
					}
				}
			}
		}
		if (detail.length === 0) {
			let obj = document.querySelectorAll('.offer-attr-item');
			for (let i = 0; i < obj.length; i++) {
				let nameEl = obj[i].querySelector('.offer-attr-item-name');
				let valEl = obj[i].querySelector('.offer-attr-item-value');
				if (nameEl && valEl) {
					detail.push({name: nameEl.innerText.trim(), value: valEl.innerText.trim()});
				}
			}
		}
		return detail;
	},
	get1688Video: function(coverImg) {
		let videoUrl = '';
		if (typeof window.__GLOBAL_DATA !== 'undefined' && window.__GLOBAL_DATA.offerDomain) {
			try {
				let offerDomain = typeof window.__GLOBAL_DATA.offerDomain === 'string' ? JSON.parse(window.__GLOBAL_DATA.offerDomain) : window.__GLOBAL_DATA.offerDomain;
				if (offerDomain.offerDetail && offerDomain.offerDetail.wirelessVideo && offerDomain.offerDetail.wirelessVideo.videoUrls) {
					const videoUrls = offerDomain.offerDetail.wirelessVideo.videoUrls;
					for (let i in videoUrls) {
						if (videoUrls[i] && videoUrls[i].length) {
							videoUrl = videoUrls[i];
							break;
						}
					}
				}
			} catch (e) {}
		}
		if (!videoUrl && window.context && window.context.result && window.context.result.global && window.context.result.global.globalData) {
			const offerDetail = window.context.result.global.globalData.model.offerDetail;
			if (offerDetail && offerDetail.wirelessVideo && offerDetail.wirelessVideo.videoUrls) {
				videoUrl = offerDetail.wirelessVideo.videoUrls.android || '';
			}
		}
		if (!videoUrl) {
			let obj = document.querySelector('#detail-main-video-content source, #detail-main-video-content video');
			if (obj) {
				videoUrl = obj.getAttribute('src') || '';
			}
		}
		if (videoUrl) {
			return {url: videoUrl, img: coverImg || ''};
		}
		return {};
	},
	get1688DescPic: function(ret_data, callback) {
		const _this = this;
		if (typeof offer_details !== 'undefined' && offer_details.content) {
			let srcArr = offer_details.content.match(/(?<=src=[\'\"])[^\'\"]*(?=[\'\"])/gi);
			if (srcArr) {
				for (let i = 0; i < srcArr.length; i++) {
					if (srcArr[i] && _this.filterIgnoreDesPic(srcArr[i])) {
						ret_data.desc_picture.push(_this.formatImgUrl(srcArr[i]));
					}
				}
			}
		}
		if (ret_data.desc_picture.length === 0) {
			let obj = document.querySelectorAll('.desc-img-loaded, .desc-img-no-load, #detailContentContainer .content-detail img');
			for (let i = 0; i < obj.length; i++) {
				let url = obj[i].getAttribute('data-lazyload-src') || obj[i].getAttribute('src') || obj[i].src;
				if (url && _this.filterIgnoreDesPic(url)) {
					ret_data.desc_picture.push(_this.formatImgUrl(url));
				}
			}
		}
		callback(0, ret_data, '获取成功!');
	},
	getAttr: function(attr, attrArr) {
		for (let k in attrArr) {
			for (let j in attrArr[k]['value']) {
				if (attrArr[k]['value'][j].name == attr || attrArr[k]['value'][j].name.replace(/&gt;$/, '') == attr) {
					var arr = {
						name: k,
						img: attrArr[k]['value'][j].img || '',
						value: j
					};
					delete attrArr[k];
					return arr;
				}
			}
		}
		return false;
	},
	// 淘宝/天猫 新版 pcdetail.data.get / detailRequest 解析
	getTaobao1: function(callback) {
		const _this = this;
		if (!_this.baseInfo || !_this.baseInfo.skuBase) {
			callback(-1, {}, '页面需要处理');
			return false;
		}
		let skuCoreMap;
		if (_this.baseInfo.skuCore && _this.baseInfo.skuCore.sku2info) {
			skuCoreMap = _this.baseInfo.skuCore.sku2info;
		} else if (_this.baseInfo.mockData) {
			let tempData = typeof _this.baseInfo.mockData == 'string' ? JSON.parse(_this.baseInfo.mockData) : _this.baseInfo.mockData;
			if (tempData.skuCore && tempData.skuCore.sku2info) {
				skuCoreMap = tempData.skuCore.sku2info;
			}
		}
		if (!skuCoreMap) {
			callback(-1, {}, '获取SKU价格库存失败');
			return false;
		}
		let obj;
		let ret_data = {};
		ret_data.channel_id = HELPERINIT.getChannelId();
		ret_data.item_id = _this.baseInfo.item.itemId;
		ret_data.name = _this.baseInfo.item.title;
		ret_data.url = _this.getUrl(ret_data.item_id);
		ret_data.post_fee = 0;
		if (_this.baseInfo.componentsVO && _this.baseInfo.componentsVO.deliveryVO && _this.baseInfo.componentsVO.deliveryVO.freight) {
			ret_data.post_fee = _this.baseInfo.componentsVO.deliveryVO.freight.replace(/[^\d]/g, '');
			if (ret_data.post_fee) {
				ret_data.post_fee = parseInt(ret_data.post_fee) / 100;
			} else {
				ret_data.post_fee = 0;
			}
		}
		ret_data.pdt_picture = [];
		if (_this.baseInfo.item.images) {
			for (let i = 0; i < _this.baseInfo.item.images.length; i++) {
				ret_data.pdt_picture.push(_this.formatImgUrl(_this.baseInfo.item.images[i]));
			}
		}
		ret_data.sale_count = _this.formatSale(_this.baseInfo.item.vagueSellCount);
		// 商家信息
		const sellerObj = _this.baseInfo.seller || {};
		const tbShopUrl = sellerObj.pcShopUrl || sellerObj.taoShopUrl || '';
		const tbShopId = sellerObj.shopId || sellerObj.sellerId || sellerObj.userId || _this.extractShopIdFromUrl(tbShopUrl);
		ret_data.seller = {
			shop_id: tbShopId ? String(tbShopId) : '',
			shop_name: sellerObj.shopName || '',
			shop_url: tbShopUrl,
			service: {
				level: sellerObj.creditLevel ? sellerObj.creditLevel : 0
			},
		};
		const evalTypeMap = {'宝贝描述': 'desc', '描述': 'desc', '卖家服务': 'serv', '服务': 'serv', '物流服务': 'post', '物流': 'post'};
		if (_this.baseInfo.seller && _this.baseInfo.seller.evaluates) {
			for (let i = 0; i < _this.baseInfo.seller.evaluates.length; i++) {
				const ev = _this.baseInfo.seller.evaluates[i];
				const sKey = ev.type || evalTypeMap[ev.title] || ev.title;
				if (sKey && ev.score) {
					ret_data.seller.service[sKey] = ev.score.trim();
				}
			}
		}

		ret_data.attr = {};
		if (_this.baseInfo.skuBase.props) {
			for (let i = 0; i < _this.baseInfo.skuBase.props.length; i++) {
				const tmp = _this.baseInfo.skuBase.props[i];
				ret_data.attr[tmp.pid] = {
					name: tmp.name,
					value: {}
				};
				for (let j = 0; j < tmp.values.length; j++) {
					ret_data.attr[tmp.pid].value[tmp.values[j].vid] = {
						name: tmp.values[j].name,
						img: tmp.values[j].image ? _this.formatImgUrl(tmp.values[j].image) : '',
					};
				}
			}
		}
		let skuMap = {};
		if (_this.baseInfo.skuBase.skus) {
			for (let i = 0; i < _this.baseInfo.skuBase.skus.length; i++) {
				const tmp = _this.baseInfo.skuBase.skus[i];
				skuMap[tmp.skuId] = tmp.propPath;
			}
		}
		ret_data.sku = {};
		if (_this.baseInfo.skuBase.skus && _this.baseInfo.skuBase.skus.length > 0) {
			for (let i in skuCoreMap) {
				if (i != '0' && skuMap[i]) {
					const tmp = skuCoreMap[i];
					const attr = skuMap[i].split(';');
					let pvs = {};
					let img = '';
					for (let r in attr) {
						let attr_item = attr[r].split(':');
						if (!ret_data.attr[attr_item[0]] || !ret_data.attr[attr_item[0]].value[attr_item[1]]) {
							continue;
						}
						pvs[ret_data.attr[attr_item[0]].name] = ret_data.attr[attr_item[0]].value[attr_item[1]].name;
						if (ret_data.attr[attr_item[0]].value[attr_item[1]].img) {
							img = ret_data.attr[attr_item[0]].value[attr_item[1]].img;
						}
					}
					ret_data.sku[i] = {
						pvs: pvs,
						sku_map: skuMap[i],
						price: _this.formatPrice(tmp.price ? tmp.price.priceText : 0),
						stock: parseInt(tmp.quantity) || 0,
						img: img,
					};
				}
			}
		} else if (skuCoreMap[0]) {
			ret_data.sku[ret_data.item_id] = {
				pvs: {},
				sku_map: '',
				price: _this.formatPrice(skuCoreMap[0].price ? skuCoreMap[0].price.priceText : 0),
				stock: parseInt(skuCoreMap[0].quantity) || 0,
				img: ret_data.pdt_picture[0] || '',
			};
		}
		ret_data.detail = [];
		ret_data.video = {};
		if (_this.baseInfo.componentsVO) {
			// 描述
			if (_this.baseInfo.componentsVO.extensionInfoVO && _this.baseInfo.componentsVO.extensionInfoVO.infos) {
				for (let i = 0; i < _this.baseInfo.componentsVO.extensionInfoVO.infos.length; i++) {
					const infoBlock = _this.baseInfo.componentsVO.extensionInfoVO.infos[i];
					if ((infoBlock.type == 'BASE_PROPS' || infoBlock.title == '参数') && infoBlock.items) {
						const tmp = infoBlock.items;
						for (let j = 0; j < tmp.length; j++) {
							ret_data.detail.push({
								name: tmp[j].title,
								value: Array.isArray(tmp[j].text) ? tmp[j].text.join(' ') : String(tmp[j].text || '')
							});
						}
					}
				}
			}
			// video
			if (_this.baseInfo.componentsVO.headImageVO && _this.baseInfo.componentsVO.headImageVO.videos && _this.baseInfo.componentsVO.headImageVO.videos.length > 0) {
				ret_data.video = {
					url: _this.baseInfo.componentsVO.headImageVO.videos[0].url,
					img: _this.formatImgUrl(_this.baseInfo.componentsVO.headImageVO.videos[0].videoThumbnailURL || '')
				};
			}
		}
		//描述图片
		ret_data.desc_picture = [];
		let count = 0;
		const internalId = setInterval(function() {
			let tmp = _this.getResponse('detail.getdesc') || _this.getResponse('detail.getDwdetailDesc');
			if (tmp && tmp.components && tmp.components.componentData) {
				if (tmp.components.componentData.desc_richtext_pc && tmp.components.componentData.desc_richtext_pc.model && tmp.components.componentData.desc_richtext_pc.model.text) {
					let matchArr = tmp.components.componentData.desc_richtext_pc.model.text.match(/(?<=src=[\'\"])([^\'\"]*)(?=[\'\"])/g);
					if (matchArr) {
						for (let i = 0; i < matchArr.length; i++) {
							if (matchArr[i].indexOf('img.alicdn.com') > -1 && _this.filterIgnoreDesPic(matchArr[i])) {
								ret_data.desc_picture.push(_this.formatImgUrl(matchArr[i]));
							}
						}
					}
				} else {
					for (let i in tmp.components.componentData) {
						if (i.indexOf('detail_pic') >= 0 && tmp.components.componentData[i]) {
							let pic = '';
							if (typeof tmp.components.componentData[i] == 'object') {
								pic = tmp.components.componentData[i].model ? tmp.components.componentData[i].model.picUrl : '';
							} else {
								pic = tmp.components.componentData[i];
							}
							if (pic && pic.indexOf('img.alicdn.com') > -1 && _this.filterIgnoreDesPic(pic)) {
								ret_data.desc_picture.push(_this.formatImgUrl(pic));
							}
						}
					}
				}
			}
			if (ret_data.desc_picture.length == 0) {
				obj = document.querySelectorAll('.desc-root .descV8-container img, #description .content img, .descV8-singleImage>img');
				for (let i = 0; i < obj.length; i++) {
					let url = obj[i].getAttribute('data-src') || obj[i].getAttribute('src') || obj[i].src;
					if (url && url.indexOf('img.alicdn.com') >= 0 && _this.filterIgnoreDesPic(url)) {
						ret_data.desc_picture.push(_this.formatImgUrl(url));
					}
				}
			}
			if (ret_data.desc_picture.length > 0 || count > 10) {
				clearInterval(internalId);
				callback(0, ret_data, '获取成功!');
			}
			count++;
		}, 300);
	},
	// 淘宝旧版 g_config.idata 页面解析
	getTaobao2: function(callback) {
		const _this = this;
		let obj;
		let ret_data = {};
		ret_data.channel_id = HELPERINIT.getChannelId();
		ret_data.item_id = g_config.idata.item.id;
		ret_data.name = g_config.idata.item.title;
		ret_data.url = _this.getUrl(ret_data.item_id);
		obj = document.querySelector('#J_WlServiceTitle');
		ret_data.post_fee = obj ? (parseFloat(obj.innerText.replace(/[^0-9.]/ig, '')) || 0) : 0;
		ret_data.sale_count = 0;
		if (typeof g_config.soldQuantity !== 'undefined') {
			if (typeof g_config.soldQuantity.confirmGoodsCount !== 'undefined') {
				ret_data.sale_count = _this.formatSale(g_config.soldQuantity.confirmGoodsCount);
			} else if (typeof g_config.soldQuantity.soldTotalCount !== 'undefined') {
				ret_data.sale_count = _this.formatSale(g_config.soldQuantity.soldTotalCount);
			}
		}
		ret_data.pdt_picture = [];
		obj = document.querySelectorAll('#J_UlThumb li');
		for (let i = 0; i < obj.length; i++) {
			let tempObj = obj[i].querySelector('img');
			if (tempObj) {
				let imgSrc = tempObj.getAttribute('data-src') || tempObj.getAttribute('src') || '';
				if (imgSrc) {
					ret_data.pdt_picture.push(_this.formatImgUrl(imgSrc.replace('_50x50.jpg', '')));
				}
			}
		}
		const tb2ShopUrl = (g_config.idata.shop && g_config.idata.shop.url) ? g_config.idata.shop.url : (g_config.shopUrl || '');
		const tb2ShopId = g_config.shopId || g_config.sellerId || (g_config.idata.shop && g_config.idata.shop.id) || _this.extractShopIdFromUrl(tb2ShopUrl);
		ret_data.seller = {
			shop_id: tb2ShopId ? String(tb2ShopId) : '',
			shop_name: g_config.shopName || '',
			shop_url: tb2ShopUrl,
			service: {},
		};
		obj = document.querySelector('.tb-shop-info, #J_ShopInfo');
		if (obj) {
			ret_data.seller.service.level = _this.getLevel(obj.getAttribute('data-creditflag'), obj.querySelectorAll('.tb-shop-rank dd i').length);
		}
		obj = document.querySelectorAll('.tb-shop-rate dl, #J_ShopInfo .tb-shop-info-bd dl');
		const descMap = {'描述': 'desc', '物流': 'post', '服务': 'serv'};
		for (let i = 0; i < obj.length; i++) {
			let dtEl = obj[i].querySelector('dt');
			let ddEl = obj[i].querySelector('dd');
			if (dtEl && ddEl) {
				let name = dtEl.innerText.trim();
				let value = ddEl.innerText.trim();
				if (descMap[name]) {
					ret_data.seller.service[descMap[name]] = value;
				}
			}
		}
		ret_data.attr = {};
		let attrObj = document.querySelectorAll('dl.J_Prop');
		for (let k = 0; k < attrObj.length; k++) {
			let attrName = attrObj[k].querySelector('dt').innerText.trim();
			let attrValue = {};
			let valueList = attrObj[k].querySelector('dd').getElementsByTagName('li');
			let attrNameId = 0;
			for (let j = 0; j < valueList.length; j++) {
				let value = valueList[j].attributes['data-value'].nodeValue.split(':');
				if (!attrNameId) {
					attrNameId = value[0];
				}
				let name = valueList[j].children[0].children[0].innerText.trim();
				let img = '';
				if (typeof valueList[j].children[0].attributes['style'] !== 'undefined') {
					let styleText = valueList[j].children[0].attributes['style'].nodeValue;
					let imgMatch = styleText.match(/background\:url\(((.+))_30x30/);
					if (imgMatch && imgMatch[1]) {
						img = _this.formatImgUrl(imgMatch[1]);
					}
				}
				attrValue[value[1]] = {
					name: name,
					img: img,
				};
			}
			ret_data.attr[attrNameId] = {
				name: attrName,
				value: attrValue
			};
		}
		ret_data.sku = {};
		if (typeof Hub !== 'undefined' && Hub.config && Hub.config.config && Hub.config.config.sku && Hub.config.config.sku.valItemInfo && Hub.config.config.sku.valItemInfo.skuMap) {
			const skuMap = Hub.config.config.sku.valItemInfo.skuMap;
			for (let k in skuMap) {
				let item = skuMap[k];
				if (item.skuId != '0') {
					let tempMap = k.substr(1, k.length - 2);
					let attr = tempMap.split(';');
					let pvs = {};
					let img = '';
					for (let r in attr) {
						let attr_item = attr[r].split(':');
						if (!ret_data.attr[attr_item[0]] || !ret_data.attr[attr_item[0]].value[attr_item[1]]) continue;
						pvs[ret_data.attr[attr_item[0]].name] = ret_data.attr[attr_item[0]].value[attr_item[1]].name;
						if (ret_data.attr[attr_item[0]].value[attr_item[1]].img) {
							img = ret_data.attr[attr_item[0]].value[attr_item[1]].img;
						}
					}
					let price = item.price;
					if (g_config.promotion && g_config.promotion.promoData && g_config.promotion.promoData[k]) {
						price = g_config.promotion.promoData[k][0].price;
					} else if (g_config.originalPrice && g_config.originalPrice[k]) {
						price = g_config.originalPrice[k].price;
					}
					let stock = (g_config.dynStock && g_config.dynStock.sku && g_config.dynStock.sku[k]) ? g_config.dynStock.sku[k].stock : item.stock;
					ret_data.sku[item.skuId] = {
						pvs: pvs,
						sku_map: tempMap,
						price: _this.formatPrice(price),
						stock: parseInt(stock) || 0,
						img: img,
					};
				}
			}
		} else {
			let price = g_config.price || 0;
			if (!price) {
				let priceObj = document.querySelector('#J_StrPrice .tb-rmb-num');
				if (priceObj) price = priceObj.innerText;
			}
			let stock = (g_config.dynStock && g_config.dynStock.stock) ? g_config.dynStock.stock : 0;
			if (!stock) {
				let stockObj = document.querySelector('#J_SpanStock');
				if (stockObj) stock = stockObj.innerText;
			}
			ret_data.sku[ret_data.item_id] = {
				pvs: {},
				sku_map: '',
				price: _this.formatPrice(price),
				stock: parseInt(stock) || 0,
				img: ret_data.pdt_picture[0] || '',
			};
		}
		// 描述
		ret_data.detail = [];
		ret_data.video = {};
		obj = document.querySelectorAll('.attributes-list li');
		for (let i = 0; i < obj.length; i++) {
			let item = obj[i].innerText.split(/:\s*|：\s*/);
			if (item.length >= 2) {
				ret_data.detail.push({name: item[0].trim(), value: item.slice(1).join(':').trim()});
			}
		}
		ret_data.desc_picture = [];
		_this.extractDescScriptPic(g_config.descUrl, ret_data, callback);
	},
	// 天猫旧版 KISSY 1.42 / TShop.Setup 解析
	getTmallOld: function(callback) {
		const _this = this;
		let obj = document.querySelectorAll('script');
		let reg = /TShop\.Setup\(\n.*/;
		let data;
		for (let i = 0; i < obj.length; i++) {
			let text = obj[i].innerText;
			if (text.indexOf('TShop.Setup') >= 0) {
				let m = text.match(reg);
				if (m && m[0]) {
					text = m[0].replace('TShop.Setup(\n\t  \t', '').trim().replace(/\);?$/, '');
					try {
						data = JSON.parse(text);
					} catch (e) {}
				}
				break;
			}
		}
		if (!data || !data.itemDO) {
			_this.fetchTaobaoOrTmall(callback);
			return;
		}
		let ret_data = {};
		ret_data.channel_id = HELPERINIT.getChannelId();
		ret_data.item_id = data.itemDO.itemId;
		ret_data.name = data.itemDO.title;
		ret_data.url = _this.getUrl(ret_data.item_id);
		ret_data.post_fee = 0;
		let sellEl = document.querySelector('.tm-ind-sellCount .tm-count');
		ret_data.sale_count = sellEl ? _this.formatSale(sellEl.innerText) : 0;

		let shopNameEl = document.querySelector('a.slogo-shopname>strong');
		let service = {};
		const descMap = {'描述': 'desc', '宝贝描述': 'desc', '物流': 'post', '物流服务': 'post', '服务': 'serv', '卖家服务': 'serv'};
		let dsrEls = document.querySelectorAll('#shop-info .shopdsr-item');
		for (let i = 0; i < dsrEls.length; i++) {
			let tEl = dsrEls[i].querySelector('.shopdsr-title');
			let sEl = dsrEls[i].querySelector('.shopdsr-score');
			if (tEl && sEl) {
				let k = descMap[tEl.innerText.replace(/\s+/g, '')] || tEl.innerText.trim();
				service[k] = sEl.innerText.trim();
			}
		}
		const tmShopUrl = typeof g_config !== 'undefined' ? (g_config.shopUrl || '') : '';
		const tmShopId = (typeof g_config !== 'undefined' ? (g_config.shopId || g_config.sellerId || '') : '') || (data.itemDO && data.itemDO.userId ? data.itemDO.userId : '') || _this.extractShopIdFromUrl(tmShopUrl);
		ret_data.seller = {
			shop_id: tmShopId ? String(tmShopId) : '',
			shop_name: shopNameEl ? shopNameEl.innerText.trim() : '',
			shop_url: tmShopUrl,
			service: service,
		};

		ret_data.pdt_picture = [];
		if (data.propertyPics && data.propertyPics.default) {
			for (let i = 0; i < data.propertyPics.default.length; i++) {
				ret_data.pdt_picture.push(_this.formatImgUrl(data.propertyPics.default[i]));
			}
		} else {
			let thumbEls = document.querySelectorAll('#J_UlThumb li img');
			for (let i = 0; i < thumbEls.length; i++) {
				ret_data.pdt_picture.push(_this.formatImgUrl(thumbEls[i].src.replace('_60x60q90.jpg', '')));
			}
		}

		let attr = {}, attrValue = {}, attrValueImg = {};
		let propDls = document.querySelectorAll('dl.tm-sale-prop');
		for (let i = 0; i < propDls.length; i++) {
			const title = propDls[i].querySelector('dt.tb-metatit');
			if (title) {
				const attrValueObj = propDls[i].querySelectorAll('ul li');
				for (let j = 0; j < attrValueObj.length; j++) {
					const dv = attrValueObj[j].getAttribute('data-value');
					if (!dv) continue;
					const attrData = dv.split(':');
					if (j === 0) {
						attr[attrData[0]] = {
							name: title.innerText.trim(),
							value: {}
						};
					}
					attrValue[attrData[1]] = attrValueObj[j].innerText.trim();
					let aEl = attrValueObj[j].querySelector('a');
					let style = aEl ? aEl.getAttribute('style') : '';
					if (style && style.match(/background:url\(.*\)/)) {
						attrValueImg[attrData[1]] = _this.formatImgUrl(style.match(/background:url\(.*\)/)[0].replace('background:url(', '').replace(')', ''));
					} else {
						attrValueImg[attrData[1]] = '';
					}
					attr[attrData[0]].value[attrData[1]] = {
						name: attrValue[attrData[1]],
						img: attrValueImg[attrData[1]]
					};
				}
			}
		}
		ret_data.attr = attr;
		ret_data.sku = {};
		if (data.itemDO.hasSku && data.valItemInfo && data.valItemInfo.skuMap) {
			const skuData = data.valItemInfo.skuMap;
			for (let k in skuData) {
				const item = skuData[k];
				const tempMap = k.replace(/^;|;$/g, '');
				const attrData = tempMap.split(';');
				let pvs = {};
				let img = '';
				for (let j = 0; j < attrData.length; j++) {
					if (attrData[j]) {
						const attribute = attrData[j].split(':');
						if (attr[attribute[0]]) {
							pvs[attr[attribute[0]].name] = attrValue[attribute[1]] || '';
							if (attrValueImg[attribute[1]]) {
								img = attrValueImg[attribute[1]];
							}
						}
					}
				}
				ret_data.sku[item.skuId] = {
					pvs: pvs,
					sku_map: tempMap,
					price: _this.formatPrice(item.price),
					stock: parseInt(item.stock) || 0,
					img: img,
				};
			}
		} else {
			let priceObj = document.querySelector('#J_PromoPrice .tm-price');
			let stockObj = document.querySelector('#J_EmStock');
			ret_data.sku[ret_data.item_id] = {
				pvs: {},
				sku_map: '',
				price: _this.formatPrice(priceObj ? priceObj.innerText : 0),
				stock: stockObj ? (parseInt(stockObj.innerText.replace(/[^0-9]/g, '')) || 0) : 0,
				img: ret_data.pdt_picture[0] || '',
			};
		}
		ret_data.detail = [];
		ret_data.video = {};
		let attrLi = document.querySelectorAll('ul#J_AttrUL>li');
		for (let i = 0; i < attrLi.length; i++) {
			let item = attrLi[i].innerText.split(/:\s*|：\s*/);
			if (item.length >= 2) {
				ret_data.detail.push({name: item[0].trim(), value: item.slice(1).join(':').trim()});
			}
		}
		ret_data.desc_picture = [];
		_this.extractDescScriptPic(data.api ? (data.api.httpsDescUrl || data.api.descUrl) : '', ret_data, callback);
	},
	extractShopIdFromUrl: function(url) {
		if (!url) return '';
		const clean = String(url).replace(/^(https?:)?\/\//i, '').replace(/\/.*$/, '').trim();
		let m = clean.match(/^shop(\d+)\.(?:m\.)?(?:taobao|tmall)\.com/i);
		if (m && m[1]) return m[1];
		m = String(url).match(/[?&]shop_?id=(\d+)/i);
		if (m && m[1]) return m[1];
		m = clean.match(/^([a-z0-9_-]+)\.(?:m\.)?(?:1688|taobao|tmall)\.com/i);
		if (m && m[1] && ['www', 'detail', 'item', 'winport', 'shop', 'm', 'h5'].indexOf(m[1].toLowerCase()) === -1) {
			return m[1];
		}
		return '';
	},
	extractDescScriptPic: function(desUrl, ret_data, callback) {
		const _this = this;
		if (typeof desc !== 'undefined' && desc) {
			let des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
			if (des_pic_craw) {
				for (let i = 0; i < des_pic_craw.length; i++) {
					let m = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/);
					if (m && m[1] && _this.filterIgnoreDesPic(m[1])) {
						ret_data.desc_picture.push(_this.formatImgUrl(m[1]));
					}
				}
			}
		}
		if (ret_data.desc_picture.length === 0) {
			let obj = document.querySelectorAll('.descV8-container .descV8-singleImage>img, #description .content img');
			for (let i = 0; i < obj.length; i++) {
				let src = obj[i].getAttribute('data-src') || obj[i].src;
				if (src && _this.filterIgnoreDesPic(src)) {
					ret_data.desc_picture.push(_this.formatImgUrl(src));
				}
			}
		}
		callback(0, ret_data, '获取成功!');
	},
	filterIgnoreDesPic: function(src) {
		if (!src) return false;
		const ignore = ['img.taobao.com', 'ma.m.1688.com', 'amos.alicdn.com', 'alisoft.com', 'add_to_favorites.htm', 'img.alicdn.com/NewGualianyingxiao', 'spaceball.gif'];
		for (let i = 0; i < ignore.length; i++) {
			if (src.indexOf(ignore[i]) !== -1) {
				return false;
			}
		}
		return true;
	},
	formatImgUrl: function(url) {
		if (!url || typeof url !== 'string') return '';
		url = url.trim();
		if (url.indexOf('//') === 0) {
			url = 'https:' + url;
		}
		return url.replace(/_.webp$/i, '');
	},
	formatPrice: function(price) {
		if (price === undefined || price === null || price === '') return 0;
		if (typeof price === 'number') return price;
		let str = String(price).trim();
		if (str.indexOf('-') > -1) {
			str = str.split('-')[0].trim();
		}
		let num = parseFloat(str.replace(/[^\d.]/g, ''));
		return isNaN(num) ? 0 : num;
	},
	getLevel: function(flag, number) {
		var level = 0;
		switch (flag) {
			case 'blue':
			case '钻级':
				level = 5;
				break;
			case 'cap':
			case 'cap1':
			case '冠级':
				level = 10;
				break;
			case 'crown':
				level = 15;
				break;
		}
		return level + number;
	},
	getUrl: function(itemId) {
		switch (HELPERINIT.getDomain()) {
			case 'taobao.com':
				return 'https://item.taobao.com/item.htm?id=' + itemId;
			case 'tmall.com':
				return 'https://detail.tmall.com/item.htm?id=' + itemId;
			case '1688.com':
				return 'https://detail.1688.com/offer/' + itemId + '.html';
			default:
				return '';
		}
	},
	ajax: function(params) {
		params = params || {};
		params.data = params.data || {};
		var json = params.dataType === 'jsonp' ? jsonp(params) : json(params);
		// jsonp请求
		function jsonp(params) {
			//创建script标签并加入到页面中
			var callbackName = params.jsonp ? params.jsonp : 'jsonp_' + random();
			var head = document.getElementsByTagName('head')[0];
			// 设置传递给后台的回调参数名
			params.data['callback'] = callbackName;
			var data = formatParams(params.data);
			var script = document.createElement('script');
			head.appendChild(script);
			//创建jsonp回调函数
			window[callbackName] = function(json) {
				head.removeChild(script);
				clearTimeout(script.timer);
				window[callbackName] = null;
				params.success && params.success(json);
			};
			//发送请求
			script.src = params.url + (data ? '?' + data : '');
			//为了得知此次请求是否成功，设置超时处理
			if (params.time) {
				script.timer = setTimeout(function() {
					window[callbackName] = null;
					head.removeChild(script);
					params.error && params.error({
						message: '超时'
					});
				}, params.time);
			}
		}
		//普通json请求
		function json(params) {
			params.type = (params.type || 'GET').toUpperCase();
			params.data = formatParams(params.data);
			var xhr = new XMLHttpRequest();
			xhr.withCredentials = true;
			xhr.onreadystatechange = function() {
				if (xhr.readyState == 4) {
					var status = xhr.status;
					if (status >= 200 && status < 300) {
						var response = '';
						var type = xhr.getResponseHeader('Content-type') || '';
						if (type.indexOf('xml') !== -1 && xhr.responseXML) {
							response = xhr.responseXML; //Document对象响应
						} else if (type.indexOf('application/json') !== -1) {
							response = JSON.parse(xhr.responseText); //JSON响应
						} else {
							response = xhr.responseText; //字符串响应
						}
						params.success && params.success(response);
					} else {
						params.error && params.error(status);
					}
				}
			};
			//请求方式，默认是GET
			if (params.type == 'GET') {
				xhr.open(params.type, params.url + (params.data ? '?' + params.data : ''), true);
				xhr.send(null);
			} else {
				xhr.open(params.type, params.url, true);
				xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
				xhr.send(params.data);
			}
		}
		//格式化参数方法
		function formatParams(data) {
			var arr = [];
			for (var name in data) {
				arr.push(encodeURIComponent(name) + '=' + encodeURIComponent(data[name]));
			}
			return arr.join('&');
		}
		// 获取随机数方法 
		function random() {
			return Math.floor(Math.random() * 10000 + 500);
		}
	},
	getCookie: function(key) {
		var cookieArr = document.cookie.split(';');
		for (var i in cookieArr) {
			if (typeof cookieArr[i] == 'string') {
				var splitArr = cookieArr[i].trim().split('=');
				if (splitArr[0] == key) {
					return splitArr[1];
				}
			}
		}
		return false;
	},
	hex_md5: function(a) {
		function b(a, b) {
			return a << b | a >>> 32 - b
		}
		function c(a, b) {
			var c, d, e, f, g;
			return e = 2147483648 & a,
			f = 2147483648 & b,
			c = 1073741824 & a,
			d = 1073741824 & b,
			g = (1073741823 & a) + (1073741823 & b),
			c & d ? 2147483648 ^ g ^ e ^ f : c | d ? 1073741824 & g ? 3221225472 ^ g ^ e ^ f : 1073741824 ^ g ^ e ^ f : g ^ e ^ f
		}
		function d(a, b, c) {
			return a & b | ~a & c
		}
		function e(a, b, c) {
			return a & c | b & ~c
		}
		function f(a, b, c) {
			return a ^ b ^ c
		}
		function g(a, b, c) {
			return b ^ (a | ~c)
		}
		function h(a, e, f, g, h, i, j) {
			return a = c(a, c(c(d(e, f, g), h), j)),
			c(b(a, i), e)
		}
		function i(a, d, f, g, h, i, j) {
			return a = c(a, c(c(e(d, f, g), h), j)),
			c(b(a, i), d)
		}
		function j(a, d, e, g, h, i, j) {
			return a = c(a, c(c(f(d, e, g), h), j)),
			c(b(a, i), d)
		}
		function k(a, d, e, f, h, i, j) {
			return a = c(a, c(c(g(d, e, f), h), j)),
			c(b(a, i), d)
		}
		function l(a) {
			for (var b, c = a.length, d = c + 8, e = (d - d % 64) / 64, f = 16 * (e + 1), g = new Array(f - 1), h = 0, i = 0; c > i; )
				b = (i - i % 4) / 4,
				h = i % 4 * 8,
				g[b] = g[b] | a.charCodeAt(i) << h,
				i++;
			return b = (i - i % 4) / 4,
			h = i % 4 * 8,
			g[b] = g[b] | 128 << h,
			g[f - 2] = c << 3,
			g[f - 1] = c >>> 29,
			g
		}
		function m(a) {
			var b, c, d = "", e = "";
			for (c = 0; 3 >= c; c++)
				b = a >>> 8 * c & 255,
				e = "0" + b.toString(16),
				d += e.substr(e.length - 2, 2);
			return d
		}
		function n(a) {
			a = a.replace(/\r\n/g, "\n");
			for (var b = "", c = 0; c < a.length; c++) {
				var d = a.charCodeAt(c);
				128 > d ? b += String.fromCharCode(d) : d > 127 && 2048 > d ? (b += String.fromCharCode(d >> 6 | 192),
				b += String.fromCharCode(63 & d | 128)) : (b += String.fromCharCode(d >> 12 | 224),
				b += String.fromCharCode(d >> 6 & 63 | 128),
				b += String.fromCharCode(63 & d | 128))
			}
			return b
		}
		var o, p, q, r, s, t, u, v, w, x = [], y = 7, z = 12, A = 17, B = 22, C = 5, D = 9, E = 14, F = 20, G = 4, H = 11, I = 16, J = 23, K = 6, L = 10, M = 15, N = 21;
		for (a = n(a),
		x = l(a),
		t = 1732584193,
		u = 4023233417,
		v = 2562383102,
		w = 271733878,
		o = 0; o < x.length; o += 16)
			p = t,
			q = u,
			r = v,
			s = w,
			t = h(t, u, v, w, x[o + 0], y, 3614090360),
			w = h(w, t, u, v, x[o + 1], z, 3905402710),
			v = h(v, w, t, u, x[o + 2], A, 606105819),
			u = h(u, v, w, t, x[o + 3], B, 3250441966),
			t = h(t, u, v, w, x[o + 4], y, 4118548399),
			w = h(w, t, u, v, x[o + 5], z, 1200080426),
			v = h(v, w, t, u, x[o + 6], A, 2821735955),
			u = h(u, v, w, t, x[o + 7], B, 4249261313),
			t = h(t, u, v, w, x[o + 8], y, 1770035416),
			w = h(w, t, u, v, x[o + 9], z, 2336552879),
			v = h(v, w, t, u, x[o + 10], A, 4294925233),
			u = h(u, v, w, t, x[o + 11], B, 2304563134),
			t = h(t, u, v, w, x[o + 12], y, 1804603682),
			w = h(w, t, u, v, x[o + 13], z, 4254626195),
			v = h(v, w, t, u, x[o + 14], A, 2792965006),
			u = h(u, v, w, t, x[o + 15], B, 1236535329),
			t = i(t, u, v, w, x[o + 1], C, 4129170786),
			w = i(w, t, u, v, x[o + 6], D, 3225465664),
			v = i(v, w, t, u, x[o + 11], E, 643717713),
			u = i(u, v, w, t, x[o + 0], F, 3921069994),
			t = i(t, u, v, w, x[o + 5], C, 3593408605),
			w = i(w, t, u, v, x[o + 10], D, 38016083),
			v = i(v, w, t, u, x[o + 15], E, 3634488961),
			u = i(u, v, w, t, x[o + 4], F, 3889429448),
			t = i(t, u, v, w, x[o + 9], C, 568446438),
			w = i(w, t, u, v, x[o + 14], D, 3275163606),
			v = i(v, w, t, u, x[o + 3], E, 4107603335),
			u = i(u, v, w, t, x[o + 8], F, 1163531501),
			t = i(t, u, v, w, x[o + 13], C, 2850285829),
			w = i(w, t, u, v, x[o + 2], D, 4243563512),
			v = i(v, w, t, u, x[o + 7], E, 1735328473),
			u = i(u, v, w, t, x[o + 12], F, 2368359562),
			t = j(t, u, v, w, x[o + 5], G, 4294588738),
			w = j(w, t, u, v, x[o + 8], H, 2272392833),
			v = j(v, w, t, u, x[o + 11], I, 1839030562),
			u = j(u, v, w, t, x[o + 14], J, 4259657740),
			t = j(t, u, v, w, x[o + 1], G, 2763975236),
			w = j(w, t, u, v, x[o + 4], H, 1272893353),
			v = j(v, w, t, u, x[o + 7], I, 4139469664),
			u = j(u, v, w, t, x[o + 10], J, 3200236656),
			t = j(t, u, v, w, x[o + 13], G, 681279174),
			w = j(w, t, u, v, x[o + 0], H, 3936430074),
			v = j(v, w, t, u, x[o + 3], I, 3572445317),
			u = j(u, v, w, t, x[o + 6], J, 76029189),
			t = j(t, u, v, w, x[o + 9], G, 3654602809),
			w = j(w, t, u, v, x[o + 12], H, 3873151461),
			v = j(v, w, t, u, x[o + 15], I, 530742520),
			u = j(u, v, w, t, x[o + 2], J, 3299628645),
			t = k(t, u, v, w, x[o + 0], K, 4096336452),
			w = k(w, t, u, v, x[o + 7], L, 1126891415),
			v = k(v, w, t, u, x[o + 14], M, 2878612391),
			u = k(u, v, w, t, x[o + 5], N, 4237533241),
			t = k(t, u, v, w, x[o + 12], K, 1700485571),
			w = k(w, t, u, v, x[o + 3], L, 2399980690),
			v = k(v, w, t, u, x[o + 10], M, 4293915773),
			u = k(u, v, w, t, x[o + 1], N, 2240044497),
			t = k(t, u, v, w, x[o + 8], K, 1873313359),
			w = k(w, t, u, v, x[o + 15], L, 4264355552),
			v = k(v, w, t, u, x[o + 6], M, 2734768916),
			u = k(u, v, w, t, x[o + 13], N, 1309151649),
			t = k(t, u, v, w, x[o + 4], K, 4149444226),
			w = k(w, t, u, v, x[o + 11], L, 3174756917),
			v = k(v, w, t, u, x[o + 2], M, 718787259),
			u = k(u, v, w, t, x[o + 9], N, 3951481745),
			t = c(t, p),
			u = c(u, q),
			v = c(v, r),
			w = c(w, s);
		var O = m(t) + m(u) + m(v) + m(w);
		return O.toLowerCase()
	},
	getResponse: function(key) {
		if (!window.customerAjaxRespone) {
			return false;
		}
		for (let i = 0; i < window.customerAjaxRespone.length; i++) {
			if (window.customerAjaxRespone[i] && window.customerAjaxRespone[i].api && window.customerAjaxRespone[i].api.indexOf(key) > -1) {
				if (JSON.stringify(window.customerAjaxRespone[i].data) != '{}') {
					return window.customerAjaxRespone[i].data;
				}
			}
		}
		return false;
	},
	formatSale: function(str) {
		if (!str) {
			return 0;
		}
		str = String(str).replace(/(^\s*)|(\s*$)/g, '').replace('十万', '00000').replace('万', '0000').replace('千', '000').replace('百', '00').replace(/[\<\+]/g, '').replace(/[^0-9]/ig, '');
		return parseInt(str) || 0;
	}
};