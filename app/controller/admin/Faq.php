<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Faq extends AdminBase
{
    public function __construct()
    {
        $this->_arr = [
            'index' => '分组管理',
            'faqList' => '文档管理',
        ];
        $this->_default = '帮助文档';
        parent::_init();
    }

    public function index()
    {   
        if (isPost()) {
            $opn = ipost('opn');
            if (in_array($opn, ['getGroupInfo', 'modifyGroupStatus', 'editGroupInfo', 'deleteGroup', 'getGroupLanguage', 'transfer', 'editGroupLanguage'], true)) {
                $this->$opn();
            }
            $this->error('非法请求');
        }

        frame('Html')->addJs();
        frame('Html')->addCss();

        $page = max(1, (int)iget('page', 1));
        $size = max(1, (int)iget('size', 20));
        $search = [
            'status'  => (int)iget('status', -1),
            'keyword' => trim(iget('keyword', '')),
        ];

        $where = [];
        if ($search['status'] >= 0) {
            $where['status'] = $search['status'];
        }
        if ($search['keyword'] !== '') {
            $where['name'] = ['like', '%' . $search['keyword'] . '%'];
        }

        $group = service('faq/Group');

        $total = $group->getCountData($where);
        if ($total > 0) {
            $list = $group->getListData($where, '*', $page, $size);
        } 

        $this->assign('size', $size);
        $this->assign('total', $total);
        $this->assign('list', $list ?? []);
        $this->assign('search', $search);
        $this->view();
    }

    protected function getGroupInfo()
    {
        $id = (int)ipost('group_id', 0);
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        $data = service('faq/Group')->loadData($id, 'group_id,name,status');
        $this->addLog('获取帮助文档分组信息-'.$id);
        if ($data) {
            $this->success('获取成功', $data);
        }
        $this->error('获取失败, 数据不存在');
    }

    protected function editGroupInfo()
    {
        $id = (int)ipost('group_id', 0);
        $status = (int)ipost('status', 0) === 1 ? 1 : 0;
        $name = trim(ipost('name', ''));
        if (empty($name)) {
            $this->error('名称不能为空');
        }
        $group = service('faq/Group');
        $data = [
            'status' => $status,
            'name' => $name,
        ];
        if ($id > 0) {
            $rst = $group->updateData($id, $data);
        } else {
            $rst = $group->insertGetId($data);
            $id = $rst;
        }
        $this->addLog('修改帮助文档分组-'.$id);
        $this->success('操作成功');
    }

    protected function modifyGroupStatus()
    {
        $id = (int)ipost('group_id', 0);
        $status = (int)ipost('status', 0) === 1 ? 1 : 0;
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        $group = service('faq/Group');
        if ($group->getCountData(['group_id'=>$id]) < 1) {
            $this->error('分组数据不存在');
        }
        $rst = $group->updateData($id, ['status' => $status]);
        $this->addLog('修改帮助文档分组状态-'.$id.':'.$status);
        if ($rst !== false) {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    protected function deleteGroup()
    {
        $id = (int)ipost('group_id', 0);
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        if (service('faq/Faq')->getCountData(['group_id' => $id]) > 0) {
            $this->error('该分组下存在帮助文档，请先处理关联文档后再删除');
        }
        $rst = service('faq/Group')->deleteData($id);
        if ($rst) {
            service('faq/GroupLanguage')->deleteData(['group_id' => $id]);
            $this->addLog('删除帮助文档分组-'.$id);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    protected function getGroupLanguage()
    {
        $id = (int) ipost('group_id');
        if (empty($id)) {
            $this->error('ID值不正确');
        }
        $info = service('faq/GroupLanguage')->getListData(['group_id'=>$id]);
        $info = array_column($info, 'name', 'lan_id');
        $languageList = service('Language')->getListData();
        $data = [];
        foreach ($languageList as $key => $value) {
            if ($value['lan_id'] < 1) continue;
            $data[] = [
                'lan_id' => $value['lan_id'],
                'tr_code' => $value['tr_code'],
                'name' => $info[$value['lan_id']] ?? '',
                'language_name' => $value['name'],
            ];
        }
        $this->addLog('获取帮助文档分组语言-'.$id);
        $this->success('获取成功', $data);
    }

    protected function editGroupLanguage()
    {
        $id = (int) ipost('group_id');
        if (empty($id)) {
            $this->error('ID值不正确');
        }
        $language = ipost('language');
        if (!empty($language)) {
            $services = service('faq/GroupLanguage');
            foreach ($language as $key => $value) {
                $services->setNxLanguage($id, $key, strTrim($value));
            }
        }
        $this->addLog('修改帮助文档分组语言-'.$id);
        $this->success('操作成功');
    }

    protected function transfer()
    {
        $trCode = trim(ipost('tr_code', ''));
        $name = trim(ipost('name', ''));
        if ($name === '' || $trCode === '') {
            $this->error('参数不正确');
        }
        $rst = service('tool/Translate')->getText($name, $trCode);
        if ($rst !== false && $rst !== null && $rst !== '') {
            $this->success('翻译成功', $rst);
        }
        $this->error('翻译失败');
    }

    public function faqList()
    {   
        if (isPost()) {
            $opn = ipost('opn');
            if (in_array($opn, ['getFaqInfo', 'modifyFaqStatus', 'editFaqInfo', 'deleteFaq', 'getFaqLanguage', 'transfer', 'editFaqLanguage'], true)) {
                $this->$opn();
            }
            $this->error('非法请求');
        }

        frame('Html')->addJs();
        frame('Html')->addCss();

        $page = max(1, (int)iget('page', 1));
        $size = max(1, (int)iget('size', 30));
        $search = [
            'group_id' => (int)iget('group_id', 0),
            'status'   => (int)iget('status', -1),
            'keyword'  => trim(iget('keyword', '')),
        ];

        $where = [];
        if ($search['group_id'] > 0) {
            $where['group_id'] = $search['group_id'];
        }
        if ($search['status'] >= 0) {
            $where['status'] = $search['status'];
        }
        if ($search['keyword'] !== '') {
            $where['title'] = ['like', '%' . $search['keyword'] . '%'];
        }

        $faq = service('faq/Faq');

        $total = $faq->getCountData($where);
        if ($total > 0) {
            $list = $faq->getListData($where, '*', $page, $size);
        }

        $group = service('faq/Group')->getListData();
        $group = array_column($group, 'name', 'group_id');

        $this->assign('size', $size);
        $this->assign('total', $total);
        $this->assign('list', $list ?? []);
        $this->assign('group', $group);
        $this->assign('search', $search);
        $this->view();
    }

    protected function getFaqInfo()
    {
        $id = (int)ipost('faq_id', 0);
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        $data = service('faq/Faq')->loadData($id, 'faq_id,group_id,title,status,visit_total');
        $this->addLog('获取帮助文档信息-'.$id);
        if ($data) {
            $this->success('获取成功', $data);
        }
        $this->error('获取失败, 数据不存在');
    }

    protected function editFaqInfo()
    {
        $id = (int)ipost('faq_id', 0);
        $status = (int)ipost('status', 0) === 1 ? 1 : 0;
        $title = trim(ipost('title', ''));
        $groupId = (int)(ipost('group_id', 0));
        $total = max(0, (int)(ipost('visit_total', 0)));
        if (empty($title)) {
            $this->error('标题不能为空');
        }
        if ($groupId < 1) {
            $this->error('分组ID不正确');
        }
        if (service('faq/Group')->getCountData(['group_id'=>$groupId]) < 1) {
            $this->error('分组不存在');
        }
        $faq = service('faq/Faq');
        $data = [
            'group_id' => $groupId,
            'status' => $status,
            'title' => $title,
            'visit_total' => $total,
        ];
        if ($id > 0) {
            $rst = $faq->updateData($id, $data);
        } else {
            $rst = $faq->insertGetId($data);
            $id = $rst;
        }
        $this->addLog('修改帮助文档-'.$id);
        $this->success('操作成功');
    }

    protected function modifyFaqStatus()
    {
        $id = (int)ipost('faq_id', 0);
        $status = (int)ipost('status', 0) === 1 ? 1 : 0;
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        $faq = service('faq/Faq');
        if ($faq->getCountData(['faq_id'=>$id]) < 1) {
            $this->error('数据不存在');
        }
        $rst = $faq->updateData($id, ['status' => $status]);
        $this->addLog('修改帮助文档状态-'.$id.':'.$status);
        if ($rst !== false) {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    protected function deleteFaq()
    {
        $id = (int)ipost('faq_id', 0);
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        $rst = service('faq/Faq')->deleteData($id);
        if ($rst) {
            service('faq/FaqLanguage')->deleteData(['faq_id' => $id]);
            $this->addLog('删除帮助文档-'.$id);
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    protected function getFaqLanguage()
    {
        $id = (int) ipost('faq_id');
        $lanId = (int) ipost('lan_id');
        if (empty($id)) {
            $this->error('ID值不正确');
        }
        $this->addLog('获取帮助文档语言-'.$id);
        $languageList = service('Language')->getListData();
        if ($lanId > 0) {
            $languageList = array_column($languageList, 'name', 'lan_id');
            $info = service('faq/FaqLanguage')->loadData(['faq_id'=>$id, 'lan_id'=>$lanId]);
            $info = [
                'faq_id' => $id,
                'lan_id' => $lanId,
                'language_name' => $languageList[$lanId]??'',
                'title' => $info['title']??'',
                'content' => $info['content']??'',
            ];
            $this->success('获取成功', $info);
        }
        $info = service('faq/FaqLanguage')->getListData(['faq_id'=>$id]);
        $info = array_column($info, 'title', 'lan_id');
        $data = [];
        foreach ($languageList as $key => $value) {
            if ($value['lan_id'] < 1) continue;
            $data[] = [
                'lan_id' => $value['lan_id'],
                'language_name' => $value['name'],
                'edit' => !empty($info[$value['lan_id']]),
            ];
        }
        $this->success('获取成功', $data);
    }

    protected function editFaqLanguage()
    {
        $id = (int) ipost('faq_id');
        $lanId = (int) ipost('lan_id');
        $title = trim(ipost('title', ''));
        $content = trim(ipost('content', ''));
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        if ($lanId < 1) {
            $this->error('语言ID值不正确');
        }
        if (empty($title)) {
            $this->error('标题不能为空');
        }
        if (empty($content)) {
            $this->error('内容不能为空');
        }
        if (service('faq/Faq')->getCountData(['faq_id'=>$id]) < 1) {
            $this->error('帮助文档数据不存在');
        }
        if (service('Language')->getCountData(['lan_id'=>$lanId]) < 1) {
            $this->error('语言数据不存在');
        }
        $rst = service('faq/FaqLanguage')->setNxLanguage($id, $lanId, $title, $content);
        $this->addLog('修改帮助文档语言-'.$id);
        if ($rst) {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }
}