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
}