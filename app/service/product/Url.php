<?php 

namespace app\service\product;
use app\service\Base;

class Url extends Base
{
    public function add($url)
    {
        $channelId = make('app/service/product/Channel')->getChannelId($url);
        if (!$channelId) {
            return false;
        }
        $itemId = make('app/service/product/Channel')->getItemId($url);
        if (!$itemId) {
            return false;
        }
        $where = [
            'channel_id' => $channelId,
            'item_id' => $itemId,
        ];
        if ($this->where($where)->count()) {
            return false;
        }
        return $this->insert($where);
    }
}