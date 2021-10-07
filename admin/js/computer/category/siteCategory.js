$(function(){
	SITECATEGORY.init();
});
const SITECATEGORY = {
	init: function() {
		const _this = this;
		//新增关联
		$('.add-btn').on('click', function(){
			_this.initData();
		});
		//保存数据
		$('#dealbox .save-btn').on('click', function(){
			let check = true;
			const _thisobj = $(this);
			_thisobj.parent().find('[required="required"]').each(function(){
				const id = parseInt($(this).val());
				if (id < 1) {
					errorTips($(this).prev().text()+' 不能为空');
					check = false;
					return false;
				}
			});
			if (!check) {
				return false;
			}
			_thisobj.button('loading');
			$.post(URI+'category/siteCategory', _thisobj.parent().serializeArray(), function(res){
				if (res.code === '200') {
					successTips(res.message);
					window.location.reload();
				} else {
					_thisobj.button('reset');
					errorTips(res.message);
				}
			});
		});
		//删除关联
		$('#data-list .delete').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			confirm('确定要删除该站点分类吗?', function(_thisobj){
				_thisobj.button('loading');
				$.post(URI+'category/siteCategory', {opn: 'deleteSiteCategory', id: id}, function(res){
					if (res.code === '200') {
						successTips(res.message);
						window.location.reload();
					} else {
						_thisobj.button('reset');
						errorTips(res.message);
					}
				});
			});
		});
		//上传图片
		$('#data-list .avatar-hover img').imageUpload('category', function(data, obj){
			const id = obj.parents('tr').data('id');
			$.post(URI+'category/siteCategory', {opn: 'modifySiteCategory', id: id, attach_id: data.attach_id}, function(res){
				if (res.code === '200') {
					successTips(res.message);
				} else {
					errorTips(res.message);
				}
			});
		});
		//更新input框数据
		$('#data-list input').on('blur', function(){
			const id = $(this).parents('tr').data('id');
			const name = this.name;
			const value = $(this).val();
			$.post(URI+'category/siteCategory', {opn: 'modifySiteCategory', id: id, [name]: value}, function(res){
				if (res.code === '200') {
					successTips(res.message);
					if (name === 'sort') {
						window.location.reload();
					}
				} else {
					errorTips(res.message);
				}
			});
		});
	},
	initData: function(data) {
		if (!data) {
			data = {
				item_id: '0',
				site_id: '-1',
				cate_id: '-1',
			};
		}
		const obj = $('#dealbox');
		for (const i in data) {
			obj.find('[name="'+i+'"]').val(data[i]);
		}
		obj.dealboxShow();
	}
};