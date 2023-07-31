<?php 

namespace app\service\product;
use app\service\Base;

class Channel extends Base
{
    public function getChannelId($url)
    {
        if (empty($url)) {
            return false;
        }
        $arr = parse_url($url);
        if (strpos($arr['host'], 'taobao.com') !== false) {
            return 6051;
        } else if (strpos($arr['host'], 'tmall.com') !== false) {
            return 6052;
        } else if (strpos($arr['host'], '1688.com') !== false) {
            return 6053;
        }
        return false;
    }

    public function getItemId($url, $channelId=0)
    {
        if (!$channelId) {
            $channelId = $this->getChannelId($url);
        }
        if (!$channelId) {
            return false;
        }
        switch ($channelId) {
            case 6051:
            case 6052:
                parse_str(parse_url($url)['query'] ?? '', $match);
                return $match['id'] ?? false;
                break;
            case 6053:
                preg_match('/^https\:\/\/detail\.1688\.com\/offer\/(\d+)\.html/i', $url, $match);
                return $match[1] ?? false;
                break;
        }
        return false;
    }
}