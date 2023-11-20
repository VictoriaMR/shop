<?php $this->load('common/header_all');?>
<section id="index-page">
    <?php $this->load('common/product_list', ['list'=>1]);?>
</section>

<div class="modal modal-2" id="left-meau-modal">
	<div class="mask"></div>
	<div class="modal-content">
		<div class="header">
			<span class="name">Shipping Address</span>
			<span class="close-btn"><i class="icon icon20 icon-close"></i></span>
		</div>
		<div class="content"></div>
		<div class="footer">
			<button type="button" class="btn btn-black">Confirm</button>
		</div>
	</div>
</div>