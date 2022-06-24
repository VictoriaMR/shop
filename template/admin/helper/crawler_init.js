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
			if (res.code === '200') {
				_this.bodyPageInit();
				_this.crawler_info = res.data;
				_this.crawlerPageinit(res.data);
			} else {
				HELPERINIT.request({action:'alert', value: res.message});
			}
		});
	},
	bodyPageInit: function() {
		const _this = this;
		var bodyPage = document.getElementById('crawler-page');
		if (!bodyPage) {
			var body = document.querySelector('body');
			bodyPage = document.createElement('div');
			bodyPage.id = 'crawler-page';
			body.appendChild(bodyPage);
			const html = `<div class="top-content">
							<div class="button-content">
								<button class="hide" id="reload-btn" onclick="CRAWLERINIt.reloadPage()">刷新</button>
								<button onclick="CRAWLERINIt.goNext()" class="hide" id="go-next-btn">跳过</button>
								<button id="crawler-show-btn" onclick="CRAWLERINIt.openClose(this)" class="right">展开</button>
								<div class="clear"></div>
							</div>
							<div class="error-msg"></div>
						</div>
						<div id="item-content"></div>`;
			bodyPage.innerHTML = html;
			HELPERINIT.request({action: 'getCache', cache_key: 'crawler_show_status'}, function(res) {
				_this.crawlerPageShow(res.data === '1' ? '1' : 0);
			});
		}
		return bodyPage;
	},
	reloadPage: function() {
		//删除缓存
		HELPERINIT.request({action: 'delCache', cache_key: 'crawler_data_cache'}, function() {
			document.getElementById('googleHelper_crawler_js').remove();
			document.getElementById('googleHelper_crawler_css').remove();
			window.postMessage({ type: 'reload_page_css', value: 'googleHelper/crawler.css'}, "*");
			window.postMessage({ type: 'reload_page_js', value: 'googleHelper/crawler.js'}, "*");
		});
	},
	goNext: function() {
		var _this = this;
		var _thisobj = document.getElementById('go-next-btn');
		if (_thisobj.className.indexOf('loading') !== -1) {
			return false;
		}
		_thisobj.innerHTML = '数据发送中...';
		_thisobj.classList.add('loading');
		HELPERINIT.request({action: 'getCache', cache_key: 'reload_param_cache'}, function(res) {
			if (res.code === '200') {
				HELPERINIT.request({action: 'request', value: 'api/addAfter', param: res.data}, function(res) {
					_this.error(res.message);
					if (res.code === '200') {
						HELPERINIT.request({action: 'setSocket', value: {is_free: 1, type: 'auto_crawler'}});
					}
				});
			} else {
				_this.error('获取缓存数据失败');
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
	error: function(msg) {
		document.querySelector('#crawler-page .top-content .error-msg').innerText = msg;
	},
	crawlerPageShow: function(status) {
		if (status === '1') {
			document.getElementById('crawler-show-btn').innerText = '收起';
			document.getElementById('crawler-page').setAttribute('class', 'open');
			document.getElementById('reload-btn').setAttribute('class', 'show');
			document.getElementById('go-next-btn').setAttribute('class', 'show');
			document.getElementById('item-content').style.display = 'block';
		} else {
			document.getElementById('crawler-show-btn').innerText = '展开';
			document.getElementById('crawler-page').setAttribute('class', 'close');
			document.getElementById('reload-btn').setAttribute('class', 'hide');
			document.getElementById('go-next-btn').setAttribute('class', 'hide');
			document.getElementById('item-content').style.display = 'none';
		}
	},
	crawlerPageinit: function(info) {
		const _this = this;
		CRAWLER.data(function(code, data, message){
			if (code === 0) {
				_this.crawler_data = data;
				_this.crawlerPage(info, data);
			} else {
				_this.error(message);
			}
			_this.clickInit();
		});
	},
	crawlerPage: function(info, data) {
		const _this = this;
		_this.category = info.site_category;
		_this.site = info.site;
		var crawlerPage = document.getElementById('item-content');
		var count = 0;
		var html = `<form id="crawler_form">
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
								<select name="bc_product_site" class="bc_product_site">
									<option value="">请选择站点</option>
									` + _this.getSiteHtml() + `
								</select>
							</div>
							<div class="clear"></div>
						</div>
						<div id="category">
							<div class="productAttLine">
								<div class="label">产品分类:</div>
								<div class="fill_in">
									<select name="bc_product_category" class="bc_product_category">
										<option value="">请选择分类</option>
									</select>
								</div>
								<div class="clear"></div>
							</div>
						</div>
						<div class="productAttLine">
							<div class="label">性别:</div>
							<div class="fill_in">
								<select name="bc_product_gender" class="bc_product_gender">
									<option value="0">默认</option>
									<option value="1">男</option>
									<option value="2">女</option>
								</select>
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">邮费:</div>
							<div class="fill_in">
								<input name="bc_post_fee" value="`+data.post_fee+`" placeholder="邮费">
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">重量:</div>
							<div class="fill_in">
								<input name="bc_product_weight" value="" placeholder="产品重量(g)">
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">体积:</div>
							<div class="fill_in">
								<input name="bc_product_volume" value="" placeholder="长,宽,高(mm)">
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">产品名称:</div>
							<div class="fill_in">
								<input name="bc_product_name" value="` + _this.nameFormat(data.name) + `" />
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
		// 属性分区
		if (data.attr) {
			html += `<div class="productAttLine">
						<div class="picTitle" style="margin-bottom: 0;">产品属性名：</div>
						<div class="attr-content"></div>`;
			html += `</div>`;
			html += `<div class="productAttLine">
						<div class="picTitle" style="margin-bottom: 0;">产品属性值：</div>
						<div class="attv-content"></div>`;
			html += `</div>`;
		}
		if (data.multi_sku) {
			//sku
			html += `<div class="productAttLine">
						<div class="picTitle" style="margin-bottom: 0;">SKU：</div>
						<div class="pdtPicHere">`;
			var count = 0;
			for (var i in data.sku) {
				html += `<div class="sku-item">
							<input type="hidden" name="bc_sku[` + count + `][sku_id]" value="`+i+`"/>
							<div class="cancel-btn">x</div>
							<div class="flex w100">
							<div class="sku_img">`;
				if (data.sku[i].sku_img) {
					html += `<img src="` + data.sku[i].sku_img + `">
									<input type="hidden" name="bc_sku[` + count + `][img]" value="` + data.sku[i].sku_img + `"/>`;
				}
				html += `</div>`;
				if (data.sku[i].pvs) {
					html += `<div class="flex1">
								<div class="sku-attr">`;
					if (data.sku[i].pvs.length) {
						for (var j=0;j<data.sku[i].pvs.length;j++) {
							html += `<div class="flex">
										<div class="sku-attr-name">`+j+`:</div>
										<div class="flex1">
											<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + _this.formatStr(data.sku[i].pvs[j].text) + `"/>
											<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku[i].pvs[j].img + `"/>
										</div>
									</div>`;
						}
					} else {
						for (var j in data.sku[i].pvs) {
							html += `<div class="flex">
										<div class="sku-attr-name">`+j+`:</div>
										<div class="flex1">
											<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + _this.formatStr(data.sku[i].pvs[j].text) + `"/>
											<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku[i].pvs[j].img + `"/>
										</div>
									</div>`;
						}
					}
					html += `</div>`;
				}
				html += `<div class="price-stock">
							<div class="flex">
								<div style="margin-right: 12px;">价格: <input name="bc_sku[` + count + `][price]" value="` + data.sku[i].price + `"/></div>
								<div>库存: <input name="bc_sku[` + count + `][stock]" value="` + data.sku[i].stock + `"/></div>
							</div>
						</div>`;
				html += `</div></div></div>`;
				count++;
			}
			html += `</div></div>`;
		} else {
			if (data.sku) {
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
						for (var j=0;j<data.sku.pvs.length;j++) {
							html += `<div>
										<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + _this.formatStr(data.sku.pvs[j].text) + `"/>
										<input type="hidden" name="bc_sku[` + count + `][attr][` + j + `][img]" value="` + data.sku.pvs[j].img + `"/>
									</div>`;
						}
					} else {
						for (var j in data.sku.pvs) {
							html += `<div>
										<input name="bc_sku[` + count + `][attr][` + j + `][text]" value="` + _this.formatStr(data.sku.pvs[j].text) + `"/>
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
		}
		if (data.pdt_picture) {
			html += `<div class="clear"></div>
					<div class="productMainPic">
						<div class="picTitle">产品图：</div>
						<div class="pdtPicHere" id="pdt_picture">
							<input type="hidden" name="bc_product_img" class="bc_product_picture" value="` + data.pdt_picture.join(',') + `"/>`;
			html += `<div>`;
			for (var i=0;i< data.pdt_picture.length;i++) {
				html += `<img class="imgList" src="` + data.pdt_picture[i] + `" />`;
				if ((i+1)%4===0) {
					html += '</div><div style="clear:both;"></div><div>';
				}
			}
			html += `</div>`;
			html += `</div></div>`;
		}
		if (data.des_picture) {
			html += `<div class="clear"></div>
					<div class="productMainPic">
						<div class="picTitle">产品详情图：<span style="color:red;font-size:12px;"></span></div>
						<div class="pdtPicHere" id="pdt_desc_picture">
							<input type="hidden" name="bc_product_des_picture" class="bc_product_picture" value="` + data.des_picture.join(',') + `"/>`;
			html += `<div>`;
			for (var i=0;i<data.des_picture.length;i++) {
				html += `<img class="imgList" src="` + data.des_picture[i] + `" />`;
				if ((i+1)%4===0) {
					html += '</div><div style="clear:both;"></div><div>';
				}
			}
			html += `</div>`;
			html += `</div></div>`;
		}
		var attributes = {};
		if (data.attributes.length > 0) {
			var ignoreArr = _this.attrIgnore();
			for (var i=0; i<data.attributes.length; i++) {
				var check = false;
				for (var j=0; j<ignoreArr.length; j++) {
					if (data.attributes[i].name.indexOf(ignoreArr[j])>=0 && !check) {
						check = true;
					}
				}
				if (!check) {
					attributes[data.attributes[i].name] = data.attributes[i].value;
				}
			}
		}
		if (attributes) {
			html += `<div class="clear"></div>
							<div class="productMainPic">
								<div class="picTitle">产品描述属性：</div>
								<div id="pdt_des_text">`;
			for (var i in attributes) {
				html += `<div class="sku-attr">
							<input type="text" name="bc_des_text[`+i+`][key]" value="`+_this.formatStr(i)+`"> - 
							<input type="text" name="bc_des_text[`+i+`][value]" value="`+_this.formatStr(attributes[i])+`">
							<div class="cancel-btn">x</div>
						</div>`;
			}
			html += `</div></div>`;
		}
		html += '</form>';
		html += `<button id="post-product-btn" type="button">上传产品</button>`;
		crawlerPage.innerHTML = html;
		crawlerPage.style.display = 'block';
		_this.initAttr(data.attr);
	},
	initAttr: function(attr) {
		var _this = this;
		var obj = document.querySelector('.attr-content');
		var objv = document.querySelector('.attv-content');
		if (obj) {
			var html = '';
			var attrValueHtml = '';
			for (var i in attr){
				html += `<div>
							<span class="old-value">`+_this.formatStr(attr[i].attrName)+`</span>
							<span>替换</span>
							<input type="text" value="` + _this.formatStr(attr[i].attrName) + `" />
							<button type="button" class="btn1">确定</button>
						</div>`;
				for (var j in attr[i].attrValue) {
					attrValueHtml += `<div>
							<span class="old-value">`+_this.formatStr(attr[i].attrValue[j].name)+`</span>
							<span>替换</span>
							<input type="text" value="` + _this.formatStr(attr[i].attrValue[j].name) + `" />
							<button type="button" class="btn1">确定</button>
							<span class="cancel-btn">x</span>
						</div>`;
				}
			}
			obj.innerHTML = html;
			objv.innerHTML = attrValueHtml;
		}
	},
	nameFormat: function(name) {
		var arr = ['跨境', '亚马逊', '欧美'];
		for (var i=0; i<arr.length; i++) {
			name = name.replace(arr[i], '');
		}
		return name;
	},
	attrIgnore: function() {
		return ['来源', '货源', '产地', '库存', '货号', '品牌', '下游', '销售', '跨境', '成分2'];
	},
	clickInit: function() {
		const _this = this;
		//上传产品按钮
		var obj1 = document.getElementById('post-product-btn');
		if (obj1) {
			obj1.onclick = function () {
				if (this.className.indexOf('loading') !== -1) {
					return false;
				}
				const param = _this.serializeForm(document.getElementById('crawler_form'));
				var _thisobj = this;
				_thisobj.innerHTML = '数据发送中...';
				_thisobj.classList.add('loading');
				HELPERINIT.request({action: 'request', value: 'api/addProduct', param:param}, function(res) {
					_thisobj.classList.remove('loading');
					_thisobj.innerHTML = '上传产品';
					_this.error(res.message);
					if (res.code === '200') {
						HELPERINIT.request({action: 'setSocket', value: {is_free: 1, type: 'auto_crawler'}});
					}
				});
			}
		}
		//图片按钮点击删除
		var obj3 = document.getElementById('pdt_picture');
		if (obj3) {
			const tobj = obj3.querySelectorAll('img');
			for (var i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this)
					_this.initPdtImgValue(obj3);
				}
			}
		}
		//图片介绍图
		var obj4 = document.getElementById('pdt_desc_picture');
		if (obj4) {
			const tobj = obj4.querySelectorAll('img');
			for (var i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.removeChild(this)
					_this.initPdtImgValue(obj4);
				}
			}
		}
		//描述文件删除
		var obj5 = document.getElementById('pdt_des_text');
		if (obj5) {
			tobj = obj5.querySelectorAll('.sku-attr .cancel-btn');
			for (var i = 0; i < tobj.length; i++) {
				tobj[i].onclick = function(event) {
					this.parentNode.remove();
				}
			}
		}
		// sku 点击删除
		var obj6 = document.querySelectorAll('.sku-item .cancel-btn');
		if (obj6) {
			for (var i = 0; i < obj6.length; i++) {
				obj6[i].onclick = function(event) {
					this.parentNode.remove();
				}
			}
		}
		//站点改变切换分类
		var obj7 = document.querySelector('#crawler-page .bc_product_site');
		if (obj7) {
			obj7.onchange = function(){
				const index = obj7.selectedIndex;
				_this.getCategoryHtml(obj7.options[index].value);
			}
		}
		//属性切换点击
		var obj8 = document.querySelectorAll('#crawler-page .attr-content button');
		if (obj8) {
			for (var i = 0; i < obj8.length; i++) {
				obj8[i].onclick = function(event) {
					var oldValue = this.parentNode.querySelector('.old-value').innerText;
					var value = this.parentNode.querySelector('input').value;
					var skuObj = document.querySelectorAll('#crawler-page .sku-item .sku-attr .flex');
					for (var j=0; j<skuObj.length; j++) {
						if (skuObj[j].querySelector('.sku-attr-name').innerText == oldValue+':') {
							skuObj[j].querySelector('.sku-attr-name').innerText = value+':';
							var inputObj = skuObj[j].querySelectorAll('input');
							for (var n=0; n<inputObj.length; n++) {
								inputObj[n].setAttribute('name', inputObj[n].getAttribute('name').replace(oldValue, value));
							}
						}
					}
					this.parentNode.querySelector('.old-value').innerText = value;
				}
			}
		}
		//属性切换点击
		var obj9 = document.querySelectorAll('#crawler-page .attv-content button');
		if (obj9) {
			for (var i = 0; i < obj9.length; i++) {
				obj9[i].onclick = function(event) {
					var oldValue = this.parentNode.querySelector('.old-value').innerText;
					var value = this.parentNode.querySelector('input').value;
					var skuObj = document.querySelectorAll('#crawler-page .sku-item .sku-attr .flex');
					for (var j=0; j<skuObj.length; j++) {
						if (skuObj[j].querySelector('input').value == oldValue) {
							skuObj[j].querySelector('input').value = value;
						}
					}
					this.parentNode.querySelector('.old-value').innerText = value;
				}
			}
		}
		var obj10 = document.querySelectorAll('#crawler-page .attv-content .cancel-btn');
		if (obj10) {
			for (var i = 0; i < obj10.length; i++) {
				obj10[i].onclick = function(event) {
					var oldValue = this.parentNode.querySelector('input').value;
					var skuObj = document.querySelectorAll('#crawler-page .sku-item .sku-attr .flex');
					for (var j=0; j<skuObj.length; j++) {
						if (skuObj[j].querySelector('input').value == oldValue) {
							skuObj[j].parentNode.parentNode.parentNode.parentNode.remove();
						}
					}
					this.parentNode.remove();
				}
			}
		}
	},
	getSiteHtml: function() {
		var html = '';
		const list = this.site;
		if (list) {
			for (var i = 0; i < list.length; i++) {
				html += '<option value="'+list[i].site_id+'">'+list[i].name+'</option>';
			}	
		}
		return html;
	},
	getCategoryHtml: function(siteId) {
		var html = '';
		const list = this.category[siteId];
		console.log(siteId, list, this)
		if (list) {
			html = '<option value="">请选择分类</option>';
			for (var i = 0; i < list.length; i++) {
				var paddingStr = '';
				var disable = false;
				if (list[i+1] && list[i+1].parent_id == list[i].cate_id) {
					disable = true;
				}
				for (var p=0; p<=list[i].level; p++) {
					paddingStr += '&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				html += '<option value="'+list[i].cate_id+'" '+(disable?'disabled="disabled"':'')+'>'+paddingStr+list[i].name+'</option>';
			}
		}
		document.querySelector('#crawler-page .bc_product_category').innerHTML = html;
	},
	serializeForm: function(formobj) {
		if (formobj) {
			var formData = new FormData(formobj);
			return Object.fromEntries(formData.entries());
		}
		return {};
	},
	initPdtImgValue: function(pobj) {
		var imgValueObj = pobj.querySelector('.bc_product_picture');
		if (imgValueObj === null) {
			pobj.innerHTML += '<input type="hidden" name="bc_product_img" class="bc_product_picture" value=""/>';
			imgValueObj = pobj.querySelector('.bc_product_picture');
		}
		const imgobj = pobj.getElementsByTagName('img');
		var value = '';
		var count = 0;
		for (var i = 0; i < imgobj.length; i++) {
			if (imgobj[i].src) {
				if (count > 0) {
					value += ',';
				}
				value += imgobj[i].src;
				count ++;
			}
		}
		if (imgValueObj) {
			imgValueObj.value = value;
		}
	},
	formatStr: function(str) {
		if (typeof str === 'undefined') {
			return '';
		}
		const arr = {
			'   ': ' ',
			'（': '(',
			'）': ')',
			' (': '(',
			' - ': '-',
			' -': '-',
			'- ': '-',
			' * ': '*',
			' *': '*',
			'* ': '*',
			' CM': 'CM',
			' cm': 'cm',
			' / ': '/',
			' /': '/',
			'/ ': '/',
			' , ': ',',
			', ': ',',
			' ,': ',',
			' + ': '+',
			' +': '+',
			'+ ': '+',
			'E 27': 'E27',
			' ＜ ': '<',
			' < ': '<',
			' <': '<',
			'< ': '<',
			'≦': '≤',
			' ≤ ': '≤',
			' ≤': '≤',
			'≤ ': '≤',
			' ~ ': '~',
			'~ ': '~',
			' ~': '~',
			' W': 'W',
			'，': ',',
			'、': ',',
			' mm': 'mm',
			' MM': 'MM',
			'2XL': 'XXL',
			'3XL': 'XXXL',
			'4XL': 'XXXXL',
			'(%)': '%',
			'(含)': '',
		};
		for (const i in arr) {
			str = str.replace(i, arr[i]);
			for (var j=0; j<5; j++) {
				str = str.replace(i, arr[i]);
			}
		}
		return str;
	},
};
CRAWLERINIt.init();