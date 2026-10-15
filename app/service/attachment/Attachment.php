<?php 

namespace app\service\attachment;
use app\service\Base;

class Attachment extends Base
{	
	public function addIfNot(array $data)
	{
		$info = $this->loadData(['name'=>$data['name']], 'attach_id');
		if (empty($info)) {
			return $this->insertGetId($data);
		}
		return $info['attach_id'];
	}

	public function getAttachmentByName($name, $path='', $width='')
	{
		$where = ['name'=>$name];
		if ($path) {
			$where['path'] = $path;
		}
		$info = $this->loadData($where);
		if (empty($info)) {
			return false;
		}
		return $this->urlInfo($info, $width);
	}

	public function getAttachmentById($attachId)
	{
		$info = $this->loadData($attachId);
		if (empty($info)) {
			return false;
		}
		return $this->urlInfo($info);
	}

	public function urlInfo($info, $width='')
	{
		$path = str_replace('\\', '/', $info['path'] ?? $info['cate'] ?? '');
		$info['url'] = mediaUrl($path.'/'.$info['name'].'.'.$info['type'], $width);
		return $info;
	}

	public function getList($where, $type='400', $format=true)
	{
		$list = $this->getListData($where, 'attach_id,name,path,type');
		if (!$format) {
			return $list;
		}
		foreach ($list as $key => $value) {
			$list[$key] = $this->urlInfo($value, $type);
		}
		return $list;
	}
}