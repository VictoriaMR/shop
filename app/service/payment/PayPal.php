<?php 

namespace app\service\payment;

class PayPal extends Payment
{
    private $config = [];

    public function getToken()
    {
        $where = [
            'site_id' => siteId(),
            'type' => self::PAYMENT_TYPE_PAYPAL,
            'status' => 1,
        ];
        $this->config = $this->getSiteInfo($where);
        if (empty($this->config)) {
            return false;
        }
        return $this->config;
    }

    public function getJsSdk($currency='', $commit=true, $express=false)
    {
        $url = sprintf('https://www.paypal.com/sdk/js?client-id=%s', $this->config['app_key']);
        if ($currency) {
            $url .= '&currency='.$currency;
        }
        if ($commit) {
            $url .= '&commit='.$commit;
        }
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && stripos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'zh-cn') !== false){
            $url .= '&locale=en_US';
        }
        if($express){
            $url .= '&disable-funding='.implode(',', ['card', 'credit', 'venmo', 'sepa', 'bancontact', 'eps', 'giropay', 'ideal', 'mybank', 'p24', 'sofort']);
        }
        return $url;
    }
}