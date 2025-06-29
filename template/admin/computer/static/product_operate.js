/* product_operate */
const OPERATE = {
	init: function(){
		const _this = this;
		//初始化
		$('.attr-item').on('click', function(e){
			let toName = $(this).find('span').eq(0).text();
			const type = $(this).hasClass('attr-name') ? '1' : '2';
			const fromName = $(this).data('name');
			let attrName = $(this).parents('tr').find('.attr-name').data('name');
			attrName = $('#sku-list .attr-name[data-name="'+attrName+'"]').eq(0).text();
			toName = $('#sku-list .attr-value[data-name="'+toName+'"]').eq(0).text();
			if (toName == fromName) {
				toName = '';
			}
			let ext = $(this).parents('td').find('.success').eq(0).data('ext');
			let extHtml = '';
			if (ext) {
				let setValue = false;
				if ($(this).parents('td').find('.success').eq(0).data('name') == fromName) {
					setValue = true;
				}
				for (let i in ext) {
					if (i != attrName) {
						extHtml += `<div class="item">
										<input type="input" class="form-control" name="attr[name][]" value="`+i+`" />
										<span>: </span>
										<input type="input" class="form-control" name="attr[value][]" value="`+(setValue?ext[i]:'')+`" />
									</div>`;
					}
				}
			}
			let html = `<div class="input-group">
							<div class="input-group-addon"><span>`+attrName+`</span></div>
							<input class="form-control" type="text" name="name" placeholder="请输入映射值" value="`+toName+`"/>
						</div>
						<div class="add-map-content">`+extHtml+`</div>`;
			html += `<div class="mt20">`;
			if (type == '2') {
				html += `<button type="button" class="btn btn-info btn-sm btn-add" style="margin-right:10px;">新增映射</button>`;
			}
			html += `<button type="button" class="btn btn-success btn-sm btn-tmp-save" style="margin-right:10px;">临时映射</button><button type="button" class="btn btn-primary btn-sm btn-save">保存</button>`;
			html += `</div>`;
			var obj = $('.map-modal');
			obj.find('.title').text(fromName);
			obj.find('[name="from_name"]').val(fromName);
			obj.find('[name="type"]').val(type);
			obj.find('.content').html(html);
			obj.modalShow();
		});
		//保存
		$('.map-modal').on('click', '.btn-save', function(){
			const obj = $(this).parents('.map-modal');
			const name = obj.find('[name="name"]').val();
			if (!name) {
				errorTips('请输入映射值');
				return false;
			}
			const _thisobj = $(this);
			_thisobj.button('loading');
			post('', obj.serializeArray(), function(res){
				showTips(res);
				_thisobj.button('reset');
				if (res.code) {
					_this.mapAttrName(obj.find('.title').text(), name);
				}
				obj.modalHide();
			});
		});
		//新增映射
		$('.map-modal').on('click', '.btn-add', function(){
			let html = `<div class="item">
							<input type="input" class="form-control" name="attr[name][]" value="" />
							<span>: </span>
							<input type="input" class="form-control" name="attr[value][]" value="" />
						</div>`;
			$(this).parents('.map-modal').find('.add-map-content').append(html);
		});
		// 临时映射
		$('.map-modal').on('click', '.btn-tmp-save', function(){
			const obj = $(this).parents('.map-modal');
			const type = obj.find('[name="type"]').val(); // 1属性 2属性值
			const name = obj.find('[name="name"]').val();
			if (!name) {
				errorTips('请输入映射值');
				return false;
			}
			const _thisobj = $(this);
			if (type == 1) {
				_this.mapAttrName(obj.find('.title').text(), name);
			} else {
				let nameExt = {};
				obj.find('.add-map-content .item').each(function(){
					let tmpName = $(this).find('input').eq(0).val();
					let tmpValue = $(this).find('input').eq(1).val();
					if (tmpName && tmpValue) {
						nameExt[tmpName] = tmpValue;
					}
				});
				nameExt[obj.find('.input-group-addon').text()] = name;
				_this.mapAttrValue(obj.find('.title').text(), nameExt);
			}
			obj.modalHide();
		});
		// 修改分类
		$('.change-category-btn').on('click', function(){
			$('.change-category-modal').modalShow();
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
			$('#add-product-page .category-name').html(cateHtml);
			$('#add-product-page [name="cate_id"]').val(cid);
			obj.hide();
		});
		// 修改站点
		$('.change-site-btn').on('click', function(){
			$('.change-site-modal').modalShow();
		});
		//保存修改站点
		$('.change-site-modal .btn-save').on('click', function(){
			const obj = $(this).parents('.change-site-modal');
			const sid = obj.find('[name="site_id"]').val();
			if (sid == '0') {
				errorTips('请选择站点');
				return false;
			}
			$('#add-product-page .site-name').html(sid+' - '+obj.find('[name="site_id"] option:selected').text());
			$('#add-product-page [name="site_id"]').val(sid);
			obj.hide();
		});
		// 图片切换
		$('.hy-sl-list-inline li').on('click', function(){
			if ($(this).hasClass('hy-sl-selected')) {
				return false;
			}
			$(this).addClass('hy-sl-selected').siblings().removeClass('hy-sl-selected');
			$('.pic-wrap[data-id="'+$(this).data('id')+'"]').show().siblings().hide();
			DROP.load();
		});
		// 批量修改弹窗
		$('.batch-btn').on('dblclick', function(){
			const type = $(this).data('type');
			let html = '';
			var name = '';
			switch (type) {
				case 'unit':
					name = '单位';
					html += `<select name="`+type+`" class="form-control">
									<option value="0">--</option>
									<option value="1">件</option>
									<option value="2">个</option>
									<option value="3">套</option>
									<option value="4">打</option>
									<option value="5">箱</option>
								</select>`;
					break;
				default:
					if (type == 'weight') {
						name = '重量(g)';
					} else if (type == 'length') {
						name = '长度(cm)';
					} else if (type == 'width') {
						name = '宽度(cm)';
					} else if (type == 'hight') {
						name = '高度(cm)';
					}
					html += `<input class="form-control" type="text" name="`+type+`" value="" placeholder="`+name+`"/>`;
					break;
			}
			var obj = $('.batch-edit-modal');
			obj.find('.title').text(name);
			obj.find('[name="type"]').val(type);
			obj.find('.content').html(html);
			obj.modalShow();
		});
		// 批量确认
		$('.batch-edit-modal').on('click', '.btn-save', function(){
			const obj = $(this).parents('.batch-edit-modal');
			const type = obj.find('[name="type"]').val();
			const value = obj.find('[name="'+type+'"]').val();
			$('#sku-list .'+type).val(value);
			obj.modalHide();
		});
		// 描述删除
		$('.desc-info-content .glyphicon-remove').on('click', function(){
			$(this).parents('.item').remove();
		});
		// 描述添加
		$('.btn-add-desc').on('click', function(){
			var html = `<div class="item">
						<input type="text" class="form-control" name="desc[name][]" value="">
						<span>: </span>
						<input type="text" class="form-control" name="desc[value][]" value="">
						<i class="glyphicon glyphicon-remove"></i>
					</div>`;
			$('.desc-info-content .content').append(html);
		});
		// 设置spu图
		$('.set-spu-cover').on('click', function(){
			var pObj = $(this).parents('.item');
			if (pObj.find('.spu-sign').length > 0) {
				return false;
			}
			pObj.find('.image-left-tips').append('<div class="spu-sign">SPU</div>');
			pObj.siblings().find('.spu-sign').remove();
			$('#add-product-page form [name="spu_image"]').val(pObj.find('img').attr('src'));
		});
		// 设置sku图
		$('.set-sku-cover').on('click', function(){
			var img = $(this).parents('.item').find('.pic-thumb img').attr('src');
			var pObj = $('.sku-modal');
			pObj.data('img', img);
			pObj.find('input[type="checkbox"]').prop('checked', false);
			pObj.modalShow();
		});
		// 保存sku图
		$('.sku-modal .btn-save').on('click', function(){
			var pObj = $(this).parents('.sku-modal');
			var img = pObj.data('img');
			pObj.find('input[type="checkbox"]:checked').each(function(){
				$('#sku-list tr[data-sku="'+$(this).data('sku')+'"] img').attr('src', img);
				$('#sku-list tr[data-sku="'+$(this).data('sku')+'"] input.img').val(img);
			});
			pObj.modalHide();
		});
		// 保存配置
		$('.confirm-btn').on('click', function(){
			var _thisobj = $(this);
			var obj = $(this).parents('form');
			if (obj.find('[name="site_id"]').val() == '0') {
				errorTips('请选择站点');
				return false;
			}
			if (obj.find('[name="cate_id"]').val() == '0') {
				errorTips('请选择分类');
				return false;
			}
			if (obj.find('[name="spu_name"]').val() == '') {
				errorTips('请设置产品标题');
				return false;
			}
			_thisobj.button('loading');
			post('', obj.serializeArray(), function(res){
				showTips(res);
				if (res.code) {
					setTimeout(function(){
						_thisobj.button('reset');
						window.history.back();
					}, 300);
				} else {
					_thisobj.button('reset');
				}
			});
		});
		// 规则生成
		$('.attr-rule-btn').on('click', function(){
			var value = $('.attr-rule [name="attr_rule_value"]').val();
			if (!value) {
				errorTips('请输入分配规则');
				$('.attr-rule [name="attr_rule_value"]').focus();
				return false;
			}
			var nameArr = {};
			$('.attr-rule-name [name="attr_rule_name[]"]').each(function(index){
				if ($(this).val()) {
					nameArr[index] = $(this).val();
				}
			});
			if (JSON.stringify(nameArr) == '{}') {
				errorTips('没有分配属性名');
				return false
			}
			$('.attr-info-content .attr-item').each(function(){
				var name = $(this).data('name');
				var tmpArr = [];
				for (var i=0; i<name.length; i++) {
					if (i % value == 0) {
						tmpArr.push(name.substr(i, value));
					}
				}
				var nameExt = {};
				for (var i in nameArr) {
					nameExt[nameArr[i]] = tmpArr[i];
				}
				_this.mapAttrValue(name, nameExt);
			});
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
	},
	mapAttrName: function(fromName, toName) {
		$('#sku-list .attr-name[data-name="'+fromName+'"]').removeClass('error').addClass('success').text(toName);
		$('.attr-info-content .attr-name[data-name="'+fromName+'"]').removeClass('error').addClass('success').attr('title', toName);
	},
	mapAttrValue: function(fromName, nameExt) {
		var obj = $('#sku-list .attr-value[data-name="'+fromName+'"]');
		var pObj = obj.parents('td');
		pObj.empty();
		let title = '';
		var sku_id = pObj.parent('tr').data('sku');
		if (nameExt.toString() != '{}') {
			var html = '';
			for (var i in nameExt) {
				html += `<p class="attr-map">
							<span class="attr-name success" data-name="`+i+`">`+i+`</span>
							<span>: </span>
							<span class="attr-value success" data-name="`+nameExt[i]+`">`+nameExt[i]+`</span>
							<input type="hidden" name="sku[`+sku_id+`][attr][`+i+`]" value="`+nameExt[i]+`">
						</p>`;
				title += i+':'+nameExt[i]+';';
			}
			pObj.append(html);
		}
		$('.attr-info-content .attr-value[data-name="'+fromName+'"]').removeClass('error').addClass('success').attr('title', title).data('ext', nameExt);
	}
};
const DROP = {
	init: function() {
		var _this = this;
		$('.pic-wrap').on('mousedown', '.item', function(){
			// 计算当前对象绝对坐标
			_this.obj = $(this);
		});
		this.load();
	},
	load: function() {
		this.pObj = $('.right .pic-wrap:visible');
		if (this.pObj.length == 0) {
			return false;
		}
		this.p_w = this.pObj.width();
		this.p_h = this.pObj.height();
		this.p_x = this.pObj.offset().left;
		this.p_y = this.pObj.offset().top;
		this.pObj.on('mouseup', '.item .cover', this.end);
		this.pObj.on('mousemove', '.item .cover', this.move);
		this.pObj.on('mousedown', '.item .cover', this.down);
	},
	down: function(e) {
		DROP.obj = $(this).parents('.item');
		DROP.w = DROP.obj.width();
		DROP.h = DROP.obj.height();
		DROP.status = true;
		DROP.obj.before('<div class="index"></div');
		DROP.obj.css({'position':'fixed', 'left':DROP.obj.offset().left - $(document).scrollLeft(), 'top':DROP.obj.offset().top - $(document).scrollTop(), 'z-index':2});
		DROP.pObj.append(DROP.obj);
	},
	end: function() {
		if (DROP.pObj.find('.index').length > 0) {
			DROP.obj.css({'position':'relative', left:0, top:0, 'z-index':1});
			DROP.pObj.find('.index').before(DROP.obj);
			DROP.pObj.find('.index').remove();
			DROP.index();
		}
		DROP.status = false;
		DROP.obj = null;
	},
	move: function(e) {
		if (DROP.status) {
			DROP.x = e.pageX - DROP.w/2;
			DROP.y = e.pageY - DROP.h/2;
			DROP.obj.css({'left':DROP.x - $(document).scrollLeft(), 'top':DROP.y - $(document).scrollTop()});
			DROP.offset();
		}
	},
	offset: function() {
		if (this.x > this.p_x && this.x < this.p_x + this.p_w && this.y > this.p_y && this.y < this.p_y + this.p_h) {
			// 判断在目标位置
			var cos_num = parseInt((this.p_w - 10) / (this.w + 10));
			var x_index = parseInt((this.x - this.p_x) / (this.w + 10)) + cos_num*parseInt((this.y - this.p_y) / (this.h + 10));
			if (x_index != this.in_index) {
				this.in_index = x_index;
				this.pObj.find('.index').remove();
				this.pObj.find('.item').eq(x_index).before('<div class="index"></div');
			}
		}
	},
	index: function() {
		DROP.pObj.find('.item').each(function(index){
			$(this).find('.num').text(index+1);
		});
	}
};
$(function(){
	OPERATE.init();
	DROP.init();
});
