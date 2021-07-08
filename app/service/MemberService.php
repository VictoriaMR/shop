<?php 

namespace app\service;

use app\service\Base as BaseService;
use App\Models\Member;

/**
 * 	用户公共类
 */
class MemberService extends BaseService
{	
	protected static $constantMap = [
        'base' => Member::class,
    ];

	public function __construct(Member $model)
    {
        $this->baseModel = $model;
    }

	public function create($data)
	{
		if (empty($data['password'])) return false;
		$data['salt'] = $this->getSalt();
		$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_DEFAULT);

		return $this->baseModel->insertGetId($data);
	}

	public function updateById($memId, $data)
	{
		if (empty($memId) || empty($data)) return false;

		if (!empty($data['password'])) {
			$data['salt'] = $this->getSalt();
			$data['password'] = password_hash($this->getPasswd($data['password'], $data['salt']), PASSWORD_DEFAULT);
		}
		$result = $this->baseModel->updateDataById($memId, $data);
		if ($result) {
			$this->clearCache($memId);
		}
		return $result;
	}

	public function login($mobile, $password, $type = 'home')
	{
		if (empty($mobile) || empty($password)) return false;

		$info = $this->getInfoByMobile($mobile);
		if (empty($info)) return false;
		if (!$info['status']) return false;

		if ($this->checkPassword($this->getPasswd($password, $info['salt']), $info['password'])) {
			$data = [
				'mem_id' => $info['mem_id'],
				'name' => $info['name'],
				'mobile' => $info['mobile'],
				'nickname' => $info['nickname'],
				'avatar' => $info['avatar'],
				'salt' => $info['salt'],
			];
			return \frame\Session::set($type, $data);
		}
		return false;
	}

	public function checkPassword($inPassword = '', $sourcePassword = '')
	{
		return password_verify($inPassword, $sourcePassword);
	}

	public function isExistUserByMobile($mobile) 
	{
		return $this->baseModel->isExistUserByMobile($mobile);
	}

	public function getInfoByMobile($mobile)
	{
		$info = $this->baseModel->getInfoByMobile($mobile);
		if (!empty($info)) {
        	if (empty($info['avatar'])) {
        		$info['avatar'] = $this->getDefaultAvatar($info['mem_id']);
        	} else {
        		$info['avatar'] = mediaUrl($info['avatar']);
        	}
        }
        return $info;
	}

    public function getInfo($userId)
    {
        $info = $this->baseModel->getInfo($userId);
    	if (!empty($info)) {
        	if (empty($info['avatar'])) {
        		$info['avatar'] = $this->getDefaultAvatar($info['mem_id']);
        	} else {
        		$info['avatar'] = mediaUrl($info['avatar']);
        	}
        }
        return $info;
    }

    public function getInfoCache($userId)
    {
        $cacheKey = $this->getInfoCacheKey($userId);
        $info = redis()->get($cacheKey);
        if (empty($info)) {
            $info = $this->getInfo($userId);
            redis()->set($cacheKey, $info, self::constant('INFO_CACHE_TIMEOUT'));
        }
        return $info;
    }

    public function getDefaultAvatar($userId, $male = true)
    {
    	if ($male) {
    		return siteUrl('image/common/male.jpg');
    	} else {
    		return siteUrl('image/common/female.jpg');
    	}
    }

    public function getInfoCacheKey($userId)
    {
        return 'MEMBER_INFO_CACHE_' . $userId;
    }

    public function clearCache($userId)
    {
        return redis()->foget($this->getInfoCacheKey($userId));
    }

    public function getTotal(array $where)
    {
    	return $this->baseModel->where($where)->count();
    }

    public function getList(array $where, $page=1, $size=20)
    {
    	$list = $this->baseModel->where($where)->page($page, $size)->get();
    	if (!empty($list)) {
    		foreach ($list as $key => $value) {
    			unset($value['password']);
    			if (empty($value['avatar'])) {
	        		$value['avatar'] = $this->getDefaultAvatar($value['mem_id']);
	        	} else {
	        		$value['avatar'] = mediaUrl($value['avatar']);
	        	}
	        	$list[$key] = $value;
    		}
    	}
    	return $list;
    }

    public function getMemIdsByName($name)
    {
        if (empty($name)) return [];
        $list = $this->baseModel->where(['name,nickname'=>['like', '%'.$name.'%']])->field('mem_id')->get();
        if (empty($list)) return [];
        return array_unique(array_column($list, 'mem_id'));
    }

    public function getMemIdsByMobile($mobile)
    {
        if (empty($mobile)) return [];
        $list = $this->baseModel->where(['mobile'=>['like', '%'.$mobile.'%']])->field('mem_id')->get();
        if (empty($list)) return [];
        return array_unique(array_column($list, 'mem_id'));
    }

}