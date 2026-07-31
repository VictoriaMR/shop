<?php

namespace app\service\tool;

/**
 * 通用数据表排序工具类
 */
class Sort
{
    /**
     * 对指定表的指定记录进行排序调整 (最顶/上移/下移/最底)
     *
     * @param string $table 表名 (如 'category')
     * @param int|string $pkValue 主键值 (如 12)
     * @param string $type 排序类型: 'top'|'up'|'down'|'bottom' (兼容 'start'/'prev'/'next'/'end'/'first'/'last')
     * @param string $pkField 主键字段名 (默认按表名自动推导如 cate_id / table_id)
     * @param string $sortField 排序字段名 (默认 'sort')
     * @param array $where 过滤条件/分组条件 (如 ['parent_id' => 0])
     * @return bool 是否成功
     */
    public function sort($table, $pkValue, $type, $pkField = '', $sortField = 'sort', $where = [])
    {
        if (empty($table) || empty($pkValue) || empty($type)) {
            return false;
        }

        // 自动识别主键字段名
        if (empty($pkField)) {
            $pkField = ($table === 'category') ? 'cate_id' : $table . '_id';
        }

        // 规范化操作类型
        $type = strtolower($type);
        $typeMap = [
            'start' => 'top',
            'first' => 'top',
            'prev'  => 'up',
            'next'  => 'down',
            'end'   => 'bottom',
            'last'  => 'bottom',
        ];
        if (isset($typeMap[$type])) {
            $type = $typeMap[$type];
        }

        if (!in_array($type, ['top', 'up', 'down', 'bottom'])) {
            return false;
        }

        // 直接从数据表查询符合条件的所有记录，按排序字段正序排列 (若sort相同则按主键正序)
        $query = \frame('Query')->table($table);
        if (!empty($where)) {
            $query->where($where);
        }

        $list = $query->field([$pkField, $sortField])
                      ->orderBy([$sortField => 'asc', $pkField => 'asc'])
                      ->page(0, 0)
                      ->get();

        if (empty($list)) {
            return false;
        }

        // 查找目标记录的当前索引位置
        $targetIndex = -1;
        foreach ($list as $index => $item) {
            if ($item[$pkField] == $pkValue) {
                $targetIndex = $index;
                break;
            }
        }

        if ($targetIndex === -1) {
            return false;
        }

        $count = count($list);

        // 根据操作类型重写列表顺序
        switch ($type) {
            case 'top':
                if ($targetIndex === 0) {
                    return true; // 已经在最顶
                }
                $targetItem = array_splice($list, $targetIndex, 1)[0];
                array_unshift($list, $targetItem);
                break;

            case 'up':
                if ($targetIndex === 0) {
                    return true; // 已经在最顶
                }
                $temp = $list[$targetIndex];
                $list[$targetIndex] = $list[$targetIndex - 1];
                $list[$targetIndex - 1] = $temp;
                break;

            case 'down':
                if ($targetIndex === $count - 1) {
                    return true; // 已经在最底
                }
                $temp = $list[$targetIndex];
                $list[$targetIndex] = $list[$targetIndex + 1];
                $list[$targetIndex + 1] = $temp;
                break;

            case 'bottom':
                if ($targetIndex === $count - 1) {
                    return true; // 已经在最底
                }
                $targetItem = array_splice($list, $targetIndex, 1)[0];
                $list[] = $targetItem;
                break;
        }

        // 直接在数据库中重新分配连续递增的 sort 值，仅更新发生了变化的行
        foreach ($list as $newSort => $item) {
            $newSortVal = $newSort + 1; // 1, 2, 3...
            if ((int)$item[$sortField] !== $newSortVal) {
                \frame('Query')->table($table)
                               ->where([$pkField => $item[$pkField]])
                               ->update([$sortField => $newSortVal]);
            }
        }

        return true;
    }

    public function move($table, $pk, $index, $sortField = 'sort', $where = [])
    {
        $pkValue = is_array($pk) ? current($pk) : $pk;
        $pkField = is_array($pk) ? key($pk) : '';
        $type = 'up';
        if ($index === 'start' || (is_numeric($index) && $index < 0)) {
            $type = ($index === 'start') ? 'top' : 'up';
        } elseif ($index === 'end' || (is_numeric($index) && $index > 0)) {
            $type = ($index === 'end') ? 'bottom' : 'down';
        }
        return $this->sort($table, $pkValue, $type, $pkField, $sortField, $where);
    }

    public function up($table, $pk, $sortField = 'sort', $where = [])
    {
        return $this->move($table, $pk, -1, $sortField, $where);
    }

    public function down($table, $pk, $sortField = 'sort', $where = [])
    {
        return $this->move($table, $pk, 1, $sortField, $where);
    }

    public function toStart($table, $pk, $sortField = 'sort', $where = [])
    {
        return $this->move($table, $pk, 'start', $sortField, $where);
    }

    public function toEnd($table, $pk, $sortField = 'sort', $where = [])
    {
        return $this->move($table, $pk, 'end', $sortField, $where);
    }
}