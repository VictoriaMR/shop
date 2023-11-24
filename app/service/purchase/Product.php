<?php 

namespace app\service\purchase;
use app\service\Base;

class Product extends Base
{
    public function addUrl($url)
    {
        $channelId = purchase()->channel()->getChannelId($url);
        if (!$channelId) {
            return false;
        }
        $itemId = purchase()->channel()->getItemId($url);
        if (!$itemId) {
            return false;
        }
        $data = [
            'purchase_channel_id' => $channelId,
            'item_id' => $itemId,
        ];
        if ($this->getCountData($data)) return true;
        return $this->insert($data);
    }

    public function getStatusList()
    {
        return [
            $this->getConst('STATUS_NORMAL') => '未使用',
            $this->getConst('STATUS_SET') => '已上传',
            $this->getConst('STATUS_USED') => '已使用',
            $this->getConst('STATUS_FAIL') => '已废弃',
        ];
    }

    public function url($channelId, $itemId)
    {
        switch ($channelId) {
            case 6051:
                return 'https://item.taobao.com/item.htm?id='.$itemId;
            case 6052:
                return 'https://detail.tmall.com/item.htm?id='.$itemId;
            case 6053:
                return 'https://detail.1688.com/offer/'.$itemId.'.html';
        }
    }
}