const CRAWLERINIt = {
	init: function() {
		const _this = this;
		//载入分析数据js
		HELPERINIT.loadStatic('css', '/computer/helper/crawler_page.css');
		HELPERINIT.loadStatic('js', '/computer/helper/crawler.js', function(){
			_this.crawlerInit();
		});
	},
	crawlerInit: function() {
		this.bodyPageInit();
	},
	bodyPageInit: function() {
		const _this = this;
		let bodyPage = document.getElementById('crawler-page');
		if (!bodyPage) {
			bodyPage = document.createElement('div');
			bodyPage.id = 'crawler-page';
			bodyPage.className = 'close';
			const html = `<div class="top-content">
							<div class="button-content">
								<div class="left-btns">
									<button type="button" id="reload-btn" onclick="CRAWLERINIt.reloadPage()">刷新</button>
									<button type="button" class="hide" id="go-next-btn" onclick="CRAWLERINIt.goNext()">跳过</button>
								</div>
								<div class="error-msg">数据抓取中...</div>
								<button type="button" id="crawler-show-btn" onclick="CRAWLERINIt.openClose(this)" class="right">展开</button>
							</div>
						</div>
						<div id="item-content"></div>`;
			document.querySelector('body').appendChild(bodyPage);
			bodyPage.innerHTML = html;
			HELPERINIT.request({action: 'getCache', cache_key: 'crawler_show_status'}, function(res) {
				_this.crawlerPageinit(function(){
					_this.crawlerPageShow(res.data);
				});
			});
		}
		return bodyPage;
	},
	reloadPage: function() {
		const _this = this;
		_this.error('重新抓取中...');
		document.getElementById('item-content').innerHTML = '';
		HELPERINIT.request({action: 'delCache', cache_key: 'helper_all_data_cache'}, function() {
			HELPERINIT.request({action: 'delCache', cache_key: 'crawler_data_cache'}, function() {
				_this.crawlerPageinit(function() {
					_this.crawlerPageShow('1');
				});
			});
		});
	},
	goNext: function() {
		var _this = this;
		var _thisobj = document.getElementById('go-next-btn');
		if (_thisobj.className.indexOf('loading') !== -1) {
			return false;
		}
		_thisobj.innerHTML = '发送中...';
		_thisobj.classList.add('loading');
		HELPERINIT.request({action: 'getCache', cache_key: 'reload_param_cache'}, function(res) {
			if (res && res.code) {
				HELPERINIT.request({action: 'request', value: 'api/addAfter', param: res.data}, function(res) {
					const ok = !!(res && res.code);
					const msg = (res && res.msg) ? res.msg : (ok ? '操作成功' : '操作失败');
					_this.error(msg, ok);
					_this.toast(msg, ok);
					if (ok) {
						HELPERINIT.request({action: 'setSocket', value: {is_free: 1, type: 'auto_crawler'}});
					}
				});
			} else {
				_this.error('获取缓存数据失败', false);
				_this.toast('获取缓存数据失败', false);
			}
			_thisobj.classList.remove('loading');
			_thisobj.innerHTML = '跳过';
		});
	},
	openClose: function(e){
		//展开/关闭详情
		var _this = this;
		var _thisobj = document.getElementById('crawler-show-btn');
		var status = '0';
		if (_thisobj.innerText === '展开') {
			status = '1';
		}
		HELPERINIT.request({action:'setCache', cache_key:'crawler_show_status', value:status, expire:-1}, function(){
			_this.crawlerPageShow(status);
		});
	},
	error: function(msg, isSuccess) {
		const el = document.querySelector('#crawler-page .top-content .error-msg');
		if (el) {
			el.innerText = msg || '';
			el.className = 'error-msg' + (isSuccess ? ' success-msg' : '');
		}
	},
	toast: function(msg, isSuccess) {
		const page = document.getElementById('crawler-page');
		if (!page) return;
		let toastEl = document.getElementById('crawler-toast-tip');
		if (!toastEl) {
			toastEl = document.createElement('div');
			toastEl.id = 'crawler-toast-tip';
			page.appendChild(toastEl);
		}
		if (this.toastTimer) {
			clearTimeout(this.toastTimer);
		}
		const icon = isSuccess ? '✓ ' : '✕ ';
		toastEl.innerText = icon + (msg || (isSuccess ? '操作成功' : '操作失败'));
		toastEl.className = 'crawler-toast show ' + (isSuccess ? 'toast-success' : 'toast-error');
		this.toastTimer = setTimeout(function() {
			toastEl.classList.remove('show');
		}, 2500);
	},
	crawlerPageShow: function(status) {
		const obj1 = document.getElementById('crawler-show-btn');
		const obj2 = document.getElementById('crawler-page');
		if (status === '1') {
			obj1.innerText = '收起';
			obj2.classList.remove('close');
		} else {
			obj1.innerText = '展开';
			obj2.classList.add('close');
		}
	},
	crawlerPageinit: function(callback) {
		const _this = this;
		HELPERINIT.request({action: 'getCache', cache_key: 'helper_all_data_cache'}, function(res) {
			if (res && res.code && res.data) {
				_this.cate_list = res.data.cate_list;
			}
			CRAWLER.init(function(code, data, msg){
				console.log(code, data, msg);
				if (code) {
					_this.error(msg, false);
				} else {
					_this.error('抓取成功', true);
					_this.crawlerPage(data);
				}
				_this.clickInit(data);
				if (callback) callback();
			});
		});
	},
	escapeHtml: function(str) {
		if (str === undefined || str === null) return '';
		return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	},
	crawlerPage: function(data) {
		const _this = this;
		const crawlerPage = document.getElementById('item-content');
		const domainName = HELPERINIT.getDomain().replace('.com', '');
		const shopName = (data.seller && data.seller.shop_name) ? data.seller.shop_name : '';
		const saleCount = data.sale_count || 0;
		const skuKeys = data.sku ? Object.keys(data.sku) : [];
		const pdtPics = data.pdt_picture || [];
		const descPics = data.desc_picture || [];
		const details = data.detail || [];

		let html = `<form id="crawler_form">
			<div class="section-card">
				<div class="productAttLine">
					<div class="productAttHalf">
						<div class="label">渠道:</div>
						<div class="fill_in">
							<input type="text" name="domain" value="${_this.escapeHtml(domainName)}" readonly />
						</div>
					</div>
					<div class="productAttHalf">
						<div class="label">产品ID:</div>
						<div class="fill_in">
							<input type="text" name="item_id" value="${_this.escapeHtml(data.item_id)}" readonly />
						</div>
					</div>
				</div>`;
		if (shopName || saleCount) {
			html += `<div class="productAttLine">
					<div class="productAttHalf">
						<div class="label">店铺:</div>
						<div class="fill_in">
							<input type="text" value="${_this.escapeHtml(shopName)}" title="${_this.escapeHtml(shopName)}" readonly />
						</div>
					</div>
					<div class="productAttHalf">
						<div class="label">销量:</div>
						<div class="fill_in">
							<input type="text" value="${_this.escapeHtml(saleCount)}" readonly />
						</div>
					</div>
				</div>`;
		}
		html += `<div class="productAttLine">
					<div class="productAttThird">
						<div class="label">邮费:</div>
						<div class="fill_in">
							<input type="text" name="post_fee" value="${_this.escapeHtml(data.post_fee)}" placeholder="邮费" />
						</div>
					</div>
					<div class="productAttThird">
						<div class="label">重量(g):</div>
						<div class="fill_in">
							<input type="text" name="weight" value="" placeholder="重量(g)" />
						</div>
					</div>
					<div class="productAttThird">
						<div class="label">体积:</div>
						<div class="fill_in">
							<input type="text" name="volume" value="" placeholder="长,宽,高(mm)" />
						</div>
					</div>
				</div>
				<div class="productAttLine">
					<div class="label">产品名称:</div>
					<div class="fill_in">
						<input type="text" name="name" value="${_this.escapeHtml(data.name)}" title="${_this.escapeHtml(data.name)}" />
					</div>
				</div>
				<div class="productAttLine">
					<div class="label">产品URL:</div>
					<div class="fill_in">
						<input type="text" name="url" value="${_this.escapeHtml(data.url)}" readonly />
					</div>
				</div>
			</div>`;

		// 属性分区
		if (data.attr && Object.keys(data.attr).length > 0) {
			html += `<div class="item-block">
				<div class="block-header"><span class="title">规格属性</span></div>`;
			for (let i in data.attr) {
				html += `<div class="attr-item">
							<div class="attr-title">${_this.escapeHtml(data.attr[i].name)}</div>
							<div class="value-content">`;
				for (let j in data.attr[i].value) {
					const valItem = data.attr[i].value[j];
					const imgHtml = valItem.img ? `<img src="${_this.escapeHtml(valItem.img)}" class="attr-val-img" />` : '';
					html += `<span class="value">${imgHtml}${_this.escapeHtml(valItem.name)}</span>`;
				}
				html += `</div></div>`;
			}
			html += `</div>`;
		}

		// SKU 列表
		if (skuKeys.length > 0) {
			html += `<div class="sku-list item-block">
						<div class="block-header">
							<span class="title">SKU 列表</span>
							<span class="badge-count">共 ${skuKeys.length} 个</span>
						</div>
						<div class="sku-table-wrap">
							<table>
								<thead>
									<tr>
										<th>规格属性</th>
										<th width="65">报价</th>
										<th width="65">库存</th>
									</tr>
								</thead>
								<tbody>`;
			for (let i in data.sku) {
				let skuAttr = [];
				for (let j in data.sku[i].pvs) {
					skuAttr.push(data.sku[i].pvs[j]);
				}
				const skuText = skuAttr.length > 0 ? skuAttr.join(' ; ') : '单品默认规格';
				const skuImg = data.sku[i].img ? `<img src="${_this.escapeHtml(data.sku[i].img)}" class="sku-row-img" />` : '';
				html += `<tr>
							<td><div class="sku-cell">${skuImg}<span>${_this.escapeHtml(skuText)}</span></div></td>
							<td class="price-cell">¥${_this.escapeHtml(data.sku[i].price)}</td>
							<td class="stock-cell">${_this.escapeHtml(data.sku[i].stock)}</td>
						</tr>`;
			}
			html += `</tbody></table></div></div>`;
		}

		// 产品主图
		if (pdtPics.length > 0) {
			html += `<div class="productMainPic item-block">
						<div class="block-header">
							<span class="title">产品主图</span>
							<span class="badge-count">${pdtPics.length} 张</span>
						</div>
						<div class="pdtPicHere" id="pdt_picture">`;
			for (let i = 0; i < pdtPics.length; i++) {
				html += `<div class="img-box"><img class="imgList" src="${_this.escapeHtml(pdtPics[i])}" loading="lazy" /></div>`;
			}
			html += `</div></div>`;
		}

		// 产品详情图
		if (descPics.length > 0) {
			html += `<div class="productMainPic item-block">
						<div class="block-header">
							<span class="title">产品详情图</span>
							<span class="badge-count">${descPics.length} 张</span>
						</div>
						<div class="pdtPicHere" id="pdt_desc_picture">`;
			for (let i = 0; i < descPics.length; i++) {
				html += `<div class="img-box"><img class="imgList" src="${_this.escapeHtml(descPics[i])}" loading="lazy" /></div>`;
			}
			html += `</div></div>`;
		}

		// 产品描述属性
		if (details.length > 0) {
			html += `<div class="item-block">
						<div class="block-header">
							<span class="title">产品描述属性</span>
							<span class="badge-count">${details.length} 项</span>
						</div>
						<div id="pdt_des_text" class="detail-grid">`;
			for (let i = 0; i < details.length; i++) {
				html += `<div class="detail-item"><span class="detail-name">${_this.escapeHtml(details[i].name)}:</span> <span class="detail-val">${_this.escapeHtml(details[i].value)}</span></div>`;
			}
			html += `</div></div>`;
		}

		html += `</form>
		<div class="bottom-action">
			<button id="post-product-btn" type="button">上传产品</button>
		</div>`;
		crawlerPage.innerHTML = html;
	},
	clickInit: function(data) {
		const _this = this;
		//上传产品按钮
		var obj1 = document.getElementById('post-product-btn');
		if (obj1) {
			obj1.onclick = function () {
				if (this.classList.contains('loading')) {
					return false;
				}
				const param = _this.serializeForm(document.getElementById('crawler_form'));
				for (let i in param) {
					data[i] = param[i];
				}
				var _thisobj = this;
				if (_thisobj.btnTimer) {
					clearTimeout(_thisobj.btnTimer);
				}
				_thisobj.classList.remove('btn-success', 'btn-error');
				_thisobj.classList.add('loading');
				_thisobj.innerHTML = '数据发送中...';
				HELPERINIT.request({action: 'request', value: 'api/addProduct', param: data}, function(res) {
					_thisobj.classList.remove('loading');
					const ok = !!(res && (res.code === 1 || res.code === '1'));
					const msg = (res && res.msg) ? res.msg : (ok ? '上传成功' : '上传失败，请检查登录状态或网络');
					_this.error(msg, ok);
					_this.toast(msg, ok);
					if (ok) {
						_thisobj.classList.add('btn-success');
						_thisobj.innerHTML = '✓ ' + msg;
						HELPERINIT.request({action: 'setSocket', value: {is_free: 1, type: 'auto_crawler'}});
					} else {
						_thisobj.classList.add('btn-error');
						_thisobj.innerHTML = '✕ ' + msg;
					}
					_thisobj.btnTimer = setTimeout(function() {
						_thisobj.classList.remove('btn-success', 'btn-error');
						_thisobj.innerHTML = ok ? '重新上传产品' : '上传产品';
					}, 2200);
				});
			}
		}
	},
	serializeForm: function(formobj) {
		if (formobj) {
			var formData = new FormData(formobj);
			return Object.fromEntries(formData.entries());
		}
		return {};
	},
};
CRAWLERINIt.init();