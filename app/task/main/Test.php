<?php

namespace app\task\main;
use app\task\TaskDriver;

class Test extends TaskDriver
{
    private $language = [];

    public $config = [
        'name' => '测试任务',
        'cron' => ['* * * * *'],
    ];

    public function run()
    {
        //分类多语言翻译
        // $this->doCateLanguage();
        //spu价格更新
        $this->updateSpuPrice();
        return false;
    }

    private function doCateLanguage()
    {
        $language = array_column($this->getLanguage(), 'tr_code', 'lan_id');
        unset($language[1]);
        $lanCount = count($language);
        $transService = make('app/service/Translate');
        $cateList = make('app/service/category/Category')->getListData();
        $languageService = make('app/service/category/Language');
        foreach ($cateList as $key=>$value) {
            $hasLanguage = $languageService->getListData(['cate_id'=>$value['cate_id']], 'lan_id');
            if (count($hasLanguage) == $lanCount) continue;
            //更新锁 防止超时
            $this->updateLock();
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
                            'cate_id' => $value['cate_id'],
                            'lan_id' => $lv,
                            'name' => $transTxt,
                        ];
                    }
                }
                if (!empty($insertData)) {
                    $languageService->insert($insertData);
                }
            }
        }
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

    private function updateSpuPrice()
    {
        $this->echo('更新spu价格开始');
        $spu = make('app/service/product/Spu');
        $sku = make('app/service/product/Sku');
        $list = $spu->getListData([], 'spu_id');
        foreach ($list as $value) {
            $info = $sku->loadData(['spu_id'=>$value['spu_id']], 'max(price) as max_price,min(price) as min_price');
            if ($info) {
                $spu->updateData($value['spu_id'], ['min_price'=>$info['min_price'], 'max_price'=>$info['max_price']]);
            }
        }
        $this->echo('更新spu价格完成');
    }
}