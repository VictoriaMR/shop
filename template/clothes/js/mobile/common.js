//缩放界面后重新设定html font size
const docEl = document.documentElement;
const xs = parseFloat(docEl.currentStyle ? docEl.currentStyle['fontSize'] : getComputedStyle(docEl,false)['fontSize']) / 100;
const recalc = function() {
    docEl.style.fontSize = (docEl.clientWidth / 3.75 / xs) + 'px';
};
window.addEventListener('orientationchange' in window ? 'orientationchange' : 'resize', recalc, false);
recalc();
function stop(obj) {
    if (!obj) {
        obj = document.querySelector('body');
    }
    obj.style.cssText += 'overflow-y:hidden';
}
function start(obj) {
    if (!obj) {
        obj = document.querySelector('body');
    }
    obj.style.cssText += 'overflow-y:auto';
}
// 头部部件滚动监听
window.onload = function(){
    var headerObj = document.getElementById('header');
    var headerH = headerObj.offsetHeight;
    var nowH = 0;
    var up = false;
    var down = false;
    window.addEventListener('scroll', function() {
        var scroH = document.documentElement.scrollTop;
        console.log(scroH, nowH)
        if (scroH > nowH) {
            if (scroH > 50 && !up) {
                headerObj.style.top = '-'+headerH+'px';
                up = true;
                down = false;
            }
        } else if(!down) {
            headerObj.style.top = 0;
            down = true;
            up = false;
        }
        nowH = scroH;
    });
};