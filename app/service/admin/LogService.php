<?php 

namespace app\service\admin;
use app\service\Base;

class LogService extends Base
{	
    public function __construct()
    {
        $this->baseModel = make('app/model/admin/Logger');
    }

    public function addLog(array $data)
    {
        return $this->addLog($data);
    }

    public function getTypeList()
    {
        return [
            $this->baseModel::TYPE_LOGIN => '登入',
            $this->baseModel::TYPE_LOGOUT => '登出',
            $this->baseModel::TYPE_LOGIN_FAIL => '登入失败',
        ];
    }

    public function getList(array $where, $page=1, $size=20)
    {
        $list = $this->getListData($where, '*', $page, $size, ['log_id'=>'desc']);
        if (!empty($list)) {
            $memIdArr = array_unique(array_column($list, 'mem_id'));
            $memberService = make('app/service/admin/MemberService');
            $memData = $memberService->getListData(['mem_id'=>['in', $memIdArr]], 'mem_id,name,nickname,avatar,sex', 0);
            $memData = array_column($memData, null, 'mem_id');
            $typeArr = $this->getTypeList();
            foreach ($list as $key => $value) {
                $value['name'] = empty($value['mem_id']) ? '' : $memData[$value['mem_id']]['name'];
                $value['nickname'] = empty($value['mem_id']) ? '' : $memData[$value['mem_id']]['nickname'];
                $value['avatar'] = empty($value['mem_id']) ? $memberService->getAvatar() : $memberService->getAvatar($memData[$value['mem_id']]['avatar'], $memData[$value['mem_id']]['sex']);;
                $value['type_text'] = $typeArr[$value['type_id']];
                $list[$key] = $value;
            }
        }
        return $list;
    }
}