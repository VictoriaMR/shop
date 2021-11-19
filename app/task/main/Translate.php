<?php

namespace app\task\main;
use app\task\TaskDriver;

class Translate extends TaskDriver
{
	private $language = [];

	public function __construct($process=[])
	{
		parent::__construct($process);
		if ($process !== false) {
			$this->lockTimeout = config('task.timeout');
			// 每运行6小时退出一次
			$this->runTimeLimit = 60*60*6;
		}
		$this->config['info'] = '属性值自动翻译进程';
		$this->config['cron'] = ['* * * * *']; //每天3点整运行
	}

	public function run()
	{
		$language = array_column($this->getLanguage(), 'tr_code', 'code');
		$transService = make('app/service/Translate');
		//spu名称
		$spuService = make('app/service/product/Language');
		$sql = 'SELECT * FROM product_language WHERE spu_id IN (SELECT spu_id FROM product_language GROUP BY spu_id HAVING COUNT(*) < '.count($language).' )';
		$list = $spuService->getQuery($sql);
		if (!empty($list)) {
			$tempData = [];
			foreach ($list as $value) {
				$tempData[$value['spu_id']][$value['lan_id']] = $value['name'];
			}
			foreach ($tempData as $key => $value) {
				$noLanguage = array_diff(array_keys($language), array_keys($value));
				if (!empty($noLanguage)) {
					$insertData = [];
					foreach ($noLanguage as $lv) {
						if (hasZht($value['zh'])) {
							$transTxt = $transService->getText($value['zh'], $language[$lv]);
							if (!empty($transTxt)) {
								$transTxt = $this->filterTxt($transTxt);
								sleep(1);
							}
						} else {
							$transTxt = $value['zh'];
						}
						if (!empty($transTxt)) {
							$insertData[] = [
								'spu_id' => $key,
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$spuService->insert($insertData);
					}
				}

			}
		}
		//待翻译属性名
		$buteService = make('app/service/attr/Bute');
		$list = $buteService->getListData(['status'=>['<>', 2]]);
		if (!empty($list)) {
			$languageService = make('app/service/attr/ButeLanguage');
			foreach ($list as $key => $value) {
				$hasLanguage = $languageService->getListData(['attr_id'=>$value['attr_id']], 'lan_id');
				$noLanguage = array_diff(array_keys($language), array_column($hasLanguage, 'lan_id'));
				if (!empty($noLanguage)) {
					$insertData = [];
					foreach ($noLanguage as $lv) {
						if (hasZht($value['name'])) {
							$transTxt = $transService->getText($value['name'], $language[$lv]);
							if (!empty($transTxt)) {
								$transTxt = $this->filterTxt($transTxt);
								sleep(1);
							}
						} else {
							$transTxt = $value['name'];
						}
						if (!empty($transTxt)) {
							$insertData[] = [
								'attr_id' => $value['attr_id'],
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$languageService->insert($insertData);
						$buteService->updateData($value['attr_id'], ['status'=>count($insertData) == count($noLanguage) ? 2 : 1]);
					}
				} else {
					$buteService->updateData($value['attr_id'], ['status'=>2]);
				}
			}
		}
		//待翻译属性值
		$valueService = make('app/service/attr/Value');
		$list = $valueService->getListData(['status'=>['<>', 2]]);
		if (!empty($list)) {
			$languageService = make('app/service/attr/ValueLanguage');
			foreach ($list as $key => $value) {
				$hasLanguage = $languageService->getListData(['attv_id'=>$value['attv_id']], 'lan_id');
				$noLanguage = array_diff(array_keys($language), array_column($hasLanguage, 'lan_id'));
				if (!empty($noLanguage)) {
					$insertData = [];
					foreach ($noLanguage as $lv) {
						if (hasZht($value['name'])) {
							$transTxt = $transService->getText($value['name'], $language[$lv]);
							if (!empty($transTxt)) {
								$transTxt = $this->filterTxt($transTxt);
								sleep(1);
							}
						} else {
							$transTxt = $value['name'];
						}
						if (!empty($transTxt)) {
							$insertData[] = [
								'attv_id' => $value['attv_id'],
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$languageService->insert($insertData);
						$valueService->updateData($value['attv_id'], ['status'=>count($insertData) == count($noLanguage) ? 2 : 1]);
					}
				} else {
					$valueService->updateData($value['attv_id'], ['status'=>2]);
				}
			}
		}
		//待翻译描述值
		$descriptionService = make('app/service/attr/Description');
		$list = $descriptionService->getListData(['status'=>['<>', 2]]);
		if (!empty($list)) {
			$languageService = make('app/service/attr/DescriptionLanguage');
			foreach ($list as $key => $value) {
				$hasLanguage = $languageService->getListData(['desc_id'=>$value['desc_id']], 'lan_id');
				$noLanguage = array_diff(array_keys($language), array_column($hasLanguage, 'lan_id'));
				if (!empty($noLanguage)) {
					$insertData = [];
					foreach ($noLanguage as $lv) {
						if (hasZht($value['name'])) {
							$transTxt = $transService->getText($value['name'], $language[$lv]);
							if (!empty($transTxt)) {
								$transTxt = $this->filterTxt($transTxt);
								sleep(1);
							}
						} else {
							$transTxt = $value['name'];
						}
						if (!empty($transTxt)) {
							$insertData[] = [
								'desc_id' => $value['desc_id'],
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$languageService->insert($insertData);
						$descriptionService->updateData($value['desc_id'], ['status'=>count($insertData) == count($noLanguage) ? 2 : 1]);
					}
				} else {
					$descriptionService->updateData($value['desc_id'], ['status'=>2]);
				}
			}
		}
		$this->taskSleep(600);
		return false;
	}

	private function getLanguage()
	{
		if (empty($this->language)) {
			$this->language = make('app/service/Language')->getTransList();
		}
		return $this->language;
	}

	private function filterTxt($str)
	{
		$arr = [
			'   ' => ' ',
			'（' => '(',
			'）' => ')',
			' (' => '(',
			' - ' => '-',
			' -' => '-',
			'- ' => '-',
			' * ' => '*',
			' *' => '*',
			'* ' => '*',
			' CM' => 'CM',
			' cm' => 'cm',
			' / ' => '/',
			' /' => '/',
			'/ ' => '/',
			' , ' => ',',
			', ' => ',',
			' ,' => ',',
			' + ' => '+',
			' +' => '+',
			'+ ' => '+',
			'E 27' => 'E27',
			' ＜ ' => '<',
			' < ' => '<',
			' <' => '<',
			'< ' => '<',
			'≦' => '≤',
			' ≤ ' => '≤',
			' ≤' => '≤',
			'≤ ' => '≤',
			' ~ ' => '~',
			'~ ' => '~',
			' ~' => '~',
			' W' => 'W',
			'，' => ',',
			'、' => ',',
			' mm' => 'mm',
			' MM' => 'MM',
		];
		return str_replace(array_keys($arr), array_values($arr), $str);
	}
}