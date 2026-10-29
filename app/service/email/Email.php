<?php

namespace app\service\email;
use app\service\Base;

class Email extends Base
{
	protected $emailAccountId = 0;
	protected array $siteInfo = [];

	public function sendEmail($memId, $code, $type, $expire = 0)
	{
		$data = service('email/Used')->getSiteAccountId();
		if (empty($data)) {
			return false;
		}
		$data['type'] = $type;
		$data['mem_id'] = $memId;
		$data['content'] = $code;
		if ($expire) {
			$data['expire_time'] = now(time() + $expire);
		}
		$rst = $this->insert($data);
		if ($rst) {
			frame('Task')->taskStart('Email');
		}
		return $rst;
	}

	public function sendEmailById($id)
	{
		$info = $this->loadData($id);
		if (empty($info)) {
			return false;
		}
		$memInfo = service('member/Member')->loadData($info['mem_id'], 'email');
		if (empty($memInfo['email'])) {
			return false;
		}
		$this->emailAccountId = $info['account_id'];
		$toName = explode('@', $memInfo['email'])[0];
		switch ($info['type']) {
			case $this->getConst('TYPE_LOGIN_SEND_CODE'):
				$subject = 'Login Verification Code';
				$template = 'code';
				$this->siteInfo = site()->getInfoCache($info['site_id']);
				$vars = [
					'name' => $toName,
					'siteName' => $this->siteInfo['name'],
					'code' => $info['content'],
					'link' => url('login', ['email' => $memInfo['email'], 'verify_code' => $info['content']], $this->siteInfo['domain']),
				];
				break;
			default:
				return false;
		}
		$rst = $this->sendTemplate($memInfo['email'], $toName, $subject, $this->siteInfo['name'], $template, $vars);
		$status = $rst ? $this->getConst('STATU_SENT_SUCCESS') : $this->getConst('STATU_SENT_FAILED');
		return $this->updateData($id, ['status' => $status, 'send_time' => now()]);
	}

	protected function getSiteEmailTemplate($siteName, $name): string
	{
		$file = ROOT_PATH . $name . DS . 'template' . DS . 'email' . DS . 'code.php';
		if (!is_file($file)) {
			$file = ROOT_PATH . 'template' . DS . 'email' . DS . 'code.php';
		}
		return $file;
	}

	protected function sendTemplate($toEmail, $toName, $subject, $siteName, $templateName, array $vars = [])
	{
		$file = $this->getSiteEmailTemplate($siteName, $templateName);
		return $this->send($toEmail, $toName, $subject, view()->getContent($file, $vars));
	}

	protected function send($toEmail, $toName, $subject, $content)
	{
		$auth = service('email/Account')->getInfoCache($this->emailAccountId);
		if (!$auth || !$auth['status']) {
			return false;
		}

		$mail = service('phpmailer/PHPMailer');
		$mail->SMTPDebug = 0;
		$mail->MessageID = randString(32) . '@' . ($this->siteInfo['domain'] ?? 'localhost');
		$mail->isSMTP();
		$mail->isHTML(true);
		$mail->SMTPAuth = true;
		$host = explode(':', $auth['smtp'], 2);
		if (count($host) < 2) {
			$host[1] = 25;
		}
		$mail->Host = $host[0];
		$mail->Port = (int)$host[1];
		if ($auth['smtp_ssl']) {
			$mail->SMTPSecure = 'ssl';
		} else {
			$mail->SMTPSecure = '';
			$mail->SMTPAutoTLS = false;
		}

		$mail->CharSet = 'UTF-8';
		$mail->setFrom($auth['address'], $auth['name']);
		$mail->Username = $auth['email_user'];
		$mail->Password = $auth['password'];
		$mail->addAddress($toEmail, $toName);
		$mail->Subject = $subject;
		$mail->Body = $content;

		$status = $mail->send();
		if (!$status) {
			frame('Debug')->runlog($mail->ErrorInfo, 'email_error');
		}
		return $status;
	}
}
