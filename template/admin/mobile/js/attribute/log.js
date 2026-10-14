$(function () {
	var ATTRIBUTE_LOG = {
		currentRawJson: '',

		init: function () {
			var _this = this;

			// 查看完整详情弹窗
			$(document).on('click', '.view-detail-btn', function () {
				var $btn = $(this);
				var detailRaw = $btn.attr('data-detail') || '';
				var info = $btn.attr('data-info') || '-';
				var typeName = $btn.attr('data-type') || '属性';
				var target = $btn.attr('data-target') || '-';
				var user = $btn.attr('data-user') || '-';
				var time = $btn.attr('data-time') || '-';

				_this.currentRawJson = detailRaw;

				var $modal = $('#dealbox-log-detail');
				$modal.find('.modal-type-name').text(typeName);
				$modal.find('.modal-action-title').text(info);
				$modal.find('.modal-target-name').text(target);
				$modal.find('.modal-meta-info').text('操作人：' + user + ' · ' + time);

				var detailObj = null;
				try {
					detailObj = typeof detailRaw === 'string' ? JSON.parse(detailRaw) : detailRaw;
				} catch (e) {
					detailObj = null;
				}

				_this.renderDetailItems(detailObj);
				$modal.fadeIn(150);
			});

			// 关闭弹窗（右上角关闭按钮或点击遮罩层）
			$('#dealbox-log-detail').on('click', '.modal-close', function () {
				$('#dealbox-log-detail').fadeOut(150);
			});
			$('#dealbox-log-detail').on('click', function (e) {
				if ($(e.target).is('#dealbox-log-detail')) {
					$(this).fadeOut(150);
				}
			});

			// 复制原始 JSON
			$('#dealbox-log-detail').on('click', '.copy-json-btn', function () {
				if (!_this.currentRawJson) {
					$. Bungle ? $.Bungle.message('暂无可复制的数据', 'warning') : alert('暂无可复制的数据');
					return;
				}
				var formatted = _this.currentRawJson;
				try {
					formatted = JSON.stringify(JSON.parse(_this.currentRawJson), null, 2);
				} catch (e) {}

				_this.copyText(formatted);
			});
		},

		renderDetailItems: function (detailObj) {
			var $container = $('#diff-table-body');
			$container.empty();

			if (!detailObj || typeof detailObj !== 'object') {
				$container.html('<div class="text-center text-muted" style="padding:0.2rem 0;">无详细字段数据</div>');
				return;
			}

			var html = '';
			var _this = this;

			$.each(detailObj, function (key, item) {
				var field = key;
				var label = key;
				var isDiff = false;

				if (item && typeof item === 'object') {
					field = item.field || key;
					label = item.label || field;
					isDiff = ('old' in item) || ('new' in item);
				}

				html += '<div class="detail-item-card">';
				html += '  <div class="detail-item-header">';
				html += '    <span class="detail-item-label">' + _this.escapeHtml(label) + '</span>';
				html += '    <span class="detail-item-field">' + _this.escapeHtml(field) + '</span>';
				html += '  </div>';
				html += '  <div class="detail-val-box">';

				if (isDiff) {
					html += '<div class="detail-val-row">';
					html += '  <span class="detail-val-tag old">原值</span>';
					html += '  <span>' + _this.formatValue(item.old) + '</span>';
					html += '</div>';
					html += '<div class="detail-val-row">';
					html += '  <span class="detail-val-tag new">新值</span>';
					html += '  <span>' + _this.formatValue(item.new) + '</span>';
					html += '</div>';
				} else {
					var val = (item && typeof item === 'object') ? (item.val !== undefined ? item.val : item.value) : item;
					html += '<div class="detail-val-row">';
					html += '  <span class="detail-val-tag snap">当前值</span>';
					html += '  <span>' + _this.formatValue(val) + '</span>';
					html += '</div>';
				}

				html += '  </div>';
				html += '</div>';
			});

			$container.html(html);
		},

		formatValue: function (val) {
			if (val === null || val === undefined || val === '') {
				return '<span class="text-muted">空</span>';
			}
			if (typeof val === 'object') {
				return '<code>' + this.escapeHtml(JSON.stringify(val)) + '</code>';
			}
			return this.escapeHtml(String(val));
		},

		copyText: function (text) {
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(text).then(function () {
					typeof tips === 'function' ? tips('JSON 已复制到剪贴板', 1) : alert('JSON 已复制到剪贴板');
				});
			} else {
				var $temp = $('<textarea>');
				$('body').append($temp);
				$temp.val(text).select();
				document.execCommand('copy');
				$temp.remove();
				typeof tips === 'function' ? tips('JSON 已复制到剪贴板', 1) : alert('JSON 已复制到剪贴板');
			}
		},

		escapeHtml: function (str) {
			if (str === null || str === undefined) return '';
			return String(str)
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}
	};

	ATTRIBUTE_LOG.init();
});
