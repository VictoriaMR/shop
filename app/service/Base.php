<?php

namespace app\service;

/**
 * 业务模型基类.
 */
class Base
{
    protected $baseModel;
    protected static $constantMap = [];

    public function loadData($id)
    {
        return $this->baseModel->loadData($id);
    }

    public function insert($data)
    {
        return $this->baseModel->insert($data);
    }

    public function insertGetId($data)
    {
        return $this->baseModel->insertGetId($data);
    }

    public function updateDataById($id, $data)
    {
        return $this->baseModel->updateDataById($id, $data);
    }

    public function isExistData($proId)
    {
        return $this->baseModel->isExistData($proId);
    }

    public function updateDataByFilter($filter, $data)
    {
        return $this->baseModel->updateDataByFilter($filter, $data);
    }

    public function deleteById($id)
    {
        return $this->baseModel->deleteById($id);
    }

    public function getCount(array $where=[])
    {
        return $this->baseModel->getCount($where);
    }

    public function getInfoByWhere(array $where=[])
    {
        return $this->baseModel->getInfoByWhere($where);
    }

    public function getListByWhere(array $where=[], $field='*', $page=1, $size=20)
    {
        return $this->baseModel->getListByWhere($where, $field, $page, $size);
    }

    public static function constant($const, $model = 'base')
    {
        $namespace = 'static';
        if (isset(static::$constantMap[$model])) {
            $namespace = static::$constantMap[$model];
        }
        return constant($namespace.'::'.$const);
    }

    public function getPaginationList($total, $list, $page, $pagesize)
    {
        return $this->baseModel->getPaginationList($total, $list, $page, $pagesize);
    }

    public function getSalt($len = 4)
    {
        $chars = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ23456789';
        $charsLen = strlen($chars) - 1;
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $chars[rand(0, $charsLen)];
        }
        return $str;
    }

    public function getPasswd($password, $salt)
    {
        $passwordArr = str_split($password);
        $saltArr = str_split($salt);
        $countpwd = count($passwordArr);
        $countSalt = count($saltArr);

        $password = '';
        if ($countSalt > $countpwd) {
            foreach ($saltArr as $key => $value) {
                $password .= $passwordArr[$key] ?? '' . $value;
            }
        } else {
            $i = 0;
            $sign = floor($countpwd / $countSalt);
            foreach ($passwordArr as $key => $value) {
                $password .= $value;
                if ($key % $sign == 0) {
                    if (empty($saltArr[$i])) $i = 0;

                    $password .= $saltArr[$i];
                    $i ++;
                }
            }
        }

        return $password;
    }

    public function start() 
    {
        return make('App\Models\Base')->start();
    }

    public function rollback()
    {
        return make('App\Models\Base')->rollback();
    }

    public function commit()
    {
        return make('App\Models\Base')->commit();
    }
}