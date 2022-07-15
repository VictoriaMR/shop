$(function(){
    $('#update-git').on('click', function(){
        var _thisobj = $(this);
        var id = _thisobj.data('id');
        _thisobj.button('loading');
        $.post(URI+'git/gitLog', {opn:'updateGit', id:id}, function(res){
            if (res.code == '200') {
                successTips(res.msg);
                window.location.reload();
            } else {
                errorTips(res.msg);
                _thisobj.button('reset');
            }
        });
    });
    $('.release-btn').on('click', function(){
        var _thisobj = $(this);
        var id = _thisobj.parents('tr').data('id');
        _thisobj.button('loading');
        $.post(URI+'git/gitLog', {opn:'releaseGit', id:id}, function(res){
            if (res.code == '200') {
                successTips(res.msg);
                window.location.reload();
            } else {
                errorTips(res.msg);
                _thisobj.button('reset');
            }
        });
    })
});