<?php 

namespace app\service\admin;
use app\service\Base;

class Git extends Base
{   
    protected function getModel()
    {
        $this->baseModel = make('app/model/admin/Git');
    }

    public function upateLibrary($id, &$msg)
    {
        $rst = $this->run('git pull', ROOT_PATH);
        $msg = implode(',', $rst['data']);
        if ($rst['code'] === 0) {
            $this->updateLog($id);
            return true;
        }
        return false;
    }

    private function updateLog($id)
    {
        //获取版本库最后记录时间
        $rst = $this->loadData(['library'=>$id], 'max(commit_time) commit_time');
        $rst = $this->getGitHistoryLog($id, $rst['commit_time']);
        if (!$rst) {
            return false;
        }
        return $this->insertData($rst);
    }

    private function getGitHistoryLog($id, $lastTime)
    {
        $cmd = 'git log';
        if ($lastTime) {
            $cmd .= ' --reverse '.$lastTime;
        }
        $rst = $this->run($cmd, ROOT_PATH);
        if ($rst['code'] != 0) {
            return false;
        }
        $list = $this->gitLogFormat($rst['data']);
        foreach ($list as $key => $value) {
            $list[$key]['library'] = $id;
        }
        return $list;
    }

    private function gitLogFormat(array $data)
    {
        if (empty($data)) {
            return [];
        }
        $tempData = [];
        $tempArr = [];
        foreach ($data as $value) {
            if (strpos($value, 'commit ') !== false) {
                if (!empty($tempArr)) {
                    $tempData[] = $tempArr;
                }
                $tempArr = [];
            }
            $tempArr[] = $value;
        }
        $data = [];
        foreach ($tempData as $value) {
            $tempArr = [];
            foreach ($value as $k=>$v) {
                if (strpos($v, 'commit ')!== false) {
                    $tempArr['commit'] = trim(str_replace('commit ', '', $v));
                    unset($value[$k]);
                } elseif (strpos($v, 'Author:')!== false) {
                    $tempArr['author'] = explode(' ', trim(str_replace('Author:', '', $v)))[0];
                    unset($value[$k]);
                } elseif (strpos($v, 'Date:')!== false) {
                    $tempArr['commit_time'] = date('Y-m-d H:i:s', strtotime(trim(str_replace('Date:', '', $v))));
                    unset($value[$k]);
                }
                $tempArr['info'] = trim(implode(',', array_filter($value)));
            }
            $data[] = $tempArr;
        }
        return $data;
    }

    private function run($cmd, $work_path = null, $filterResult = true)
    {
        if (!isWin()) {
            $cmd = '/usr/local/git/bin/'.$cmd;
        }
        $rst =[];
        $code =null;
        if(!empty($work_path)){
            $cmd = 'cd '.$work_path.' && '.$cmd;
        }
        $cmd .= ' 2>&1';
        exec($cmd, $rst, $code);
        foreach ($rst as $k=>$v){
            // 命令行按百分比进度输出的时候， 提取最后一条进度
            if($filterResult && strpos($v, "\r")!==false){
                $tmp = explode("\r",$v);
                $rst[$k] = end($tmp);
            }
        }
        return ['data'=>$rst, 'code'=>$code];
    }
}