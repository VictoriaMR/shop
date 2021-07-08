<?php $this->load('common/header');?>
<div class="container-fluid">
	<div class="w50 left pr10 mt10">
		<p class="boxTitle">网站基本信息</p>
		<table width="100%" border="0" cellspacing="0" cellpadding="7" class="table">
			<tbody>
				<tr>
					<td width="130">系统</td>
					<td>
						<strong><?php echo php_uname('s').php_uname('r');?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">服务器版本</td>
					<td>
						<strong><?php echo $_SERVER['SERVER_SOFTWARE'];?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">服务器域名</td>
					<td>
						<strong><?php echo $_SERVER['SERVER_NAME'];?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">服务器地址</td>
					<td>
						<strong><?php echo $_SERVER['SERVER_ADDR'];?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">服务器端口</td>
					<td>
						<strong><?php echo $_SERVER['SERVER_PORT'];?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">PHP版本</td>
					<td>
						<strong><?php echo PHP_VERSION;?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">PHP运行方式</td>
					<td>
						<strong><?php echo php_sapi_name();?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">MySQL版本</td>
					<td>
						<strong><?php echo mysqlVersion();?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">执行时间</td>
					<td>
						<strong><?php echo get_cfg_var('max_execution_time').'s';?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">内存限制</td>
					<td>
						<strong><?php echo get_cfg_var('memory_limit');?></strong>
					</td>
				</tr>
				<tr>
					<td width="130">内存限制</td>
					<td>
						<strong><?php echo get_cfg_var('memory_limit');?></strong>
					</td>
				</tr>
			</tbody>
    	</table>
	</div>
	<div class="w50 left pl10 mt10">
		<p class="boxTitle">服务器信息</p>
		<table width="100%" border="0" cellspacing="0" cellpadding="7" class="table">
			<tbody>
				<?php if (!empty($cpuInfo['Name'])) {?>
				<tr>
					<td width="130">CPU信息</td>
					<td>
						<strong><?php echo $cpuInfo['Name'];?></strong>
					</td>
				</tr>
				<?php } ?>
				<?php if (!empty($cpuInfo['NumberOfCores'])) {?>
				<tr>
					<td width="130">CPU核数</td>
					<td>
						<strong><?php echo $cpuInfo['NumberOfCores'];?>核</strong>
					</td>
				</tr>
				<?php } ?>
				<tr>
					<td width="130">CPU使用</td>
					<td>
						<strong id="loadpercentage">0%</strong>
					</td>
				</tr>
				<tr>
					<td width="130">内存总量</td>
					<td>
						<strong id="memory_total">0M</strong>
					</td>
				</tr>
				<tr>
					<td width="130">内存使用</td>
					<td>
						<strong id="memory_used">0M</strong>
					</td>
				</tr>
				<tr>
					<td width="130">内存剩余</td>
					<td>
						<strong id="memory_free">0M</strong>
					</td>
				</tr>
				<tr>
					<td width="130">磁盘总量</td>
					<td>
						<strong id="disk_total_space">0M</strong>
					</td>
				</tr>
				<tr>
					<td width="130">磁盘使用</td>
					<td>
						<strong id="disk_used_space">0M</strong>
					</td>
				</tr>
				<tr>
					<td width="130">磁盘剩余</td>
					<td>
						<strong id="disk_free_space">0M</strong>
					</td>
				</tr>
			</tbody>
    	</table>
	</div>
	<div class="clear"></div>
</div>
<script type="text/javascript">
$(function(){
	STATINFO.init();
})
</script>
<?php $this->load('common/footer');?>