const CRAWLERINIt = {
	init: function() {
		const _this = this;
		//载入分析数据js
		HELPERINIT.loadStatic('js', 'helper/crawler.js', function(){
			//注入页面相关css
			HELPERINIT.loadStatic('css', 'helper/crawler_page.css', function(){
				_this.crawlerInit();
			});
		});
	},
	crawlerInit: function() {
		const _this = this;
		//获取基本数据
		HELPERINIT.request({action: 'request', value: 'api/getHelperData', cache_key: 'helper_all_data_cache'}, function(res) {
			if (res.code === 200) {
				_this.bodyPageInit();
				_this.crawlerPageinit(res.data);
			} else {
				HELPERINIT.request({action:'alert', value: res.message});
			}
		});
	},
	bodyPageInit: function() {
		const _this = this;
		let bodyPage = document.getElementById('crawler_page');
		if (!bodyPage) {
			let body = document.querySelector('body');
			bodyPage = document.createElement('div');
			bodyPage.id = 'crawler_page';
			body.appendChild(bodyPage);
			const html = '<div class="top-content">\
							<div class="crawler-button">\
								<button onclick="CRAWLER.reload()">刷新</button>\
								<button id="add-category">添加分类</button>\
								<span class="error-msg"></span>\
							</div>\
							<button id="crawler-show-btn">展开</button>\
						</div>\
						<div id="item-content"></div>';
			bodyPage.innerHTML = html;
			HELPERINIT.request({action: 'getCache', cache_key: 'crawler_show_status'}, function(res) {
				_this.crawlerPageShow(res.data === '1' ? '1' : 0);
			});
		}
		return bodyPage;
	},
	crawlerPageShow: function(status) {
		if (status === '1') {
			document.getElementById('crawler-show-btn').innerText = '收起';
			document.getElementById('item-content').style.display = 'block';
			document.querySelector('.crawler-button').style.display = 'block';
		} else {
			document.getElementById('crawler-show-btn').innerText = '展开';
			document.getElementById('item-content').style.display = 'none';
			document.querySelector('.crawler-button').style.display = 'none';
		}
	},
	crawlerPageinit: function(info) {
		const _this = this;
		CRAWLER.getData(function(res) {
			if (res.code === 200) {
				_this.crawlerPage(info, res.data);
			} else {
				document.getElementById('item-content').innerHTML = '<div class="error-msg">'+res.message+'</div>';
			}
			_this.clickInit();
		});
	},
	crawlerPage: function(info, data) {
		const _this = this;
		const category = info.category;
		const site = info.site;
		let crawlerPage = document.getElementById('item-content');
		let count = 0;
		let html = `<form id="crawler_form">
						<input type="hidden" name="bc_shop_name" value="` + data.shop_name + `" />
						<input type="hidden" name="bc_shop_url" value="` + data.shop_url + `" />
						<div class="productAttLine">
							<div class="label">供应商:</div>
							<div class="fill_in">
								<input type="text" name="bc_site_id" value="` + HELPERINIT.getDomain().replace('.com', '') + `" />
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">产品ID:</div>
							<div class="fill_in">
								<input type="text" name="bc_product_id" value="` + data.item_id + `" />
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">站点:</div>
							<div class="fill_in">
								<select name="bc_product_site">
									<option value="">请选择站点</option>
									` + _this.getSiteHtml(site) + `
								</select>
							</div>
							<div class="clear"></div>
						</div>
						<div id="category">
							<div class="productAttLine">
								<div class="label">产品分类:</div>
								<div class="fill_in">
									<select name="bc_product_category[0]">
										<option value="">请选择分类</option>
										` + _this.getCategoryHtml(category) + `
									</select>
								</div>
								<div class="clear"></div>
							</div>
						</div>
						<div class="productAttLine">
							<div class="label">产品名称:</div>
							<div class="fill_in">
								<input name="bc_product_name" value="` + data.name + `" />
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">产品URL:</div>
							<div class="fill_in">
								<input type="text" name="bc_product_url" value="` + data.product_url + `" />
							</div>
							<div class="clear"></div>
						</div>`;
		if (data.multi_sku) {
			//sku
			html += `<div class="productAttLine">
						<div class="picTitle" style="margin-bottom: 0;">SKU：</div>
						<div class="pdtPicHere">`;
			let count = 0;
			for (let i in data.sku) {
				html += `<div class="sku-item flex">
							<div class="cancel-btn">x</div>
							<div class="flex w100">
							<div class="sku_img">`;
				if (data.sku[i].sku_img) {
					html += `<img src="` + data.sku[i].sku_img + `">
									<input type="hidden" name="bc_sku[` + count + `][img]" value="` + data.sku[i].sku_img + `"/>`;
				}
				html += `</div>`;
				if (data.sku[i].pvs) {
					html += '<div class="flex1">';
					html += `<div class="flex">
										<div style="width:32px;">
										<span>属性:</span>
									</div>
									<div class="flex1 sku-attr">`;
					if (data.sku[i].pvs.length) {
						for (let j=0;j<data.sku[i].pvs.length;j++) {
							html += `<div>
										<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku[i].pvs[j].text + `"/>
										<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku[i].pvs[j].img + `"/>
									</div>`;
						}
					} else {
						for (let j in data.sku[i].pvs) {
							html += `<div>
										<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku[i].pvs[j].text + `"/>
										<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku[i].pvs[j].img + `"/>
									</div>`;
						}
					}
					html += `</div></div>`;
				}
				html += `<div class="flex price-stock">
									<div style="margin-right: 12px;">价格: <input name="bc_sku[` + count + `][price]" value="` + data.sku[i].price + `"/></div>
									<div>库存: <input name="bc_sku[` + count + `][stock]" value="` + data.sku[i].stock + `"/></div>
								</div>
							</div>
						</div></div>`;
				count++;
			}
			html += `</div></div>`;
		} else {
			html += `<div class="productAttLine">
						<div class="picTitle" style="margin-bottom: 0px;">SKU：</div>
						<div class="pdtPicHere">
							<div class="sku-item flex">
								<div class="cancel-btn">x</div>
								<div class="flex w100">
						<div class="sku_img">`;
			if (data.sku.sku_img) {
				html += `<img src="` + data.sku.sku_img + `">
							<input type="hidden" name="bc_sku[` + count + `][img]" value="` + data.sku.sku_img + `"/>`;
			}
			html += `</div>`;
			if (data.sku.pvs) {
				html += '<div class="flex1">';
				html += `<div class="flex">
							<div style="width:32px;">
							<span>属性:</span>
						</div>
						<div class="flex1 sku-attr">`;
				if (data.sku.pvs.length) {
					for (let j=0;j<data.sku.pvs.length;j++) {
						html += `<div>
									<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku.pvs[j].text + `"/>
									<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku.pvs[j].img + `"/>
								</div>`;
					}
				} else {
					for (let j in data.sku.pvs) {
						html += `<div>
									<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + data.sku.pvs[j].text + `"/>
									<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku.pvs[j].img + `"/>
								</div>`;
					}
				}
				html += `</div></div>`;
			}
			html += `<div class="flex price-stock">
								<div style="margin-right: 12px;">价格: <input name="bc_sku[` + count + `][price]" value="` + data.sku.price + `"/></div>
								<div>库存: <input name="bc_sku[` + count + `][stock]" value="` + data.sku.stock + `"/></div>
							</div>
						</div>
					</div></div>`;
			html += `</div></div>`;
		}
		if (data.pdt_picture) {
			html += `<div class="clear"></div>
							<div class="productMainPic">
								<div class="picTitle">产品图：</div>
								<div class="pdtPicHere" id="pdt_picture">
									<input type="hidden" name="bc_product_img" class="bc_product_picture" value="` + data.pdt_picture.join(',') + `"/>`;
			for (let i=0;i< data.pdt_picture.length;i++) {
				html += `<img class="imgList" src="` + data.pdt_picture[i] + `" />`;
			}
			html += `</div></div>`;
		}
		if (data.des_picture) {
			html += `<div class="clear"></div>
							<div class="productMainPic">
								<div class="picTitle">产品详情图：<span style="color:red;font-size:12px;"></span></div>
								<div class="pdtPicHere" id="pdt_desc_picture">
									<input type="hidden" name="bc_product_des_picture" class="bc_product_picture" value="` + data.des_picture.join(',') + `"/>`;
			for (let i=0;i<data.des_picture.length;i++) {
				html += `<img class="imgList" src="` + data.des_picture[i] + `" />`;
			}
			html += `</div></div>`;
		}
		if (data.attributes) {
			html += `<div class="clear"></div>
							<div class="productMainPic">
								<div class="picTitle">产品描述属性：</div>
								<div id="pdt_des_text">`;
			let count = 0
			for (const i in data.attributes) {
				html += `<div class="sku-attr">
							<input type="text" name="bc_des_text[` + count + `][key]" value="` + data.attributes[i].name + `"> - 
							<input type="text" name="bc_des_text[` + count + `][value]" value="` + data.attributes[i].value + `">
							<div class="cancel-btn">x</div>
						</div>`;
				count++;
			}
			html += `</div></div>`;
		}
		html += '</form>';
		html += `<div class="postProduct" id="postProduct-btn">上传产品</div>`;
		document.getElementById('item-content').innerHTML = html;
	},
	clickInit: function() {
		const _this = this;
		//上传产品按钮
		let obj = document.getElementById('postProduct-btn');
		if (obj) {
			obj.onclick = function () {
				if (this.className.indexOf('loading') !== -1) {
					return false;
				}
				const param = _this.serializeForm(document.getElementById('crawler_form'));
				let _thisobj = this;
				_thisobj.innerHTML = '数据发送中...';
				_thisobj.classList.add('loading');
				HELPERINIT.request({action: 'request', value: 'api/addProduct', param:param}, function(res) {
					_thisobj.classList.remove('loading');
					_thisobj.innerHTML = '上传产品';
					if (res.code !== 200) {
						document.querySelector('.crawler-button .error-msg').innerText = res.message;
					} else {
						document.querySelector('.crawler-button .error-msg').innerText = '';
					}
				});
			}
		}
		//图片按钮点击删除
		obj = document.getElementById('pdt_picture');
		if (obj) {
			const tobj = obj.querySelectorAll('img');
			for (let i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this)
					POP_PAGE.initPdtImgValue(obj);
				}
			}
		}
		//图片介绍图
		obj = document.getElementById('pdt_desc_picture');
		if (obj) {
			const tobj = obj.querySelectorAll('img');
			for (let i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this)
					POP_PAGE.initPdtImgValue(obj);
				}
			}
		}
		obj = document.getElementById('pdt_des_text');
		if (obj) {
			tobj = obj.querySelectorAll('.sku-attr .close');
			for (var i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this);
				}
			}
		}
		// sku 点击删除
		obj = document.querySelectorAll('.cancel-btn');
		if (obj) {
			for (let i = 0; i < obj.length; i++) {
				obj[i].onclick = function(event) {
					this.parentNode.remove();
				}
			}
		}
		//展开/关闭详情
		document.getElementById('crawler-show-btn').onclick = function() {
			let status = '0';
			if (this.innerText === '展开') {
				status = '1';
			}
			HELPERINIT.request({action:'setCache', cache_key:'crawler_show_status', value:status}, function(){
				_this.crawlerPageShow(status);
			});
		}
		//添加分类
		document.getElementById('add-category').onclick = function() {
			const count = document.querySelectorAll('#category .productAttLine').length;
			let div = document.createElement('div');
			div.setAttribute('class', 'productAttLine');
			div.innerHTML = `<div class="label">产品分类:</div>
							<div class="fill_in">
								<select name="bc_product_category[`+count+`]">
									<option value="">请选择分类</option>
									`+_this.categoryHtml+`
								</select>
							</div>
							<div class="clear"></div>`;
			document.getElementById('category').appendChild(div);
		}
	},

	
	reload_crawlerPage: function() {
		//删除缓存
		HELPER.request({action: 'delCache', cache_key: 'crawler_data_cache'}, function() {
			document.getElementById('googleHelper_crawler_js').remove();
			document.getElementById('googleHelper_crawler_css').remove();
			window.postMessage({ type: 'reload_page_css', value: 'googleHelper/crawler.css'}, "*");
			window.postMessage({ type: 'reload_page_js', value: 'googleHelper/crawler.js'}, "*");
		});
	},
	getSiteHtml: function(list) {
		let html = '';
		for (let i = 0; i < list.length; i++) {
			html += '<option value="'+list[i].site_id+'">'+list[i].name+'</option>';
		}
		return html;
	},
	getCategoryHtml: function(list) {
		this.categoryHtml = '';
		for (let i = 0; i < list.length; i++) {
			let padding = '';
			for (let j = 0; j < list[i].level; j++) {
				padding += '&nbsp;&nbsp;&nbsp;';
			}
			let disabled = '';
			if (list[i].level === 0) {
				disabled = 'disabled="disabled"';
			}
			this.categoryHtml += '<option '+disabled+' value="'+list[i].cate_id+'">'+padding+list[i].name+'</option>';
		}
		return this.categoryHtml;
	},
	serializeForm: function(formobj) {
		let formData = new FormData(formobj);
		return Object.fromEntries(formData.entries());
	},
	initPdtImgValue: function(pobj) {
		let imgValueObj = pobj.querySelector('.bc_product_picture');
		if (imgValueObj === null) {
			pobj.innerHTML += '<input type="hidden" name="bc_product_img" class="bc_product_picture" value=""/>';
			imgValueObj = pobj.querySelector('.bc_product_picture');
		}
		const imgobj = pobj.getElementsByTagName('img');
		let value = '';
		for (let i = 0; i < imgobj.length; i++) {
			if (i > 0) {
				value += ',';
			}
			value += imgobj[i].src;
		}
		imgValueObj.value = value;
	}
};
CRAWLERINIt.init();