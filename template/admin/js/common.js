function post(url, param, callback, async) {
	ajax({type:'post',data:param,callback:callback,async:async});
}
function get(url, param, callback, async) {
	ajax({type:'get',data:param,callback:callback,async:async});
}
function ajax(params) {
	if (window.XMLHttpRequest) {
		var xhr = new XMLHttpRequest();
	} else {
		var xhr = new ActiveXObject();
	}
	var type = params.type ? params.type : 'get';
	var isAsync = params.async ? params.async : 'true';
	var dataType = params.dataType ? params.dataType : 'json';
	var str;
	if (JSON.stringify(params.data) !== '{}') {
	str = new Array();
	for (var i in params.data) {
	str.push(i+'='+params.data[i]);
		}
		str = str.split('&');
	}
	if (type == 'get' && str) {
		params.url = params.url + str.split('&');
	}
	xhr.open(type, params.url, isAsync);
	if (type == 'post') {
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-rulencoded');
	}
	xhr.send(str);
	xhr.onreadystatechange = function() {
		if(xhr.readyState === 4) {
		console.log(xhr, 'xhr');
			if(xhr.status == 200) {
				callback(dataType === 'json' ? JSON.parse(xhr.responseText) : xhr.responseText) // 请求成功，返回数据
			}
		}
	}
}
function windowLoad(callback) {
	window.onload = callback;
}
function addRightTips(info, type, delay) {
	if (!info) {
		return false;
	}
    if (!delay) {
        delay = 5000;
    }
    info = info.replace(/\n/g,'<br>');
    var obj = document.getElementById('rightTips');
    if (!obj) {
		obj = document.createElement('div');
		obj.id = 'rightTips';
		document.getElementsByTagName('body')[0].appendChild(obj);
    }
    obj.innerHTML = '<div class="info '+type+'"><i class="glyphicon glyphicon-remove"></i>'+info+'</div>';
    obj.querySelector('.glyphicon-remove').onclick = function(e) {
    	e.parentNode.remove();
    };
    setTimeout(function(){
    	obj.remove();
    }, 5000);
}
function successTips(msg) {
	addRightTips(msg, 'success');
}
function errorTips(msg) {
	addRightTips(msg, 'error');
}
function showTips(res) {
	if (res.code == 200) {
		successTips(res.msg);
	} else {
		errorTips(res.msg);
	}
}