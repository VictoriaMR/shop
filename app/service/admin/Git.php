<?php

namespace app\service\admin;
use app\service\Base;

class Git extends Base
{
	public function upateLibrary($id, &$msg)
	{
		$pullRst = $this->run('git pull', ROOT_PATH);
		$pullMsg = trim(implode(' ', array_filter($pullRst['data'] ?? [])));
		$added = $this->updateLog($id);

		if ($pullRst['code'] === 0) {
			$msg = '同步成功' . ($added > 0 ? '，新增 ' . $added . ' 条提交记录' : '，当前已是最新记录');
			return true;
		}

		if ($added > 0) {
			$msg = '本地日志同步成功，新增 ' . $added . ' 条提交记录';
			return true;
		}

		$msg = $pullMsg !== '' ? $pullMsg : '同步完成，暂无新提交记录';
		return $pullRst['code'] === 0 || $added !== false;
	}

	public function updateLog($id)
	{
		$lastRow = $this->loadData(['library' => $id], 'max(commit_time) commit_time');
		$lastTime = $lastRow['commit_time'] ?? '';
		$rst = $this->getGitHistoryLog($id, $lastTime);
		if (empty($rst)) {
			return 0;
		}

		$commits = array_column($rst, 'commit');
		$hasArr = $this->getListData(['library' => $id, 'commit' => ['in', $commits]], 'git_id,commit');
		$hasMap = array_column($hasArr, 'git_id', 'commit');
		foreach ($rst as $key => $value) {
			if (isset($hasMap[$value['commit']])) {
				unset($rst[$key]);
			}
		}
		if (empty($rst)) {
			return 0;
		}
		$rst = array_values($rst);
		$this->insertData($rst);
		return count($rst);
	}

	public function getCurrentBranch()
	{
		$headFile = ROOT_PATH . '.git/HEAD';
		if (!is_file($headFile)) {
			return '';
		}
		$content = trim((string)@file_get_contents($headFile));
		if (strpos($content, 'ref: refs/heads/') === 0) {
			return substr($content, strlen('ref: refs/heads/'));
		}
		return substr($content, 0, 7);
	}

	private function getGitHistoryLog($id, $lastTime)
	{
		$cmd = 'git log --reverse';
		if (!empty($lastTime)) {
			$cmd .= ' --after="' . $lastTime . '"';
		}
		$rst = $this->run($cmd, ROOT_PATH);
		if ($rst['code'] != 0) {
			return [];
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
			if (strpos($value, 'commit ') === 0) {
				if (!empty($tempArr)) {
					$tempData[] = $tempArr;
				}
				$tempArr = [];
			}
			$tempArr[] = $value;
		}
		if (!empty($tempArr)) {
			$tempData[] = $tempArr;
		}
		$result = [];
		foreach ($tempData as $value) {
			$tempArr = [
				'commit'      => '',
				'author'      => '',
				'commit_time' => '',
				'info'        => '',
			];
			$msgLines = [];
			foreach ($value as $v) {
				if (strpos($v, 'commit ') === 0) {
					$tempArr['commit'] = trim( substr($v, 7) );
				} elseif (strpos($v, 'Author:') === 0) {
					$authorStr = trim( substr($v, 7) );
					$tempArr['author'] = explode(' ', $authorStr)[0];
				} elseif (strpos($v, 'Date:') === 0) {
					$dateStr = trim( substr($v, 5) );
					$ts = strtotime($dateStr);
					$tempArr['commit_time'] = $ts ? date('Y-m-d H:i:s', $ts) : '';
				} elseif (strpos($v, 'Merge:') === 0) {
					continue;
				} else {
					$line = trim($v);
					if ($line !== '') {
						$msgLines[] = $line;
					}
				}
			}
			$tempArr['info'] = implode('; ', $msgLines);
			if ($tempArr['commit'] !== '') {
				$result[] = $tempArr;
			}
		}
		return $result;
	}

	private function run($cmd, $workPath = null, $filterResult = true)
	{
		if (!isWin()) {
			$cmd = 'sudo /usr/local/git/bin/' . $cmd;
		}
		$rst = [];
		$code = null;
		if (!empty($workPath)) {
			if (isWin()) {
				$cmd = 'cd /d "' . rtrim($workPath, '/\\') . '" && ' . $cmd;
			} else {
				$cmd = 'cd "' . rtrim($workPath, '/') . '" && ' . $cmd;
			}
		}
		$cmd .= ' 2>&1';
		exec($cmd, $rst, $code);
		foreach ($rst as $k => $v) {
			if ($filterResult && strpos($v, "\r") !== false) {
				$tmp = explode("\r", $v);
				$rst[$k] = end($tmp);
			}
		}
		return ['data' => $rst, 'code' => $code];
	}
}