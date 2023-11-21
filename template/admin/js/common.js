function post(url, param, callback) {
	$.post(url, param, callback);
}
function get(url, param, callback) {
	$.get(url, param, callback);
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