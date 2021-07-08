<?php

namespace app\controller;

class Controller 
{
    protected $_nav;
    protected $_tag;
    protected $_arr;
    protected $_default;
    protected $_tagShow = true;
    
	protected function result($code, $data=[], $options=[])
    {
       $data = [
            'code' => $code,
            'data' => $data,
            'message' => '',
        ];
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode(array_merge($data, $options), JSON_UNESCAPED_UNICODE);
        exit();
    }

    protected function success($data=[], $options=null)
    {
        if (is_array($data)) {
            if (is_null($options)) {
                $options = 'success';
            }
        } else {
            if (is_null($options)) {
                $options = $data;
            }
        }
        $options = ['message' => $options];
        $this->result('200', $data, $options);
    }

    protected function error($message='')
    {
        if (empty($message)) {
            $message = 'error';
        }
        $this->result('10000', [], ['message' => $message]);
    }

    protected function assign($name, $value = null)
    {
        return view()->assign($name, $value);
    }

    protected function view()
    {
        return view()->display();
    }

    protected function _init()
    {
        $this->_tag = $this->_arr;
        if (empty($this->_default)) {
            $this->_nav = $this->_arr;
        } else {
            $this->_nav = array_merge(['default' => $this->_default], $this->_arr);
        }
        $this->assign('_tag', $this->_tag);
        $this->assign('_nav', $this->_nav);
        $this->assign('_tagShow', $this->_tagShow);
        $this->assign('_path', \Router::$_route['path']);
        $this->assign('_func', \Router::$_route['func']);
        $this->assign('_title', $this->_tag[\Router::$_route['func']] ?? '');
    }
}
