<?php

namespace app\service;
use app\service\Base;

class MessageService extends Base
{
    public function sendMessage($from, $to, $content, $tr_code='')
    {
    	$from = (int) $from;
    	if (empty($from) || empty($to) || empty($content)) return false;
    	$groupKey = $this->createGroup($from, $to);
    	if (empty($groupKey)) {
            return false;
        }
    	return $this->sendMessageByKey($groupKey, $content, $from);
    }

    public function createGroup($from, $to, $type=0)
    {
        $groupKey = $this->createGroupKey($from, $to, $type);
        if ($this->isExistGroup($groupKey)) {
            return $groupKey;
        }
        $insert = [
            'group_key' => $groupKey,
            'mem_id' => $from,
            'type' => $type,
            'create_at' => now(),
        ];
        $groupModel = make('App/Models/MessageGroup');
        $result = $groupModel->insert($insert);
        //群组加人员
        $insert = [
            'group_key' => $groupKey,
            'mem_id' => $from,
            'create_at' => time(),
        ];
        if (!empty($to)) {
            $insert = [$insert];
            $insert[] = [
                'group_key' => $groupKey,
                'mem_id' => $to,
                'create_at' => time(),
            ];
        }
        $result = make('App/Models/MessageMember')->insert($insert);
        if ($type ==0 && substr($to, 0, 1) == 5) {
            //发送初始化聊天术语
            $this->sendMessageByKey($groupKey, dist('您好, 有什么可以帮到您'), $to);
        }
        if ($result) return $groupKey;
        return false;
    }

    public function sendMessageByKey($groupKey, $content, $from, $trCode)
    {
        //消息数据
        $insert = [
            'group_key' => $groupKey,
            'mem_id' => $from,
            'content' => trim($content),
            'create_at' => time(),
        ];
        $result = make('App/Models/Message')->insert($insert);
        if ($result) {
            //更新未读消息
            $this->updateReadCount($groupKey, $from);
        }
        return $result;
    }

    protected function createGroupKey($from, $to, $type)
    {
        $array = [$from, $to];
        sort($array);
        return md5(implode('_', $array).'_'.$type);
    }

    protected function isExistGroup($groupKey)
    {
        if (empty($groupKey)) return false;
        return make('App/Models/MessageGroup')->where('group_key', $groupKey)->count() > 0;
    }

    protected function updateReadCount($groupKey, $from)
    {
    	//消息组消息数
        make('App/Models/MessageGroup')->where('group_key', $groupKey)->increment('message_total');
    	//同组其他人员未读消息数
    	make('App/Models/MessageMember')->where('group_key', $groupKey)->where('mem_id', '<>', $from)->increment('unread');
    	return true;
    }

    public function joinInGroup($groupKey, $memId)
    {
    	$memId = (int) $memId;
    	if (empty($groupKey) || empty($memId)) return false;
    	$insert = [
			'group_key' => $key,
			'mem_id' => $mem_id,
			'create_at' => time(),
		];
		return make('App/Models/MessageMember')->insert($insert);
    }

    public function isExistMember($groupKey, $memId)
    {
    	$memId = (int) $memId;
    	if (empty($groupKey) || empty($memId)) return false;
    	return make('App/Models/MessageMember')->where('group_key', $groupKey)->where('mem_id', $memId)->count() > 0;
    }
}