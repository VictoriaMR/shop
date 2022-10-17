<?php

namespace app\model\faq;
use app\model\Base;

class Group extends Base
{
    protected $_table = 'faq_group';
    protected $_primaryKey = 'group_id';
    protected $_addTime = 'add_time';
    protected $_intFields = ['group_id', 'status'];
}