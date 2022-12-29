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
        make('app/service/currency/Currency')->updateRate();
        return false;
    }
}