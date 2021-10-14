<?php 

namespace app\service\admin;
use app\service\Base;

class Logger extends Base
{	
    protected function getModel()
    {
        $this->baseModel = make('app/model/admin/Logger');
    }

    public function getTypeList()
    {
        return [
            $this->getConst('TYPE_LOGIN') => '登入',
            $this->getConst('TYPE_LOGOUT') => '登出',
            $this->getConst('TYPE_LOGIN_FAIL') => '登入失败',
            $this->getConst('TYPE_BEHAVIOR') => '操作',
        ];
    }

    public function getList(array $where, $page=1, $size=20)
    {
        $list = $this->getListData($where, '*', $page, $size, ['log_id'=>'desc']);
        if (!empty($list)) {
            $memIdArr = array_unique(array_column($list, 'mem_id'));
            $member = make('app/service/admin/Member');
            $memData = $member->getListData(['mem_id'=>['in', $memIdArr]], 'mem_id,name,nickname,avatar,sex', 0);
            $memData = array_column($memData, null, 'mem_id');
            $typeArr = $this->getTypeList();
            foreach ($list as $key => $value) {
                $value['name'] = empty($value['mem_id']) ? '' : $memData[$value['mem_id']]['name'];
                $value['nickname'] = empty($value['mem_id']) ? '' : $memData[$value['mem_id']]['nickname'];
                $value['avatar'] = empty($value['mem_id']) ? $member->getAvatar() : $member->getAvatar($memData[$value['mem_id']]['avatar'], $memData[$value['mem_id']]['sex']);;
                $value['type_text'] = $typeArr[$value['type']];
                $list[$key] = $value;
            }
        }
        return $list;
    }
}