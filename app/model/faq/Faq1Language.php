<?php

namespace app\model\faq;
use app\model\Base;

class faqLanguage extends Base
{
    protected $_table = 'faq_language';
    protected $_primaryKey = 'item_id';
    protected $_intFields = ['item_id', 'faq_id', 'lan_id'];
}