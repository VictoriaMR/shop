var INDEX = {
	init: function() {
		//左1切换大小
		$('#index-page .left-one .toggle').on('click', function() {
			if ($(this).hasClass('open')) {
				var status = 'close';
			} else {
				var status = 'open';
				//把内容展开
				$('#index-page .person .info').show();
				$('#index-page .left-two').show().css({width: '145px'});
			}
			localStorage.setItem('toggle_status', status);
			INDEX.initPage();
		});
		//左侧悬浮标题
		$('#index-page .left-one [data-title]').on('mouseover', function(){
			if ($('.left-one .toggle').hasClass('open')) return false;
			var offTop = $(this).position().top;
			var oh = $(this).height();
			var diff = (oh - 24) / 2;
			$(this).parent().find('.tooltips').remove();
			$(this).parent().append('<div class="tooltips" style="top:'+(parseInt(offTop)+diff)+'px">'+$(this).data('title')+'</div>');
		}).on('mouseleave', function(){
			$(this).parent().find('.tooltips').remove();
		});
		//左2点击切换收起
		$('#index-page .left-two .title .glyphicon-backward').on('click', function(){
			var width = $('#index-page .nav-left').width();
			$('#index-page .left-two').css({width: '0px'}).hide();
			$('#index-page .nav-left').css({'transition': '0.1s', width: '40px'});
			$('#index-page .body .nav-left .left-content .left-one').css({width: '100%'}).find('.toggle').removeClass('open');
			$('#index-page .person .info').hide();
			$('#index-page .content-right').css({'transition': '0.1s', 'width': 'calc(100% - 40px)'});
		});
		//选择点击
		$('#index-page .left-content .nav-content li').on('click', function(){
			if ($(this).hasClass('select')) return false;
			$(this).addClass('select').siblings().removeClass('select');
			$('#index-page .left-two .item li').removeClass('select');
			$('#index-page .left-two .nav-son-content .item').hide();
			$('#index-page .left-two .nav-son-content [data-for="'+$(this).data('to')+'"]').show();
			$('#index-page .left-two .title .text').text($(this).data('title'));
		});
		$('#index-page .left-content .nav-son-content li').on('click', function(){
			$(this).addClass('select').siblings().removeClass('select');
			var src = $(this).data('src');
			$('#href-to-iframe').attr('src', src);
		});
		this.initPage();
		$('#index-page .left-content .nav-content li').eq(0).trigger('click');
		$('#index-page .left-content .nav-son-content .item:visible li').eq(0).trigger('click');
	},
	initPage:function() {
		var status = localStorage.getItem('toggle_status');
		if (!status) {
			return false;
		}
		if (status == 'open') {
			$('.left-one .toggle').addClass('open');
			$('#index-page .nav-left').css({'width': '256px'});
			$('#index-page .nav-left .left-one').css({width: 'calc(100% - 145px)'})
			$('#index-page .content-right').css({'width': 'calc(100% - 256px)'});
		} else {
			$('.left-one .toggle').removeClass('open');
			$('#index-page .nav-left').css({'width': '185px'});
			$('#index-page .content-right').css({'width': 'calc(100% - 185px)'});
		}
	}
};