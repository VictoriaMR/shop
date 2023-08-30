<?php 

namespace app\service\purchase;
use app\service\Base;

class Channel extends Base
{
    protected $_channelMap = [
        'taobao' => 6051,
        'tmall' => 6052,
        '1688' => 6053,
    ];
    public function getChannelId($url)
    {
        if (preg_match('/(http:|https:)?\/\/.*(taobao|tmall|1688)\.com/', $url, $match)) {
            return $this->_channelMap[$match[2]] ?? false;
        }
        return false;
    }

    public function getItemId($url)
    {
        if (preg_match('/(http:|https:)?\/\/(item|detail)\.(taobao|tmall|1688)\.com\/(item|item_o|offer)\/?(\d+)?\.(htm|html)(?:.*?[?&]id=(\d+))?/', $url, $match)) {
            return $match[5] ?: $match[7];
        }
        return false;
    }
}