<div id="left-filter">
	<div class="content">
		<?php foreach ($list as $key=>$value){?><div class="item">
			<div class="relative title-content">
				<p class="title"><?php echo $value['name'];?></p>
				<span class="iconfont icon-<?php echo $key==0?'xiangshang2':'xiangxia2';?>"></span>
			</div>
			<ul<?php echo ($key==0&&!$rid)||$value['attrn_id']==$rid?' class="open"':'';?>>
				<?php foreach ($value['attv_list'] as $attrValue){?><li>
					<span class="iconfont icon-<?php echo $value['attrn_id']==$rid&&$attrValue['attrv_id']==$vid?'fangxingxuanzhongfill':'fangxingweixuanzhong';?>"></span>
					<a href="<?php echo url('', ['rid'=>$value['attrn_id'], 'vid'=>$attrValue['attrv_id'], 'page'=>0, 'keyword'=>iget('keyword', '')]);?>"><?php echo $attrValue['name'];?></a>
				</li>
				<?php }?>
			</ul>
		</div>
	<?php }?>
	</div>
</div>
