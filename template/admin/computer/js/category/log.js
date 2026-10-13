$(function(){
	CATEGORY_LOG.init();
});

const CATEGORY_LOG = {
	init: function(){
		const _this = this;

		// 日期选择器
		if ($.fn.datetimepicker) {
			$('.form_datetime').datetimepicker({
				language: 'zh-CN',
				format: 'yyyy-mm-dd',
				minView: 'month',
				autoclose: true,
				todayBtn: true,
				clearBtn: true
			});
		}

		// 查看字段明细弹窗
		$('#log-list .view-detail-btn').on('click', function(){
			const btn = $(this);
			const info = btn.data('info') || '-';
			const type = parseInt(btn.data('type') || 0);
			const target = btn.data('target') || '-';
			const user = btn.data('user') || '-';
			const time = btn.data('time') || '-';
			let detail = btn.data('detail');

			const typeNames = {
				0: '类目',
				1: '多语言',
				2: '排序'
			};

			let list = [];
			if (typeof detail === 'object' && detail !== null) {
				list = Array.isArray(detail) ? detail : [detail];
			} else if (typeof detail === 'string') {
				try {
					const parsed = JSON.parse(detail);
					list = Array.isArray(parsed) ? parsed : [parsed];
				} catch(e) {
					list = [];
				}
			}

			const modal = $('#dealbox-log-detail');
			modal.find('.modal-type-name').text(typeNames[type] || '通用分类');
			modal.find('.modal-action-title').text(info);
			modal.find('.modal-target-name').text(target);
			modal.find('.modal-meta-info').text(user + ' / ' + time);

			// 渲染字段明细表格
			const tbody = modal.find('#diff-table-body');
			tbody.empty();

			// 判断是 Diff 还是 Snapshot
			let isDiff = false;
			if (list.length > 0 && typeof list[0] === 'object' && (list[0].old !== undefined || list[0].new !== undefined)) {
				isDiff = true;
			}

			if (isDiff) {
				modal.find('.diff-table-container thead').html('<tr><th width="160">变更字段</th><th width="200">修改前 (原值)</th><th>修改后 (新值)</th></tr>');
				for (let i = 0; i < list.length; i++) {
					const item = list[i];
					const label = item.label || item.field || '字段';
					const oldVal = (item.old !== undefined && item.old !== null) ? String(item.old) : '-';
					const newVal = (item.new !== undefined && item.new !== null) ? String(item.new) : '-';
					
					const tr = $('<tr></tr>');
					tr.append('<td><strong>' + _this.escapeHtml(label) + '</strong></td>');
					tr.append('<td style="color:#64748b; background:#f8fafc; text-decoration:line-through;">' + _this.escapeHtml(oldVal) + '</td>');
					tr.append('<td style="color:#0284c7; font-weight:600; background:#f0f9ff;">' + _this.escapeHtml(newVal) + '</td>');
					tbody.append(tr);
				}
			} else {
				modal.find('.diff-table-container thead').html('<tr><th width="160">属性字段</th><th>设定属性数值</th></tr>');
				for (let i = 0; i < list.length; i++) {
					const item = list[i];
					const label = item.label || item.field || ('属性 #' + (i+1));
					const val = (item.val !== undefined && item.val !== null) ? String(item.val) : (typeof item === 'object' ? JSON.stringify(item) : String(item));
					
					const tr = $('<tr></tr>');
					tr.append('<td><strong>' + _this.escapeHtml(label) + '</strong></td>');
					tr.append('<td style="color:#1e293b;">' + _this.escapeHtml(val) + '</td>');
					tbody.append(tr);
				}
			}

			if (list.length === 0) {
				tbody.append('<tr><td colspan="3" class="tc text-muted" style="padding: 20px;">无结构化字段数据</td></tr>');
			}

			modal.data('raw-json', typeof detail === 'string' ? detail : JSON.stringify(detail, null, 2));
			modal.dealboxShow('字段变更记录明细');
		});

		// 复制原始数据
		$('#dealbox-log-detail .copy-json-btn').on('click', function(){
			const raw = $('#dealbox-log-detail').data('raw-json');
			if (!raw) {
				if (typeof errorTips === 'function') errorTips('内容为空，无需复制');
				return;
			}
			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(raw).then(function(){
					if (typeof successTips === 'function') successTips('已成功复制原始数据到剪贴板');
				}).catch(function(){
					_this.fallbackCopy(raw);
				});
			} else {
				_this.fallbackCopy(raw);
			}
		});

		// 关闭弹窗
		$('#dealbox-log-detail').on('click', '.close, .close-btn, .mask', function(){
			$('#dealbox-log-detail').dealboxHide();
		});
	},

	escapeHtml: function(text) {
		if (text === null || text === undefined) return '';
		return String(text)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	},

	fallbackCopy: function(text) {
		const ta = document.createElement('textarea');
		ta.value = text;
		ta.style.position = 'fixed';
		ta.style.left = '-9999px';
		ta.style.top = '0';
		document.body.appendChild(ta);
		ta.focus();
		ta.select();
		try {
			const successful = document.execCommand('copy');
			if (successful && typeof successTips === 'function') {
				successTips('已成功复制原始数据到剪贴板');
			}
		} catch (err) {}
		document.body.removeChild(ta);
	}
};
