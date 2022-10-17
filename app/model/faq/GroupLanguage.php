<?php

namespace app\model\faq;
use app\model\Base;

class GroupLanguage extends Base
{
    protected $_table = 'faq_group_language';
    protected $_primaryKey = 'item_id';
    protected $_intFields = ['item_id', 'group_id', 'lan_id'];
}