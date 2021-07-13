<?php 
namespace app\service\admin;

use app\service\Base as BaseService;
use App\Models\Admin\Logger;

class LogService extends BaseService
{	
	protected static $constantMap = [
        'base' => Logger::class,
    ];

	public function __construct(Logger $model)
    {
        $this->baseModel =  $model;
    }

    public function getTypeList()
    {
        return [
            self::constant('TYPE_LOGIN') => '登入',
            self::constant('TYPE_LOGOUT') => '登出',
        ];
    }

    public function getTotal(array $where)
    {
        return $this->baseModel->where($where)->count();
    }

    public function getList(array $where, $page=1, $size=20)
    {
        $list = $this->baseModel->where($where)->orderBy('create_at', 'desc')->page($page, $size)->get();
        if (!empty($list)) {
            $memIdArr = array_unique(array_column($list, 'mem_id'));
            $memData = make('App/Services/Admin/MemberService')->getList(['mem_id'=>['in', $memIdArr]], 0);
            $memData = array_column($memData, null, 'mem_id');
            $typeArr = $this->getTypeList();
            foreach ($list as $key => $value) {
                $value['name'] = $memData[$value['mem_id']]['name'];
                $value['nickname'] = $memData[$value['mem_id']]['nickname'];
                $value['avatar'] = $memData[$value['mem_id']]['avatar'];
                $value['type_text'] = $typeArr[$value['type_id']];
                $list[$key] = $value;
            }
        }
        return $list;
    }
}