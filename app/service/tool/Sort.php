<?php

// 更新排序类
class Sort
{
    public function move($table, $pk, $index, $sortField='sort', $where=[], $config='database')
    {
        $query = frame('Query')->database($database)->table($table);
        if(!is_array($pk)){
            $_pk=$query->getPk();
            if(is_array($_pk)){
                throw new Exception('does not support complex primary');
            }
            $pk = [$_pk=>$pk];
        }

        $sort = $query->field($sortField)->where($pk)->find();
        if(!is_numeric($sort)){
            return false;
        }

        if($index=='start'){
            $query->where($where)->where([$sortField=>['<',$sort]])->increment($sortField,1);
            $query->where($pk)->setField($sortField,1);
            return 1;
        } else if ($index<0){ // 向前移
            if($sort+$index<1){
                return $this->toStart($table, $pk, $sortField, $where, $config);
            } else {
                $query->where($where)->where([$sortField=>['between',[$sort+$index,$sort-1]]])->increment($sortField,1);
                $query->where($pk)->setField($sortField,$sort+$index);
                return $sort+$index;
            }
        } else if($index=='end'){
            if($sort==0){ // 处理新添加的记录的特殊情况
                $max = $query->where($where)->max($sortField);
                $query->where($pk)->setField($sortField, $max + 1);
                return $max + 1;
            } else {
                $query->where($where)->where([$sortField => ['>', $sort]])->decrement($sortField, 1);
                foreach ($pk as $k=>$v) {
                    break;
                }
                $max = $query->where($where)->where([$k=>['<>',$v]])->max($sortField);
                if ($max >= $sort) {
                    $query->where($pk)->setField($sortField, $max + 1);
                    return $max + 1;
                } else {
                    return $sort;
                }
            }
        } else { // $index >0 后移
            $query->where($where)->where([$sortField=>['between',[$sort+1,$sort+$index]]])->decrement($sortField,1);
            foreach ($pk as $k=>$v) {
                break;
            }
            $max = $query->where($where)->where([$k=>['<>',$v]])->max($sortField);
            if($max>$sort+$index){
                $max = $sort+$index;
            } else {
                $max =$max+1;
            }
            $query->where($pk)->setField($sortField,$max);
            return $max;
        }
    }

    // 向上移动一位
    public function up($table, $pk, $sortField='sort',  $where=[], $config='database')
    {
        return $this->move($table, $pk, -1, $sortField, $where, $config);
    }

    // 向下移动一位
    public function down($table, $pk, $sortField='sort', $where=[], $config='database')
    {
        return $this->move($table,  $pk, 1, $sortField,$where, $config);
    }

    // 移动到开头
    public function toStart($table, $pk, $sortField='sort', $where=[], $config='database')
    {
        return $this->move($table, $pk, 'start', $sortField, $where, $config);
    }

    // 移动到最后
    public function toEnd($table, $pk, $sortField='sort', $where=[], $config='database')
    {
        return $this->move($table, $pk, 'end', $sortField, $where, $config);
    }

    // 新添加到排序开头， 注意新添加的行排序字段值要为0.
    public function addToStart($table, $pk, $sortField='sort', $where=[], $config='database')
    {
        $this->move($table, $pk, 'end', $sortField, $where, $config);
        return $this->move($table, $pk, 'start', $sortField, $where, $config);
    }

    // 新添加到排序结尾， 注意新添加的行排序字段值要为0.
    public function addToEnd($table, $pk, $sortField='sort', $where=[], $config='database')
    {
        return $this->move($table, $pk, 'end', $sortField, $where, $config);
    }

    // 便捷排序函数
    public  function sort($type, $table, $pk, $sortField='sort', $where=[], $config='database')
    {
        $type = strtolower($type);
        switch ($type){
            case 'first':
            case 'start':
            case 'top':
                $rst = Sort::toStart($table, $pk, $sortField, $where, $config);
                break;
            case 'prev':
            case 'up':
                $rst = Sort::up($table, $pk, $sortField, $where, $config);
                break;
            case 'next':
            case 'down':
                $rst = Sort::down($table, $pk, $sortField, $where, $config);
                break;
            case 'last':
            case 'end':
            case 'bottom':
                $rst = Sort::toEnd($table, $pk, $sortField, $where, $config);
                break;
            default:
                $rst = false;
        }
        return $rst;
    }

    public function reset($table, $sortField='sort', $where=[], $config='database')
    {
        $query=db($table,$config);
        $pk=$query->getPk();
        if(is_array($pk)){
            $pk=implode(',',$pk);
        }
        $rst = $query->field($pk)->where($where)->field($sortField)->resultType('array')->order($sortField,'asc')->select();
        $i=1;
        $sql=[];
        foreach ($rst as $val){
            $val[$sortField]=$i++;
            $sql[] = $query->fetchSql(true)->update($val);
            if(count($sql)>20){ // 每次批量更新20行
                Db::query(implode(';',$sql),[],false,null,$config);
                $sql=[];
            }
        }
        if(count($sql)>0){
            Db::query(implode(';',$sql),[],false,null,$config);
        }
    }
}