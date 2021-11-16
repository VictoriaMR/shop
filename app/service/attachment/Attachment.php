<?php 

namespace app\service\attachment;
use app\service\Base;

class Attachment extends Base
{	
	protected function getModel()
	{
		$this->baseModel = make('app/model/attachment/Attachment');
	}

	public function addIfNot(array $data)
	{
		$info = $this->loadData(['name'=>$data['name']], 'attach_id');
		if (empty($info)) {
			return $this->insertGetId($data);
		}
		return $info['attach_id'];
	}

	public function getAttachmentByName($name, $width='')
	{
		$info = $this->loadData(['name'=>$name]);
		if (empty($info)) {
			return false;
		}
		return $this->urlInfo($info, $width);;
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
		$info['url'] = mediaUrl($info['cate'].DS.$info['name'].($width == '' ? '' : DS.$width).'.'.$info['type']);
		return $info;
	}

	public function getList($where, $type='400')
	{
		$list = $this->getListData($where);
		foreach ($list as $key => $value) {
			$list[$key] = $this->urlInfo($value, $type);
		}
		return $list;
	}
}