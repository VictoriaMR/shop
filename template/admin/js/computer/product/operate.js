$(function(){
	OPERATE.init();
});
const OPERATE = {
	init: function(){
		const _this = this;
		//初始化
		$('.attr-item').on('click', function(e){
			const name = $(this).find('span').eq(0).text();
			const type = $(this).hasClass('attr-name') ? '1' : '2';
			const toName = $(this).data('name');
			let ext = $(this).data('ext');
			let extHtml = '';
			if (ext) {
				for (let i in ext) {
					extHtml += `<div class="item">
									<input type="input" class="form-control" name="attr[name][]" value="`+i+`" />
									<span>: </span>
									<input type="input" class="form-control" name="attr[value][]" value="`+ext[i]+`" />
								</div>`;
				}
			}
			let html = `<div class="mapping-popper s-modal">
							<div class="content">
								<p>
									<span>`+name+`</span>
									<input type="hidden" name="from_name" value="`+name+`" />
									<input type="hidden" name="type" value="`+type+`" />
									<i class="glyphicon glyphicon-random"></i>
								</p>
								<div class="content">
 									<input class="form-control" type="text" name="name" placeholder="请输入映射值" value="`+toName+`"/>
 								</div>
 								<div class="add-map-content">`+extHtml+`</div>	
								<input type="hidden" name="opn" value="attrMap">`;
			if (type == '2') {
				html += `<button type="button" class="btn btn-info btn-sm btn-add" style="margin-right:10px;">新增映射</button>`;
			}
			html += `<button type="button" class="btn btn-success btn-sm btn-tmp-save" style="margin-right:10px;">临时保存</button>`;
			html += `<button type="button" class="btn btn-primary btn-sm btn-save">保存</button>
				</div>
				<i class="glyphicon glyphicon-remove"></i>
			</div>`;
			$('.mapping-popper').remove();
			$('body').append(html);
		});
		//移除
		$('body').on('click', '.s-modal .glyphicon-remove', function(){
			$(this).parents('.s-modal').remove();
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
				showTips(res);
				_thisobj.button('reset');
				if (res.code == 200) {

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
		// 修改分类
		$('.change-category-bth').on('click', function(){
			$('.change-category-modal').show();
		});
		// 分类变化
		$('.change-category-modal [name="root_category_id"]').on('change', function(){
			_this.initCate($(this).val());
		});
		//保存修改分类
		$('.change-category-modal .btn-save').on('click', function(){
			const obj = $(this).parents('.change-category-modal');
			const cid = obj.find('[name="category_id"]').val();
			if (cid == '0') {
				errorTips('请选择分类');
				return false;
			}
			const cateHtml = _this.pCate(cid, '');
			$('.category-name').html(cateHtml);
			$('#add-product-page [name="cate_id"]').val(cid);
			obj.hide();
		});
		// 图片切换
		$('.hy-sl-list-inline li').on('click', function(){
			if ($(this).hasClass('hy-sl-selected')) {
				return false;
			}
			$(this).addClass('hy-sl-selected').siblings().removeClass('hy-sl-selected');
			$('.pic-wrap[data-id="'+$(this).data('id')+'"]').show().siblings().hide();
		});
		// 批量修改弹窗
		$('.batch-btn').on('dblclick', function(){
			const type = $(this).data('type');
			let html = `<div class="batch-edit-modal s-modal">
							<input type="hidden" name="type" value="`+type+`">
							<div class="content">`;
								
			switch (type) {
				case 'unit':
					html += `<p>
								<span>单位</span>
							</p>
							<div class="content">
								<select name="`+type+`" class="form-control">
                					<option value="0">--</option>
                					<option value="1">件</option>
                					<option value="2">个</option>
                					<option value="3">套</option>
                					<option value="4">打</option>
                					<option value="5">箱</option>
                				</select>
							</div>`;
					break;
				default:
					let name = '';
					if (type == 'weight') {
						name = '重量(g)';
					} else if (type == 'length') {
						name = '长度(cm)';
					} else if (type == 'width') {
						name = '宽度(cm)';
					} else if (type == 'hight') {
						name = '高度(cm)';
					}
					html += `<p>
							<span>`+name+`</span>
						</p>
						<div class="content">
							<input class="form-control" type="text" name="`+type+`" value="" placeholder="`+name+`"/>
						</div>`;
					break;
			}
			html += `<button type="button" class="btn btn-primary btn-sm btn-save">保存</button>
				</div>
				<i class="glyphicon glyphicon-remove"></i>
			</div>`;
			$('.batch-edit-modal').remove();
			$('body').append(html);
		});
		// 批量确认
		$('body').on('click', '.batch-edit-modal .btn-save', function(){
			const obj = $(this).parents('.batch-edit-modal');
			const type = obj.find('[name="type"]').val();
			const value = obj.find('[name="'+type+'"]').val();
			$('#sku-list .'+type).val(value);
			obj.hide();
		});
		// 临时映射
		$('body').on('click', '.batch-edit-modal .btn-tmp-save', function(){
		});
		// 描述删除
		$('.desc-info-content .glyphicon-remove').on('click', function(){
			$(this).parents('.item').remove();
		});
	},
	initCate: function(pid) {
		let html = '';
		if (pid == '0') {
			html += '<option value="0">请先选择品类</option>';
		} else {
			let status = false;
			for (let i=0; i<category.length; i++) {
				if (status && category[i].parent_id == '0') {
					break;
				}
				if (pid == category[i].cate_id) {
					status = true;
				}
				if (status && category[i].parent_id != '0') {
					let disable = '';
					if (category[i+1] && category[i+1].parent_id == category[i].cate_id) {
						disable = 'disabled';
					}
					html += '<option value="'+category[i].cate_id+'" '+disable+'>'+'&nbsp;&nbsp;'.repeat(category[i].level)+category[i].name+'</option>';
				}
			}
		}
		$('.change-category-modal [name="category_id"]').html(html);
	},
	pCate: function(cid, html) {
		for (let i=0; i<category.length; i++) {
			if (cid == category[i].cate_id) {
				html = category[i].name+(html?' - '+html:html);
				if (category[i].parent_id == '0') {
					return html;
				} else {
					return this.pCate(category[i].parent_id, html);
				}
			}
		}
	}
};