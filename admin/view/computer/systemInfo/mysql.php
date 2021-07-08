<?php $this->load('common/header');?>
<div class="container-fluid">
    <table class="table table-border">
        <tr>
            <th class="col1">名称</th>
            <th class="col6">值</th>
        </tr>
        <?php foreach($info['stat'] as $val){?>
        <tr>
            <td class="col1"><?php echo $val[0]?></td>
            <td class="col6"><?php echo $val[1]?></td>
        </tr>
        <?php }?>
        <tr>
            <td class="col1">Server Version</td>
            <td class="col6"><?php echo $info['server'];?></td>
        </tr>
        <tr>
            <td class="col1">Client Version</td>
            <td class="col6"><?php echo $info['client'];?></td>
        </tr>
        <?php foreach($info['other'] as $key => $val){?>
        <tr>
            <td class="col1"><?php echo $key;?></td>
            <td class="col6"><?php echo $val;?></td>
        </tr>
        <?php }?>
    </table>
</div>
<?php $this->load('common/footer');?>