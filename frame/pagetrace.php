<div id="think_page_trace" style="position: fixed;bottom:0;right:0;font-size:14px;width:100%;z-index: 999999;color: #000;text-align:left;font-family:'微软雅黑';">
    <div id="think_page_trace_tab" style="display: none;background:white;margin:0;height: 250px;">
        <div id="think_page_trace_tab_tit" style="height:30px;padding: 0 12px;border-bottom:1px solid #ececec;border-top:1px solid #ececec;font-size:16px">
            <?php foreach ($trace as $key => $value) {?>
            <span style="color:#000;padding-right:12px;height:30px;line-height:30px;display:inline-block;margin-right:3px;cursor:pointer;font-weight:700"><?php echo $key ?></span>
            <?php }?>
        </div>
        <div id="think_page_trace_tab_cont" style="overflow:auto;height:212px;padding:0;line-height: 24px">
            <?php foreach ($trace as $info) {?>
            <div style="display:none;">
                <ol style="padding: 0; margin:0">
                    <?php
                    if (is_array($info)) {
                        foreach ($info as $k => $val) {
                            echo '<li style="border-bottom:1px solid #EEE;font-size:14px;padding:0 12px;word-break:break-all;">' . (is_numeric($k) ? '' : $k.' : ') . htmlentities(print_r($val,true), ENT_COMPAT, 'utf-8') . '</li>';
                        }
                    }
                    ?>
                </ol>
            </div>
            <?php }?>
        </div>
    </div>
    <div id="think_page_trace_close" style="text-align:right;height:15px;position:absolute;top:7px;right:12px;cursor:pointer;">X</div>
</div>
<div id="think_page_trace_open" style="height:30px;float:right;text-align:right;overflow:hidden;position:fixed;bottom:1px;right:0;color:#000;line-height:30px;cursor:pointer;">
    <div style="background:#232323;color:#FFF;padding:0 6px;float:right;line-height:30px;font-size:14px;"><?php echo $runtime.'s ';?></div>
</div>
<script type="text/javascript">
(function(){
    var tab_tit  = document.getElementById('think_page_trace_tab_tit').getElementsByTagName('span');
    var tab_cont = document.getElementById('think_page_trace_tab_cont').getElementsByTagName('div');
    var open     = document.getElementById('think_page_trace_open');
    var close    = document.getElementById('think_page_trace_close');
    var trace    = document.getElementById('think_page_trace_tab');
    var cookie   = document.cookie.match(/thinkphp_show_page_trace=(\d\|\d)/);
    var history  = (cookie && typeof cookie[1] != 'undefined' && cookie[1].split('|')) || [0,0];
    open.onclick = function(){
        trace.style.display = 'block';
        this.style.display = 'none';
        close.parentNode.style.display = 'block';
        history[0] = 1;
        document.cookie = 'thinkphp_show_page_trace='+history.join('|')
    }
    close.onclick = function(){
        trace.style.display = 'none';
        this.parentNode.style.display = 'none';
        open.style.display = 'block';
        history[0] = 0;
        document.cookie = 'thinkphp_show_page_trace='+history.join('|')
    }
    for(var i = 0; i < tab_tit.length; i++){
        tab_tit[i].onclick = (function(i){
            return function(){
                for(var j = 0; j < tab_cont.length; j++){
                    tab_cont[j].style.display = 'none';
                    tab_tit[j].style.color = '#999';
                }
                tab_cont[i].style.display = 'block';
                tab_tit[i].style.color = '#000';
                history[1] = i;
                document.cookie = 'thinkphp_show_page_trace='+history.join('|')
            }
        })(i)
    }
    parseInt(history[0]) && open.click();
    tab_tit[history[1]].click();
})();
</script>