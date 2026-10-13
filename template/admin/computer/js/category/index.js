$(function(){
	CATEGORYINDEX.init();
});

const CATEGORYINDEX = {
	init: function() {
		const _this = this;

		// 打开新增品类弹窗
		$('#add-data-btn').on('click', function(){
			_this.showEditModal();
		});

		// 编辑品类
		$('#data-list .modify').on('click', function(e){
			e.stopPropagation();
			const btn = $(this);
			btn.button('loading');
			const tr = btn.closest('tr.item');
			const id = tr.data('id');

			post('', {opn: 'getCateInfo', cate_id: id}, function(res){
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(err){}
				}
				if (res && (res.code == 1 || res.code == 200)) {
					const data = res.data || res.msg || {};
					_this.showEditModal(data);
				} else {
					showTips(res || {code: 0, msg: '获取品类信息失败'});
				}
			});
		});

		// 状态切换开关
		$('#data-list .switch_botton').on('click', function(e){
			e.stopPropagation();
			const btn = $(this);
			const tr = btn.closest('tr.item');
			const id = tr.data('id');
			const curStatus = parseInt(btn.data('status') || 0);
			const newStatus = curStatus === 1 ? 0 : 1;

			post('', {opn: 'modifyCategory', id: id, status: newStatus}, function(res){
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(err){}
				}
				showTips(res);
				if (res && (res.code == 1 || res.code == 200)) {
					btn.switchBtn(newStatus);
				}
			});
		});

		// 保存按钮
		$('#dealbox .save').on('click', function(){
			const name = $.trim($('#dealbox form input[name="name"]').val());
			if (!name) {
				errorTips('品类中文名称不能为空');
				$('#dealbox form input[name="name"]').focus();
				return false;
			}

			const btn = $(this);
			btn.button('loading');
			post('', $('#dealbox form').serializeArray(), function(res){
				btn.button('reset');
				if (typeof res === 'string') {
					try { res = JSON.parse(res); } catch(err){}
				}
				showTips(res);
				if (res && (res.code == 1 || res.code == 200)) {
					$('#dealbox').dealboxHide();
					setTimeout(function(){
						window.location.reload();
					}, 500);
				}
			});
		});

		// 删除品类
		$('#data-list .delete').on('click', function(e){
			e.stopPropagation();
			const tr = $(this).closest('tr.item');
			const id = tr.data('id');
			const name = tr.find('.cate_name').text().trim();

			confirm('确定要删除品类【' + name + '】吗？', function(btn, modal){
				post('', {opn: 'deleteCategory', cate_id: id}, function(res){
					if (modal && modal.hide) modal.hide();
					if (typeof res === 'string') {
						try { res = JSON.parse(res); } catch(err){}
					}
					showTips(res);
					if (res && (res.code == 1 || res.code == 200)) {
						tr.fadeOut(300, function(){
							tr.remove();
							if ($('#data-list tbody tr.item').length === 0) {
								window.location.reload();
							}
						});
					}
				});
			});
		});

		// 关闭弹窗
		$('#dealbox').on('click', '.close, .mask', function(){
			$('#dealbox').dealboxHide();
		});
	},

	showEditModal: function(data) {
		const form = $('#dealbox form')[0];
		if (form) form.reset();

		if (!data) {
			data = {
				cate_id: 0,
				parent_id: 0,
				name: '',
				name_en: ''
			};
		}

		const obj = $('#dealbox');
		obj.find('input[name="cate_id"]').val(data.cate_id || 0);
		obj.find('input[name="parent_id"]').val(data.parent_id || 0);
		obj.find('input[name="name"]').val(data.name || '');
		obj.find('input[name="name_en"]').val(data.name_en || '');
		obj.find('.dealbox-title').text(data.cate_id ? '编辑品类' : '新增品类');
		obj.dealboxShow();
	}
};