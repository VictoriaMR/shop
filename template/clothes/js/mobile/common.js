//缩放界面后重新设定html font size
const docEl = document.documentElement;
const xs = parseFloat(docEl.currentStyle ? docEl.currentStyle['fontSize'] : getComputedStyle(docEl,false)['fontSize']) / 100;
const recalc = function() {
    docEl.style.fontSize = (docEl.clientWidth / 3.75 / xs) + 'px';
};
window.addEventListener('orientationchange' in window ? 'orientationchange' : 'resize', recalc, false);
recalc();
// 底部界面不滚动
var notScroll = {
    modalHtmlStyle: '',
    modalBodyStyle: '',
    modalWindowScrollTop: 0,
    modalLock: false,
    modalLockScreen: function () {
        if (this.modalLock) {
            return true;
        } else {
            this.modalLock = true;
        }
        this.modalHtmlStyle = docEl.getAttribute('style');
        this.bodyObj = document.querySelector('body');
        this.modalBodyStyle = this.bodyObj.getAttribute('style');
        docEl.style = this.modalHtmlStyle+';overflow:hidden;';
        this.modalWindowScrollTop = docEl.scrollTop;
        this.bodyObj.style = this.modalBodyStyle+';overflow:hidden;position:fixed;top:-'+this.modalWindowScrollTop+'px;left:0;bottom:0;right:0;';
    },
    modalUnLockScreen: function () {
        var that = this;
        if (!this.modalLock) {
            return true;
        }
        this.bodyObj.style = this.modalBodyStyle;
        docEl.style = this.modalHtmlStyle;
        docEl.scrollTop = this.modalWindowScrollTop;
        that._modalForceUnLockScreen();
    },
    _modalForceUnLockScreen: function () {
        this.modalLock = false;
        this.modalBodyStyle = '';
        this.modalWindowScrollTop = 0;
    }
};
function stop(obj) {
    notScroll.modalLockScreen();
}
function start(obj) {
    notScroll.modalUnLockScreen();
}
function addShake(obj) {
    obj.classList.add('shake');
    navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
    if (navigator.vibrate) {
        navigator.vibrate([200]);
    }
    setTimeout(function(){
        obj.classList.remove('shake');
    }, 600);
}
var modalShowCount = 0;
// 头部部件滚动监听
pageReady(function(){
    var headerObj = document.getElementById('header');
    if (headerObj) {
        var headerH = headerObj.offsetHeight;
        var clientH = document.documentElement.scrollHeight
        console.log(clientH)
        var nowH = 0, scroH = 0;
        var firstScroll = true;
        $scroll(function(){
            if (firstScroll) {
                nowH = scroH;
                firstScroll = false;
            } else {
                scroH = document.documentElement.scrollTop;
                if (firstScroll) {
                    nowH = scroH;
                    firstScroll = false;
                    return false;
                }
                if (scroH > nowH) {//上滑
                    headerObj.style.top = -headerH+'px';
                    nowH = scroH;
                } else {//下来
                    headerObj.style.top = 0;
                    nowH = scroH;
                }
            }
        });
    }
    // inputgroup组件控制
    $change('.input-group input', function(){
        var obj = this.parentNode.querySelector('.icon-box.remove');
        if (obj) {
            obj.style.opacity = this.value == '' ? '0' : '1';
        }
        this.parentNode.parentNode.classList.remove('error');
        var obj = this.parentNode.parentNode.querySelector('.error-tips');
        if (obj) {
            obj.remove();
        }
    });
    $blur('.input-group input', function(){
        var obj = this.parentNode.querySelector('.icon-box.remove');
        if (obj) {
            obj.style.opacity = '0';
        }
    });
    $focus('.input-group input', function(){
        if (this.value != '') {
            var obj = this.parentNode.querySelector('.icon-box.remove');
            if (obj) {
                obj.style.display = 'block';
            }
        }
    });
    $click('.input-group .icon-box.remove', function(e){
        e.stopPropagation();
        var obj = this.parentNode.querySelector('input');
        if (obj) {
            obj.value = '';
        }
        this.style.opacity = '0';
    });
    $click('.input-group .icon-box.eye', function(){
        var obj = this.parentNode.querySelector('input');
        if (obj) {
            var tempObj = this.querySelector('.icon');
            if (tempObj) {
                if (tempObj.classList.contains('icon-eye-close')) {
                    tempObj.classList.remove('icon-eye-close');
                    tempObj.classList.add('icon-eye-open');
                    obj.type = 'text';
                } else {
                    tempObj.classList.remove('icon-eye-open');
                    tempObj.classList.add('icon-eye-close');
                    obj.type = 'password';
                }
            }
        }
    });
    // modal 弹窗隐藏
    $click('.modal .mask,.modal .close-btn', function(){
        var obj = parents(this, 'modal');
        if (obj) {
            if (obj.classList.contains('modal-1')) {
                obj.querySelector('.modal-content').style.bottom = '-100%';
            } else if (obj.classList.contains('modal-2')) {
                obj.querySelector('.modal-content').style.left = '-100%';
            } else if (obj.classList.contains('modal-3')) {
                obj.querySelector('.modal-content').style.right = '-100%';
            }
            var obj = obj.querySelector('.mask');
            obj.style.opacity = '0';
            modalShowCount--;
            if (modalShowCount <= 0) {
                start();
            }
            setTimeout(function(){
                obj.style.visibility = 'hidden';
            }, 300);
        }
    });
    // 映射弹窗
    $click('.to-modal', function(){
        var obj = document.getElementById(this.getAttribute('data-modal'));
        if (obj) {
            stop();
            if (obj.classList.contains('modal-1')) {
                obj.querySelector('.modal-content').style.bottom = '0';
            } else if (obj.classList.contains('modal-2')) {
                obj.querySelector('.modal-content').style.left = '0';
            } else if (obj.classList.contains('modal-3')) {
                obj.querySelector('.modal-content').style.right = '0';
            }
            modalShowCount++;
            obj = obj.querySelector('.mask');
            obj.style.visibility = 'visible';
            if (obj.getAttribute('opacity') !== 'undefinded') {
                obj.style.opacity = obj.getAttribute('opacity');
            } else {
                obj.style.opacity = '0.6';
            }
        }
    })
});