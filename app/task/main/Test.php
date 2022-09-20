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
        // $this->updateSpuPrice();
        //转换webp图片
        $this->getDirFile(ROOT_PATH.'storage');
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

    private function getDirFile($path)
    {
        if (is_file($path)) {
            return $this->toWebp($path);
        }
        $files = scandir($path);
        foreach ($files as $v) {
            $newPath = $path.DS.$v;
            if (is_file($newPath)) {
                $this->toWebp($newPath);
            } elseif (is_dir($newPath)&&$v!='.'&&$v!='..') {
                $this->getDirFile($newPath);
            }
        }
        return true;
    }

    private function toWebp($file)
    {
        $extension = pathinfo($file)['extension'];
        if ($extension == 'webp') {
            return true;
        }
        $toFile = str_replace('.'.$extension, '.webp', $file);
        if (is_file($toFile)) {
            return true;
        }
        $suffix = getimagesize($file)['mime'];
        switch ($suffix) {
            case 'image/png':
                $im = imagecreatefrompng($file);
                break;
            // case 'image/gif':
            //     $im = imagecreatefromgif($file);
            //     break;
            case 'image/jpeg':
            case 'image/jpg':
                $im = imagecreatefromjpeg($file);
                break;
            default:
                return false;
        }
        imagewebp($im, $toFile);
        imagedestroy($im);
        return true;
    }
}