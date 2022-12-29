$(function(){
	TRANSFER.init();
});
const TRANSFER = {
	init: function() {
		var _this = this;
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
			post(URI+'transfer', {opn: 'getInfo', id: id}, function(res) {
				if (res.code == 200) {
					_this.initModel(data);
				} else {
					showTips(res);
				}
				_thisobj.button('reset');
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
			post(URI+'transfer', $('#dealbox form').serializeArray(), function(res) {
				showTips(res);
				if (res.code == 200) {
					window.location.reload();
				} else {
					_thisobj.button('reset');
				}
			});
		});
		//自动翻译
		$('#dealbox .glyphicon-transfer').on('click', function(){
			const _thisobj = $(this);
			_thisobj.button('loading');
			post(URI+'transfer', {opn: 'autoTransfer'}, function(res) {
				showTips(res);
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