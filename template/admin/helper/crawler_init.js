const CRAWLERINIt = {
	init: function() {
		const _this = this;
		//载入分析数据js
		HELPERINIT.loadStatic('css', 'helper/crawler_page.css');
		HELPERINIT.loadStatic('js', 'helper/crawler.js', function(){
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
			bodyPage.classList = 'close';
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
			document.querySelector('body').appendChild(bodyPage);
			bodyPage.innerHTML = html;
			HELPERINIT.request({action: 'getCache', cache_key: 'crawler_show_status'}, function(res) {
				_this.crawlerPageinit(function(){
					_this.crawlerPageShow(res.data === '1' ? '1' : '0');
				});
				
			});
		}
		return bodyPage;
	},
	reloadPage: function() {
		//删除缓存
		HELPERINIT.request({action: 'delCache', cache_key: 'crawler_data_cache'}, function() {
			document.getElementById('googleHelper_crawler_js').remove();
			document.getElementById('googleHelper_crawler_css').remove();
			window.postMessage({type: 'reload_page_css', value: 'googleHelper/crawler.css'}, "*");
			window.postMessage({type: 'reload_page_js', value: 'googleHelper/crawler.js'}, "*");
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
					_this.error(res.msg);
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
			if (res.code == 200) {
				_this.cate_list = res.data.cate_list;
				CRAWLER.init(function(code, data, msg){
					if (code === 0) {
						_this.crawlerPage(data);
					} else {
						_this.error(msg);
					}
					_this.clickInit(data);
					callback();
				});
			} else {
				_this.error(res.msg);
				callback();
			}
		});
	},
	crawlerPage: function(data) {
		const _this = this;
		var crawlerPage = document.getElementById('item-content');
		var count = 0;
		var html = `<form id="crawler_form">`;
				html += `<div class="productAttLine">
							<div class="label">供应商:</div>
							<div class="fill_in">
								<input type="text" name="domain" value="` + HELPERINIT.getDomain().replace('.com', '') + `" />
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">产品ID:</div>
							<div class="fill_in">
								<input type="text" name="item_id" value="` + data.item_id + `" />
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">品类:</div>
							<div class="fill_in">
								<select name="root_category">
									<option value="">请选择品类</option>
									`+_this.getCategoryHtml(0)+`
								</select>
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">分类:</div>
							<div class="fill_in">
								<select name="cate_id">
									<option value="">请选择分类</option>
								</select>
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">邮费:</div>
							<div class="fill_in">
								<input name="post_fee" value="`+data.post_fee+`" placeholder="邮费">
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">重量:</div>
							<div class="fill_in">
								<input name="weight" value="" placeholder="产品重量(g)">
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">体积:</div>
							<div class="fill_in">
								<input name="volume" value="" placeholder="长,宽,高(mm)">
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">产品名称:</div>
							<div class="fill_in">
								<input name="name" value="` +data.name + `" />
							</div>
							<div class="clear"></div>
						</div>
						<div class="productAttLine">
							<div class="label">产品URL:</div>
							<div class="fill_in">
								<input type="text" name="url" value="` + data.url + `" />
							</div>
							<div class="clear"></div>
						</div>`;
		// 属性分区
		if (data.attr) {
			html += `<div class="item-block">`;
			for (var i in data.attr) {
				html += `<div class="attr-item">
							<p class="title">`+data.attr[i].name+`</p>
							<div class="value-content">`;
				for (var j in data.attr[i].value) {
					html += `<p class="value">`+data.attr[i].value[j].name+`</p>`;
				}
				html += `</div>
						</div>`;
			}
			html += `</div>`;
		}
		if (data.sku) {
			//sku
			html += `<div class="sku-list mt12">
						<div class="title">SKU：</div>
						<table border="0" width="100%">
							<tr>
								<td>SKU</td>
								<td>报价</td>
								<td>库存</td>
							</tr>
						`;
			for (var i in data.sku) {
				var skuAttr = new Array();
				for (var j in data.sku[i].pvs) {
					skuAttr.push(data.sku[i].pvs[j]);
				}
				skuAttr 
				html += `<tr>
							<td>`+skuAttr.join(';')+`</td>
							<td>`+data.sku[i].price+`</td>
							<td>`+data.sku[i].stock+`</td>
						</tr>`;
			}
			html += '</table>';
		}
		if (data.pdt_picture.length > 0) {
			html += `<div class="clear"></div>
					<div class="productMainPic mt12">
						<div class="title">产品图：</div>
						<div class="pdtPicHere" id="pdt_picture">`;
			for (var i=0;i< data.pdt_picture.length;i++) {
				html += `<img class="imgList" src="` + data.pdt_picture[i] + `" />`;
			}
			html += `</div></div>`;
		}
		if (data.desc_picture.length > 0) {
			html += `<div class="clear"></div>
					<div class="productMainPic mt12">
						<div class="title">产品详情图：</div>
						<div class="pdtPicHere" id="pdt_desc_picture">`;
			for (var i=0;i<data.desc_picture.length;i++) {
				html += `<img class="imgList" src="` + data.desc_picture[i] + `" />`;
			}
			html += `</div></div>`;
		}
		if (data.detail.length > 0) {
			html += `<div class="clear"></div>
						<div class="item-block mt12">
							<div class="title">产品描述属性：</div>
							<div id="pdt_des_text">`;
			for (var i=0; i<data.detail.length; i++) {
				html += `<p>`+data.detail[i].name+`: `+data.detail[i].value+`</p>`;
			}
			html += `</div></div>`;
		}
		html += '</form>';
		html += `<button id="post-product-btn" type="button">上传产品</button>`;
		crawlerPage.style += 'border-top: 1px solid #321679; margin-top: 4px; padding-bottom: 48px;';
		crawlerPage.innerHTML = html;
	},
	clickInit: function(data) {
		const _this = this;
		//上传产品按钮
		var obj1 = document.getElementById('post-product-btn');
		if (obj1) {
			obj1.onclick = function () {
				if (this.className.indexOf('loading') !== -1) {
					return false;
				}
				const param = _this.serializeForm(document.getElementById('crawler_form'));
				for (let i in param) {
					data[i] = param[i];
				}
				var _thisobj = this;
				_thisobj.innerHTML = '数据发送中...';
				_thisobj.classList.add('loading');
				HELPERINIT.request({action: 'request', value: 'api/addProduct', param:data}, function(res) {
					_thisobj.classList.remove('loading');
					_thisobj.innerHTML = '上传产品';
					_this.error(res.msg);
					if (res.code === '200') {
						HELPERINIT.request({action: 'setSocket', value: {is_free: 1, type: 'auto_crawler'}});
					}
				});
			}
		}
		//切换品类
		var obj2 = document.querySelector('#crawler-page select[name="root_category"]');
		if (obj2) {
			obj2.onchange = function(){
				const index = obj2.selectedIndex;
				var html = _this.getCategoryHtml(obj2.options[index].value);
				html = '<option value="">请选择分类</option>'+html;
				document.querySelector('#crawler-page select[name="cate_id"]').innerHTML=html;
			}
		}
	},
	getCategoryHtml: function(pid) {
		var html = '';
		var start = false;
		if (this.cate_list) {
			if (pid == 0) {
				for (var i in this.cate_list) {
					html += '<option value="'+i+'">'+this.cate_list[i].name+'</option>';
				}
			} else {
				var list = this.cate_list[pid].son;
				for (var i = 0; i < list.length; i++) {
					var disable = '';
					var padding = '';
					for (var j=1; j<list[i].level; j++) {
						padding += '&nbsp;&nbsp;&nbsp;';
					}
					if (list[i+1] && list[i+1].level > list[i].level) {
						disable = 'disabled="disabled"';
					}
					html += '<option '+disable+' value="'+list[i].cate_id+'">'+padding+list[i].name+'</option>';
				}
			}
		}
		return html;
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