<?php 

namespace app\service\purchase;
use app\service\Base;

class Shop extends Base
{
    public function addShop($channelId, $uniqueId, $name, $url, $ext)
    {
        $channelId = (int)$channelId;
        $uniqueId = trim((string)$uniqueId);
        $name = trim((string)$name);
        $cleanUrl = trim(str_replace(['https://', 'http://'], '', (string)$url), '/');
        if ($uniqueId === '' && $cleanUrl !== '') {
            $uniqueId = $this->extractUniqueId($cleanUrl);
        }

        $info = [];
        if ($uniqueId !== '') {
            $info = $this->loadData([
                'channel_id' => $channelId,
                'unique_id'  => $uniqueId,
            ], 'purchase_shop_id');
        }
        if (empty($info) && $cleanUrl !== '') {
            $info = $this->loadData([
                'channel_id' => $channelId,
                'url'        => $cleanUrl,
            ], 'purchase_shop_id');
        }
        if (empty($info) && $name !== '') {
            $info = $this->loadData([
                'channel_id' => $channelId,
                'name'       => $name,
                'unique_id'  => '',
            ], 'purchase_shop_id');
        }

        $data = [
            'channel_id' => $channelId,
            'unique_id'  => $uniqueId,
            'name'       => $name,
            'url'        => $cleanUrl,
            'info'       => is_array($ext) ? json_encode($ext, JSON_UNESCAPED_UNICODE) : (string)$ext,
        ];
        if (!empty($info)) {
            $this->updateData($info['purchase_shop_id'], $data);
            return (int)$info['purchase_shop_id'];
        }
        return (int)$this->insertGetId($data);
    }

    public function extractUniqueId(string $url): string
    {
        $cleanUrl = trim(str_replace(['https://', 'http://'], '', $url), '/');
        if ($cleanUrl === '') {
            return '';
        }
        if (preg_match('/^shop(\d+)\.(?:m\.)?(?:taobao|tmall)\.com/i', $cleanUrl, $m)) {
            return $m[1];
        }
        if (preg_match('/[?&]shop_?id=(\d+)/i', $cleanUrl, $m)) {
            return $m[1];
        }
        if (preg_match('/^([a-z0-9_-]+)\.(?:m\.)?(?:1688|taobao|tmall)\.com/i', $cleanUrl, $m)) {
            $sub = strtolower($m[1]);
            if (!in_array($sub, ['www', 'detail', 'item', 'winport', 'shop', 'm', 'h5'], true)) {
                return $m[1];
            }
        }
        return '';
    }

    public function shopInfo($data, $name = true)
    {
        if (empty($data)) {
            return '--';
        }
        $html = '';
        if ($name) {
            $shopName = htmlspecialchars($data['name'] ?: '--');
            if (!empty($data['url'])) {
                $shopUrl = url('Product/purchaseShop', ['id' => $data['purchase_shop_id']]);
                $html .= '<p class="shop-title"><a href="' . htmlspecialchars($shopUrl) . '" target="_blank"><span class="glyphicon glyphicon-link"></span> ' . $shopName . '</a></p>';
            } else {
                $html .= '<p class="shop-title">' . $shopName . '</p>';
            }
        }
        if (!empty($data['info'])) {
            $infoArr = is_array($data['info']) ? $data['info'] : json_decode($data['info'], true);
            if (!empty($infoArr) && is_array($infoArr)) {
                $items = [];
                foreach ($infoArr as $key => $value) {
                    if ($value === '' || $value === null) {
                        continue;
                    }
                    if ($key === 'level' && (int)$value <= 0) {
                        continue;
                    }
                    $label = $this->getDescText($key);
                    $items[] = '<span class="shop-metric" title="' . htmlspecialchars($label ?: $key) . '">'
                        . ($label ? '<em class="metric-label">' . htmlspecialchars($label) . ':</em>' : '')
                        . htmlspecialchars((string)$value)
                        . '</span>';
                }
                if (!empty($items)) {
                    $html .= '<p class="desc-info">' . implode(' <span class="metric-sep">/</span> ', $items) . '</p>';
                }
            }
        }
        return $html ?: '--';
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