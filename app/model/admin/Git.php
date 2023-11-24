<?php

namespace app\model\admin;
use app\model\Base;

class Git extends Base
{
    protected $_table = 'git';
    protected $_addTime = 'add_time';
    protected $_intFields = ['git_id', 'status'];
}