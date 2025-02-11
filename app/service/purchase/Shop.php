<?php 

namespace app\service\purchase;
use app\service\Base;

class Shop extends Base
{
    public function addShop($channelId, $uniqueId, $name, $url, $ext)
    {
        $data = [
            'channel_id' => $channelId,
            'unique_id' => $uniqueId,
        ];
        $info = $this->loadData($data, 'purchase_shop_id');
        $data['name'] = $name;
        $data['url'] = trim(str_replace(['https://', 'http://'], '', $url), '/');
        $data['info'] = is_array($ext) ? json_encode($ext, JSON_UNESCAPED_UNICODE) : $ext;
        if (!empty($info)) {
            $this->updateData($info['purchase_shop_id'], $data);
            return $info['purchase_shop_id'];
        } else {
            return $this->insertGetId($data);
        }
    }

    public function shopInfo($data, $name=true)
    {
        if (empty($data)) return '--';
        $html = '';
        if ($name) {
            $html .= '<p><a href="https://'.$data['url'].'" target="_black">'.$data['name'].'</a></p>';
        }
        if (!empty($data['info'])) {
            $data['info'] = json_decode($data['info'], true);
            $html .= '<p class="desc-info">';
            foreach ($data['info'] as $key => $value) {
                $html .= '<span title="'.$this->getDescText($key).'">'.$value.'</span> / ';
            }
            $html = trim($html, ' / ');
            $html .= '</p>';
        }
        return $html;
    }

    public function getDescText($type)
    {
        $arr = [
            'level' => '等级',
            'desc' => '描述',
            'post' => '物流',
            'serv' => '服务',
            'star' => '综合服务',
            'year' => '年限',
            'lgt' => '物流时效',
            'rdf' => '退换体验',
            'dspt' => '纠纷解决',
            'goods' => '品质体验',
            'cst' => '采购咨询',
        ];
        return $arr[$type] ?? '';
    }
}