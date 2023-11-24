<?php

namespace app\model\faq;
use app\model\Base;

class Faq extends Base
{
    protected $_table = 'faq';
    protected $_addTime = 'add_time';
    protected $_intFields = ['faq_id', 'group_id', 'status', 'visit_total'];
}