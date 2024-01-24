$(function(){
	OPERATE.init();
});
const OPERATE = {
	init: function(){
		//初始化
		$('.attr-item.error').on('click', function(e){
			const name = $(this).find('span').eq(0).text();
			const type = $(this).hasClass('attr-name') ? '1' : '2';
			let html = `<form class="mapping-popper">
							<div class="content">
								<p>
									<span>`+name+`</span>
									<input type="hidden" name="from_name" value="`+name+`" />
									<input type="hidden" name="type" value="`+type+`" />
									<i class="glyphicon glyphicon-random"></i>
								</p>
								<div class="map-content">
 									<input class="form-control" type="text" name="name" placeholder="请输入映射值" />
 								</div>
 								<div class="add-map-content"></div>	
								<input type="hidden" name="opn" value="attrMap">`;
			if (type == '2') {
				html += `<button type="button" class="btn btn-info btn-sm btn-add" style="margin-right:10px;">新增映射</button>`;
			}
			html += `<button type="button" class="btn btn-primary btn-sm btn-save">保存</button>
				</div>
				<i class="glyphicon glyphicon-remove"></i>
			</form>`;
			$('.mapping-popper').remove();
			$('body').append(html);
		});
		//移除
		$('body').on('click', '.mapping-popper .glyphicon-remove', function(){
			$(this).parents('.mapping-popper').remove();
		});
		//保存
		$('body').on('click', '.mapping-popper .btn-save', function(){
			const obj = $(this).parents('.mapping-popper');
			const name = obj.find('[name="name"]').val();
			if (!name) {
				errorTips('请输入映射值');
				return false;
			}
			const _thisobj = $(this);
			const fromName = obj.find('p span').eq(0).text();
			_thisobj.button('loading');
			post('', obj.serializeArray(), function(res){
				if (res.code == 200) {

				} else {
					_thisobj.button('reset');
				}
			});
		});
		//新增映射
		$('body').on('click', '.mapping-popper .btn-add', function(){
			let html = `<div class="item">
							<input type="input" class="form-control" name="attr[name][]" value="" />
							<span>: </span>
							<input type="input" class="form-control" name="attr[value][]" value="" />
						</div>`;
			const obj = $(this).parents('.mapping-popper');
			obj.find('.add-map-content').append(html);
		});
	}
};