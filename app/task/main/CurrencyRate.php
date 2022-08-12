<?php

namespace app\task\main;
use app\task\TaskDriver;

class CurrencyRate extends TaskDriver
{
    public $config = [
        'name' => '货币汇率更新任务',
        'cron' => ['0 8 * * *'],
    ];

    public function run()
    {
        $result = make('frame/Http')->get('https://www.bankofchina.com/sourcedb/whpj/enindex_1619.html?timestamprand='.time());
        if (!$result) {
            return false;
        }
        $arr = [];
        $result = preg_match_all('/<tr align=\"center\">([\s\r\n]+)<td bgcolor=\"#FFFFFF\">([A-Z]{3})<\/td>([\s\r\n]+)<td bgcolor=\"#FFFFFF\">([0-9\.]+)<\/td>/', $result, $arr);
        if (!$result) {
            return false;
        }
        $result = [];
        foreach ($arr[2] as $k => $v) {
            $result[$v] = number_format(1/$arr[4][$k]*100, 6, '.', '');
        }
        $currency = make('app/service/currency/Currency');
        $logger = make('app/service/currency/Logger');
        $currencyArr = $currency->getListData();
        foreach ($currencyArr as $value) {
            if (!isset($result[$value['code']]) || $value['rate'] == $result[$value['code']]) {
                continue;
            }
            $currency->updateData($value['code'], ['rate'=>$result[$value['code']]]);
            $logger->insert([
                'code' => $value['code'],
                'old_rate' => $value['rate'],
                'new_rate' => $result[$value['code']],
            ]);
        }
        return false;
    }
}