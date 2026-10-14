$(function() {
	PAYMENTLOG.init();
});

const PAYMENTLOG = {
	currentJsonText: '',
	typeMap: {},
	siteMap: {},
	paymentMap: {},

	init: function() {
		var _this = this;
		var $page = $('#payment-log-page');
		this.typeMap = $page.data('types') || {};
		this.siteMap = $page.data('sites') || {};
		this.paymentMap = $page.data('payments') || {};

		// 查看详情
		$('#log-list').on('click', '.view-detail-btn', function() {
			var $btn = $(this);
			var rawDetail = $btn.attr('data-detail') || '{}';
			var info = $btn.attr('data-info') || '操作详情';
			var typeName = $btn.attr('data-type') || '-';
			var target = $btn.attr('data-target') || '-';
			var user = $btn.attr('data-user') || '-';
			var time = $btn.attr('data-time') || '-';

			_this.openDetailModal(rawDetail, info, typeName, target, user, time);
		});

		// 关闭弹窗
		$(document).on('click', '.modal-close', function() {
			$(this).closest('.modal-mask').fadeOut(150);
		});
		$(document).on('click', '.modal-mask', function(e) {
			if (e.target === this) {
				$(this).fadeOut(150);
			}
		});

		// 复制 JSON
		$('#dealbox-log-detail').on('click', '.copy-json-btn', function() {
			if (!_this.currentJsonText) {
				errorTips('暂无可复制内容');
				return;
			}
			var $btn = $(this);
			_this.copyText(_this.currentJsonText, function() {
				var oldHtml = $btn.html();
				$btn.html('<i class="fa fa-check"></i> 已复制');
				successTips('JSON 已复制到剪贴板');
				setTimeout(function() {
					$btn.html(oldHtml);
				}, 1500);
			});
		});
	},

	openDetailModal: function(rawDetail, info, typeName, target, user, time) {
		var $modal = $('#dealbox-log-detail');
		$modal.find('.modal-type-name')
			.removeClass('biz-account biz-used')
			.addClass(typeName === '账户使用' ? 'biz-used' : 'biz-account')
			.text(typeName);
		$modal.find('.modal-action-title').text(info);
		$modal.find('.modal-target-name').text(target);
		$modal.find('.modal-meta-info').text('操作人：' + user + ' | 时间：' + time);

		var parsed = null;
		try {
			parsed = JSON.parse(rawDetail);
			this.currentJsonText = JSON.stringify(parsed, null, 2);
		} catch (e) {
			this.currentJsonText = rawDetail;
		}

		var $body = $('#diff-table-body').empty();

		if (!parsed || typeof parsed !== 'object') {
			$body.append(
				'<div class="detail-field-card">' +
					'<div class="detail-val-content">' + this.escapeHtml(rawDetail) + '</div>' +
				'</div>'
			);
			$modal.fadeIn(150);
			return;
		}

		var keys = Object.keys(parsed);
		if (keys.length === 0) {
			$body.append('<div class="empty-state"><p>无字段变更记录</p></div>');
			$modal.fadeIn(150);
			return;
		}

		for (var i = 0; i < keys.length; i++) {
			var key = keys[i];
			var item = parsed[key];
			if (!item || typeof item !== 'object') {
				$body.append(
					'<div class="detail-field-card">' +
						'<div class="detail-field-header">' +
							'<span class="detail-field-title">' + this.escapeHtml(key) + '</span>' +
						'</div>' +
						'<div class="detail-val-row">' +
							'<span class="detail-val-tag">快照值：</span>' +
							'<div class="detail-val-content">' + this.formatValue(key, item) + '</div>' +
						'</div>' +
					'</div>'
				);
				continue;
			}

			var field = item.field || key;
			var label = item.label || field;
			if ('old' in item || 'new' in item) {
				$body.append(
					'<div class="detail-field-card">' +
						'<div class="detail-field-header">' +
							'<span class="detail-field-title">' + this.escapeHtml(label) + '</span>' +
							'<span class="detail-field-code">' + this.escapeHtml(field) + '</span>' +
						'</div>' +
						'<div class="detail-val-row">' +
							'<span class="detail-val-tag">变更前：</span>' +
							'<div class="detail-val-content diff-old">' + this.formatValue(field, item.old) + '</div>' +
						'</div>' +
						'<div class="detail-val-row">' +
							'<span class="detail-val-tag">变更后：</span>' +
							'<div class="detail-val-content diff-new">' + this.formatValue(field, item.new) + '</div>' +
						'</div>' +
					'</div>'
				);
			} else {
				var snapVal = item.val !== undefined ? item.val : item.value;
				$body.append(
					'<div class="detail-field-card">' +
						'<div class="detail-field-header">' +
							'<span class="detail-field-title">' + this.escapeHtml(label) + '</span>' +
							'<span class="detail-field-code">' + this.escapeHtml(field) + '</span>' +
						'</div>' +
						'<div class="detail-val-row">' +
							'<span class="detail-val-tag">快照值：</span>' +
							'<div class="detail-val-content diff-cur">' + this.formatValue(field, snapVal) + '</div>' +
						'</div>' +
					'</div>'
				);
			}
		}

		$modal.fadeIn(150);
	},

	formatValue: function(field, val) {
		if (val === null || val === undefined || val === '') {
			return '<span class="text-muted">空</span>';
		}
		if (typeof val === 'object') {
			return '<code>' + this.escapeHtml(JSON.stringify(val)) + '</code>';
		}
		var strVal = String(val);
		if (field === 'status') {
			if (strVal === '1' || strVal === '启用') {
				return '<span class="val-tag tag-success">启用</span>';
			}
			if (strVal === '0' || strVal === '停用') {
				return '<span class="val-tag tag-default">停用</span>';
			}
		}
		if (field === 'is_sandbox') {
			if (strVal === '1' || strVal.indexOf('沙盒') !== -1) {
				return '<span class="val-tag tag-warning">沙盒环境</span>';
			}
			if (strVal === '0' || strVal.indexOf('生产') !== -1 || strVal.indexOf('正式') !== -1) {
				return '<span class="val-tag tag-info">正式环境</span>';
			}
		}
		if (field === 'type' && this.typeMap[val]) {
			return this.escapeHtml(this.typeMap[val] + ' (' + val + ')');
		}
		if (field === 'site_id' && this.siteMap[val]) {
			return this.escapeHtml(this.siteMap[val] + ' (#' + val + ')');
		}
		if (field === 'payment_id' && this.paymentMap[val]) {
			return this.escapeHtml(this.paymentMap[val] + ' (#' + val + ')');
		}
		if (field === 'app_key' || field === 'secret_key' || field === 'webhook_key') {
			return '<code>' + this.escapeHtml(strVal) + '</code>';
		}
		return this.escapeHtml(strVal);
	},

	escapeHtml: function(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	},

	copyText: function(text, callback) {
		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(callback);
		} else {
			var $temp = $('<textarea>');
			$('body').append($temp);
			$temp.val(text).select();
			document.execCommand('copy');
			$temp.remove();
			if (typeof callback === 'function') callback();
		}
	}
};
