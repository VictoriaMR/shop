<?php 

namespace app\service;
use app\service\Base;

class LoggerService extends Base
{
	public function getModel()
	{
		$this->baseModel = make('app/model/Logger');
	}

	public function addLog(array $data=[])
	{
		$info = session()->get(APP_TEMPLATE_TYPE.'_info');
		$insert = [
			'mem_id' => $info['mem_id'] ?? 0,
			'lan_id' => lanId(),
			'is_moblie' => IS_MOBILE ? 1 : 0,
			'ip' => request()->getIp(),
			'path' => $_SERVER['REQUEST_URI'] ?? '',
			'system' => request()->getSystem(),
			'browser' => request()->getBrowser(),
			'agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
			'add_time' => now(),
		];
		return $this->insert(array_merge($insert, $data));
	}

	public function getStats($field)
	{
		return $this->field('count(*) AS count, '.$field)->groupBy($field)->get();
	}

	public function getIpDateStat($limit = 14)
	{
		$sql = 'SELECT COUNT(*) AS count, a.`format_date` FROM (SELECT `ip`, DATE_FORMAT(`add_time`,"%Y-%m-%d") AS `format_date` FROM `visitor_log` GROUP BY `ip`,`format_date`) a GROUP BY a.`format_date` ORDER BY a.`format_date` DESC LIMIT '.$limit;
		return $this->getQuery($sql);
	}
}