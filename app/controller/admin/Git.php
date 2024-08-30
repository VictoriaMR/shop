<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Git extends AdminBase
{
    private $git_library = ['shop'];

    public function __construct()
    {
        $this->_arr = [
            'index' => 'Git版本库管理',
            'gitLog' => 'Git版本日志'
        ];
        $this->_default = 'Ban本管理';
        parent::_init();
    }

    public function index()
    {
        $list = $this->git_library;
        $git = service('admin/Git');
        foreach ($list as $key => $value) {
            $info = $git->where(['library'=>$value])->field('release_time,commit_time,info,commit,status')->orderBy('git_id', 'desc')->find();
            $info['name'] = $value;
            $info['status_text'] = $this->getStatus($info['status'] ?? '--');
            $list[$key] = $info;
        }
        $this->assign('list', $list);
        $this->view();
    }

    private function getStatus($status)
    {
        $arr = [
            '0' => '未发布',
            '1' => '已发布',
        ];
        return $arr[$status] ?? $status;
    }

    public function gitLog()
    {
        if (frame('Request')->isPost()) {
            $opn = ipost('opn');
            if (in_array($opn, ['updateGit', 'releaseGit'])) {
                $this->$opn();
            }
        }

        frame('Html')->addJs();
        $id = iget('id', '');
        $page = iget('page', 1);
        $size = iget('size', 20);
        $where = [];
        if ($id) {
            $where['library'] = $id;
        }
        $git = service('admin/Git');
        $total = $git->getCountData($where);
        if ($total > 0) {
            $list = $git->getListData($where, '*', $page, $size, ['git_id'=>'desc']);
        }
        $this->assign('id', $id);
        $this->assign('size', $size);
        $this->assign('total', $total);
        $this->assign('list', $list??[]);
        $this->assign('git_library', $this->git_library);
        $this->view();
    }

    private function updateGit()
    {
        $id = ipost('id', '');
        if (!in_array($id, $this->git_library)) {
            $this->error('版本库不合法');
        }
        $rst = service('admin/Git')->upateLibrary($id, $msg);
        if ($rst) {
            $this->success($msg);
        }
        $this->error($msg);
    }

    private function releaseGit()
    {
        $id = ipost('id', 0);
        if ($id <= 0) {
            $this->error('参数不正确');
        }
        $git = service('admin/Git');
        $info = $git->loadData($id);
        if (empty($info)) {
            $this->error('找不到版本记录');
        }
        $rst = $git->updateData(['library'=>$info['library'], 'git_id'=>['<=', $info['git_id']]], ['status'=>1, 'release_time'=>now()]);
        if ($rst) {
            \App::setVersion($info['commit']);
            //删除缓存模板文件
            service('site/Site')->deleteTemplateCache();
            $this->success('发布成功');
        }
        $this->error('发布失败');
    }
}