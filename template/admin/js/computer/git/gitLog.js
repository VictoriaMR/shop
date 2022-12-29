$(function(){
    $('#update-git').on('click', function(){
        var _thisobj = $(this);
        var id = _thisobj.data('id');
        _thisobj.button('loading');
        post(URI+'git/gitLog', {opn:'updateGit', id:id}, function(res){
            showTips(res);
            if (res.code == 200) {
                window.location.reload();
            } else {
                _thisobj.button('reset');
            }
        });
    });
    $('.release-btn').on('click', function(){
        var _thisobj = $(this);
        var id = _thisobj.parents('tr').data('id');
        _thisobj.button('loading');
        post(URI+'git/gitLog', {opn:'releaseGit', id:id}, function(res){
            showTips(res);
            if (res.code == 200) {
                window.location.reload();
            } else {
                _thisobj.button('reset');
            }
        });
    })
});