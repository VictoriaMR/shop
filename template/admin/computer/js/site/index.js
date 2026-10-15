$(function(){
	SITE.init();
});

const SITE = {
	init: function() {
		const _this = this;

		// 状态筛选
		$('.status-filter button').on('click', function(){
			const status = $(this).data('status');
			const form = $(this).closest('form');
			form.find('input[name="status"]').val(status);
			form.submit();
		});

		// 新增按钮
		$('#site-page .btn.add-btn').on('click', function(){
			_this.initInfo('新增站点');
		});

		// 编辑按钮（优先读取行内 data-info，零额外请求）
		$('#site-page .btn.modify').on('click', function(){
			const tr = $(this).closest('tr');
			let rowData = tr.data('info') || null;
			if (typeof rowData === 'string') {
				try { rowData = JSON.parse(rowData); } catch(e){}
			}
			if (rowData && rowData.site_id) {
				_this.initInfo('编辑站点', rowData);
				return;
			}
			const obj = $(this);
			obj.button('loading');
			post('', {opn: 'getInfo', id: tr.data('id')}, function(res){
				obj.button('reset');
				if (res && res.code == 1) {
					_this.initInfo('编辑站点', res.data);
				} else {
					showTips(res);
				}
			});
		});

		// 弹窗内状态开关
		$('#dealbox-info .modal-status-switch').on('click', function(){
			const sw = $(this);
			const next = parseInt(sw.attr('data-status')) === 1 ? 0 : 1;
			sw.switchBtn(next);
			$('#dealbox-info input[name="status"]').val(next);
		});

		// 关闭弹窗
		$('#dealbox-info .close, #dealbox-info .mask').on('click', function(){
			$('#dealbox-info').dealboxHide();
		});

		// 保存按钮
		$('#dealbox-info .btn.save-btn').on('click', function(){
			const obj = $(this);
			const form = $('#dealbox-info form');
			const name = $.trim(form.find('[name="name"]').val());
			const path = $.trim(form.find('[name="path"]').val());
			if (name === '' || path === '') {
				errorTips('站点名称和模板目录不能为空');
				return false;
			}
			obj.button('loading');
			post('', form.serializeArray(), function(res){
				showTips(res);
				if (res && res.code == 1) {
					$('#dealbox-info').dealboxHide();
					setTimeout(function(){
						window.location.reload();
					}, 600);
				} else {
					obj.button('reset');
				}
			});
		});

		// 列表状态开关
		$('#data-list .site-status-switch').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.closest('tr').data('id');
			const status = parseInt(_thisobj.attr('data-status')) === 0 ? 1 : 0;
			post('', {opn: 'modifySite', id: id, status: status}, function(res){
				showTips(res);
				if (res && res.code == 1) {
					_thisobj.switchBtn(status);
				}
			});
		});
	},

	initInfo: function(title, data) {
		if (!data) {
			data = {
				site_id: 0,
				name: '',
				type: 'home',
				domain: '',
				path: '',
				email: '',
				view_cache: 1,
				static_cache: 1,
				debug: 0,
				status: 1
			};
		}
		const obj = $('#dealbox-info');
		obj.find('[name="site_id"]').val(data.site_id || 0);
		obj.find('[name="name"]').val(data.name || '');
		obj.find('[name="type"]').val(data.type || 'home');
		obj.find('[name="domain"]').val(data.domain || '');
		obj.find('[name="path"]').val(data.path || '');
		obj.find('[name="email"]').val(data.email || '');
		obj.find('[name="view_cache"]').prop('checked', parseInt(data.view_cache) === 1);
		obj.find('[name="static_cache"]').prop('checked', parseInt(data.static_cache) === 1);
		obj.find('[name="debug"]').prop('checked', parseInt(data.debug) === 1);

		const st = parseInt(data.status) === 1 ? 1 : 0;
		obj.find('[name="status"]').val(st);
		obj.find('.modal-status-switch').switchBtn(st);

		obj.dealboxShow(title);
		return true;
	}
};