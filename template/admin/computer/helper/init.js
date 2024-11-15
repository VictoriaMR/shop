const HELPERINIT = {
	init: function() {
		const _this = this;
		//获取域名
		_this.getDomain();
		//获取动作开关
		_this.request({action: 'getCache', cache_key: 'helper_action_status'}, function(res){
			if (res.data) {
				if (res.data.crawler_switch_status && _this.isItemPage()) {
					//产品详情抓取
					_this.crawlerItem();
				}
				if (res.data.auto_check_switch_status) {
					//产品维护
					_this.autoCheckItem();
				}
			}
		});
	},
	request: function(param, callback) {
		chrome.runtime.sendMessage(this.getExtId(), param, function(response) {
			if (callback) callback(response);
		});
	},
	windowLoad: function(callback) {
		const _this = this;
		_this.wait(function(){
			let obj = document.querySelector('title');
			if (obj && obj.innerText) {
				_this.clearInter();
				callback();
			}
		});
	},
	getExtId: function() {
		this.helper_extid = this.helper_extid ? this.helper_extid : localStorage.getItem('helper_extid');
		return this.helper_extid;
	},
	getDomain: function() {
		if (this.domain) {
			return this.domain;
		}
		const host = location.host.split('.');
		const len = host.length;
		this.domain = host[len-2]+'.'+host[len-1];
		return this.domain;
	},
	getChannelId: function() {
		switch(this.getDomain()) {
			case 'taobao.com':
				return 6051;
			case 'tmall.com':
				return 6052;
			case '1688.com':
				return 6053;
		}
		return 0;
	},
	isItemPage: function() {
		if (this.item_id) {
			return this.item_id;
		}
		let reg = /(http:|https:)?\/\/(item|detail)\.(taobao|tmall|1688)\.com\/(item|item_o|offer)\/?(\d+)?\.(htm|html)(?:.*?[?&]id=(\d+))?/;
		let ret = location.href.match(reg);
		if (ret) {
			this.item_id = ret[5] ? ret[5] : ret[7];
		}
		return this.item_id;
	},
	isLoginPage: function() {
		if (['login.taobao.com','login.1688.com','login.tmall.com'].indexOf(location.host) >= 0){
			return true;
		}
		var obj = document.querySelector('.next-btn-helper');
		if (obj && obj.innerText == '登录') {
			return true;
		}
		return false;
	},
	isVerifyPage: function() { //有一定的延时性
		if (document.querySelector('.captcha-tips') || document.querySelector('#nc-verify-form')) {
			return true;
		}
		let titleObj = document.querySelector('title');
		if (titleObj && (titleObj.innerText.indexOf('验证码拦截') >= 0 || titleObj.innerText.indexOf('驗證碼攔截') >= 0)) {
			return true;
		}
		return false;
	},
	isDenyPage: function() {
		if (/(action=deny|deny_pc)/.test(location.href)) {
			return true;
		}
		let titleObj = document.querySelector('title');
		if (titleObj && titleObj.innerText.indexOf('访问被拒绝') >= 0) {
			return true;
		}
		return false;
	},
	isErrorPage: function() {
		let obj = document.querySelector('#ctn1 .tip');
		if (obj) {
            return obj.innerText.replace(/\n+/g, ' ');
        }
        obj = document.querySelector('#error-notice .error-notice-hd');
        if (obj) {
            return obj.innerText;
        }
        if (location.host == 'error.taobao.com') {
        	obj = document.querySelector('#err .tips');
        	if (obj) {
            	return obj.innerText;
        	}
        }
        return false;
	},
	loadStatic: function(action, value, callback) {
		const _this = this;
		if (_this.url) {
			_this.loadStaticUrl(action, value, callback);
		} else {
			_this.request({action: 'request', value: 'api/helperData', cache_key: 'helper_all_data_cache'}, function(res) {
				if (res.code) {
					_this.url = res.data.domain;
					_this.version = res.data.version;
					_this.loadStaticUrl(action, value, callback);
				}
			});
		}
	},
	loadStaticUrl: function(action, value, callback){
		const id = value.replace(/\/|\./g, '-');
		if (document.getElementById(id)) {
			return false;
		}
		let obj = document.querySelector('head');
		let loadObj = document.createElement(action=='js'?'script':'link');
		let url = this.url+value+'?v='+this.version;
		switch (action) {
			case 'js': //加载js
				loadObj.type = 'text/javascript';
				loadObj.src = url;
				loadObj.charset = 'utf-8';
				break;
			case 'css': //加载css
				loadObj.rel = 'stylesheet';
				loadObj.href = url;
				loadObj.type = 'text/css'
				break;
		}
		loadObj.id = id;
		obj.appendChild(loadObj);
		if (callback) {
			loadObj.onload = callback;
		}
	},
	wait: function(callback, time, noStop) {
        let timeCount = 0;
        const _this = this;
        _this.clearInter();
        _this.intervalId = setInterval(function() {
            ++timeCount;
            if (!noStop && timeCount > 100) {
                _this.clearInter();
            }
            callback(timeCount);
        }, time ? time*1000 : 100);
    },
    clearInter: function() {
    	clearInterval(this.intervalId);
    },
	crawlerItem: function() {
		const _this = this;
		_this.windowLoad(function(){
			_this.loadStatic('js', '/computer/helper/crawler_init.js');
		});
	},
	autoCheckItem: function(){
		const _this = this;
		_this.windowLoad(function(){
			_this.loadStatic('js', '/computer/helper/check_init.js');
		});
	},
};
HELPERINIT.init();