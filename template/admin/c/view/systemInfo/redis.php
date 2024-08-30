<div class="container-fluid">
    <table class="table table-border">
        <tr>
            <th class="col1">名称</th>
            <th class="col6">值</th>
        </tr>
        <?php if (!empty($info) && is_array($info)){?>
        <?php foreach ($info as $key=>$val){?>
        <tr>
            <td class="col1"><?php echo $key;?></td>
            <td class="col6"><?php echo $val;?></td>
        </tr>
        <?php }?>
        <?php } else {?>
        <tr>
            <td colspan="2" class="orange tc">Redis链接不正常</td>
        </tr>
        <?php }?>
    </table>
</div>