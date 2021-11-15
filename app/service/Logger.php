<?php 

namespace app\service;
use app\service\Base;

class Logger extends Base
{
	public function getModel()
	{
		$this->baseModel = make('app/model/Logger');
	}

	public function getStats($field)
	{
		return $this->field('count(*) AS count, '.$field)->groupBy($field)->get();
	}

	public function getIpDateStat($limit = 14)
	{
		$sql = 'SELECT COUNT(*) AS count, a.`format_date` FROM (SELECT `ip`, DATE_FORMAT(`add_time`,"%Y-%m-%d") AS `format_date` FROM `visitor_logger` GROUP BY `ip`,`format_date`) a GROUP BY a.`format_date` ORDER BY a.`format_date` DESC LIMIT '.$limit;
		return $this->getQuery($sql);
	}
}