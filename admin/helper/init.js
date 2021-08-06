const HELPERINIT = {
	init: function() {
		//获取域名
		this.domain = this.getDomain();
		//爬取数据控制
		if (this.isItemPage(this.domain)) {
			this.crawlerItem();
		}
	},
	getUrl: function(callback) {
		this.request({action: 'getUrl'}, callback);
	},
	request: function(param, callback) {
		chrome.runtime.sendMessage(this.getExtId(), param, function(response) {
			if (callback) {
				callback(response);
			}
		});
	},
	getExtId: function() {
		return localStorage.getItem('helper_extid');
	},
	getDomain: function() {
		const host = location.host.split('.');
		const len = host.length;
		return host[len-2]+'.'+host[len-1];
	},
	isItemPage: function(domain) {
		let reg = '';
		switch (domain) {
			case '1688.com':
				reg = /^https\:\/\/detail\.1688\.com\/offer\/(\d+)\.html(?:.)*/i;
				break;
			case 'taobao.com':
				reg = /^https\:\/\/item\.taobao\.com\/item\.htm\?(?:.)*id=(\d+)(?:.)*$/i;
				break;
			case 'tmall.com':
				reg = /^https\:\/\/detail\.tmall\.com\/item\.htm\?(?:.)*id=(\d+)(?:.)*$/i;
				break;
		}
		if (reg) {
			return reg.test(location.href);
		} else {
			return false;
		}
	},
	loadStatic: function(action, value, callback) {
		const _this = this;
		//获取版本号
		_this.request({action: 'request', value: 'api/getHelperData', cache_key: 'helper_all_data_cache'}, function(res) {
			const version = res.data.version;
			if (res.code === 200 || res.code === '200') {
				_this.getUrl(function(res){
					let url = res.data + value;
					if (typeof version !== 'undefined') {
						url += '?v='+version;
					}
					const id = value.replace(/\//g, '_').replace(/\./g, '_');
					if (document.getElementById(id)) {
						document.getElementById(id).remove();
					}
					_this.loadStaticUrl(action, url, id, callback);
				});
			}
		});
	},
	loadStaticUrl: function(action, url, id, callback){
		let obj = document.querySelector('head');
		let loadObj;
		switch (action) {
			case 'js': //加载js
				loadObj = document.createElement('script');
				loadObj.type = 'text/javascript';
				loadObj.src = url;
				loadObj.charset = 'utf-8';
				loadObj.id = id;
				obj.appendChild(loadObj);
				break;
			case 'css': //加载css
				loadObj = document.createElement('link');
				loadObj.rel = 'stylesheet';
				loadObj.href = url;
				loadObj.type = 'text/css'
				loadObj.id = id;
				obj.appendChild(loadObj);
				break;
		}
		if (callback) {
			loadObj.onload = function() {
				callback();
			};
		}
	},
	crawlerItem: function() {
		const _this = this;
		_this.request({action: 'getCache', cache_key: 'crawler_switch_status'}, function(res){
			if (res.data && res.data === '1') {
				_this.loadStatic('js', 'helper/crawler_init.js');
			}
		});
	},
};
HELPERINIT.init();