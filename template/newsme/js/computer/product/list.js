$(function(){
	//左侧点击
	$('#left-filter .item .title').on('click', function(){
		var nextObj = $(this).parent().next();
		if (nextObj.hasClass('open')) {
			nextObj.removeClass('open');
			$(this).next().removeClass('icon-xiangshang2').addClass('icon-xiangxia2');
		} else {
			nextObj.addClass('open');
			$(this).next().removeClass('icon-xiangxia2').addClass('icon-xiangshang2');
		}
	});
	//左侧自动滚动到中间
	$('#left-filter .item .icon-fangxingxuanzhongfill').each(function(){
		var index = $(this).parent().index();
		if (index > 5) {
			$(this).parent().parent().scrollTop((index-3)*48);
		}
		console.log(index, 'index');
	});
});