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
    }

    public function index()
    {   
        if (request()->isPost()) {
            $opn = ipost('opn');
            if (in_array($opn, ['getGroupInfo', 'modifyGroupStatus', 'editGroupInfo', 'getGroupLanguage', 'transfer', 'editGroupLanguage'])) {
                $this->$opn();
            }
        }

        html()->addJs();
        html()->addCss();

        $page = (int)iget('page', 1);
        $size = (int)iget('size', 20);

        $group = make('app/service/faq/Group');

        $total = $group->getCountData();
        if ($total > 0) {
            $list = $group->getListData([], '*', $page, $size);
        } 

        $this->assign('size', $size);
        $this->assign('total', $total);
        $this->assign('list', $list ?? []);

        $this->_init();
        $this->view();
    }

    protected function getGroupInfo()
    {
        $id = (int)ipost('group_id', 0);
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        $data = make('app/service/faq/Group')->loadData($id, 'group_id,name,status');
        $this->addLog('获取帮助文档分组信息-'.$id);
        if ($data) {
            $this->success('获取成功', $data);
        }
        $this->error('获取失败, 数据不存在');
    }

    protected function editGroupInfo()
    {
        $id = (int)ipost('group_id', 0);
        $status = (int)ipost('status', 0);
        $name = trim(ipost('name', ''));
        if (empty($name)) {
            $this->error('名称不能为空');
        }
        $group = make('app/service/faq/Group');
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
        $status = (int)ipost('status', 0);
        if ($id < 1) {
            $this->error('ID值不正确');
        }
        $group = make('app/service/faq/Group');
        if ($group->getCountData(['group_id'=>$id]) < 1) {
            $this->error('分组数据不存在');
        }
        $rst = $group->updateData($id, ['status' => $status]);
        $this->addLog('修改帮助文档分组状态-'.$id.':'.$status);
        if ($rst) {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    protected function getGroupLanguage()
    {
        $id = (int) ipost('group_id');
        if (empty($id)) {
            $this->error('ID值不正确');
        }
        $info = make('app/service/faq/GroupLanguage')->getListData(['group_id'=>$id]);
        $info = array_column($info, 'name', 'lan_id');
        $languageList = make('app/service/Language')->getListData();
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
        $this->success($data);
    }

    protected function editGroupLanguage()
    {
        $id = (int) ipost('group_id');
        if (empty($id)) {
            $this->error('ID值不正确');
        }
        $language = ipost('language');
        if (!empty($language)) {
            $services = make('app/service/faq/GroupLanguage');
            foreach ($language as $key => $value) {
                $services->setNxLanguage($id, $key, strTrim($value));
            }
        }
        $this->addLog('修改帮助文档分组语言-'.$id);
        $this->success('操作成功');
    }
}