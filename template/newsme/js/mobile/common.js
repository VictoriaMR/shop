//缩放界面后重新设定html font size
let view_jsset_font, result_font, user_webset_font, clientWidth;
if(document.documentElement.currentStyle) {
    user_webset_font = document.documentElement.currentStyle['fontSize'];
} else {
    user_webset_font = getComputedStyle(document.documentElement,false)['fontSize'];
}
const xs = parseFloat(user_webset_font) / 100;
const docEl = document.documentElement,
    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
    recalc = function() {
        clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
        if (!document.addEventListener) return;
        view_jsset_font = (clientWidth / 3.75);
        result_font = view_jsset_font / xs;
        docEl.style.fontSize = result_font + 'px';
    };
window.addEventListener(resizeEvt, recalc, false);
recalc();