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