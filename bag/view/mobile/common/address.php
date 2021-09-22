<?php
$countryList = make('app/service/address/Country')->getListData(['status'=>1], 'code2,dialing_code,name_en', 0, 0, ['sort'=>'asc']);
$tempArr = make('app/service/address/Zone')->getListData([], 'zone_id,country_code2,name_en', 0, 0, ['sort'=>'asc']);
$zoneList = [];
foreach ($tempArr as $value) {
	$zoneList[$value['country_code2']][] = $value;
}
?>
<div id="address-book" class="modal hidden">
	<div class="mask address-book-mask"></div>
	<div class="dialog">
		<button class="btn24 btn-black top-close-btn"><?php echo appT('close');?></button>
		<div class="layer">
			<div class="list-title flex mt20">
				<div class="tcell">
					<p class="line"></p>
				</div>
				<p class="title"><?php echo appT('edit_address');?></p>
				<div class="tcell">
					<p class="line"></p>
				</div>
			</div>
		</div>
		<form class="content layer">
			<input type="hidden" name="address_id" value="0">
			<div class="mt16">
				<div class="item">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('Country');?></span>
						<span class="cred">*</span>
					</p>
					<input type="hidden" name="country_code2" required="required" maxlength="2">
					<div class="selection mt2">
						<div class="selector-icon">
							<span class="e1 f14 pr12"><?php echo appT('please_select');?></span>
							<i class="iconfont icon-xiangxia1"></i>
						</div>
						<div class="selector-content">
							<div class="selector-search">
								<button type="button" class="btn"><i class="iconfont icon-sousuo"></i></button>
								<input type="input" class="input" placeholder="Quick find">
								<div class="clear"></div>
								<p class="empty-selector tc c6 f12 mt6 hide"><?php echo appT('result_empty');?></p>
							</div>
							<ul class="selector country-selector">
								<?php foreach($countryList as $k => $v){?>
								<li class="e1" value="<?php echo $v['code2'];?>" code="<?php echo $v['dialing_code'];?>" key="<?php echo $k;?>"><?php echo $v['name_en'];?></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="item mt18" key="0">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('tax_number');?></span>
					</p>
					<input type="text" name="tax_number" class="input mt2" maxlength="32">
				</div>
				<div class="item mt18" key="1">
					<div class="w50 left pr4">
						<p class="title f700 f14">
							<span class="text"><?php echo appT('first_name');?></span>
							<span class="cred">*</span>
						</p>
						<input type="text" name="first_name" class="input mt2" required="required" maxlength="32">
					</div>
					<div class="w50 left pl4">
						<p class="title f700 f14">
							<span class="text"><?php echo appT('last_name');?></span>
						</p>
						<input type="text" name="last_name" class="input mt2" maxlength="32">
					</div>
					<div class="clear"></div>
				</div>
				<div class="item mt18" key="2">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('phone_number');?></span>
						<span class="cred">*</span>
					</p>
					<div class="input-group flex mt2">
						<button class="btn phone-code pl12 pr12 bg-f" type="button">
							<span class="text">+123</span>
							<span class="iconfont icon-xiangxia2"></span>
						</button>
						<input type="text" name="phone" class="input" required="required" maxlength="20">
					</div>
				</div>
				<div class="item mt18" key="3">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('zip_code');?></span>
						<span class="cred">*</span>
					</p>
					<input type="text" name="postcode" class="input mt2" required="required" maxlength="10">
				</div>
				<div class="item mt18" key="4">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('city');?></span>
						<span class="cred">*</span>
					</p>
					<input type="text" name="city" class="input mt2" required="required" maxlength="32">
				</div>
				<div class="item mt18 zone-selection" key="5">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('state');?></span>
						<span class="cred">*</span>
					</p>
					<input type="hidden" name="zone_id" value="0" maxlength="11">
					<input type="hidden" name="state" required="required" maxlength="32">
					<div class="selection mt2">
						<div class="selector-icon">
							<span class="e1 f14 pr12"><?php echo appT('please_select');?></span>
							<i class="iconfont icon-xiangxia1"></i>
						</div>
					</div>
				</div>
				<div class="item mt18" key="6">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('address');?></span>
						<span class="cred">*</span>
					</p>
					<input type="text" name="address1" class="input mt2" required="required" maxlength="64">
				</div>
				<div class="item mt18" key="7">
					<p class="title f700 f14">
						<span class="text"><?php echo appT('address2');?></span>
						<span class="right c9 f400"><?php echo appT('address2_tips');?></span>
					</p>
					<input type="text" name="address2" class="input mt2" maxlength="64">
				</div>
				<div class="mt10 tr">
					<a href="javascript:;" class="default-btn">
						<span class="iconfont icon-fangxingweixuanzhong"></span>
						<span><?php echo appT('set_default');?></span>
						<input type="hidden" name="is_default" value="0" maxlength="1">
					</a>
					<a href="javascript:;" class="default-btn ml6">
						<span class="iconfont icon-fangxingweixuanzhong"></span>
						<span><?php echo appT('set_bill');?></span>
						<input type="hidden" name="is_bill" value="0" maxlength="1">
					</a>
				</div>
			</div>
		</form>
		<div class="footer">
			<button class="btn cancel-btn"><?php echo appT('cancel');?></button>
			<button class="btn btn-black right save-btn"><?php echo appT('save');?></button>
		</div>
	</div>
</div>
<script type="text/javascript">
const zone_list = <?php echo json_encode($zoneList, JSON_UNESCAPED_UNICODE);?>;
const js_language_text_common = {
	edit_address: '<?php echo appT('edit_address');?>',
	add_address: '<?php echo appT('add_address');?>',
	please_select: '<?php echo appT('please_select');?>',
	quick_find: '<?php echo appT('quick_find');?>',
	result_empty: '<?php echo appT('result_empty');?>',
}
</script>