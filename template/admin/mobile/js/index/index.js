$(function(){
	INDEX.init();
});
const INDEX = {
	openDrawer: function() {
		$('#index-page').addClass('drawer-open');
	},
	closeDrawer: function() {
		$('#index-page').removeClass('drawer-open');
	},
	init: function() {
		const _this = this;

		// 抽屉开关事件
		$('#menu-toggle').on('click', function() {
			if ($('#index-page').hasClass('drawer-open')) {
				_this.closeDrawer();
			} else {
				_this.openDrawer();
			}
		});
		$('#drawer-mask, #drawer-close').on('click', function() {
			_this.closeDrawer();
		});

		// 刷新当前 iframe
		$('#iframe-refresh').on('click', function() {
			const iframe = document.getElementById('href-to-iframe');
			if (iframe && iframe.contentWindow) {
				try {
					iframe.contentWindow.location.reload();
				} catch (e) {
					iframe.src = iframe.src;
				}
			}
		});

		// 一级分类菜单点击
		$('#index-page .left-content .nav-content li').on('click', function() {
			const $li = $(this);
			const groupKey = $li.data('to');
			const groupTitle = $li.data('title');
			$li.addClass('select').siblings().removeClass('select');
			$('#index-page .left-two .nav-son-content .item').hide();
			$('#index-page .left-two .nav-son-content [data-for="' + groupKey + '"]').show();
			$('#index-page .left-two .title .text').text(groupTitle);
			post('', {opn: 'setLeft', key: 'last_group', value: groupKey});
		});

		// 二级菜单点击阻止新窗口图标冒泡
		$('#index-page .left-content .nav-son-content li .item-ext').on('click', function(e) {
			e.stopPropagation();
		});

		// 二级页面菜单点击
		$('#index-page .left-content .nav-son-content li').on('click', function(e, isInit) {
			const $li = $(this);
			$('#index-page .left-two .item li').removeClass('select');
			$li.addClass('select');
			const src = $li.data('src');
			$('#href-to-iframe').attr('src', src + '?iframe=1');
			post('', {opn: 'setLeft', key: 'last_url', value: src});
			if (!isInit) {
				_this.closeDrawer();
			}
		});

		// 初始自动选中上次访问菜单或默认第一项
		const $autoGroup = $('#index-page .left-one li.auto-select');
		const $autoItem  = $('#index-page .left-two li.auto-select');
		if ($autoGroup.length > 0) {
			$autoGroup.trigger('click');
		} else {
			$('#index-page .left-one li').eq(0).trigger('click');
		}

		if ($autoItem.length > 0) {
			$autoItem.trigger('click', [true]);
		} else {
			$('#index-page .left-two .item:visible li').eq(0).trigger('click', [true]);
		}
	}
};
