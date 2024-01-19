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

    public function url(int $channelId, int $itemId)
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

    public function getInfo(int $id)
    {
        $info = $this->loadData($id);
        if (empty($info)) {
            return false;
        }
        $result = $this->getResult($info['purchase_channel_id'], $info['item_id']);
        return array_merge($info, $result ?: []);
    }

    public function saveResult(int $channelId, int $itemId, array $data)
    {
        $path = $this->resultPath($channelId, $itemId);
        file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE));
        return true;
    }

    public function getResult(int $channelId, int $itemId)
    {
        $path = $this->resultPath($channelId, $itemId);
        $rst = file_get_contents($path);
        return isJson($rst);
    }

    protected function resultPath($channelId, int $itemId, $create=true)
    {
        $path = ROOT_PATH.'storage'.DS.'product_data'.DS.$channelId.DS;
        if ($create) {
            createDir($path);
        }
        return $path.$itemId.'.json';
    }
}