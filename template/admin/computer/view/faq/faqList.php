<style type="text/css">
    #dealbox-faq-language .form-horizontal .form-group {
        margin-left: 0;
        margin-right: 0;
    }
    #dealbox-faq-language .ke-container {
        width: 100% !important;
        height: 650px;
    }
    #dealbox-faq-language .centerShow {
        width: 880px;
    }
</style>
<div class="container-fluid" id="faq-page">
    <div class="form-group right">
        <button class="btn btn-success add-btn" type="button"><i class="glyphicon glyphicon-plus-sign"></i> 新增文章</button>
    </div>
    <table class="table table-hover mt20" id="data-list">
        <tbody>
            <tr>
                <th width="100">ID</th>
                <th width="200">分组</th>
                <th width="300">标题</th>
                <th width="120">浏览次数</th>
                <th width="120">状态</th>
                <th>操作</th>
            </tr>
            <?php if (empty($list)){ ?>
            <tr>
                <td colspan="10">
                    <div class="tc orange">暂无数据</div>
                </td>
            </tr>
            <?php } else {?>
            <?php foreach ($list as $key => $value) { ?>
            <tr data-id="<?php echo $value['faq_id'];?>">
                <td><?php echo $value['faq_id'];?></td>
                <td><?php echo $group[$value['group_id']]??'--';?></td>
                <td>
                    <span class="glyphicon glyphicon-globe" title="点击编辑多语言"></span>
                    <span><?php echo $value['title'];?></span>
                </td>
                <td><?php echo $value['visit_total'];?></td>
                <td>
                    <div class="switch_botton" data-status="<?php echo $value['status'];?>">
                        <div class="switch_status <?php echo $value['status'] == 1 ? 'on' : 'off';?>"></div>
                    </div>
                </td>
                <td>
                    <button class="btn btn-primary btn-xs modify mt2" type="button"><i class="glyphicon glyphicon-edit"></i> 修改</button>
                    <button class="btn btn-danger btn-xs delete mt2" type="button"><i class="glyphicon glyphicon-trash"></i> 删除</button>
                </td>
            </tr>
            <?php } ?>
            <?php }?>
        </tbody>
    </table>
    <?php echo page($size, $total);?>
</div>
<script src="<?php echo siteUrl('kindEditor/kindeditor-min.js');?>"></script>
<!-- 新增/编辑弹窗 -->
<div id="dealbox-info" class="hidden">
    <div class="mask"></div>
    <div class="centerShow">
    <form class="form-horizontal">
        <button type="button" class="close" aria-hidden="true">&times;</button>
        <div class="f24 dealbox-title">编辑文章</div>
        <input type="hidden" name="faq_id" value="0">
        <input type="hidden" name="opn" value="editFaqInfo">
        <div class="input-group">
            <div class="input-group-addon"><span>标题</span>：</div>
            <input class="form-control" name="title" placeholder="请输入标题">
        </div>
        <div class="input-group">
            <div class="input-group-addon"><span>分组</span>：</div>
            <select class="form-control" name="group_id">
                <option value="0">请选择分组</option>
                <?php foreach ($group as $key=>$value){?>
                <option value="<?php echo $key;?>"><?php echo $value;?></option>
                <?php }?>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-addon"><span>状态</span>：</div>
            <select class="form-control" name="status">
                <option value="">请选择状态</option>
                <option value="0">关闭</option>
                <option value="1">开启</option>
            </select>
        </div>
        <div class="input-group">
            <div class="input-group-addon"><span>浏览次数</span>：</div>
            <input class="form-control" name="visit_total" placeholder="请输入次数">
        </div>
        <button type="button" class="btn btn-primary btn-lg btn-block save-btn mt20">确认</button>
    </form>
    </div>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
    <div class="mask"></div>
    <div class="centerShow">
        <form class="form-horizontal">
            <button type="button" class="close" aria-hidden="true">&times;</button>
            <div class="f24 dealbox-title">帮助文章多语言管理</div>
            <input type="hidden" name="faq_id" value="0">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>语言名称</th>
                        <th>是否配置</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </form>
    </div>
</div>
<!-- 文档语言 -->
<div id="dealbox-faq-language" class="hidden">
    <div class="mask"></div>
    <div class="centerShow">
        <form class="form-horizontal">
            <button type="button" class="close" aria-hidden="true">&times;</button>
            <div class="f24 dealbox-title"></div>
            <input type="hidden" name="faq_id" value="0">
            <input type="hidden" name="lan_id" value="0">
            <input type="hidden" name="opn" value="editFaqLanguage">
            <div class="form-group">
                <label for="exampleInputEmail1">文章标题</label>
                <input type="text" class="form-control" name="title" placeholder="标题">
            </div>
            <div class="form-group editor-group">
                <label for="exampleInputPassword1">文章内容</label>
                <textarea id="faq_editor" class="form-control" name="content"></textarea>
            </div>
            <button type="button" class="btn btn-primary save-btn">确认</button>
        </form>
    </div>
</div>