<?php

namespace app\controller\home;
use app\controller\HomeBase;

class Contact extends HomeBase
{
	public function index()
	{
		frame('Html')->addCss();
		frame('Html')->addJs();

		$this->assign('_title', appT('contact_us'));
		$this->view();
	}

	public function create()
	{
		$service = service('message/Message');
		$fromId = userId() ?: (int) crc32(uuId());
		$key = $service->createGroup($fromId, $service->getSystemId());
		if ($key) {
			$this->success($key, '');
		} else {
			$this->error('Could not initialize support chat.');
		}
	}

	public function send()
	{
		$name = trim((string)ipost('name', ''));
		$email = trim((string)ipost('email', ''));
		$subject = trim((string)ipost('subject', 'General Inquiry'));
		$content = trim((string)ipost('content', ''));
		if (empty($email) || empty($content)) {
			$this->error('Please enter your email address and message.');
		}
		$ticketId = 'TKT-' . date('Ymd') . '-' . strtoupper(randString(6));
		$record = [
			'ticket_id' => $ticketId,
			'mem_id'    => userId(),
			'name'      => substr($name, 0, 64),
			'email'     => substr($email, 0, 128),
			'subject'   => substr($subject, 0, 128),
			'content'   => substr($content, 0, 1000),
			'add_time'  => now(),
		];
		$history = session()->get('contact_tickets') ?: [];
		$history[] = $record;
		session()->set('contact_tickets', $history);
		$this->success('Thank you! Your inquiry (' . $ticketId . ') has been received. Our support team will reply within 24 hours.', ['ticket_id' => $ticketId]);
	}

	public function message()
	{
		$key = ipost('key');
		$page = (int)ipost('page', 1);
		$size = (int)ipost('size', 20);
		if (empty($key)) {
			$this->error('Contact key is empty.');
		}
		$list = service('message/Message')->getListData(['group_key'=>$key], '*', $page, $size, ['add_time'=>'desc']);
		if (!empty($list)) {
			$memId = userId();
			$userInfo = session()->get(type().'_info') ?: [];
			foreach ($list as $idx => $value) {
				if ($memId && $memId == $value['mem_id']) {
					$value['is_self'] = true;
					$value['avatar'] = $userInfo['avatar'] ?? '';
					$value['name'] = $userInfo['name'] ?? 'Me';
				} else {
					$value['is_self'] = false;
					$value['avatar'] = '';
					$value['name'] = site()->getName();
				}
				$list[$idx] = $value;
			}
		}
		$this->success($list, '');
	}
}
