$(function(){
	TRANSFER.init();
});
const TRANSFER = {
	init: function() {
		//新增按钮
		$('.btn.reload').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'transfer', {opn: 'reloadCache'}, function(data) {
				_thisobj.button('reset');
			});
		});
		//修改按钮
		$('.btn.modify').on('click', function(){
			const _thisobj = $(this);
			const id = _thisobj.parents('tr').data('id');
			_thisobj.button('loading');
			post(URI+'transfer', {opn: 'getInfo', id: id}, function(data) {
				_thisobj.button('reset');
				TRANSFER.initModel(data);
			});
		});
		//保存按钮
		$('#dealbox .btn.save').on('click', function(){
			const value = $('#dealbox [name="value"]').val();
			if (value === '') {
				errorTips('翻译不能为空');
				return false;
			}
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'transfer', $('#dealbox form').serializeArray(), function(data) {
				window.location.reload();
			}, function(){
				_thisobj.button('reset');
			});
		});
		//自动翻译
		$('#dealbox .glyphicon-transfer').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'transfer', {opn: 'autoTransfer'}, function(data) {
				_thisobj.button('reset');
			});
		});
	},
	initModel: function(data) {
		if (!data) {
			data = {
				name: '',
				tran_id: '0',
				type: '',
				value: '',
				type_name: ''
			};
		}
		const modelobj = $('#dealbox');
		for (const i in data) {
			modelobj.find('[name="'+i+'"]').val(data[i]);
		}
		modelobj.dealboxShow();
	}
};