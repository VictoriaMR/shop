<?php

namespace app\controller\admin;
use app\controller\AdminBase;

class Currency extends AdminBase
{
    public function __construct()
    {
        $this->_arr = [
            'index' => '货币管理',
        ];
        $this->_default = '财务管理';
        parent::_init();
    }

    public function index()
    {
        if (request()->isPost()) {
            $opn = ipost('opn');
            if (in_array($opn, [''])) {
                $this->$opn();
            }
            $this->error('非法请求');
        }
        html()->addJs();
        $list = make('app/service/currency/Currency')->getListData();
        $this->assign('list', $list);
        $this->view();
    }
}