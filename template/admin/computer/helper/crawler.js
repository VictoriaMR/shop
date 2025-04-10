var CRAWLER = {
	init: function(callback){
		const _this = this;
		_this.intervalId = setInterval(function(){
			if (!HELPERINIT.isItemPage()) {
				clearInterval(_this.intervalId);
				callback(-1, {}, '非产品详情页面!');
			} else if (HELPERINIT.isLoginPage()) {
				clearInterval(_this.intervalId);
				callback(-2, {}, '页面需要登录!');
			} else if (HELPERINIT.isVerifyPage()) {
				clearInterval(_this.intervalId);
				callback(-3, {}, '页面需要验证!');
			} else if (HELPERINIT.isDenyPage()) {
				clearInterval(_this.intervalId);
				callback(-4, {}, '页面被阻止访问!');
			} else if (HELPERINIT.isErrorPage()) {
				clearInterval(_this.intervalId);
				callback(-5, {}, HELPERINIT.isErrorPage());
			} else if (_this.isOffShelf()) {
				clearInterval(_this.intervalId);
				callback(-6, {}, '产品已下架!');
			} else {
				_this.data(function(code, data, msg) {
					clearInterval(CRAWLER.intervalId);
					localStorage.setItem('CRAWLER_DATA', JSON.stringify(data));
					callback(code, data, msg);
				});
			}
		}, 500);
	},
	isOffShelf: function() {
		let obj = document.querySelector('.mod-detail-offline .mod-detail-offline-title');
		if (!obj) {
			obj = document.querySelector('.tb-off-sale .tb-hint strong');
		}
		if (!obj) {
			obj = document.querySelector('.sold-out-recommend');
		}
		return obj;
	},
	data: function(callback) {
		const _this = this;
		switch (HELPERINIT.getDomain()) {
			case '1688.com':
				if (typeof __INIT_DATA !== 'undefined') {
					_this.get16881(callback);
				} else if (window.context && window.context.result && window.context.result.global && window.context.result.global.globalData) {
					_this.get16882(callback);
				} else {
					callback(-8, {}, '获取数据失败!');
				}
				break;
			case 'taobao.com':
			case 'tmall.com':
				if (g_config && g_config.idata) {
					_this.getTaobao2(callback);
				} else {
					//查询请求池是否有数据
					let tmp = _this.getResponse('pcdetail.data.get');
					if (tmp) {
						clearInterval(_this.intervalId);
						_this.baseInfo = tmp;
						_this.getTaobao1(callback);
					}
				}
				break;
			default:
				callback(-9, {}, '未知渠道商品详情页面');
				break;
		}
	},
	get16881: function(callback) {
		let ret_data = {};
		let obj;
		ret_data.channel_id = 6053;
		ret_data.item_id = __INIT_DATA.globalData.tempModel.offerId;
		ret_data.name = __INIT_DATA.globalData.tempModel.offerTitle;
		ret_data.url = this.getUrl(ret_data.item_id);
		obj = document.querySelector('.logistics-express-price');
		ret_data.post_fee = obj ? obj.innerText.replace(/[^0-9]/ig, '') : 0;
		ret_data.sale_count = 0;
		if (__INIT_DATA.data['1081181309095'].data) {
			ret_data.sale_count = Math.ceil(__INIT_DATA.data['1081181309095'].data.saleNum.replace(/[\<\+]/g, '')/3);
		}
		ret_data.seller = {
			shop_id: __INIT_DATA.globalData.tempModel.sellerMemberId.replace('b2b-', ''),
			shop_name: __INIT_DATA.globalData.tempModel.companyName,
			shop_url: __INIT_DATA.globalData.offerBaseInfo.sellerWinportUrl,
			service: {},
		};
		if (__STORE_DATA && __STORE_DATA.components['38229149']) {
			ret_data.seller.service.star = __STORE_DATA.components['38229149'].moduleData.appData.customerStar;
			ret_data.seller.service.year = __STORE_DATA.components['38229149'].moduleData.tpYear;
			for (let i in __STORE_DATA.components['38229149'].moduleData.appData.serviceList) {
				let name = __STORE_DATA.components['38229149'].moduleData.appData.serviceList[i].serviceKey;
				let len = name.indexOf('_');
				name = name.substr(0, len);
				ret_data.seller.service[name] = __STORE_DATA.components['38229149'].moduleData.appData.serviceList[i].score;
			}
		}
		ret_data.pdt_picture = [];
		for (let i=0; i<__INIT_DATA.globalData.images.length; i++) {
			ret_data.pdt_picture.push(__INIT_DATA.globalData.images[i].fullPathImageURI);
		}
		ret_data.attr = {};
		for (let i=0; i<__INIT_DATA.globalData.skuModel.skuProps.length; i++) {
			const item = __INIT_DATA.globalData.skuModel.skuProps[i];
			let value = {};
			for (let j=0; j<item.value.length; j++) {
				value[j] = {
					name: item.value[j].name,
				};
				if (item.value[j].imageUrl) {
					value[j].img = item.value[j].imageUrl;
				}
			}
			ret_data.attr[item.fid] = {
				name: item.prop,
				value: value
			};
		}
		ret_data.sku = {};
		for (let i in __INIT_DATA.globalData.skuModel.skuInfoMap) {
			const item = __INIT_DATA.globalData.skuModel.skuInfoMap[i];
			let tempMap = i.replace('&gt', '');
			const attr = tempMap.split(';');
			let pvs = {};
			let tmpAttr = JSON.parse(JSON.stringify(ret_data.attr));
			let img = '';
			for (let r in attr) {
				let attrMap = this.getAttr(attr[r], tmpAttr);
				// pvs[attrMap.name] = attrMap.value;
				pvs[ret_data.attr[attrMap.name].name] = ret_data.attr[attrMap.name].value[attrMap.value].name;
				if (attrMap.img) {
					img = attrMap.img;
				}
			}
			let price;
			if (item.price) {
				price = item.price;
			} else {
				price = __INIT_DATA.globalData.skuModel.skuPriceScale.split('-')[1];
			}
			ret_data.sku[item.skuId] = {
				pvs: pvs,
				sku_map: tempMap,
				price: price,
				stock: item.canBookCount,
				img: img,
			};
		}
		ret_data.detail = [];
		if (__INIT_DATA.data['1081181309201'].data) {
			for (let i=0; i<__INIT_DATA.data['1081181309201'].data.length; i++) {
				const tmp = __INIT_DATA.data['1081181309201'].data[i]
				ret_data.detail.push({name:tmp.name, value:tmp.value});
			}
		}
		ret_data.desc_picture = [];
		if (typeof offer_details != 'undefined') {
			var srcArr = offer_details.content.match(/(?<=src=[\'\"])[^\'\"]*(?=[\'\"])/gi);
			for (let i=0; i<srcArr.length; i++) {
				if (srcArr[i]) {
					ret_data.desc_picture.push(srcArr[i]);
				}
			}
		} else {
			obj = document.querySelectorAll('.desc-img-loaded,.desc-img-no-load');
			for (let i=0; i<obj.length; i++) {
				const url = obj[i].getAttribute('data-lazyload-src');
				if (url) {
					ret_data.desc_picture.push(url);
				}
			}
		}
		callback(0, ret_data, '获取成功!');
	},
	get16882: function(callback) {
		var model = window.context.result.global.globalData.model;
		let ret_data = {};
		ret_data.channel_id = 6053;
		ret_data.item_id = model.offerDetail.offerId;
		ret_data.name = model.offerDetail.subject;
		ret_data.url = this.getUrl(ret_data.item_id);
		// 销量
		ret_data.sale_count = model.tradeModel.sale_count;
		// 邮费
		// ret_data.post_fee = 
		console.log(ret_data)
		callback(-1, ret_data, 'get16882 待处理!');
	},
	getAttr: function(attr, attrArr) {
		for (let k in attrArr) {
			for (let j in attrArr[k]['value']) {
				if (attrArr[k]['value'][j].name == attr) {
					var arr = {
						name: k,
						img: attrArr[k]['value'][j].img,
						value: j
					};
					delete attrArr[k];
					return arr;
				}
			}
		}
	},
	getTaobao1: function(callback) {
		const _this = this;
		if (!_this.baseInfo.skuBase) {
			callback(-1, {}, '页面需要处理');
			return false;
		}
		let obj;
		let ret_data = {};
		ret_data.channel_id = HELPERINIT.getChannelId();
		ret_data.item_id = _this.baseInfo.item.itemId;
		ret_data.name = _this.baseInfo.item.title;
		ret_data.url = _this.getUrl(ret_data.item_id);
		ret_data.post_fee = 0;
		if (_this.baseInfo.componentsVO.deliveryVO.freight) {
			ret_data.post_fee = _this.baseInfo.componentsVO.deliveryVO.freight.replace(/[^\d]/g, '');
			if (ret_data.post_fee) {
				ret_data.post_fee = parseInt(ret_data.post_fee) / 100;
			} else {
				ret_data.post_fee = 0;
			}
		}
		ret_data.pdt_picture = _this.baseInfo.item.images;
		ret_data.sale_count = _this.formatSale(_this.baseInfo.item.vagueSellCount);
		// 商家信息
		ret_data.seller = {
			shop_id: _this.baseInfo.seller.sellerId,
			shop_name: _this.baseInfo.seller.shopName,
			shop_url: _this.baseInfo.seller.pcShopUrl,
			service: {
				level: _this.baseInfo.seller.creditLevel
			},
		};
		for (let i=0; i<_this.baseInfo.seller.evaluates.length; i++) {
			ret_data.seller.service[_this.baseInfo.seller.evaluates[i].type] = _this.baseInfo.seller.evaluates[i].score.trim();
		}

		ret_data.attr = {};
		for (let i = 0; i < _this.baseInfo.skuBase.props.length; i++) {
			const tmp = _this.baseInfo.skuBase.props[i];
			ret_data.attr[tmp.pid] = {
				name: tmp.name,
				value: {}
			};
			for (let j=0; j<tmp.values.length; j++) {
				ret_data.attr[tmp.pid].value[tmp.values[j].vid] = {
					name: tmp.values[j].name,
					img: tmp.values[j].image ? tmp.values[j].image : '',
				};
			}
		}
		let skuMap = {};
		for (let i = 0; i < _this.baseInfo.skuBase.skus.length; i++) {
			const tmp = _this.baseInfo.skuBase.skus[i];
			skuMap[tmp.skuId] = tmp.propPath;
		}
		ret_data.sku = {};
		for (let i in _this.baseInfo.skuCore.sku2info) {
			if (i != '0') {
				const tmp = _this.baseInfo.skuCore.sku2info[i];
				const attr = skuMap[i].split(';');
				let pvs = {};
				let img = '';
				for (let r in attr) {
					let attr_item = attr[r].split(':');
					pvs[ret_data.attr[attr_item[0]].name] = ret_data.attr[attr_item[0]].value[attr_item[1]].name;
					if (ret_data.attr[attr_item[0]].value[attr_item[1]].img) {
						img = ret_data.attr[attr_item[0]].value[attr_item[1]].img;
					}
				}
				ret_data.sku[i] = {
					pvs: pvs,
					sku_map: skuMap[i],
					price: tmp.price.priceText,
					stock: tmp.quantity,
					img: img,
				}
			}
		}
		ret_data.detail = [];
		ret_data.video = {};
		if (_this.baseInfo.componentsVO) {
			// 描述
			if (_this.baseInfo.componentsVO.extensionInfoVO.infos) {
				for (let i=0; i<_this.baseInfo.componentsVO.extensionInfoVO.infos.length; i++) {
					if (_this.baseInfo.componentsVO.extensionInfoVO.infos[i].type == 'BASE_PROPS') {
						const tmp = _this.baseInfo.componentsVO.extensionInfoVO.infos[i].items;
						for (let j=0; j<tmp.length; j++) {
							ret_data.detail.push({name:tmp[j].title, value:tmp[j].text.join(' ')});
						}
					}
				}
			}
			// video
			if (_this.baseInfo.componentsVO.headImageVO.videos.length > 0) {
				ret_data.video = {
					url: _this.baseInfo.componentsVO.headImageVO.videos[0].url,
					img: _this.baseInfo.componentsVO.headImageVO.videos[0].videoThumbnailURL
				}
			}
		}
		//描述图片
		ret_data.desc_picture = [];
		const internalId = setInterval(function(){
			let tmp = _this.getResponse('detail.getdesc');
			if (tmp) {
				clearInterval(internalId);
				if (tmp && tmp.components && tmp.components.componentData) {
					if (tmp.components.componentData.desc_richtext_pc) {
						tmp = tmp.components.componentData.desc_richtext_pc.model.text.match(/(?<=src=[\'\"])([^\'\"]*)(?=[\'\"])/g);
						for (let i=0; i<tmp.length; i++) {
							if (tmp[i].indexOf('img.alicdn.com') > -1) {
								ret_data.desc_picture.push(tmp[i]);
							}
						}
					} else {
						for (let i in tmp.components.componentData) {
							if (i.indexOf('detail_pic') >=0 && tmp.components.componentData[i]) {
								let pic = '';
								if (typeof tmp.components.componentData[i] == 'object') {
									pic = tmp.components.componentData[i].model.picUrl;
								} else {
									pic = tmp.components.componentData[i];
								}
								if (pic.indexOf('img.alicdn.com') > -1) {
									ret_data.desc_picture.push(pic);
								}
							}
						}
					}
				}
				if (ret_data.desc_picture.length == 0) {
					obj = document.querySelectorAll('.descV8-singleImage>img');
					for (var i=0; i<obj.length; i++) {
						let url = '';
						if (obj[i].getAttribute('data-src')) {
							url = obj[i].getAttribute('data-src');
						} else {
							url = obj[i].src;
						}
						if (url.indexOf('img.alicdn.com') >= 0) {
							ret_data.desc_picture.push(url);
						}
					}
				}
				callback(0, ret_data, '获取成功!');
			}
		}, 100);
	},
	getTaobao2: function(callback) {
		const _this = this;
		let obj;
		let ret_data = {};
		ret_data.channel_id = HELPERINIT.getChannelId();
		ret_data.item_id = g_config.idata.item.id;
		ret_data.name = g_config.idata.item.title;
		ret_data.url = _this.getUrl(ret_data.item_id);
		obj = document.querySelector('#J_WlServiceTitle');
		ret_data.post_fee = obj ? obj.innerText.replace(/[^0-9]/ig, '') : 0;
		ret_data.pdt_picture = [];
		obj = document.querySelectorAll('#J_UlThumb li');
		for (var i=0; i<obj.length; i++) {
			var tempObj = obj[i].querySelector('img');
			if (tempObj) {
				ret_data.pdt_picture.push(tempObj.getAttribute('data-src').replace('_50x50.jpg', ''));
			}
		}
		ret_data.seller = {
			shop_id: g_config.shopId,
			shop_name: g_config.shopName,
			shop_url: g_config.idata.shop.url,
			service: {},
		};
		obj = document.querySelector('.tb-shop-info');
		if (obj) {
		   ret_data.seller.service.level = this.getLevel(obj.getAttribute('data-creditflag'), obj.querySelectorAll('.tb-shop-rank dd i').length); 
		}
		obj = document.querySelectorAll('.tb-shop-rate dl');
		var descMap = {'描述':'desc', '物流':'post', '服务':'serv'};
		for (var i=0; i<obj.length; i++) {
			var name = obj[i].querySelector('dt').innerText;
			var value = obj[i].querySelector('dd').innerText;
			ret_data.seller.service[descMap[name]] = value;
		}
		ret_data.attr = {};
		let attrObj = document.querySelectorAll('dl.J_Prop');
		for (let k = 0; k < attrObj.length; k++) {
			let attrName = attrObj[k].querySelector('dt').innerText; //属性名
			let attrValue = {};
			let valueList = attrObj[k].querySelector('dd').getElementsByTagName('li'); //属性名
			let attrNameId = 0;
			for (let j = 0; j < valueList.length; j++) {
				let value = valueList[j].attributes['data-value'].nodeValue.split(':');
				if (!attrNameId) {
					attrNameId = value[0];
				}
				let name = valueList[j].children[0].children[0].innerText;
				attrValue[value[1]] = {
					name: name
				};
				if (typeof valueList[j].children[0].attributes['style'] !== 'undefined') {
					let styleText = valueList[j].children[0].attributes['style'].nodeValue;
					attrValue[value[1]].img = styleText.match(/background\:url\(((.+))_30x30/)[1];
				}
			}
			ret_data.attr[attrNameId] = {
				name: attrName,
				value: attrValue
			};
		}
		ret_data.sku = {};
		if (Hub.config.config.sku.valItemInfo.skuMap) {
			for (let k in Hub.config.config.sku.valItemInfo.skuMap) {
				let item = Hub.config.config.sku.valItemInfo.skuMap[k];
				if (item.skuId != '0') {
					let tempMap = k.substr(1, k.length - 2);
					var attr = tempMap.split(';');
					let pvs = {};
					for (let r in attr) {
						let attr_item = attr[r].split(':');
						pvs[ret_data.attr[attr_item[0]].name] = ret_data.attr[attr_item[0]].value[attr_item[1]].name;
					}
					ret_data.sku[item.skuId] = {
						pvs: pvs,
						sku_map: tempMap,
						price: item.price,
						stock: item.stock
					};
				}
			}
		}
		// 描述
		ret_data.detail = [];
		obj = document.querySelectorAll('.attributes-list li');
		for (var i=0; i<obj.length; i++) {
			var item = obj[i].innerText.split(': ');
			ret_data.detail.push({name:item[0], value:item[1]});
		}
		ret_data.desc_picture = [];
		if (typeof desc !== 'undefined') {
			var des_pic_craw = desc.match(/<img(?:[^>]+)src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])(?:[^>]*)>/g);
			if (des_pic_craw) {
				for (let i = 0; i < des_pic_craw.length; i++) {
					var src = des_pic_craw[i].match(/src=(?:[\s|\\\\]*["']([^"'\\]+)[\s|\\\\]*["'])/)[1];
					ret_data.desc_picture.push(src);
				}
			}
		} else {
			obj = document.querySelectorAll('.descV8-container .descV8-singleImage>img');
			for (var i=0; i<obj.length; i++) {
				ret_data.desc_picture.push(obj[i].src);
			}
		}
		callback(0, ret_data, '获取成功!');
	},
	getLevel:function(flag, number){
		var level = 0;
		switch(flag) {
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
				return 'https://item.taobao.com/item.htm?id='+itemId;
			case 'tmall.com':
				return 'https://detail.tmall.com/item.htm?id='+itemId;
			case '1688.com':
				return 'https://detail.1688.com/offer/'+itemId+'.html';
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
			window[callbackName] = function (json) {
				head.removeChild(script);
				clearTimeout(script.timer);
				window[callbackName] = null;
				params.success && params.success(json);
			};
			//发送请求
			script.src = params.url + (data?'?' + data:'');
			//为了得知此次请求是否成功，设置超时处理
			if (params.time) {
				script.timer = setTimeout(function () {
					window[callbackName] = null;
					head.removeChild(script);
					params.error && params.error({
						message: '超时'
					});
				}, time);
			}
		};
		//普通json请求
		function json(params) {
			params.type = (params.type || 'GET').toUpperCase();
			params.data = formatParams(params.data);
			var xhr = new XMLHttpframe('Request');
			xhr.withCredentials = true;
			xhr.onreadystatechange = function () {
				if (xhr.readyState == 4) {
					var status = xhr.status;
					if (status >= 200 && status < 300) {
						var response = '';
						var type = xhr.getResponseHeader('Content-type');
						if (type.indexOf('xml') !== -1 && xhr.responseXML) {
							response = xhr.responseXML; //Document对象响应
						} else {
							response = JSON.parse(xhr.responseText); //JSON响应
						};
						params.success && params.success(response);
					} else {
						params.error && params.error(status);
					}
				};
			};
			//请求方式，默认是GET
			if (params.type == 'GET') {
				xhr.open(params.type, params.url + (params.data?'?' + params.data:''), true);
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
			};
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
		for (let i=0; i<window.customerAjaxRespone.length; i++) {
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
		str = str.replace('+', '').replace('万', '0000').replace('千', '000').replace('百', '00').trim();
		return parseInt(str);
	}
};