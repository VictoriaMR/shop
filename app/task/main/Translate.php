<?php

namespace app\task\main;
use app\task\TaskDriver;

class Translate extends TaskDriver
{
	private $language = [];

	public $config = [
		'name' => '自动翻译任务',
		'cron' => ['0 6 * * *'],
	];

	public function run()
	{
		$language = array_column($this->getLanguage(), 'tr_code', 'lan_id');
		$transService = service('Translate');
		//spu名称
		$spuService = service('product/Language');
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
						if (hasZht($value[0])) {
							$transTxt = $transService->getText($value[0], $language[$lv]);
							if (!empty($transTxt)) {
								$transTxt = $this->filterTxt($transTxt);
								sleep(1);
							}
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
		$nameService = service('attr/Name');
		$list = $nameService->getListData(['status'=>['<>', 2]]);
		if (!empty($list)) {
			$languageService = service('attr/NameLanguage');
			foreach ($list as $key => $value) {
				$hasLanguage = $languageService->getListData(['attrn_id'=>$value['attrn_id']], 'lan_id');
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
								'attrn_id' => $value['attrn_id'],
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$languageService->insert($insertData);
						$nameService->updateData($value['attrn_id'], ['status'=>count($insertData) == count($noLanguage) ? 2 : 1]);
					}
				} else {
					$nameService->updateData($value['attrn_id'], ['status'=>2]);
				}
			}
		}
		//待翻译属性值
		$valueService = service('attr/Value');
		$list = $valueService->getListData(['status'=>['<>', 2]]);
		if (!empty($list)) {
			$languageService = service('attr/ValueLanguage');
			foreach ($list as $key => $value) {
				$hasLanguage = $languageService->getListData(['attrv_id'=>$value['attrv_id']], 'lan_id');
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
								'attrv_id' => $value['attrv_id'],
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$languageService->insert($insertData);
						$valueService->updateData($value['attrv_id'], ['status'=>count($insertData) == count($noLanguage) ? 2 : 1]);
					}
				} else {
					$valueService->updateData($value['attrv_id'], ['status'=>2]);
				}
			}
		}
		//待翻译描述值
		$nameService = service('desc/Name');
		$list = $nameService->getListData(['status'=>['<>', 2]]);
		if (!empty($list)) {
			$languageService = service('desc/NameLanguage');
			foreach ($list as $key => $value) {
				$hasLanguage = $languageService->getListData(['descn_id'=>$value['descn_id']], 'lan_id');
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
								'descn_id' => $value['descn_id'],
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$languageService->insert($insertData);
						$nameService->updateData($value['descn_id'], ['status'=>count($insertData) == count($noLanguage) ? 2 : 1]);
					}
				} else {
					$nameService->updateData($value['descn_id'], ['status'=>2]);
				}
			}
		}
		$valueService = service('desc/Value');
		$list = $valueService->getListData(['status'=>['<>', 2]]);
		if (!empty($list)) {
			$languageService = service('desc/ValueLanguage');
			foreach ($list as $key => $value) {
				$hasLanguage = $languageService->getListData(['descv_id'=>$value['descv_id']], 'lan_id');
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
								'descv_id' => $value['descv_id'],
								'lan_id' => $lv,
								'name' => $transTxt,
							];
						}
					}
					if (!empty($insertData)) {
						$languageService->insert($insertData);
						$valueService->updateData($value['descv_id'], ['status'=>count($insertData) == count($noLanguage) ? 2 : 1]);
					}
				} else {
					$valueService->updateData($value['descv_id'], ['status'=>2]);
				}
			}
		}
		return false;
	}

	private function getLanguage()
	{
		if (empty($this->language)) {
			$this->language = service('Language')->getTransList();
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